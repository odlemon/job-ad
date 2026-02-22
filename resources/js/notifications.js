// Notification system
(function() {
    let notificationPollInterval = null;
    let unreadCount = 0;

    let isInitialized = false;

    function initNotifications() {
        const notificationButton = document.getElementById('notification-button');
        const notificationDropdown = document.getElementById('notification-dropdown');

        if (!notificationButton || !notificationDropdown) {
            return;
        }

        // Prevent multiple initializations
        if (isInitialized && notificationButton.dataset.initialized === 'true') {
            return;
        }

        notificationButton.dataset.initialized = 'true';
        isInitialized = true;

        // Toggle dropdown
        notificationButton.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            const isHidden = notificationDropdown.classList.contains('hidden');
            console.log('Notification button clicked, isHidden:', isHidden);
            notificationDropdown.classList.toggle('hidden');
            if (isHidden) {
                // Opening dropdown, load notifications
                loadNotifications();
            }
        });

        // Close dropdown when clicking outside
        const closeHandler = function(e) {
            const container = document.getElementById('notification-container');
            if (container && !container.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
        };
        document.removeEventListener('click', closeHandler);
        document.addEventListener('click', closeHandler);

        // Mark all as read
        const markAllReadBtn = document.getElementById('mark-all-read-btn');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                markAllAsRead();
            });
        }

        // Load notifications and start polling
        loadNotifications();
        if (!notificationPollInterval) {
            startPolling();
        }
    }

    function loadNotifications() {
        const notificationList = document.getElementById('notification-list');
        if (!notificationList) return;

        fetch('/api/notifications?limit=10', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            unreadCount = data.unread_count || 0;
            updateBadge();
            renderNotifications(data.notifications || []);
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            notificationList.innerHTML = `
                <div class="p-4 text-center text-red-500">
                    <p class="text-sm">Error loading notifications</p>
                </div>
            `;
        });
    }

    function updateBadge() {
        const badge = document.getElementById('notification-badge');
        if (badge) {
            if (unreadCount > 0) {
                badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    }

    function renderNotifications(notifications) {
        const notificationList = document.getElementById('notification-list');
        if (!notificationList) return;

        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p>No notifications</p>
                </div>
            `;
            return;
        }

        notificationList.innerHTML = notifications.map(notification => {
            const isRead = notification.is_read;
            const timeAgo = getTimeAgo(notification.created_at);
            const bgColor = isRead ? 'bg-white' : 'bg-blue-50';
            
            return `
                <div class="notification-item ${bgColor} border-b border-gray-100 px-4 py-3 hover:bg-gray-50 cursor-pointer transition" 
                     data-id="${notification.id}" 
                     data-read="${isRead}"
                     onclick="handleNotificationClick(${notification.id}, ${notification.data?.application_id || 'null'})">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">${escapeHtml(notification.title)}</p>
                            <p class="text-xs text-gray-600 mt-1">${escapeHtml(notification.message)}</p>
                            <p class="text-xs text-gray-400 mt-1">${timeAgo}</p>
                        </div>
                        ${!isRead ? '<div class="ml-2 w-2 h-2 bg-blue-600 rounded-full"></div>' : ''}
                    </div>
                </div>
            `;
        }).join('');
    }

    function handleNotificationClick(notificationId, applicationId) {
        // Mark as read
        markAsRead(notificationId);
        
        // Navigate based on user type
        const userType = document.body.getAttribute('data-user-type') || '';
        const currentPath = window.location.pathname;
        
        // Determine user type from current path if not in body attribute
        let isJobSeeker = false;
        if (userType === 'job_seeker' || currentPath.includes('/job-seeker')) {
            isJobSeeker = true;
        } else if (userType === 'employer' || currentPath.includes('/employer')) {
            isJobSeeker = false;
        }
        
        if (applicationId && applicationId !== 'null') {
            if (isJobSeeker) {
                // Navigate to job seeker applications page
                if (typeof navigateTo === 'function') {
                    navigateTo(`/job-seeker/applications`);
                } else {
                    window.location.href = `/job-seeker/applications`;
                }
            } else {
                // Navigate to employer applications page
                if (typeof navigateTo === 'function') {
                    navigateTo(`/employer/applications`);
                } else {
                    window.location.href = `/employer/applications`;
                }
            }
        }
    }

    function markAsRead(notificationId) {
        fetch(`/api/notifications/${notificationId}/read`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            unreadCount = data.unread_count || 0;
            updateBadge();
            
            // Update notification item
            const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
            if (item) {
                item.classList.remove('bg-blue-50');
                item.classList.add('bg-white');
                item.setAttribute('data-read', 'true');
                const dot = item.querySelector('.bg-blue-600');
                if (dot) dot.remove();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }

    function markAllAsRead() {
        fetch('/api/notifications/mark-all-read', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            unreadCount = 0;
            updateBadge();
            loadNotifications();
        })
        .catch(error => {
            console.error('Error marking all as read:', error);
        });
    }

    function startPolling() {
        // Poll every 30 seconds
        notificationPollInterval = setInterval(() => {
            fetch('/api/notifications/unread-count', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const newCount = data.unread_count || 0;
                if (newCount !== unreadCount) {
                    unreadCount = newCount;
                    updateBadge();
                    // Reload notifications if dropdown is open
                    const dropdown = document.getElementById('notification-dropdown');
                    if (dropdown && !dropdown.classList.contains('hidden')) {
                        loadNotifications();
                    }
                }
            })
            .catch(error => {
                console.error('Error polling notifications:', error);
            });
        }, 30000);
    }

    function getTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`;
        return date.toLocaleDateString();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Make handleNotificationClick globally available
    window.handleNotificationClick = handleNotificationClick;

    // Initialize on page load
    function initializeNotifications() {
        // Wait a bit to ensure DOM is ready
        setTimeout(() => {
            initNotifications();
        }, 100);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeNotifications);
    } else {
        initializeNotifications();
    }

    // Re-initialize on Livewire navigation
    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:navigated', function() {
            setTimeout(initNotifications, 200);
        });
    }

    // Also try to initialize after a short delay (in case elements are added dynamically)
    setTimeout(initializeNotifications, 500);
})();
