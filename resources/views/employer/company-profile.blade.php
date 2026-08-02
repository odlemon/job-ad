@extends('layouts.employer')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-800">
    @include('partials.employer-navbar')

    <div class="flex">
        @include('partials.employer-sidebar')

        <!-- Main Content: match Job Listings spacing (end-to-end with small margin, subtle radius) -->
        <main class="flex-1 p-6 ml-64 w-0 min-w-0">
            <div class="w-full relative">
                <!-- Single white block: banner + form, same width behavior as Job Listings -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm dark:shadow-none overflow-hidden rounded-md">
                    <!-- Banner: blue gradient (200px) -->
                    <div class="relative overflow-hidden" style="height: 200px;">
                        <div id="cover-image-container" class="absolute inset-0 bg-gradient-to-r from-sky-400 via-blue-500 to-blue-700">
                            @if($company->cover_image)
                                @php
                                    $coverImageUrl = $company->cover_image;
                                    if (!str_starts_with($coverImageUrl, 'http')) {
                                        $coverImageUrl = Storage::url($coverImageUrl);
                                    }
                                @endphp
                                <img src="{{ $coverImageUrl }}" alt="Cover" class="w-full h-full object-cover" id="cover-image-img">
                            @endif
                        </div>
                        <div id="cover-image-loading" class="hidden absolute inset-0 bg-black/50 flex items-center justify-center">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto mb-2"></div>
                                <p class="text-white font-medium">Uploading cover image...</p>
                            </div>
                        </div>
                        <button type="button" id="cover-change-btn" onclick="document.getElementById('cover_image_file').click()" class="absolute top-4 right-6 bg-sky-300 hover:bg-sky-200 text-white px-4 py-2 rounded-md flex items-center gap-2 text-sm font-medium transition shadow">
                            <svg id="cover-camera-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <svg id="cover-loading-icon" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Change Cover</span>
                        </button>
                    </div>

                    <!-- White content area: edit button top-right, light grey input boxes -->
                    <div id="company-info-section" class="px-8 pt-24 pb-8 relative bg-white dark:bg-gray-800">
                    <button type="button" id="company-edit-btn" class="absolute top-6 right-8 w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center shadow hover:bg-blue-700 transition" title="Edit company information">
                        <svg id="company-edit-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828L15.586 6.586z"></path></svg>
                    </button>
                    <div id="company-info-skeleton" class="hidden animate-pulse">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4"><div class="space-y-4"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28"></div><div class="h-10 bg-gray-200 dark:bg-gray-700 rounded-md"></div></div><div class="space-y-4"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28"></div><div class="h-10 bg-gray-200 dark:bg-gray-700 rounded-md"></div></div></div>
                    </div>
                    <div id="company-info-content">
                        <form id="companyInfoForm" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-5">
                                <div class="space-y-5">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1.5">Company Name</label>
                                        <input type="text" id="name" name="name" required value="{{ old('name', $company->name) }}" class="w-full border border-gray-200 dark:border-gray-700 rounded-md px-4 py-2.5 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white" placeholder="TechCorp Inc.">
                                    </div>
                                    <div>
                                        <label for="industry" class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1.5">Industry</label>
                                        <div class="relative">
                                            <select id="industry" name="industry" class="w-full border border-gray-200 dark:border-gray-700 rounded-md px-4 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white">
                                                <option value="">Select industry</option>
                                                <option value="Technology" {{ old('industry', $company->industry) == 'Technology' ? 'selected' : '' }}>Technology</option>
                                                <option value="Finance" {{ old('industry', $company->industry) == 'Finance' ? 'selected' : '' }}>Finance</option>
                                                <option value="Healthcare" {{ old('industry', $company->industry) == 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                                                <option value="Education" {{ old('industry', $company->industry) == 'Education' ? 'selected' : '' }}>Education</option>
                                                <option value="Retail" {{ old('industry', $company->industry) == 'Retail' ? 'selected' : '' }}>Retail</option>
                                                <option value="Manufacturing" {{ old('industry', $company->industry) == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                                <option value="Other" {{ old('industry', $company->industry) == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-700 dark:text-gray-300 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1.5">Company Size & Working Hours</label>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="relative">
                                                <select id="size" name="size" class="w-full border border-gray-200 dark:border-gray-700 rounded-md px-4 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white">
                                                    <option value="">Select size</option>
                                                    <option value="1-10 employees" {{ old('size', $company->size) == '1-10 employees' ? 'selected' : '' }}>1-10 employees</option>
                                                    <option value="11-50 employees" {{ old('size', $company->size) == '11-50 employees' ? 'selected' : '' }}>11-50 employees</option>
                                                    <option value="51-200 employees" {{ old('size', $company->size) == '51-200 employees' ? 'selected' : '' }}>51-200 employees</option>
                                                    <option value="201-500 employees" {{ old('size', $company->size) == '201-500 employees' ? 'selected' : '' }}>201-500 employees</option>
                                                    <option value="501-1000 employees" {{ old('size', $company->size) == '501-1000 employees' ? 'selected' : '' }}>501-1000 employees</option>
                                                    <option value="1000+ employees" {{ old('size', $company->size) == '1000+ employees' ? 'selected' : '' }}>1000+ employees</option>
                                                </select>
                                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-700 dark:text-gray-300 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                            <div class="hidden">
                                                <label for="working_hours_legacy" class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1.5 md:mb-0">Working Hours (legacy)</label>
                                                <input type="text" id="working_hours_legacy" name="working_hours_legacy" value="{{ old('working_hours', $company->working_hours) }}" class="w-full border border-gray-200 dark:border-gray-700 rounded-md px-4 py-2.5 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white" placeholder="e.g. Mon–Fri, 9:00am – 5:30pm">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-5">
                                    <div>
                                        <label for="location" class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1.5">Location</label>
                                        <div class="relative">
                                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <input type="text" id="location" name="location" value="{{ old('location', $company->location) }}" class="w-full border border-gray-200 dark:border-gray-700 rounded-md pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white" placeholder="San Francisco, CA">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="founded_year" class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1.5">Founded Year</label>
                                        <div class="relative">
                                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <input type="number" id="founded_year" name="founded_year" value="{{ old('founded_year', $company->founded_year) }}" class="w-full border border-gray-200 dark:border-gray-700 rounded-md pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white w-32" placeholder="2015" min="1800" max="{{ date('Y') }}">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="working_hours" class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1.5">Working Hours</label>
                                        <input type="text" id="working_hours" name="working_hours" value="{{ old('working_hours', $company->working_hours) }}" class="w-full border border-gray-200 dark:border-gray-700 rounded-md px-4 py-2.5 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white" placeholder="e.g. Mon–Fri, 9:00am – 5:30pm (flexible)">
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4 space-y-5 border-t border-gray-100 dark:border-gray-700">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1.5">Company Description</label>
                                        <textarea id="description" name="description" rows="5" class="w-full border border-gray-200 dark:border-gray-700 rounded-md px-4 py-3 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white resize-y" placeholder="TechCorp is a leading technology company specializing in innovative software solutions. We're passionate about building products that make a difference.">{{ old('description', $company->description) }}</textarea>
                                    </div>
                                    <div>
                                        <label for="workplace_description" class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1.5">Workplace & Environment</label>
                                        <textarea id="workplace_description" name="workplace_description" rows="5" class="w-full border border-gray-200 dark:border-gray-700 rounded-md px-4 py-3 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white resize-y" placeholder="Describe the office layout, remote culture, collaboration style, perks, etc.">{{ old('workplace_description', $company->workplace_description) }}</textarea>
                                    </div>
                                </div>

                                <div class="pt-2">
                                    @php
                                            // Normalise selected benefits from stored value (JSON array or comma-separated string)
                                            $selectedBenefits = [];
                                            $rawBenefits = old('culture_benefits', $company->culture_benefits);
                                            if (is_array($rawBenefits)) {
                                                $selectedBenefits = $rawBenefits;
                                            } elseif (is_string($rawBenefits) && $rawBenefits !== '') {
                                                $decoded = json_decode($rawBenefits, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                    $selectedBenefits = $decoded;
                                                } else {
                                                    $selectedBenefits = array_filter(array_map('trim', explode(',', $rawBenefits)));
                                                }
                                            }

                                            $allBenefits = [
                                                // User-provided benefits
                                                'Competitive Salary' => 'Industry-leading compensation packages with performance bonuses.',
                                                'Health Insurance' => 'Comprehensive medical, dental, and vision coverage.',
                                                'Learning & Development' => 'Training programs, workshops, and educational assistance.',
                                                'Work-Life Balance' => 'Flexible working hours and remote work options.',
                                                'Paid Time Off' => 'Generous vacation days, sick leave, and public holidays.',
                                                'Employee Wellness' => 'Gym memberships, wellness programs, and mental health support.',
                                                // Additional curated benefits
                                                'Remote Work & Hybrid Options' => 'Work from anywhere or choose a hybrid schedule.',
                                                'Retirement & Pension Plan' => 'Company-supported retirement savings and pension plans.',
                                                'Performance Bonuses' => 'Regular performance-based bonuses and rewards.',
                                                'Stock Options & Equity' => 'Opportunities to share in the company’s long‑term growth.',
                                                'Parental Leave & Family Support' => 'Paid parental leave and family-friendly policies.',
                                                'Team Events & Retreats' => 'Regular team-building events, offsites, and retreats.',
                                            ];
                                        @endphp

                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1.5">Benefits</label>
                                    <input type="hidden" id="culture_benefits" name="culture_benefits" value='@json($selectedBenefits)'>

                                    <div id="benefits-options" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($allBenefits as $label => $description)
                                            @php
                                                $isSelected = in_array($label, $selectedBenefits ?? []);
                                            @endphp
                                            <button
                                                type="button"
                                                class="benefit-option flex items-start gap-3 p-3 rounded-lg border text-left transition focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500
                                                    {{ $isSelected ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-400' : 'border-gray-200 bg-white dark:bg-gray-800 hover:border-blue-400 hover:bg-blue-50' }}"
                                                data-benefit-label="{{ $label }}"
                                            >
                                                <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $label }}</div>
                                                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $description }}</div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Select up to <span class="font-semibold">6</span> benefits.
                                        </p>
                                        <p id="benefits-count" class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ count($selectedBenefits) }}/6 selected
                                        </p>
                                    </div>
                                    <p id="benefits-error" class="mt-1 text-xs text-red-500 hidden">
                                        You can select up to 6 benefits only.
                                    </p>
                                </div>
                                <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Social Links</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label for="website" class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1.5">Website</label>
                                            <div class="relative">
                                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                                <input type="url" id="website" name="website" value="{{ old('website', $company->website) }}" class="w-full border border-gray-200 dark:border-gray-700 rounded-md pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white" placeholder="https://techcorp.com">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="linkedin" class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1.5">LinkedIn</label>
                                            <input type="url" id="linkedin" name="linkedin" value="{{ old('linkedin', $company->linkedin) }}" class="w-full border border-gray-200 dark:border-gray-700 rounded-md px-4 py-2.5 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white" placeholder="https://linkedin.com/company/techcorp">
                                        </div>
                                        <div>
                                            <label for="facebook" class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1.5">Facebook</label>
                                            <input type="url" id="facebook" name="facebook" value="{{ old('facebook', $company->facebook) }}" class="w-full border border-gray-200 dark:border-gray-700 rounded-md px-4 py-2.5 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white" placeholder="https://facebook.com/yourcompany">
                                        </div>
                                        <div>
                                            <label for="twitter" class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1.5">Twitter</label>
                                            <input type="url" id="twitter" name="twitter" value="{{ old('twitter', $company->twitter) }}" class="w-full border border-gray-200 dark:border-gray-700 rounded-md px-4 py-2.5 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white" placeholder="https://twitter.com/techcorp">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" id="save-company-info-btn" onclick="saveCompanyInfo()" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-md hover:from-blue-600 hover:to-cyan-500 shadow-md transition font-medium flex items-center gap-2">
                                    <svg id="save-info-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <svg id="save-info-loading" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span id="save-info-text">Save Changes</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>

                <!-- Company logo: full circle visible, straddling banner and white area (outside card so not clipped) -->
                <div class="absolute left-8 z-10" style="top: 144px;">
                    <div class="relative inline-block">
                        <div id="logo-container" class="relative w-28 h-28 rounded-full bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center border-4 border-white shadow-lg overflow-hidden">
                            @if($company->logo)
                                @php
                                    $logoUrl = $company->logo;
                                    if (!str_starts_with($logoUrl, 'http')) {
                                        $logoUrl = Storage::url($logoUrl);
                                    }
                                @endphp
                                <img src="{{ $logoUrl }}" alt="Logo" class="w-full h-full object-cover" id="logo-img">
                            @else
                                <span class="text-white text-3xl font-bold uppercase" id="logo-placeholder">{{ strtoupper(substr($company->name ?? 'C', 0, 2)) }}</span>
                            @endif
                            <div id="logo-loading-overlay" class="hidden absolute inset-0 rounded-full bg-black/60 flex items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
                            </div>
                        </div>
                        <button type="button" id="logo-change-btn" onclick="document.getElementById('logo_file').click()" class="absolute -right-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center border-2 border-white shadow hover:bg-blue-700 transition">
                            <svg id="logo-edit-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            <svg id="logo-loading-icon" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></button>
                    </div>
                </div>

                <!-- Gallery + Verification row -->
                <div class="mt-8 mb-8 grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
                    <!-- Company Gallery Section (left, half width) -->
                    <div id="gallery-section" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm dark:shadow-none rounded-md p-8 flex flex-col">
                        <!-- Skeleton Loader -->
                        <div id="gallery-skeleton" class="hidden animate-pulse">
                            <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-48 mb-4"></div>
                            <div class="grid grid-cols-3 gap-4 mb-4">
                                @for($i = 0; $i < 6; $i++)
                                    <div class="aspect-square bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                                @endfor
                            </div>
                            <div class="h-12 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                        </div>
                        
                        <!-- Content -->
                        <div id="gallery-content" class="flex-1 flex flex-col">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Company Gallery</h2>
                            <div id="gallery-grid" class="grid grid-cols-3 gap-4 mb-4">
                                @php
                                    $gallery = is_array($company->gallery_images) ? $company->gallery_images : (is_string($company->gallery_images) ? json_decode($company->gallery_images, true) : []);
                                    $gallery = $gallery ?: [];
                                @endphp
                                @foreach($gallery as $index => $image)
                                    @php
                                        $mediaBaseUrl = $mediaBaseUrl ?? env('MEDIA_BASE_URL', 'http://31.220.82.129/uploads');
                                        // Handle both old local storage paths and new remote paths
                                        if (str_starts_with($image, 'http')) {
                                            $imageUrl = $image;
                                        } elseif (str_starts_with($image, 'company-gallery/')) {
                                            // New remote server path
                                            $imageUrl = $mediaBaseUrl . '/' . $image;
                                        } elseif (str_starts_with($image, 'companies/gallery/')) {
                                            // Old local storage path
                                            $imageUrl = asset('storage/' . $image);
                                        } else {
                                            // Default to remote server
                                            $imageUrl = $mediaBaseUrl . '/' . $image;
                                        }
                                    @endphp
                                    <div class="relative aspect-square rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700 group">
                                        <img src="{{ $imageUrl }}" alt="Gallery {{ $index+1 }}" class="w-full h-full object-cover">
                                        <button onclick="deleteGalleryImage({{ $index }})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition opacity-0 group-hover:opacity-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                                
                                @if(count($gallery) < 6)
                                    @for($i = count($gallery); $i < 6; $i++)
                                        <div class="aspect-square border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg flex items-center justify-center bg-gray-50 dark:bg-gray-900">
                                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                    @endfor
                                @endif
                            </div>
                            <button type="button" id="gallery-upload-btn" onclick="document.getElementById('gallery_images_file').click()" class="w-full border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center hover:border-blue-500 hover:bg-blue-50 transition flex items-center justify-center space-x-2 mt-auto">
                                <svg id="gallery-upload-icon" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <svg id="gallery-loading-icon" class="w-5 h-5 text-gray-400 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Upload Photos</span>
                            </button>
                        </div>
                    </div>

                    <!-- Verification Status Section (right, half width) -->
                    <div id="verification-section" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm dark:shadow-none rounded-md p-8 flex flex-col">
                        <!-- Skeleton Loader -->
                        <div id="verification-skeleton" class="hidden animate-pulse">
                            <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-48 mb-4"></div>
                            <div class="h-24 bg-gray-200 dark:bg-gray-700 rounded mb-4"></div>
                            <div class="space-y-2">
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div id="verification-content" class="flex-1 flex flex-col">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Verification Status</h2>
                            @if($company->verified_at)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-white">Company Verified</div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                                    Verified on {{ $company->verified_at->format('F d, Y') }} at {{ $company->verified_at->format('g:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-green-600 font-medium text-sm">Verified</span>
                                    </div>
                                </div>
                            @else
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-white">Not Verified</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Your company is not yet verified</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="mt-2">
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Benefits of Verification</h3>
                                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Verified badge on job postings
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Higher visibility in search
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Increased applicant trust
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Hidden file inputs -->
<input type="file" id="logo_file" name="logo" accept="image/jpeg,image/jpg,image/png,image/webp" class="hidden">
<input type="file" id="cover_image_file" name="cover_image" accept="image/jpeg,image/jpg,image/png,image/webp" class="hidden">
<input type="file" id="gallery_images_file" name="gallery_images[]" accept="image/jpeg,image/jpg,image/png,image/webp" multiple class="hidden">

<script>
// Show skeleton loaders on page load
document.addEventListener('DOMContentLoaded', function() {
    // Show skeletons initially
    document.getElementById('company-info-skeleton').classList.remove('hidden');
    document.getElementById('company-info-content').classList.add('hidden');
    document.getElementById('gallery-skeleton').classList.remove('hidden');
    document.getElementById('gallery-content').classList.add('hidden');
    document.getElementById('verification-skeleton').classList.remove('hidden');
    document.getElementById('verification-content').classList.add('hidden');
    
    // Load sections individually with delay
    setTimeout(() => {
        loadCompanyInfo();
    }, 500);
    
    setTimeout(() => {
        loadGallery();
    }, 800);
    
    setTimeout(() => {
        loadVerification();
    }, 600);
});

function loadCompanyInfo() {
    document.getElementById('company-info-skeleton').classList.add('hidden');
    document.getElementById('company-info-content').classList.remove('hidden');
}

async function loadGallery() {
    try {
        // Fetch company data to get gallery images
        const response = await fetch('{{ route("employer.company-profile") }}', {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.company && data.company.gallery_images !== undefined) {
                let galleryImages = data.company.gallery_images;
                
                // Handle different formats
                if (typeof galleryImages === 'string') {
                    try {
                        galleryImages = JSON.parse(galleryImages);
                    } catch (e) {
                        galleryImages = [];
                    }
                }
                if (!Array.isArray(galleryImages)) {
                    galleryImages = [];
                }
                
                // Get media base URL from response or use default
                const mediaBaseUrl = data.mediaBaseUrl || '{{ $mediaBaseUrl ?? env("MEDIA_BASE_URL", "http://31.220.82.129/uploads") }}';
                
                // Update gallery grid with fetched data
                updateGalleryGrid(galleryImages, mediaBaseUrl);
                return;
            }
        }
        
        // Fallback: just show the server-rendered content
        document.getElementById('gallery-skeleton').classList.add('hidden');
        document.getElementById('gallery-content').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading gallery:', error);
        // On error, just show the server-rendered content
        document.getElementById('gallery-skeleton').classList.add('hidden');
        document.getElementById('gallery-content').classList.remove('hidden');
    }
}

function loadVerification() {
    document.getElementById('verification-skeleton').classList.add('hidden');
    document.getElementById('verification-content').classList.remove('hidden');
}

// Handle logo upload with loading state
document.getElementById('logo_file').addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
        const file = e.target.files[0];
        const reader = new FileReader();
        
        // Show preview
        reader.onload = function(e) {
            const logoContainer = document.getElementById('logo-container');
            const placeholder = document.getElementById('logo-placeholder');
            let logoImg = document.getElementById('logo-img');
            
            if (!logoImg) {
                logoImg = document.createElement('img');
                logoImg.id = 'logo-img';
                logoImg.className = 'w-full h-full rounded-full object-cover';
                logoImg.alt = 'Logo';
                logoContainer.appendChild(logoImg);
            }
            
            logoImg.src = e.target.result;
            if (placeholder) placeholder.classList.add('hidden');
            logoImg.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
        
        // Show loading overlay
        document.getElementById('logo-loading-overlay').classList.remove('hidden');
        document.getElementById('logo-edit-icon').classList.add('hidden');
        document.getElementById('logo-loading-icon').classList.remove('hidden');
        document.getElementById('logo-change-btn').disabled = true;
        
        const formData = new FormData();
        formData.append('logo', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        fetch('{{ route("employer.company-profile.update") }}', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(async response => {
            // Check if response is ok
            if (!response.ok) {
                // Try to parse error response
                let errorData;
                try {
                    errorData = await response.json();
                } catch (e) {
                    // If not JSON, get text
                    const text = await response.text();
                    throw new Error(text || 'Upload failed');
                }
                throw new Error(errorData.message || 'Upload failed');
            }
            return response.json();
        })
        .then(data => {
            if (data.company && data.company.logo) {
                const logoImg = document.getElementById('logo-img');
                const placeholder = document.getElementById('logo-placeholder');
                if (logoImg) {
                    // Use the URL directly from the response (already full URL)
                    logoImg.src = data.company.logo;
                    logoImg.classList.remove('hidden');
                    if (placeholder) placeholder.classList.add('hidden');
                } else {
                    // Create image element if it doesn't exist
                    const logoContainer = document.getElementById('logo-container');
                    if (logoContainer) {
                        const newImg = document.createElement('img');
                        newImg.id = 'logo-img';
                        newImg.className = 'w-full h-full rounded-full object-cover';
                        newImg.alt = 'Logo';
                        newImg.src = data.company.logo;
                        logoContainer.appendChild(newImg);
                        if (placeholder) placeholder.classList.add('hidden');
                    }
                }
                if (typeof window.showSuccessToast === 'function') {
                    window.showSuccessToast('Logo updated successfully');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof window.showErrorToast === 'function') {
                window.showErrorToast(error.message || 'Failed to upload logo');
            } else {
                alert('Error: ' + (error.message || 'Failed to upload logo'));
            }
        })
        .finally(() => {
            document.getElementById('logo-loading-overlay').classList.add('hidden');
            document.getElementById('logo-edit-icon').classList.remove('hidden');
            document.getElementById('logo-loading-icon').classList.add('hidden');
            document.getElementById('logo-change-btn').disabled = false;
        });
    }
});

// Handle cover image upload with loading state
document.getElementById('cover_image_file').addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
        const file = e.target.files[0];
        const reader = new FileReader();
        
        // Show preview
        reader.onload = function(e) {
            const container = document.getElementById('cover-image-container');
            let coverImg = document.getElementById('cover-image-img');
            
            if (!coverImg) {
                coverImg = document.createElement('img');
                coverImg.id = 'cover-image-img';
                coverImg.className = 'w-full h-full object-cover';
                coverImg.alt = 'Cover';
                container.appendChild(coverImg);
            }
            
            coverImg.src = e.target.result;
            coverImg.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
        
        // Show loading state
        document.getElementById('cover-image-loading').classList.remove('hidden');
        document.getElementById('cover-camera-icon').classList.add('hidden');
        document.getElementById('cover-loading-icon').classList.remove('hidden');
        document.getElementById('cover-change-btn').disabled = true;
        
        const formData = new FormData();
        formData.append('cover_image', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        fetch('{{ route("employer.company-profile.update") }}', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(async response => {
            // Check if response is ok
            if (!response.ok) {
                // Try to parse error response
                let errorData;
                try {
                    errorData = await response.json();
                } catch (e) {
                    // If not JSON, get text
                    const text = await response.text();
                    throw new Error(text || 'Upload failed');
                }
                throw new Error(errorData.message || 'Upload failed');
            }
            return response.json();
        })
        .then(data => {
            if (data.company && data.company.cover_image) {
                const coverImg = document.getElementById('cover-image-img');
                const container = document.getElementById('cover-image-container');
                if (coverImg) {
                    // Use the URL directly from the response (already full URL)
                    coverImg.src = data.company.cover_image;
                    coverImg.classList.remove('hidden');
                } else if (container) {
                    // Create image element if it doesn't exist
                    const newImg = document.createElement('img');
                    newImg.id = 'cover-image-img';
                    newImg.className = 'w-full h-full object-cover';
                    newImg.alt = 'Cover';
                    newImg.src = data.company.cover_image;
                    container.appendChild(newImg);
                }
                if (typeof window.showSuccessToast === 'function') {
                    window.showSuccessToast('Cover image updated successfully');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof window.showErrorToast === 'function') {
                window.showErrorToast(error.message || 'Failed to upload cover image');
            } else {
                alert('Error: ' + (error.message || 'Failed to upload cover image'));
            }
        })
        .finally(() => {
            document.getElementById('cover-image-loading').classList.add('hidden');
            document.getElementById('cover-camera-icon').classList.remove('hidden');
            document.getElementById('cover-loading-icon').classList.add('hidden');
            document.getElementById('cover-change-btn').disabled = false;
        });
    }
});

// Handle gallery upload with loading state
document.getElementById('gallery_images_file').addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
        const files = Array.from(e.target.files);
        
        // Show loading state
        document.getElementById('gallery-upload-icon').classList.add('hidden');
        document.getElementById('gallery-loading-icon').classList.remove('hidden');
        document.getElementById('gallery-upload-btn').disabled = true;
        
        // Show skeleton loader for gallery section
        document.getElementById('gallery-skeleton').classList.remove('hidden');
        document.getElementById('gallery-content').classList.add('hidden');
        
        const formData = new FormData();
        files.forEach(file => {
            formData.append('images[]', file);
        });
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        fetch('{{ route("employer.company-profile.upload-gallery") }}', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Upload response:', data);
            if (data.images || data.gallery_images) {
                // Get updated gallery images
                let galleryImages = data.gallery_images || data.images || [];
                
                // Handle string format (JSON string)
                if (typeof galleryImages === 'string') {
                    try {
                        galleryImages = JSON.parse(galleryImages);
                    } catch (e) {
                        console.error('Failed to parse gallery_images:', e);
                        galleryImages = [];
                    }
                }
                
                if (!Array.isArray(galleryImages)) {
                    console.error('gallery_images is not an array:', galleryImages);
                    galleryImages = [];
                }
                
                console.log('Gallery images to display:', galleryImages);
                console.log('Gallery images count:', galleryImages.length);
                
                // Get media base URL from response or use default
                const mediaBaseUrl = data.mediaBaseUrl || '{{ $mediaBaseUrl ?? env("MEDIA_BASE_URL", "http://31.220.82.129/uploads") }}';
                console.log('Media base URL:', mediaBaseUrl);
                
                // Update gallery grid
                updateGalleryGrid(galleryImages, mediaBaseUrl);
                
                if (typeof window.showSuccessToast === 'function') {
                    window.showSuccessToast('Gallery images uploaded successfully');
                } else {
                    alert('Gallery images uploaded successfully!');
                }
            } else {
                console.error('No gallery images in response:', data);
                alert('Upload successful but no gallery images returned. Please refresh the page.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof window.showErrorToast === 'function') {
                window.showErrorToast('Failed to upload gallery images');
            } else {
                alert('Failed to upload gallery images');
            }
            // Show content again on error
            document.getElementById('gallery-skeleton').classList.add('hidden');
            document.getElementById('gallery-content').classList.remove('hidden');
        })
        .finally(() => {
            document.getElementById('gallery-upload-icon').classList.remove('hidden');
            document.getElementById('gallery-loading-icon').classList.add('hidden');
            document.getElementById('gallery-upload-btn').disabled = false;
            // Reset file input
            e.target.value = '';
        });
    }
});

// Function to update gallery grid
function updateGalleryGrid(galleryImages, mediaBaseUrl = null) {
    console.log('updateGalleryGrid called with:', galleryImages, mediaBaseUrl);
    const galleryGrid = document.getElementById('gallery-grid');
    if (!galleryGrid) {
        console.error('Gallery grid element not found!');
        return;
    }
    
    // Use provided mediaBaseUrl or fallback to default
    if (!mediaBaseUrl) {
        mediaBaseUrl = '{{ $mediaBaseUrl ?? env("MEDIA_BASE_URL", "http://31.220.82.129/uploads") }}';
    }
    
    console.log('Using mediaBaseUrl:', mediaBaseUrl);
    console.log('Gallery images array length:', galleryImages ? galleryImages.length : 0);
    
    // Clear existing content
    galleryGrid.innerHTML = '';
    
    if (!galleryImages || galleryImages.length === 0) {
        console.log('No gallery images to display');
        // Add empty placeholders
        for (let i = 0; i < 6; i++) {
            const placeholderDiv = document.createElement('div');
            placeholderDiv.className = 'aspect-square border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg flex items-center justify-center bg-gray-50 dark:bg-gray-900';
            placeholderDiv.innerHTML = `
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            `;
            galleryGrid.appendChild(placeholderDiv);
        }
        // Hide skeleton, show content
        document.getElementById('gallery-skeleton').classList.add('hidden');
        document.getElementById('gallery-content').classList.remove('hidden');
        return;
    }
    
    // Add uploaded images
    galleryImages.forEach((image, index) => {
        const imageDiv = document.createElement('div');
        imageDiv.className = 'relative aspect-square group';
        
        // Build image URL - handle both old local storage and new remote server paths
        let imagePath = image;
        
        // If it's already a full URL (starts with http:// or https://), use it directly
        if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
            // Already a full URL from upload service, use as is
            // No modification needed
            console.log('Using full URL:', imagePath);
        } else if (imagePath.startsWith('company-gallery/')) {
            // New remote server path (relative path)
            // Try different possible media server URLs
            // First try the standard media base URL
            imagePath = mediaBaseUrl + '/' + imagePath;
            console.log('Constructed URL from path:', imagePath);
            
            // If mediaBaseUrl doesn't work, we might need to try port 3050
            // But for now, use the configured mediaBaseUrl
        } else if (imagePath.startsWith('companies/gallery/')) {
            // Old local storage path - use /storage/ prefix
            imagePath = '/storage/' + imagePath;
            console.log('Using local storage path:', imagePath);
        } else {
            // Default to remote server
            imagePath = mediaBaseUrl + '/' + imagePath;
            console.log('Default remote server path:', imagePath);
        }
        
        console.log('Adding gallery image:', image, '-> URL:', imagePath);
        
        imageDiv.innerHTML = `
            <div class="w-full h-full rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700">
                <img src="${imagePath}" alt="Gallery ${index + 1}" class="w-full h-full object-cover" onerror="console.error('Failed to load image:', '${imagePath}'); this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' viewBox=\\'0 0 24 24\\' fill=\\'none\\' stroke=\\'%23999\\'%3E%3Cpath d=\\'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z\\'/%3E%3Cpath d=\\'M15 13a3 3 0 11-6 0 3 3 0 016 0z\\'/%3E%3C/svg%3E';">
            </div>
            <button onclick="deleteGalleryImage(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition opacity-0 group-hover:opacity-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        galleryGrid.appendChild(imageDiv);
    });
    
    // Add empty placeholders if less than 6 images
    if (galleryImages.length < 6) {
        for (let i = galleryImages.length; i < 6; i++) {
            const placeholderDiv = document.createElement('div');
            placeholderDiv.className = 'aspect-square border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg flex items-center justify-center bg-gray-50 dark:bg-gray-900';
            placeholderDiv.innerHTML = `
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            `;
            galleryGrid.appendChild(placeholderDiv);
        }
    }
    
    // Hide skeleton, show content
    document.getElementById('gallery-skeleton').classList.add('hidden');
    document.getElementById('gallery-content').classList.remove('hidden');
}

let isCompanyEditing = false;

function setCompanyInfoEditable(editable) {
    const form = document.getElementById('companyInfoForm');
    if (!form) return;

    const fields = form.querySelectorAll('input, select, textarea');
    fields.forEach(field => {
        field.disabled = !editable;
        field.classList.toggle('bg-gray-50', editable);
        field.classList.toggle('bg-gray-100', !editable);
        field.classList.toggle('text-gray-900', editable);
        field.classList.toggle('text-gray-500', !editable);
        field.classList.toggle('cursor-not-allowed', !editable);
    });

    // Toggle benefits selector interactivity
    const benefitButtons = form.querySelectorAll('.benefit-option');
    benefitButtons.forEach(button => {
        button.classList.toggle('opacity-60', !editable);
        button.classList.toggle('cursor-not-allowed', !editable);
    });

    const saveButton = document.getElementById('save-company-info-btn');
    if (saveButton) {
        saveButton.disabled = !editable;
        saveButton.classList.toggle('opacity-60', !editable);
        saveButton.classList.toggle('cursor-not-allowed', !editable);
        saveButton.classList.toggle('hidden', !editable);
    }

    const editBtn = document.getElementById('company-edit-btn');
    if (editBtn) {
        editBtn.title = editable ? 'Cancel editing' : 'Edit company information';
    }
}

function initBenefitsSelector() {
    const container = document.getElementById('benefits-options');
    const hiddenInput = document.getElementById('culture_benefits');
    const countEl = document.getElementById('benefits-count');
    const errorEl = document.getElementById('benefits-error');
    const MAX_BENEFITS = 6;

    if (!container || !hiddenInput) return;

    let selected = [];
    if (hiddenInput.value) {
        try {
            const parsed = JSON.parse(hiddenInput.value);
            if (Array.isArray(parsed)) {
                selected = parsed;
            } else if (typeof parsed === 'string') {
                selected = parsed.split(',').map(s => s.trim()).filter(Boolean);
            }
        } catch (e) {
            selected = hiddenInput.value.split(',').map(s => s.trim()).filter(Boolean);
        }
    }

    function syncInput() {
        hiddenInput.value = JSON.stringify(selected);
        if (countEl) {
            countEl.textContent = `${selected.length}/${MAX_BENEFITS} selected`;
        }
    }

    container.querySelectorAll('[data-benefit-label]').forEach(button => {
        const label = button.getAttribute('data-benefit-label');
        if (selected.includes(label)) {
            button.classList.add('border-blue-500', 'bg-blue-50', 'ring-1', 'ring-blue-400');
            button.classList.remove('border-gray-200');
        }

        button.addEventListener('click', function () {
            if (!isCompanyEditing) {
                return;
            }

            const index = selected.indexOf(label);
            if (index >= 0) {
                selected.splice(index, 1);
                button.classList.remove('border-blue-500', 'bg-blue-50', 'ring-1', 'ring-blue-400');
                button.classList.add('border-gray-200');
                if (errorEl) errorEl.classList.add('hidden');
            } else {
                if (selected.length >= MAX_BENEFITS) {
                    if (errorEl) errorEl.classList.remove('hidden');
                    return;
                }
                selected.push(label);
                button.classList.add('border-blue-500', 'bg-blue-50', 'ring-1', 'ring-blue-400');
                button.classList.remove('border-gray-200');
                if (errorEl) errorEl.classList.add('hidden');
            }

            syncInput();
        });
    });

    syncInput();
}

function initCompanyInfoEditState() {
    // Default: read-only
    isCompanyEditing = false;
    setCompanyInfoEditable(false);

    initBenefitsSelector();

    const editBtn = document.getElementById('company-edit-btn');
    if (editBtn) {
        editBtn.addEventListener('click', function () {
            isCompanyEditing = !isCompanyEditing;
            setCompanyInfoEditable(isCompanyEditing);
        });
    }
}

document.addEventListener('DOMContentLoaded', initCompanyInfoEditState);

// Save company info only
async function saveCompanyInfo() {
    const form = document.getElementById('companyInfoForm');
    const formData = new FormData(form);
    const button = document.getElementById('save-company-info-btn');
    const saveIcon = document.getElementById('save-info-icon');
    const loadingIcon = document.getElementById('save-info-loading');
    const saveText = document.getElementById('save-info-text');
    
    // Show loading state
    saveIcon.classList.add('hidden');
    loadingIcon.classList.remove('hidden');
    saveText.textContent = 'Saving...';
    button.disabled = true;
    
    let success = false;

    try {
        const response = await fetch('{{ route("employer.company-profile.update") }}', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.message) {
            if (typeof window.showSuccessToast === 'function') {
                window.showSuccessToast('Company information saved successfully');
            } else {
                alert('Company information saved successfully!');
            }
            success = true;
        }
    } catch (error) {
        console.error('Error:', error);
        if (typeof window.showErrorToast === 'function') {
            window.showErrorToast('Failed to save company information');
        } else {
            alert('An error occurred while saving.');
        }
    } finally {
        // Reset button state
        saveIcon.classList.remove('hidden');
        loadingIcon.classList.add('hidden');
        saveText.textContent = 'Save Changes';
        button.disabled = false;

        // After successful save, return to read-only mode
        if (success) {
            isCompanyEditing = false;
            setCompanyInfoEditable(false);
        }
    }
}

// Delete gallery image
async function deleteGalleryImage(index) {
    const confirmed = await window.showConfirmDialog(
        'This image will be removed from your company gallery.',
        { title: 'Delete image?', confirmText: 'Delete', cancelText: 'Cancel' }
    );
    if (!confirmed) {
        return;
    }
    
    try {
        // Show skeleton loader
        document.getElementById('gallery-skeleton').classList.remove('hidden');
        document.getElementById('gallery-content').classList.add('hidden');
        
        const response = await fetch(`{{ route("employer.company-profile.delete-gallery", ":index") }}`.replace(':index', index), {
            method: 'DELETE',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });
        
        const data = await response.json();
        
        if (data.message && data.gallery_images) {
            // Get updated gallery from response
            let galleryImages = data.gallery_images;
            if (typeof galleryImages === 'string') {
                galleryImages = JSON.parse(galleryImages);
            }
            
            // Get media base URL from response or use default
            const mediaBaseUrl = data.mediaBaseUrl || '{{ $mediaBaseUrl ?? env("MEDIA_BASE_URL", "http://31.220.82.129/uploads") }}';
            
            // Update gallery grid
            updateGalleryGrid(galleryImages, mediaBaseUrl);
            
            if (typeof window.showSuccessToast === 'function') {
                window.showSuccessToast('Image deleted successfully');
            } else {
                alert('Image deleted successfully!');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        if (typeof window.showErrorToast === 'function') {
            window.showErrorToast('Failed to delete image');
        } else {
            alert('Failed to delete image');
        }
        // Show content again on error
        document.getElementById('gallery-skeleton').classList.add('hidden');
        document.getElementById('gallery-content').classList.remove('hidden');
    }
}
</script>
@endsection
