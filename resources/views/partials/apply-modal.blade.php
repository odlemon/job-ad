<!-- Application Preview Modal -->
<div id="applyModal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background: rgba(0,0,0,0.3); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);">
    <div class="relative w-full max-w-lg mx-4 bg-white rounded-xl shadow-2xl overflow-hidden" style="max-height: 90vh;">
        <div class="overflow-y-auto" style="max-height: 90vh;">
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10">
                <h2 class="text-lg font-bold text-gray-900">Apply for this job</h2>
                <button onclick="window.closeApplyModal()" class="p-1 text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Job Summary -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-lg shadow-sm border border-gray-100 p-1 flex items-center justify-center flex-shrink-0">
                        <img id="applyModalLogo" src="" alt="" class="w-full h-full object-contain rounded">
                    </div>
                    <div class="min-w-0">
                        <h3 id="applyModalJobTitle" class="text-sm font-bold text-gray-900 truncate"></h3>
                        <p id="applyModalCompany" class="text-xs text-gray-500 truncate"></p>
                    </div>
                </div>
            </div>

            <!-- Your Application Info -->
            <div class="px-6 py-5 space-y-4">
                <h4 class="text-sm font-semibold text-gray-900">Your Application Details</h4>

                <div class="space-y-3">
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="text-gray-500 w-16">Name</span>
                        <span id="applyModalName" class="text-gray-900 font-medium"></span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="text-gray-500 w-16">Email</span>
                        <span id="applyModalEmail" class="text-gray-900 font-medium"></span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <span class="text-gray-500 w-16">Phone</span>
                        <span id="applyModalPhone" class="text-gray-900 font-medium"></span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="text-gray-500 w-16">CV</span>
                        <span id="applyModalCV" class="text-gray-900 font-medium"></span>
                    </div>
                </div>

                <!-- Cover Letter (optional) -->
                <div>
                    <label for="applyModalCoverLetter" class="block text-sm font-semibold text-gray-700 mb-1.5">Cover Letter <span class="text-gray-400 text-xs font-normal">(optional)</span></label>
                    <textarea id="applyModalCoverLetter" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" placeholder="Tell the employer why you're a great fit for this role..."></textarea>
                </div>

                <!-- Error/Success Messages -->
                <div id="applyModalError" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm"></div>
                <div id="applyModalSuccess" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm"></div>
            </div>

            <!-- Footer -->
            <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                <button onclick="window.closeApplyModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                <button id="submitApplyBtn" class="px-6 py-2.5 text-sm font-semibold text-white rounded-lg transition shadow-sm flex items-center gap-2" style="background:#ec4899;">
                    <span id="submitApplyBtnText">Submit Application</span>
                    <svg id="submitApplySpinner" class="hidden animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window._applyJobId = null;

    window.closeApplyModal = function() {
        const modal = document.getElementById('applyModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    };

    window.openApplyModal = function(jobId, jobData) {
        const modal = document.getElementById('applyModal');
        if (!modal) return;
        window._applyJobId = jobId;

        // Reset state
        document.getElementById('applyModalError').classList.add('hidden');
        document.getElementById('applyModalSuccess').classList.add('hidden');
        document.getElementById('applyModalCoverLetter').value = '';
        const btn = document.getElementById('submitApplyBtn');
        btn.disabled = false;
        document.getElementById('submitApplyBtnText').textContent = 'Submit Application';
        document.getElementById('submitApplySpinner').classList.add('hidden');

        // Fill job info
        if (jobData) {
            document.getElementById('applyModalJobTitle').textContent = jobData.title || 'Job Title';
            document.getElementById('applyModalCompany').textContent = jobData.company?.name || 'Company';
            const logo = document.getElementById('applyModalLogo');
            if (jobData.company?.logo) {
                logo.src = jobData.company.logo;
            } else {
                logo.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIGZpbGw9IiNGM0Y0RjYiLz48L3N2Zz4=';
            }
        } else {
            document.getElementById('applyModalJobTitle').textContent = 'Loading...';
            document.getElementById('applyModalCompany').textContent = '';
        }

        // Set initial loading placeholders for profile
        document.getElementById('applyModalName').textContent = 'Loading...';
        document.getElementById('applyModalEmail').textContent = 'Loading...';
        document.getElementById('applyModalPhone').textContent = 'Loading...';
        document.getElementById('applyModalCV').textContent = 'Loading...';

        // Show modal IMMEDIATELY
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        // Click outside to close
        modal.onclick = function(e) { if (e.target === modal) window.closeApplyModal(); };

        // Submit handler
        btn.onclick = function() { window.submitJobApplication(jobId); };

        // Fetch job seeker profile in background to fill details
        fetch('/api/job-seeker/profile', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function(profileRes) {
            if (profileRes.ok) return profileRes.json();
            throw new Error('Profile fetch failed');
        }).then(function(profileData) {
            var seeker = profileData.job_seeker || profileData.data || profileData;
            var user = seeker.user || window.AUTH_USER || {};
            document.getElementById('applyModalName').textContent = ((seeker.first_name || '') + ' ' + (seeker.last_name || '')).trim() || 'Not set';
            document.getElementById('applyModalEmail').textContent = user.email || (window.AUTH_USER && window.AUTH_USER.email) || 'Not set';
            document.getElementById('applyModalPhone').textContent = seeker.phone || user.phone || 'Not set';
            var cvEl = document.getElementById('applyModalCV');
            if (seeker.cv_file_path) {
                cvEl.textContent = 'CV attached';
                cvEl.className = 'text-gray-900 font-medium';
            } else {
                cvEl.textContent = 'No CV uploaded';
                cvEl.className = 'text-amber-600 font-medium';
            }
        }).catch(function() {
            document.getElementById('applyModalName').textContent = (window.AUTH_USER && window.AUTH_USER.name) || 'Not set';
            document.getElementById('applyModalEmail').textContent = (window.AUTH_USER && window.AUTH_USER.email) || 'Not set';
            document.getElementById('applyModalPhone').textContent = 'Not set';
            document.getElementById('applyModalCV').textContent = 'Not available';
        });
    };

    window.submitJobApplication = async function(jobId) {
        const errorEl = document.getElementById('applyModalError');
        const successEl = document.getElementById('applyModalSuccess');
        const btn = document.getElementById('submitApplyBtn');
        const btnText = document.getElementById('submitApplyBtnText');
        const spinner = document.getElementById('submitApplySpinner');

        errorEl.classList.add('hidden');
        successEl.classList.add('hidden');
        btn.disabled = true;
        btnText.textContent = 'Submitting...';
        spinner.classList.remove('hidden');

        try {
            const coverLetter = document.getElementById('applyModalCoverLetter').value.trim();
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            const res = await fetch('/api/job-seeker/applications', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({
                    job_advertisement_id: parseInt(jobId),
                    cover_letter: coverLetter || undefined,
                    additional_info: coverLetter ? { cover_letter: coverLetter } : {}
                })
            });

            const data = await res.json();

            if (res.ok) {
                successEl.textContent = 'Application submitted successfully! Good luck!';
                successEl.classList.remove('hidden');
                btnText.textContent = 'Applied';
                spinner.classList.add('hidden');

                // Update apply button on job show page if it exists
                const applyBtn = document.getElementById('apply-btn-' + jobId);
                if (applyBtn) {
                    applyBtn.disabled = true;
                    applyBtn.classList.remove('bg-gradient-to-r', 'from-pink-500', 'to-pink-600', 'hover:from-pink-600', 'hover:to-pink-700');
                    applyBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                    applyBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Already Applied';
                    applyBtn.onclick = null;
                }

                setTimeout(() => window.closeApplyModal(), 2000);
            } else {
                errorEl.textContent = data.message || data.error || 'Failed to submit application. Please try again.';
                errorEl.classList.remove('hidden');
                btn.disabled = false;
                btnText.textContent = 'Submit Application';
                spinner.classList.add('hidden');
            }
        } catch (e) {
            errorEl.textContent = 'Network error. Please try again.';
            errorEl.classList.remove('hidden');
            btn.disabled = false;
            btnText.textContent = 'Submit Application';
            spinner.classList.add('hidden');
        }
    };

    window.handleJobApply = function(jobId, jobData) {
        if (!window.IS_AUTHENTICATED) {
            if (typeof window.openAuthModal === 'function') {
                window.openAuthModal('login');
            } else {
                window.location.href = '/login';
            }
            return;
        }

        // Open modal immediately with whatever data we have
        window.openApplyModal(jobId, jobData || null);

        // If no jobData, fetch it in background to update the modal header
        if (!jobData) {
            fetch('/api/jobs/' + jobId, { headers: { 'Accept': 'application/json' } })
                .then(function(res) { return res.ok ? res.json() : null; })
                .then(function(result) {
                    if (result) {
                        var job = result.data || result;
                        document.getElementById('applyModalJobTitle').textContent = job.title || 'Job Title';
                        document.getElementById('applyModalCompany').textContent = (job.company && job.company.name) || 'Company';
                        var logo = document.getElementById('applyModalLogo');
                        if (job.company && job.company.logo) logo.src = job.company.logo;
                    }
                }).catch(function() {});
        }
    };
</script>
