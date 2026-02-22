// Auth Modal Management
// Define function immediately at top level so it's available right away
let currentTab = 'login'; // 'login' or 'signup'
let currentUserType = 'job_seeker'; // 'job_seeker' or 'employer' - default to job_seeker
let isInitialized = false;

// Helper function to get modal elements
function getModalElements() {
    return {
        authModal: document.getElementById('authModal'),
        closeAuthModal: document.getElementById('closeAuthModal'),
        loginTab: document.getElementById('loginTab'),
        signUpTab: document.getElementById('signUpTab'),
        loginForm: document.getElementById('loginForm'),
        registerForm: document.getElementById('registerForm'),
        userTypeSelection: document.getElementById('userTypeSelection'),
        jobSeekerBtn: document.getElementById('jobSeekerBtn'),
        employerBtn: document.getElementById('employerBtn'),
        jobSeekerFields: document.getElementById('jobSeekerFields'),
        employerFields: document.getElementById('employerFields'),
        registerUserType: document.getElementById('registerUserType'),
        rightPanel: document.getElementById('rightPanel'),
        rightPanelIcon: document.getElementById('rightPanelIcon'),
        rightPanelSubtitle: document.getElementById('rightPanelSubtitle'),
        rightPanelHeadline: document.getElementById('rightPanelHeadline'),
        rightPanelFeatures: document.getElementById('rightPanelFeatures'),
        rightPanelFooter: document.getElementById('rightPanelFooter'),
        modalTitle: document.getElementById('modalTitle'),
        modalSubtitle: document.getElementById('modalSubtitle')
    };
}

// Switch between Login and Sign Up tabs
function switchTab(tab) {
    const elements = getModalElements();
    if (!elements.authModal) return;
    
    currentTab = tab;
    
    if (tab === 'login') {
        if (elements.loginTab) {
            elements.loginTab.classList.add('bg-blue-600', 'text-white');
            elements.loginTab.classList.remove('bg-gray-100', 'text-gray-700');
        }
        if (elements.signUpTab) {
            elements.signUpTab.classList.remove('bg-blue-600', 'text-white');
            elements.signUpTab.classList.add('bg-gray-100', 'text-gray-700');
        }
        
        if (elements.loginForm) elements.loginForm.classList.remove('hidden');
        if (elements.registerForm) elements.registerForm.classList.add('hidden');
        if (elements.userTypeSelection) elements.userTypeSelection.classList.add('hidden');
        
        if (elements.modalTitle) elements.modalTitle.textContent = 'Welcome Back';
        if (elements.modalSubtitle) elements.modalSubtitle.textContent = 'Sign in to continue your journey';
        
        // Show login user type selection and update right panel (default to job seeker for login)
        const loginUserTypeSelection = document.getElementById('loginUserTypeSelection');
        if (loginUserTypeSelection) loginUserTypeSelection.classList.remove('hidden');
        const loginUserType = currentUserType === 'employer' ? 'employer' : 'job_seeker';
        updateLoginUserType(loginUserType);
        updateRightPanel(loginUserType);
    } else {
        if (elements.loginTab) {
            elements.loginTab.classList.remove('bg-blue-600', 'text-white');
            elements.loginTab.classList.add('bg-gray-100', 'text-gray-700');
        }
        if (elements.signUpTab) {
            elements.signUpTab.classList.add('bg-blue-600', 'text-white');
            elements.signUpTab.classList.remove('bg-gray-100', 'text-gray-700');
        }
        
        if (elements.loginForm) elements.loginForm.classList.add('hidden');
        if (elements.registerForm) elements.registerForm.classList.remove('hidden');
        if (elements.userTypeSelection) elements.userTypeSelection.classList.remove('hidden');
        
        if (elements.modalTitle) elements.modalTitle.textContent = 'Create Your Account';
        if (elements.modalSubtitle) elements.modalSubtitle.textContent = 'Let\'s start with your basic information';
        
        // Hide registration form fields initially
        const accountInfoFields = document.getElementById('accountInfoFields');
        const registrationProgress = document.getElementById('registrationProgress');
        const registrationFormTitle = document.getElementById('registrationFormTitle');
        const registrationNavigation = document.getElementById('registrationNavigation');
        const continueToRegistrationBtn = document.getElementById('continueToRegistrationBtn');
        const jobSeekerFields = document.getElementById('jobSeekerFields');
        const employerFields = document.getElementById('employerFields');
        
        if (accountInfoFields) accountInfoFields.classList.add('hidden');
        if (registrationProgress) registrationProgress.classList.add('hidden');
        if (registrationFormTitle) registrationFormTitle.classList.add('hidden');
        if (registrationNavigation) registrationNavigation.classList.add('hidden');
        if (continueToRegistrationBtn) continueToRegistrationBtn.classList.remove('hidden');
        if (jobSeekerFields) jobSeekerFields.classList.add('hidden');
        if (employerFields) employerFields.classList.add('hidden');
        
        // Set default to job_seeker when switching to signup
        currentUserType = 'job_seeker';
        updateUserType(currentUserType);
        updateRightPanel(currentUserType);
    }
}

// Update right panel content based on user type
function updateRightPanel(userType) {
    const elements = getModalElements();
    if (!elements.rightPanel) return;
    
    if (userType === 'job_seeker') {
        // Job Seeker content
        if (elements.rightPanelIcon) {
            elements.rightPanelIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>';
        }
        if (elements.rightPanelSubtitle) elements.rightPanelSubtitle.textContent = 'JOB SEEKER';
        if (elements.rightPanelHeadline) elements.rightPanelHeadline.textContent = 'Find Your Dream Job!';
        if (elements.rightPanelFeatures) {
            elements.rightPanelFeatures.innerHTML = `
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
            `;
        }
        const rightPanelFooter = document.getElementById('rightPanelFooter');
        const switchToEmployerLink = document.getElementById('switchToEmployerLink');
        if (rightPanelFooter) {
            rightPanelFooter.textContent = 'Join thousands of professionals who found their dream job';
        }
        if (switchToEmployerLink) {
            switchToEmployerLink.style.display = 'inline';
        }
    } else {
        // Employer content
        if (elements.rightPanelIcon) {
            elements.rightPanelIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>';
        }
        if (elements.rightPanelSubtitle) elements.rightPanelSubtitle.textContent = 'EMPLOYER';
        if (elements.rightPanelHeadline) elements.rightPanelHeadline.textContent = 'Find Top Talent!';
        if (elements.rightPanelFeatures) {
            elements.rightPanelFeatures.innerHTML = `
                <li class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <p class="text-white text-sm leading-relaxed">Access thousands of qualified job seekers actively looking for work</p>
                </li>
                <li class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <p class="text-white text-sm leading-relaxed">Quick and efficient hiring process with our streamlined tools</p>
                </li>
                <li class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <p class="text-white text-sm leading-relaxed">Verified candidate profiles ensure quality matches</p>
                </li>
                <li class="flex items-start">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-white text-sm leading-relaxed">Direct communication with applicants for faster decisions</p>
                </li>
            `;
        }
        const rightPanelFooter = document.getElementById('rightPanelFooter');
        const switchToEmployerLink = document.getElementById('switchToEmployerLink');
        if (rightPanelFooter) {
            rightPanelFooter.textContent = 'Trusted by leading companies for their hiring needs';
        }
        if (switchToEmployerLink) {
            switchToEmployerLink.style.display = 'none';
        }
    }
}

// Open modal function (global) - available immediately
window.openAuthModal = function(tab = 'login') {
    const elements = getModalElements();
    if (!elements.authModal) {
        console.warn('Auth modal not found in DOM. Make sure the modal HTML is included.');
        return;
    }
    currentTab = tab;
    elements.authModal.classList.remove('hidden');
    elements.authModal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    switchTab(tab);
};

// Wrap the rest in IIFE to avoid polluting global scope
(function() {

    // Close modal function
    function closeModal() {
        const elements = getModalElements();
        if (!elements.authModal) return;
        elements.authModal.classList.add('hidden');
        elements.authModal.classList.remove('flex');
        document.body.style.overflow = '';
        // Reset forms
        if (elements.loginForm) elements.loginForm.reset();
        if (elements.registerForm) elements.registerForm.reset();
        currentTab = 'login';
        currentUserType = 'job_seeker'; // Default to job_seeker
        switchTab('login');
        updateLoginUserType('job_seeker');
        updateRightPanel('job_seeker');
    }

    // Update login user type selection
    function updateLoginUserType(userType) {
        const loginJobSeekerBtn = document.getElementById('loginJobSeekerBtn');
        const loginEmployerBtn = document.getElementById('loginEmployerBtn');
        const loginUserType = document.getElementById('loginUserType');
        
        if (!loginJobSeekerBtn || !loginEmployerBtn) return;
        
        currentUserType = userType;
        if (loginUserType) loginUserType.value = userType;
        
        if (userType === 'job_seeker') {
            loginJobSeekerBtn.classList.add('border-blue-500', 'bg-blue-50');
            loginJobSeekerBtn.classList.remove('border-gray-200', 'bg-white');
            const icon = loginJobSeekerBtn.querySelector('svg');
            if (icon) {
                icon.classList.remove('text-gray-600');
                icon.classList.add('text-blue-600');
            }
            
            loginEmployerBtn.classList.remove('border-blue-500', 'bg-blue-50');
            loginEmployerBtn.classList.add('border-gray-200', 'bg-white');
            const employerIcon = loginEmployerBtn.querySelector('svg');
            if (employerIcon) {
                employerIcon.classList.remove('text-blue-600');
                employerIcon.classList.add('text-gray-600');
            }
        } else {
            loginJobSeekerBtn.classList.remove('border-blue-500', 'bg-blue-50');
            loginJobSeekerBtn.classList.add('border-gray-200', 'bg-white');
            const icon = loginJobSeekerBtn.querySelector('svg');
            if (icon) {
                icon.classList.remove('text-blue-600');
                icon.classList.add('text-gray-600');
            }
            
            loginEmployerBtn.classList.add('border-blue-500', 'bg-blue-50');
            loginEmployerBtn.classList.remove('border-gray-200', 'bg-white');
            const employerIcon = loginEmployerBtn.querySelector('svg');
            if (employerIcon) {
                employerIcon.classList.remove('text-gray-600');
                employerIcon.classList.add('text-blue-600');
            }
        }
        
        // Update right panel
        if (currentTab === 'login') {
            updateRightPanel(userType);
        }
    }

    // Update user type selection
    function updateUserType(userType) {
        const elements = getModalElements();
        if (!elements.authModal) return;
        
        currentUserType = userType;
        if (elements.registerUserType) elements.registerUserType.value = userType;
        
        if (userType === 'job_seeker') {
            if (elements.jobSeekerBtn) {
                elements.jobSeekerBtn.classList.add('border-blue-500', 'bg-blue-50');
                elements.jobSeekerBtn.classList.remove('border-gray-200', 'bg-white');
                const icon = elements.jobSeekerBtn.querySelector('svg');
                if (icon) {
                    icon.classList.remove('text-gray-600');
                    icon.classList.add('text-blue-600');
                }
            }
            if (elements.employerBtn) {
                elements.employerBtn.classList.remove('border-blue-500', 'bg-blue-50');
                elements.employerBtn.classList.add('border-gray-200', 'bg-white');
                const icon = elements.employerBtn.querySelector('svg');
                if (icon) {
                    icon.classList.remove('text-blue-600');
                    icon.classList.add('text-gray-600');
                }
            }
        } else {
            if (elements.jobSeekerBtn) {
                elements.jobSeekerBtn.classList.remove('border-blue-500', 'bg-blue-50');
                elements.jobSeekerBtn.classList.add('border-gray-200', 'bg-white');
                const icon = elements.jobSeekerBtn.querySelector('svg');
                if (icon) {
                    icon.classList.remove('text-blue-600');
                    icon.classList.add('text-gray-600');
                }
            }
            if (elements.employerBtn) {
                elements.employerBtn.classList.add('border-blue-500', 'bg-blue-50');
                elements.employerBtn.classList.remove('border-gray-200', 'bg-white');
                const icon = elements.employerBtn.querySelector('svg');
                if (icon) {
                    icon.classList.remove('text-gray-600');
                    icon.classList.add('text-blue-600');
                }
            }
        }
        
        // Only update right panel, don't change form fields on signup tab
        if (currentTab === 'signup') {
            updateRightPanel(userType);
        }
    }

    // Password visibility toggles
    function setupPasswordToggles() {
        // Login password toggle
        const toggleLoginPassword = document.getElementById('toggleLoginPassword');
        const loginPassword = document.getElementById('loginPassword');
        const loginPasswordEyeIcon = document.getElementById('loginPasswordEyeIcon');
        
        if (toggleLoginPassword && loginPassword) {
            toggleLoginPassword.addEventListener('click', function() {
                const type = loginPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                loginPassword.setAttribute('type', type);
                
                if (type === 'text') {
                    loginPasswordEyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
                } else {
                    loginPasswordEyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                }
            });
        }

        // Register password toggles
        const toggleRegisterPassword = document.getElementById('toggleRegisterPassword');
        const registerPassword = document.getElementById('registerPassword');
        const registerPasswordEyeIcon = document.getElementById('registerPasswordEyeIcon');
        
        const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
        const passwordConfirmation = document.getElementById('password_confirmation');
        const passwordConfirmationEyeIcon = document.getElementById('passwordConfirmationEyeIcon');

        if (toggleRegisterPassword && registerPassword) {
            toggleRegisterPassword.addEventListener('click', function() {
                const type = registerPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                registerPassword.setAttribute('type', type);
                
                if (type === 'text') {
                    registerPasswordEyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
                } else {
                    registerPasswordEyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                }
            });
        }

        if (togglePasswordConfirmation && passwordConfirmation) {
            togglePasswordConfirmation.addEventListener('click', function() {
                const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmation.setAttribute('type', type);
                
                if (type === 'text') {
                    passwordConfirmationEyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
                } else {
                    passwordConfirmationEyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                }
            });
        }
    }

    // Login form submission
    function setupLoginForm() {
        const elements = getModalElements();
        if (!elements.loginForm) return;
        
        elements.loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const errorDiv = document.getElementById('loginErrorMessage');
            const loginBtn = document.getElementById('loginBtn');
            const loginBtnText = document.getElementById('loginBtnText');
            const loginBtnSpinner = document.getElementById('loginBtnSpinner');
            
            // Show loading state
            errorDiv.classList.add('hidden');
            loginBtn.disabled = true;
            loginBtnText.textContent = 'Signing in...';
            loginBtnSpinner.classList.remove('hidden');
            
            try {
                const userType = document.getElementById('loginUserType')?.value || 'job_seeker';
                const response = await fetch('/web/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        email: formData.get('email'),
                        password: formData.get('password'),
                        user_type: userType
                    })
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    loginBtnText.textContent = 'Success! Redirecting...';
                    const redirect = data.redirect || '/dashboard';
                    setTimeout(() => {
                        window.location.href = redirect;
                    }, 300);
                } else {
                    loginBtn.disabled = false;
                    loginBtnText.textContent = 'Login';
                    loginBtnSpinner.classList.add('hidden');
                    errorDiv.textContent = data.message || 'Invalid credentials';
                    errorDiv.classList.remove('hidden');
                }
            } catch (error) {
                loginBtn.disabled = false;
                loginBtnText.textContent = 'Login';
                loginBtnSpinner.classList.add('hidden');
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('hidden');
            }
        });
    }

    // Register form submission
    function setupRegisterForm() {
        const elements = getModalElements();
        if (!elements.registerForm) return;
        
        elements.registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const errorDiv = document.getElementById('registerErrorMessage');
            const registerBtn = document.getElementById('registerBtn');
            const registerBtnText = document.getElementById('registerBtnText');
            const registerBtnSpinner = document.getElementById('registerBtnSpinner');
            
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
                const userType = formData.get('user_type');
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
                    requestData.phone = formData.get('phone');
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
                    const userType = data.user?.user_type || data.user_type;
                    const redirect = userType === 'employer' ? '/employer/dashboard' : '/dashboard';
                    setTimeout(() => {
                        window.location.href = redirect;
                    }, 500);
                } else {
                    registerBtn.disabled = false;
                    registerBtnText.textContent = 'Continue';
                    registerBtnSpinner.classList.add('hidden');
                    const errorMsg = data.errors ? Object.values(data.errors).flat().join(', ') : data.message || 'Registration failed';
                    errorDiv.textContent = errorMsg;
                    errorDiv.classList.remove('hidden');
                }
            } catch (error) {
                registerBtn.disabled = false;
                registerBtnText.textContent = 'Continue';
                registerBtnSpinner.classList.add('hidden');
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('hidden');
            }
        });
    }

    // Initialize
    function init() {
        const elements = getModalElements();
        if (!elements.authModal) {
            // Modal not in DOM yet, try again later
            setTimeout(init, 100);
            return;
        }
        
        isInitialized = true;
        
        // Close modal events
        if (elements.closeAuthModal) {
            elements.closeAuthModal.addEventListener('click', closeModal);
        }
        
        elements.authModal.addEventListener('click', function(e) {
            if (e.target === elements.authModal) {
                closeModal();
            }
        });
        
        // Tab switching
        if (elements.loginTab) {
            elements.loginTab.addEventListener('click', () => switchTab('login'));
        }
        
        if (elements.signUpTab) {
            elements.signUpTab.addEventListener('click', () => switchTab('signup'));
        }
        
        // User type selection for signup
        if (elements.jobSeekerBtn) {
            elements.jobSeekerBtn.addEventListener('click', () => updateUserType('job_seeker'));
        }
        
        if (elements.employerBtn) {
            elements.employerBtn.addEventListener('click', () => updateUserType('employer'));
        }
        
        // User type selection for login
        const loginJobSeekerBtn = document.getElementById('loginJobSeekerBtn');
        const loginEmployerBtn = document.getElementById('loginEmployerBtn');
        if (loginJobSeekerBtn) {
            loginJobSeekerBtn.addEventListener('click', () => updateLoginUserType('job_seeker'));
        }
        if (loginEmployerBtn) {
            loginEmployerBtn.addEventListener('click', () => updateLoginUserType('employer'));
        }
        
        // Continue to Registration button
        const continueToRegistrationBtn = document.getElementById('continueToRegistrationBtn');
        if (continueToRegistrationBtn) {
            continueToRegistrationBtn.addEventListener('click', function() {
                // Hide role selection and continue button
                const userTypeSelection = document.getElementById('userTypeSelection');
                if (userTypeSelection) userTypeSelection.classList.add('hidden');
                continueToRegistrationBtn.classList.add('hidden');
                
                // Show registration form
                const accountInfoFields = document.getElementById('accountInfoFields');
                const registrationProgress = document.getElementById('registrationProgress');
                const registrationFormTitle = document.getElementById('registrationFormTitle');
                const registrationNavigation = document.getElementById('registrationNavigation');
                
                if (accountInfoFields) accountInfoFields.classList.remove('hidden');
                if (registrationProgress) registrationProgress.classList.remove('hidden');
                if (registrationFormTitle) registrationFormTitle.classList.remove('hidden');
                if (registrationNavigation) registrationNavigation.classList.remove('hidden');
                
                // Update title (already set, but ensure it's correct)
                const modalTitle = document.getElementById('modalTitle');
                const modalSubtitle = document.getElementById('modalSubtitle');
                if (modalTitle) modalTitle.textContent = 'Create Your Account';
                if (modalSubtitle) modalSubtitle.textContent = 'Let\'s start with your basic information';
            });
        }
        
        // Back to role selection button
        const backToRoleSelection = document.getElementById('backToRoleSelection');
        if (backToRoleSelection) {
            backToRoleSelection.addEventListener('click', function() {
                // Show role selection and continue button
                const userTypeSelection = document.getElementById('userTypeSelection');
                if (userTypeSelection) userTypeSelection.classList.remove('hidden');
                if (continueToRegistrationBtn) continueToRegistrationBtn.classList.remove('hidden');
                
                // Hide registration form
                const accountInfoFields = document.getElementById('accountInfoFields');
                const registrationProgress = document.getElementById('registrationProgress');
                const registrationFormTitle = document.getElementById('registrationFormTitle');
                const registrationNavigation = document.getElementById('registrationNavigation');
                
                if (accountInfoFields) accountInfoFields.classList.add('hidden');
                if (registrationProgress) registrationProgress.classList.add('hidden');
                if (registrationFormTitle) registrationFormTitle.classList.add('hidden');
                if (registrationNavigation) registrationNavigation.classList.add('hidden');
                
                // Update title
                const modalTitle = document.getElementById('modalTitle');
                const modalSubtitle = document.getElementById('modalSubtitle');
                if (modalTitle) modalTitle.textContent = 'Create Account';
                if (modalSubtitle) modalSubtitle.textContent = 'Join our community today';
            });
        }
        
        // Switch to employer link
        const switchToEmployerLink = document.getElementById('switchToEmployerLink');
        if (switchToEmployerLink) {
            switchToEmployerLink.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentTab === 'signup') {
                    updateUserType('employer');
                }
            });
        }
        
        // Setup forms
        setupPasswordToggles();
        setupLoginForm();
        setupRegisterForm();
        
        // Initialize right panel (default to job_seeker)
        updateLoginUserType('job_seeker');
        updateRightPanel('job_seeker');
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
