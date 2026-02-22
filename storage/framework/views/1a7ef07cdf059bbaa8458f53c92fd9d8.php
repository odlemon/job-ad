

<?php $__env->startSection('content'); ?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-pink-50 via-white to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 space-y-4">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900">
                    Create Account
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Join JobHub and start your journey
                </p>
            </div>

            <!-- Form -->
            <form class="space-y-3" id="registerForm">
                <?php echo csrf_field(); ?>
                
                <!-- User Type Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        I am a <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="relative flex cursor-pointer rounded-lg border-2 border-blue-500 bg-blue-50 p-3 focus:outline-none hover:border-blue-500 transition user-type-option" data-type="job_seeker">
                            <input type="radio" name="user_type" value="job_seeker" class="sr-only" required checked>
                            <div class="flex flex-1 flex-col">
                                <div class="flex items-center">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="ml-2 font-semibold text-gray-900">Job Seeker</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Looking for jobs</p>
                            </div>
                            <svg class="h-5 w-5 text-blue-600 absolute top-4 right-4 check-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </label>
                        <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 p-3 focus:outline-none hover:border-blue-500 transition user-type-option" data-type="employer">
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
                <div id="jobSeekerFields" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-1">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input id="first_name" name="first_name" type="text" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="John">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-1">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input id="last_name" name="last_name" type="text" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Doe">
                        </div>
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-semibold text-gray-700 mb-1">
                            Location
                        </label>
                        <input id="location" name="location" type="text" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Victoria, Mahe">
                    </div>
                    <div>
                        <label for="bio" class="block text-sm font-semibold text-gray-700 mb-1">
                            Bio
                        </label>
                        <textarea id="bio" name="bio" rows="2"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                            placeholder="Tell us about yourself..."></textarea>
                    </div>
                </div>

                <!-- Employer Fields -->
                <div id="employerFields" class="hidden space-y-3">
                    <div>
                        <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-1">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input id="company_name" name="company_name" type="text" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Your Company Ltd">
                    </div>
                    <div>
                        <label for="company_description" class="block text-sm font-semibold text-gray-700 mb-1">
                            Company Description
                        </label>
                        <textarea id="company_description" name="company_description" rows="2"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                            placeholder="Tell us about your company..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="industry" class="block text-sm font-semibold text-gray-700 mb-1">
                                Industry
                            </label>
                            <input id="industry" name="industry" type="text" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Technology">
                        </div>
                        <div>
                            <label for="company_size" class="block text-sm font-semibold text-gray-700 mb-1">
                                Company Size
                            </label>
                            <select id="company_size" name="company_size" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                <option value="">Select size</option>
                                <option value="1-10">1-10 employees</option>
                                <option value="11-50">11-50 employees</option>
                                <option value="51-200">51-200 employees</option>
                                <option value="201-500">201-500 employees</option>
                                <option value="500+">500+ employees</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="website" class="block text-sm font-semibold text-gray-700 mb-1">
                                Website
                            </label>
                            <input id="website" name="website" type="url" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="https://yourcompany.com">
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-1">
                                Address
                            </label>
                            <input id="address" name="address" type="text" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Company address">
                        </div>
                    </div>
                </div>

                <!-- Common Fields -->
                <div class="border-t border-gray-200 pt-3 space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="email" required 
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    placeholder="you@example.com">
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1">
                                Phone <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <input id="phone" name="phone" type="tel" 
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    placeholder="+248 1234567">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input id="password" name="password" type="password" autocomplete="new-password" required 
                                    class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    placeholder="Min 8 chars">
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg id="passwordEyeIcon" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                                    class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                    placeholder="Re-enter password">
                                <button type="button" id="togglePasswordConfirmation" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg id="passwordConfirmationEyeIcon" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" id="registerBtn"
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform hover:scale-[1.02] active:scale-[0.98]">
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
                    <a href="<?php echo e(route('login')); ?>" wire:navigate class="font-semibold text-blue-600 hover:text-blue-500 transition">
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // Password visibility toggle
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const passwordEyeIcon = document.getElementById('passwordEyeIcon');
        
        const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        const passwordConfirmationEyeIcon = document.getElementById('passwordConfirmationEyeIcon');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle eye icon
                if (type === 'text') {
                    passwordEyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
                } else {
                    passwordEyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                }
            });
        }
        
        if (togglePasswordConfirmation && passwordConfirmationInput) {
            togglePasswordConfirmation.addEventListener('click', function() {
                const type = passwordConfirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmationInput.setAttribute('type', type);
                
                // Toggle eye icon
                if (type === 'text') {
                    passwordConfirmationEyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
                } else {
                    passwordConfirmationEyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                }
            });
        }
    });

    // Function to handle user type selection
    function handleUserTypeChange(userType) {
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
            const firstName = document.getElementById('first_name');
            const lastName = document.getElementById('last_name');
            const companyName = document.getElementById('company_name');
            if (firstName) firstName.required = true;
            if (lastName) lastName.required = true;
            if (companyName) companyName.required = false;
        } else {
            jobSeekerFields.classList.add('hidden');
            employerFields.classList.remove('hidden');
            // Make employer fields required
            const firstName = document.getElementById('first_name');
            const lastName = document.getElementById('last_name');
            const companyName = document.getElementById('company_name');
            if (firstName) firstName.required = false;
            if (lastName) lastName.required = false;
            if (companyName) companyName.required = true;
        }
    }

    // Initialize job seeker as default on page load
    document.addEventListener('DOMContentLoaded', function() {
        const jobSeekerRadio = document.querySelector('input[name="user_type"][value="job_seeker"]');
        if (jobSeekerRadio && jobSeekerRadio.checked) {
            handleUserTypeChange('job_seeker');
        }
    });

    // Handle user type selection
    document.querySelectorAll('input[name="user_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            handleUserTypeChange(this.value);
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\lysp\Downloads\Job Ad\resources\views/auth/register.blade.php ENDPATH**/ ?>