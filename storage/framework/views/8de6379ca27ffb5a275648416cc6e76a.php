

<?php $__env->startSection('content'); ?>
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col">
        <?php echo $__env->make('partials.job-seeker-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        
        <!-- Main Content -->
        <main class="flex-1 p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Back Button -->
            <a href="/job-seeker/applications" wire:navigate class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Applications
            </a>

            <?php
                $job = $application->jobAdvertisement;
                $company = $job->company ?? null;
                $statusConfig = [
                    'applied' => ['label' => 'Applied', 'color' => 'blue'],
                    'in_review' => ['label' => 'In Review', 'color' => 'yellow'],
                    'interview' => ['label' => 'Interview', 'color' => 'purple'],
                    'offered' => ['label' => 'Offered', 'color' => 'green'],
                    'rejected' => ['label' => 'Rejected', 'color' => 'red'],
                ];
                $statusInfo = $statusConfig[$application->status] ?? $statusConfig['applied'];
                $statusColors = [
                    'blue' => 'bg-blue-100 text-blue-800',
                    'yellow' => 'bg-yellow-100 text-yellow-800',
                    'purple' => 'bg-purple-100 text-purple-800',
                    'green' => 'bg-green-100 text-green-800',
                    'red' => 'bg-red-100 text-red-800',
                ];
            ?>

            <!-- Application Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-start space-x-4">
                        <!-- Company Logo -->
                        <div class="w-20 h-20 rounded-lg bg-gray-200 flex items-center justify-center flex-shrink-0 overflow-hidden">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company && $company->logo): ?>
                                <img src="<?php echo e(asset('storage/' . $company->logo)); ?>" alt="<?php echo e($company->name); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-1"><?php echo e($job->title); ?></h1>
                            <p class="text-lg text-gray-600 mb-2"><?php echo e($company->name ?? 'Unknown Company'); ?></p>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-medium <?php echo e($statusColors[$statusInfo['color']]); ?>">
                                <?php echo e($statusInfo['label']); ?>

                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Applied Date</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($application->created_at->format('M d, Y')); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Last Updated</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($application->updated_at->diffForHumans()); ?></p>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->reviewed_at): ?>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Reviewed At</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($application->reviewed_at->format('M d, Y')); ?></p>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- Status-specific Information -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->status === 'interview' && isset($application->additional_info['interview_date'])): ?>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-purple-900 mb-3">Interview Details</h2>
                <div class="space-y-2">
                    <p class="text-sm text-purple-800">
                        <span class="font-medium">Date & Time:</span> 
                        <?php echo e(\Carbon\Carbon::parse($application->additional_info['interview_date'])->format('M d, Y')); ?> at <?php echo e(\Carbon\Carbon::parse($application->additional_info['interview_date'])->format('g:i A')); ?>

                    </p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($application->additional_info['interview_location'])): ?>
                    <p class="text-sm text-purple-800">
                        <span class="font-medium">Location:</span> <?php echo e($application->additional_info['interview_location']); ?>

                    </p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($application->additional_info['interview_notes'])): ?>
                    <p class="text-sm text-purple-800 mt-3">
                        <span class="font-medium">Notes:</span> <?php echo e($application->additional_info['interview_notes']); ?>

                    </p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php elseif($application->status === 'offered' && isset($application->additional_info['salary'])): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-green-900 mb-3">Offer Details</h2>
                <div class="space-y-2">
                    <p class="text-sm text-green-800">
                        <span class="font-medium">Salary Offered:</span> <?php echo e($application->additional_info['salary']); ?>

                    </p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($application->additional_info['start_date'])): ?>
                    <p class="text-sm text-green-800">
                        <span class="font-medium">Start Date:</span> <?php echo e(\Carbon\Carbon::parse($application->additional_info['start_date'])->format('M d, Y')); ?>

                    </p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($application->additional_info['offer_notes'])): ?>
                    <p class="text-sm text-green-800 mt-3">
                        <span class="font-medium">Notes:</span> <?php echo e($application->additional_info['offer_notes']); ?>

                    </p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Application Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Application Information</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Full Name</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($application->first_name); ?> <?php echo e($application->last_name); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Email</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($application->email); ?></p>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->phone): ?>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Phone</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($application->phone); ?></p>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->cover_letter): ?>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Cover Letter</p>
                        <p class="text-sm text-gray-900 whitespace-pre-wrap"><?php echo e($application->cover_letter); ?></p>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->resume_path): ?>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Resume</p>
                        <a href="<?php echo e(asset('storage/' . $application->resume_path)); ?>" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            View Resume
                        </a>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- Job Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Job Details</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Job Title</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($job->title); ?></p>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job->description): ?>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Description</p>
                        <p class="text-sm text-gray-900 whitespace-pre-wrap"><?php echo e($job->description); ?></p>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job->location): ?>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Location</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($job->location); ?></p>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job->employment_type): ?>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Employment Type</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e(ucfirst(str_replace('_', ' ', $job->employment_type))); ?></p>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job->salary_min || $job->salary_max): ?>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Salary</p>
                        <p class="text-sm font-medium text-gray-900">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($job->salary_min && $job->salary_max): ?>
                                <?php echo e($job->currency ?? 'SCR'); ?> <?php echo e(number_format($job->salary_min)); ?> - <?php echo e(number_format($job->salary_max)); ?>

                            <?php elseif($job->salary_min): ?>
                                <?php echo e($job->currency ?? 'SCR'); ?> <?php echo e(number_format($job->salary_min)); ?>+
                            <?php elseif($job->salary_max): ?>
                                Up to <?php echo e($job->currency ?? 'SCR'); ?> <?php echo e(number_format($job->salary_max)); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="pt-4">
                        <a href="<?php echo e(route('jobs.show', $job->id)); ?>" wire:navigate class="inline-flex items-center text-blue-600 hover:text-blue-800">
                            View Job Posting
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($application->notes): ?>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Your Notes</h2>
                <p class="text-sm text-gray-900 whitespace-pre-wrap"><?php echo e($application->notes); ?></p>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Actions -->
            <div class="flex items-center justify-between bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <button onclick="deleteApplication(<?php echo e($application->id); ?>)" class="px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition font-medium">
                    Delete Application
                </button>
                <div class="flex items-center space-x-3">
                    <button onclick="openNoteModal()" class="px-4 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition font-medium">
                        <?php echo e($application->notes ? 'Edit Note' : 'Add Note'); ?>

                    </button>
                    <a href="<?php echo e(route('jobs.show', $job->id)); ?>" wire:navigate class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        View Job Posting
                    </a>
                </div>
            </div>
        </div>
    </main>
    </div>

<!-- Note Modal -->
<div id="noteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4"><?php echo e($application->notes ? 'Edit Note' : 'Add Note'); ?></h3>
            <textarea id="noteText" rows="6" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Add a note about this application..."><?php echo e($application->notes); ?></textarea>
            <div class="flex justify-end space-x-3 mt-4">
                <button onclick="closeNoteModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </button>
                <button onclick="saveNote(<?php echo e($application->id); ?>)" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Save Note
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openNoteModal() {
    document.getElementById('noteModal').classList.remove('hidden');
}

function closeNoteModal() {
    document.getElementById('noteModal').classList.add('hidden');
}

async function saveNote(applicationId) {
    const note = document.getElementById('noteText').value;
    
    try {
        const response = await fetch(`/job-seeker/applications/${applicationId}/notes`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ notes: note }),
        });
        
        if (response.ok) {
            window.location.reload();
        } else {
            alert('Failed to save note');
        }
    } catch (error) {
        console.error('Error saving note:', error);
        alert('An error occurred while saving the note');
    }
}

async function deleteApplication(applicationId) {
    if (!confirm('Are you sure you want to delete this application?')) {
        return;
    }
    
    try {
        const response = await fetch(`/job-seeker/applications/${applicationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        });
        
        if (response.ok) {
            window.location.href = '/job-seeker/applications';
        } else {
            alert('Failed to delete application');
        }
    } catch (error) {
        console.error('Error deleting application:', error);
        alert('An error occurred while deleting the application');
    }
}
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.job-seeker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\lysp\Downloads\Job Ad\resources\views/job-seeker/application-detail.blade.php ENDPATH**/ ?>