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

        const docFileTrigger = document.getElementById('document-file-trigger');
        const docFile = document.getElementById('document-file');
        const docUploadBtn = document.getElementById('document-upload-btn');
        if (docFileTrigger && docFile) {
            docFileTrigger.addEventListener('click', function() { docFile.click(); });
        }
        if (docFile) {
            docFile.addEventListener('change', function() {
                var nameEl = document.getElementById('document-file-name');
                var file = docFile.files && docFile.files[0];
                if (nameEl) nameEl.textContent = file ? file.name : '';
                if (docUploadBtn) docUploadBtn.disabled = !(file && document.getElementById('document-name') && document.getElementById('document-name').value.trim());
            });
        }
        const docNameEl = document.getElementById('document-name');
        if (docNameEl) {
            docNameEl.addEventListener('input', function() {
                var file = document.getElementById('document-file');
                var hasFile = file && file.files && file.files.length > 0;
                if (docUploadBtn) docUploadBtn.disabled = !(hasFile && this.value.trim());
            });
        }
        if (docUploadBtn) {
            docUploadBtn.removeEventListener('click', handleDocumentUpload);
            docUploadBtn.addEventListener('click', handleDocumentUpload);
        }

        const dobInput = document.getElementById('date_of_birth');
        if (dobInput) {
            dobInput.removeEventListener('change', calculateAge);
            dobInput.addEventListener('change', calculateAge);
        }
        const licenseDateInput = document.getElementById('license_issued_date');
        if (licenseDateInput) {
            licenseDateInput.removeEventListener('change', updateLicenseIssuedDateDisplay);
            licenseDateInput.addEventListener('change', updateLicenseIssuedDateDisplay);
        }
        const drivingLicenseSelect = document.getElementById('driving_license');
        if (drivingLicenseSelect) {
            drivingLicenseSelect.removeEventListener('change', toggleLicenseIssuedDateFromDrivingLicense);
            drivingLicenseSelect.addEventListener('change', toggleLicenseIssuedDateFromDrivingLicense);
        }
        const currentExpCheckbox = document.getElementById('exp-is-current');
        if (currentExpCheckbox) {
            currentExpCheckbox.removeEventListener('change', onCurrentExperienceToggle);
            currentExpCheckbox.addEventListener('change', onCurrentExperienceToggle);
        }
        const currentEduCheckbox = document.getElementById('edu-is-current');
        if (currentEduCheckbox) {
            currentEduCheckbox.removeEventListener('change', onCurrentEducationToggle);
            currentEduCheckbox.addEventListener('change', onCurrentEducationToggle);
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
                window.location.href = '/';
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
                updateSalaryRangeSection();
                updateDocuments();
                updateSocialLinks();
                updateVisibility();
            }
        } catch (error) {
            console.error('Error loading profile:', error);
        }
    }

    // Full load: profile + all sections in parallel
    async function loadProfile() {
        await Promise.all([
            loadProfileData(),
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

        if (headerName) headerName.textContent = fullName;
        if (headerInitials) headerInitials.textContent = initials;
        if (userInitials) userInitials.textContent = initials;

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
        updateLicenseIssuedDateDisplay();
        applyEmploymentStatusGreen();
        if (typeof window.setPersonalInfoEditable === 'function') {
            window.setPersonalInfoEditable(false);
        }
    }

    function updateLicenseIssuedDateDisplay() {
        const el = document.getElementById('license_issued_date');
        const display = document.getElementById('license-issued-date-display');
        if (!el || !display) return;
        if (el.value) {
            const d = new Date(el.value);
            display.textContent = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        } else {
            display.textContent = '';
        }
    }

    function applyEmploymentStatusGreen() {
        const sel = document.getElementById('employment_status');
        if (!sel) return;
        if (sel.value === 'currently_employed' && sel.disabled) {
            sel.classList.add('text-green-600');
            sel.classList.remove('text-gray-900');
        } else {
            sel.classList.remove('text-green-600');
            sel.classList.add('text-gray-900');
        }
    }

    function toggleLicenseIssuedDateFromDrivingLicense() {
        const drivingEl = document.getElementById('driving_license');
        const licenseDateEl = document.getElementById('license_issued_date');
        if (!drivingEl || !licenseDateEl) return;
        const saveBtn = document.getElementById('personal-info-save-btn');
        const isEditMode = saveBtn && !saveBtn.classList.contains('hidden');
        if (!isEditMode) return;
        const hasLicense = drivingEl.value === '1';
        licenseDateEl.disabled = !hasLicense;
        if (hasLicense) {
            licenseDateEl.classList.remove('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
            licenseDateEl.classList.add('bg-white', 'text-gray-900');
        } else {
            licenseDateEl.classList.add('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
            licenseDateEl.classList.remove('bg-white', 'text-gray-900');
            licenseDateEl.value = '';
            const display = document.getElementById('license-issued-date-display');
            if (display) display.textContent = '';
        }
    }

    window.cancelPersonalInfoEdit = function() {
        if (typeof updatePersonalInfoForm === 'function') updatePersonalInfoForm();
        if (typeof window.setPersonalInfoEditable === 'function') window.setPersonalInfoEditable(false);
    };

    window.setPersonalInfoEditable = function(editable) {
        const inputs = document.querySelectorAll('.personal-info-input');
        const editBtn = document.getElementById('personal-info-edit-btn');
        const saveBtn = document.getElementById('personal-info-save-btn');
        const cancelBtn = document.getElementById('personal-info-cancel-btn');

        inputs.forEach(function(inp) {
            if (inp.id === 'email') return;
            if (inp.id === 'license_issued_date') {
                if (editable) {
                    if (document.getElementById('driving_license').value === '1') {
                        inp.disabled = false;
                        inp.classList.remove('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
                        inp.classList.add('bg-white', 'text-gray-900');
                    } else {
                        inp.disabled = true;
                        inp.classList.add('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
                        inp.classList.remove('bg-white', 'text-gray-900');
                    }
                } else {
                    inp.disabled = true;
                    inp.classList.remove('bg-white', 'text-gray-900');
                    inp.classList.add('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
                }
                return;
            }
            inp.disabled = !editable;
            if (editable) {
                inp.classList.remove('bg-gray-100', 'text-gray-500', 'cursor-not-allowed', 'text-green-600');
                inp.classList.add('bg-white', 'text-gray-900');
            } else {
                inp.classList.remove('bg-white', 'text-gray-900');
                inp.classList.add('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
            }
        });

        if (editBtn) editBtn.classList.toggle('hidden', editable);
        if (saveBtn) saveBtn.classList.toggle('hidden', !editable);
        if (cancelBtn) cancelBtn.classList.toggle('hidden', !editable);
        if (editable && typeof toggleLicenseIssuedDateFromDrivingLicense === 'function') toggleLicenseIssuedDateFromDrivingLicense();
        if (!editable && typeof applyEmploymentStatusGreen === 'function') applyEmploymentStatusGreen();
    };

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
            displayDiv.innerHTML = '<span class="px-4 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-md text-sm">No preferences set</span>';
        } else {
            displayDiv.innerHTML = preferences.map(pref => {
                const labels = {
                    'full_time': { text: 'Full Time', class: 'bg-blue-100 text-blue-700' },
                    'part_time': { text: 'Part Time', class: 'bg-purple-100 text-purple-700' },
                    'contract': { text: 'Contract', class: 'bg-green-100 text-green-700' }
                };
                const label = labels[pref] || { text: pref, class: 'bg-gray-100 text-gray-700' };
                return `<span class="px-4 py-1.5 ${label.class} rounded-md text-sm">${label.text}</span>`;
            }).join('');
        }
    }

    // ===== Salary Range =====
    const SALARY_MIN = 0;
    const SALARY_MAX = 100000;
    const SALARY_STEP = 1000;
    const SALARY_MIN_GAP = 1000;

    function formatSalary(amount) {
        if (amount == null) return '0 SCR';
        return amount.toLocaleString('en-US') + ' SCR';
    }

    function clampSalary(value) {
        if (value == null || isNaN(value)) return 0;
        return Math.min(SALARY_MAX, Math.max(SALARY_MIN, value));
    }

    function updateSalaryRangeSection() {
        const minRaw = profileData.expected_salary_min;
        const maxRaw = profileData.expected_salary_max;
        let min = clampSalary(minRaw);
        let max = clampSalary(maxRaw);
        if (min === 0 && max === 0) {
            min = 0;
            max = SALARY_MAX;
        }
        if (min > max - SALARY_MIN_GAP) {
            min = Math.max(SALARY_MIN, max - SALARY_MIN_GAP);
        }

        const minInput = document.getElementById('salary_min_range');
        const maxInput = document.getElementById('salary_max_range');
        const minBadge = document.getElementById('salary-min-badge');
        const maxBadge = document.getElementById('salary-max-badge');

        if (!minInput || !maxInput) return;

        minInput.value = min;
        maxInput.value = max;

        if (minBadge) minBadge.textContent = formatSalary(min);
        if (maxBadge) maxBadge.textContent = formatSalary(max);

        updateSalarySliderTrack();
    }

    function updateSalarySliderTrack() {
        const minInput = document.getElementById('salary_min_range');
        const maxInput = document.getElementById('salary_max_range');
        if (!minInput || !maxInput) return;

        const min = parseInt(minInput.value || '0', 10);
        const max = parseInt(maxInput.value || '0', 10);
        const left = ((min - SALARY_MIN) / (SALARY_MAX - SALARY_MIN)) * 100;
        const right = ((max - SALARY_MIN) / (SALARY_MAX - SALARY_MIN)) * 100;

        const trackGradient = `linear-gradient(to right, #e5e7eb 0%, #e5e7eb ${left}%, #3b82f6 ${left}%, #3b82f6 ${right}%, #e5e7eb ${right}%, #e5e7eb 100%)`;
        minInput.style.background = trackGradient;
    }

    function handleSalaryRangeInput(changed) {
        const minInput = document.getElementById('salary_min_range');
        const maxInput = document.getElementById('salary_max_range');
        const minBadge = document.getElementById('salary-min-badge');
        const maxBadge = document.getElementById('salary-max-badge');
        if (!minInput || !maxInput) return;

        let min = parseInt(minInput.value || '0', 10);
        let max = parseInt(maxInput.value || '0', 10);

        if (changed === 'min' && min > max - SALARY_MIN_GAP) {
            min = max - SALARY_MIN_GAP;
            minInput.value = min;
        }
        if (changed === 'max' && max < min + SALARY_MIN_GAP) {
            max = min + SALARY_MIN_GAP;
            maxInput.value = max;
        }

        if (minBadge) minBadge.textContent = formatSalary(min);
        if (maxBadge) maxBadge.textContent = formatSalary(max);

        updateSalarySliderTrack();
    }

    window.editSalaryRange = function() {
        const minInput = document.getElementById('salary_min_range');
        const maxInput = document.getElementById('salary_max_range');
        const actions = document.getElementById('salary-range-actions');
        const editBtn = document.getElementById('salary-range-edit-btn');
        if (minInput) {
            minInput.disabled = false;
            minInput.classList.remove('cursor-default');
            minInput.classList.add('cursor-pointer');
        }
        if (maxInput) {
            maxInput.disabled = false;
            maxInput.classList.remove('cursor-default', 'pointer-events-none');
            maxInput.classList.add('cursor-pointer');
        }
        if (actions) actions.classList.remove('hidden');
        if (editBtn) editBtn.classList.add('hidden');

        minInput.addEventListener('input', () => handleSalaryRangeInput('min'));
        maxInput.addEventListener('input', () => handleSalaryRangeInput('max'));
    };

    window.cancelSalaryRange = function() {
        // Reset sliders from current profileData and disable editing
        updateSalaryRangeSection();
        const minInput = document.getElementById('salary_min_range');
        const maxInput = document.getElementById('salary_max_range');
        const actions = document.getElementById('salary-range-actions');
        const editBtn = document.getElementById('salary-range-edit-btn');
        if (minInput) {
            minInput.disabled = true;
            minInput.classList.remove('cursor-pointer');
            minInput.classList.add('cursor-default');
        }
        if (maxInput) {
            maxInput.disabled = true;
            maxInput.classList.remove('cursor-pointer');
            maxInput.classList.add('cursor-default', 'pointer-events-none');
        }
        if (actions) actions.classList.add('hidden');
        if (editBtn) editBtn.classList.remove('hidden');
    };

    window.saveSalaryRange = async function() {
        const minInput = document.getElementById('salary_min_range');
        const maxInput = document.getElementById('salary_max_range');
        const saveButton = document.getElementById('salary-range-save-btn');
        if (!minInput || !maxInput) return;

        let expected_salary_min = parseInt(minInput.value || '0', 10);
        let expected_salary_max = parseInt(maxInput.value || '0', 10);
        if (expected_salary_min > expected_salary_max - SALARY_MIN_GAP) {
            expected_salary_min = expected_salary_max - SALARY_MIN_GAP;
        }

        const originalText = saveButton ? saveButton.innerHTML : '';

        try {
            if (saveButton) setButtonLoading(saveButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ expected_salary_min, expected_salary_max })
            });

            const data = await response.json();
            if (response.ok) {
                const updated = data.data || data.job_seeker || {};
                profileData.expected_salary_min = updated.expected_salary_min ?? expected_salary_min;
                profileData.expected_salary_max = updated.expected_salary_max ?? expected_salary_max;
                updateSalaryRangeSection();
                window.cancelSalaryRange();
                showSuccessToast('Salary range saved!');
            } else {
                const msg = data.errors ? Object.values(data.errors).flat().join(', ') : data.message;
                showErrorToast(msg || 'Failed to save salary range');
            }
        } catch (error) {
            console.error('Error saving salary range:', error);
            showErrorToast('An error occurred');
        } finally {
            if (saveButton) setButtonLoading(saveButton, false, '', originalText);
        }
    };

    function updateDocuments() {
        const listEl = document.getElementById('documents-list');
        const emptyEl = document.getElementById('documents-empty');
        if (!listEl) return;

        const documents = profileData.documents || [];
        if (documents.length === 0) {
            listEl.innerHTML = '';
            if (emptyEl) emptyEl.classList.remove('hidden');
            return;
        }
        if (emptyEl) emptyEl.classList.add('hidden');

        listEl.innerHTML = documents.map(function(doc) {
            const dateStr = doc.created_at ? new Date(doc.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';
            const primaryBadge = doc.is_primary ? '<span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Primary resume</span>' : '';
            return '<div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900 rounded-lg gap-4 document-item" data-document-id="' + doc.id + '">' +
                '<div class="flex items-center space-x-3 min-w-0 flex-1">' +
                '<div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">' +
                '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg></div>' +
                '<div class="min-w-0 flex-1">' +
                '<p class="font-medium text-gray-900 dark:text-white truncate" title="' + (doc.name || '').replace(/"/g, '&quot;') + '">' + (doc.name || 'Document') + '</p>' +
                '<p class="text-xs text-gray-500 dark:text-gray-400">' + dateStr + '</p>' +
                '</div>' +
                (primaryBadge ? '<div class="flex-shrink-0">' + primaryBadge + '</div>' : '') +
                '</div>' +
                '<div class="flex items-center space-x-2 flex-shrink-0">' +
                (!doc.is_primary ? '<button type="button" class="document-set-primary text-blue-600 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded hover:bg-blue-50 transition" data-id="' + doc.id + '">Set primary</button>' : '') +
                '<a href="' + (doc.file_path || '#') + '" target="_blank" download class="text-gray-600 dark:text-gray-400 hover:text-gray-700 text-sm font-medium px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition inline-flex items-center">Download</a>' +
                '<button type="button" class="document-delete text-red-600 hover:text-red-700 text-sm font-medium px-2 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition" data-id="' + doc.id + '">Delete</button>' +
                '</div></div>';
        }).join('');

        listEl.querySelectorAll('.document-set-primary').forEach(function(btn) {
            btn.addEventListener('click', function() { setPrimaryDocument(parseInt(btn.dataset.id, 10)); });
        });
        listEl.querySelectorAll('.document-delete').forEach(function(btn) {
            btn.addEventListener('click', function() { deleteDocument(parseInt(btn.dataset.id, 10)); });
        });
    }

    async function setPrimaryDocument(id) {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/documents/${id}/primary`, {
                method: 'PUT',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json', 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({})
            });
            const data = await response.json();
            if (response.ok) {
                profileData.documents = data.documents || data.data?.documents || profileData.documents;
                profileData.cv_file_path = data.data?.cv_file_path ?? profileData.cv_file_path;
                updateDocuments();
                updateProfileStrength();
                showSuccessToast('Primary resume updated');
            } else {
                showErrorToast(data.message || 'Failed to set primary document');
            }
        } catch (e) {
            showErrorToast('An error occurred');
        }
    }

    async function deleteDocument(id) {
        if (!(await window.showConfirmDialog('This document will be permanently removed.', { title: 'Delete document?', confirmText: 'Delete', cancelText: 'Cancel' }))) return;
        try {
            const response = await fetch(`${API_BASE}/job-seeker/documents/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include'
            });
            const data = await response.json();
            if (response.ok) {
                profileData.documents = (profileData.documents || []).filter(function(d) { return d.id !== id; });
                var primary = profileData.documents && profileData.documents.find(function(d) { return d.is_primary; });
                profileData.cv_file_path = primary ? primary.file_path : null;
                updateDocuments();
                updateProfileStrength();
                showSuccessToast('Document deleted');
            } else {
                showErrorToast(data.message || 'Failed to delete document');
            }
        } catch (e) {
            showErrorToast('An error occurred');
        }
    }

    async function handleDocumentUpload() {
        const nameEl = document.getElementById('document-name');
        const fileEl = document.getElementById('document-file');
        const isPrimaryEl = document.getElementById('document-is-primary');
        const btn = document.getElementById('document-upload-btn');
        const errEl = document.getElementById('document-name-error');
        var name = nameEl && nameEl.value ? nameEl.value.trim() : '';
        var file = fileEl && fileEl.files && fileEl.files[0];
        if (!name) {
            if (errEl) { errEl.textContent = 'Please enter a document name.'; errEl.classList.remove('hidden'); }
            return;
        }
        if (errEl) errEl.classList.add('hidden');
        if (!file) {
            showErrorToast('Please choose a file');
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            showErrorToast('File size must be less than 10MB');
            return;
        }
        var formData = new FormData();
        formData.append('name', name);
        formData.append('file', file);
        formData.append('is_primary', (isPrimaryEl && isPrimaryEl.checked) ? '1' : '0');
        var originalText = btn ? btn.innerHTML : '';
        try {
            if (btn) setButtonLoading(btn, true, '', originalText);
            const response = await fetch(`${API_BASE}/job-seeker/documents`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: formData
            });
            const data = await response.json();
            if (response.ok) {
                profileData.documents = profileData.documents || [];
                profileData.documents.push(data.data);
                if (data.data && data.data.is_primary) {
                    profileData.cv_file_path = data.data.file_path;
                }
                updateDocuments();
                updateProfileStrength();
                showSuccessToast('Document uploaded');
                nameEl.value = '';
                fileEl.value = '';
                if (isPrimaryEl) isPrimaryEl.checked = false;
                document.getElementById('document-file-name').textContent = '';
                if (btn) btn.disabled = true;
            } else {
                showErrorToast(data.message || (data.errors && data.errors.name ? data.errors.name[0] : 'Upload failed'));
            }
        } catch (e) {
            showErrorToast('An error occurred');
        } finally {
            if (btn) setButtonLoading(btn, false, '', originalText);
        }
    }

    function displayUrl(url) {
        if (!url) return '';
        try {
            const u = url.replace(/^https?:\/\//i, '').replace(/\/$/, '');
            return u;
        } catch (e) {
            return url;
        }
    }

    function ensureUrlHasProtocol(url) {
        if (!url || !url.trim()) return '';
        const s = url.trim();
        if (/^https?:\/\//i.test(s)) return s;
        return 'https://' + s;
    }

    function updateSocialLinks() {
        const links = {
            facebook_url: profileData.facebook_url || '',
            instagram_url: profileData.instagram_url || '',
            linkedin_url: profileData.linkedin_url || '',
            website_url: profileData.website_url || ''
        };
        const ids = ['facebook', 'instagram', 'linkedin', 'website'];
        let hasAny = false;
        ids.forEach(function(key) {
            const urlKey = key + '_url';
            const url = links[urlKey];
            const row = document.getElementById('social-link-' + key + '-row');
            const linkEl = document.getElementById('social-link-' + key);
            if (row) row.classList.toggle('hidden', !url);
            if (linkEl) {
                if (url) {
                    linkEl.href = ensureUrlHasProtocol(url);
                    linkEl.textContent = displayUrl(url);
                    linkEl.classList.remove('hidden');
                    hasAny = true;
                } else {
                    linkEl.href = '#';
                    linkEl.textContent = '';
                }
            }
            const inp = document.getElementById(urlKey);
            if (inp) inp.value = url;
        });
        const emptyEl = document.getElementById('social-links-empty');
        if (emptyEl) emptyEl.classList.toggle('hidden', hasAny);
    }

    window.setSocialLinksEditable = function(editable) {
        const display = document.getElementById('social-links-display');
        const edit = document.getElementById('social-links-edit');
        const editBtn = document.getElementById('social-links-edit-btn');
        if (display) display.classList.toggle('hidden', editable);
        if (edit) edit.classList.toggle('hidden', !editable);
        if (editBtn) editBtn.classList.toggle('hidden', editable);
        if (editable) {
            document.getElementById('facebook_url').value = profileData.facebook_url || '';
            document.getElementById('instagram_url').value = profileData.instagram_url || '';
            document.getElementById('linkedin_url').value = profileData.linkedin_url || '';
            document.getElementById('website_url').value = profileData.website_url || '';
        }
    };

    window.cancelSocialLinksEdit = function() {
        window.setSocialLinksEditable(false);
    };

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

        // Resume / primary document
        const hasPrimary = !!profileData.cv_file_path || (profileData.documents && profileData.documents.some(function(d) { return d.is_primary; }));
        items.push({
            label: '✓ Resume / Documents',
            complete: hasPrimary,
            status: hasPrimary ? 'Done' : 'Pending'
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
        const submitButton = document.getElementById('personal-info-save-btn') || e.target.querySelector('button[type="submit"]');
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
                if (typeof window.setPersonalInfoEditable === 'function') {
                    window.setPersonalInfoEditable(false);
                }
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
                // Also refresh salary range if backend returns it
                const updated = data.data || data.job_seeker || {};
                if (updated.expected_salary_min !== undefined) {
                    profileData.expected_salary_min = updated.expected_salary_min;
                }
                if (updated.expected_salary_max !== undefined) {
                    profileData.expected_salary_max = updated.expected_salary_max;
                }
                updateSalaryRangeSection();
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
        const toNull = function(v) { return (v && v.trim()) ? v.trim() : null; };
        const facebook_url = toNull(document.getElementById('facebook_url').value);
        const instagram_url = toNull(document.getElementById('instagram_url').value);
        const linkedin_url = toNull(document.getElementById('linkedin_url').value);
        const website_url = toNull(document.getElementById('website_url').value);
        const saveButton = document.getElementById('social-links-save-btn');
        const originalText = saveButton ? saveButton.innerHTML : '';

        try {
            if (saveButton) setButtonLoading(saveButton, true, '', originalText);

            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ facebook_url, instagram_url, linkedin_url, website_url })
            });

            const data = await response.json();
            if (response.ok) {
                const updated = data.data || data.job_seeker || {};
                profileData.facebook_url = updated.facebook_url !== undefined ? updated.facebook_url : facebook_url;
                profileData.instagram_url = updated.instagram_url !== undefined ? updated.instagram_url : instagram_url;
                profileData.linkedin_url = updated.linkedin_url !== undefined ? updated.linkedin_url : linkedin_url;
                profileData.website_url = updated.website_url !== undefined ? updated.website_url : website_url;
                updateSocialLinks();
                if (typeof window.setSocialLinksEditable === 'function') window.setSocialLinksEditable(false);
                showSuccessToast('Social links saved!');
            } else {
                const msg = data.errors ? Object.values(data.errors).flat().join(', ') : data.message;
                showErrorToast(msg || 'Failed to save links');
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
    function onCurrentExperienceToggle() {
        const checkbox = document.getElementById('exp-is-current');
        const endInput = document.getElementById('exp-end-date');
        if (!checkbox || !endInput) return;
        if (checkbox.checked) {
            endInput.value = '';
            endInput.disabled = true;
            endInput.classList.add('bg-gray-100', 'cursor-not-allowed');
            endInput.classList.remove('bg-white', 'cursor-pointer');
        } else {
            endInput.disabled = false;
            endInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            endInput.classList.add('bg-white');
        }
    }

    function onCurrentEducationToggle() {
        const checkbox = document.getElementById('edu-is-current');
        const endInput = document.getElementById('edu-end-date');
        if (!checkbox || !endInput) return;
        if (checkbox.checked) {
            endInput.value = '';
            endInput.disabled = true;
            endInput.classList.add('bg-gray-100', 'cursor-not-allowed');
            endInput.classList.remove('bg-white', 'cursor-pointer');
        } else {
            endInput.disabled = false;
            endInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            endInput.classList.add('bg-white');
        }
    }

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

        if (skeleton) skeleton.classList.add('hidden');

        if (experiences.length === 0) {
            container.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm">No work experience added yet.</p>';
            return;
        }

        const itemsHtml = experiences.map(exp => {
            const startDate = exp.start_date ? new Date(exp.start_date).toLocaleDateString('en-US', { month: 'short', year: 'numeric' }) : '';
            const endDate = exp.is_current ? 'Present' : (exp.end_date ? new Date(exp.end_date).toLocaleDateString('en-US', { month: 'short', year: 'numeric' }) : '');
            const currentBadge = exp.is_current ? '<span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-md text-xs font-medium ml-1">Current</span>' : '';
            const desc = (exp.description || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return `
                <div class="flex gap-3 items-start">
                    <div class="flex justify-center flex-shrink-0 w-4 mt-1.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-blue-500 border-2 border-white shadow-sm dark:shadow-none" style="margin-left: -9px;"></div>
                    </div>
                    <div class="flex-1 relative min-w-0 py-2">
                        <button type="button" onclick="deleteExperience(${exp.id}, this)" class="absolute top-0 right-0 text-gray-500 dark:text-gray-400 hover:text-gray-700 p-1 rounded cursor-pointer" title="Remove">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        <h3 class="font-semibold text-gray-900 dark:text-white pr-8">${(exp.job_title || '').replace(/</g, '&lt;')}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">${(exp.company_name || '')}${exp.location ? ' • ' + (exp.location || '') : ''}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">${startDate} - ${endDate}${currentBadge}</p>
                        ${desc ? `<p class="text-sm text-gray-700 dark:text-gray-300 mt-2">${desc}</p>` : ''}
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = `<div class="border-l-2 border-gray-200 dark:border-gray-700 pl-1 space-y-4">${itemsHtml}</div>`;
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
        if (isCurrent) {
            isCurrent.checked = exp?.is_current || false;
        }
        // Apply end-date disabled state based on checkbox
        onCurrentExperienceToggle();
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
        if (!(await window.showConfirmDialog('This work experience entry will be permanently removed from your profile.', { title: 'Delete experience?', confirmText: 'Delete', cancelText: 'Cancel' }))) return;

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

        if (skeleton) skeleton.classList.add('hidden');

        if (educations.length === 0) {
            container.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm">No education added yet.</p>';
            return;
        }
        container.innerHTML = educations.map(edu => {
            const startYear = edu.start_date ? new Date(edu.start_date).getFullYear() : '';
            const endYear = edu.end_date ? new Date(edu.end_date).getFullYear() : 'Present';
            const gpaScale = edu.gpa_scale || '4.0';
            const gpaText = edu.gpa != null && edu.gpa !== '' ? ` GPA: ${edu.gpa}/${gpaScale}` : '';
            return `
                <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm dark:shadow-none p-4 relative">
                    <button type="button" onclick="deleteEducation(${edu.id}, this)" class="absolute top-3 right-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 p-1 rounded cursor-pointer" title="Remove">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <h3 class="font-semibold text-gray-900 dark:text-white pr-8">${(edu.degree || '').replace(/</g, '&lt;')}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">${(edu.institution || '')}${edu.location ? ' ' + (edu.location || '') : ''}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">${startYear} - ${endYear}${gpaText}</p>
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
        for (const [fieldId, val] of Object.entries(fields)) {
            const el = document.getElementById(fieldId);
            if (el) el.value = val;
        }
        const isCurrentCheckbox = document.getElementById('edu-is-current');
        if (isCurrentCheckbox) {
            isCurrentCheckbox.checked = !!(edu && !edu.end_date);
        }
        onCurrentEducationToggle();
        const modal = document.getElementById('education-modal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeEducationModal() {
        const modal = document.getElementById('education-modal');
        if (modal) modal.classList.add('hidden');
        editingEducationId = null;
    }

    async function saveEducation() {
        const isCurrent = document.getElementById('edu-is-current')?.checked;
        const data = {
            degree: document.getElementById('edu-degree').value,
            institution: document.getElementById('edu-institution').value,
            location: document.getElementById('edu-location').value,
            start_date: document.getElementById('edu-start-date').value,
            end_date: isCurrent ? null : (document.getElementById('edu-end-date').value || null),
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
        if (!(await window.showConfirmDialog('This education entry will be permanently removed from your profile.', { title: 'Delete education?', confirmText: 'Delete', cancelText: 'Cancel' }))) return;

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
            container.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm">No skills added yet.</p>';
            return;
        }
        container.innerHTML = skills.map(skill => {
            const levelText = skill.proficiency_level ? (skill.proficiency_level.charAt(0).toUpperCase() + skill.proficiency_level.slice(1)) : '—';
            const skillName = (skill.skill_name || '').replace(/</g, '&lt;');
            return `
                <span class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
                    <span class="text-gray-900 dark:text-white font-medium text-sm">${skillName}</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-medium">${levelText}</span>
                    <button type="button" onclick="deleteSkill(${skill.id}, this)" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 p-0.5 rounded cursor-pointer ml-0.5" title="Remove">
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
        if (!(await window.showConfirmDialog('This skill will be permanently removed from your profile.', { title: 'Delete skill?', confirmText: 'Delete', cancelText: 'Cancel' }))) return;

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
            container.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm col-span-2">No languages added yet.</p>';
            return;
        }
        const languageIconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path></svg>';
        container.innerHTML = languages.map(lang => {
            const levelText = lang.proficiency_level ? (lang.proficiency_level.charAt(0).toUpperCase() + lang.proficiency_level.slice(1)) : '—';
            const langName = (lang.language || '').replace(/</g, '&lt;');
            return `
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm dark:shadow-none p-4 flex items-center gap-3">
                    <span class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-lg bg-green-100 text-green-600">${languageIconSvg}</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white">${langName}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">${levelText}</p>
                    </div>
                    <button type="button" onclick="deleteLanguage(${lang.id}, this)" class="flex-shrink-0 text-gray-500 dark:text-gray-400 hover:text-gray-700 p-1 rounded cursor-pointer" title="Remove">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
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
        if (!(await window.showConfirmDialog('This language will be permanently removed from your profile.', { title: 'Delete language?', confirmText: 'Delete', cancelText: 'Cancel' }))) return;

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

        const cardsHtml = certifications.map(cert => {
            const name = (cert.certification_name || '').replace(/</g, '&lt;');
            const org = (cert.issuing_organization || '').replace(/</g, '&lt;');
            const issue = cert.issue_date
                ? new Date(cert.issue_date).toLocaleDateString('en-US', { month: 'short', year: 'numeric' })
                : null;
            const expiry = cert.expiry_date
                ? new Date(cert.expiry_date).toLocaleDateString('en-US', { month: 'short', year: 'numeric' })
                : null;
            const datesLine = issue
                ? `Issued: ${issue}${expiry ? ' &nbsp;&nbsp;·&nbsp;&nbsp; Expires: ' + expiry : ''}`
                : (expiry ? `Expires: ${expiry}` : '');

            const fileUrl = cert.certificate_file_path || '';
            const safeFileUrl = fileUrl.replace(/"/g, '&quot;');

            return `
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-4 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-100 text-amber-500 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 dark:text-white">${name}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">${org}</p>
                            ${datesLine ? `<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${datesLine}</p>` : ''}
                            ${fileUrl ? `
                                <button type="button" data-file-url="${safeFileUrl}" onclick="previewFile('cert', this.dataset.fileUrl)" class="mt-2 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"></path>
                                    </svg>
                                    View Certificate
                                </button>
                            ` : ''}
                        </div>
                    </div>
                    <button type="button" onclick="deleteCertification(${cert.id}, this)" class="flex-shrink-0 text-gray-400 hover:text-gray-700 p-1 rounded cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
        }).join('');

        const uploadBlock = `
            <button type="button" onclick="openCertificationModal()" class="mt-3 w-full border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 flex items-center justify-center text-sm text-gray-600 dark:text-gray-400 hover:border-blue-400 hover:text-blue-600 transition cursor-pointer bg-gray-50 dark:bg-gray-900">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"></path>
                </svg>
                <span class="font-medium">Upload Certificate Copy</span>
            </button>
        `;

        if (certifications.length === 0) {
            container.innerHTML = uploadBlock;
        } else {
            container.innerHTML = cardsHtml + uploadBlock;
        }
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
        const triggerText = document.getElementById('cert-file-trigger-text');
        if (triggerText) triggerText.textContent = 'Choose file';
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
        if (!(await window.showConfirmDialog('This certification will be permanently removed from your profile.', { title: 'Delete certification?', confirmText: 'Delete', cancelText: 'Cancel' }))) return;

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
            container.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm">No references added yet.</p>';
            return;
        }

        const peopleIconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0194A5;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>';
        const envelopeIconSvg = '<svg class="w-4 h-4 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>';
        const phoneIconSvg = '<svg class="w-4 h-4 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>';

        container.innerHTML = references.map(ref => {
            const name = (ref.reference_name || '').replace(/</g, '&lt;');
            const title = (ref.title || '').replace(/</g, '&lt;');
            const company = (ref.company || '').replace(/</g, '&lt;');
            const rel = (ref.relationship || '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()).replace(/</g, '&lt;');
            const email = (ref.email || '').replace(/</g, '&lt;');
            const phone = (ref.phone || '').replace(/</g, '&lt;');
            const roleLine = [title, company].filter(Boolean).join(' at ') || '—';
            return `
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm dark:shadow-none p-4 flex items-start gap-3 relative">
                    <button type="button" onclick="deleteReference(${ref.id}, this)" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 p-1 rounded cursor-pointer" title="Remove">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <span class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-lg" style="background-color: #E0F7FA;">${peopleIconSvg}</span>
                    <div class="flex-1 min-w-0 pr-8">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-base">${name}</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-0.5">${roleLine}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">${rel}</p>
                        <div class="mt-2 space-y-1">
                            ${email ? `<div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">${envelopeIconSvg}<a href="mailto:${email}" class="hover:text-blue-600 truncate">${email}</a></div>` : ''}
                            ${phone ? `<div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">${phoneIconSvg}<a href="tel:${phone}" class="hover:text-blue-600">${phone}</a></div>` : ''}
                        </div>
                    </div>
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
        if (!(await window.showConfirmDialog('This reference will be permanently removed from your profile.', { title: 'Delete reference?', confirmText: 'Delete', cancelText: 'Cancel' }))) return;

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

    // ========== Category Preferences Functions (dropdown only; select/remove update endpoint immediately) ==========

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
            container.innerHTML = '<span class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-md text-sm">No categories selected</span>';
            const countEl = document.getElementById('category-count');
            if (countEl) countEl.textContent = '0 of 6 categories selected';
            return;
        }

        container.innerHTML = categoryPreferences.map(pref => {
            const category = pref.category || {};
            return `
                <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md text-sm flex items-center space-x-2">
                    <span>${category.name || 'Unknown'}</span>
                    <button type="button" onclick="removeCategoryPreference(${category.id}, this)" class="text-red-600 hover:text-red-700 cursor-pointer">
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

    async function syncCategoryPreferencesToApi() {
        try {
            const categoryIds = categoryPreferences.map(pref => pref.category_id);
            const response = await fetch(`${API_BASE}/job-seeker/category-preferences/sync`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ category_ids: categoryIds })
            });
            const data = await response.json();
            if (response.ok) {
                categoryPreferences = data.data || [];
                renderCategoryPreferences();
                populateCategorySelect();
            } else {
                const msg = data.errors ? Object.values(data.errors).flat().join(', ') : data.message;
                showErrorToast(msg || 'Failed to update categories');
            }
        } catch (error) {
            console.error('Error syncing category preferences:', error);
            showErrorToast('An error occurred');
        }
    }

    async function addCategoryPreference() {
        const select = document.getElementById('category-select');
        if (!select) return;
        const categoryId = select.value;
        if (!categoryId) return;
        if (categoryPreferences.length >= 6) {
            showWarningToast('You can select up to 6 categories only');
            select.value = '';
            return;
        }
        const idInt = parseInt(categoryId, 10);
        if (categoryPreferences.some(pref => pref.category_id === idInt)) {
            select.value = '';
            return;
        }
        const category = allCategories.find(cat => cat.id === idInt) || { id: idInt };
        categoryPreferences.push({ category_id: idInt, category });
        renderCategoryPreferences();
        select.value = '';
        populateCategorySelect();
        await syncCategoryPreferencesToApi();
    }

    async function removeCategoryPreference(categoryId, buttonElement = null) {
        const idInt = parseInt(categoryId, 10);
        categoryPreferences = categoryPreferences.filter(pref => pref.category_id !== idInt);
        renderCategoryPreferences();
        populateCategorySelect();
        await syncCategoryPreferencesToApi();
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
            displayDiv.innerHTML = '<span class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-md text-sm">No hobbies added</span>';
        } else {
            displayDiv.innerHTML = hobbies.map(hobby =>
                `<span class="px-4 py-2 bg-pink-100 text-pink-700 rounded-md text-sm">${hobby}</span>`
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
    window.saveSalaryRange = window.saveSalaryRange;
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
    window.addCategoryPreference = addCategoryPreference;
    window.removeCategoryPreference = removeCategoryPreference;
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
                <p class="mt-4 text-gray-600 dark:text-gray-400">Loading preview...</p>
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
                    <iframe src="${url}" class="w-full h-full min-h-[600px] rounded-lg border border-gray-200 dark:border-gray-700" 
                            frameborder="0" 
                            onerror="this.parentElement.innerHTML='<div class=\\'text-center text-red-600 p-8\\'><p class=\\'mb-4\\'>Unable to preview PDF in browser</p><a href=\\'${url}\\' target=\\'_blank\\' class=\\'text-blue-600 hover:underline\\'>Click to open in new tab</a></div>'">
                    </iframe>
                </div>
            `;
        } else {
            // For other file types (doc, docx), show download option
            previewContent.innerHTML = `
                <div class="text-center p-8">
                    <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-lg mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-2">Preview not available for this file type</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">${url.split('/').pop()}</p>
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
        const triggerText = document.getElementById('cert-file-trigger-text');
        if (triggerText) triggerText.textContent = file.name;
        
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
        const triggerText = document.getElementById('cert-file-trigger-text');
        if (fileInput) fileInput.value = '';
        if (previewDiv) previewDiv.classList.add('hidden');
        if (triggerText) triggerText.textContent = 'Choose file';
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
