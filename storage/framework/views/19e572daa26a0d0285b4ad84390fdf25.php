<!-- Auth Modal -->
<div id="authModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="relative w-full max-w-4xl mx-4 rounded-2xl shadow-2xl overflow-hidden" style="max-height: 90vh;">
        <div class="flex flex-col lg:flex-row h-full bg-white rounded-2xl overflow-hidden">
            <!-- Left Panel: Form -->
            <div class="flex-1 bg-white p-8 lg:p-10 overflow-y-auto">
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2" id="modalTitle">Welcome Back</h2>
                    <p class="text-base text-gray-600" id="modalSubtitle">Sign in to continue your journey</p>
                </div>

                <!-- Tabs: Login / Sign Up -->
                <div class="flex gap-2 mb-6">
                    <button id="loginTab" class="flex-1 px-4 py-2.5 rounded-lg text-base font-medium transition bg-blue-600 text-white">
                        Login
                    </button>
                    <button id="signUpTab" class="flex-1 px-4 py-2.5 rounded-lg text-base font-medium transition bg-gray-100 text-gray-700 hover:bg-gray-200">
                        Sign Up
                    </button>
                </div>

                <!-- User Type Selection (only shown in Sign Up) -->
                <div id="userTypeSelection" class="mb-6 hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">I am a:</label>
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" id="jobSeekerBtn" class="user-type-btn p-4 border-2 border-blue-500 bg-blue-50 rounded-lg transition hover:border-blue-600">
                            <div class="flex flex-col items-center">
                                <svg class="w-10 h-10 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="font-semibold text-gray-900 text-base">Job Seeker</span>
                            </div>
                        </button>
                        <button type="button" id="employerBtn" class="user-type-btn p-4 border-2 border-gray-200 bg-white rounded-lg transition hover:border-gray-300">
                            <div class="flex flex-col items-center">
                                <svg class="w-10 h-10 text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="font-semibold text-gray-900 text-base">Employer</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="space-y-5">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="user_type" id="loginUserType" value="job_seeker">
                    
                    <!-- User Type Selection for Login -->
                    <div id="loginUserTypeSelection" class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">I am a:</label>
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" id="loginJobSeekerBtn" class="login-user-type-btn p-4 border-2 border-blue-500 bg-blue-50 rounded-lg transition hover:border-blue-600">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="font-semibold text-gray-900 text-base">Job Seeker</span>
                                </div>
                            </button>
                            <button type="button" id="loginEmployerBtn" class="login-user-type-btn p-4 border-2 border-gray-200 bg-white rounded-lg transition hover:border-gray-300">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="font-semibold text-gray-900 text-base">Employer</span>
                                </div>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="loginEmail" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input id="loginEmail" name="email" type="email" autocomplete="email" required 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="you@example.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="loginPassword" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input id="loginPassword" name="password" type="password" autocomplete="current-password" required 
                                class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="••••••••">
                            <button type="button" id="toggleLoginPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg id="loginPasswordEyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="loginBtn"
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        <span id="loginBtnText">Login</span>
                        <div id="loginBtnSpinner" class="hidden ml-2">
                            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>

                    <!-- Error Message -->
                    <div id="loginErrorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm"></div>
                </form>

                <!-- Register Form (hidden by default) -->
                <form id="registerForm" class="space-y-5 hidden">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="user_type" id="registerUserType" value="job_seeker">
                    
                    <!-- Progress Bar -->
                    <div id="registrationProgress" class="mb-8 hidden">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">1</div>
                                <span class="ml-3 text-sm font-semibold text-gray-900">Account Info</span>
                            </div>
                            <div class="flex-1 h-0.5 bg-gray-300 mx-4"></div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-semibold">2</div>
                                <span class="ml-3 text-sm font-medium text-gray-500">Profile Details</span>
                            </div>
                            <div class="flex-1 h-0.5 bg-gray-300 mx-4"></div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-semibold">3</div>
                                <span class="ml-3 text-sm font-medium text-gray-500">Verification</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Registration Form Title (shown when form is visible) -->
                    <div id="registrationFormTitle" class="mb-8 hidden">
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">Create Your Account</h3>
                        <p class="text-base text-gray-600">Let's start with your basic information</p>
                    </div>
                    
                    <!-- Job Seeker Fields -->
                    <div id="jobSeekerFields" class="space-y-5 hidden">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">First Name <span class="text-red-500">*</span></label>
                                <input id="first_name" name="first_name" type="text" required
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="John">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                                <input id="last_name" name="last_name" type="text" required
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Doe">
                            </div>
                        </div>
                        <div>
                            <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                            <input id="location" name="location" type="text"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Victoria, Mahe">
                        </div>
                        <div>
                            <label for="bio" class="block text-sm font-semibold text-gray-700 mb-2">Bio</label>
                            <textarea id="bio" name="bio" rows="2"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                placeholder="Tell us about yourself..."></textarea>
                        </div>
                    </div>

                    <!-- Employer Fields -->
                    <div id="employerFields" class="space-y-5 hidden">
                        <div>
                            <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-2">Company Name <span class="text-red-500">*</span></label>
                            <input id="company_name" name="company_name" type="text"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Your Company Ltd">
                        </div>
                        <div>
                            <label for="company_description" class="block text-sm font-semibold text-gray-700 mb-2">Company Description</label>
                            <textarea id="company_description" name="company_description" rows="2"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                placeholder="Tell us about your company..."></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="industry" class="block text-sm font-semibold text-gray-700 mb-2">Industry</label>
                                <input id="industry" name="industry" type="text"
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Technology">
                            </div>
                            <div>
                                <label for="company_size" class="block text-sm font-semibold text-gray-700 mb-2">Company Size</label>
                                <select id="company_size" name="company_size"
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select size</option>
                                    <option value="1-10">1-10 employees</option>
                                    <option value="11-50">11-50 employees</option>
                                    <option value="51-200">51-200 employees</option>
                                    <option value="201-500">201-500 employees</option>
                                    <option value="500+">500+ employees</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="website" class="block text-sm font-semibold text-gray-700 mb-2">Website</label>
                                <input id="website" name="website" type="url"
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="https://yourcompany.com">
                            </div>
                            <div>
                                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                                <input id="address" name="address" type="text"
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Company address">
                            </div>
                        </div>
                    </div>

                    <!-- Account Info Fields (shown after clicking Continue to Registration) -->
                    <div id="accountInfoFields" class="space-y-5 hidden">
                        <div>
                            <label for="registerFirstName" class="block text-sm font-semibold text-gray-700 mb-2">First Name <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <input id="registerFirstName" name="first_name" type="text" required value="John"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label for="registerSurname" class="block text-sm font-semibold text-gray-700 mb-2">Surname <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <input id="registerSurname" name="last_name" type="text" required value="Doe"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label for="registerEmail" class="block text-sm font-semibold text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input id="registerEmail" name="email" type="email" autocomplete="email" required value="you@example.com"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label for="registerContactNumber" class="block text-sm font-semibold text-gray-700 mb-2">Contact Number <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <input id="registerContactNumber" name="phone" type="tel" required value="+1 (555) 123-4567"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label for="registerPassword" class="block text-sm font-semibold text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input id="registerPassword" name="password" type="password" autocomplete="new-password" required
                                    class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="••••••••">
                                <button type="button" id="toggleRegisterPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg id="registerPasswordEyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                    class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="••••••••">
                                <button type="button" id="togglePasswordConfirmation" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg id="passwordConfirmationEyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons (shown when form is visible) -->
                    <div id="registrationNavigation" class="flex gap-4 mt-6 hidden">
                        <button type="button" id="backToRoleSelection" class="flex-1 flex items-center justify-center px-4 py-3 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Back
                        </button>
                        <button type="submit" id="registerBtn"
                            class="flex-1 flex items-center justify-center px-4 py-3 border border-transparent rounded-lg shadow-sm text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            <span id="registerBtnText">Continue</span>
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <div id="registerBtnSpinner" class="hidden ml-2">
                                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </button>
                    </div>

                    <!-- Initial Continue Button (shown before form) -->
                    <button type="button" id="continueToRegistrationBtn"
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Continue to Registration
                    </button>

                    <!-- Disclaimer Text -->
                    <p class="text-xs text-gray-500 text-center mt-3">
                        By signing up, you agree to receive a verification email and confirm your account.
                    </p>

                    <!-- Error Message -->
                    <div id="registerErrorMessage" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm"></div>
                </form>
            </div>

            <!-- Right Panel: Features (changes based on user type) -->
            <div id="rightPanel" class="relative w-full lg:w-96 bg-slate-900 p-8 lg:p-12 text-white flex flex-col justify-between">
                <!-- Close Button -->
                <button id="closeAuthModal" class="absolute top-4 right-4 z-10 p-2 text-white hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <!-- Logo and Title -->
                <div>
                    <div class="flex items-center mb-2">
                        <svg id="rightPanelIcon" class="w-8 h-8 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <h3 class="text-2xl font-bold text-white">JobConnect</h3>
                    </div>
                    <p id="rightPanelSubtitle" class="text-sm text-gray-300 mb-8 uppercase">JOB SEEKER</p>
                </div>

                <!-- Headline -->
                <div class="mb-8">
                    <h2 id="rightPanelHeadline" class="text-3xl font-bold mb-8 text-white">Find Your Dream Job!</h2>
                    
                    <!-- Features List -->
                    <ul id="rightPanelFeatures" class="space-y-5">
                        <!-- Job Seeker Features (default) -->
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <p class="text-white text-sm leading-relaxed">Browse thousands of job opportunities from top companies</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <p class="text-white text-sm leading-relaxed">Create your professional profile and stand out to employers</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <p class="text-white text-sm leading-relaxed">Apply with confidence to verified job postings</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <p class="text-white text-sm leading-relaxed">Get notified about new opportunities matching your skills</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <p class="text-white text-sm leading-relaxed">Find opportunities near you or explore remote positions</p>
                        </li>
                    </ul>
                </div>

                <!-- Footer Text -->
                <div class="border-t border-blue-700 pt-6 mt-auto">
                    <p id="rightPanelFooter" class="text-sm text-gray-300 mb-3">Join thousands of professionals who found their dream job</p>
                    <p class="text-sm text-gray-300">
                        Looking to hire instead? 
                        <a href="#" id="switchToEmployerLink" class="text-blue-400 underline hover:text-blue-300">Create an employer account</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\lysp\Downloads\Job Ad\resources\views/partials/auth-modal.blade.php ENDPATH**/ ?>