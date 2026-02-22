@extends('layouts.job-seeker')

@section('content')
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col">
        @include('partials.job-seeker-navbar')

        <!-- Profile Content -->
        <main class="flex-1 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <!-- Profile Header Banner -->
                <div class="bg-gradient-to-r from-blue-500 via-blue-600 to-cyan-400 rounded-2xl shadow-lg mb-8 p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-6">
                            <div class="relative">
                                <div id="profile-header-photo" class="w-24 h-24 rounded-full bg-white bg-opacity-20 flex items-center justify-center border-4 border-white border-opacity-30">
                                    <span id="profile-header-initials" class="text-3xl font-bold">JD</span>
                                </div>
                                <img id="profile-header-photo-img" src="" alt="Profile" class="w-24 h-24 rounded-full object-cover border-4 border-white border-opacity-30 hidden">
                                
                                <!-- Upload Progress Overlay -->
                                <div id="profile-photo-upload-overlay" class="hidden absolute inset-0 w-24 h-24 rounded-full bg-black bg-opacity-60 flex items-center justify-center border-4 border-white border-opacity-30">
                                    <div class="text-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto mb-2"></div>
                                        <p class="text-white text-xs font-medium">Uploading...</p>
                                    </div>
                                </div>
                                
                                <!-- Preview Overlay (shows selected image before upload) -->
                                <div id="profile-photo-preview-overlay" class="hidden absolute inset-0 w-24 h-24 rounded-full overflow-hidden border-4 border-white border-opacity-30">
                                    <img id="profile-photo-preview-img" src="" alt="Preview" class="w-full h-full object-cover">
                                </div>
                                
                                <button onclick="document.getElementById('profile_photo_file').click()" id="profile-photo-upload-btn" class="absolute bottom-0 right-0 bg-white bg-opacity-30 rounded-full p-2 border-2 border-white hover:bg-opacity-40 transition z-10">
                                    <svg id="profile-photo-camera-icon" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <svg id="profile-photo-loading-icon" class="w-4 h-4 text-white hidden animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                            </div>
                            <div>
                                <h1 id="profile-header-name" class="text-3xl font-bold mb-1">John Doe</h1>
                                <p id="profile-header-title" class="text-white text-opacity-90 text-lg">Senior Software Engineer</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Personal Information Section -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <h2 class="text-xl font-bold text-gray-900">Personal Information</h2>
                                </div>
                            </div>
                            <form id="personalInfoForm" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                        <input type="text" id="first_name" name="first_name" required
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Surname *</label>
                                        <input type="text" id="last_name" name="last_name" required
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" id="email" name="email" readonly
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 text-gray-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                        <input type="tel" id="phone" name="phone"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                        <input type="text" id="address" name="address"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                        <select id="gender" name="gender"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Select</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                            <option value="prefer_not_to_say">Prefer not to say</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                        <input type="date" id="date_of_birth" name="date_of_birth"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <p id="age-display" class="text-xs text-gray-500 mt-1"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Employment Status</label>
                                        <select id="employment_status" name="employment_status"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Select</option>
                                            <option value="currently_employed">Currently Employed</option>
                                            <option value="unemployed">Unemployed</option>
                                            <option value="student">Student</option>
                                            <option value="self_employed">Self Employed</option>
                                            <option value="retired">Retired</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Highest Education</label>
                                        <select id="highest_education" name="highest_education"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Select</option>
                                            <option value="high_school">High School</option>
                                            <option value="bachelor">Bachelor's Degree</option>
                                            <option value="master">Master's Degree</option>
                                            <option value="phd">PhD</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Driving License</label>
                                        <select id="driving_license" name="driving_license"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">License Issued Date</label>
                                        <input type="date" id="license_issued_date" name="license_issued_date"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                                <div class="pt-4 border-t border-gray-200">
                                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                                        Save Changes
                                    </button>
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
                                    <button onclick="saveAbout()" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">Save</button>
                                    <button onclick="cancelAbout()" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-50 transition">Cancel</button>
                                </div>
                            </div>
                        </div>

                        <!-- Job Preferences Section -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h2 class="text-xl font-bold text-gray-900">Job Preferences</h2>
                                </div>
                                <button onclick="editJobPreferences()" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Edit</button>
                            </div>
                            <div id="job-preferences-display" class="flex flex-wrap gap-2">
                                <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm">No preferences set</span>
                            </div>
                            <div id="job-preferences-edit" class="hidden mt-4">
                                <div class="flex flex-wrap gap-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="job_preference" value="full_time" class="mr-2">
                                        <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm cursor-pointer">Full Time</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="job_preference" value="part_time" class="mr-2">
                                        <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm cursor-pointer">Part Time</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="job_preference" value="contract" class="mr-2">
                                        <span class="px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm cursor-pointer">Contract</span>
                                    </label>
                                </div>
                                <div class="flex space-x-3 mt-4">
                                    <button onclick="saveJobPreferences()" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">Save</button>
                                    <button onclick="cancelJobPreferences()" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-50 transition">Cancel</button>
                                </div>
                            </div>
                        </div>

                        <!-- Job Discovery Section -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <h2 class="text-xl font-bold text-gray-900">Job Discovery</h2>
                                </div>
                                <button onclick="editJobDiscovery()" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Edit</button>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Select job categories you're interested in (max 6)</p>
                            <div id="category-preferences-display" class="flex flex-wrap gap-2 mb-2">
                                <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm">No categories selected</span>
                            </div>
                            <p class="text-xs text-gray-500" id="category-count">0 of 6 categories selected</p>
                            <div id="category-preferences-edit" class="hidden mt-4">
                                <select id="category-select" class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-4">
                                    <option value="">Select a category...</option>
                                </select>
                                <div class="flex space-x-3">
                                    <button onclick="saveCategoryPreferences()" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">Save</button>
                                    <button onclick="cancelCategoryPreferences()" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-50 transition">Cancel</button>
                                </div>
                            </div>
                        </div>

                        <!-- Work Experience Section -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <h2 class="text-xl font-bold text-gray-900">Work Experience</h2>
                                </div>
                                <button onclick="openExperienceModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">+ Add Experience</button>
                            </div>
                            <div id="experiences-list" class="space-y-4">
                                <div id="experiences-skeleton" class="space-y-4">
                                    <div class="flex items-start space-x-3 border-l-4 border-gray-200 pl-4 py-2 animate-pulse">
                                        <div class="w-2 h-2 bg-gray-300 rounded-full mt-2 flex-shrink-0"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                            <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3 border-l-4 border-gray-200 pl-4 py-2 animate-pulse">
                                        <div class="w-2 h-2 bg-gray-300 rounded-full mt-2 flex-shrink-0"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                            <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Education Section -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                    </svg>
                                    <h2 class="text-xl font-bold text-gray-900">Education</h2>
                                </div>
                                <button onclick="openEducationModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">+ Add Education</button>
                            </div>
                            <div id="educations-list" class="space-y-4">
                                <div id="educations-skeleton" class="space-y-4">
                                    <div class="flex items-start space-x-3 border-l-4 border-gray-200 pl-4 py-2 animate-pulse">
                                        <div class="w-2 h-2 bg-gray-300 rounded-full mt-2 flex-shrink-0"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                            <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Skills Section -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-gray-900">Skills</h2>
                                <button onclick="openSkillModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">+ Add Skill</button>
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
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                    </svg>
                                    <h2 class="text-xl font-bold text-gray-900">Languages</h2>
                                </div>
                                <button onclick="openLanguageModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">+ Add Language</button>
                            </div>
                            <div id="languages-list" class="flex flex-wrap gap-2">
                                <div id="languages-skeleton" class="flex flex-wrap gap-2 animate-pulse">
                                    <div class="h-8 bg-gray-200 rounded-full w-24"></div>
                                    <div class="h-8 bg-gray-200 rounded-full w-28"></div>
                                    <div class="h-8 bg-gray-200 rounded-full w-20"></div>
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
                                <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm">No hobbies added</span>
                            </div>
                            <div id="hobbies-edit" class="hidden mt-4">
                                <textarea id="hobbies-input" rows="3" placeholder="Enter hobbies separated by commas (e.g., Photography, Hiking, Reading)"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                <div class="flex space-x-3 mt-4">
                                    <button onclick="saveHobbies()" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">Save</button>
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
                                <button onclick="openCertificationModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">+ Add Certification</button>
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
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <h2 class="text-xl font-bold text-gray-900">References</h2>
                                </div>
                                <button onclick="openReferenceModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">+ Add Reference</button>
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
                            <div id="cv-document" class="space-y-3">
                                <div id="cv-document-item" class="hidden flex items-center justify-between p-3 bg-gray-50 rounded-lg gap-4">
                                    <div class="flex items-center space-x-3 min-w-0 flex-1">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-medium text-gray-900 truncate" id="cv-filename" title="Resume.pdf">Resume.pdf</p>
                                            <p class="text-xs text-gray-500" id="cv-updated">Updated Jan 10, 2026</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 flex-shrink-0">
                                        <button onclick="previewFile('cv')" class="text-blue-600 hover:text-blue-700 text-sm font-medium px-3 py-1 rounded hover:bg-blue-50 transition whitespace-nowrap">Preview</button>
                                        <a href="#" id="cv-view-link" target="_blank" download class="text-gray-600 hover:text-gray-700 text-sm font-medium px-3 py-1 rounded hover:bg-gray-100 transition inline-flex items-center whitespace-nowrap">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Download
                                        </a>
                                    </div>
                                </div>
                                <input type="file" id="cv_file" name="cv" accept=".pdf,.doc,.docx" class="hidden">
                                <button id="cv-upload-btn" onclick="document.getElementById('cv_file').click()" 
                                    class="w-full border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-500 hover:bg-blue-50 transition flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Upload New Resume</span>
                                </button>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Social Links</h3>
                            <div class="space-y-3">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                    <input type="url" id="linkedin_url" placeholder="linkedin.com/in/username"
                                        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                    <input type="url" id="website_url" placeholder="yourwebsite.com"
                                        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <button onclick="saveSocialLinks()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">Save Links</button>
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
                                <button onclick="saveVisibility()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition mt-4">Save Settings</button>
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
