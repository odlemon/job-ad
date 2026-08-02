// Auth Modal Management
let currentTab = 'login';
let currentUserType = 'job_seeker';
let currentStep = 0; // 0 = role selection, 1 = account info, 2 = profile details, 3 = verification
let captchaNum1 = 0;
let captchaNum2 = 0;
let isInitialized = false;

// Generate a simple math captcha
function generateCaptcha() {
    captchaNum1 = Math.floor(Math.random() * 20) + 1;
    captchaNum2 = Math.floor(Math.random() * 20) + 1;
    const questionEl = document.getElementById('captchaQuestion');
    if (questionEl) {
        questionEl.textContent = `What is ${captchaNum1} + ${captchaNum2}?`;
    }
}

// Password strength checker
function checkPasswordStrength(password) {
    let score = 0;
    if (password.length >= 6) score++;
    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password) && /[a-z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    
    // Normalize to 1-4
    if (score <= 1) return { level: 1, text: 'Weak', color: 'bg-red-500' };
    if (score === 2) return { level: 2, text: 'Fair', color: 'bg-orange-500' };
    if (score === 3) return { level: 3, text: 'Good', color: 'bg-yellow-500' };
    return { level: 4, text: 'Strong', color: 'bg-green-500' };
}

function updatePasswordStrengthUI(password) {
    const strengthDiv = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('strengthText');
    if (!strengthDiv || !strengthText) return;

    if (!password) {
        strengthDiv.classList.add('hidden');
        return;
    }
    strengthDiv.classList.remove('hidden');

    const strength = checkPasswordStrength(password);
    const bars = [
        document.getElementById('strengthBar1'),
        document.getElementById('strengthBar2'),
        document.getElementById('strengthBar3'),
        document.getElementById('strengthBar4')
    ];

    bars.forEach((bar, i) => {
        bar.className = 'h-1 flex-1 rounded';
        if (i < strength.level) {
            bar.classList.add(strength.color);
        } else {
            bar.classList.add('bg-gray-200');
        }
    });

    const textColors = { 1: 'text-red-500', 2: 'text-orange-500', 3: 'text-yellow-600', 4: 'text-green-500' };
    strengthText.className = 'text-xs ' + textColors[strength.level];
    strengthText.textContent = strength.text;
}

// Update progress bar
function updateProgressBar(step) {
    const steps = [
        { circle: document.getElementById('progressStep1'), label: document.getElementById('progressLabel1'), line: document.getElementById('progressLine1') },
        { circle: document.getElementById('progressStep2'), label: document.getElementById('progressLabel2'), line: document.getElementById('progressLine2') },
        { circle: document.getElementById('progressStep3'), label: document.getElementById('progressLabel3'), line: null },
    ];

    steps.forEach((s, i) => {
        if (!s.circle) return;
        const stepNum = i + 1;
        
        if (stepNum < step) {
            // Completed: green with checkmark
            s.circle.className = 'w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center text-base font-bold';
            s.circle.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            if (s.label) { s.label.className = 'mt-2 text-sm font-semibold text-gray-900 dark:text-white'; }
            if (s.line) { s.line.className = 'flex-1 h-0.5 bg-green-500 mx-4 mb-6'; }
        } else if (stepNum === step) {
            // Active: blue
            s.circle.className = 'w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center text-base font-bold';
            s.circle.innerHTML = stepNum;
            if (s.label) { s.label.className = 'mt-2 text-sm font-semibold text-gray-900 dark:text-white'; }
            if (s.line) { s.line.className = 'flex-1 h-0.5 bg-gray-300 mx-4 mb-6'; }
        } else {
            // Upcoming: gray
            s.circle.className = 'w-12 h-12 rounded-full bg-gray-200 text-gray-500 dark:text-gray-400 flex items-center justify-center text-base font-semibold';
            s.circle.innerHTML = stepNum;
            if (s.label) { s.label.className = 'mt-2 text-sm font-medium text-gray-500 dark:text-gray-400'; }
            if (s.line) { s.line.className = 'flex-1 h-0.5 bg-gray-300 mx-4 mb-6'; }
        }
    });
}

// Show specific registration step
function showStep(step) {
    currentStep = step;
    const authTabs = document.getElementById('authTabs');
    const modalHeader = document.getElementById('modalHeader');
    const registrationProgress = document.getElementById('registrationProgress');
    const userTypeSelection = document.getElementById('userTypeSelection');
    const continueToRegistrationBtn = document.getElementById('continueToRegistrationBtn');
    const disclaimerText = document.getElementById('disclaimerText');
    const registrationNavigation = document.getElementById('registrationNavigation');
    const registerStep1 = document.getElementById('registerStep1');
    const registerStep2 = document.getElementById('registerStep2');
    const registerStep2Employer = document.getElementById('registerStep2Employer');
    const registerStep3 = document.getElementById('registerStep3');
    const modalTitle = document.getElementById('modalTitle');
    const modalSubtitle = document.getElementById('modalSubtitle');
    const continueBtnText = document.getElementById('continueBtnText');
    const continueBtnArrow = document.getElementById('continueBtnArrow');

    // Hide everything first
    if (registerStep1) registerStep1.classList.add('hidden');
    if (registerStep2) registerStep2.classList.add('hidden');
    if (registerStep2Employer) registerStep2Employer.classList.add('hidden');
    if (registerStep3) registerStep3.classList.add('hidden');

    if (step === 0) {
        // Role selection view
        if (authTabs) authTabs.classList.remove('hidden');
        if (modalHeader) modalHeader.classList.remove('hidden');
        if (registrationProgress) registrationProgress.classList.add('hidden');
        if (userTypeSelection) userTypeSelection.classList.remove('hidden');
        if (continueToRegistrationBtn) continueToRegistrationBtn.classList.remove('hidden');
        if (disclaimerText) disclaimerText.classList.remove('hidden');
        if (registrationNavigation) registrationNavigation.classList.add('hidden');
        if (modalTitle) modalTitle.textContent = 'Create Your Account';
        if (modalSubtitle) modalSubtitle.textContent = 'Let\'s start with your basic information';
    } else if (step === 1) {
        // Step 1: Account Info
        if (authTabs) authTabs.classList.add('hidden');
        if (modalHeader) modalHeader.classList.remove('hidden');
        if (registrationProgress) registrationProgress.classList.remove('hidden');
        if (userTypeSelection) userTypeSelection.classList.add('hidden');
        if (continueToRegistrationBtn) continueToRegistrationBtn.classList.add('hidden');
        if (disclaimerText) disclaimerText.classList.add('hidden');
        if (registrationNavigation) registrationNavigation.classList.remove('hidden');
        if (registerStep1) registerStep1.classList.remove('hidden');
        if (modalTitle) modalTitle.textContent = 'Create Your Account';
        if (modalSubtitle) modalSubtitle.textContent = 'Let\'s start with your basic information';
        if (continueBtnText) continueBtnText.textContent = 'Continue';
        if (continueBtnArrow) continueBtnArrow.classList.remove('hidden');
        updateProgressBar(1);
    } else if (step === 2) {
        // Step 2: Profile Details
        if (authTabs) authTabs.classList.add('hidden');
        if (modalHeader) modalHeader.classList.remove('hidden');
        if (registrationProgress) registrationProgress.classList.remove('hidden');
        if (userTypeSelection) userTypeSelection.classList.add('hidden');
        if (continueToRegistrationBtn) continueToRegistrationBtn.classList.add('hidden');
        if (disclaimerText) disclaimerText.classList.add('hidden');
        if (registrationNavigation) registrationNavigation.classList.remove('hidden');
        
        if (currentUserType === 'job_seeker') {
            if (registerStep2) registerStep2.classList.remove('hidden');
            if (modalTitle) modalTitle.textContent = 'Profile Details';
            if (modalSubtitle) modalSubtitle.textContent = 'Tell us more about yourself';
            // Update progress bar label for job seeker
            const progressLabel2 = document.getElementById('progressLabel2');
            if (progressLabel2) progressLabel2.textContent = 'Profile Details';
        } else {
            if (registerStep2Employer) registerStep2Employer.classList.remove('hidden');
            if (modalTitle) modalTitle.textContent = 'Company Details';
            if (modalSubtitle) modalSubtitle.textContent = 'Tell us about your organization';
            // Update progress bar label for employer
            const progressLabel2 = document.getElementById('progressLabel2');
            if (progressLabel2) progressLabel2.textContent = 'Company Details';
        }
        if (continueBtnText) continueBtnText.textContent = 'Continue';
        if (continueBtnArrow) continueBtnArrow.classList.remove('hidden');
        updateProgressBar(2);
    } else if (step === 3) {
        // Step 3: Verification
        if (authTabs) authTabs.classList.add('hidden');
        if (modalHeader) modalHeader.classList.remove('hidden');
        if (registrationProgress) registrationProgress.classList.remove('hidden');
        if (userTypeSelection) userTypeSelection.classList.add('hidden');
        if (continueToRegistrationBtn) continueToRegistrationBtn.classList.add('hidden');
        if (disclaimerText) disclaimerText.classList.add('hidden');
        if (registrationNavigation) registrationNavigation.classList.remove('hidden');
        if (registerStep3) registerStep3.classList.remove('hidden');
        if (modalTitle) modalTitle.textContent = 'Security Verification';
        if (modalSubtitle) modalSubtitle.textContent = 'Complete this final step to create your account';
        if (continueBtnText) continueBtnText.textContent = 'Create Account';
        if (continueBtnArrow) continueBtnArrow.classList.add('hidden');
        updateProgressBar(3);
        generateCaptcha();
    }
}

// Validate current step
function validateStep(step) {
    const errorDiv = document.getElementById('registerErrorMessage');
    if (errorDiv) errorDiv.classList.add('hidden');

    if (step === 1) {
        const firstName = document.getElementById('registerFirstName')?.value?.trim();
        const lastName = document.getElementById('registerSurname')?.value?.trim();
        const email = document.getElementById('registerEmail')?.value?.trim();
        const phone = document.getElementById('registerContactNumber')?.value?.trim();
        const password = document.getElementById('registerPassword')?.value;
        const passwordConfirmation = document.getElementById('password_confirmation')?.value;

        if (!firstName) { showRegError('First name is required'); return false; }
        if (!lastName) { showRegError('Surname is required'); return false; }
        if (!email) { showRegError('Email address is required'); return false; }
        if (!isValidEmail(email)) { showRegError('Please enter a valid email address'); return false; }
        if (!phone) { showRegError('Contact number is required'); return false; }
        if (!password) { showRegError('Password is required'); return false; }
        if (password.length < 6) { showRegError('Password must be at least 6 characters'); return false; }
        if (!passwordConfirmation) { showRegError('Please confirm your password'); return false; }
        if (password !== passwordConfirmation) {
            showRegError('Passwords do not match');
            const matchError = document.getElementById('passwordMatchError');
            if (matchError) matchError.classList.remove('hidden');
            return false;
        }
        return true;
    }

    if (step === 2) {
        if (currentUserType === 'job_seeker') {
            const dob = document.getElementById('registerDob')?.value;
            const gender = document.getElementById('registerGender')?.value;
            const employment = document.getElementById('registerEmploymentStatus')?.value;
            const degree = document.getElementById('registerHighestDegree')?.value;

            if (!dob) { showRegError('Date of birth is required'); return false; }
            if (!gender) { showRegError('Gender is required'); return false; }
            if (!employment) { showRegError('Employment status is required'); return false; }
            if (!degree) { showRegError('Highest degree is required'); return false; }
        } else {
            const companyName = document.getElementById('company_name')?.value?.trim();
            const companySize = document.getElementById('company_size')?.value;
            const industry = document.getElementById('industry')?.value;
            if (!companyName) { showRegError('Company name is required'); return false; }
            if (!companySize) { showRegError('Company size is required'); return false; }
            if (!industry) { showRegError('Industry is required'); return false; }
            // Validate business certificate
            const certInput = document.getElementById('businessCertInput');
            if (!certInput || !certInput.files || certInput.files.length === 0) {
                showRegError('Business certificate is required');
                const certError = document.getElementById('businessCertError');
                if (certError) certError.classList.remove('hidden');
                return false;
            }
            const file = certInput.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                showRegError('Business certificate must be less than 5MB');
                return false;
            }
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                showRegError('Business certificate must be PDF, JPG, or PNG');
                return false;
            }
        }
        return true;
    }

    if (step === 3) {
        const answer = document.getElementById('captchaAnswer')?.value?.trim();
        const correctAnswer = captchaNum1 + captchaNum2;
        const captchaError = document.getElementById('captchaError');
        
        if (!answer) { showRegError('Please answer the security question'); return false; }
        if (parseInt(answer) !== correctAnswer) {
            if (captchaError) captchaError.classList.remove('hidden');
            generateCaptcha(); // New question
            document.getElementById('captchaAnswer').value = '';
            return false;
        }
        if (captchaError) captchaError.classList.add('hidden');
        return true;
    }

    return true;
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showRegError(msg) {
    const errorDiv = document.getElementById('registerErrorMessage');
    if (errorDiv) {
        errorDiv.textContent = msg;
        errorDiv.classList.remove('hidden');
    }
}

// Helper function to get modal elements
function getModalElements() {
    return {
        authModal: document.getElementById('authModal'),
        closeAuthModal: document.getElementById('closeAuthModal'),
        loginTab: document.getElementById('loginTab'),
        signUpTab: document.getElementById('signUpTab'),
        authTabs: document.getElementById('authTabs'),
        loginForm: document.getElementById('loginForm'),
        registerForm: document.getElementById('registerForm'),
        userTypeSelection: document.getElementById('userTypeSelection'),
        jobSeekerBtn: document.getElementById('jobSeekerBtn'),
        employerBtn: document.getElementById('employerBtn'),
        registerUserType: document.getElementById('registerUserType'),
        rightPanel: document.getElementById('rightPanel'),
        rightPanelIcon: document.getElementById('rightPanelIcon'),
        rightPanelSubtitle: document.getElementById('rightPanelSubtitle'),
        rightPanelHeadline: document.getElementById('rightPanelHeadline'),
        rightPanelFeatures: document.getElementById('rightPanelFeatures'),
        rightPanelFooter: document.getElementById('rightPanelFooter'),
        rightPanelFooterLinks: document.getElementById('rightPanelFooterLinks'),
        modalTitle: document.getElementById('modalTitle'),
        modalSubtitle: document.getElementById('modalSubtitle'),
        modalHeader: document.getElementById('modalHeader')
    };
}

// Switch between Login and Sign Up tabs
function switchTab(tab) {
    const elements = getModalElements();
    if (!elements.authModal) return;
    
    currentTab = tab;
    
    if (tab === 'login') {
        if (elements.authTabs) elements.authTabs.classList.remove('hidden');
        if (elements.modalHeader) elements.modalHeader.classList.remove('hidden');
        const registrationProgress = document.getElementById('registrationProgress');
        if (registrationProgress) registrationProgress.classList.add('hidden');
        
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
        
        currentUserType = 'job_seeker';
        updateUserType(currentUserType);
        updateRightPanel(currentUserType);
        
        // Show role selection (step 0)
        showStep(0);
    }
}

// Update right panel content based on user type
function updateRightPanel(userType) {
    const elements = getModalElements();
    if (!elements.rightPanel) return;
    
    if (userType === 'job_seeker') {
        if (elements.rightPanelIcon) {
            elements.rightPanelIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>';
        }
        if (elements.rightPanelSubtitle) elements.rightPanelSubtitle.textContent = 'JOB SEEKER';
        if (elements.rightPanelHeadline) elements.rightPanelHeadline.textContent = 'Find Your Dream Job!';
        if (elements.rightPanelFeatures) {
            elements.rightPanelFeatures.innerHTML = `
                <li class="flex items-start"><div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div><p class="text-white text-sm leading-relaxed">Browse thousands of job opportunities from top companies</p></li>
                <li class="flex items-start"><div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg></div><p class="text-white text-sm leading-relaxed">Create your professional profile and stand out to employers</p></li>
                <li class="flex items-start"><div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div><p class="text-white text-sm leading-relaxed">Apply with confidence to verified job postings</p></li>
                <li class="flex items-start"><div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></div><p class="text-white text-sm leading-relaxed">Get notified about new opportunities matching your skills</p></li>
                <li class="flex items-start"><div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></div><p class="text-white text-sm leading-relaxed">Find opportunities near you or explore remote positions</p></li>
            `;
        }
        if (elements.rightPanelFooter) {
            elements.rightPanelFooter.textContent = 'Join thousands of professionals who found their dream job';
        }
        if (elements.rightPanelFooterLinks) {
            elements.rightPanelFooterLinks.innerHTML = '<p class="text-sm text-gray-300">Looking to hire instead? <a href="#" id="switchToEmployerLink" class="text-blue-400 underline hover:text-blue-300">Create an employer account</a></p>';
            const link = document.getElementById('switchToEmployerLink');
            if (link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    updateUserType('employer');
                });
            }
        }
    } else {
        if (elements.rightPanelIcon) {
            elements.rightPanelIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>';
        }
        if (elements.rightPanelSubtitle) elements.rightPanelSubtitle.textContent = 'EMPLOYER';
        if (elements.rightPanelHeadline) elements.rightPanelHeadline.textContent = 'Start Hiring Today!';
        if (elements.rightPanelFeatures) {
            elements.rightPanelFeatures.innerHTML = `
                <li class="flex items-start"><div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div><p class="text-white text-sm leading-relaxed">Post unlimited job openings and reach thousands of qualified candidates</p></li>
                <li class="flex items-start"><div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></div><p class="text-white text-sm leading-relaxed">Advanced screening tools to filter and find the perfect match for your team</p></li>
                <li class="flex items-start"><div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg></div><p class="text-white text-sm leading-relaxed">Dashboard with analytics to track your hiring performance and insights</p></li>
                <li class="flex items-start"><div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div><p class="text-white text-sm leading-relaxed">Secure and verified candidate profiles to ensure quality hires</p></li>
                <li class="flex items-start"><div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center mr-4"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></div><p class="text-white text-sm leading-relaxed">Direct messaging system to communicate with applicants instantly</p></li>
            `;
        }
        if (elements.rightPanelFooter) {
            elements.rightPanelFooter.textContent = 'Join thousands of companies already hiring through JobConnect';
        }
        if (elements.rightPanelFooterLinks) {
            elements.rightPanelFooterLinks.innerHTML = `
                <p class="text-gray-400 text-sm">Looking for a job instead?</p>
                <a href="#" id="switchToJobSeekerLink" class="text-blue-400 hover:text-blue-300 text-sm underline">Create a job seeker account</a>
            `;
            // Add event listener for the switch link
            const switchLink = document.getElementById('switchToJobSeekerLink');
            if (switchLink) {
                switchLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (currentTab === 'signup') {
                        updateUserType('job_seeker');
                    } else {
                        updateLoginUserType('job_seeker');
                    }
                });
            }
        }
    }
}

// Open modal function (global)
window.openAuthModal = function(tab = 'login') {
    const elements = getModalElements();
    if (!elements.authModal) {
        console.warn('Auth modal not found in DOM.');
        return;
    }
    currentTab = tab;
    currentStep = 0;
    elements.authModal.classList.remove('hidden');
    elements.authModal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    switchTab(tab);
};

// Wrap the rest in IIFE
(function() {
    function closeModal() {
        const elements = getModalElements();
        if (!elements.authModal) return;
        elements.authModal.classList.add('hidden');
        elements.authModal.classList.remove('flex');
        document.body.style.overflow = '';
        if (elements.loginForm) elements.loginForm.reset();
        if (elements.registerForm) elements.registerForm.reset();
        currentTab = 'login';
        currentUserType = 'job_seeker';
        currentStep = 0;
        // Reset password strength
        const strengthDiv = document.getElementById('passwordStrength');
        if (strengthDiv) strengthDiv.classList.add('hidden');
        const matchError = document.getElementById('passwordMatchError');
        if (matchError) matchError.classList.add('hidden');
        const errorDiv = document.getElementById('registerErrorMessage');
        if (errorDiv) errorDiv.classList.add('hidden');
        // Reset business certificate upload
        const certInput = document.getElementById('businessCertInput');
        const certPlaceholder = document.getElementById('businessCertPlaceholder');
        const certPreview = document.getElementById('businessCertPreview');
        const certError = document.getElementById('businessCertError');
        if (certInput) certInput.value = '';
        if (certPlaceholder) certPlaceholder.classList.remove('hidden');
        if (certPreview) certPreview.classList.add('hidden');
        if (certError) certError.classList.add('hidden');
        switchTab('login');
        updateLoginUserType('job_seeker');
        updateRightPanel('job_seeker');
    }

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
            loginJobSeekerBtn.querySelector('svg')?.classList.remove('text-gray-600');
            loginJobSeekerBtn.querySelector('svg')?.classList.add('text-blue-600');
            loginEmployerBtn.classList.remove('border-blue-500', 'bg-blue-50');
            loginEmployerBtn.classList.add('border-gray-200', 'bg-white');
            loginEmployerBtn.querySelector('svg')?.classList.remove('text-blue-600');
            loginEmployerBtn.querySelector('svg')?.classList.add('text-gray-600');
        } else {
            loginJobSeekerBtn.classList.remove('border-blue-500', 'bg-blue-50');
            loginJobSeekerBtn.classList.add('border-gray-200', 'bg-white');
            loginJobSeekerBtn.querySelector('svg')?.classList.remove('text-blue-600');
            loginJobSeekerBtn.querySelector('svg')?.classList.add('text-gray-600');
            loginEmployerBtn.classList.add('border-blue-500', 'bg-blue-50');
            loginEmployerBtn.classList.remove('border-gray-200', 'bg-white');
            loginEmployerBtn.querySelector('svg')?.classList.remove('text-gray-600');
            loginEmployerBtn.querySelector('svg')?.classList.add('text-blue-600');
        }
        if (currentTab === 'login') updateRightPanel(userType);
    }

    function updateUserType(userType) {
        const elements = getModalElements();
        if (!elements.authModal) return;
        currentUserType = userType;
        if (elements.registerUserType) elements.registerUserType.value = userType;
        
        const jobSeekerBtn = elements.jobSeekerBtn;
        const employerBtn = elements.employerBtn;

        if (userType === 'job_seeker') {
            if (jobSeekerBtn) {
                jobSeekerBtn.classList.add('border-blue-500', 'bg-blue-50');
                jobSeekerBtn.classList.remove('border-gray-200', 'bg-white');
                jobSeekerBtn.querySelector('svg')?.classList.remove('text-gray-600');
                jobSeekerBtn.querySelector('svg')?.classList.add('text-blue-600');
            }
            if (employerBtn) {
                employerBtn.classList.remove('border-blue-500', 'bg-blue-50');
                employerBtn.classList.add('border-gray-200', 'bg-white');
                employerBtn.querySelector('svg')?.classList.remove('text-blue-600');
                employerBtn.querySelector('svg')?.classList.add('text-gray-600');
            }
        } else {
            if (jobSeekerBtn) {
                jobSeekerBtn.classList.remove('border-blue-500', 'bg-blue-50');
                jobSeekerBtn.classList.add('border-gray-200', 'bg-white');
                jobSeekerBtn.querySelector('svg')?.classList.remove('text-blue-600');
                jobSeekerBtn.querySelector('svg')?.classList.add('text-gray-600');
            }
            if (employerBtn) {
                employerBtn.classList.add('border-blue-500', 'bg-blue-50');
                employerBtn.classList.remove('border-gray-200', 'bg-white');
                employerBtn.querySelector('svg')?.classList.remove('text-gray-600');
                employerBtn.querySelector('svg')?.classList.add('text-blue-600');
            }
        }
        if (currentTab === 'signup') updateRightPanel(userType);
    }

    // Password toggles (event delegation — survives Livewire / DOM remounts)
    function setupPasswordToggles() {
        if (document.documentElement.dataset.passwordTogglesBound === 'true') return;
        document.documentElement.dataset.passwordTogglesBound = 'true';

        document.addEventListener('click', function (e) {
            const toggle = e.target.closest('[data-toggle-password]');
            if (!toggle) return;

            e.preventDefault();
            e.stopPropagation();

            const inputId = toggle.getAttribute('data-toggle-password');
            const input = inputId ? document.getElementById(inputId) : null;
            if (!input) return;

            const showing = input.type === 'password';
            input.type = showing ? 'text' : 'password';
            toggle.setAttribute('aria-label', showing ? 'Hide password' : 'Show password');

            toggle.querySelectorAll('.eye-open').forEach((el) => el.classList.toggle('hidden', showing));
            toggle.querySelectorAll('.eye-closed').forEach((el) => el.classList.toggle('hidden', !showing));
        });
    }

    // Setup password validation listeners
    function setupPasswordValidation() {
        const passwordInput = document.getElementById('registerPassword');
        const confirmInput = document.getElementById('password_confirmation');
        const matchError = document.getElementById('passwordMatchError');

        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                updatePasswordStrengthUI(this.value);
                if (confirmInput && confirmInput.value) {
                    if (this.value !== confirmInput.value) {
                        if (matchError) matchError.classList.remove('hidden');
                    } else {
                        if (matchError) matchError.classList.add('hidden');
                    }
                }
            });
        }
        if (confirmInput) {
            confirmInput.addEventListener('input', function() {
                if (passwordInput && this.value) {
                    if (passwordInput.value !== this.value) {
                        if (matchError) matchError.classList.remove('hidden');
                    } else {
                        if (matchError) matchError.classList.add('hidden');
                    }
                }
            });
        }
    }

    // Login form
    function setupLoginForm() {
        const loginForm = document.getElementById('loginForm');
        if (!loginForm) return;
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const errorDiv = document.getElementById('loginErrorMessage');
            const loginBtn = document.getElementById('loginBtn');
            const loginBtnText = document.getElementById('loginBtnText');
            const loginBtnSpinner = document.getElementById('loginBtnSpinner');
            
            errorDiv.classList.add('hidden');
            loginBtn.disabled = true;
            loginBtnText.textContent = 'Signing in...';
            loginBtnSpinner.classList.remove('hidden');
            
            try {
                const userType = document.getElementById('loginUserType')?.value || 'job_seeker';
                const response = await fetch('/web/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify({ email: formData.get('email'), password: formData.get('password'), user_type: userType })
                });
                const data = await response.json();
                if (response.ok && data.success) {
                    loginBtnText.textContent = 'Success! Redirecting...';
                    const intended = typeof sessionStorage !== 'undefined' && sessionStorage.getItem('loginRedirect');
                    const redirectUrl = intended || data.redirect || '/dashboard';
                    if (intended) sessionStorage.removeItem('loginRedirect');
                    setTimeout(() => { window.location.href = redirectUrl; }, 300);
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

    // Submit registration to backend
    async function submitRegistration() {
        const errorDiv = document.getElementById('registerErrorMessage');
        const continueBtn = document.getElementById('continueBtn');
        const continueBtnText = document.getElementById('continueBtnText');
        const registerBtnSpinner = document.getElementById('registerBtnSpinner');
        const continueBtnArrow = document.getElementById('continueBtnArrow');
        
        errorDiv.classList.add('hidden');
        continueBtn.disabled = true;
        continueBtnText.textContent = 'Creating account...';
        if (continueBtnArrow) continueBtnArrow.classList.add('hidden');
        registerBtnSpinner.classList.remove('hidden');
        
        try {
            // Use FormData to support file upload (business certificate)
            const formData = new FormData();
            formData.append('user_type', currentUserType);
            formData.append('email', document.getElementById('registerEmail')?.value || '');
            formData.append('phone', document.getElementById('registerContactNumber')?.value || '');
            formData.append('password', document.getElementById('registerPassword')?.value || '');
            formData.append('password_confirmation', document.getElementById('password_confirmation')?.value || '');
            
            // Always send first_name and last_name (shared Step 1 fields)
            formData.append('first_name', document.getElementById('registerFirstName')?.value || '');
            formData.append('last_name', document.getElementById('registerSurname')?.value || '');

            if (currentUserType === 'job_seeker') {
                formData.append('date_of_birth', document.getElementById('registerDob')?.value || '');
                formData.append('gender', document.getElementById('registerGender')?.value || '');
                formData.append('employment_status', document.getElementById('registerEmploymentStatus')?.value || '');
                formData.append('highest_education', document.getElementById('registerHighestDegree')?.value || '');
            } else {
                formData.append('company_name', document.getElementById('company_name')?.value || '');
                formData.append('industry', document.getElementById('industry')?.value || '');
                formData.append('company_size', document.getElementById('company_size')?.value || '');
                formData.append('website', document.getElementById('website')?.value || '');
                
                // Append business certificate file
                const certInput = document.getElementById('businessCertInput');
                if (certInput && certInput.files && certInput.files.length > 0) {
                    formData.append('business_certificate', certInput.files[0]);
                }
            }
            
            const response = await fetch('/api/auth/register', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok) {
                continueBtnText.textContent = 'Success! Redirecting...';
                const userType = data.user?.user_type || data.user_type;
                const redirect = userType === 'employer' ? '/employer/dashboard' : '/dashboard';
                setTimeout(() => { window.location.href = redirect; }, 500);
            } else {
                continueBtn.disabled = false;
                continueBtnText.textContent = 'Create Account';
                registerBtnSpinner.classList.add('hidden');
                const errorMsg = data.errors ? Object.values(data.errors).flat().join(', ') : data.message || 'Registration failed';
                errorDiv.textContent = errorMsg;
                errorDiv.classList.remove('hidden');
            }
        } catch (error) {
            continueBtn.disabled = false;
            continueBtnText.textContent = 'Create Account';
            registerBtnSpinner.classList.add('hidden');
            errorDiv.textContent = 'An error occurred. Please try again.';
            errorDiv.classList.remove('hidden');
        }
    }

    // Initialize
    function init() {
        const elements = getModalElements();
        if (!elements.authModal) {
            setTimeout(init, 100);
            return;
        }
        isInitialized = true;
        
        // Close modal
        if (elements.closeAuthModal) elements.closeAuthModal.addEventListener('click', closeModal);
        elements.authModal.addEventListener('click', function(e) { if (e.target === elements.authModal) closeModal(); });
        
        // Tab switching
        if (elements.loginTab) elements.loginTab.addEventListener('click', () => switchTab('login'));
        if (elements.signUpTab) elements.signUpTab.addEventListener('click', () => switchTab('signup'));
        
        // User type for signup
        if (elements.jobSeekerBtn) elements.jobSeekerBtn.addEventListener('click', () => updateUserType('job_seeker'));
        if (elements.employerBtn) elements.employerBtn.addEventListener('click', () => updateUserType('employer'));
        
        // User type for login
        const loginJobSeekerBtn = document.getElementById('loginJobSeekerBtn');
        const loginEmployerBtn = document.getElementById('loginEmployerBtn');
        if (loginJobSeekerBtn) loginJobSeekerBtn.addEventListener('click', () => updateLoginUserType('job_seeker'));
        if (loginEmployerBtn) loginEmployerBtn.addEventListener('click', () => updateLoginUserType('employer'));
        
        // Continue to Registration (from role selection)
        const continueToRegistrationBtn = document.getElementById('continueToRegistrationBtn');
        if (continueToRegistrationBtn) {
            continueToRegistrationBtn.addEventListener('click', function() {
                showStep(1);
            });
        }
        
        // Continue button (multi-step)
        const continueBtn = document.getElementById('continueBtn');
        if (continueBtn) {
            continueBtn.addEventListener('click', function() {
                if (validateStep(currentStep)) {
                    if (currentStep < 3) {
                        showStep(currentStep + 1);
                    } else {
                        // Step 3 validated => submit
                        submitRegistration();
                    }
                }
            });
        }
        
        // Back button (multi-step)
        const backBtn = document.getElementById('backBtn');
        if (backBtn) {
            backBtn.addEventListener('click', function() {
                const errorDiv = document.getElementById('registerErrorMessage');
                if (errorDiv) errorDiv.classList.add('hidden');
                if (currentStep > 0) {
                    showStep(currentStep - 1);
                }
            });
        }
        
        // Switch to employer link
        const switchToEmployerLink = document.getElementById('switchToEmployerLink');
        if (switchToEmployerLink) {
            switchToEmployerLink.addEventListener('click', function(e) {
                e.preventDefault();
                updateUserType('employer');
            });
        }
        
        // Setup forms
        setupPasswordToggles();
        setupPasswordValidation();
        setupLoginForm();
        setupBusinessCertUpload();
        
        // Initialize defaults
        updateLoginUserType('job_seeker');
        updateRightPanel('job_seeker');
    }

    // Business Certificate file upload handling
    function setupBusinessCertUpload() {
        const dropZone = document.getElementById('businessCertDropZone');
        const fileInput = document.getElementById('businessCertInput');
        const placeholder = document.getElementById('businessCertPlaceholder');
        const preview = document.getElementById('businessCertPreview');
        const fileName = document.getElementById('businessCertFileName');
        const fileSize = document.getElementById('businessCertFileSize');
        const removeBtn = document.getElementById('businessCertRemove');
        const certError = document.getElementById('businessCertError');
        
        if (!dropZone || !fileInput) return;
        
        // Click to upload
        dropZone.addEventListener('click', function(e) {
            if (e.target.closest('#businessCertRemove')) return;
            fileInput.click();
        });
        
        // File selected
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                showFilePreview(this.files[0]);
            }
        });
        
        // Drag & drop
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('border-blue-400', 'bg-blue-50');
        });
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        });
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
            const files = e.dataTransfer.files;
            if (files && files.length > 0) {
                const dt = new DataTransfer();
                dt.items.add(files[0]);
                fileInput.files = dt.files;
                showFilePreview(files[0]);
            }
        });
        
        // Remove file
        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.value = '';
                if (placeholder) placeholder.classList.remove('hidden');
                if (preview) preview.classList.add('hidden');
                if (certError) certError.classList.add('hidden');
            });
        }
        
        function showFilePreview(file) {
            const maxSize = 5 * 1024 * 1024;
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            
            if (file.size > maxSize) {
                showRegError('File must be less than 5MB');
                fileInput.value = '';
                return;
            }
            if (!allowedTypes.includes(file.type)) {
                showRegError('Only PDF, JPG, and PNG files are allowed');
                fileInput.value = '';
                return;
            }
            
            if (placeholder) placeholder.classList.add('hidden');
            if (preview) preview.classList.remove('hidden');
            if (fileName) fileName.textContent = file.name;
            if (fileSize) {
                const sizeKB = (file.size / 1024).toFixed(1);
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                fileSize.textContent = file.size > 1024 * 1024 ? `${sizeMB} MB` : `${sizeKB} KB`;
            }
            if (certError) certError.classList.add('hidden');
            const errorDiv = document.getElementById('registerErrorMessage');
            if (errorDiv) errorDiv.classList.add('hidden');
        }
    }

    // Bind password toggles immediately (delegation does not need modal in DOM yet)
    setupPasswordToggles();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
