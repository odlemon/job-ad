<?php $__env->startSection('content'); ?>
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col">
        <?php echo $__env->make('partials.job-seeker-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        
        <!-- Main Content -->
        <main class="flex-1 p-8 bg-gray-50">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Job Applications Tracker</h1>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-5 gap-4 mb-6">
                    <!-- Applied -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Applied</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900"><?php echo e($stats['pending'] ?? $applications->total()); ?></div>
                    </div>

                    <!-- In Review -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">In Review</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900"><?php echo e($stats['reviewing'] ?? 0); ?></div>
                    </div>

                    <!-- Interview -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Interview</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900"><?php echo e($stats['interview'] ?? 0); ?></div>
                    </div>

                    <!-- Offered -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Offered</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900"><?php echo e($stats['offered'] ?? $stats['hired'] ?? 0); ?></div>
                    </div>

                    <!-- Rejected -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Rejected</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900"><?php echo e($stats['rejected'] ?? 0); ?></div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="mb-6">
                    <div class="flex gap-2">
                        <button id="tab-all" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium text-sm transition">
                            All Applications (<?php echo e($applications->total()); ?>)
                        </button>
                        <button id="tab-interviews" class="px-6 py-2.5 bg-white text-gray-700 rounded-lg font-medium text-sm border border-gray-200 hover:bg-gray-50 transition">
                            Interviews
                        </button>
                        <button id="tab-offers" class="px-6 py-2.5 bg-white text-gray-700 rounded-lg font-medium text-sm border border-gray-200 hover:bg-gray-50 transition">
                            Offers
                        </button>
                    </div>
                </div>

                <!-- Applications List -->
                <div class="space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            $job = $application->jobAdvertisement;
                            $company = $job->company ?? null;
                            // Map application status to display status
                            $statusConfig = [
                                'pending' => ['label' => 'Applied', 'color' => 'bg-blue-100 text-blue-700', 'filter' => 'applied'],
                                'reviewing' => ['label' => 'In Review', 'color' => 'bg-yellow-100 text-yellow-800', 'filter' => 'reviewing'],
                                'shortlisted' => ['label' => 'Interview', 'color' => 'bg-purple-100 text-purple-700', 'filter' => 'interview'],
                                'rejected' => ['label' => 'Rejected', 'color' => 'bg-red-100 text-red-700', 'filter' => 'rejected'],
                                'hired' => ['label' => 'Offered', 'color' => 'bg-green-100 text-green-700', 'filter' => 'offered'],
                            ];
                            $status = $statusConfig[$application->status] ?? $statusConfig['pending'];
                        ?>
                        
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition application-card" data-status="<?php echo e($application->status); ?>" data-filter="<?php echo e($status['filter']); ?>">
                            <div class="flex items-start gap-4">
                                <!-- Company Logo -->
                                <div class="flex-shrink-0">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company && $company->logo): ?>
                                        <?php
                                            // Handle both full URLs and relative paths
                                            $logoUrl = $company->logo;
                                            if (!str_starts_with($logoUrl, 'http://') && !str_starts_with($logoUrl, 'https://')) {
                                                // It's a relative path, prepend storage path
                                                $logoUrl = asset('storage/' . $logoUrl);
                                            }
                                        ?>
                                        <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($company->name); ?>" class="w-14 h-14 rounded-lg object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center\'><span class=\'text-lg font-semibold text-gray-600\'><?php echo e(substr($company->name ?? 'C', 0, 1)); ?></span></div>';">
                                    <?php else: ?>
                                        <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center">
                                            <span class="text-lg font-semibold text-gray-600"><?php echo e(substr($company->name ?? 'C', 0, 1)); ?></span>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <!-- Application Details -->
                                <div class="flex-1 min-w-0">
                                    <!-- Title, Company and Status Badge -->
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-base font-bold text-gray-900 mb-1"><?php echo e($job->title); ?></h3>
                                            <p class="text-sm text-gray-600"><?php echo e($company->name ?? 'Unknown Company'); ?></p>
                                        </div>
                                        <!-- Status Badge -->
                                        <span class="ml-4 px-3 py-1 rounded-md text-xs font-medium whitespace-nowrap <?php echo e($status['color']); ?>">
                                            <svg class="w-3.5 h-3.5 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->status === 'shortlisted'): ?>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                <?php elseif($application->status === 'reviewing'): ?>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                <?php elseif($application->status === 'hired'): ?>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                <?php elseif($application->status === 'rejected'): ?>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                <?php else: ?>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </svg>
                                            <?php echo e($status['label']); ?>

                                        </span>
                                    </div>

                                    <!-- Dates -->
                                    <div class="flex items-center gap-8 text-sm text-gray-600 mb-3">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-gray-700">Applied: <?php echo e($application->created_at->format('M j, Y')); ?></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-gray-700">Updated: <?php echo e($application->updated_at->diffForHumans()); ?></span>
                                        </div>
                                    </div>

                                    <!-- Interview Scheduled (if applicable) -->
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->status === 'shortlisted' && $application->interview_date): ?>
                                        <div class="bg-purple-50 border border-purple-200 rounded-md px-3 py-2.5 mb-3">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span class="text-sm text-purple-900">Interview Scheduled: <?php echo e(\Carbon\Carbon::parse($application->interview_date)->format('M j, Y \a\t g:i A')); ?></span>
                                            </div>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <!-- Notes (if any) -->
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->notes): ?>
                                        <div class="flex items-start gap-2 text-sm text-gray-700 mb-3 application-notes" data-application-id="<?php echo e($application->id); ?>">
                                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="text-gray-700"><?php echo e($application->notes); ?></span>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-3">
                                        <button type="button" onclick="openApplicationModal(<?php echo e($application->id); ?>)" 
                                            class="px-5 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition">
                                            View Details
                                        </button>
                                        <button type="button" onclick="openAddNoteModal(<?php echo e($application->id); ?>)" 
                                            class="px-5 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50 transition">
                                            Add Note
                                        </button>
                                        <button type="button" onclick="confirmDelete(<?php echo e($application->id); ?>)" 
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-md transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No applications found</h3>
                            <p class="text-gray-500 mb-6">You haven't applied to any jobs yet.</p>
                            <a href="/jobs" wire:navigate class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                                Browse Jobs
                            </a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($applications->hasPages()): ?>
                    <div class="mt-8">
                        <?php echo e($applications->links()); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </main>
    </div>

<!-- Application Detail Modal -->
<div id="applicationModal" class="hidden fixed inset-0 bg-transparent h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center flex-shrink-0 rounded-t-lg">
            <h3 class="text-xl font-bold text-gray-900">Application Details</h3>
            <button onclick="closeApplicationModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Content -->
        <div id="applicationModalContent" class="p-6 overflow-y-auto flex-1">
            <div class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="text-gray-500 mt-4">Loading application details...</p>
            </div>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div id="addNoteModal" class="hidden fixed inset-0 bg-transparent h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md">
        <!-- Modal Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-lg">
            <h3 class="text-lg font-bold text-gray-900">Add Note</h3>
            <button onclick="closeAddNoteModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6">
            <textarea id="noteText" rows="4" placeholder="Add your note here..." 
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
            <div class="flex gap-3 mt-4">
                <button id="saveNoteBtn" onclick="saveNote()" class="flex-1 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center justify-center gap-2">
                    <span id="saveNoteText">Save Note</span>
                    <div id="saveNoteSpinner" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </button>
                <button onclick="closeAddNoteModal()" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentApplicationId = null;

// Tab filtering
document.getElementById('tab-all')?.addEventListener('click', function() {
    setActiveTab(this);
    filterApplications('all');
});

document.getElementById('tab-interviews')?.addEventListener('click', function() {
    setActiveTab(this);
    filterApplications('interview');
});

document.getElementById('tab-offers')?.addEventListener('click', function() {
    setActiveTab(this);
    filterApplications('offered');
});

function setActiveTab(activeBtn) {
    document.querySelectorAll('[id^="tab-"]').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white');
        btn.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200');
    });
    activeBtn.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-200');
    activeBtn.classList.add('bg-blue-600', 'text-white');
}

function filterApplications(filter) {
    const cards = document.querySelectorAll('.application-card');
    cards.forEach(card => {
        if (filter === 'all' || card.dataset.filter === filter) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function openApplicationModal(applicationId) {
    const modal = document.getElementById('applicationModal');
    const content = document.getElementById('applicationModalContent');
    
    modal.classList.remove('hidden');
    
    // Show loading state
    content.innerHTML = `
        <div class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="text-gray-500 mt-4">Loading application details...</p>
        </div>
    `;
    
    // Fetch application details
    fetch(`/job-seeker/applications/${applicationId}`, {
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
    const content = document.getElementById('applicationModalContent');
    
    const statusConfig = {
        'pending': { label: 'Applied', color: 'bg-blue-100 text-blue-700' },
        'reviewing': { label: 'In Review', color: 'bg-yellow-100 text-yellow-800' },
        'shortlisted': { label: 'Interview', color: 'bg-purple-100 text-purple-700' },
        'rejected': { label: 'Rejected', color: 'bg-red-100 text-red-700' },
        'hired': { label: 'Offered', color: 'bg-green-100 text-green-700' },
    };
    
    const status = statusConfig[application.status] || statusConfig['pending'];
    
    const job = application.job_advertisement;
    const company = job.company;
    // Handle both full URLs and relative paths
    let companyLogo = null;
    if (company && company.logo) {
        if (company.logo.startsWith('http://') || company.logo.startsWith('https://')) {
            companyLogo = company.logo;
        } else {
            companyLogo = `<?php echo e(asset('storage/')); ?>/${company.logo}`;
        }
    }

    content.innerHTML = `
        <div class="space-y-6">
            <!-- Application Header -->
            <div class="flex items-start justify-between pb-6 border-b border-gray-200">
                <div class="flex items-center gap-4">
                    ${companyLogo ? `
                        <img src="${companyLogo}" alt="${company.name}" class="w-16 h-16 rounded-lg object-cover" onerror="this.onerror=null; this.outerHTML='<div class=\\'w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center\\'><svg class=\\'w-8 h-8 text-gray-400\\' fill=\\'none\\' stroke=\\'currentColor\\' viewBox=\\'0 0 24 24\\'><path stroke-linecap=\\'round\\' stroke-linejoin=\\'round\\' stroke-width=\\'2\\' d=\\'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4\\'></path></svg></div>';">
                    ` : `
                        <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    `}
                    <div>
                        <h4 class="text-xl font-bold text-gray-900 mb-1">${job.title}</h4>
                        <p class="text-gray-600 mb-2">${company.name}</p>
                        <p class="text-sm text-gray-500">Applied on ${new Date(application.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-md text-xs font-medium ${status.color}">
                    ${status.label}
                </span>
            </div>

            <!-- Job Details -->
            <div>
                <h5 class="text-base font-bold text-gray-900 mb-3">Job Details</h5>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    ${job.location ? `<div><span class="text-gray-600">Location:</span> <span class="text-gray-900 font-medium">${job.location}</span></div>` : ''}
                    ${job.employment_type ? `<div><span class="text-gray-600">Type:</span> <span class="text-gray-900 font-medium">${job.employment_type}</span></div>` : ''}
                    ${job.salary_min || job.salary_max ? `<div><span class="text-gray-600">Salary:</span> <span class="text-gray-900 font-medium">SCR ${job.salary_min?.toLocaleString() || '0'} - ${job.salary_max?.toLocaleString() || '0'}</span></div>` : ''}
                    ${job.category ? `<div><span class="text-gray-600">Category:</span> <span class="text-gray-900 font-medium">${job.category.name}</span></div>` : ''}
                </div>
            </div>

            <!-- Job Description -->
            ${job.description ? `
                <div>
                    <h5 class="text-base font-bold text-gray-900 mb-3">Job Description</h5>
                    <p class="text-sm text-gray-700 leading-relaxed">${job.description}</p>
                </div>
            ` : ''}

            <!-- Cover Letter -->
            ${application.cover_letter ? `
                <div>
                    <h5 class="text-base font-bold text-gray-900 mb-3">Your Cover Letter</h5>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 leading-relaxed">
                        ${application.cover_letter}
                    </div>
                </div>
            ` : ''}

            <!-- Your Note -->
            ${application.notes ? `
                <div class="application-note-detail">
                    <h5 class="text-base font-bold text-gray-900 mb-3">Your Note</h5>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 leading-relaxed">
                        ${application.notes}
                    </div>
                </div>
            ` : ''}

            <!-- Actions -->
            <div class="flex gap-3 pt-4 border-t border-gray-200">
                <button onclick="withdrawApplication(${application.id}, '${application.status}')" 
                    class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                    Withdraw Application
                </button>
                <a href="/jobs/${job.id}" target="_blank"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium inline-block">
                    View Job Posting
                </a>
            </div>
        </div>
    `;
}

function closeApplicationModal() {
    document.getElementById('applicationModal').classList.add('hidden');
}

function openAddNoteModal(applicationId) {
    currentApplicationId = applicationId;
    
    // Find existing note in the card
    const cards = document.querySelectorAll('.application-card');
    let existingNote = '';
    
    cards.forEach(card => {
        const viewBtn = card.querySelector(`button[onclick*="openApplicationModal(${applicationId})"]`);
        if (viewBtn) {
            const notesSection = card.querySelector(`.application-notes[data-application-id="${applicationId}"]`);
            if (notesSection) {
                const noteSpan = notesSection.querySelector('span');
                if (noteSpan) {
                    existingNote = noteSpan.textContent.trim();
                }
            }
        }
    });
    
    document.getElementById('addNoteModal').classList.remove('hidden');
    document.getElementById('noteText').value = existingNote;
    document.getElementById('noteText').focus();
}

function closeAddNoteModal() {
    document.getElementById('addNoteModal').classList.add('hidden');
    currentApplicationId = null;
    // Reset button state
    const saveBtn = document.getElementById('saveNoteBtn');
    const saveText = document.getElementById('saveNoteText');
    const saveSpinner = document.getElementById('saveNoteSpinner');
    if (saveBtn) {
        saveBtn.disabled = false;
        saveText.classList.remove('hidden');
        saveSpinner.classList.add('hidden');
    }
}

function saveNote() {
    const noteText = document.getElementById('noteText').value;
    const saveBtn = document.getElementById('saveNoteBtn');
    const saveText = document.getElementById('saveNoteText');
    const saveSpinner = document.getElementById('saveNoteSpinner');
    
    if (!noteText.trim()) {
        if (typeof window.showWarningToast === 'function') {
            window.showWarningToast('Please enter a note');
        } else {
            alert('Please enter a note');
        }
        return;
    }
    
    // Show loading state
    saveBtn.disabled = true;
    saveText.classList.add('hidden');
    saveSpinner.classList.remove('hidden');
    
    fetch(`/job-seeker/applications/${currentApplicationId}/note`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ note: noteText })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            if (typeof window.showSuccessToast === 'function') {
                window.showSuccessToast('Note saved successfully');
            }
            closeAddNoteModal();
            // Update the note in real time without page reload
            updateNoteInCard(currentApplicationId, noteText);
            // Update note in detail modal if it's open
            updateNoteInDetailModal(currentApplicationId, noteText);
        } else {
            if (typeof window.showErrorToast === 'function') {
                window.showErrorToast('Failed to save note');
            } else {
                alert('Failed to save note');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof window.showErrorToast === 'function') {
            window.showErrorToast('An error occurred while saving the note.');
        } else {
            alert('An error occurred while saving the note.');
        }
    })
    .finally(() => {
        // Hide loading state
        saveBtn.disabled = false;
        saveText.classList.remove('hidden');
        saveSpinner.classList.add('hidden');
    });
}

function updateNoteInDetailModal(applicationId, noteText) {
    const modal = document.getElementById('applicationModal');
    if (modal && !modal.classList.contains('hidden')) {
        // Check if this is the current application in the modal by checking the withdraw button
        const modalContent = document.getElementById('applicationModalContent');
        if (modalContent) {
            const withdrawBtn = modalContent.querySelector(`button[onclick*="withdrawApplication(${applicationId}"]`);
            if (withdrawBtn) {
                // This is the current application, update the note
                let notesSection = modalContent.querySelector('.application-note-detail');
                if (!notesSection) {
                    // Find a good place to insert (after cover letter or before actions)
                    const actionsSection = modalContent.querySelector('.flex.gap-3.pt-4');
                    if (actionsSection && actionsSection.parentElement) {
                        notesSection = document.createElement('div');
                        notesSection.className = 'application-note-detail mb-6';
                        notesSection.innerHTML = `
                            <h5 class="text-base font-bold text-gray-900 mb-3">Your Note</h5>
                            <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 leading-relaxed">
                                ${noteText}
                            </div>
                        `;
                        actionsSection.parentElement.insertBefore(notesSection, actionsSection);
                    }
                } else {
                    // Update existing note
                    const noteDiv = notesSection.querySelector('.bg-gray-50');
                    if (noteDiv) {
                        noteDiv.textContent = noteText;
                    }
                }
            }
        }
    }
}

function updateNoteInCard(applicationId, noteText) {
    // Find the card for this application by looking for the button with the application ID
    const cards = document.querySelectorAll('.application-card');
    let targetCard = null;
    
    cards.forEach(card => {
        const viewBtn = card.querySelector(`button[onclick*="openApplicationModal(${applicationId})"]`);
        const deleteBtn = card.querySelector(`button[onclick*="confirmDelete(${applicationId})"]`);
        if (viewBtn || deleteBtn) {
            targetCard = card;
        }
    });
    
    if (targetCard) {
        updateNoteInCardElement(targetCard, noteText, applicationId);
    }
}

function updateNoteInCardElement(card, noteText, applicationId) {
    // Find or create the notes section
    let notesSection = card.querySelector(`.application-notes[data-application-id="${applicationId}"]`);
    
    if (!notesSection) {
        // Find the interview scheduled section or dates section to insert after
        let insertAfter = card.querySelector('.bg-purple-50');
        if (!insertAfter) {
            insertAfter = card.querySelector('.flex.items-center.gap-8');
        }
        
        if (insertAfter && insertAfter.parentElement) {
            notesSection = document.createElement('div');
            notesSection.className = 'flex items-start gap-2 text-sm text-gray-700 mb-3 application-notes';
            notesSection.setAttribute('data-application-id', applicationId);
            notesSection.innerHTML = `
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-gray-700">${noteText}</span>
            `;
            insertAfter.parentElement.insertBefore(notesSection, insertAfter.nextSibling);
        }
    } else {
        // Update existing notes
        const noteTextSpan = notesSection.querySelector('span');
        if (noteTextSpan) {
            noteTextSpan.textContent = noteText;
        }
    }
}

function confirmDelete(applicationId) {
    // Show confirmation toast
    if (typeof window.showWarningToast === 'function') {
        window.showWarningToast('Deleting application...', 2000);
    }
    
    // Small delay to show the toast, then proceed with deletion
    setTimeout(() => {
        // First fetch the application to check its status
        fetch(`/job-seeker/applications/${applicationId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.application) {
                withdrawApplication(applicationId, data.application.status);
            } else {
                if (typeof window.showErrorToast === 'function') {
                    window.showErrorToast('Failed to load application details');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof window.showErrorToast === 'function') {
                window.showErrorToast('An error occurred while checking application status.');
            }
        });
    }, 500);
}

function withdrawApplication(applicationId, currentStatus) {
    // Check if status has been changed by employer
    // Only allow withdrawal if status is 'pending' or 'reviewing'
    // If status is 'shortlisted', 'hired', or 'rejected', employer has already acted
    const withdrawableStatuses = ['pending', 'reviewing'];
    
    if (!withdrawableStatuses.includes(currentStatus)) {
        if (typeof window.showErrorToast === 'function') {
            window.showErrorToast('Cannot withdraw application. The employer has already reviewed your application.');
        } else {
            alert('Cannot withdraw application. The employer has already reviewed your application.');
        }
        return;
    }
    
    fetch(`/job-seeker/applications/${applicationId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            if (typeof window.showSuccessToast === 'function') {
                window.showSuccessToast('Application withdrawn successfully');
            }
            closeApplicationModal();
            // Remove the card in real time without page reload
            removeApplicationCard(applicationId);
        } else {
            if (typeof window.showErrorToast === 'function') {
                window.showErrorToast(data.error || 'Failed to withdraw application');
            } else {
                alert(data.error || 'Failed to withdraw application');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof window.showErrorToast === 'function') {
            window.showErrorToast('An error occurred while withdrawing the application.');
        } else {
            alert('An error occurred while withdrawing the application.');
        }
    });
}

function removeApplicationCard(applicationId) {
    // Find the card for this application
    const cards = document.querySelectorAll('.application-card');
    cards.forEach(card => {
        const viewBtn = card.querySelector(`button[onclick*="openApplicationModal(${applicationId})"]`);
        const deleteBtn = card.querySelector(`button[onclick*="confirmDelete(${applicationId})"]`);
        if (viewBtn || deleteBtn) {
            // Animate out
            card.style.transition = 'opacity 0.3s, transform 0.3s';
            card.style.opacity = '0';
            card.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                card.remove();
                // Update stats if needed
                updateStatsAfterDelete();
            }, 300);
        }
    });
}

function updateStatsAfterDelete() {
    // Recalculate and update the stats cards
    const cards = document.querySelectorAll('.application-card:not([style*="display: none"])');
    const stats = {
        applied: 0,
        reviewing: 0,
        interview: 0,
        offered: 0,
        rejected: 0
    };
    
    cards.forEach(card => {
        const filter = card.dataset.filter;
        if (filter && stats.hasOwnProperty(filter)) {
            stats[filter]++;
        }
    });
    
    // Update the stat numbers (if elements exist)
    const statElements = {
        applied: document.querySelector('.application-card')?.closest('.grid')?.querySelector('.text-3xl')?.parentElement,
        // We can add more specific selectors if needed
    };
}
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.job-seeker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\lysp\Downloads\Job Ad\resources\views/job-seeker/applications.blade.php ENDPATH**/ ?>