<?php
    use Illuminate\Support\Facades\Storage;
?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <?php echo $__env->make('partials.employer-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="flex">
        <?php echo $__env->make('partials.employer-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Main Content -->
        <main class="flex-1 p-8 ml-64">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Company Profile</h1>
                    <p class="text-gray-600">Manage your company information and branding</p>
                </div>

                <!-- Company Branding Banner -->
                <div class="relative mb-8 rounded-2xl overflow-hidden" style="height: 200px;">
                    <div id="cover-image-container" class="absolute inset-0 bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-400">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company->cover_image): ?>
                            <?php
                                // Handle both full URLs and local storage paths
                                $coverImageUrl = $company->cover_image;
                                if (!str_starts_with($coverImageUrl, 'http')) {
                                    // Local storage path
                                    $coverImageUrl = Storage::url($coverImageUrl);
                                }
                            ?>
                            <img src="<?php echo e($coverImageUrl); ?>" alt="Cover" class="w-full h-full object-cover" id="cover-image-img">
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    
                    <!-- Cover Image Loading Overlay -->
                    <div id="cover-image-loading" class="hidden absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto mb-2"></div>
                            <p class="text-white font-medium">Uploading cover image...</p>
                        </div>
                    </div>
                    
                    <button id="cover-change-btn" onclick="document.getElementById('cover_image_file').click()" class="absolute top-4 right-4 bg-blue-400 hover:bg-blue-500 px-4 py-2 rounded-lg flex items-center space-x-2 text-white font-medium transition border border-white border-opacity-50">
                        <svg id="cover-camera-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <svg id="cover-loading-icon" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Change Cover</span>
                    </button>
                    
                    <!-- Profile Picture -->
                    <div class="absolute top-4 left-8">
                        <div class="relative">
                            <div id="logo-container" class="w-32 h-32 rounded-full bg-white p-2 flex items-center justify-center border-4 border-white shadow-lg relative">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company->logo): ?>
                                    <?php
                                        // Handle both full URLs and local storage paths
                                        $logoUrl = $company->logo;
                                        if (!str_starts_with($logoUrl, 'http')) {
                                            // Local storage path
                                            $logoUrl = Storage::url($logoUrl);
                                        }
                                    ?>
                                    <img src="<?php echo e($logoUrl); ?>" alt="Logo" class="w-full h-full rounded-full object-cover" id="logo-img">
                                <?php else: ?>
                                    <div class="w-full h-full rounded-full bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-400 flex items-center justify-center" id="logo-placeholder">
                                        <span class="text-white text-4xl font-bold"><?php echo e(strtoupper(substr($company->name ?? 'C', 0, 2))); ?></span>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                
                                <!-- Logo Loading Overlay -->
                                <div id="logo-loading-overlay" class="hidden absolute inset-0 rounded-full bg-black bg-opacity-60 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto mb-1"></div>
                                        <p class="text-white text-xs font-medium">Uploading...</p>
                                    </div>
                                </div>
                            </div>
                            <button id="logo-change-btn" onclick="document.getElementById('logo_file').click()" class="absolute bottom-0 right-0 bg-white bg-opacity-30 rounded-full p-2 border-2 border-white hover:bg-opacity-40 transition">
                                <svg id="logo-edit-icon" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <svg id="logo-loading-icon" class="w-4 h-4 text-white animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Company Information Section -->
                <div id="company-info-section" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-8 relative">
                    <!-- Skeleton Loader -->
                    <div id="company-info-skeleton" class="hidden animate-pulse">
                        <div class="h-6 bg-gray-200 rounded w-48 mb-6"></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-6">
                                <div class="h-4 bg-gray-200 rounded w-32 mb-2"></div>
                                <div class="h-10 bg-gray-200 rounded"></div>
                                <div class="h-4 bg-gray-200 rounded w-32 mb-2"></div>
                                <div class="h-10 bg-gray-200 rounded"></div>
                                <div class="h-4 bg-gray-200 rounded w-32 mb-2"></div>
                                <div class="h-24 bg-gray-200 rounded"></div>
                            </div>
                            <div class="space-y-6">
                                <div class="h-4 bg-gray-200 rounded w-32 mb-2"></div>
                                <div class="h-10 bg-gray-200 rounded"></div>
                                <div class="h-4 bg-gray-200 rounded w-32 mb-2"></div>
                                <div class="h-10 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div id="company-info-content">
                        
                        <form id="companyInfoForm" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="space-y-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                                        <input type="text" id="name" name="name" required value="<?php echo e(old('name', $company->name)); ?>" 
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label for="website" class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                                        <div class="relative">
                                            <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                            </svg>
                                            <input type="url" id="website" name="website" value="<?php echo e(old('website', $company->website)); ?>" 
                                                class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                placeholder="https://techcorp.com">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="industry" class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                                        <div class="relative">
                                            <select id="industry" name="industry" 
                                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 appearance-none bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <option value="">Select industry</option>
                                                <option value="Technology" <?php echo e(old('industry', $company->industry) == 'Technology' ? 'selected' : ''); ?>>Technology</option>
                                                <option value="Finance" <?php echo e(old('industry', $company->industry) == 'Finance' ? 'selected' : ''); ?>>Finance</option>
                                                <option value="Healthcare" <?php echo e(old('industry', $company->industry) == 'Healthcare' ? 'selected' : ''); ?>>Healthcare</option>
                                                <option value="Education" <?php echo e(old('industry', $company->industry) == 'Education' ? 'selected' : ''); ?>>Education</option>
                                                <option value="Retail" <?php echo e(old('industry', $company->industry) == 'Retail' ? 'selected' : ''); ?>>Retail</option>
                                                <option value="Manufacturing" <?php echo e(old('industry', $company->industry) == 'Manufacturing' ? 'selected' : ''); ?>>Manufacturing</option>
                                                <option value="Other" <?php echo e(old('industry', $company->industry) == 'Other' ? 'selected' : ''); ?>>Other</option>
                                            </select>
                                            <svg class="absolute right-3 top-3 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="size" class="block text-sm font-medium text-gray-700 mb-2">Company Size</label>
                                        <div class="relative">
                                            <select id="size" name="size" 
                                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 appearance-none bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <option value="">Select size</option>
                                                <option value="1-10 employees" <?php echo e(old('size', $company->size) == '1-10 employees' ? 'selected' : ''); ?>>1-10 employees</option>
                                                <option value="11-50 employees" <?php echo e(old('size', $company->size) == '11-50 employees' ? 'selected' : ''); ?>>11-50 employees</option>
                                                <option value="51-200 employees" <?php echo e(old('size', $company->size) == '51-200 employees' ? 'selected' : ''); ?>>51-200 employees</option>
                                                <option value="201-500 employees" <?php echo e(old('size', $company->size) == '201-500 employees' ? 'selected' : ''); ?>>201-500 employees</option>
                                                <option value="501-1000 employees" <?php echo e(old('size', $company->size) == '501-1000 employees' ? 'selected' : ''); ?>>501-1000 employees</option>
                                                <option value="1000+ employees" <?php echo e(old('size', $company->size) == '1000+ employees' ? 'selected' : ''); ?>>1000+ employees</option>
                                            </select>
                                            <svg class="absolute right-3 top-3 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Company Description</label>
                                        <textarea id="description" name="description" rows="6" 
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize" 
                                            ><?php echo e(old('description', $company->description)); ?></textarea>
                                    </div>

                                    <div>
                                        <label for="culture_benefits" class="block text-sm font-medium text-gray-700 mb-2">Company Culture & Benefits</label>
                                        <textarea id="culture_benefits" name="culture_benefits" rows="4" 
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize" 
                                            placeholder="Describe your company culture, values, and benefits..."><?php echo e(old('culture_benefits', $company->culture_benefits)); ?></textarea>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-6">
                                    <div>
                                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                        <div class="relative">
                                            <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <input type="text" id="location" name="location" value="<?php echo e(old('location', $company->location)); ?>" 
                                                class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                placeholder="San Francisco, CA">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="founded_year" class="block text-sm font-medium text-gray-700 mb-2">Founded Year</label>
                                        <div class="relative">
                                            <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <input type="number" id="founded_year" name="founded_year" value="<?php echo e(old('founded_year', $company->founded_year)); ?>" 
                                                class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                placeholder="2015" min="1800" max="<?php echo e(date('Y')); ?>">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="linkedin" class="block text-sm font-medium text-gray-700 mb-2">LinkedIn</label>
                                        <div class="relative">
                                            <input type="url" id="linkedin" name="linkedin" value="<?php echo e(old('linkedin', $company->linkedin)); ?>" 
                                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                placeholder="https://linkedin.com/company/techcorp">
                                            <svg class="absolute right-3 top-3 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="twitter" class="block text-sm font-medium text-gray-700 mb-2">Twitter</label>
                                        <div class="relative">
                                            <input type="url" id="twitter" name="twitter" value="<?php echo e(old('twitter', $company->twitter)); ?>" 
                                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                placeholder="https://twitter.com/techcorp">
                                            <svg class="absolute right-3 top-3 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Save Button -->
                            <div id="save-button-container" class="pt-4 border-t border-gray-200">
                                <button type="button" id="save-company-info-btn" onclick="saveCompanyInfo()" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center space-x-2">
                                    <svg id="save-info-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <svg id="save-info-loading" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span id="save-info-text">Save Changes</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Company Gallery Section -->
                <div id="gallery-section" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-8">
                    <!-- Skeleton Loader -->
                    <div id="gallery-skeleton" class="hidden animate-pulse">
                        <div class="h-6 bg-gray-200 rounded w-48 mb-4"></div>
                        <div class="grid grid-cols-6 gap-4 mb-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 6; $i++): ?>
                                <div class="aspect-square bg-gray-200 rounded-lg"></div>
                            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="h-12 bg-gray-200 rounded w-full"></div>
                    </div>
                    
                    <!-- Content -->
                    <div id="gallery-content">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Company Gallery</h2>
                        <div id="gallery-grid" class="flex flex-wrap gap-4 mb-4">
                            <?php
                                $gallery = is_array($company->gallery_images) ? $company->gallery_images : (is_string($company->gallery_images) ? json_decode($company->gallery_images, true) : []);
                                $gallery = $gallery ?: [];
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $gallery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="relative group">
                                    <div class="w-32 h-32 rounded-lg overflow-hidden border-2 border-gray-200">
                                        <?php
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
                                        ?>
                                        <img src="<?php echo e($imageUrl); ?>" alt="Gallery <?php echo e($index+1); ?>" class="w-full h-full object-cover">
                                    </div>
                                    <button onclick="deleteGalleryImage(<?php echo e($index); ?>)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition opacity-0 group-hover:opacity-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($gallery) < 6): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = count($gallery); $i < 6; $i++): ?>
                                    <div class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <button type="button" id="gallery-upload-btn" onclick="document.getElementById('gallery_images_file').click()" class="w-full border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-500 hover:bg-blue-50 transition flex items-center justify-center space-x-2">
                            <svg id="gallery-upload-icon" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <svg id="gallery-loading-icon" class="w-5 h-5 text-gray-400 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Upload Photos</span>
                        </button>
                    </div>
                </div>

                <!-- Verification Status Section -->
                <div id="verification-section" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <!-- Skeleton Loader -->
                    <div id="verification-skeleton" class="hidden animate-pulse">
                        <div class="h-6 bg-gray-200 rounded w-48 mb-4"></div>
                        <div class="h-24 bg-gray-200 rounded mb-4"></div>
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-200 rounded w-full"></div>
                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div id="verification-content">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Verification Status</h2>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company->verified_at): ?>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">Company Verified</div>
                                            <div class="text-sm text-gray-600">
                                                Verified on <?php echo e($company->verified_at->format('F d, Y')); ?> at <?php echo e($company->verified_at->format('g:i A')); ?>

                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-green-600 font-medium text-sm">Verified</span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">Not Verified</div>
                                        <div class="text-sm text-gray-600">Your company is not yet verified</div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Benefits of Verification</h3>
                            <ul class="space-y-2 text-sm text-gray-600">
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
        const response = await fetch('<?php echo e(route("employer.company-profile")); ?>', {
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
                const mediaBaseUrl = data.mediaBaseUrl || '<?php echo e($mediaBaseUrl ?? env("MEDIA_BASE_URL", "http://31.220.82.129/uploads")); ?>';
                
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
        
        fetch('<?php echo e(route("employer.company-profile.update")); ?>', {
            method: 'POST',
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
        
        fetch('<?php echo e(route("employer.company-profile.update")); ?>', {
            method: 'POST',
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
        
        fetch('<?php echo e(route("employer.company-profile.upload-gallery")); ?>', {
            method: 'POST',
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
                const mediaBaseUrl = data.mediaBaseUrl || '<?php echo e($mediaBaseUrl ?? env("MEDIA_BASE_URL", "http://31.220.82.129/uploads")); ?>';
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
        mediaBaseUrl = '<?php echo e($mediaBaseUrl ?? env("MEDIA_BASE_URL", "http://31.220.82.129/uploads")); ?>';
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
            placeholderDiv.className = 'w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50';
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
        imageDiv.className = 'relative group';
        
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
            <div class="w-32 h-32 rounded-lg overflow-hidden border-2 border-gray-200">
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
            placeholderDiv.className = 'w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50';
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
    
    try {
        const response = await fetch('<?php echo e(route("employer.company-profile.update")); ?>', {
            method: 'POST',
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
    }
}

// Delete gallery image
async function deleteGalleryImage(index) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }
    
    try {
        // Show skeleton loader
        document.getElementById('gallery-skeleton').classList.remove('hidden');
        document.getElementById('gallery-content').classList.add('hidden');
        
        const response = await fetch(`<?php echo e(route("employer.company-profile.delete-gallery", ":index")); ?>`.replace(':index', index), {
            method: 'DELETE',
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
            const mediaBaseUrl = data.mediaBaseUrl || '<?php echo e($mediaBaseUrl ?? env("MEDIA_BASE_URL", "http://31.220.82.129/uploads")); ?>';
            
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\lysp\Downloads\Job Ad\resources\views/employer/company-profile.blade.php ENDPATH**/ ?>