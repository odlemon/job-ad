@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Apply for Job</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Fill out the form below to submit your application</p>
    </div>

    @if(isset($job))
    <div class="bg-gradient-to-r from-blue-50 to-pink-50 rounded-2xl shadow-sm dark:shadow-none p-6 mb-6 border border-gray-100 dark:border-gray-700">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $job->title }}</h2>
                <p class="text-gray-700 dark:text-gray-300 font-medium mb-1">{{ $job->company->name ?? 'Company' }}</p>
                @if($job->location)
                    <p class="text-gray-600 dark:text-gray-400 text-sm">📍 {{ $job->location }}</p>
                @endif
            </div>
            <a href="{{ route('jobs.show', $job->id) }}" wire:navigate class="text-blue-600 hover:text-blue-700 text-sm font-medium">View details →</a>
        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
        <form id="applicationForm" class="space-y-6">
            @if(empty($questions))
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <p class="text-yellow-800 text-sm">
                        <strong>Note:</strong> This job posting doesn't have any application questions. Your application will be submitted with your profile information.
                    </p>
                </div>
            @else
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Please answer the following questions:</h3>
                    <div class="space-y-6">
                        @foreach($questions as $index => $question)
                            <div>
                                <label for="question_{{ $index }}" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    {{ $question['question'] ?? $question }}
                                    @if(isset($question['required']) && $question['required'])
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                                @if(isset($question['type']) && $question['type'] === 'textarea')
                                    <textarea 
                                        id="question_{{ $index }}" 
                                        name="questions[{{ $index }}]" 
                                        rows="4"
                                        @if(isset($question['required']) && $question['required']) required @endif
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                                        placeholder="{{ $question['placeholder'] ?? 'Your answer...' }}"></textarea>
                                @else
                                    <input 
                                        type="{{ $question['type'] ?? 'text' }}" 
                                        id="question_{{ $index }}" 
                                        name="questions[{{ $index }}]" 
                                        @if(isset($question['required']) && $question['required']) required @endif
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                        placeholder="{{ $question['placeholder'] ?? 'Your answer...' }}">
                                @endif
                                @if(isset($question['hint']))
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $question['hint'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <div class="flex space-x-4 pt-4">
                <button type="submit" id="submitBtn"
                    class="flex-1 flex justify-center items-center bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-8 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-cyan-600 transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="submitBtnText">Submit Application</span>
                    <svg id="submitBtnSpinner" class="hidden animate-spin h-5 w-5 text-white ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
                <a href="{{ route('jobs.show', $job->id) }}" wire:navigate class="border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-8 py-3 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition inline-flex items-center">
                    Cancel
                </a>
            </div>
            
            <div id="message" class="hidden text-sm px-4 py-3 rounded-xl"></div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const API_BASE = '/api';
    const jobId = {{ $job->id ?? 'null' }};
    
    // Submit application
    document.getElementById('applicationForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const messageDiv = document.getElementById('message');
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        const submitBtnSpinner = document.getElementById('submitBtnSpinner');
        
        messageDiv.classList.add('hidden');
        submitBtn.disabled = true;
        submitBtnText.textContent = 'Submitting...';
        submitBtnSpinner.classList.remove('hidden');
        
        try {
            // Collect form data
            const formData = new FormData(this);
            const questions = {};
            
            // Collect all question answers
            formData.forEach((value, key) => {
                if (key.startsWith('questions[')) {
                    const questionIndex = key.match(/\[(\d+)\]/)[1];
                    questions[questionIndex] = value;
                }
            });
            
            // Prepare application data
            const applicationData = {
                job_advertisement_id: jobId,
                additional_info: {
                    questions: questions
                }
            };
            
            const response = await fetch(`${API_BASE}/job-seeker/applications`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(applicationData)
            });
            
            let data;
            try {
                const responseText = await response.text();
                console.log('Application submission response:', responseText);
                data = JSON.parse(responseText);
            } catch (parseError) {
                console.error('Failed to parse response:', parseError);
                throw new Error('Invalid response from server');
            }
            
            if (response.ok) {
                submitBtnText.textContent = 'Success! Redirecting...';
                messageDiv.textContent = data.message || 'Application submitted successfully!';
                messageDiv.className = 'bg-green-50 border border-green-200 text-green-700';
                messageDiv.classList.remove('hidden');
                
                // Show success toast if available
                if (typeof window.showSuccessToast === 'function') {
                    window.showSuccessToast(data.message || 'Application submitted successfully!');
                }
                
                setTimeout(() => {
                    if (typeof navigateTo === 'function') {
                        navigateTo('/job-seeker/applications');
                    } else if (typeof window.navigateTo === 'function') {
                        window.navigateTo('/job-seeker/applications');
                    } else {
                        window.location.href = '/job-seeker/applications';
                    }
                }, 1500);
            } else {
                submitBtn.disabled = false;
                submitBtnText.textContent = 'Submit Application';
                submitBtnSpinner.classList.add('hidden');
                
                let errorMessage = data.message || data.error || 'Failed to submit application';
                if (data.errors) {
                    const errorList = Object.values(data.errors).flat().join(', ');
                    errorMessage = errorList || errorMessage;
                }
                
                console.error('Application submission error:', data);
                
                messageDiv.textContent = errorMessage;
                messageDiv.className = 'bg-red-50 border border-red-200 text-red-700';
                messageDiv.classList.remove('hidden');
                
                // Show error toast if available
                if (typeof window.showErrorToast === 'function') {
                    window.showErrorToast(errorMessage);
                }
            }
        } catch (error) {
            console.error('Error submitting application:', error);
            submitBtn.disabled = false;
            submitBtnText.textContent = 'Submit Application';
            submitBtnSpinner.classList.add('hidden');
            
            const errorMessage = error.message || 'An error occurred. Please try again.';
            messageDiv.textContent = errorMessage;
            messageDiv.className = 'bg-red-50 border border-red-200 text-red-700';
            messageDiv.classList.remove('hidden');
            
            // Show error toast if available
            if (typeof window.showErrorToast === 'function') {
                window.showErrorToast(errorMessage);
            }
        }
    });
</script>
@endpush
@endsection
