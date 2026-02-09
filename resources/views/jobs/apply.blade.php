@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Apply for Job</h1>
        <p class="text-gray-600 mt-2">Fill out the form below to submit your application</p>
    </div>

    <div id="job-info" class="bg-gradient-to-r from-blue-50 to-pink-50 rounded-2xl shadow-sm p-6 mb-6 border border-gray-100">
        <div class="text-center py-8">
            <div class="spinner mx-auto mb-4"></div>
            <p class="text-gray-500">Loading job information...</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-8">
        <form id="applicationForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="first_name" name="first_name" required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="John">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="last_name" name="last_name" required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="Doe">
                </div>
            </div>
            
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" required
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="you@example.com">
            </div>
            
            <div>
                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                    Phone
                </label>
                <input type="tel" id="phone" name="phone"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="+248 1234567">
            </div>
            
            <div>
                <label for="cover_letter" class="block text-sm font-semibold text-gray-700 mb-2">
                    Cover Letter
                </label>
                <textarea id="cover_letter" name="cover_letter" rows="6"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                    placeholder="Tell us why you're a great fit for this position..."></textarea>
                <p class="mt-1 text-xs text-gray-500">Optional but recommended</p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Resume</label>
                <p class="text-sm text-gray-600 mb-3" id="cv-status">No CV uploaded to profile</p>
                <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" 
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            
            <div class="flex space-x-4 pt-4">
                <button type="submit" id="submitBtn"
                    class="flex-1 flex justify-center items-center bg-gradient-to-r from-pink-500 to-pink-600 text-white px-8 py-3 rounded-xl font-semibold hover:from-pink-600 hover:to-pink-700 transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg">
                    <span id="submitBtnText">Submit Application</span>
                    <div id="submitBtnSpinner" class="hidden spinner-sm ml-2"></div>
                </button>
                <a href="/jobs/{{ $id }}" wire:navigate class="border-2 border-gray-300 text-gray-700 px-8 py-3 rounded-xl font-semibold hover:bg-gray-50 transition inline-flex items-center">
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
    const jobId = {{ $id }};
    
    // Load job info and profile data
    async function loadData() {
        try {
            // Load job
            const jobResponse = await fetch(`${API_BASE}/jobs/${jobId}`);
            if (jobResponse.ok) {
                const jobData = await jobResponse.json();
                if (jobData.data) {
                    const job = jobData.data;
                    document.getElementById('job-info').innerHTML = `
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-2">${job.title}</h2>
                                <p class="text-gray-700 font-medium mb-1">${job.company?.name || 'Company'}</p>
                                ${job.location ? `<p class="text-gray-600 text-sm">📍 ${job.location}</p>` : ''}
                            </div>
                            <a href="/jobs/${job.id}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View details →</a>
                        </div>
                    `;
                }
            }
            
            // Load profile to pre-fill form
            const profileResponse = await fetch(`${API_BASE}/job-seeker/profile`);
            if (profileResponse.ok) {
                const profileData = await profileResponse.json();
                if (profileData.job_seeker) {
                    const profile = profileData.job_seeker;
                    document.getElementById('first_name').value = profile.first_name || '';
                    document.getElementById('last_name').value = profile.last_name || '';
                    document.getElementById('email').value = profile.user?.email || '';
                    document.getElementById('phone').value = profile.user?.phone || '';
                    
                    if (profile.cv_file_path) {
                        document.getElementById('cv-status').textContent = 'CV available in profile';
                        document.getElementById('cv-status').classList.add('text-green-600');
                    }
                }
            }
        } catch (error) {
            console.error('Error loading data:', error);
        }
    }
    
    // Submit application
    document.getElementById('applicationForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const messageDiv = document.getElementById('message');
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        const submitBtnSpinner = document.getElementById('submitBtnSpinner');
        
        messageDiv.classList.add('hidden');
        submitBtn.disabled = true;
        submitBtnText.textContent = 'Submitting...';
        submitBtnSpinner.classList.remove('hidden');
        
        try {
            const applicationData = {
                job_advertisement_id: jobId,
                first_name: formData.get('first_name'),
                last_name: formData.get('last_name'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                cover_letter: formData.get('cover_letter')
            };
            
            const response = await fetch(`${API_BASE}/job-seeker/applications`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(applicationData)
            });
            
            const data = await response.json();
            
            if (response.ok) {
                submitBtnText.textContent = 'Success! Redirecting...';
                messageDiv.textContent = 'Application submitted successfully!';
                messageDiv.className = 'bg-green-50 border border-green-200 text-green-700';
                messageDiv.classList.remove('hidden');
                setTimeout(() => {
                    navigateTo('/job-seeker/applications');
                }, 1500);
            } else {
                submitBtn.disabled = false;
                submitBtnText.textContent = 'Submit Application';
                submitBtnSpinner.classList.add('hidden');
                messageDiv.textContent = data.message || 'Failed to submit application';
                messageDiv.className = 'bg-red-50 border border-red-200 text-red-700';
                messageDiv.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error submitting application:', error);
            submitBtn.disabled = false;
            submitBtnText.textContent = 'Submit Application';
            submitBtnSpinner.classList.add('hidden');
            messageDiv.textContent = 'An error occurred. Please try again.';
            messageDiv.className = 'bg-red-50 border border-red-200 text-red-700';
            messageDiv.classList.remove('hidden');
        }
    });
    
    document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
@endsection
