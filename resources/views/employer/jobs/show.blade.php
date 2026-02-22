@extends('layouts.employer')

@section('content')
<div class="min-h-screen bg-gray-50">
    @include('partials.employer-navbar')

    <div class="flex">
        @include('partials.employer-sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-8 ml-64">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <a href="{{ route('employer.jobs.index') }}" wire:navigate class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Job Listings
                    </a>
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $job->title }}</h1>
                            <p class="text-gray-600">{{ $job->company->name ?? 'Unknown Company' }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ 
                            $job->status === 'published' ? 'bg-green-100 text-green-800' :
                            ($job->status === 'draft' ? 'bg-yellow-100 text-yellow-800' :
                            ($job->status === 'closed' ? 'bg-gray-100 text-gray-800' :
                            'bg-red-100 text-red-800'))
                        }}">
                            {{ ucfirst($job->status) }}
                        </span>
                    </div>
                </div>

                <!-- Job Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Job Details</h2>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Location</p>
                            <p class="text-sm font-medium text-gray-900">{{ $job->location ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Employment Type</p>
                            <p class="text-sm font-medium text-gray-900">{{ $job->employment_type ? ucfirst(str_replace('_', ' ', $job->employment_type)) : 'Not specified' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Experience Level</p>
                            <p class="text-sm font-medium text-gray-900">{{ $job->experience_level ? ucfirst($job->experience_level) : 'Not specified' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Remote Work</p>
                            <p class="text-sm font-medium text-gray-900">{{ $job->is_remote ? 'Yes' : 'No' }}</p>
                        </div>
                        @if($job->salary_min || $job->salary_max)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Salary</p>
                            <p class="text-sm font-medium text-gray-900">
                                @if($job->salary_min && $job->salary_max)
                                    {{ $job->currency ?? 'SCR' }} {{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }}
                                @elseif($job->salary_min)
                                    {{ $job->currency ?? 'SCR' }} {{ number_format($job->salary_min) }}+
                                @elseif($job->salary_max)
                                    Up to {{ $job->currency ?? 'SCR' }} {{ number_format($job->salary_max) }}
                                @endif
                            </p>
                        </div>
                        @endif
                        @if($job->application_deadline)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Application Deadline</p>
                            <p class="text-sm font-medium text-gray-900">{{ $job->application_deadline->format('M d, Y') }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $job->description }}</p>
                    </div>

                    @if($job->requirements)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Requirements</h3>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $job->requirements }}</p>
                    </div>
                    @endif

                    @if($job->benefits)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Benefits</h3>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $job->benefits }}</p>
                    </div>
                    @endif
                </div>

                <!-- Statistics -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="text-3xl font-bold text-gray-900 mb-1">{{ $job->views_count ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Total Views</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="text-3xl font-bold text-gray-900 mb-1">{{ $job->applications_count ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Applications</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="text-sm font-medium text-gray-900 mb-1">Posted</div>
                        <div class="text-sm text-gray-500">{{ $job->created_at->format('M d, Y') }}</div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <a href="{{ route('employer.applications.index', ['job_id' => $job->id]) }}" wire:navigate class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        View Applications ({{ $job->applications_count ?? 0 }})
                    </a>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('employer.jobs.edit', $job->id) }}" wire:navigate class="px-4 py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition font-medium">
                            Edit Job
                        </a>
                        <button onclick="deleteJob({{ $job->id }})" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
async function deleteJob(jobId) {
    if (!confirm('Are you sure you want to delete this job posting?')) {
        return;
    }
    
    try {
        const response = await fetch(`/employer/jobs/${jobId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        });
        
        if (response.ok) {
            window.location.href = '/employer/jobs';
        } else {
            alert('Failed to delete job posting');
        }
    } catch (error) {
        console.error('Error deleting job:', error);
        alert('An error occurred while deleting the job posting');
    }
}
</script>
@endsection
