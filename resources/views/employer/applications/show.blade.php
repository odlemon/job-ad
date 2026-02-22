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
                    <a href="{{ route('employer.applications.index') }}" wire:navigate class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Applications
                    </a>
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $application->first_name }} {{ $application->last_name }}</h1>
                            <p class="text-gray-600">{{ $application->jobAdvertisement->title }}</p>
                        </div>
                        @php
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
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$statusInfo['color']] }}">
                            {{ $statusInfo['label'] }}
                        </span>
                    </div>
                </div>

                <!-- Applicant Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Applicant Information</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Full Name</p>
                            <p class="text-sm font-medium text-gray-900">{{ $application->first_name }} {{ $application->last_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Email</p>
                            <p class="text-sm font-medium text-gray-900">{{ $application->email }}</p>
                        </div>
                        @if($application->phone)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Phone</p>
                            <p class="text-sm font-medium text-gray-900">{{ $application->phone }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Applied Date</p>
                            <p class="text-sm font-medium text-gray-900">{{ $application->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Cover Letter -->
                @if($application->cover_letter)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Cover Letter</h2>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $application->cover_letter }}</p>
                </div>
                @endif

                <!-- Resume -->
                @if($application->resume_path)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Resume</h2>
                    @php
                        $resumeUrl = null;
                        if (str_starts_with($application->resume_path, 'http://') || str_starts_with($application->resume_path, 'https://')) {
                            $resumeUrl = $application->resume_path;
                        } else {
                            $resumeUrl = env('MEDIA_BASE_URL', 'http://31.220.82.129/uploads') . '/' . $application->resume_path;
                        }
                    @endphp
                    <a href="{{ $resumeUrl }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Resume
                    </a>
                </div>
                @endif

                <!-- Job Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Job Details</h2>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $application->jobAdvertisement->title }}</h3>
                        <p class="text-gray-600">{{ $application->jobAdvertisement->company->name ?? 'Unknown Company' }}</p>
                        <a href="{{ route('employer.jobs.show', $application->jobAdvertisement->id) }}" wire:navigate class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                            View Job Posting →
                        </a>
                    </div>
                </div>

                <!-- Status Update -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Update Status</h2>
                    <form id="statusForm" onsubmit="updateStatus(event)" class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select id="status" name="status" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="applied" {{ $application->status === 'applied' ? 'selected' : '' }}>Applied</option>
                                <option value="in_review" {{ $application->status === 'in_review' ? 'selected' : '' }}>In Review</option>
                                <option value="interview" {{ $application->status === 'interview' ? 'selected' : '' }}>Interview</option>
                                <option value="offered" {{ $application->status === 'offered' ? 'selected' : '' }}>Offered</option>
                                <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div>
                            <label for="employer_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea id="employer_notes" name="employer_notes" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Add notes about this applicant...">{{ old('employer_notes', $application->employer_notes) }}</textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
async function updateStatus(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const data = {
        status: formData.get('status'),
        employer_notes: formData.get('employer_notes'),
    };
    
    try {
        const response = await fetch(`/employer/applications/{{ $application->id }}/status`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });
        
        if (response.ok) {
            window.location.reload();
        } else {
            alert('Failed to update status');
        }
    } catch (error) {
        console.error('Error updating status:', error);
        alert('An error occurred while updating the status');
    }
}
</script>
@endsection
