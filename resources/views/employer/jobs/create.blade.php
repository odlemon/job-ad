@extends('layouts.employer')

@section('content')
<div class="min-h-screen bg-gray-50">
    @include('partials.employer-navbar')

    <div class="flex">
        @include('partials.employer-sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-8 ml-64">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <a href="{{ route('employer.jobs.index') }}" wire:navigate class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Job Listings
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Post New Job</h1>
                    <p class="text-gray-600 mt-2">Create a new job posting to attract qualified candidates</p>
                </div>

                <!-- Form -->
                <form action="{{ route('employer.jobs.store') }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                    @csrf

                    <!-- Basic Information -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
                        
                        <div class="space-y-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                                <input type="text" id="title" name="title" required value="{{ old('title') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., Senior Software Engineer">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select id="category_id" name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Job Description *</label>
                                <textarea id="description" name="description" required rows="8" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Describe the role, responsibilities, and what you're looking for...">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                                <textarea id="requirements" name="requirements" rows="6" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="List the required skills, qualifications, and experience...">{{ old('requirements') }}</textarea>
                                @error('requirements')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="benefits" class="block text-sm font-medium text-gray-700 mb-2">Benefits</label>
                                <textarea id="benefits" name="benefits" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="List the benefits and perks...">{{ old('benefits') }}</textarea>
                                @error('benefits')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Job Details</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                                <select id="employment_type" name="employment_type" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select type</option>
                                    <option value="full_time" {{ old('employment_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                    <option value="part_time" {{ old('employment_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                    <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                                    <option value="freelance" {{ old('employment_type') == 'freelance' ? 'selected' : '' }}>Freelance</option>
                                    <option value="internship" {{ old('employment_type') == 'internship' ? 'selected' : '' }}>Internship</option>
                                </select>
                            </div>

                            <div>
                                <label for="experience_level" class="block text-sm font-medium text-gray-700 mb-2">Experience Level</label>
                                <select id="experience_level" name="experience_level" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select level</option>
                                    <option value="entry" {{ old('experience_level') == 'entry' ? 'selected' : '' }}>Entry Level</option>
                                    <option value="mid" {{ old('experience_level') == 'mid' ? 'selected' : '' }}>Mid Level</option>
                                    <option value="senior" {{ old('experience_level') == 'senior' ? 'selected' : '' }}>Senior Level</option>
                                    <option value="executive" {{ old('experience_level') == 'executive' ? 'selected' : '' }}>Executive</option>
                                </select>
                            </div>

                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                <input type="text" id="location" name="location" value="{{ old('location') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., New York, NY">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Remote Work</label>
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_remote" name="is_remote" value="1" {{ old('is_remote') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="is_remote" class="ml-2 text-sm text-gray-700">This is a remote position</label>
                                </div>
                            </div>

                            <div>
                                <label for="salary_min" class="block text-sm font-medium text-gray-700 mb-2">Salary Min</label>
                                <input type="text" id="salary_min" name="salary_min" value="{{ old('salary_min') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., 50000">
                            </div>

                            <div>
                                <label for="salary_max" class="block text-sm font-medium text-gray-700 mb-2">Salary Max</label>
                                <input type="text" id="salary_max" name="salary_max" value="{{ old('salary_max') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., 80000">
                            </div>

                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                <select id="currency" name="currency" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="SCR" {{ old('currency', 'SCR') == 'SCR' ? 'selected' : '' }}>SCR</option>
                                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hide Salary</label>
                                <div class="flex items-center">
                                    <input type="checkbox" id="hide_salary" name="hide_salary" value="1" {{ old('hide_salary') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="hide_salary" class="ml-2 text-sm text-gray-700">Don't show salary (Negotiable)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Publishing Options -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Publishing Options</h2>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select id="status" name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Save as Draft</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publish Immediately</option>
                            </select>
                            <p class="mt-2 text-sm text-gray-500">Draft jobs are saved but not visible to job seekers. Published jobs are immediately visible.</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('employer.jobs.index') }}" wire:navigate class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white rounded-lg hover:from-blue-600 hover:to-cyan-500 shadow-md transition font-medium">
                            Create Job Posting
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection
