import { io } from 'socket.io-client';

/**
 * Shared notifications: navbar badge (+ employer dropdown) + Socket.IO realtime.
 */
(function () {
    let unreadCount = 0;
    let pollTimer = null;
    let socket = null;
    let started = false;

    const REALTIME_URL = (import.meta.env.VITE_REALTIME_URL || 'http://127.0.0.1:3001').replace(/\/$/, '');

    function csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    function userId() {
        const fromBody = Number(document.body?.dataset?.userId || 0);
        if (fromBody > 0) return fromBody;
        const fromMeta = Number(document.querySelector('meta[name="user-id"]')?.content || 0);
        return fromMeta > 0 ? fromMeta : 0;
    }

    function authHeaders(json) {
        const h = {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf(),
        };
        if (json) h['Content-Type'] = 'application/json';
        return h;
    }

    function updateBadge() {
        const badge = document.getElementById('notification-badge');
        if (!badge) return;

        if (unreadCount > 0) {
            const label = unreadCount > 9 ? '9+' : String(unreadCount);
            badge.textContent = label;
            badge.classList.remove('hidden');
            badge.style.display = 'flex';
            // Employer uses a tiny red dot — still show count if possible
            if (badge.classList.contains('w-2') || badge.classList.contains('h-2')) {
                badge.classList.remove('w-2', 'h-2');
                badge.classList.add('min-w-4', 'h-4', 'px-1', 'text-[10px]', 'font-bold', 'items-center', 'justify-center', 'rounded-full');
                badge.style.minWidth = '1rem';
                badge.style.height = '1rem';
                badge.style.fontSize = '0.65rem';
                badge.style.fontWeight = '700';
                badge.style.alignItems = 'center';
                badge.style.justifyContent = 'center';
                badge.style.borderRadius = '9999px';
                badge.style.padding = '0 0.2rem';
                badge.style.color = '#fff';
                badge.style.background = '#ef4444';
            }
        } else {
            badge.textContent = '0';
            badge.classList.add('hidden');
            badge.style.display = 'none';
        }

        // Full page counter (seeker notifications page)
        const pageNum = document.getElementById('notif-unread-num');
        const pageText = document.getElementById('notif-unread-text');
        if (pageNum) pageNum.textContent = String(unreadCount);
        if (pageText) {
            pageText.textContent =
                'You have ' + unreadCount + ' unread notification' + (unreadCount !== 1 ? 's' : '');
        }

        window.dispatchEvent(
            new CustomEvent('jobhub:notifications-updated', {
                detail: { unreadCount },
            })
        );
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text == null ? '' : String(text);
        return div.innerHTML;
    }

    function getTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        if (Number.isNaN(diffInSeconds) || diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + 'm ago';
        if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + 'h ago';
        if (diffInSeconds < 604800) return Math.floor(diffInSeconds / 86400) + 'd ago';
        return date.toLocaleDateString();
    }

    function renderDropdown(notifications) {
        const list = document.getElementById('notification-list');
        if (!list) return;

        if (!notifications.length) {
            list.innerHTML =
                '<div class="p-8 text-center text-gray-500 dark:text-gray-400"><p>No notifications</p></div>';
            return;
        }

        list.innerHTML = notifications
            .map((n) => {
                const isRead = !!(n.is_read || n.read);
                const bg = isRead ? 'bg-white dark:bg-gray-800' : 'bg-blue-50 dark:bg-blue-900/20';
                const appId = n.data?.application_id ?? 'null';
                return (
                    '<div class="notification-item ' +
                    bg +
                    ' border-b border-gray-100 dark:border-gray-700 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition" data-id="' +
                    escapeHtml(n.id) +
                    '" data-read="' +
                    isRead +
                    '" onclick="window.handleNotificationClick && window.handleNotificationClick(\'' +
                    escapeHtml(n.id) +
                    "', " +
                    appId +
                    ')">' +
                    '<div class="flex items-start justify-between gap-2">' +
                    '<div class="flex-1 min-w-0">' +
                    '<p class="text-sm font-medium text-gray-900 dark:text-white">' +
                    escapeHtml(n.title) +
                    '</p>' +
                    '<p class="text-xs text-gray-600 dark:text-gray-400 mt-1">' +
                    escapeHtml(n.message || n.body || '') +
                    '</p>' +
                    '<p class="text-xs text-gray-400 mt-1">' +
                    escapeHtml(getTimeAgo(n.created_at)) +
                    '</p>' +
                    '</div>' +
                    (!isRead ? '<div class="ml-2 w-2 h-2 bg-blue-600 rounded-full flex-shrink-0 mt-1"></div>' : '') +
                    '</div></div>'
                );
            })
            .join('');
    }

    async function fetchUnreadCount() {
        const res = await fetch('/api/notifications/unread-count', {
            credentials: 'same-origin',
            headers: authHeaders(false),
        });
        if (!res.ok) return;
        const data = await res.json();
        unreadCount = data.unread_count ?? data.data?.count ?? 0;
        updateBadge();
    }

    async function loadDropdownNotifications() {
        const list = document.getElementById('notification-list');
        if (!list) return;

        try {
            const res = await fetch('/api/notifications?limit=10', {
                credentials: 'same-origin',
                headers: authHeaders(false),
            });
            if (!res.ok) throw new Error('Failed');
            const data = await res.json();
            unreadCount = data.unread_count ?? unreadCount;
            updateBadge();
            renderDropdown(data.notifications || data.data || []);
        } catch (e) {
            list.innerHTML =
                '<div class="p-4 text-center text-red-500"><p class="text-sm">Error loading notifications</p></div>';
        }
    }

    function onRealtimeNotification(payload) {
        unreadCount = Math.max(0, unreadCount + 1);
        updateBadge();

        const dropdown = document.getElementById('notification-dropdown');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            loadDropdownNotifications();
        }

        // Let full page reload list if present
        window.dispatchEvent(
            new CustomEvent('jobhub:notification-new', {
                detail: payload || {},
            })
        );

        if (typeof window.showSuccessToast === 'function' && payload?.title) {
            window.showSuccessToast(payload.title);
        }
    }

    function connectSocket() {
        const uid = userId();
        if (!uid || socket) return;

        try {
            socket = io(REALTIME_URL, {
                transports: ['websocket', 'polling'],
                auth: { userId: uid },
                query: { userId: uid },
                reconnection: true,
                reconnectionDelay: 2000,
            });

            socket.on('connect', () => {
                socket.emit('join', { userId: uid });
            });

            socket.on('notification:new', onRealtimeNotification);
            socket.on('connect_error', () => {
                // Polling remains as fallback
            });
        } catch (e) {
            console.warn('Realtime socket unavailable', e);
        }
    }

    function startPolling() {
        if (pollTimer) return;
        pollTimer = setInterval(() => {
            fetchUnreadCount().catch(() => {});
        }, 30000);
    }

    function initDropdown() {
        const button = document.getElementById('notification-button');
        const dropdown = document.getElementById('notification-dropdown');
        if (!button || !dropdown) return;
        if (button.dataset.notifBound === 'true') return;
        button.dataset.notifBound = 'true';

        button.addEventListener('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            const opening = dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden');
            if (opening) loadDropdownNotifications();
        });

        document.addEventListener('click', function (e) {
            const container = document.getElementById('notification-container');
            if (container && !container.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        const markAll = document.getElementById('mark-all-read-btn');
        if (markAll && !markAll.dataset.notifBound) {
            markAll.dataset.notifBound = 'true';
            markAll.addEventListener('click', function (e) {
                e.stopPropagation();
                markAllAsRead();
            });
        }
    }

    async function markAsRead(notificationId) {
        const res = await fetch('/api/notifications/' + notificationId + '/read', {
            method: 'PUT',
            credentials: 'same-origin',
            headers: authHeaders(true),
        });
        if (!res.ok) return;
        const data = await res.json();
        unreadCount = data.unread_count ?? Math.max(0, unreadCount - 1);
        updateBadge();

        const item = document.querySelector('.notification-item[data-id="' + notificationId + '"]');
        if (item) {
            item.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
            item.classList.add('bg-white', 'dark:bg-gray-800');
            item.setAttribute('data-read', 'true');
            const dot = item.querySelector('.bg-blue-600');
            if (dot) dot.remove();
        }
    }

    async function markAllAsRead() {
        const res = await fetch('/api/notifications/mark-all-read', {
            method: 'PUT',
            credentials: 'same-origin',
            headers: authHeaders(true),
        });
        if (!res.ok) return;
        unreadCount = 0;
        updateBadge();
        loadDropdownNotifications();
        window.dispatchEvent(new CustomEvent('jobhub:notifications-all-read'));
    }

    window.handleNotificationClick = function (notificationId, applicationId) {
        markAsRead(notificationId);
        const userType = document.body.getAttribute('data-user-type') || '';
        const path = window.location.pathname;
        const isSeeker = userType === 'job_seeker' || path.includes('/job-seeker') || path === '/dashboard';

        if (applicationId && applicationId !== 'null') {
            window.location.href = isSeeker ? '/job-seeker/applications' : '/employer/applications';
            return;
        }

        if (isSeeker) {
            window.location.href = '/job-seeker/notifications';
        }
    };

    window.JobHubNotifications = {
        refresh: fetchUnreadCount,
        markAsRead,
        markAllAsRead,
        getUnreadCount: () => unreadCount,
    };

    function boot() {
        if (!document.getElementById('notification-badge') && !document.getElementById('notification-button')) {
            // Still allow socket if user id present (e.g. notifications page without badge mid-nav)
        }
        if (started) {
            initDropdown();
            return;
        }
        started = true;
        initDropdown();
        fetchUnreadCount().catch(() => {});
        connectSocket();
        startPolling();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    document.addEventListener('livewire:navigated', function () {
        started = false;
        setTimeout(boot, 150);
    });
})();
