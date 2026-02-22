<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <?php echo $__env->make('partials.employer-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="flex">
        <?php echo $__env->make('partials.employer-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Main Content -->
        <main class="flex-1 p-8 ml-64">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Applicant Tracking</h1>
                        <p class="text-sm text-gray-500 mt-1">Review and manage job applications</p>
                    </div>
                    <button type="button" onclick="exportApplications()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Export</span>
                    </button>
                </div>

                <!-- Summary Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="text-sm text-gray-500 mb-1">Total Applications</div>
                        <div class="text-3xl font-bold text-gray-900"><?php echo e(number_format($stats['all'])); ?></div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="text-sm text-gray-500 mb-1">New Today</div>
                        <div class="text-3xl font-bold text-blue-600"><?php echo e(number_format($stats['new_today'] ?? 0)); ?></div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="text-sm text-gray-500 mb-1">Shortlisted</div>
                        <div class="text-3xl font-bold text-green-600"><?php echo e(number_format($stats['shortlisted'] ?? 0)); ?></div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="text-sm text-gray-500 mb-1">In Interview</div>
                        <div class="text-3xl font-bold text-purple-600"><?php echo e(number_format($stats['reviewing'] ?? 0)); ?></div>
                    </div>
                </div>

                <!-- Status Navigation Tabs -->
                <div id="statusTabs" class="flex items-center space-x-2 mb-6 overflow-x-auto pb-2">
                    <button onclick="filterByStatus('all')" class="status-tab-btn px-4 py-2.5 rounded-lg font-medium text-sm flex items-center space-x-2 <?php echo e($currentStatus === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50'); ?> transition whitespace-nowrap" data-status="all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>All Applications</span>
                        <span class="px-2 py-0.5 text-xs rounded-full <?php echo e($currentStatus === 'all' ? 'bg-blue-500' : 'bg-gray-100'); ?>"><?php echo e($stats['all']); ?></span>
                    </button>
                    <button onclick="filterByStatus('rejected')" class="status-tab-btn px-4 py-2.5 rounded-lg font-medium text-sm flex items-center space-x-2 <?php echo e($currentStatus === 'rejected' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50'); ?> transition whitespace-nowrap" data-status="rejected">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Rejected</span>
                        <span class="px-2 py-0.5 text-xs rounded-full <?php echo e($currentStatus === 'rejected' ? 'bg-blue-500' : 'bg-gray-100'); ?>"><?php echo e($stats['rejected'] ?? 0); ?></span>
                    </button>
                    <button onclick="filterByStatus('shortlisted')" class="status-tab-btn px-4 py-2.5 rounded-lg font-medium text-sm flex items-center space-x-2 <?php echo e($currentStatus === 'shortlisted' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50'); ?> transition whitespace-nowrap" data-status="shortlisted">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Shortlisted</span>
                        <span class="px-2 py-0.5 text-xs rounded-full <?php echo e($currentStatus === 'shortlisted' ? 'bg-blue-500' : 'bg-gray-100'); ?>"><?php echo e($stats['shortlisted'] ?? 0); ?></span>
                    </button>
                    <button onclick="filterByStatus('reviewing')" class="status-tab-btn px-4 py-2.5 rounded-lg font-medium text-sm flex items-center space-x-2 <?php echo e($currentStatus === 'reviewing' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50'); ?> transition whitespace-nowrap" data-status="reviewing">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Interview</span>
                        <span class="px-2 py-0.5 text-xs rounded-full <?php echo e($currentStatus === 'reviewing' ? 'bg-blue-500' : 'bg-gray-100'); ?>"><?php echo e($stats['reviewing'] ?? 0); ?></span>
                    </button>
                    <button onclick="filterByStatus('hired')" class="status-tab-btn px-4 py-2.5 rounded-lg font-medium text-sm flex items-center space-x-2 <?php echo e($currentStatus === 'hired' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50'); ?> transition whitespace-nowrap" data-status="hired">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Selected</span>
                        <span class="px-2 py-0.5 text-xs rounded-full <?php echo e($currentStatus === 'hired' ? 'bg-blue-500' : 'bg-gray-100'); ?>"><?php echo e($stats['hired'] ?? 0); ?></span>
                    </button>
                    <button onclick="filterByStatus('talent_pool')" class="status-tab-btn px-4 py-2.5 rounded-lg font-medium text-sm flex items-center space-x-2 <?php echo e($currentStatus === 'talent_pool' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50'); ?> transition whitespace-nowrap" data-status="talent_pool">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        <span>Talent Pool</span>
                        <span class="px-2 py-0.5 text-xs rounded-full <?php echo e($currentStatus === 'talent_pool' ? 'bg-blue-500' : 'bg-gray-100'); ?>">0</span>
                    </button>
                </div>

                <!-- Search and Filter Bar -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                    <div class="flex items-center space-x-3">
                        <!-- Search Input -->
                        <div class="flex-1 relative">
                            <svg class="absolute left-3.5 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input 
                                type="text" 
                                id="searchInput"
                                value="<?php echo e(request('search')); ?>" 
                                placeholder="Search applicants..." 
                                class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50"
                            >
                        </div>

                        <!-- Job Filter -->
                        <div class="relative">
                            <select 
                                id="jobFilter"
                                class="appearance-none bg-white border border-gray-200 rounded-lg px-4 py-2.5 pr-9 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer font-medium text-gray-700"
                            >
                                <option value="">All Jobs</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <option value="<?php echo e($job->id); ?>" <?php echo e($currentJobId == $job->id ? 'selected' : ''); ?>>
                                        <?php echo e($job->title); ?>

                                    </option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                            <svg class="absolute right-2.5 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        <!-- Status Filter -->
                        <div class="relative">
                            <select 
                                id="statusFilter"
                                class="appearance-none bg-white border border-gray-200 rounded-lg px-4 py-2.5 pr-9 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer font-medium text-gray-700"
                            >
                                <option value="all" <?php echo e($currentStatus === 'all' ? 'selected' : ''); ?>>All Status</option>
                                <option value="pending" <?php echo e($currentStatus === 'pending' ? 'selected' : ''); ?>>Pending</option>
                                <option value="reviewing" <?php echo e($currentStatus === 'reviewing' ? 'selected' : ''); ?>>Reviewing</option>
                                <option value="shortlisted" <?php echo e($currentStatus === 'shortlisted' ? 'selected' : ''); ?>>Shortlisted</option>
                                <option value="rejected" <?php echo e($currentStatus === 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                                <option value="hired" <?php echo e($currentStatus === 'hired' ? 'selected' : ''); ?>>Hired</option>
                            </select>
                            <svg class="absolute right-2.5 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        <!-- More Filters Button -->
                        <button type="button" class="px-4 py-2.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition flex items-center space-x-2 text-sm font-medium text-gray-700">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            <span>More Filters</span>
                        </button>
                    </div>
                </div>

                <!-- Applications List -->
                <div id="applicationsList" class="space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            $initials = strtoupper(substr($application->first_name, 0, 1) . substr($application->last_name, 0, 1));
                            $statusColors = [
                                'pending' => 'bg-blue-50 text-blue-600 border border-blue-200',
                                'reviewing' => 'bg-purple-50 text-purple-600 border border-purple-200',
                                'shortlisted' => 'bg-green-50 text-green-600 border border-green-200',
                                'rejected' => 'bg-red-50 text-red-600 border border-red-200',
                                'hired' => 'bg-green-50 text-green-600 border border-green-200',
                            ];
                            $statusLabels = [
                                'pending' => 'new',
                                'reviewing' => 'interview',
                                'shortlisted' => 'shortlisted',
                                'rejected' => 'rejected',
                                'hired' => 'selected',
                            ];
                            $statusColor = $statusColors[$application->status] ?? 'bg-gray-50 text-gray-600 border border-gray-200';
                            $statusLabel = $statusLabels[$application->status] ?? ucfirst($application->status);
                            
                            // Calculate experience
                            $experience = 'N/A';
                            try {
                                if ($application->jobSeeker && $application->jobSeeker->relationLoaded('experiences') && $application->jobSeeker->experiences) {
                                    $totalYears = 0;
                                    foreach ($application->jobSeeker->experiences as $exp) {
                                        if ($exp && isset($exp->start_date) && $exp->start_date) {
                                            $start = new DateTime($exp->start_date);
                                            $end = (isset($exp->end_date) && $exp->end_date) ? new DateTime($exp->end_date) : new DateTime();
                                            $diff = $start->diff($end);
                                            $totalYears += $diff->y;
                                        }
                                    }
                                    $experience = $totalYears > 0 ? $totalYears . ' years' : 'N/A';
                                }
                            } catch (\Exception $e) {
                                $experience = 'N/A';
                            }
                            
                            // Mock rating (would need to implement rating system)
                            $rating = 4; // Default rating
                        ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
                            <div class="flex items-center justify-between gap-4">
                                <!-- Left Section: Avatar and Info -->
                                <div class="flex items-center space-x-4 flex-1 min-w-0">
                                    <!-- Avatar -->
                                    <?php
                                        $jobSeeker = $application->jobSeeker;
                                        $profilePhoto = $jobSeeker?->profile_photo;
                                        $photoUrl = null;
                                        if ($profilePhoto) {
                                            // Handle both full URLs and relative paths
                                            if (str_starts_with($profilePhoto, 'http://') || str_starts_with($profilePhoto, 'https://')) {
                                                $photoUrl = $profilePhoto;
                                            } else {
                                                $photoUrl = asset('storage/' . $profilePhoto);
                                            }
                                        }
                                    ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($photoUrl): ?>
                                        <img src="<?php echo e($photoUrl); ?>" alt="<?php echo e($application->first_name); ?> <?php echo e($application->last_name); ?>" 
                                             class="w-12 h-12 rounded-md object-cover flex-shrink-0 border border-gray-200"
                                             onerror="this.onerror=null; this.outerHTML='<div class=\'w-12 h-12 rounded-md bg-gradient-to-b from-blue-600 to-blue-400 flex items-center justify-center text-white font-bold text-sm flex-shrink-0\'><?php echo e($initials); ?></div>';">
                                    <?php else: ?>
                                        <div class="w-12 h-12 rounded-md bg-gradient-to-b from-blue-600 to-blue-400 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                            <?php echo e($initials); ?>

                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    
                                    <!-- Applicant Info - Horizontal Layout -->
                                    <div class="flex-1 min-w-0">
                                        <!-- Name and Status Tag -->
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-base font-semibold text-gray-900"><?php echo e($application->first_name); ?> <?php echo e($application->last_name); ?></h3>
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-md <?php echo e($statusColor); ?> whitespace-nowrap">
                                                <?php echo e($statusLabel); ?>

                                            </span>
                                        </div>
                                        
                                        <!-- Job Title, ID, and Location - All on one line -->
                                        <div class="flex items-center space-x-2 text-sm text-gray-900 mb-2 flex-wrap">
                                            <span class="font-medium"><?php echo e($application->jobAdvertisement->title); ?></span>
                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            <span class="text-gray-500">JOB-<?php echo e(str_pad($application->jobAdvertisement->id, 3, '0', STR_PAD_LEFT)); ?></span>
                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span class="text-gray-600"><?php echo e($application->jobAdvertisement->is_remote ? 'Remote' : ($application->jobAdvertisement->location ?? 'Not specified')); ?></span>
                                        </div>
                                        
                                        <!-- Date, Experience, and Rating - All on one line -->
                                        <div class="flex items-center space-x-4 text-sm text-gray-600 flex-wrap">
                                            <div class="flex items-center space-x-1.5">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span><?php echo e($application->created_at->format('Y-m-d')); ?></span>
                                            </div>
                                            <div class="flex items-center space-x-1.5">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <span><?php echo e($experience); ?></span>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= 5; $i++): ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i <= $rating): ?>
                                                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    <?php else: ?>
                                                        <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Section: Action Buttons -->
                                <div class="flex items-center space-x-2 flex-shrink-0">
                                    <!-- Add to Talent Pool -->
                                    <button class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition border border-gray-200" title="Add to Talent Pool">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Download -->
                                    <?php
                                        $resumeUrl = null;
                                        if ($application->resume_path) {
                                            if (str_starts_with($application->resume_path, 'http://') || str_starts_with($application->resume_path, 'https://')) {
                                                $resumeUrl = $application->resume_path;
                                            } else {
                                                $resumeUrl = env('MEDIA_BASE_URL', 'http://31.220.82.129/uploads') . '/' . $application->resume_path;
                                            }
                                        }
                                    ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resumeUrl): ?>
                                        <a href="<?php echo e($resumeUrl); ?>" target="_blank" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition border border-gray-200" title="Download Resume">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                    <?php else: ?>
                                        <button class="p-2 text-gray-400 hover:text-gray-500 rounded-lg transition border border-gray-200 opacity-50 cursor-not-allowed" title="No Resume Available" disabled>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    
                                    <!-- View Button -->
                                    <button onclick="openApplicationModal(<?php echo e($application->id); ?>)" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm whitespace-nowrap">
                                        View
                                    </button>
                                    
                                    <!-- Arrow -->
                                    <button onclick="openApplicationModal(<?php echo e($application->id); ?>)" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium">No applications found</p>
                                <p class="text-sm mt-1">Try adjusting your filters</p>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Application Detail Modal -->
<div id="applicationModal" class="hidden fixed inset-0 bg-transparent h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-5xl h-[65vh] flex flex-col">
        <!-- Modal Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center flex-shrink-0">
            <h3 class="text-xl font-semibold text-gray-900">Application Details</h3>
            <button onclick="closeApplicationModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Content -->
        <div id="applicationModalContent" class="p-6 overflow-y-auto flex-1">
            <div class="text-center py-8">
                <div class="spinner mx-auto mb-4"></div>
                <p class="text-gray-500">Loading application details...</p>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function filterByStatus(status) {
        const url = new URL(window.location.href);
        if (status === 'all') {
            url.searchParams.delete('status');
        } else if (status === 'talent_pool') {
            // Talent pool not implemented yet, show empty for now
            url.searchParams.set('status', 'talent_pool');
        } else {
            url.searchParams.set('status', status);
        }
        window.location.href = url.toString();
    }

    function openApplicationModal(applicationId) {
        const modal = document.getElementById('applicationModal');
        const content = document.getElementById('applicationModalContent');
        
        modal.classList.remove('hidden');
        content.innerHTML = `
            <div class="text-center py-8">
                <div class="spinner mx-auto mb-4"></div>
                <p class="text-gray-500">Loading application details...</p>
            </div>
        `;

        // Fetch application details
        fetch(`/employer/applications/${applicationId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.application) {
                populateApplicationModal(data.application);
            } else {
                content.innerHTML = '<div class="text-red-600">Failed to load application details.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<div class="text-red-600">An error occurred while loading the application.</div>';
        });
    }

    function populateApplicationModal(application) {
        try {
            const content = document.getElementById('applicationModalContent');
            
            if (!application) {
                content.innerHTML = '<div class="text-red-600">Application data is missing.</div>';
                return;
            }
            
            const statusColors = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'reviewing': 'bg-blue-100 text-blue-800',
                'shortlisted': 'bg-purple-100 text-purple-800',
                'rejected': 'bg-red-100 text-red-800',
                'hired': 'bg-green-100 text-green-800',
            };
            
            const statusColor = statusColors[application.status] || 'bg-gray-100 text-gray-800';
            
            // Handle resume URL - can be full URL or relative path
            let resumeUrl = null;
            if (application.resume_path) {
                if (application.resume_path.startsWith('http://') || application.resume_path.startsWith('https://')) {
                    // Already a full URL, use it directly
                    resumeUrl = application.resume_path;
                } else {
                    // Relative path, prepend base URL
                    resumeUrl = `<?php echo e(env('MEDIA_BASE_URL', 'http://31.220.82.129/uploads')); ?>/${application.resume_path}`;
                }
            }

            const jobSeeker = application.job_seeker || application.jobSeeker || {};
            // Handle profile photo URL - can be full URL or relative path
            let profilePhoto = null;
            if (jobSeeker.profile_photo || jobSeeker.profilePhoto) {
                const photo = jobSeeker.profile_photo || jobSeeker.profilePhoto;
                if (photo.startsWith('http://') || photo.startsWith('https://')) {
                    profilePhoto = photo;
                } else {
                    profilePhoto = `<?php echo e(asset('storage/')); ?>/${photo}`;
                }
            }

        content.innerHTML = `
            <div class="space-y-6">
                <!-- Applicant Info -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-4">
                            ${profilePhoto ? `
                                <img src="${profilePhoto}" alt="${application.first_name} ${application.last_name}" class="h-14 w-14 rounded-full object-cover border border-gray-200" onerror="this.onerror=null; this.outerHTML='<div class=\\'h-14 w-14 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-lg\\'>${application.first_name.charAt(0)}${application.last_name.charAt(0)}</div>';">
                            ` : `
                                <div class="h-14 w-14 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-lg">
                                    ${application.first_name.charAt(0)}${application.last_name.charAt(0)}
                                </div>
                            `}
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">${application.first_name} ${application.last_name}</h4>
                                <p class="text-sm text-gray-600 mt-1">${application.email}</p>
                                ${application.phone ? `<p class="text-sm text-gray-600 mt-1">${application.phone}</p>` : ''}
                                ${jobSeeker.location ? `<p class="text-sm text-gray-600 mt-1">📍 ${jobSeeker.location}</p>` : ''}
                            </div>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded ${statusColor}">
                            ${application.status.charAt(0).toUpperCase() + application.status.slice(1)}
                        </span>
                    </div>
                </div>

                <!-- Bio Section -->
                ${jobSeeker.bio ? `
                    <div class="border-b border-gray-200 pb-6">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">About</h5>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">${jobSeeker.bio}</p>
                    </div>
                ` : ''}

                <!-- Applied for and Resume - Side by Side -->
                <div class="grid grid-cols-2 gap-4 border-b border-gray-200 pb-6">
                    <!-- Job Info -->
                    <div>
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Applied for:</h5>
                        <p class="text-base font-medium text-gray-900">${application.job_advertisement.title}</p>
                        <p class="text-sm text-gray-600 mt-1">${application.job_advertisement.company.name}</p>
                        <p class="text-xs text-gray-500 mt-2">Applied on ${new Date(application.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                    </div>
                    
                    <!-- Resume -->
                    <div>
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Resume</h5>
                        ${resumeUrl ? `
                            <a href="${resumeUrl}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                View Resume
                            </a>
                        ` : '<p class="text-sm text-gray-500">No resume uploaded</p>'}
                    </div>
                </div>

                <!-- Skills and Languages - Side by Side -->
                <div class="grid grid-cols-2 gap-4 border-b border-gray-200 pb-6">
                    <!-- Skills Section -->
                    <div>
                        ${jobSeeker && jobSeeker.skills && Array.isArray(jobSeeker.skills) && jobSeeker.skills.length > 0 ? `
                            <h5 class="text-sm font-medium text-gray-700 mb-3">Skills</h5>
                            <div class="flex flex-wrap gap-2">
                                ${jobSeeker.skills.map(skill => `
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                        ${(skill && (skill.skill_name || skill.name)) || 'Skill'}
                                        ${skill && skill.proficiency_level ? ` (${skill.proficiency_level})` : ''}
                                    </span>
                                `).join('')}
                            </div>
                        ` : '<p class="text-sm text-gray-500">No skills added</p>'}
                    </div>
                    
                    <!-- Languages Section -->
                    <div>
                        ${jobSeeker && jobSeeker.languages && Array.isArray(jobSeeker.languages) && jobSeeker.languages.length > 0 ? `
                            <h5 class="text-sm font-medium text-gray-700 mb-3">Languages</h5>
                            <div class="flex flex-wrap gap-2">
                                ${jobSeeker.languages.map(lang => `
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                        ${(lang && (lang.language_name || lang.name)) || 'Language'}
                                        ${lang && lang.proficiency_level ? ` (${lang.proficiency_level})` : ''}
                                    </span>
                                `).join('')}
                            </div>
                        ` : '<p class="text-sm text-gray-500">No languages added</p>'}
                    </div>
                </div>

                <!-- Work Experience and Education - Side by Side -->
                <div class="grid grid-cols-2 gap-4 border-b border-gray-200 pb-6">
                    <!-- Work Experience Section -->
                    <div>
                        ${jobSeeker && jobSeeker.experiences && Array.isArray(jobSeeker.experiences) && jobSeeker.experiences.length > 0 ? `
                            <h5 class="text-sm font-medium text-gray-700 mb-3">Work Experience</h5>
                            <div class="space-y-3">
                                ${jobSeeker.experiences.map(exp => {
                                    if (!exp) return '';
                                    const startDate = exp.start_date ? new Date(exp.start_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' }) : '';
                                    const endDate = exp.end_date ? new Date(exp.end_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' }) : (exp.is_current ? 'Present' : '');
                                    return `
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-sm font-semibold text-gray-900">${exp.job_title || 'Position'}</p>
                                            <p class="text-xs text-gray-600">${exp.company_name || ''} ${exp.company_name && exp.location ? '•' : ''} ${exp.location || ''}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                ${startDate}${endDate ? ' - ' + endDate : ''}
                                            </p>
                                            ${exp.description ? `<p class="text-xs text-gray-600 mt-2">${exp.description}</p>` : ''}
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        ` : '<p class="text-sm text-gray-500">No work experience added</p>'}
                    </div>
                    
                    <!-- Education Section -->
                    <div>
                        ${jobSeeker && jobSeeker.educations && Array.isArray(jobSeeker.educations) && jobSeeker.educations.length > 0 ? `
                            <h5 class="text-sm font-medium text-gray-700 mb-3">Education</h5>
                            <div class="space-y-3">
                                ${jobSeeker.educations.map(edu => {
                                    if (!edu) return '';
                                    const startDate = edu.start_date ? new Date(edu.start_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' }) : '';
                                    const endDate = edu.end_date ? new Date(edu.end_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short' }) : (edu.is_current ? 'Present' : '');
                                    return `
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-sm font-semibold text-gray-900">${edu.degree || 'Degree'}</p>
                                            <p class="text-xs text-gray-600">${edu.institution_name || ''} ${edu.institution_name && edu.field_of_study ? '•' : ''} ${edu.field_of_study || ''}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                ${startDate}${endDate ? ' - ' + endDate : ''}
                                            </p>
                                            ${edu.gpa ? `<p class="text-xs text-gray-600 mt-1">GPA: ${edu.gpa}</p>` : ''}
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        ` : '<p class="text-sm text-gray-500">No education added</p>'}
                    </div>
                </div>

                <!-- Certifications Section -->
                ${jobSeeker && jobSeeker.certifications && Array.isArray(jobSeeker.certifications) && jobSeeker.certifications.length > 0 ? `
                    <div class="border-b border-gray-200 pb-6">
                        <h5 class="text-sm font-medium text-gray-700 mb-3">Certifications</h5>
                        <div class="space-y-3">
                            ${jobSeeker.certifications.map(cert => {
                                if (!cert) return '';
                                const issueDate = cert.issue_date ? new Date(cert.issue_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long' }) : '';
                                const expiryDate = cert.expiry_date ? new Date(cert.expiry_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long' }) : '';
                                return `
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <p class="text-sm font-semibold text-gray-900">${cert.certification_name || cert.name || 'Certification'}</p>
                                        <p class="text-xs text-gray-600">${cert.issuing_organization || ''}</p>
                                        ${issueDate ? `<p class="text-xs text-gray-500 mt-1">Issued: ${issueDate}</p>` : ''}
                                        ${expiryDate ? `<p class="text-xs text-gray-500">Expires: ${expiryDate}</p>` : ''}
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                ` : ''}

                <!-- Additional Info -->
                ${jobSeeker.employment_status || jobSeeker.highest_education ? `
                    <div class="border-b border-gray-200 pb-6">
                        <h5 class="text-sm font-medium text-gray-700 mb-3">Additional Information</h5>
                        <div class="space-y-2 text-sm">
                            ${jobSeeker.employment_status ? `<p class="text-gray-700"><span class="font-medium">Employment Status:</span> ${jobSeeker.employment_status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>` : ''}
                            ${jobSeeker.highest_education ? `<p class="text-gray-700"><span class="font-medium">Highest Education:</span> ${jobSeeker.highest_education.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>` : ''}
                            ${jobSeeker.driving_license ? `<p class="text-gray-700"><span class="font-medium">Driving License:</span> Yes</p>` : ''}
                        </div>
                    </div>
                ` : ''}

                <!-- Cover Letter -->
                ${application.cover_letter ? `
                    <div class="border-b border-gray-200 pb-6">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Cover Letter</h5>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">${application.cover_letter}</p>
                    </div>
                ` : ''}

                <!-- Status Actions -->
                <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-3">Update Status</h5>
                    <div class="space-y-2">
                        <label class="flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 transition ${application.status === 'pending' ? 'bg-blue-50 border-blue-500' : ''}">
                            <span class="text-sm font-medium text-gray-700">Pending</span>
                            <input type="radio" name="status_${application.id}" value="pending" 
                                ${application.status === 'pending' ? 'checked' : ''}
                                onchange="updateApplicationStatus(${application.id}, 'pending')"
                                class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                        </label>
                        <label class="flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 transition ${application.status === 'reviewing' ? 'bg-blue-50 border-blue-500' : ''}">
                            <span class="text-sm font-medium text-gray-700">Reviewing</span>
                            <input type="radio" name="status_${application.id}" value="reviewing" 
                                ${application.status === 'reviewing' ? 'checked' : ''}
                                onchange="updateApplicationStatus(${application.id}, 'reviewing')"
                                class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                        </label>
                        <label class="flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 transition ${application.status === 'shortlisted' ? 'bg-blue-50 border-blue-500' : ''}">
                            <span class="text-sm font-medium text-gray-700">Shortlisted</span>
                            <input type="radio" name="status_${application.id}" value="shortlisted" 
                                ${application.status === 'shortlisted' ? 'checked' : ''}
                                onchange="updateApplicationStatus(${application.id}, 'shortlisted')"
                                class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                        </label>
                        <label class="flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 transition ${application.status === 'hired' ? 'bg-blue-50 border-blue-500' : ''}">
                            <span class="text-sm font-medium text-gray-700">Hired</span>
                            <input type="radio" name="status_${application.id}" value="hired" 
                                ${application.status === 'hired' ? 'checked' : ''}
                                onchange="updateApplicationStatus(${application.id}, 'hired')"
                                class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                        </label>
                        <label class="flex items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50 transition ${application.status === 'rejected' ? 'bg-blue-50 border-blue-500' : ''}">
                            <span class="text-sm font-medium text-gray-700">Rejected</span>
                            <input type="radio" name="status_${application.id}" value="rejected" 
                                ${application.status === 'rejected' ? 'checked' : ''}
                                onchange="updateApplicationStatus(${application.id}, 'rejected')"
                                class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                        </label>
                    </div>
                </div>
            </div>
        `;
        } catch (error) {
            console.error('Error populating application modal:', error);
            const content = document.getElementById('applicationModalContent');
            content.innerHTML = `
                <div class="text-red-600 p-4">
                    <p class="font-medium mb-2">Error loading application details</p>
                    <p class="text-sm">${error.message || 'An unexpected error occurred'}</p>
                </div>
            `;
        }
    }

    function closeApplicationModal() {
        document.getElementById('applicationModal').classList.add('hidden');
    }

    function updateApplicationStatus(applicationId, status) {
        // Show custom confirmation toast
        if (typeof window.showInfoToast === 'function') {
            window.showInfoToast(`Updating status to "${status}"...`, 2000);
        }

        fetch(`/employer/applications/${applicationId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => {
            // Check if response is ok
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.application) {
                // Update modal content in real-time
                try {
                    populateApplicationModal(data.application);
                } catch (error) {
                    console.error('Error populating modal:', error);
                    // Don't show error toast here, just log it
                }
                
                // Update table row in real-time
                try {
                    updateTableRowStatus(data.application);
                } catch (error) {
                    console.error('Error updating table:', error);
                    // Don't show error toast here, just log it
                }
                
                // Show success toast
                if (typeof window.showSuccessToast === 'function') {
                    window.showSuccessToast('Application status updated successfully!');
                }
            } else {
                if (typeof window.showErrorToast === 'function') {
                    window.showErrorToast('Failed to update status: ' + (data.message || 'Unknown error'));
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const errorMessage = error.message || (error.error || 'An error occurred while updating the status.');
            if (typeof window.showErrorToast === 'function') {
                window.showErrorToast(errorMessage);
            }
        });
    }

    function updateTableRowStatus(application) {
        // Find the table row for this application
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const viewDetailsBtn = row.querySelector('button[onclick*="openApplicationModal"]');
            if (viewDetailsBtn && viewDetailsBtn.getAttribute('onclick').includes(`openApplicationModal(${application.id})`)) {
                // Update the status badge in the table
                const statusCell = row.querySelector('td:nth-child(5)');
                if (statusCell) {
                    const statusColors = {
                        'pending': 'bg-yellow-100 text-yellow-800',
                        'reviewing': 'bg-blue-100 text-blue-800',
                        'shortlisted': 'bg-purple-100 text-purple-800',
                        'rejected': 'bg-red-100 text-red-800',
                        'hired': 'bg-green-100 text-green-800',
                    };
                    
                    const statusColor = statusColors[application.status] || 'bg-gray-100 text-gray-800';
                    const statusText = application.status.charAt(0).toUpperCase() + application.status.slice(1);
                    
                    statusCell.innerHTML = `<span class="px-3 py-1 text-xs font-medium rounded ${statusColor}">${statusText}</span>`;
                }
            }
        });
    }


    // Close modal when clicking outside
    document.getElementById('applicationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeApplicationModal();
        }
    });

    // Real-time search and filtering
    let searchTimeout;
    let currentFilters = {
        search: document.getElementById('searchInput').value,
        job_id: document.getElementById('jobFilter').value,
        status: document.getElementById('statusFilter').value
    };

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function loadApplications() {
        const searchInput = document.getElementById('searchInput');
        const jobFilter = document.getElementById('jobFilter');
        const statusFilter = document.getElementById('statusFilter');
        const applicationsList = document.getElementById('applicationsList');
        
        // Show loading state
        applicationsList.innerHTML = `
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="text-gray-500">
                    <div class="spinner mx-auto mb-4"></div>
                    <p class="text-lg font-medium">Loading applications...</p>
                </div>
            </div>
        `;
        
        const params = new URLSearchParams({
            search: searchInput.value,
            job_id: jobFilter.value || '',
            status: statusFilter.value || 'all'
        });
        
        fetch(`/employer/applications/data?${params.toString()}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.applications && data.applications.length > 0) {
                renderApplications(data.applications);
            } else {
                applicationsList.innerHTML = `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                        <div class="text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-lg font-medium">No applications found</p>
                            <p class="text-sm mt-1">Try adjusting your filters</p>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading applications:', error);
            applicationsList.innerHTML = `
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="text-red-500">
                        <p class="text-lg font-medium">Error loading applications</p>
                        <p class="text-sm mt-1">Please try again</p>
                    </div>
                </div>
            `;
        });
    }

    function renderApplications(applications) {
        const applicationsList = document.getElementById('applicationsList');
        const statusColors = {
            'pending': 'bg-blue-50 text-blue-600 border border-blue-200',
            'reviewing': 'bg-purple-50 text-purple-600 border border-purple-200',
            'shortlisted': 'bg-green-50 text-green-600 border border-green-200',
            'rejected': 'bg-red-50 text-red-600 border border-red-200',
            'hired': 'bg-green-50 text-green-600 border border-green-200',
        };
        const statusLabels = {
            'pending': 'new',
            'reviewing': 'interview',
            'shortlisted': 'shortlisted',
            'rejected': 'rejected',
            'hired': 'selected',
        };
        
        applicationsList.innerHTML = applications.map(application => {
            const statusColor = statusColors[application.status] || 'bg-gray-50 text-gray-600 border border-gray-200';
            const statusLabel = statusLabels[application.status] || application.status;
            
            let starsHtml = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= application.rating) {
                    starsHtml += `<svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>`;
                } else {
                    starsHtml += `<svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>`;
                }
            }
            
            // Handle profile photo
            const jobSeeker = application.job_seeker || application.jobSeeker || {};
            let profilePhoto = null;
            let initials = application.initials || (application.first_name ? (application.first_name.charAt(0) + (application.last_name || '').charAt(0)).toUpperCase() : 'JD');
            
            if (jobSeeker && (jobSeeker.profile_photo || jobSeeker.profilePhoto)) {
                const photo = jobSeeker.profile_photo || jobSeeker.profilePhoto;
                if (photo.startsWith('http://') || photo.startsWith('https://')) {
                    profilePhoto = photo;
                } else {
                    profilePhoto = `<?php echo e(asset('storage/')); ?>/${photo}`;
                }
            }
            
            const avatarHtml = profilePhoto 
                ? `<img src="${profilePhoto}" alt="${application.first_name} ${application.last_name}" class="w-12 h-12 rounded-md object-cover flex-shrink-0 border border-gray-200" onerror="this.onerror=null; this.outerHTML='<div class=\\'w-12 h-12 rounded-md bg-gradient-to-b from-blue-600 to-blue-400 flex items-center justify-center text-white font-bold text-sm flex-shrink-0\\'>${initials}</div>';" />`
                : `<div class="w-12 h-12 rounded-md bg-gradient-to-b from-blue-600 to-blue-400 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">${initials}</div>`;
            
            // Handle resume URL - can be full URL or relative path
            let resumeUrl = null;
            if (application.resume_path) {
                if (application.resume_path.startsWith('http://') || application.resume_path.startsWith('https://')) {
                    resumeUrl = application.resume_path;
                } else {
                    resumeUrl = `<?php echo e(env('MEDIA_BASE_URL', 'http://31.220.82.129/uploads')); ?>/${application.resume_path}`;
                }
            }
            
            return `
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center space-x-4 flex-1 min-w-0">
                            ${avatarHtml}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-base font-semibold text-gray-900">${application.first_name} ${application.last_name}</h3>
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-md ${statusColor} whitespace-nowrap">
                                        ${statusLabel}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2 text-sm text-gray-900 mb-2 flex-wrap">
                                    <span class="font-medium">${application.job_title}</span>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="text-gray-500">JOB-${String(application.job_id).padStart(3, '0')}</span>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-gray-600">${application.location}</span>
                                </div>
                                <div class="flex items-center space-x-4 text-sm text-gray-600 flex-wrap">
                                    <div class="flex items-center space-x-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>${application.applied_date}</span>
                                    </div>
                                    <div class="flex items-center space-x-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>${application.experience}</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        ${starsHtml}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <button class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition border border-gray-200" title="Add to Talent Pool">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </button>
                            ${resumeUrl ? `
                                <a href="${resumeUrl}" target="_blank" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition border border-gray-200" title="Download Resume">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                            ` : `
                                <button class="p-2 text-gray-400 hover:text-gray-500 rounded-lg transition border border-gray-200 opacity-50 cursor-not-allowed" title="No Resume Available" disabled>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </button>
                            `}
                            <button onclick="openApplicationModal(${application.id})" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm whitespace-nowrap">
                                View
                            </button>
                            <button onclick="openApplicationModal(${application.id})" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function exportApplications() {
        const searchInput = document.getElementById('searchInput');
        const jobFilter = document.getElementById('jobFilter');
        const statusFilter = document.getElementById('statusFilter');
        
        const params = new URLSearchParams({
            search: searchInput.value,
            job_id: jobFilter.value || '',
            status: statusFilter.value || 'all'
        });
        
        window.location.href = `/employer/applications/export?${params.toString()}`;
    }

    // Real-time search with debouncing
    document.getElementById('searchInput').addEventListener('input', debounce(function() {
        loadApplications();
    }, 500));

    // Filter change handlers
    document.getElementById('jobFilter').addEventListener('change', function() {
        loadApplications();
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        loadApplications();
        // Update status tab styling
        const status = this.value;
        document.querySelectorAll('.status-tab-btn').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200');
            const badge = btn.querySelector('span:last-child');
            if (badge) {
                badge.classList.remove('bg-blue-500');
                badge.classList.add('bg-gray-100');
            }
        });
        
        const activeBtn = document.querySelector(`[data-status="${status}"]`);
        if (activeBtn) {
            activeBtn.classList.add('bg-blue-600', 'text-white');
            activeBtn.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-200');
            const badge = activeBtn.querySelector('span:last-child');
            if (badge) {
                badge.classList.add('bg-blue-500');
                badge.classList.remove('bg-gray-100');
            }
        }
    });

    // Update status tab filter
    function filterByStatus(status) {
        document.getElementById('statusFilter').value = status;
        loadApplications();
        
        // Update active tab styling
        document.querySelectorAll('.status-tab-btn').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200');
            const badge = btn.querySelector('span:last-child');
            if (badge) {
                badge.classList.remove('bg-blue-500');
                badge.classList.add('bg-gray-100');
            }
        });
        
        const activeBtn = document.querySelector(`[data-status="${status}"]`);
        if (activeBtn) {
            activeBtn.classList.add('bg-blue-600', 'text-white');
            activeBtn.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-200');
            const badge = activeBtn.querySelector('span:last-child');
            if (badge) {
                badge.classList.add('bg-blue-500');
                badge.classList.remove('bg-gray-100');
            }
        }
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\lysp\Downloads\Job Ad\resources\views/employer/applications/index.blade.php ENDPATH**/ ?>