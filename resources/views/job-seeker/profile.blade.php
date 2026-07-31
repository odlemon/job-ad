@extends('layouts.job-seeker')

@section('content')
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col">
        @include('partials.job-seeker-navbar')

        <!-- Profile Content -->
        <main class="flex-1 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <!-- Profile Header: blue top, white bottom; avatar center-left; name on white part only; upload icon = white circle + dark gray arrow-up-tray -->
                <div class="rounded-2xl shadow-lg mb-8 overflow-hidden border border-gray-200 bg-white">
                    <div class="relative" style="min-height: 12rem;">
                        <!-- Blue gradient top section -->
                        <div class="h-28 w-full rounded-t-2xl" style="background: linear-gradient(to right, #3b82f6, #2563eb, #22d3ee);"></div>
                        <!-- White bottom section -->
                        <div class="h-20 w-full bg-white"></div>
                        <!-- Avatar (overlaps boundary) + name on white part only -->
                        <div class="absolute flex items-end gap-5" style="left: 1.5rem; top: 7rem;">
                            <div class="relative flex-shrink-0" style="margin-top: -3rem;">
                                <div id="profile-header-photo" class="w-24 h-24 rounded-full flex items-center justify-center border-4 border-white shadow-md" style="background: linear-gradient(to right, #3b82f6, #22d3ee);">
                                    <span id="profile-header-initials" class="text-3xl font-bold text-white font-sans">JD</span>
                                </div>
                                <img id="profile-header-photo-img" src="" alt="Profile" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md hidden">
                                
                                <div id="profile-photo-upload-overlay" class="hidden absolute inset-0 w-24 h-24 rounded-full bg-black bg-opacity-60 flex items-center justify-center border-4 border-white">
                                    <div class="text-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto mb-2"></div>
                                        <p class="text-white text-xs font-medium">Uploading...</p>
                                    </div>
                                </div>
                                <div id="profile-photo-preview-overlay" class="hidden absolute inset-0 w-24 h-24 rounded-full overflow-hidden border-4 border-white">
                                    <img id="profile-photo-preview-img" src="" alt="Preview" class="w-full h-full object-cover">
                                </div>
                                <!-- Upload icon: white circle + dark gray arrow-up-tray (exact design) -->
                                <button type="button" onclick="document.getElementById('profile_photo_file').click()" id="profile-photo-upload-btn" class="absolute -bottom-0.5 -right-0.5 w-8 h-8 rounded-full bg-white border-2 border-white shadow flex items-center justify-center hover:bg-gray-50 transition z-10">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color: #374151;">
                                        <path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                    </svg>
                                </button>
                            </div>
                            <h1 id="profile-header-name" class="font-sans text-2xl font-bold text-black pb-2">John Doe</h1>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Personal Information Section (design: field cards, address full width, greyed by default, Edit/Save) -->
                        <div class="bg-gray-100 rounded-2xl p-6 border border-gray-200">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <h2 class="text-xl font-bold text-gray-900">Personal Information</h2>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" id="personal-info-edit-btn" onclick="window.setPersonalInfoEditable(true)" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 transition cursor-pointer">Edit</button>
                                    <button type="button" id="personal-info-cancel-btn" onclick="window.cancelPersonalInfoEdit()" class="hidden p-2 rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition cursor-pointer" title="Cancel editing">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                    <button type="submit" form="personalInfoForm" id="personal-info-save-btn" class="hidden px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-400 text-white text-sm font-medium hover:from-blue-600 hover:to-cyan-500 shadow-md transition cursor-pointer">Save</button>
                                </div>
                            </div>
                            <form id="personalInfoForm" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                        <input type="text" id="first_name" name="first_name" required disabled
                                            class="personal-info-input w-full border border-gray-200 rounded-lg px-3 py-2 text-gray-900 bg-gray-100 text-gray-500 cursor-not-allowed">
                                    </div>
                                    <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Surname *</label>
                                        <input type="text" id="last_name" name="last_name" required disabled
                                            class="personal-info-input w-full border border-gray-200 rounded-lg px-3 py-2 text-gray-900 bg-gray-100 text-gray-500 cursor-not-allowed">
                                    </div>
                                    <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" id="email" name="email" readonly
                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 bg-gray-100 text-gray-500 cursor-not-allowed">
                                    </div>
                                    <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                        <input type="tel" id="phone" name="phone" disabled
                                            class="personal-info-input w-full border border-gray-200 rounded-lg px-3 py-2 text-gray-900 bg-gray-100 text-gray-500 cursor-not-allowed">
                                    </div>
                                    <!-- Address: enlarged, full width -->
                                    <div class="md:col-span-2 bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                        <input type="text" id="address" name="address" disabled
                                            class="personal-info-input w-full border border-gray-200 rounded-lg px-3 py-2 text-gray-900 bg-gray-100 text-gray-500 cursor-not-allowed">
                                    </div>
                                    <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm relative">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                        <select id="gender" name="gender" disabled
                                            class="personal-info-input w-full border border-gray-200 rounded-lg px-3 py-2 pr-9 bg-gray-100 text-gray-500 cursor-not-allowed appearance-none">
                                            <option value="">Select</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                            <option value="prefer_not_to_say">Prefer not to say</option>
                                        </select>
                                        <svg class="absolute right-4 w-4 h-4 text-gray-400 pointer-events-none" style="top: 2.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                    <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm relative">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                        <input type="date" id="date_of_birth" name="date_of_birth" disabled
                                            class="personal-info-input w-full border border-gray-200 rounded-lg px-3 py-2 pr-9 text-gray-900 bg-gray-100 text-gray-500 cursor-not-allowed">
                                        <p id="age-display" class="text-xs text-gray-500 mt-1"></p>
                                    </div>
                                    <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm relative">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Employment Status</label>
                                        <select id="employment_status" name="employment_status" disabled
                                            class="personal-info-input w-full border border-gray-200 rounded-lg px-3 py-2 pr-9 bg-gray-100 text-gray-500 cursor-not-allowed appearance-none employment-status-select">
                                            <option value="">Select</option>
                                            <option value="currently_employed">Currently Employed</option>
                                            <option value="unemployed">Unemployed</option>
                                            <option value="student">Student</option>
                                            <option value="self_employed">Self Employed</option>
                                            <option value="retired">Retired</option>
                                        </select>
                                        <svg class="absolute right-4 w-4 h-4 text-gray-400 pointer-events-none" style="top: 2.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                    <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm relative">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Highest Education</label>
                                        <select id="highest_education" name="highest_education" disabled
                                            class="personal-info-input w-full border border-gray-200 rounded-lg px-3 py-2 pr-9 bg-gray-100 text-gray-500 cursor-not-allowed appearance-none">
                                            <option value="">Select</option>
                                            <option value="high_school">High School</option>
                                            <option value="bachelor">Bachelor's Degree</option>
                                            <option value="master">Master's Degree</option>
                                            <option value="phd">PhD</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <svg class="absolute right-4 w-4 h-4 text-gray-400 pointer-events-none" style="top: 2.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                    <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm relative">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Driving License</label>
                                        <select id="driving_license" name="driving_license" disabled
                                            class="personal-info-input w-full border border-gray-200 rounded-lg px-3 py-2 pr-9 bg-gray-100 text-gray-500 cursor-not-allowed appearance-none">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                        <svg class="absolute right-4 w-4 h-4 text-gray-400 pointer-events-none" style="top: 2.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                    <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm relative">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">License Issued Date</label>
                                        <input type="date" id="license_issued_date" name="license_issued_date" disabled
                                            class="personal-info-input w-full border border-gray-200 rounded-lg px-3 py-2 text-gray-900 bg-gray-100 text-gray-500 cursor-not-allowed">
                                        <p id="license-issued-date-display" class="text-xs text-gray-500 mt-1"></p>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- About Section -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-gray-900">About</h2>
                                <button onclick="editAbout()" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Edit</button>
                            </div>
                            <div id="about-display" class="text-gray-700">
                                <p id="about-text">No bio added yet.</p>
                            </div>
                            <div id="about-edit" class="hidden">
                                <textarea id="bio" rows="4" maxlength="5000"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                <p class="text-xs text-gray-500 mt-1"><span id="bio-char-count">0</span>/5000 characters</p>
                                <div class="flex space-x-3 mt-4">
                                    <button onclick="saveAbout()" class="bg-gradient-to-r from-blue-500 to-cyan-400 text-white px-6 py-2 rounded-lg font-medium hover:from-blue-600 hover:to-cyan-500 shadow-md transition">Save</button>
                                    <button onclick="cancelAbout()" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-50 transition">Cancel</button>
                                </div>
                            </div>
                        </div>

                        <!-- Job Preferences Section -->
                        <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-purple-50">
                                        <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </span>
                                    <h2 class="text-base font-semibold text-gray-900">Job Preferences</h2>
                                </div>
                                <button onclick="editJobPreferences()" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Edit</button>
                            </div>
                            <div id="job-preferences-display" class="flex flex-wrap gap-2">
                                <span class="px-4 py-1.5 bg-gray-100 text-gray-700 rounded-md text-sm">No preferences set</span>
                            </div>
                            <div id="job-preferences-edit" class="hidden mt-4">
                                <div class="flex flex-wrap gap-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="job_preference" value="full_time" class="mr-2">
                                        <span class="px-4 py-1.5 bg-blue-100 text-blue-700 rounded-md text-sm cursor-pointer">Full Time</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="job_preference" value="part_time" class="mr-2">
                                        <span class="px-4 py-1.5 bg-purple-100 text-purple-700 rounded-md text-sm cursor-pointer">Part Time</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="job_preference" value="contract" class="mr-2">
                                        <span class="px-4 py-1.5 bg-green-100 text-green-700 rounded-md text-sm cursor-pointer">Contract</span>
                                    </label>
                                </div>
                                <div class="flex space-x-3 mt-4">
                                    <button onclick="saveJobPreferences()" class="bg-gradient-to-r from-blue-500 to-cyan-400 text-white px-6 py-2 rounded-lg font-medium hover:from-blue-600 hover:to-cyan-500 shadow-md transition">Save</button>
                                    <button onclick="cancelJobPreferences()" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-50 transition">Cancel</button>
                                </div>
                            </div>
                        </div>

                        <!-- Salary Range Section -->
                        <div class="bg-white rounded-md shadow-sm p-5 border border-gray-100 mt-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-600 text-lg font-semibold leading-none">$</span>
                                    <div>
                                        <h2 class="text-base font-semibold text-gray-900">Salary Range</h2>
                                        <p class="text-xs text-gray-500">Expected salary range to show employers</p>
                                    </div>
                                </div>
                                <button type="button" onclick="window.editSalaryRange()" id="salary-range-edit-btn" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Edit</button>
                            </div>
                            <div class="space-y-3">
                                <div class="relative">
                                    <input type="range" id="salary_min_range" min="0" max="100000" step="1000"
                                        class="w-full h-1 rounded-full appearance-none bg-gray-200 outline-none cursor-default salary-range-input" disabled>
                                    <input type="range" id="salary_max_range" min="0" max="100000" step="1000"
                                        class="w-full h-1 rounded-full appearance-none bg-transparent outline-none cursor-default salary-range-input pointer-events-none absolute inset-0"
                                        disabled>
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>0 SCR</span>
                                    <span>100k SCR</span>
                                </div>
                                <div class="flex items-center gap-2 pt-1">
                                    <span id="salary-min-badge" class="px-4 py-1.5 bg-emerald-100 text-emerald-700 rounded-md text-sm">0 SCR</span>
                                    <span class="text-gray-500 text-sm">-</span>
                                    <span id="salary-max-badge" class="px-4 py-1.5 bg-emerald-100 text-emerald-700 rounded-md text-sm">0 SCR</span>
                                </div>
                                <div id="salary-range-actions" class="hidden flex items-center gap-2 pt-2">
                                    <button type="button" onclick="window.cancelSalaryRange()" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 transition">Cancel</button>
                                    <button type="button" onclick="window.saveSalaryRange()" id="salary-range-save-btn" class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-400 text-white text-sm font-medium hover:from-blue-600 hover:to-cyan-500 shadow-md transition">Save</button>
                                </div>
                            </div>
                        </div>

                        <!-- Job Discovery Section (dropdown only, select updates endpoint immediately) -->
                        <div class="bg-white rounded-md shadow-sm p-5 border border-gray-100 mt-4">
                            <div class="flex items-center space-x-2 mb-4">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <h2 class="text-base font-semibold text-gray-900">Job Discovery</h2>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">Select job categories you're interested in (max 6)</p>
                            <select id="category-select" class="w-full border border-gray-300 rounded-md px-4 py-2 mb-3 bg-white text-gray-900 cursor-pointer">
                                <option value="">Select a category...</option>
                            </select>
                            <div id="category-preferences-display" class="flex flex-wrap gap-2 mb-2">
                                <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">No categories selected</span>
                            </div>
                            <p class="text-xs text-gray-500" id="category-count">0 of 6 categories selected</p>
                        </div>

                        <!-- Work Experience Section (design: blue briefcase, timeline dots+line, white cards, Current pill, X) -->
                        <div class="bg-white rounded-md shadow-sm p-6 border border-gray-100">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <h2 class="text-lg font-semibold text-gray-900">Work Experience</h2>
                                </div>
                                <button type="button" onclick="openExperienceModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium inline-flex items-center gap-2 transition cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add Experience
                                </button>
                            </div>
                            <div id="experiences-list" class="relative">
                                <div id="experiences-skeleton" class="space-y-4">
                                    <div class="flex gap-4 animate-pulse">
                                        <div class="w-4 flex justify-center flex-shrink-0"><div class="w-2.5 h-2.5 rounded-full bg-gray-300"></div></div>
                                        <div class="flex-1 rounded-md border border-gray-200 p-4 space-y-2">
                                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                            <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                                        </div>
                                    </div>
                                    <div class="flex gap-4 animate-pulse">
                                        <div class="w-4 flex justify-center flex-shrink-0"><div class="w-2.5 h-2.5 rounded-full bg-gray-300"></div></div>
                                        <div class="flex-1 rounded-md border border-gray-200 p-4 space-y-2">
                                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                            <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Education Section (design: purple graduation cap, no timeline, white cards, X) -->
                        <div class="bg-white rounded-md shadow-sm p-6 border border-gray-100 mt-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                    </svg>
                                    <h2 class="text-lg font-semibold text-gray-900">Education</h2>
                                </div>
                                <button type="button" onclick="openEducationModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium inline-flex items-center gap-2 transition cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add Education
                                </button>
                            </div>
                            <div id="educations-list" class="space-y-4">
                                <div id="educations-skeleton" class="space-y-4">
                                    <div class="rounded-md border border-gray-200 p-4 animate-pulse space-y-2">
                                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                        <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Skills Section -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                    <h2 class="text-xl font-bold text-gray-900">Skills</h2>
                                </div>
                                <button type="button" onclick="openSkillModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium inline-flex items-center gap-2 transition cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add Skill
                                </button>
                            </div>
                            <div id="skills-list" class="flex flex-wrap gap-2">
                                <div id="skills-skeleton" class="flex flex-wrap gap-2 animate-pulse">
                                    <div class="h-8 bg-gray-200 rounded-full w-24"></div>
                                    <div class="h-8 bg-gray-200 rounded-full w-28"></div>
                                    <div class="h-8 bg-gray-200 rounded-full w-20"></div>
                                    <div class="h-8 bg-gray-200 rounded-full w-32"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Languages Section -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 text-green-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                        </svg>
                                    </span>
                                    <h2 class="text-xl font-bold text-gray-900">Languages</h2>
                                </div>
                                <button type="button" onclick="openLanguageModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium inline-flex items-center gap-2 transition cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add Language
                                </button>
                            </div>
                            <div id="languages-list" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div id="languages-skeleton" class="grid grid-cols-1 sm:grid-cols-2 gap-3 animate-pulse">
                                    <div class="h-20 bg-gray-200 rounded-lg"></div>
                                    <div class="h-20 bg-gray-200 rounded-lg"></div>
                                    <div class="h-20 bg-gray-200 rounded-lg"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Hobbies & Interests Section -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                    <h2 class="text-xl font-bold text-gray-900">Hobbies & Interests</h2>
                                </div>
                                <button onclick="editHobbies()" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Edit</button>
                            </div>
                            <div id="hobbies-display" class="flex flex-wrap gap-2">
                                <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm">No hobbies added</span>
                            </div>
                            <div id="hobbies-edit" class="hidden mt-4">
                                <textarea id="hobbies-input" rows="3" placeholder="Enter hobbies separated by commas (e.g., Photography, Hiking, Reading)"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                <div class="flex space-x-3 mt-4">
                                    <button onclick="saveHobbies()" class="bg-gradient-to-r from-blue-500 to-cyan-400 text-white px-6 py-2 rounded-lg font-medium hover:from-blue-600 hover:to-cyan-500 shadow-md transition">Save</button>
                                    <button onclick="cancelHobbies()" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-50 transition">Cancel</button>
                                </div>
                            </div>
                        </div>

                        <!-- Certifications Section -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                    <h2 class="text-xl font-bold text-gray-900">Certifications</h2>
                                </div>
                                <button type="button" onclick="openCertificationModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium inline-flex items-center gap-2 transition cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add Certification
                                </button>
                            </div>
                            <div id="certifications-list" class="space-y-4">
                                <div id="certifications-skeleton" class="space-y-4 animate-pulse">
                                    <div class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg">
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex-shrink-0"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                            <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- References Section -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-lg" style="background-color: #E0F7FA;">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #0194A5;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </span>
                                    <h2 class="text-xl font-bold text-gray-900">References</h2>
                                </div>
                                <button type="button" onclick="openReferenceModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium inline-flex items-center gap-2 transition cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add Reference
                                </button>
                            </div>
                            <div id="references-list" class="space-y-4">
                                <div id="references-skeleton" class="space-y-4 animate-pulse">
                                    <div class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg">
                                        <div class="w-12 h-12 bg-gray-200 rounded-full flex-shrink-0"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                            <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Profile Strength -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Profile Strength</h3>
                            <div class="flex items-center justify-center mb-4">
                                <div class="relative w-32 h-32">
                                    <svg id="profile-strength-circle" class="transform -rotate-90 w-32 h-32">
                                        <circle cx="64" cy="64" r="56" stroke="#e5e7eb" stroke-width="8" fill="none"></circle>
                                        <circle id="profile-strength-progress" cx="64" cy="64" r="56" stroke="#3b82f6" stroke-width="8" fill="none"
                                            stroke-dasharray="351.86" stroke-dashoffset="351.86" stroke-linecap="round" style="transition: stroke-dashoffset 0.5s ease;"></circle>
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span id="profile-strength-percent" class="text-3xl font-bold text-gray-900">0%</span>
                                        <span class="text-xs text-gray-500 mt-1">Complete</span>
                                    </div>
                                </div>
                            </div>
                            <div id="profile-strength-items" class="space-y-2">
                                <!-- Items will be loaded dynamically -->
                            </div>
                        </div>

                        <!-- Documents -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Documents</h3>
                            <p class="text-sm text-gray-500 mb-4">Upload resumes, certificates, or other documents. Give each one a name. Mark one as your primary resume for job applications.</p>
                            <div id="documents-list" class="space-y-3 mb-4">
                                <!-- Document items rendered by JS -->
                            </div>
                            <div id="documents-empty" class="text-sm text-gray-500 py-4 text-center border-2 border-dashed border-gray-200 rounded-lg mb-4 hidden">No documents yet. Add one below.</div>
                            <!-- Add document form -->
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 bg-gray-50/50">
                                <p class="text-sm font-medium text-gray-700 mb-3">Add a document</p>
                                <div class="flex flex-wrap gap-3 items-end">
                                    <div class="min-w-[180px] flex-1">
                                        <label for="document-name" class="block text-xs font-medium text-gray-500 mb-1">Document name</label>
                                        <input type="text" id="document-name" placeholder="e.g. Resume, Cover Letter, Certificate" maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="file" id="document-file" accept=".pdf,.doc,.docx" class="sr-only" aria-hidden="true">
                                        <label for="document-file" id="document-file-trigger" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer inline-flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"></path></svg>
                                            Choose file
                                        </label>
                                        <span id="document-file-name" class="text-sm text-gray-500 truncate max-w-[120px]"></span>
                                    </div>
                                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                                        <input type="checkbox" id="document-is-primary" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        Use as primary resume
                                    </label>
                                    <button type="button" id="document-upload-btn" disabled class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Upload
                                    </button>
                                </div>
                                <p id="document-name-error" class="text-xs text-red-500 mt-1 hidden">Please enter a document name.</p>
                            </div>
                            <!-- Legacy single CV upload kept for backward compat in JS (can be removed later) -->
                            <input type="file" id="cv_file" name="cv" accept=".pdf,.doc,.docx" class="hidden">
                        </div>

                        <!-- Social Links (read-only display + edit mode with icon per row) -->
                        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Social Links</h3>
                                <button type="button" id="social-links-edit-btn" onclick="window.setSocialLinksEditable(true)" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition cursor-pointer" title="Edit social links">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                            </div>
                            <!-- Read-only display -->
                            <div id="social-links-display" class="space-y-4">
                                <div id="social-link-facebook-row" class="flex items-center gap-3 hidden">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded bg-[#1877F2]">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </span>
                                    <a id="social-link-facebook" href="#" target="_blank" rel="noopener" class="text-gray-700 text-base truncate hover:underline"></a>
                                </div>
                                <div id="social-link-instagram-row" class="flex items-center gap-3 hidden">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-gradient-to-br from-[#F58529] via-[#DD2A7B] to-[#8134AF]">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                    </span>
                                    <a id="social-link-instagram" href="#" target="_blank" rel="noopener" class="text-gray-700 text-base truncate hover:underline"></a>
                                </div>
                                <div id="social-link-linkedin-row" class="flex items-center gap-3 hidden">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded bg-[#0A66C2]">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                    </span>
                                    <a id="social-link-linkedin" href="#" target="_blank" rel="noopener" class="text-gray-700 text-base truncate hover:underline"></a>
                                </div>
                                <div id="social-link-website-row" class="flex items-center gap-3 hidden">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded bg-gray-500/90">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                    </span>
                                    <a id="social-link-website" href="#" target="_blank" rel="noopener" class="text-gray-700 text-base truncate hover:underline"></a>
                                </div>
                                <p id="social-links-empty" class="text-sm text-gray-500">No social links added yet.</p>
                            </div>
                            <!-- Edit mode: inputs -->
                            <div id="social-links-edit" class="hidden space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded bg-[#1877F2]"><svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></span>
                                    <input type="url" id="facebook_url" placeholder="facebook.com/username" class="social-links-input flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-gradient-to-br from-[#F58529] via-[#DD2A7B] to-[#8134AF]"><svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></span>
                                    <input type="url" id="instagram_url" placeholder="instagram.com/username" class="social-links-input flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded bg-[#0A66C2]"><svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></span>
                                    <input type="url" id="linkedin_url" placeholder="linkedin.com/in/username" class="social-links-input flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded bg-gray-500/90"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg></span>
                                    <input type="url" id="website_url" placeholder="yourwebsite.com" class="social-links-input flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="flex items-center gap-2 pt-1">
                                    <button type="button" onclick="window.cancelSocialLinksEdit()" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 transition cursor-pointer">Cancel</button>
                                    <button type="button" id="social-links-save-btn" onclick="saveSocialLinks()" class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-400 text-white text-sm font-medium hover:from-blue-600 hover:to-cyan-500 shadow-md transition cursor-pointer">Save</button>
                                </div>
                            </div>
                        </div>

                        <!-- Visibility -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Visibility</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" id="public_profile" name="public_profile" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-gray-700">Public Profile</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="open_to_opportunities" name="open_to_opportunities" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-gray-700">Open to Opportunities</span>
                                </label>
                                <button onclick="saveVisibility()" class="w-full bg-gradient-to-r from-blue-500 to-cyan-400 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-blue-600 hover:to-cyan-500 shadow-md transition mt-4">Save Settings</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Hidden file inputs -->
    <input type="file" id="profile_photo_file" name="profile_photo" accept="image/jpeg,image/jpg,image/png,image/webp" class="hidden">
</div>

@push('scripts')
<!-- Modals -->
@include('job-seeker.profile-modals')
@endpush
@endsection
