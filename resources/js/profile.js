// Profile page JavaScript - loaded globally for wire:navigate compatibility
// All functions are attached to window so onclick handlers in HTML work

(function() {
    const API_BASE = '/api';
    let editMode = false;
    let profileData = {};
    let experiences = [];
    let editingExperienceId = null;
    let educations = [];
    let editingEducationId = null;
    let skills = [];
    let languages = [];
    let certifications = [];
    let editingCertificationId = null;
    let references = [];
    let editingReferenceId = null;
    let categoryPreferences = [];
    let allCategories = [];

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    // ========== Button Loading Helper ==========
    function setButtonLoading(button, isLoading, loadingText = '', originalText = null) {
        if (!button) return;

        if (originalText === null) {
            originalText = button.innerHTML;
            button.dataset.originalContent = originalText;
            button.dataset.originalClasses = button.className;
        } else {
            button.dataset.originalContent = originalText;
            button.dataset.originalClasses = button.className;
        }

        if (isLoading) {
            button.disabled = true;
            button.classList.add('opacity-75', 'cursor-not-allowed', 'relative');

            if (!button.dataset.originalWidth) {
                button.dataset.originalWidth = button.offsetWidth + 'px';
            }
            button.style.minWidth = button.dataset.originalWidth;

            button.innerHTML = `
                <div class="flex items-center justify-center">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            `;
        } else {
            button.disabled = false;
            button.classList.remove('opacity-75', 'cursor-not-allowed', 'relative');

            const restored = button.dataset.originalContent || originalText;
            if (restored) {
                button.innerHTML = restored;
            }

            if (button.dataset.originalClasses) {
                button.className = button.dataset.originalClasses;
            }

            if (button.dataset.originalWidth) {
                button.style.minWidth = '';
                delete button.dataset.originalWidth;
            }
        }
    }

    // ========== Event Listeners Setup ==========
    function setupEventListeners() {
        const photoInput = document.getElementById('profile_photo_file');
        if (photoInput) {
            photoInput.removeEventListener('change', handleProfilePhotoUpload);
            photoInput.addEventListener('change', handleProfilePhotoUpload);
        }

        const cvInput = document.getElementById('cv_file');
        if (cvInput) {
            cvInput.removeEventListener('change', handleCvUpload);
            cvInput.addEventListener('change', handleCvUpload);
        }

        const dobInput = document.getElementById('date_of_birth');
        if (dobInput) {
            dobInput.removeEventListener('change', calculateAge);
            dobInput.addEventListener('change', calculateAge);
        }

        const bioInput = document.getElementById('bio');
        if (bioInput) {
            bioInput.addEventListener('input', function() {
                const counter = document.getElementById('bio-char-count');
                if (counter) counter.textContent = this.value.length;
            });
        }

        const personalInfoForm = document.getElementById('personalInfoForm');
        if (personalInfoForm) {
            personalInfoForm.removeEventListener('submit', savePersonalInfo);
            personalInfoForm.addEventListener('submit', savePersonalInfo);
        }

        // User menu dropdown is handled by the navbar script in job-seeker-navbar.blade.php
        // No need to duplicate the handler here - it causes conflicts

        const categorySelect = document.getElementById('category-select');
        if (categorySelect) {
            categorySelect.removeEventListener('change', onCategorySelectChange);
            categorySelect.addEventListener('change', onCategorySelectChange);
        }
    }

    function onCategorySelectChange() {
        if (this.value) {
            addCategoryPreference();
        }
    }

    // ========== Profile Loading ==========
    async function loadProfileData() {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            });

            if (response.status === 401 || response.status === 403) {
                window.location.href = '/login';
                return;
            }

            if (response.ok) {
                const data = await response.json();
                profileData = data.data || data.job_seeker || {};

                const profileContent = document.querySelector('main');
                if (profileContent) {
                    profileContent.setAttribute('data-profile-loaded', 'true');
                }

                updateProfileHeader();
                updatePersonalInfoForm();
                updateAboutSection();
                updateJobPreferences();
                updateDocuments();
                updateSocialLinks();
                updateVisibility();
            }
        } catch (error) {
            console.error('Error loading profile:', error);
        }
    }

    // Full load: profile + all sections
    async function loadProfile() {
        await loadProfileData();
        await Promise.all([
            loadExperiences(),
            loadEducations(),
            loadSkills(),
            loadLanguages(),
            loadCertifications(),
            loadReferences(),
            loadCategoryPreferences()
        ]);
        updateHobbies();
        // Update header title after experiences are loaded
        updateProfileHeader();
        // Update profile strength after all data is loaded
        updateProfileStrength();
    }

    // ========== Profile Update Functions ==========
    function updateProfileHeader() {
        const firstName = profileData.first_name || '';
        const lastName = profileData.last_name || '';
        const fullName = `${firstName} ${lastName}`.trim() || 'User';
        const initials = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase() || 'U';

        const headerName = document.getElementById('profile-header-name');
        const headerInitials = document.getElementById('profile-header-initials');
        const userInitials = document.getElementById('user-initials');
        const headerTitle = document.getElementById('profile-header-title');
        
        if (headerName) headerName.textContent = fullName;
        if (headerInitials) headerInitials.textContent = initials;
        if (userInitials) userInitials.textContent = initials;

        // Set title from most recent/current experience
        if (headerTitle) {
            let title = '';
            if (experiences && experiences.length > 0) {
                // Find current job first, otherwise most recent
                const currentJob = experiences.find(exp => exp.is_current);
                const mostRecent = experiences.sort((a, b) => new Date(b.start_date) - new Date(a.start_date))[0];
                title = (currentJob || mostRecent)?.job_title || '';
            }
            headerTitle.textContent = title || 'Job Seeker';
        }

        if (profileData.profile_photo) {
            const img = document.getElementById('profile-header-photo-img');
            const placeholder = document.getElementById('profile-header-photo');
            if (img) {
                img.src = profileData.profile_photo;
                img.classList.remove('hidden');
                // Handle image load errors (e.g., server down)
                img.onerror = function() {
                    console.error('Failed to load profile photo:', profileData.profile_photo);
                    // Hide image and show placeholder on error
                    img.classList.add('hidden');
                    if (placeholder) placeholder.classList.remove('hidden');
                };
            }
            if (placeholder) placeholder.classList.add('hidden');
        }
    }

    function updatePersonalInfoForm() {
        // Helper to format date for input fields (YYYY-MM-DD)
        const formatDateForInput = (dateValue) => {
            if (!dateValue) return '';
            // If it's already in YYYY-MM-DD format, return as is
            if (typeof dateValue === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(dateValue)) {
                return dateValue;
            }
            // If it's a date string, parse and format it
            try {
                const date = new Date(dateValue);
                if (isNaN(date.getTime())) return '';
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            } catch (e) {
                return '';
            }
        };

        const fields = {
            'first_name': profileData.first_name || '',
            'last_name': profileData.last_name || '',
            'email': profileData.user?.email || '',
            'phone': profileData.phone || '',
            'address': profileData.address || '',
            'gender': profileData.gender || '',
            'date_of_birth': formatDateForInput(profileData.date_of_birth),
            'employment_status': profileData.employment_status || '',
            'highest_education': profileData.highest_education || '',
            'driving_license': profileData.driving_license ? '1' : '0',
            'license_issued_date': formatDateForInput(profileData.license_issued_date)
        };

        for (const [id, value] of Object.entries(fields)) {
            const el = document.getElementById(id);
            if (el) el.value = value;
        }

        if (profileData.date_of_birth) {
            calculateAge();
        }
    }

    function updateAboutSection() {
        const bio = profileData.bio || '';
        const bioEl = document.getElementById('bio');
        const charCount = document.getElementById('bio-char-count');
        const aboutText = document.getElementById('about-text');
        if (bioEl) bioEl.value = bio;
        if (charCount) charCount.textContent = bio.length;
        if (aboutText) aboutText.textContent = bio || 'No bio added yet.';
    }

    function updateJobPreferences() {
        const preferences = profileData.job_preferences || [];
        const displayDiv = document.getElementById('job-preferences-display');
        if (!displayDiv) return;

        if (preferences.length === 0) {
            displayDiv.innerHTML = '<span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm">No preferences set</span>';
        } else {
            displayDiv.innerHTML = preferences.map(pref => {
                const labels = {
                    'full_time': { text: 'Full Time', class: 'bg-blue-100 text-blue-700' },
                    'part_time': { text: 'Part Time', class: 'bg-gray-100 text-gray-700' },
                    'contract': { text: 'Contract', class: 'bg-green-100 text-green-700' }
                };
                const label = labels[pref] || { text: pref, class: 'bg-gray-100 text-gray-700' };
                return `<span class="px-4 py-2 ${label.class} rounded-full text-sm">${label.text}</span>`;
            }).join('');
        }
    }

    function updateDocuments() {
        const cvDocumentItem = document.getElementById('cv-document-item');
        if (!cvDocumentItem) return;

        if (profileData.cv_file_path) {
            const filename = profileData.cv_file_path.split('/').pop() || 'Resume.pdf';
            const updatedDate = profileData.cv_uploaded_at ? new Date(profileData.cv_uploaded_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'Recently';
            const fnEl = document.getElementById('cv-filename');
            const updEl = document.getElementById('cv-updated');
            const linkEl = document.getElementById('cv-view-link');
            if (fnEl) {
                fnEl.textContent = filename;
                fnEl.title = filename; // Show full filename on hover
            }
            if (updEl) updEl.textContent = `Updated ${updatedDate}`;
            if (linkEl) linkEl.href = profileData.cv_file_path;
            cvDocumentItem.classList.remove('hidden');
        } else {
            cvDocumentItem.classList.add('hidden');
        }
    }

    function updateSocialLinks() {
        const linkedinEl = document.getElementById('linkedin_url');
        const websiteEl = document.getElementById('website_url');
        if (linkedinEl) linkedinEl.value = profileData.linkedin_url || '';
        if (websiteEl) websiteEl.value = profileData.website_url || '';
    }

    function updateVisibility() {
        const publicEl = document.getElementById('public_profile');
        const openEl = document.getElementById('open_to_opportunities');
        if (publicEl) publicEl.checked = profileData.public_profile !== false;
        if (openEl) openEl.checked = profileData.open_to_opportunities !== false;
    }

    async function updateProfileStrength() {
        // Ensure we have the latest data by checking DOM or reloading if needed
        // Calculate profile completeness based on actual data
        const items = [];

        // Basic Information
        const basicInfoComplete = !!(profileData.first_name && profileData.last_name && 
                                   (profileData.user?.email || profileData.email) && profileData.phone);
        items.push({
            label: '✓ Basic Info',
            complete: basicInfoComplete,
            status: basicInfoComplete ? 'Done' : 'Pending'
        });

        // Resume Uploaded
        const resumeComplete = !!profileData.cv_file_path;
        items.push({
            label: '✓ Resume',
            complete: resumeComplete,
            status: resumeComplete ? 'Done' : 'Pending'
        });

        // Certifications - check both variable and DOM
        const certsList = document.getElementById('certifications-list');
        const certificationsComplete = (certifications && certifications.length > 0) || 
                                     (certsList && !certsList.innerHTML.includes('No certifications'));
        items.push({
            label: '• Certifications',
            complete: certificationsComplete,
            status: certificationsComplete ? 'Done' : 'Pending'
        });

        // Work Experience - check both variable and DOM
        const expList = document.getElementById('experiences-list');
        const experienceComplete = (experiences && experiences.length > 0) || 
                                 (expList && !expList.innerHTML.includes('No work experience'));
        items.push({
            label: '• Work Experience',
            complete: experienceComplete,
            status: experienceComplete ? 'Done' : 'Pending'
        });

        // Education - check both variable and DOM
        const eduList = document.getElementById('educations-list');
        const educationComplete = (educations && educations.length > 0) || 
                                (eduList && !eduList.innerHTML.includes('No education'));
        items.push({
            label: '• Education',
            complete: educationComplete,
            status: educationComplete ? 'Done' : 'Pending'
        });

        // Skills - check both variable and DOM
        const skillsList = document.getElementById('skills-list');
        const skillsComplete = (skills && skills.length > 0) || 
                             (skillsList && !skillsList.innerHTML.includes('No skills'));
        items.push({
            label: '• Skills',
            complete: skillsComplete,
            status: skillsComplete ? 'Done' : 'Pending'
        });

        // Calculate percentage
        const completedCount = items.filter(item => item.complete).length;
        const totalCount = items.length;
        const percentage = totalCount > 0 ? Math.round((completedCount / totalCount) * 100) : 0;

        // Update percentage display
        const percentEl = document.getElementById('profile-strength-percent');
        if (percentEl) percentEl.textContent = percentage + '%';

        // Update circular progress
        const progressCircle = document.getElementById('profile-strength-progress');
        if (progressCircle) {
            const circumference = 2 * Math.PI * 56; // radius is 56
            const offset = circumference - (percentage / 100) * circumference;
            progressCircle.style.strokeDashoffset = offset;
            // Set initial stroke-dasharray if not set
            if (!progressCircle.style.strokeDasharray) {
                progressCircle.style.strokeDasharray = circumference;
            }
        }

        // Update items list
        const itemsContainer = document.getElementById('profile-strength-items');
        if (itemsContainer) {
            itemsContainer.innerHTML = items.map(item => `
                <div class="flex items-center justify-between text-sm">
                    <span class="${item.complete ? 'text-gray-600' : 'text-gray-600'}">${item.label}</span>
                    <span class="${item.complete ? 'text-green-600' : 'text-orange-600'} font-medium">${item.status}</span>
                </div>
            `).join('');
        }
    }

    function calculateAge() {
        const dobEl = document.getElementById('date_of_birth');
        const ageDisplay = document.getElementById('age-display');
        if (!dobEl || !ageDisplay) return;

        const dob = dobEl.value;
        if (dob) {
            const birthDate = new Date(dob);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            const formattedDate = birthDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            ageDisplay.textContent = `${formattedDate} (${age} years)`;
        } else {
            ageDisplay.textContent = '';
        }
    }

    // ========== File Upload Handlers ==========
    async function handleProfilePhotoUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
            showErrorToast('File size must be less than 5MB');
            e.target.value = ''; // Clear the input
            return;
        }

        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            showErrorToast('Please upload a valid image file (JPEG, PNG, or WebP)');
            e.target.value = ''; // Clear the input
            return;
        }

        const uploadButton = document.getElementById('profile-photo-upload-btn');
        const uploadOverlay = document.getElementById('profile-photo-upload-overlay');
        const previewOverlay = document.getElementById('profile-photo-preview-overlay');
        const previewImg = document.getElementById('profile-photo-preview-img');
        const cameraIcon = document.getElementById('profile-photo-camera-icon');
        const loadingIcon = document.getElementById('profile-photo-loading-icon');

        // Show preview immediately
        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewImg) {
                previewImg.src = e.target.result;
                if (previewOverlay) previewOverlay.classList.remove('hidden');
            }
        };
        reader.readAsDataURL(file);

        const formData = new FormData();
        formData.append('profile_photo', file);

        try {
            // Show loading state
            if (uploadOverlay) uploadOverlay.classList.remove('hidden');
            if (cameraIcon) cameraIcon.classList.add('hidden');
            if (loadingIcon) loadingIcon.classList.remove('hidden');
            if (uploadButton) uploadButton.disabled = true;
            if (uploadButton) uploadButton.classList.add('opacity-75', 'cursor-not-allowed');

            const response = await fetch(`${API_BASE}/job-seeker/profile/photo`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: formData
            });

            const data = await response.json();
            if (response.ok) {
                // Update only profile photo from response (instant)
                profileData.profile_photo = data.data?.profile_photo || data.job_seeker?.profile_photo;
                updateProfileHeader();
                showSuccessToast('Profile photo uploaded successfully!');
            } else {
                showErrorToast(data.message || 'Failed to upload photo');
                // Hide preview on error
                if (previewOverlay) previewOverlay.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error uploading photo:', error);
            showErrorToast('An error occurred while uploading');
            // Hide preview on error
            if (previewOverlay) previewOverlay.classList.add('hidden');
        } finally {
            // Hide loading state
            if (uploadOverlay) uploadOverlay.classList.add('hidden');
            if (cameraIcon) cameraIcon.classList.remove('hidden');
            if (loadingIcon) loadingIcon.classList.add('hidden');
            if (uploadButton) uploadButton.disabled = false;
            if (uploadButton) uploadButton.classList.remove('opacity-75', 'cursor-not-allowed');
            // Hide preview overlay (actual photo will show via updateProfileHeader)
            if (previewOverlay) previewOverlay.classList.add('hidden');
            // Clear file input
            e.target.value = '';
        }
    }

    async function handleCvUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        if (file.size > 10 * 1024 * 1024) {
            showErrorToast('File size must be less than 10MB');
            return;
        }

        const uploadButton = document.getElementById('cv-upload-btn');
        const originalText = uploadButton ? uploadButton.innerHTML : '';

        const formData = new FormData();
        formData.append('cv', file);

        try {
            if (uploadButton) setButtonLoading(uploadButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/profile/cv`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: formData
            });

            const data = await response.json();
            if (response.ok) {
                // Update only CV info from response (instant)
                profileData.cv_file_path = data.data?.cv_file_path || data.job_seeker?.cv_file_path;
                profileData.cv_uploaded_at = data.data?.cv_uploaded_at || data.job_seeker?.cv_uploaded_at;
                updateDocuments();
                updateProfileStrength();
            } else {
                showErrorToast(data.message || 'Failed to upload CV');
            }
        } catch (error) {
            console.error('Error uploading CV:', error);
            showErrorToast('An error occurred');
        } finally {
            if (uploadButton) setButtonLoading(uploadButton, false, '', originalText);
        }
    }

    // ========== Save Functions ==========
    async function savePersonalInfo(e) {
        e.preventDefault();
        const submitButton = e.target.querySelector('button[type="submit"]');
        const originalText = submitButton ? submitButton.innerHTML : '';

        // Helper to convert empty strings to null
        const toNullIfEmpty = (value) => value === '' ? null : value;

        const formData = {
            first_name: document.getElementById('first_name').value,
            last_name: document.getElementById('last_name').value,
            phone: toNullIfEmpty(document.getElementById('phone').value),
            address: toNullIfEmpty(document.getElementById('address').value),
            gender: toNullIfEmpty(document.getElementById('gender').value),
            date_of_birth: toNullIfEmpty(document.getElementById('date_of_birth').value),
            employment_status: toNullIfEmpty(document.getElementById('employment_status').value),
            highest_education: toNullIfEmpty(document.getElementById('highest_education').value),
            driving_license: document.getElementById('driving_license').value === '1',
            license_issued_date: toNullIfEmpty(document.getElementById('license_issued_date').value),
        };

        try {
            if (submitButton) setButtonLoading(submitButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            if (response.ok) {
                // Update profileData with the response (includes user relationship)
                profileData = data.data || data.job_seeker || {};
                
                // Only update the personal info form fields (instant, no extra API calls)
                updatePersonalInfoForm();
                
                // Also update header in case name changed
                updateProfileHeader();
                
                // Update profile strength
                updateProfileStrength();
                
                showSuccessToast('Profile updated successfully!');
            } else {
                // Show validation errors if any
                if (data.errors) {
                    const errorMessages = Object.values(data.errors).flat().join(', ');
                    showErrorToast(errorMessages || 'Validation failed');
                } else {
                    showErrorToast(data.message || 'Failed to update profile');
                }
            }
        } catch (error) {
            console.error('Error saving profile:', error);
            showErrorToast('An error occurred');
        } finally {
            if (submitButton) setButtonLoading(submitButton, false, '', originalText);
        }
    }

    function editAbout() {
        const display = document.getElementById('about-display');
        const edit = document.getElementById('about-edit');
        if (display) display.classList.add('hidden');
        if (edit) edit.classList.remove('hidden');
    }

    function cancelAbout() {
        const display = document.getElementById('about-display');
        const edit = document.getElementById('about-edit');
        if (display) display.classList.remove('hidden');
        if (edit) edit.classList.add('hidden');
        const bioEl = document.getElementById('bio');
        if (bioEl) bioEl.value = profileData.bio || '';
    }

    async function saveAbout() {
        const bio = document.getElementById('bio').value;
        const saveButton = document.querySelector('button[onclick="saveAbout()"]');
        const originalText = saveButton ? saveButton.innerHTML : '';

        try {
            if (saveButton) setButtonLoading(saveButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ bio })
            });

            const data = await response.json();
            if (response.ok) {
                // Update only the bio in profileData and refresh about section (instant)
                profileData.bio = bio;
                updateAboutSection();
                cancelAbout();
            } else {
                showErrorToast(data.message || 'Failed to update bio');
            }
        } catch (error) {
            console.error('Error saving bio:', error);
            showErrorToast('An error occurred');
        } finally {
            if (saveButton) setButtonLoading(saveButton, false, '', originalText);
        }
    }

    function editJobPreferences() {
        const display = document.getElementById('job-preferences-display');
        const edit = document.getElementById('job-preferences-edit');
        if (display) display.classList.add('hidden');
        if (edit) edit.classList.remove('hidden');

        const preferences = profileData.job_preferences || [];
        document.querySelectorAll('input[name="job_preference"]').forEach(checkbox => {
            checkbox.checked = preferences.includes(checkbox.value);
        });
    }

    function cancelJobPreferences() {
        const display = document.getElementById('job-preferences-display');
        const edit = document.getElementById('job-preferences-edit');
        if (display) display.classList.remove('hidden');
        if (edit) edit.classList.add('hidden');
    }

    async function saveJobPreferences() {
        const preferences = Array.from(document.querySelectorAll('input[name="job_preference"]:checked')).map(cb => cb.value);
        const saveButton = document.querySelector('button[onclick="saveJobPreferences()"]');
        const originalText = saveButton ? saveButton.innerHTML : '';

        try {
            if (saveButton) setButtonLoading(saveButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ job_preferences: preferences })
            });

            const data = await response.json();
            if (response.ok) {
                // Update only job preferences in profileData and refresh display (instant)
                profileData.job_preferences = preferences;
                updateJobPreferences();
                cancelJobPreferences();
            } else {
                showErrorToast(data.message || 'Failed to update preferences');
            }
        } catch (error) {
            console.error('Error saving preferences:', error);
            showErrorToast('An error occurred');
        } finally {
            if (saveButton) setButtonLoading(saveButton, false, '', originalText);
        }
    }

    async function saveSocialLinks() {
        const linkedin_url = document.getElementById('linkedin_url').value;
        const website_url = document.getElementById('website_url').value;
        const saveButton = document.querySelector('button[onclick="saveSocialLinks()"]');
        const originalText = saveButton ? saveButton.innerHTML : '';

        try {
            if (saveButton) setButtonLoading(saveButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ linkedin_url, website_url })
            });

            const data = await response.json();
            if (response.ok) {
                // Update only social links in profileData and refresh (instant)
                profileData.linkedin_url = linkedin_url;
                profileData.website_url = website_url;
                updateSocialLinks();
                showSuccessToast('Social links saved!');
            } else {
                showErrorToast(data.message || 'Failed to save links');
            }
        } catch (error) {
            console.error('Error saving social links:', error);
            showErrorToast('An error occurred');
        } finally {
            if (saveButton) setButtonLoading(saveButton, false, '', originalText);
        }
    }

    async function saveVisibility() {
        const public_profile = document.getElementById('public_profile').checked;
        const open_to_opportunities = document.getElementById('open_to_opportunities').checked;
        const saveButton = document.querySelector('button[onclick="saveVisibility()"]');
        const originalText = saveButton ? saveButton.innerHTML : '';

        try {
            if (saveButton) setButtonLoading(saveButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ public_profile, open_to_opportunities })
            });

            const data = await response.json();
            if (response.ok) {
                // Update only visibility settings in profileData and refresh (instant)
                profileData.public_profile = public_profile;
                profileData.open_to_opportunities = open_to_opportunities;
                updateVisibility();
                showSuccessToast('Visibility settings saved!');
            } else {
                showErrorToast(data.message || 'Failed to save settings');
            }
        } catch (error) {
            console.error('Error saving visibility:', error);
            showErrorToast('An error occurred');
        } finally {
            if (saveButton) setButtonLoading(saveButton, false, '', originalText);
        }
    }

    function toggleEditMode() {
        editMode = !editMode;
    }

    // ========== Work Experience Functions ==========
    async function loadExperiences() {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/experiences`, {
                credentials: 'include',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }
            });
            if (response.ok) {
                const data = await response.json();
                experiences = data.data || [];
                renderExperiences();
            }
        } catch (error) {
            console.error('Error loading experiences:', error);
        }
    }

    function renderExperiences() {
        const container = document.getElementById('experiences-list');
        const skeleton = document.getElementById('experiences-skeleton');
        if (!container) return;
        
        // Hide skeleton
        if (skeleton) skeleton.classList.add('hidden');
        
        if (experiences.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-sm">No work experience added yet.</p>';
            return;
        }
        container.innerHTML = experiences.map(exp => {
            const startDate = new Date(exp.start_date).toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            const endDate = exp.is_current ? 'Present' : (exp.end_date ? new Date(exp.end_date).toLocaleDateString('en-US', { month: 'short', year: 'numeric' }) : '');
            const currentBadge = exp.is_current ? '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium ml-2">Current</span>' : '';
            return `
                <div class="flex items-start space-x-3 border-l-4 border-blue-500 pl-4 py-2">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900">${exp.job_title}</h3>
                                <p class="text-sm text-gray-600">${exp.company_name}${exp.location ? ' • ' + exp.location : ''}</p>
                                <p class="text-xs text-gray-500 mt-1">${startDate} - ${endDate}${currentBadge}</p>
                                ${exp.description ? `<p class="text-sm text-gray-700 mt-2">${exp.description}</p>` : ''}
                            </div>
                            <button onclick="deleteExperience(${exp.id}, this)" class="text-red-600 hover:text-red-700 ml-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function openExperienceModal(id = null) {
        editingExperienceId = id;
        const exp = id ? experiences.find(e => e.id === id) : null;
        const fields = {
            'exp-job-title': exp?.job_title || '',
            'exp-company': exp?.company_name || '',
            'exp-location': exp?.location || '',
            'exp-start-date': exp?.start_date || '',
            'exp-end-date': exp?.end_date || '',
            'exp-description': exp?.description || ''
        };
        for (const [id, val] of Object.entries(fields)) {
            const el = document.getElementById(id);
            if (el) el.value = val;
        }
        const isCurrent = document.getElementById('exp-is-current');
        if (isCurrent) isCurrent.checked = exp?.is_current || false;
        const modal = document.getElementById('experience-modal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeExperienceModal() {
        const modal = document.getElementById('experience-modal');
        if (modal) modal.classList.add('hidden');
        editingExperienceId = null;
    }

    async function saveExperience() {
        const data = {
            job_title: document.getElementById('exp-job-title').value,
            company_name: document.getElementById('exp-company').value,
            location: document.getElementById('exp-location').value,
            start_date: document.getElementById('exp-start-date').value,
            end_date: document.getElementById('exp-end-date').value || null,
            is_current: document.getElementById('exp-is-current').checked,
            description: document.getElementById('exp-description').value,
        };

        const saveButton = document.querySelector('#experience-modal button[onclick="saveExperience()"]');
        const originalText = saveButton ? saveButton.innerHTML : '';

        try {
            if (saveButton) setButtonLoading(saveButton, true, '', originalText);

            const url = editingExperienceId
                ? `${API_BASE}/job-seeker/experiences/${editingExperienceId}`
                : `${API_BASE}/job-seeker/experiences`;
            const method = editingExperienceId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(data)
            });

            if (response.ok) {
                await loadExperiences();
                closeExperienceModal();
                // Update profile strength after data is loaded
                updateProfileStrength();
            } else {
                const error = await response.json();
                showErrorToast(error.message || 'Failed to save experience');
            }
        } catch (error) {
            console.error('Error saving experience:', error);
            showErrorToast('An error occurred');
        } finally {
            if (saveButton) setButtonLoading(saveButton, false, '', originalText);
        }
    }

    async function deleteExperience(id, buttonElement = null) {
        if (!confirm('Are you sure you want to delete this experience?')) return;

        const deleteButton = buttonElement || document.querySelector(`button[onclick*="deleteExperience(${id})"]`);
        const originalText = deleteButton ? deleteButton.innerHTML : '';

        try {
            if (deleteButton) setButtonLoading(deleteButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/experiences/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (response.ok) {
                await loadExperiences();
                // Update profile strength after data is loaded
                updateProfileStrength();
            } else {
                showErrorToast('Failed to delete experience');
            }
        } catch (error) {
            console.error('Error deleting experience:', error);
            showErrorToast('An error occurred');
        } finally {
            if (deleteButton) setButtonLoading(deleteButton, false, '', originalText);
        }
    }

    // ========== Education Functions ==========
    async function loadEducations() {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/educations`, {
                credentials: 'include',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }
            });
            if (response.ok) {
                const data = await response.json();
                educations = data.data || [];
                renderEducations();
            }
        } catch (error) {
            console.error('Error loading educations:', error);
        }
    }

    function renderEducations() {
        const container = document.getElementById('educations-list');
        const skeleton = document.getElementById('educations-skeleton');
        if (!container) return;
        
        // Hide skeleton
        if (skeleton) skeleton.classList.add('hidden');
        
        if (educations.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-sm">No education added yet.</p>';
            return;
        }
        container.innerHTML = educations.map(edu => {
            const startDate = new Date(edu.start_date).getFullYear();
            const endDate = edu.end_date ? new Date(edu.end_date).getFullYear() : 'Present';
            const gpaText = edu.gpa ? ` GPA: ${edu.gpa}/${edu.gpa_scale || '4.0'}` : '';
            return `
                <div class="flex items-start justify-between border-b border-gray-200 pb-4">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">${edu.degree}</h3>
                        <p class="text-sm text-gray-600">${edu.institution}${edu.location ? ' • ' + edu.location : ''}</p>
                        <p class="text-xs text-gray-500 mt-1">${startDate} - ${endDate}${gpaText}</p>
                    </div>
                    <button onclick="deleteEducation(${edu.id}, this)" class="text-red-600 hover:text-red-700 ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
        }).join('');
    }

    function openEducationModal(id = null) {
        editingEducationId = id;
        const edu = id ? educations.find(e => e.id === id) : null;
        const fields = {
            'edu-degree': edu?.degree || '',
            'edu-institution': edu?.institution || '',
            'edu-location': edu?.location || '',
            'edu-start-date': edu?.start_date || '',
            'edu-end-date': edu?.end_date || '',
            'edu-gpa': edu?.gpa || '',
            'edu-gpa-scale': edu?.gpa_scale || '4.0',
            'edu-description': edu?.description || ''
        };
        for (const [id, val] of Object.entries(fields)) {
            const el = document.getElementById(id);
            if (el) el.value = val;
        }
        const modal = document.getElementById('education-modal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeEducationModal() {
        const modal = document.getElementById('education-modal');
        if (modal) modal.classList.add('hidden');
        editingEducationId = null;
    }

    async function saveEducation() {
        const data = {
            degree: document.getElementById('edu-degree').value,
            institution: document.getElementById('edu-institution').value,
            location: document.getElementById('edu-location').value,
            start_date: document.getElementById('edu-start-date').value,
            end_date: document.getElementById('edu-end-date').value || null,
            gpa: document.getElementById('edu-gpa').value || null,
            gpa_scale: document.getElementById('edu-gpa-scale').value || null,
            description: document.getElementById('edu-description').value,
        };

        const saveButton = document.querySelector('#education-modal button[onclick="saveEducation()"]');
        const originalText = saveButton ? saveButton.innerHTML : '';

        try {
            if (saveButton) setButtonLoading(saveButton, true, '', originalText);

            const url = editingEducationId
                ? `${API_BASE}/job-seeker/educations/${editingEducationId}`
                : `${API_BASE}/job-seeker/educations`;
            const method = editingEducationId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(data)
            });

            if (response.ok) {
                await loadEducations();
                closeEducationModal();
                // Update profile strength after data is loaded
                updateProfileStrength();
            } else {
                const error = await response.json();
                showErrorToast(error.message || 'Failed to save education');
            }
        } catch (error) {
            console.error('Error saving education:', error);
            showErrorToast('An error occurred');
        } finally {
            if (saveButton) setButtonLoading(saveButton, false, '', originalText);
        }
    }

    async function deleteEducation(id, buttonElement = null) {
        if (!confirm('Are you sure you want to delete this education?')) return;

        const deleteButton = buttonElement || document.querySelector(`button[onclick*="deleteEducation(${id})"]`);
        const originalText = deleteButton ? deleteButton.innerHTML : '';

        try {
            if (deleteButton) setButtonLoading(deleteButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/educations/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (response.ok) {
                await loadEducations();
                // Update profile strength after data is loaded
                updateProfileStrength();
            } else {
                showErrorToast('Failed to delete education');
            }
        } catch (error) {
            console.error('Error deleting education:', error);
            showErrorToast('An error occurred');
        } finally {
            if (deleteButton) setButtonLoading(deleteButton, false, '', originalText);
        }
    }

    // ========== Skills Functions ==========
    async function loadSkills() {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/skills`, {
                credentials: 'include',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }
            });
            if (response.ok) {
                const data = await response.json();
                skills = data.data || [];
                renderSkills();
            }
        } catch (error) {
            console.error('Error loading skills:', error);
        }
    }

    function renderSkills() {
        const container = document.getElementById('skills-list');
        const skeleton = document.getElementById('skills-skeleton');
        if (!container) return;
        
        // Hide skeleton
        if (skeleton) skeleton.classList.add('hidden');
        
        if (skills.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-sm">No skills added yet.</p>';
            return;
        }
        const proficiencyColors = {
            beginner: 'bg-gray-100 text-gray-700',
            intermediate: 'bg-blue-100 text-blue-700',
            advanced: 'bg-blue-100 text-blue-700',
            expert: 'bg-gray-100 text-gray-700'
        };
        container.innerHTML = skills.map(skill => {
            const colorClass = proficiencyColors[skill.proficiency_level] || 'bg-gray-100 text-gray-700';
            const levelText = skill.proficiency_level.charAt(0).toUpperCase() + skill.proficiency_level.slice(1);
            return `
                <span class="px-4 py-2 ${colorClass} rounded-full text-sm flex items-center space-x-2">
                    <span>${skill.skill_name} ${levelText}</span>
                    <button onclick="deleteSkill(${skill.id}, this)" class="text-red-600 hover:text-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            `;
        }).join('');
    }

    function openSkillModal() {
        const nameEl = document.getElementById('skill-name');
        const profEl = document.getElementById('skill-proficiency');
        if (nameEl) nameEl.value = '';
        if (profEl) profEl.value = 'intermediate';
        const modal = document.getElementById('skill-modal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeSkillModal() {
        const modal = document.getElementById('skill-modal');
        if (modal) modal.classList.add('hidden');
    }

    async function saveSkill() {
        const data = {
            skill_name: document.getElementById('skill-name').value,
            proficiency_level: document.getElementById('skill-proficiency').value,
        };

        const saveButton = document.querySelector('#skill-modal button[onclick="saveSkill()"]');
        const originalText = saveButton ? saveButton.innerHTML : '';

        try {
            if (saveButton) setButtonLoading(saveButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/skills`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(data)
            });

            if (response.ok) {
                await loadSkills();
                closeSkillModal();
                // Update profile strength after data is loaded
                updateProfileStrength();
            } else {
                const error = await response.json();
                showErrorToast(error.message || 'Failed to save skill');
            }
        } catch (error) {
            console.error('Error saving skill:', error);
            showErrorToast('An error occurred');
        } finally {
            if (saveButton) setButtonLoading(saveButton, false, '', originalText);
        }
    }

    async function deleteSkill(id, buttonElement = null) {
        if (!confirm('Are you sure you want to delete this skill?')) return;

        const deleteButton = buttonElement || document.querySelector(`button[onclick*="deleteSkill(${id})"]`);
        const originalText = deleteButton ? deleteButton.innerHTML : '';

        try {
            if (deleteButton) setButtonLoading(deleteButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/skills/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (response.ok) {
                await loadSkills();
                // Update profile strength after data is loaded
                updateProfileStrength();
            } else {
                showErrorToast('Failed to delete skill');
            }
        } catch (error) {
            console.error('Error deleting skill:', error);
            showErrorToast('An error occurred');
        } finally {
            if (deleteButton) setButtonLoading(deleteButton, false, '', originalText);
        }
    }

    // ========== Languages Functions ==========
    async function loadLanguages() {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/languages`, {
                credentials: 'include',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }
            });
            if (response.ok) {
                const data = await response.json();
                languages = data.data || [];
                renderLanguages();
            }
        } catch (error) {
            console.error('Error loading languages:', error);
        }
    }

    function renderLanguages() {
        const container = document.getElementById('languages-list');
        const skeleton = document.getElementById('languages-skeleton');
        if (!container) return;
        
        // Hide skeleton
        if (skeleton) skeleton.classList.add('hidden');
        
        if (languages.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-sm">No languages added yet.</p>';
            return;
        }
        container.innerHTML = languages.map(lang => {
            const levelText = lang.proficiency_level.charAt(0).toUpperCase() + lang.proficiency_level.slice(1);
            return `
                <span class="px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7 2a1 1 0 011 1v1h3a1 1 0 110 2H9.578a18.87 18.87 0 01-1.724 4.78c.29.354.596.696.914 1.026a1 1 0 11-1.44 1.389c-.255-.244-.49-.5-.714-.756H7a1 1 0 110-2H5.834a18.747 18.747 0 01-.22-4H7a1 1 0 011-1V3a1 1 0 011-1zm6 6a1 1 0 01.894.553l2.991 6.491a.869.869 0 01-.02.937 1 1 0 01-1.447.425L15 14.618V17a1 1 0 11-2 0v-2.382l-1.418.708a1 1 0 01-1.447-.425.869.869 0 01-.02-.937l2.99-6.491A1 1 0 0113 8z" clip-rule="evenodd"></path>
                    </svg>
                    <span>${lang.language} ${levelText}</span>
                    <button onclick="deleteLanguage(${lang.id}, this)" class="text-red-600 hover:text-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            `;
        }).join('');
    }

    function openLanguageModal() {
        const nameEl = document.getElementById('language-name');
        const profEl = document.getElementById('language-proficiency');
        if (nameEl) nameEl.value = '';
        if (profEl) profEl.value = 'conversational';
        const modal = document.getElementById('language-modal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeLanguageModal() {
        const modal = document.getElementById('language-modal');
        if (modal) modal.classList.add('hidden');
    }

    async function saveLanguage() {
        const data = {
            language: document.getElementById('language-name').value,
            proficiency_level: document.getElementById('language-proficiency').value,
        };

        const saveButton = document.querySelector('#language-modal button[onclick="saveLanguage()"]');
        const originalText = saveButton ? saveButton.innerHTML : '';

        try {
            if (saveButton) setButtonLoading(saveButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/languages`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(data)
            });

            if (response.ok) {
                await loadLanguages();
                closeLanguageModal();
            } else {
                const error = await response.json();
                showErrorToast(error.message || 'Failed to save language');
            }
        } catch (error) {
            console.error('Error saving language:', error);
            showErrorToast('An error occurred');
        } finally {
            if (saveButton) setButtonLoading(saveButton, false, '', originalText);
        }
    }

    async function deleteLanguage(id, buttonElement = null) {
        if (!confirm('Are you sure you want to delete this language?')) return;

        const deleteButton = buttonElement || document.querySelector(`button[onclick*="deleteLanguage(${id})"]`);
        const originalText = deleteButton ? deleteButton.innerHTML : '';

        try {
            if (deleteButton) setButtonLoading(deleteButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/languages/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (response.ok) {
                await loadLanguages();
            } else {
                showErrorToast('Failed to delete language');
            }
        } catch (error) {
            console.error('Error deleting language:', error);
            showErrorToast('An error occurred');
        } finally {
            if (deleteButton) setButtonLoading(deleteButton, false, '', originalText);
        }
    }

    // ========== Certifications Functions ==========
    async function loadCertifications() {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/certifications`, {
                credentials: 'include',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }
            });
            if (response.ok) {
                const data = await response.json();
                certifications = data.data || [];
                renderCertifications();
            }
        } catch (error) {
            console.error('Error loading certifications:', error);
        }
    }

    function renderCertifications() {
        const container = document.getElementById('certifications-list');
        const skeleton = document.getElementById('certifications-skeleton');
        if (!container) return;
        
        // Hide skeleton
        if (skeleton) skeleton.classList.add('hidden');
        
        if (certifications.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-sm">No certifications added yet.</p>';
            return;
        }
        container.innerHTML = certifications.map(cert => {
            const issueDate = new Date(cert.issue_date).toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            const expiryDate = cert.expiry_date ? new Date(cert.expiry_date).toLocaleDateString('en-US', { month: 'short', year: 'numeric' }) : null;
            return `
                <div class="flex items-start space-x-3 border-b border-gray-200 pb-4">
                    <svg class="w-5 h-5 text-orange-600 mt-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">${cert.certification_name}</h3>
                        <p class="text-sm text-gray-600">${cert.issuing_organization}</p>
                        <p class="text-xs text-gray-500 mt-1">Issued: ${issueDate}${expiryDate ? ' Expires: ' + expiryDate : ''}</p>
                        ${cert.certificate_file_path ? `
                            <div class="flex items-center space-x-3 mt-2">
                                <button data-file-url="${cert.certificate_file_path.replace(/"/g, '&quot;')}" onclick="previewFile('cert', this.dataset.fileUrl)" class="text-blue-600 hover:text-blue-700 text-sm font-medium px-3 py-1 rounded hover:bg-blue-50 transition inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Preview
                                </button>
                                <a href="${cert.certificate_file_path}" target="_blank" download class="text-gray-600 hover:text-gray-700 text-sm font-medium px-3 py-1 rounded hover:bg-gray-100 transition inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        ` : ''}
                    </div>
                    <button onclick="deleteCertification(${cert.id}, this)" class="text-red-600 hover:text-red-700 ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
        }).join('');
    }

    function openCertificationModal(id = null) {
        editingCertificationId = id;
        const cert = id ? certifications.find(c => c.id === id) : null;
        const fields = {
            'cert-name': cert?.certification_name || '',
            'cert-organization': cert?.issuing_organization || '',
            'cert-issue-date': cert?.issue_date || '',
            'cert-expiry-date': cert?.expiry_date || '',
            'cert-credential-id': cert?.credential_id || '',
            'cert-credential-url': cert?.credential_url || ''
        };
        for (const [id, val] of Object.entries(fields)) {
            const el = document.getElementById(id);
            if (el) el.value = val;
        }
        const fileEl = document.getElementById('cert-file');
        if (fileEl) fileEl.value = '';
        const previewDiv = document.getElementById('cert-file-preview');
        if (previewDiv) previewDiv.classList.add('hidden');
        const modal = document.getElementById('certification-modal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeCertificationModal() {
        const modal = document.getElementById('certification-modal');
        if (modal) modal.classList.add('hidden');
        editingCertificationId = null;
    }

    async function saveCertification() {
        const formData = new FormData();
        formData.append('certification_name', document.getElementById('cert-name').value);
        formData.append('issuing_organization', document.getElementById('cert-organization').value);
        formData.append('issue_date', document.getElementById('cert-issue-date').value);
        formData.append('expiry_date', document.getElementById('cert-expiry-date').value || '');
        formData.append('credential_id', document.getElementById('cert-credential-id').value || '');
        formData.append('credential_url', document.getElementById('cert-credential-url').value || '');
        const fileInput = document.getElementById('cert-file');
        if (fileInput && fileInput.files[0]) {
            formData.append('certificate_file', fileInput.files[0]);
        }

        const saveButton = document.querySelector('#certification-modal button[onclick="saveCertification()"]');
        const originalText = saveButton ? saveButton.innerHTML : '';

        try {
            if (saveButton) setButtonLoading(saveButton, true, '', originalText);

            const url = editingCertificationId
                ? `${API_BASE}/job-seeker/certifications/${editingCertificationId}`
                : `${API_BASE}/job-seeker/certifications`;
            const method = editingCertificationId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: formData
            });

            if (response.ok) {
                await loadCertifications();
                closeCertificationModal();
                // Update profile strength after data is loaded
                updateProfileStrength();
            } else {
                const error = await response.json();
                showErrorToast(error.message || 'Failed to save certification');
            }
        } catch (error) {
            console.error('Error saving certification:', error);
            showErrorToast('An error occurred');
        } finally {
            if (saveButton) setButtonLoading(saveButton, false, '', originalText);
        }
    }

    async function deleteCertification(id, buttonElement = null) {
        if (!confirm('Are you sure you want to delete this certification?')) return;

        const deleteButton = buttonElement || document.querySelector(`button[onclick*="deleteCertification(${id})"]`);
        const originalText = deleteButton ? deleteButton.innerHTML : '';

        try {
            if (deleteButton) setButtonLoading(deleteButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/certifications/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (response.ok) {
                await loadCertifications();
                // Update profile strength after data is loaded
                updateProfileStrength();
            } else {
                showErrorToast('Failed to delete certification');
            }
        } catch (error) {
            console.error('Error deleting certification:', error);
            showErrorToast('An error occurred');
        } finally {
            if (deleteButton) setButtonLoading(deleteButton, false, '', originalText);
        }
    }

    // ========== References Functions ==========
    async function loadReferences() {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/references`, {
                credentials: 'include',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }
            });
            if (response.ok) {
                const data = await response.json();
                references = data.data || [];
                renderReferences();
            }
        } catch (error) {
            console.error('Error loading references:', error);
        }
    }

    function renderReferences() {
        const container = document.getElementById('references-list');
        const skeleton = document.getElementById('references-skeleton');
        if (!container) return;
        
        // Hide skeleton
        if (skeleton) skeleton.classList.add('hidden');
        
        if (references.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-sm">No references added yet.</p>';
            return;
        }
        container.innerHTML = references.map(ref => {
            return `
                <div class="flex items-start space-x-3 border-b border-gray-200 pb-4">
                    <svg class="w-5 h-5 text-blue-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">${ref.reference_name}</h3>
                        <p class="text-sm text-gray-600">${ref.title} at ${ref.company}</p>
                        <p class="text-xs text-gray-500 mt-1">${ref.relationship.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                        <div class="flex items-center space-x-4 mt-2 text-xs text-gray-600">
                            <a href="mailto:${ref.email}" class="flex items-center space-x-1 hover:text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>${ref.email}</span>
                            </a>
                            ${ref.phone ? `<a href="tel:${ref.phone}" class="flex items-center space-x-1 hover:text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span>${ref.phone}</span>
                            </a>` : ''}
                        </div>
                    </div>
                    <button onclick="deleteReference(${ref.id}, this)" class="text-red-600 hover:text-red-700 ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
        }).join('');
    }

    function openReferenceModal(id = null) {
        editingReferenceId = id;
        const ref = id ? references.find(r => r.id === id) : null;
        const fields = {
            'ref-name': ref?.reference_name || '',
            'ref-title': ref?.title || '',
            'ref-company': ref?.company || '',
            'ref-relationship': ref?.relationship || 'other',
            'ref-email': ref?.email || '',
            'ref-phone': ref?.phone || '',
            'ref-notes': ref?.notes || ''
        };
        for (const [id, val] of Object.entries(fields)) {
            const el = document.getElementById(id);
            if (el) el.value = val;
        }
        const modal = document.getElementById('reference-modal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeReferenceModal() {
        const modal = document.getElementById('reference-modal');
        if (modal) modal.classList.add('hidden');
        editingReferenceId = null;
    }

    async function saveReference() {
        const data = {
            reference_name: document.getElementById('ref-name').value,
            title: document.getElementById('ref-title').value,
            company: document.getElementById('ref-company').value,
            relationship: document.getElementById('ref-relationship').value,
            email: document.getElementById('ref-email').value,
            phone: document.getElementById('ref-phone').value || null,
            notes: document.getElementById('ref-notes').value || null,
        };

        const saveButton = document.querySelector('#reference-modal button[onclick="saveReference()"]');
        const originalText = saveButton ? saveButton.innerHTML : '';

        try {
            if (saveButton) setButtonLoading(saveButton, true, '', originalText);

            const url = editingReferenceId
                ? `${API_BASE}/job-seeker/references/${editingReferenceId}`
                : `${API_BASE}/job-seeker/references`;
            const method = editingReferenceId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(data)
            });

            if (response.ok) {
                await loadReferences();
                closeReferenceModal();
            } else {
                const error = await response.json();
                showErrorToast(error.message || 'Failed to save reference');
            }
        } catch (error) {
            console.error('Error saving reference:', error);
            showErrorToast('An error occurred');
        } finally {
            if (saveButton) setButtonLoading(saveButton, false, '', originalText);
        }
    }

    async function deleteReference(id, buttonElement = null) {
        if (!confirm('Are you sure you want to delete this reference?')) return;

        const deleteButton = buttonElement || document.querySelector(`button[onclick*="deleteReference(${id})"]`);
        const originalText = deleteButton ? deleteButton.innerHTML : '';

        try {
            if (deleteButton) setButtonLoading(deleteButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/references/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (response.ok) {
                await loadReferences();
            } else {
                showErrorToast('Failed to delete reference');
            }
        } catch (error) {
            console.error('Error deleting reference:', error);
            showErrorToast('An error occurred');
        } finally {
            if (deleteButton) setButtonLoading(deleteButton, false, '', originalText);
        }
    }

    // ========== Category Preferences Functions ==========
    async function loadCategoryPreferences() {
        try {
            const [prefsResponse, catsResponse] = await Promise.all([
                fetch(`${API_BASE}/job-seeker/category-preferences`, {
                    credentials: 'include',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }
                }),
                fetch(`${API_BASE}/categories`, {
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' }
                })
            ]);

            if (prefsResponse.ok) {
                const prefsData = await prefsResponse.json();
                categoryPreferences = prefsData.data || [];
            }

            if (catsResponse.ok) {
                const catsData = await catsResponse.json();
                allCategories = catsData.data || catsData || [];
            }

            renderCategoryPreferences();
            populateCategorySelect();
        } catch (error) {
            console.error('Error loading category preferences:', error);
        }
    }

    function renderCategoryPreferences() {
        const container = document.getElementById('category-preferences-display');
        if (!container) return;
        if (categoryPreferences.length === 0) {
            container.innerHTML = '<span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm">No categories selected</span>';
            const countEl = document.getElementById('category-count');
            if (countEl) countEl.textContent = '0 of 6 categories selected';
            return;
        }

        container.innerHTML = categoryPreferences.map(pref => {
            const category = pref.category || {};
            return `
                <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm flex items-center space-x-2">
                    <span>${category.name || 'Unknown'}</span>
                    <button onclick="removeCategoryPreference(${category.id}, this)" class="text-red-600 hover:text-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            `;
        }).join('');
        const countEl = document.getElementById('category-count');
        if (countEl) countEl.textContent = `${categoryPreferences.length} of 6 categories selected`;
    }

    function populateCategorySelect() {
        const select = document.getElementById('category-select');
        if (!select) return;
        const selectedIds = categoryPreferences.map(p => p.category_id);
        select.innerHTML = '<option value="">Select a category...</option>' +
            allCategories.filter(cat => !selectedIds.includes(cat.id))
                .map(cat => `<option value="${cat.id}">${cat.name}</option>`)
                .join('');
    }

    function editJobDiscovery() {
        const display = document.getElementById('category-preferences-display');
        const edit = document.getElementById('category-preferences-edit');
        if (display) display.classList.add('hidden');
        if (edit) edit.classList.remove('hidden');
        populateCategorySelect();
    }

    function cancelCategoryPreferences() {
        const display = document.getElementById('category-preferences-display');
        const edit = document.getElementById('category-preferences-edit');
        if (display) display.classList.remove('hidden');
        if (edit) edit.classList.add('hidden');
    }

    async function addCategoryPreference() {
        const select = document.getElementById('category-select');
        if (!select) return;
        const categoryId = select.value;
        if (!categoryId) {
            showWarningToast('Please select a category');
            return;
        }

        select.disabled = true;

        try {
            const response = await fetch(`${API_BASE}/job-seeker/category-preferences`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ category_id: parseInt(categoryId) })
            });

            if (response.ok) {
                await loadCategoryPreferences();
                select.value = '';
                populateCategorySelect();
            } else {
                const error = await response.json();
                showErrorToast(error.message || 'Failed to add category');
            }
        } catch (error) {
            console.error('Error adding category preference:', error);
            showErrorToast('An error occurred');
        } finally {
            select.disabled = false;
        }
    }

    async function removeCategoryPreference(categoryId, buttonElement = null) {
        if (!confirm('Remove this category preference?')) return;

        const deleteButton = buttonElement || document.querySelector(`button[onclick*="removeCategoryPreference(${categoryId})"]`);
        const originalText = deleteButton ? deleteButton.innerHTML : '';

        try {
            if (deleteButton) setButtonLoading(deleteButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/category-preferences/${categoryId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (response.ok) {
                await loadCategoryPreferences();
            } else {
                showErrorToast('Failed to remove category');
            }
        } catch (error) {
            console.error('Error removing category preference:', error);
            showErrorToast('An error occurred');
        } finally {
            if (deleteButton) setButtonLoading(deleteButton, false, '', originalText);
        }
    }

    async function saveCategoryPreferences() {
        const saveButton = document.querySelector('button[onclick="saveCategoryPreferences()"]');
        const originalText = saveButton ? saveButton.innerHTML : '';

        try {
            if (saveButton) setButtonLoading(saveButton, true, '', originalText);
            await new Promise(resolve => setTimeout(resolve, 300));
            cancelCategoryPreferences();
        } finally {
            if (saveButton) setButtonLoading(saveButton, false, '', originalText);
        }
    }

    // ========== Hobbies Functions ==========
    function editHobbies() {
        const hobbies = profileData.hobbies || [];
        const input = document.getElementById('hobbies-input');
        if (input) input.value = hobbies.join(', ');
        const display = document.getElementById('hobbies-display');
        const edit = document.getElementById('hobbies-edit');
        if (display) display.classList.add('hidden');
        if (edit) edit.classList.remove('hidden');
    }

    function cancelHobbies() {
        const display = document.getElementById('hobbies-display');
        const edit = document.getElementById('hobbies-edit');
        if (display) display.classList.remove('hidden');
        if (edit) edit.classList.add('hidden');
    }

    async function saveHobbies() {
        const hobbiesText = document.getElementById('hobbies-input').value;
        const hobbies = hobbiesText.split(',').map(h => h.trim()).filter(h => h.length > 0);

        const saveButton = document.querySelector('button[onclick="saveHobbies()"]');
        const originalText = saveButton ? saveButton.innerHTML : '';

        try {
            if (saveButton) setButtonLoading(saveButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ hobbies })
            });

            const data = await response.json();
            if (response.ok) {
                // Update only hobbies in profileData and refresh display (instant)
                profileData.hobbies = hobbies;
                updateHobbies();
                cancelHobbies();
            } else {
                showErrorToast(data.message || 'Failed to save hobbies');
            }
        } catch (error) {
            console.error('Error saving hobbies:', error);
            showErrorToast('An error occurred');
        } finally {
            if (saveButton) setButtonLoading(saveButton, false, '', originalText);
        }
    }

    function updateHobbies() {
        const hobbies = profileData.hobbies || [];
        const displayDiv = document.getElementById('hobbies-display');
        if (!displayDiv) return;
        if (hobbies.length === 0) {
            displayDiv.innerHTML = '<span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm">No hobbies added</span>';
        } else {
            displayDiv.innerHTML = hobbies.map(hobby =>
                `<span class="px-4 py-2 bg-pink-100 text-pink-700 rounded-full text-sm">${hobby}</span>`
            ).join('');
        }
    }

    // ========== Expose all functions to window for onclick handlers ==========
    window.loadProfile = loadProfile;
    window.setupProfileEventListeners = setupEventListeners;
    window.toggleEditMode = toggleEditMode;
    window.editAbout = editAbout;
    window.cancelAbout = cancelAbout;
    window.saveAbout = saveAbout;
    window.editJobPreferences = editJobPreferences;
    window.cancelJobPreferences = cancelJobPreferences;
    window.saveJobPreferences = saveJobPreferences;
    window.saveSocialLinks = saveSocialLinks;
    window.saveVisibility = saveVisibility;
    window.savePersonalInfo = savePersonalInfo;
    window.openExperienceModal = openExperienceModal;
    window.closeExperienceModal = closeExperienceModal;
    window.saveExperience = saveExperience;
    window.deleteExperience = deleteExperience;
    window.openEducationModal = openEducationModal;
    window.closeEducationModal = closeEducationModal;
    window.saveEducation = saveEducation;
    window.deleteEducation = deleteEducation;
    window.openSkillModal = openSkillModal;
    window.closeSkillModal = closeSkillModal;
    window.saveSkill = saveSkill;
    window.deleteSkill = deleteSkill;
    window.openLanguageModal = openLanguageModal;
    window.closeLanguageModal = closeLanguageModal;
    window.saveLanguage = saveLanguage;
    window.deleteLanguage = deleteLanguage;
    window.openCertificationModal = openCertificationModal;
    window.closeCertificationModal = closeCertificationModal;
    window.saveCertification = saveCertification;
    window.deleteCertification = deleteCertification;
    window.openReferenceModal = openReferenceModal;
    window.closeReferenceModal = closeReferenceModal;
    window.saveReference = saveReference;
    window.deleteReference = deleteReference;
    window.editJobDiscovery = editJobDiscovery;
    window.cancelCategoryPreferences = cancelCategoryPreferences;
    window.addCategoryPreference = addCategoryPreference;
    window.removeCategoryPreference = removeCategoryPreference;
    window.saveCategoryPreferences = saveCategoryPreferences;
    window.editHobbies = editHobbies;
    window.cancelHobbies = cancelHobbies;
    window.saveHobbies = saveHobbies;

    // ========== File Preview Functions ==========
    function previewFile(type, fileUrl = null) {
        let url = fileUrl;
        
        // If type is 'cv', get URL from profileData
        if (type === 'cv' && !url) {
            url = profileData.cv_file_path;
        }
        
        if (!url) {
            showErrorToast('File not found');
            return;
        }
        
        const modal = document.getElementById('file-preview-modal');
        const previewContent = document.getElementById('preview-content');
        const downloadLink = document.getElementById('preview-download-link');
        const title = document.getElementById('preview-modal-title');
        
        if (!modal || !previewContent) return;
        
        // Set download link
        if (downloadLink) {
            downloadLink.href = url;
        }
        
        // Set title
        if (title) {
            const filename = url.split('/').pop() || 'File';
            title.textContent = `Preview: ${filename}`;
        }
        
        // Show modal
        modal.classList.remove('hidden');
        
        // Determine file type
        const fileExtension = url.split('.').pop()?.toLowerCase();
        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension);
        const isPdf = fileExtension === 'pdf';
        
        // Show loading
        previewContent.innerHTML = `
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-4 text-gray-600">Loading preview...</p>
            </div>
        `;
        
        if (isImage) {
            // Preview image
            previewContent.innerHTML = `
                <div class="w-full flex items-center justify-center">
                    <img src="${url}" alt="Preview" class="max-w-full max-h-[70vh] rounded-lg shadow-lg" 
                         onerror="this.parentElement.innerHTML='<div class=\\'text-center text-red-600\\'><p>Failed to load image</p></div>'">
                </div>
            `;
        } else if (isPdf) {
            // Preview PDF using iframe
            previewContent.innerHTML = `
                <div class="w-full h-full">
                    <iframe src="${url}" class="w-full h-full min-h-[600px] rounded-lg border border-gray-200" 
                            frameborder="0" 
                            onerror="this.parentElement.innerHTML='<div class=\\'text-center text-red-600 p-8\\'><p class=\\'mb-4\\'>Unable to preview PDF in browser</p><a href=\\'${url}\\' target=\\'_blank\\' class=\\'text-blue-600 hover:underline\\'>Click to open in new tab</a></div>'">
                    </iframe>
                </div>
            `;
        } else {
            // For other file types (doc, docx), show download option
            previewContent.innerHTML = `
                <div class="text-center p-8">
                    <div class="w-24 h-24 bg-gray-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600 mb-2">Preview not available for this file type</p>
                    <p class="text-sm text-gray-500 mb-4">${url.split('/').pop()}</p>
                    <a href="${url}" target="_blank" download class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download File
                    </a>
                </div>
            `;
        }
    }
    
    function closeFilePreview() {
        const modal = document.getElementById('file-preview-modal');
        if (modal) {
            modal.classList.add('hidden');
            // Clear preview content
            const previewContent = document.getElementById('preview-content');
            if (previewContent) {
                previewContent.innerHTML = '';
            }
        }
    }
    
    // Close preview on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFilePreview();
        }
    });
    
    // Close preview when clicking outside
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('file-preview-modal');
        if (modal && !modal.classList.contains('hidden')) {
            const modalContent = modal.querySelector('.bg-white');
            if (modalContent && !modalContent.contains(e.target)) {
                closeFilePreview();
            }
        }
    });
    
    window.previewFile = previewFile;
    window.closeFilePreview = closeFilePreview;

    // ========== Certification File Selection Handler ==========
    function handleCertFileSelect(event) {
        const file = event.target.files[0];
        const previewDiv = document.getElementById('cert-file-preview');
        const fileNameEl = document.getElementById('cert-file-name');
        const fileSizeEl = document.getElementById('cert-file-size');
        const fileIconEl = document.getElementById('cert-file-icon');
        
        if (!file || !previewDiv) return;
        
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showErrorToast('File size must be less than 5MB');
            event.target.value = '';
            return;
        }
        
        // Show preview
        previewDiv.classList.remove('hidden');
        
        // Set file name
        if (fileNameEl) fileNameEl.textContent = file.name;
        
        // Set file size
        if (fileSizeEl) {
            const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
            fileSizeEl.textContent = `${sizeInMB} MB`;
        }
        
        // Update icon based on file type
        if (fileIconEl) {
            const isImage = file.type.startsWith('image/');
            const isPdf = file.type === 'application/pdf';
            
            if (isImage) {
                fileIconEl.innerHTML = `
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                `;
            } else if (isPdf) {
                fileIconEl.innerHTML = `
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                `;
            }
        }
    }
    
    function clearCertFile() {
        const fileInput = document.getElementById('cert-file');
        const previewDiv = document.getElementById('cert-file-preview');
        if (fileInput) fileInput.value = '';
        if (previewDiv) previewDiv.classList.add('hidden');
    }
    
    window.handleCertFileSelect = handleCertFileSelect;
    window.clearCertFile = clearCertFile;

    // ========== Initialization ==========
    function initProfile() {
        if (window.location.pathname !== '/job-seeker/profile') return;

        const profileContent = document.querySelector('main');
        if (!profileContent) return;

        const isLoaded = profileContent.getAttribute('data-profile-loaded') === 'true';
        if (isLoaded) return;

        console.log('[profile.js] Initializing profile page...');
        setupEventListeners();
        loadProfile();
    }

    // Run on initial page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProfile);
    } else {
        initProfile();
    }

    // Run on Livewire SPA navigation
    document.addEventListener('livewire:navigated', function() {
        // Reset loaded flag so data reloads
        const profileContent = document.querySelector('main');
        if (profileContent && window.location.pathname === '/job-seeker/profile') {
            profileContent.removeAttribute('data-profile-loaded');
        }
        // Small delay to ensure DOM is swapped
        setTimeout(initProfile, 50);
    });

    // Handle browser back/forward
    window.addEventListener('popstate', function() {
        const profileContent = document.querySelector('main');
        if (profileContent) {
            profileContent.removeAttribute('data-profile-loaded');
        }
        setTimeout(initProfile, 50);
    });
})();
