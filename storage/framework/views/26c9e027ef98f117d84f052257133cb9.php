

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.job-seeker-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="flex-1 p-8 bg-gray-50">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
                <p id="unread-count-text" class="mt-1 text-sm text-gray-600">You have <span id="unread-count-number">0</span> unread notifications</p>
            </div>
            <button id="mark-all-read-btn" class="flex items-center space-x-2 text-blue-600 hover:text-blue-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-medium">Mark all as read</span>
            </button>
        </div>

        <!-- Notifications List -->
        <div id="notifications-list" class="space-y-4">
            <!-- Skeleton Loaders -->
            <div id="skeleton-loaders" class="space-y-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 5; $i++): ?>
                <div class="bg-white rounded-lg shadow-sm p-6 animate-pulse">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 rounded w-1/3 mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-full mb-1"></div>
                            <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                        </div>
                    </div>
                </div>
                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <!-- Actual Notifications (will be populated by JavaScript) -->
            <div id="notifications-container" class="hidden space-y-4">
                <!-- Notifications will be inserted here -->
            </div>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
            <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
        </div>
    </main>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    // Notification type configurations
    const notificationTypes = {
        'status_updated': {
            icon: 'bg-blue-500',
            iconSvg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>'
        },
        'new_job_alert': {
            icon: 'bg-green-400',
            iconSvg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>'
        },
        'recruiter_message': {
            icon: 'bg-purple-400',
            iconSvg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>'
        },
        'job_expiring': {
            icon: 'bg-red-500',
            iconSvg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        },
        'company_update': {
            icon: 'bg-yellow-500',
            iconSvg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>'
        }
    };

    // Format time ago
    function formatTimeAgo(date) {
        const now = new Date();
        const diff = Math.floor((now - new Date(date)) / 1000);
        
        if (diff < 60) return 'Just now';
        if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
        if (diff < 604800) return `${Math.floor(diff / 86400)} days ago`;
        return `${Math.floor(diff / 604800)} weeks ago`;
    }

    // Get notification icon config
    function getNotificationIcon(type) {
        return notificationTypes[type] || {
            icon: 'bg-gray-500',
            iconSvg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>'
        };
    }

    // Render notification card
    function renderNotification(notification) {
        const iconConfig = getNotificationIcon(notification.type);
        const isUnread = !notification.is_read;
        const viewUrl = notification.data?.job_id 
            ? `/jobs/${notification.data.job_id}` 
            : notification.data?.application_id 
                ? `/job-seeker/applications/${notification.data.application_id}`
                : '#';

        return `
            <div class="bg-white rounded-lg shadow-sm p-6 relative ${isUnread ? '' : 'opacity-75'}" data-notification-id="${notification.id}">
                ${isUnread ? '<div class="absolute top-4 right-4 w-2 h-2 bg-blue-500 rounded-full"></div>' : ''}
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 ${iconConfig.icon} rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${iconConfig.iconSvg}
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-900 mb-1">${notification.title}</h3>
                        <p class="text-sm text-gray-600 mb-2">${notification.message}</p>
                        <p class="text-xs text-gray-500">${formatTimeAgo(notification.created_at)}</p>
                    </div>
                    <div class="flex items-center space-x-3 flex-shrink-0">
                        <a href="${viewUrl}" wire:navigate class="text-blue-600 hover:text-blue-700 text-sm font-medium">View</a>
                        ${isUnread ? `<button onclick="markAsRead(${notification.id})" class="text-gray-500 hover:text-gray-700 text-sm">Mark as read</button>` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    // Load notifications
    async function loadNotifications() {
        try {
            const response = await fetch('/api/notifications');
            const data = await response.json();
            
            document.getElementById('skeleton-loaders').classList.add('hidden');
            
            if (data.notifications && data.notifications.length > 0) {
                document.getElementById('notifications-container').classList.remove('hidden');
                document.getElementById('empty-state').classList.add('hidden');
                
                const container = document.getElementById('notifications-container');
                container.innerHTML = data.notifications.map(renderNotification).join('');
            } else {
                document.getElementById('notifications-container').classList.add('hidden');
                document.getElementById('empty-state').classList.remove('hidden');
            }
            
            // Update unread count
            const unreadCount = data.unread_count || 0;
            document.getElementById('unread-count-number').textContent = unreadCount;
            document.getElementById('unread-count-text').textContent = `You have ${unreadCount} unread notification${unreadCount !== 1 ? 's' : ''}`;
        } catch (error) {
            console.error('Error loading notifications:', error);
            document.getElementById('skeleton-loaders').classList.add('hidden');
            document.getElementById('empty-state').classList.remove('hidden');
        }
    }

    // Mark notification as read
    window.markAsRead = async function(notificationId) {
        try {
            const response = await fetch(`/api/notifications/${notificationId}/read`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Remove "Mark as read" button and blue dot
                const notificationCard = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notificationCard) {
                    const markAsReadBtn = notificationCard.querySelector('button');
                    if (markAsReadBtn) markAsReadBtn.remove();
                    const blueDot = notificationCard.querySelector('.bg-blue-500');
                    if (blueDot) blueDot.remove();
                    notificationCard.classList.add('opacity-75');
                }
                
                // Update unread count
                const unreadCount = data.unread_count || 0;
                document.getElementById('unread-count-number').textContent = unreadCount;
                document.getElementById('unread-count-text').textContent = `You have ${unreadCount} unread notification${unreadCount !== 1 ? 's' : ''}`;
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    };

    // Mark all as read
    document.getElementById('mark-all-read-btn').addEventListener('click', async function() {
        try {
            const response = await fetch('/api/notifications/mark-all-read', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Reload notifications
                await loadNotifications();
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    });

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadNotifications);
    } else {
        loadNotifications();
    }

    // Reload on Livewire navigation
    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:navigated', function() {
            setTimeout(loadNotifications, 100);
        });
    }
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.job-seeker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\lysp\Downloads\Job Ad\resources\views/job-seeker/notifications.blade.php ENDPATH**/ ?>