@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-pink-50 via-white to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">
                    Create Account
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Join JobHub and start your journey
                </p>
            </div>

            <!-- Form -->
            <form class="space-y-5" id="registerForm">
                @csrf
                
                <!-- User Type Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        I am a <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex cursor-pointer rounded-xl border-2 border-gray-200 p-4 focus:outline-none hover:border-blue-500 transition user-type-option" data-type="job_seeker">
                            <input type="radio" name="user_type" value="job_seeker" class="sr-only" required>
                            <div class="flex flex-1 flex-col">
                                <div class="flex items-center">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="ml-2 font-semibold text-gray-900">Job Seeker</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Looking for jobs</p>
                            </div>
                            <svg class="h-5 w-5 text-blue-600 absolute top-4 right-4 hidden check-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </label>
                        <label class="relative flex cursor-pointer rounded-xl border-2 border-gray-200 p-4 focus:outline-none hover:border-blue-500 transition user-type-option" data-type="employer">
                            <input type="radio" name="user_type" value="employer" class="sr-only" required>
                            <div class="flex flex-1 flex-col">
                                <div class="flex items-center">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="ml-2 font-semibold text-gray-900">Employer</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Hiring talent</p>
                            </div>
                            <svg class="h-5 w-5 text-blue-600 absolute top-4 right-4 hidden check-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </label>
                    </div>
                </div>

                <!-- Job Seeker Fields -->
                <div id="jobSeekerFields" class="hidden space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input id="first_name" name="first_name" type="text" 
                                class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="John">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input id="last_name" name="last_name" type="text" 
                                class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Doe">
                        </div>
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">
                            Location
                        </label>
                        <input id="location" name="location" type="text" 
                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Victoria, Mahe">
                    </div>
                    <div>
                        <label for="bio" class="block text-sm font-semibold text-gray-700 mb-2">
                            Bio
                        </label>
                        <textarea id="bio" name="bio" rows="3"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                            placeholder="Tell us about yourself..."></textarea>
                    </div>
                </div>

                <!-- Employer Fields -->
                <div id="employerFields" class="hidden space-y-5">
                    <div>
                        <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input id="company_name" name="company_name" type="text" 
                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Your Company Ltd">
                    </div>
                    <div>
                        <label for="company_description" class="block text-sm font-semibold text-gray-700 mb-2">
                            Company Description
                        </label>
                        <textarea id="company_description" name="company_description" rows="3"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                            placeholder="Tell us about your company..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="industry" class="block text-sm font-semibold text-gray-700 mb-2">
                                Industry
                            </label>
                            <input id="industry" name="industry" type="text" 
                                class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Technology">
                        </div>
                        <div>
                            <label for="company_size" class="block text-sm font-semibold text-gray-700 mb-2">
                                Company Size
                            </label>
                            <select id="company_size" name="company_size" 
                                class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                <option value="">Select size</option>
                                <option value="1-10">1-10 employees</option>
                                <option value="11-50">11-50 employees</option>
                                <option value="51-200">51-200 employees</option>
                                <option value="201-500">201-500 employees</option>
                                <option value="500+">500+ employees</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="website" class="block text-sm font-semibold text-gray-700 mb-2">
                            Website
                        </label>
                        <input id="website" name="website" type="url" 
                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="https://yourcompany.com">
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                            Address
                        </label>
                        <input id="address" name="address" type="text" 
                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Company address">
                    </div>
                </div>

                <!-- Common Fields -->
                <div class="border-t border-gray-200 pt-5 space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="you@example.com">
                        </div>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                            Phone <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <input id="phone" name="phone" type="tel" 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="+248 1234567">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="new-password" required 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Minimum 8 characters">
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Re-enter password">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" id="registerBtn"
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-gradient-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                        <span id="registerBtnText">Create account</span>
                        <div id="registerBtnSpinner" class="hidden spinner-sm ml-2"></div>
                    </button>
                </div>
                
                <!-- Error Message -->
                <div id="error-message" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm"></div>
            </form>

            <!-- Sign In Link -->
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" wire:navigate class="font-semibold text-blue-600 hover:text-blue-500 transition">
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Handle user type selection
    document.querySelectorAll('input[name="user_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const userType = this.value;
            const jobSeekerFields = document.getElementById('jobSeekerFields');
            const employerFields = document.getElementById('employerFields');
            const allOptions = document.querySelectorAll('.user-type-option');
            
            // Update visual selection
            allOptions.forEach(option => {
                const checkIcon = option.querySelector('.check-icon');
                if (option.dataset.type === userType) {
                    option.classList.add('border-blue-500', 'bg-blue-50');
                    checkIcon.classList.remove('hidden');
                } else {
                    option.classList.remove('border-blue-500', 'bg-blue-50');
                    checkIcon.classList.add('hidden');
                }
            });
            
            // Show/hide fields
            if (userType === 'job_seeker') {
                jobSeekerFields.classList.remove('hidden');
                employerFields.classList.add('hidden');
                // Make job seeker fields required
                document.getElementById('first_name').required = true;
                document.getElementById('last_name').required = true;
                document.getElementById('company_name').required = false;
            } else {
                jobSeekerFields.classList.add('hidden');
                employerFields.classList.remove('hidden');
                // Make employer fields required
                document.getElementById('first_name').required = false;
                document.getElementById('last_name').required = false;
                document.getElementById('company_name').required = true;
            }
        });
    });

    document.getElementById('registerForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const errorDiv = document.getElementById('error-message');
        const registerBtn = document.getElementById('registerBtn');
        const registerBtnText = document.getElementById('registerBtnText');
        const registerBtnSpinner = document.getElementById('registerBtnSpinner');
        
        // Validate user type selected
        const userType = formData.get('user_type');
        if (!userType) {
            errorDiv.textContent = 'Please select whether you are a Job Seeker or Employer';
            errorDiv.classList.remove('hidden');
            return;
        }
        
        // Validate passwords match
        if (formData.get('password') !== formData.get('password_confirmation')) {
            errorDiv.textContent = 'Passwords do not match';
            errorDiv.classList.remove('hidden');
            return;
        }
        
        // Show loading state
        errorDiv.classList.add('hidden');
        registerBtn.disabled = true;
        registerBtnText.textContent = 'Creating account...';
        registerBtnSpinner.classList.remove('hidden');
        
        try {
            // Build request data based on user type
            const requestData = {
                user_type: userType,
                email: formData.get('email'),
                phone: formData.get('phone'),
                password: formData.get('password'),
                password_confirmation: formData.get('password_confirmation'),
            };
            
            if (userType === 'job_seeker') {
                requestData.first_name = formData.get('first_name');
                requestData.last_name = formData.get('last_name');
                requestData.location = formData.get('location');
                requestData.bio = formData.get('bio');
            } else {
                requestData.company_name = formData.get('company_name');
                requestData.company_description = formData.get('company_description');
                requestData.industry = formData.get('industry');
                requestData.company_size = formData.get('company_size');
                requestData.website = formData.get('website');
                requestData.address = formData.get('address');
            }
            
            const response = await fetch('/api/auth/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify(requestData)
            });
            
            const data = await response.json();
            
            if (response.ok) {
                registerBtnText.textContent = 'Success! Redirecting...';
                // Determine redirect based on user type
                const userType = data.user?.user_type || data.user_type;
                const redirect = userType === 'employer' ? '/employer/dashboard' : '/dashboard';
                setTimeout(() => {
                    window.location.href = redirect;
                }, 500);
            } else {
                registerBtn.disabled = false;
                registerBtnText.textContent = 'Create account';
                registerBtnSpinner.classList.add('hidden');
                const errorMsg = data.errors ? Object.values(data.errors).flat().join(', ') : data.message || 'Registration failed';
                errorDiv.textContent = errorMsg;
                errorDiv.classList.remove('hidden');
            }
        } catch (error) {
            registerBtn.disabled = false;
            registerBtnText.textContent = 'Create account';
            registerBtnSpinner.classList.add('hidden');
            errorDiv.textContent = 'An error occurred. Please try again.';
            errorDiv.classList.remove('hidden');
        }
    });
</script>
@endpush
@endsection
