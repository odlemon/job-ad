<!-- Auth Modal -->
<div id="authModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="relative w-full max-w-4xl mx-4 rounded-2xl shadow-2xl overflow-hidden" style="max-height: 90vh;">
        <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden">
            
            <!-- Progress Bar (at the top of the card, full width, hidden by default) -->
            <div id="registrationProgress" class="bg-white dark:bg-gray-800 px-10 pt-8 pb-4 hidden">
                <div class="flex items-center justify-between max-w-lg mx-auto">
                    <!-- Step 1 -->
                    <div class="flex flex-col items-center">
                        <div id="progressStep1" class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-cyan-400 text-white flex items-center justify-center text-base font-bold">1</div>
                        <span id="progressLabel1" class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Account Info</span>
                    </div>
                    <div id="progressLine1" class="flex-1 h-0.5 bg-gray-300 dark:bg-gray-600 mx-4 mb-6"></div>
                    <!-- Step 2 -->
                    <div class="flex flex-col items-center">
                        <div id="progressStep2" class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 flex items-center justify-center text-base font-semibold">2</div>
                        <span id="progressLabel2" class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">Profile Details</span>
                    </div>
                    <div id="progressLine2" class="flex-1 h-0.5 bg-gray-300 dark:bg-gray-600 mx-4 mb-6"></div>
                    <!-- Step 3 -->
                    <div class="flex flex-col items-center">
                        <div id="progressStep3" class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 flex items-center justify-center text-base font-semibold">3</div>
                        <span id="progressLabel3" class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">Verification</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row h-full overflow-hidden">
                <!-- Left Panel: Form -->
                <div class="flex-1 bg-white dark:bg-gray-800 p-8 lg:p-10 overflow-y-auto" style="max-height: 75vh;">
                    <!-- Header -->
                    <div id="modalHeader" class="mb-6">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2" id="modalTitle">Welcome Back</h2>
                        <p class="text-base text-gray-600 dark:text-gray-400" id="modalSubtitle">Sign in to continue your journey</p>
                    </div>

                    <!-- Tabs: Login / Sign Up (hidden when registration form is showing) -->
                    <div id="authTabs" class="flex gap-2 mb-6">
                        <button id="loginTab" class="flex-1 px-4 py-2.5 rounded-lg text-base font-medium transition bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-md">
                            Login
                        </button>
                        <button id="signUpTab" class="flex-1 px-4 py-2.5 rounded-lg text-base font-medium transition bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200">
                            Sign Up
                        </button>
                    </div>

                    <!-- User Type Selection (only shown in Sign Up initial view) -->
                    <div id="userTypeSelection" class="mb-6 hidden">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">I am a:</label>
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" id="jobSeekerBtn" class="user-type-btn p-4 border-2 border-blue-500 bg-blue-50 rounded-lg transition hover:border-blue-600">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="font-semibold text-gray-900 dark:text-white text-base">Job Seeker</span>
                                </div>
                            </button>
                            <button type="button" id="employerBtn" class="user-type-btn p-4 border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg transition hover:border-gray-300 dark:hover:border-gray-600">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 text-gray-600 dark:text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="font-semibold text-gray-900 dark:text-white text-base">Employer</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Login Form -->
                    <form id="loginForm" class="space-y-5">
                        @csrf
                        <input type="hidden" name="user_type" id="loginUserType" value="job_seeker">
                        
                        <!-- User Type Selection for Login -->
                        <div id="loginUserTypeSelection" class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">I am a:</label>
                            <div class="grid grid-cols-2 gap-4">
                                <button type="button" id="loginJobSeekerBtn" class="login-user-type-btn p-4 border-2 border-blue-500 bg-blue-50 rounded-lg transition hover:border-blue-600">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-10 h-10 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="font-semibold text-gray-900 dark:text-white text-base">Job Seeker</span>
                                    </div>
                                </button>
                                <button type="button" id="loginEmployerBtn" class="login-user-type-btn p-4 border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg transition hover:border-gray-300 dark:hover:border-gray-600">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-10 h-10 text-gray-600 dark:text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="font-semibold text-gray-900 dark:text-white text-base">Employer</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label for="loginEmail" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                                <input id="loginEmail" name="email" type="email" autocomplete="email" required 
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="you@example.com">
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="loginPassword" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input id="loginPassword" name="password" type="password" autocomplete="current-password" required 
                                    class="block w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="••••••••">
                                <button type="button" id="toggleLoginPassword" data-toggle-password="loginPassword"
                                    class="absolute inset-y-0 right-0 z-10 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                                    aria-label="Show password">
                                    <svg class="h-5 w-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path class="eye-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path class="eye-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        <path class="eye-closed hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="loginBtn"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm dark:shadow-none text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
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
                        @csrf
                        <input type="hidden" name="user_type" id="registerUserType" value="job_seeker">
                        
                        <!-- ==================== STEP 1: Account Info ==================== -->
                        <div id="registerStep1" class="space-y-5 hidden">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="registerFirstName" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">First Name <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <input id="registerFirstName" name="first_name" type="text" required
                                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="John">
                                    </div>
                                </div>
                                <div>
                                    <label for="registerSurname" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Surname <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <input id="registerSurname" name="last_name" type="text" required
                                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="Doe">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="registerEmail" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email Address <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <input id="registerEmail" name="email" type="email" autocomplete="email" required
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="you@example.com">
                                </div>
                            </div>
                            <div>
                                <label for="registerContactNumber" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Contact Number <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <input id="registerContactNumber" name="phone" type="tel" required
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="+1 (555) 123-4567">
                                </div>
                            </div>
                            <div>
                                <label for="registerPassword" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <input id="registerPassword" name="password" type="password" autocomplete="new-password" required
                                        class="block w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="••••••••">
                                    <button type="button" id="toggleRegisterPassword" data-toggle-password="registerPassword"
                                        class="absolute inset-y-0 right-0 z-10 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                                        aria-label="Show password">
                                        <svg class="h-5 w-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path class="eye-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path class="eye-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            <path class="eye-closed hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Minimum 6 characters</p>
                                <!-- Password Strength Indicator -->
                                <div id="passwordStrength" class="mt-2 hidden">
                                    <div class="flex gap-1 mb-1">
                                        <div id="strengthBar1" class="h-1 flex-1 rounded bg-gray-200 dark:bg-gray-700"></div>
                                        <div id="strengthBar2" class="h-1 flex-1 rounded bg-gray-200 dark:bg-gray-700"></div>
                                        <div id="strengthBar3" class="h-1 flex-1 rounded bg-gray-200 dark:bg-gray-700"></div>
                                        <div id="strengthBar4" class="h-1 flex-1 rounded bg-gray-200 dark:bg-gray-700"></div>
                                    </div>
                                    <p id="strengthText" class="text-xs text-gray-500 dark:text-gray-400"></p>
                                </div>
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                        class="block w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="••••••••">
                                    <button type="button" id="togglePasswordConfirmation" data-toggle-password="password_confirmation"
                                        class="absolute inset-y-0 right-0 z-10 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                                        aria-label="Show password">
                                        <svg class="h-5 w-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path class="eye-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path class="eye-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            <path class="eye-closed hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                        </svg>
                                    </button>
                                </div>
                                <p id="passwordMatchError" class="text-xs text-red-500 mt-1 hidden">Passwords do not match</p>
                            </div>
                        </div>

                        <!-- ==================== STEP 2: Profile Details (Job Seeker) ==================== -->
                        <div id="registerStep2" class="space-y-5 hidden">
                            <div>
                                <label for="registerDob" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Date of Birth <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <input id="registerDob" name="date_of_birth" type="date" required
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                            <div>
                                <label for="registerGender" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Gender <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <select id="registerGender" name="gender" required
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white dark:bg-gray-800">
                                        <option value="" disabled selected>Select gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                        <option value="prefer_not_to_say">Prefer not to say</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="registerEmploymentStatus" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Employment Status <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <select id="registerEmploymentStatus" name="employment_status" required
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white dark:bg-gray-800">
                                        <option value="" disabled selected>Select employment status</option>
                                        <option value="currently_employed">Currently Employed</option>
                                        <option value="unemployed">Unemployed</option>
                                        <option value="student">Student</option>
                                        <option value="self_employed">Self Employed</option>
                                        <option value="retired">Retired</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="registerHighestDegree" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Highest Degree <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                        </svg>
                                    </div>
                                    <select id="registerHighestDegree" name="highest_education" required
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white dark:bg-gray-800">
                                        <option value="" disabled selected>Select highest degree</option>
                                        <option value="high_school">High School / Secondary</option>
                                        <option value="diploma">Diploma / Certificate</option>
                                        <option value="associate">Associate Degree</option>
                                        <option value="bachelor">Bachelor's Degree</option>
                                        <option value="master">Master's Degree</option>
                                        <option value="doctorate">Doctorate / PhD</option>
                                        <option value="professional">Professional Degree</option>
                                        <option value="none">No Formal Education</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== STEP 2: Employer Details ==================== -->
                        <div id="registerStep2Employer" class="space-y-5 hidden">
                            <!-- Company Name -->
                            <div>
                                <label for="company_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Company Name <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <input id="company_name" name="company_name" type="text" required
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Acme Corporation">
                                </div>
                            </div>
                            <!-- Company Size -->
                            <div>
                                <label for="company_size" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Company Size <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <select id="company_size" name="company_size" required
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white dark:bg-gray-800">
                                        <option value="" disabled selected>Select company size</option>
                                        <option value="1-10">1-10 employees</option>
                                        <option value="11-50">11-50 employees</option>
                                        <option value="51-200">51-200 employees</option>
                                        <option value="201-500">201-500 employees</option>
                                        <option value="500+">500+ employees</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Industry -->
                            <div>
                                <label for="industry" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Industry <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <select id="industry" name="industry" required
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white dark:bg-gray-800">
                                        <option value="" disabled selected>Select industry</option>
                                        <option value="Technology">Technology</option>
                                        <option value="Healthcare">Healthcare</option>
                                        <option value="Finance">Finance</option>
                                        <option value="Education">Education</option>
                                        <option value="Manufacturing">Manufacturing</option>
                                        <option value="Retail">Retail</option>
                                        <option value="Hospitality">Hospitality</option>
                                        <option value="Real Estate">Real Estate</option>
                                        <option value="Consulting">Consulting</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Company Website (Optional) -->
                            <div>
                                <label for="website" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Company Website <span class="text-gray-400 font-normal italic">(Optional)</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                        </svg>
                                    </div>
                                    <input id="website" name="website" type="url"
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="https://www.company.com">
                                </div>
                            </div>
                            <!-- Business Certificate / Proof of Ownership -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Business Certificate / Proof of Ownership <span class="text-red-500">*</span></label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Upload a business registration certificate, tax document, or official proof that validates your ownership or employment at the company</p>
                                <div id="businessCertDropZone" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                                    <input type="file" id="businessCertInput" name="business_certificate" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                    <div id="businessCertPlaceholder">
                                        <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Click to upload document</p>
                                        <p class="text-xs text-gray-400 mt-1">PDF, JPG, or PNG (Max 5MB)</p>
                                    </div>
                                    <div id="businessCertPreview" class="hidden">
                                        <div class="flex items-center justify-center gap-3">
                                            <svg class="w-8 h-8 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div class="text-left">
                                                <p id="businessCertFileName" class="text-sm font-semibold text-gray-700 dark:text-gray-300 truncate max-w-xs"></p>
                                                <p id="businessCertFileSize" class="text-xs text-gray-400"></p>
                                            </div>
                                            <button type="button" id="businessCertRemove" class="ml-2 text-red-500 hover:text-red-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <p id="businessCertError" class="text-xs text-red-500 mt-1 hidden">Please upload a business certificate</p>
                            </div>
                        </div>

                        <!-- ==================== STEP 3: Security Verification ==================== -->
                        <div id="registerStep3" class="space-y-5 hidden">
                            <!-- Security Check Card -->
                            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                                <div class="flex items-center mb-4">
                                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Security Check</h3>
                                </div>
                                <p id="captchaQuestion" class="text-base font-semibold text-gray-900 dark:text-white mb-4"></p>
                                <input id="captchaAnswer" type="text" required
                                    class="block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Enter your answer">
                                <p id="captchaError" class="text-xs text-red-500 mt-1 hidden">Incorrect answer. Please try again.</p>
                            </div>

                            <!-- Note -->
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-bold">Note:</span> By creating an account, you agree to receive a verification email. Please check your inbox and verify your email address to activate your account.
                                </p>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div id="registrationNavigation" class="flex gap-4 mt-6 hidden">
                            <button type="button" id="backBtn" class="flex items-center justify-center px-6 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back
                            </button>
                            <button type="button" id="continueBtn"
                                class="flex-1 flex items-center justify-center px-4 py-3 border border-transparent rounded-lg shadow-sm dark:shadow-none text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                <span id="continueBtnText">Continue</span>
                                <svg id="continueBtnArrow" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                                <div id="registerBtnSpinner" class="hidden ml-2">
                                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </button>
                        </div>

                        <!-- Initial Continue Button (shown before form, on role selection view) -->
                        <button type="button" id="continueToRegistrationBtn"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm dark:shadow-none text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            Continue to Registration
                        </button>

                        <!-- Disclaimer Text -->
                        <p id="disclaimerText" class="text-xs text-gray-500 dark:text-gray-400 text-center mt-3">
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
                        <p id="rightPanelFooter" class="text-sm text-gray-300 italic mb-3">Join thousands of professionals who found their dream job</p>
                        <div id="rightPanelFooterLinks">
                            <p class="text-sm text-gray-300">
                                Looking to hire instead? 
                                <a href="#" id="switchToEmployerLink" class="text-blue-400 underline hover:text-blue-300">Create an employer account</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
