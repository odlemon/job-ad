@extends('layouts.job-seeker')

@section('content')
<div class="min-h-screen bg-gray-50 flex">
    <!-- Left Sidebar - Same as Dashboard -->
    <aside class="w-64 bg-white min-h-screen sticky top-0 border-r border-gray-200 flex flex-col">
        <div class="h-1 bg-gray-800"></div>
        
        <div class="px-6 py-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-bold text-blue-600">JobHub</div>
                    <div class="text-xs text-gray-500">Job Seeker Portal</div>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-4 space-y-1">
            <!-- Dashboard Overview -->
            <a href="/dashboard" wire:navigate class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span>Dashboard Overview</span>
            </a>

            <!-- My Profile (Active) -->
            <a href="/job-seeker/profile" wire:navigate class="flex items-center space-x-3 px-4 py-3 relative group">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-600 rounded-r-full"></div>
                <div class="absolute left-0 top-0 bottom-0 right-0 bg-blue-600 rounded-r-lg -z-10"></div>
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="text-white font-medium">My Profile</span>
            </a>

            <!-- Job Applications -->
            <a href="/job-seeker/applications" wire:navigate class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span>Job Applications</span>
            </a>

            <!-- Job Discovery -->
            <a href="/jobs" wire:navigate class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Job Discovery</span>
            </a>

            <!-- Companies -->
            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Companies</span>
            </a>

            <!-- Notifications -->
            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span>Notifications</span>
            </a>

            <!-- Career Tools -->
            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Career Tools</span>
            </a>

            <!-- Support -->
            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Support</span>
            </a>

            <!-- Settings -->
            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Settings</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col">
        <!-- Top Header Bar -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1 max-w-2xl">
                        <div class="relative">
                            <input type="text" placeholder="Search jobs, companies..." 
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 ml-6">
                        <button class="p-2 text-gray-600 hover:text-gray-900 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </button>
                        <button class="p-2 text-gray-600 hover:text-gray-900 transition relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <div class="relative" id="user-menu-container">
                            <button id="user-menu-button" class="flex items-center">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span id="user-initials" class="text-white text-sm font-semibold">U</span>
                                </div>
                            </button>
                            <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                <a href="/dashboard" wire:navigate class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                <a href="/job-seeker/profile" wire:navigate class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                                <div class="border-t border-gray-200 my-1"></div>
                                <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Profile Content -->
        <main class="flex-1 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <!-- Profile Header Banner -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-lg mb-8 p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-6">
                            <div class="relative">
                                <div id="profile-header-photo" class="w-24 h-24 rounded-full bg-white bg-opacity-20 flex items-center justify-center border-4 border-white border-opacity-30">
                                    <span id="profile-header-initials" class="text-3xl font-bold">JD</span>
                                </div>
                                <img id="profile-header-photo-img" src="" alt="Profile" class="w-24 h-24 rounded-full object-cover border-4 border-white border-opacity-30 hidden">
                                <button onclick="document.getElementById('profile_photo_file').click()" class="absolute bottom-0 right-0 bg-blue-500 rounded-full p-2 border-2 border-white">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                </button>
                            </div>
                            <div>
                                <h1 id="profile-header-name" class="text-3xl font-bold mb-1">John Doe</h1>
                                <p id="profile-header-title" class="text-blue-100 text-lg">Senior Software Engineer</p>
                            </div>
                        </div>
                        <button onclick="toggleEditMode()" id="edit-profile-btn" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span>Edit Profile</span>
                        </button>
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
                                <p class="text-gray-500 text-sm">No work experience added yet.</p>
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
                                <p class="text-gray-500 text-sm">No education added yet.</p>
                            </div>
                        </div>

                        <!-- Skills Section -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-gray-900">Skills</h2>
                                <button onclick="openSkillModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">+ Add Skill</button>
                            </div>
                            <div id="skills-list" class="flex flex-wrap gap-2">
                                <p class="text-gray-500 text-sm">No skills added yet.</p>
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
                                <p class="text-gray-500 text-sm">No languages added yet.</p>
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
                                <p class="text-gray-500 text-sm">No certifications added yet.</p>
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
                                <p class="text-gray-500 text-sm">No references added yet.</p>
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
                                    <svg class="transform -rotate-90 w-32 h-32">
                                        <circle cx="64" cy="64" r="56" stroke="#e5e7eb" stroke-width="8" fill="none"></circle>
                                        <circle cx="64" cy="64" r="56" stroke="#3b82f6" stroke-width="8" fill="none"
                                            stroke-dasharray="351.86" stroke-dashoffset="52.78" stroke-linecap="round"></circle>
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span class="text-2xl font-bold text-gray-900">85%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">✓ Basic Info</span>
                                    <span class="text-green-600 font-medium">Done</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">✓ Resume</span>
                                    <span class="text-green-600 font-medium">Done</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">• Portfolio</span>
                                    <span class="text-orange-600 font-medium">Pending</span>
                                </div>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Documents</h3>
                            <div id="cv-document" class="space-y-3">
                                <div id="cv-document-item" class="hidden flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900" id="cv-filename">Resume.pdf</p>
                                        <p class="text-xs text-gray-500" id="cv-updated">Updated Jan 10, 2026</p>
                                    </div>
                                    <a href="#" id="cv-view-link" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View</a>
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
<script>
    const API_BASE = '/api';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let editMode = false;
    let profileData = {};

    // Helper function to manage button loading states
    function setButtonLoading(button, isLoading, loadingText = 'Loading...', originalText = null) {
        if (!button) return;
        
        // Store original HTML content (not just text) to preserve structure
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
            
            // Store original width to prevent layout shift
            if (!button.dataset.originalWidth) {
                button.dataset.originalWidth = button.offsetWidth + 'px';
            }
            button.style.minWidth = button.dataset.originalWidth;
            
            // Show only spinner - centered, inherits button's text color
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
            
            // Restore original content
            const restored = button.dataset.originalContent || originalText;
            if (restored) {
                button.innerHTML = restored;
            }
            
            // Restore original classes if stored
            if (button.dataset.originalClasses) {
                button.className = button.dataset.originalClasses;
            }
            
            // Remove min-width constraint
            if (button.dataset.originalWidth) {
                button.style.minWidth = '';
                delete button.dataset.originalWidth;
            }
        }
    }

    function setupEventListeners() {
        // Profile photo upload
        document.getElementById('profile_photo_file').addEventListener('change', handleProfilePhotoUpload);
        
        // CV upload
        document.getElementById('cv_file').addEventListener('change', handleCvUpload);
        
        // Date of birth age calculation
        document.getElementById('date_of_birth').addEventListener('change', calculateAge);
        
        // Bio character counter
        document.getElementById('bio').addEventListener('input', function() {
            document.getElementById('bio-char-count').textContent = this.value.length;
        });
        
        // Personal info form
        document.getElementById('personalInfoForm').addEventListener('submit', savePersonalInfo);
        
        // User menu toggle
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenuDropdown = document.getElementById('user-menu-dropdown');
        if (userMenuButton && userMenuDropdown) {
            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userMenuDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                if (!userMenuButton.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                    userMenuDropdown.classList.add('hidden');
                }
            });
        }
    }

    async function loadProfile() {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (response.status === 401 || response.status === 403) {
                window.location.href = '/login';
                return;
            }

            if (response.ok) {
                const data = await response.json();
                profileData = data.data || data.job_seeker || {};
                
                // Mark profile as loaded
                const profileContent = document.querySelector('main');
                if (profileContent) {
                    profileContent.setAttribute('data-profile-loaded', 'true');
                }
                
                // Update header
                updateProfileHeader();
                
                // Update personal info form
                updatePersonalInfoForm();
                
                // Update about section
                updateAboutSection();
                
                // Update job preferences
                updateJobPreferences();
                
                // Update documents
                updateDocuments();
                
                // Update social links
                updateSocialLinks();
                
                // Update visibility
                updateVisibility();
            }
        } catch (error) {
            console.error('Error loading profile:', error);
        }
    }

    function updateProfileHeader() {
        const firstName = profileData.first_name || '';
        const lastName = profileData.last_name || '';
        const fullName = `${firstName} ${lastName}`.trim() || 'User';
        const initials = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase() || 'U';
        
        document.getElementById('profile-header-name').textContent = fullName;
        document.getElementById('profile-header-initials').textContent = initials;
        document.getElementById('user-initials').textContent = initials;
        
        if (profileData.profile_photo) {
            document.getElementById('profile-header-photo-img').src = profileData.profile_photo;
            document.getElementById('profile-header-photo-img').classList.remove('hidden');
            document.getElementById('profile-header-photo').classList.add('hidden');
        }
    }

    function updatePersonalInfoForm() {
        document.getElementById('first_name').value = profileData.first_name || '';
        document.getElementById('last_name').value = profileData.last_name || '';
        document.getElementById('email').value = profileData.user?.email || '';
        document.getElementById('phone').value = profileData.phone || '';
        document.getElementById('address').value = profileData.address || '';
        document.getElementById('gender').value = profileData.gender || '';
        document.getElementById('date_of_birth').value = profileData.date_of_birth || '';
        document.getElementById('employment_status').value = profileData.employment_status || '';
        document.getElementById('highest_education').value = profileData.highest_education || '';
        document.getElementById('driving_license').value = profileData.driving_license ? '1' : '0';
        document.getElementById('license_issued_date').value = profileData.license_issued_date || '';
        
        if (profileData.date_of_birth) {
            calculateAge();
        }
    }

    function updateAboutSection() {
        const bio = profileData.bio || '';
        document.getElementById('bio').value = bio;
        document.getElementById('bio-char-count').textContent = bio.length;
        document.getElementById('about-text').textContent = bio || 'No bio added yet.';
    }

    function updateJobPreferences() {
        const preferences = profileData.job_preferences || [];
        const displayDiv = document.getElementById('job-preferences-display');
        
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
        if (profileData.cv_file_path) {
            const filename = profileData.cv_file_path.split('/').pop() || 'Resume.pdf';
            const updatedDate = profileData.cv_uploaded_at ? new Date(profileData.cv_uploaded_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'Recently';
            document.getElementById('cv-filename').textContent = filename;
            document.getElementById('cv-updated').textContent = `Updated ${updatedDate}`;
            document.getElementById('cv-view-link').href = profileData.cv_file_path;
            cvDocumentItem.classList.remove('hidden');
        } else {
            cvDocumentItem.classList.add('hidden');
        }
    }

    function updateSocialLinks() {
        document.getElementById('linkedin_url').value = profileData.linkedin_url || '';
        document.getElementById('website_url').value = profileData.website_url || '';
    }

    function updateVisibility() {
        document.getElementById('public_profile').checked = profileData.public_profile !== false;
        document.getElementById('open_to_opportunities').checked = profileData.open_to_opportunities !== false;
    }

    function calculateAge() {
        const dob = document.getElementById('date_of_birth').value;
        if (dob) {
            const birthDate = new Date(dob);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            const formattedDate = birthDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            document.getElementById('age-display').textContent = `${formattedDate} (${age} years)`;
        } else {
            document.getElementById('age-display').textContent = '';
        }
    }

    async function handleProfilePhotoUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
            showErrorToast('File size must be less than 5MB');
            return;
        }

        const uploadButton = document.getElementById('profile-photo-upload-btn');
        const originalText = uploadButton ? uploadButton.innerHTML : '';

        const formData = new FormData();
        formData.append('profile_photo', file);

        try {
            if (uploadButton) setButtonLoading(uploadButton, true, '', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/profile/photo`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: formData
            });

            const data = await response.json();
            if (response.ok) {
                await loadProfile();
            } else {
                showErrorToast(data.message || 'Failed to upload photo');
            }
        } catch (error) {
            console.error('Error uploading photo:', error);
            showErrorToast('An error occurred');
        } finally {
            if (uploadButton) setButtonLoading(uploadButton, false, '', originalText);
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
            if (uploadButton) setButtonLoading(uploadButton, true, 'Uploading...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/profile/cv`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: formData
            });

            const data = await response.json();
            if (response.ok) {
                await loadProfile();
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

    async function savePersonalInfo(e) {
        e.preventDefault();
        const submitButton = e.target.querySelector('button[type="submit"]');
        const originalText = submitButton ? submitButton.textContent : 'Save Changes';

        const formData = {
            first_name: document.getElementById('first_name').value,
            last_name: document.getElementById('last_name').value,
            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value,
            gender: document.getElementById('gender').value,
            date_of_birth: document.getElementById('date_of_birth').value,
            employment_status: document.getElementById('employment_status').value,
            highest_education: document.getElementById('highest_education').value,
            driving_license: document.getElementById('driving_license').value === '1',
            license_issued_date: document.getElementById('license_issued_date').value,
        };

        try {
            if (submitButton) setButtonLoading(submitButton, true, 'Saving...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            if (response.ok) {
                await loadProfile();
                showSuccessToast('Profile updated successfully!');
            } else {
                showErrorToast(data.message || 'Failed to update profile');
            }
        } catch (error) {
            console.error('Error saving profile:', error);
            showErrorToast('An error occurred');
        } finally {
            if (submitButton) setButtonLoading(submitButton, false, '', originalText);
        }
    }

    function editAbout() {
        document.getElementById('about-display').classList.add('hidden');
        document.getElementById('about-edit').classList.remove('hidden');
    }

    function cancelAbout() {
        document.getElementById('about-display').classList.remove('hidden');
        document.getElementById('about-edit').classList.add('hidden');
        document.getElementById('bio').value = profileData.bio || '';
    }

    async function saveAbout() {
        const bio = document.getElementById('bio').value;
        const saveButton = document.querySelector('button[onclick="saveAbout()"]');
        const originalText = saveButton ? saveButton.textContent : 'Save';

        try {
            if (saveButton) setButtonLoading(saveButton, true, 'Saving...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({ bio })
            });

            const data = await response.json();
            if (response.ok) {
                await loadProfile();
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
        document.getElementById('job-preferences-display').classList.add('hidden');
        document.getElementById('job-preferences-edit').classList.remove('hidden');
        
        // Check existing preferences
        const preferences = profileData.job_preferences || [];
        document.querySelectorAll('input[name="job_preference"]').forEach(checkbox => {
            checkbox.checked = preferences.includes(checkbox.value);
        });
    }

    function cancelJobPreferences() {
        document.getElementById('job-preferences-display').classList.remove('hidden');
        document.getElementById('job-preferences-edit').classList.add('hidden');
    }

    async function saveJobPreferences() {
        const preferences = Array.from(document.querySelectorAll('input[name="job_preference"]:checked')).map(cb => cb.value);
        const saveButton = document.querySelector('button[onclick="saveJobPreferences()"]');
        const originalText = saveButton ? saveButton.textContent : 'Save';

        try {
            if (saveButton) setButtonLoading(saveButton, true, 'Saving...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({ job_preferences: preferences })
            });

            const data = await response.json();
            if (response.ok) {
                await loadProfile();
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
        const originalText = saveButton ? saveButton.textContent : 'Save Links';
        
        try {
            if (saveButton) setButtonLoading(saveButton, true, 'Saving...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({ linkedin_url, website_url })
            });

            const data = await response.json();
            if (response.ok) {
                await loadProfile();
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
        const originalText = saveButton ? saveButton.textContent : 'Save Settings';
        
        try {
            if (saveButton) setButtonLoading(saveButton, true, 'Saving...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({ public_profile, open_to_opportunities })
            });

            const data = await response.json();
            if (response.ok) {
                await loadProfile();
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
        // Toggle edit mode UI
    }

    // ========== Work Experience Functions ==========
    let experiences = [];
    let editingExperienceId = null;

    async function loadExperiences() {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/experiences`, {
                credentials: 'include',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
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
        document.getElementById('exp-job-title').value = exp?.job_title || '';
        document.getElementById('exp-company').value = exp?.company_name || '';
        document.getElementById('exp-location').value = exp?.location || '';
        document.getElementById('exp-start-date').value = exp?.start_date || '';
        document.getElementById('exp-end-date').value = exp?.end_date || '';
        document.getElementById('exp-is-current').checked = exp?.is_current || false;
        document.getElementById('exp-description').value = exp?.description || '';
        document.getElementById('experience-modal').classList.remove('hidden');
    }

    function closeExperienceModal() {
        document.getElementById('experience-modal').classList.add('hidden');
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
        const originalText = saveButton ? saveButton.textContent : 'Save';

        try {
            if (saveButton) setButtonLoading(saveButton, true, 'Saving...', originalText);
            
            const url = editingExperienceId 
                ? `${API_BASE}/job-seeker/experiences/${editingExperienceId}`
                : `${API_BASE}/job-seeker/experiences`;
            const method = editingExperienceId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify(data)
            });

            if (response.ok) {
                await loadExperiences();
                closeExperienceModal();
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
            if (deleteButton) setButtonLoading(deleteButton, true, 'Deleting...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/experiences/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (response.ok) {
                await loadExperiences();
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
    let educations = [];
    let editingEducationId = null;

    async function loadEducations() {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/educations`, {
                credentials: 'include',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
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
        document.getElementById('edu-degree').value = edu?.degree || '';
        document.getElementById('edu-institution').value = edu?.institution || '';
        document.getElementById('edu-location').value = edu?.location || '';
        document.getElementById('edu-start-date').value = edu?.start_date || '';
        document.getElementById('edu-end-date').value = edu?.end_date || '';
        document.getElementById('edu-gpa').value = edu?.gpa || '';
        document.getElementById('edu-gpa-scale').value = edu?.gpa_scale || '4.0';
        document.getElementById('edu-description').value = edu?.description || '';
        document.getElementById('education-modal').classList.remove('hidden');
    }

    function closeEducationModal() {
        document.getElementById('education-modal').classList.add('hidden');
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
        const originalText = saveButton ? saveButton.textContent : 'Save';

        try {
            if (saveButton) setButtonLoading(saveButton, true, 'Saving...', originalText);
            
            const url = editingEducationId 
                ? `${API_BASE}/job-seeker/educations/${editingEducationId}`
                : `${API_BASE}/job-seeker/educations`;
            const method = editingEducationId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify(data)
            });

            if (response.ok) {
                await loadEducations();
                closeEducationModal();
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
            if (deleteButton) setButtonLoading(deleteButton, true, 'Deleting...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/educations/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (response.ok) {
                await loadEducations();
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
    let skills = [];

    async function loadSkills() {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/skills`, {
                credentials: 'include',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
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
        document.getElementById('skill-name').value = '';
        document.getElementById('skill-proficiency').value = 'intermediate';
        document.getElementById('skill-modal').classList.remove('hidden');
    }

    function closeSkillModal() {
        document.getElementById('skill-modal').classList.add('hidden');
    }

    async function saveSkill() {
        const data = {
            skill_name: document.getElementById('skill-name').value,
            proficiency_level: document.getElementById('skill-proficiency').value,
        };

        const saveButton = document.querySelector('#skill-modal button[onclick="saveSkill()"]');
        const originalText = saveButton ? saveButton.textContent : 'Save';

        try {
            if (saveButton) setButtonLoading(saveButton, true, 'Saving...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/skills`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify(data)
            });

            if (response.ok) {
                await loadSkills();
                closeSkillModal();
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
            if (deleteButton) setButtonLoading(deleteButton, true, 'Deleting...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/skills/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (response.ok) {
                await loadSkills();
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
    let languages = [];

    async function loadLanguages() {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/languages`, {
                credentials: 'include',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
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
        document.getElementById('language-name').value = '';
        document.getElementById('language-proficiency').value = 'conversational';
        document.getElementById('language-modal').classList.remove('hidden');
    }

    function closeLanguageModal() {
        document.getElementById('language-modal').classList.add('hidden');
    }

    async function saveLanguage() {
        const data = {
            language: document.getElementById('language-name').value,
            proficiency_level: document.getElementById('language-proficiency').value,
        };

        const saveButton = document.querySelector('#language-modal button[onclick="saveLanguage()"]');
        const originalText = saveButton ? saveButton.textContent : 'Save';

        try {
            if (saveButton) setButtonLoading(saveButton, true, 'Saving...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/languages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
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
            if (deleteButton) setButtonLoading(deleteButton, true, 'Deleting...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/languages/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
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
    let certifications = [];
    let editingCertificationId = null;

    async function loadCertifications() {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/certifications`, {
                credentials: 'include',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
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
                        ${cert.certificate_file_path ? `<a href="${cert.certificate_file_path}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm font-medium mt-2 inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            View Certificate
                        </a>` : ''}
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
        document.getElementById('cert-name').value = cert?.certification_name || '';
        document.getElementById('cert-organization').value = cert?.issuing_organization || '';
        document.getElementById('cert-issue-date').value = cert?.issue_date || '';
        document.getElementById('cert-expiry-date').value = cert?.expiry_date || '';
        document.getElementById('cert-credential-id').value = cert?.credential_id || '';
        document.getElementById('cert-credential-url').value = cert?.credential_url || '';
        document.getElementById('cert-file').value = '';
        document.getElementById('certification-modal').classList.remove('hidden');
    }

    function closeCertificationModal() {
        document.getElementById('certification-modal').classList.add('hidden');
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
        if (fileInput.files[0]) {
            formData.append('certificate_file', fileInput.files[0]);
        }

        const saveButton = document.querySelector('#certification-modal button[onclick="saveCertification()"]');
        const originalText = saveButton ? saveButton.textContent : 'Save';

        try {
            if (saveButton) setButtonLoading(saveButton, true, 'Saving...', originalText);
            
            const url = editingCertificationId 
                ? `${API_BASE}/job-seeker/certifications/${editingCertificationId}`
                : `${API_BASE}/job-seeker/certifications`;
            const method = editingCertificationId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: formData
            });

            if (response.ok) {
                await loadCertifications();
                closeCertificationModal();
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
            if (deleteButton) setButtonLoading(deleteButton, true, 'Deleting...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/certifications/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (response.ok) {
                await loadCertifications();
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
    let references = [];
    let editingReferenceId = null;

    async function loadReferences() {
        try {
            const response = await fetch(`${API_BASE}/job-seeker/references`, {
                credentials: 'include',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
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
        document.getElementById('ref-name').value = ref?.reference_name || '';
        document.getElementById('ref-title').value = ref?.title || '';
        document.getElementById('ref-company').value = ref?.company || '';
        document.getElementById('ref-relationship').value = ref?.relationship || 'other';
        document.getElementById('ref-email').value = ref?.email || '';
        document.getElementById('ref-phone').value = ref?.phone || '';
        document.getElementById('ref-notes').value = ref?.notes || '';
        document.getElementById('reference-modal').classList.remove('hidden');
    }

    function closeReferenceModal() {
        document.getElementById('reference-modal').classList.add('hidden');
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
        const originalText = saveButton ? saveButton.textContent : 'Save';

        try {
            if (saveButton) setButtonLoading(saveButton, true, 'Saving...', originalText);
            
            const url = editingReferenceId 
                ? `${API_BASE}/job-seeker/references/${editingReferenceId}`
                : `${API_BASE}/job-seeker/references`;
            const method = editingReferenceId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
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
            if (deleteButton) setButtonLoading(deleteButton, true, 'Deleting...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/references/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
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
    let categoryPreferences = [];
    let allCategories = [];

    async function loadCategoryPreferences() {
        try {
            const [prefsResponse, catsResponse] = await Promise.all([
                fetch(`${API_BASE}/job-seeker/category-preferences`, {
                    credentials: 'include',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
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
        if (categoryPreferences.length === 0) {
            container.innerHTML = '<span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm">No categories selected</span>';
            document.getElementById('category-count').textContent = '0 of 6 categories selected';
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
        document.getElementById('category-count').textContent = `${categoryPreferences.length} of 6 categories selected`;
    }

    function populateCategorySelect() {
        const select = document.getElementById('category-select');
        const selectedIds = categoryPreferences.map(p => p.category_id);
        select.innerHTML = '<option value="">Select a category...</option>' + 
            allCategories.filter(cat => !selectedIds.includes(cat.id))
                .map(cat => `<option value="${cat.id}">${cat.name}</option>`)
                .join('');
    }

    function editJobDiscovery() {
        document.getElementById('category-preferences-display').classList.add('hidden');
        document.getElementById('category-preferences-edit').classList.remove('hidden');
        populateCategorySelect();
    }

    function cancelCategoryPreferences() {
        document.getElementById('category-preferences-display').classList.remove('hidden');
        document.getElementById('category-preferences-edit').classList.add('hidden');
    }

    async function addCategoryPreference() {
        const select = document.getElementById('category-select');
        const categoryId = select.value;
        if (!categoryId) {
            showWarningToast('Please select a category');
            return;
        }

        const addButton = select; // Using select as button indicator
        const originalValue = select.value;
        select.disabled = true;

        try {
            const response = await fetch(`${API_BASE}/job-seeker/category-preferences`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
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
            if (deleteButton) setButtonLoading(deleteButton, true, 'Removing...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/category-preferences/${categoryId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
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
        // This function is called when clicking Save, but we're using add/remove instead
        const saveButton = document.querySelector('button[onclick="saveCategoryPreferences()"]');
        const originalText = saveButton ? saveButton.textContent : 'Save';

        try {
            if (saveButton) setButtonLoading(saveButton, true, 'Saving...', originalText);
            // No API call needed as categories are added/removed individually
            await new Promise(resolve => setTimeout(resolve, 300)); // Small delay for UX
            cancelCategoryPreferences();
        } finally {
            if (saveButton) setButtonLoading(saveButton, false, '', originalText);
        }
    }

    // ========== Hobbies Functions ==========
    function editHobbies() {
        const hobbies = profileData.hobbies || [];
        document.getElementById('hobbies-input').value = hobbies.join(', ');
        document.getElementById('hobbies-display').classList.add('hidden');
        document.getElementById('hobbies-edit').classList.remove('hidden');
    }

    function cancelHobbies() {
        document.getElementById('hobbies-display').classList.remove('hidden');
        document.getElementById('hobbies-edit').classList.add('hidden');
    }

    async function saveHobbies() {
        const hobbiesText = document.getElementById('hobbies-input').value;
        const hobbies = hobbiesText.split(',').map(h => h.trim()).filter(h => h.length > 0);

        const saveButton = document.querySelector('button[onclick="saveHobbies()"]');
        const originalText = saveButton ? saveButton.textContent : 'Save';

        try {
            if (saveButton) setButtonLoading(saveButton, true, 'Saving...', originalText);
            
            const response = await fetch(`${API_BASE}/job-seeker/profile`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({ hobbies })
            });

            if (response.ok) {
                await loadProfile();
                cancelHobbies();
            } else {
                showErrorToast('Failed to save hobbies');
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
        if (hobbies.length === 0) {
            displayDiv.innerHTML = '<span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm">No hobbies added</span>';
        } else {
            displayDiv.innerHTML = hobbies.map(hobby => 
                `<span class="px-4 py-2 bg-pink-100 text-pink-700 rounded-full text-sm">${hobby}</span>`
            ).join('');
        }
    }

    // Update loadProfile to load all sections
    const originalLoadProfile = loadProfile;
    loadProfile = async function() {
        await originalLoadProfile();
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
    };

    // Make loadProfile globally available for wire:navigate (after it's been updated)
    window.loadProfile = loadProfile;

    // Initialize - execute immediately for initial page loads
    (function executeNow() {
        if (window.location.pathname === '/job-seeker/profile') {
            const profileElement = document.querySelector('main');
            if (profileElement && typeof window.loadProfile === 'function') {
                const isSkeleton = profileElement.innerHTML.includes('animate-pulse') || 
                                 profileElement.innerHTML.includes('Loading');
                const isLoaded = profileElement.getAttribute('data-profile-loaded') === 'true';
                if (isSkeleton || !isLoaded) {
                    window.loadProfile();
                }
            } else if (profileElement && typeof window.loadProfile !== 'function') {
                // Function not available yet, retry quickly
                setTimeout(executeNow, 10);
            }
        }
    })();

    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.pathname === '/job-seeker/profile') {
            window.loadProfile();
            setupEventListeners();
        }
    });

    // Update category select to add on change
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category-select');
        if (categorySelect) {
            categorySelect.addEventListener('change', function() {
                if (this.value) {
                    addCategoryPreference();
                }
            });
        }
    });

    // Reload on Livewire navigation
    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:navigated', function() {
            if (window.location.pathname === '/job-seeker/profile') {
                // Try multiple times with increasing delays to catch the function
                let attempts = 0;
                const maxAttempts = 10;
                
                function tryLoad() {
                    attempts++;
                    const profileElement = document.querySelector('main');
                    // Try both window.loadProfile and loadProfile (in case window.loadProfile isn't set yet)
                    const loadFn = typeof window.loadProfile === 'function' ? window.loadProfile : 
                                  (typeof loadProfile === 'function' ? loadProfile : null);
                    
                    if (profileElement && loadFn) {
                        const isSkeleton = profileElement.innerHTML.includes('animate-pulse') || 
                                         profileElement.innerHTML.includes('Loading');
                        const isLoaded = profileElement.getAttribute('data-profile-loaded') === 'true';
                        if (isSkeleton || !isLoaded) {
                            loadFn();
                            // Setup event listeners after a short delay to ensure DOM is ready
                            setTimeout(setupEventListeners, 100);
                            return; // Success, stop trying
                        }
                    }
                    
                    if (attempts < maxAttempts) {
                        setTimeout(tryLoad, 50 * attempts); // Increasing delay
                    }
                }
                
                setTimeout(tryLoad, 50);
            }
        });
    }
</script>

<!-- Modals -->
@include('job-seeker.profile-modals')

@endpush
@endsection
