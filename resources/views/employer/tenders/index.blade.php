@extends('layouts.employer')

@section('content')
<div class="min-h-screen bg-white">
    @include('partials.employer-navbar')

    <div class="flex">
        @include('partials.employer-sidebar')

        <main class="flex-1 p-6 ml-64 w-0 min-w-0">
            <div class="w-full">
                <div class="bg-white border border-gray-200 shadow-sm overflow-hidden rounded-lg">
                    <div class="flex items-start justify-between px-6 py-4 border-b border-gray-200">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Tenders</h1>
                            <p class="text-sm text-gray-500 mt-1">Create and manage your tenders.</p>
                        </div>
                        <button type="button" id="btn-create-tender" class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-medium text-sm transition shadow-md bg-gradient-to-r from-blue-500 to-cyan-400 hover:from-blue-600 hover:to-cyan-500 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Create Tender
                        </button>
                    </div>

                    @if(session('success'))
                        <div class="mx-6 mt-4 p-4 rounded-lg bg-green-50 text-green-800 border border-green-200 text-sm">{{ session('success') }}</div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50/50">
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Submission deadline</th>
                                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tenders-tbody">
                                @forelse($tenders as $t)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50/50 transition" data-tender-id="{{ $t->id }}">
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-semibold text-gray-900">{{ $t->title }}</span>
                                            @if($t->entity_name)
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $t->entity_name }}</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $t->reference_number ?? '—' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $t->tender_type ?? '—' }}</td>
                                        <td class="px-6 py-4">
                                            @php
                                                $statusClass = match($t->status) {
                                                    'active' => 'bg-green-100 text-green-800',
                                                    'pending_approval' => 'bg-amber-100 text-amber-800',
                                                    'draft' => 'bg-gray-100 text-gray-800',
                                                    'expired' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-600'
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 text-xs font-medium rounded {{ $statusClass }}">{{ str_replace('_', ' ', ucfirst($t->status)) }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $t->location ?? '—' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $t->submission_deadline ? $t->submission_deadline->format('M d, Y') : '—' }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <button type="button" class="btn-tender-view p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="View" data-tender-id="{{ $t->id }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </button>
                                                <button type="button" class="btn-tender-edit p-1.5 text-gray-600 hover:bg-gray-100 rounded" title="Edit" data-tender-id="{{ $t->id }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <button type="button" class="btn-tender-delete p-1.5 text-red-600 hover:bg-red-50 rounded" title="Delete" data-tender-id="{{ $t->id }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                            <p class="text-sm">No tenders yet.</p>
                                            <p class="text-xs mt-1">Click "Create Tender" to add one.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Create Tender Modal (4-step wizard) -->
<div id="create-tender-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.4);backdrop-filter:blur(2px);">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 flex-shrink-0" style="background:linear-gradient(180deg,#f0f4ff 0%,#fff 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <h3 id="ctm-title" class="text-xl font-bold text-gray-900">Create Tender</h3>
                    <p id="ctm-step-label" class="text-sm text-blue-600 mt-0.5">Step 1 of 4</p>
                </div>
                <button type="button" id="ctm-close" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6 bg-white">
            <form id="create-tender-form">
                @csrf
                <input type="hidden" name="tender_id" id="tender_edit_id" value="">
                <div class="flex items-center justify-center gap-2 mb-8">
                    <div id="ctm-ind-1" class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">1</div>
                    <div class="w-8 h-0.5 bg-gray-300"></div>
                    <div id="ctm-ind-2" class="w-9 h-9 rounded-full bg-white border-2 border-gray-300 text-gray-400 flex items-center justify-center text-sm font-bold">2</div>
                    <div class="w-8 h-0.5 bg-gray-300"></div>
                    <div id="ctm-ind-3" class="w-9 h-9 rounded-full bg-white border-2 border-gray-300 text-gray-400 flex items-center justify-center text-sm font-bold">3</div>
                    <div class="w-8 h-0.5 bg-gray-300"></div>
                    <div id="ctm-ind-4" class="w-9 h-9 rounded-full bg-white border-2 border-gray-300 text-gray-400 flex items-center justify-center text-sm font-bold">4</div>
                    <div class="w-8 h-0.5 bg-gray-300"></div>
                    <div id="ctm-ind-5" class="w-9 h-9 rounded-full bg-white border-2 border-gray-300 text-gray-400 flex items-center justify-center text-sm font-bold">5</div>
                </div>

                <!-- Step 1: Basic info -->
                <div id="ctm-step-1">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Basic Information</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" name="title" id="tender_title" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Tender title">
                            <p id="ctm-err-title" class="mt-1 text-sm text-red-600 hidden"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reference number</label>
                                <input type="text" name="reference_number" id="tender_reference_number" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="e.g. REF-2024-001">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tender type</label>
                                <select name="tender_type" id="tender_tender_type" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select type</option>
                                    <option value="RFQ">RFQ</option>
                                    <option value="RFP">RFP</option>
                                    <option value="EOI">EOI</option>
                                    <option value="ITB">ITB</option>
                                    <option value="RFT">RFT</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category_id" id="tender_category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                                <option value="">Select category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="tender_description" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="Full description"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Summary</label>
                            <textarea name="summary" id="tender_summary" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="Brief summary"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Entity & location -->
                <div id="ctm-step-2" class="hidden">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Entity & Location</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Entity / Organisation name</label>
                            <input type="text" name="entity_name" id="tender_entity_name" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="Company or organisation name">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sector</label>
                                <input type="text" name="sector" id="tender_sector" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="e.g. Construction, IT">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Procuring entity</label>
                                <input type="text" name="procuring_entity" id="tender_procuring_entity" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="Procuring entity">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Country / Region</label>
                                <input type="text" name="country_region" id="tender_country_region" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="e.g. Seychelles">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <input type="text" name="location" id="tender_location" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="Specific location">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Scope & requirements -->
                <div id="ctm-step-3" class="hidden">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Scope & Requirements</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Scope of work</label>
                            <textarea name="scope_of_work" id="tender_scope_of_work" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="Describe the scope of work"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Requirements (one per line)</label>
                            <textarea name="requirements_text" id="tender_requirements_text" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="Each requirement on a new line"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Eligibility criteria (one per line)</label>
                            <textarea name="eligibility_text" id="tender_eligibility_text" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="Each criterion on a new line"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Required documents (one per line)</label>
                            <textarea name="required_docs_text" id="tender_required_docs_text" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="e.g. Company registration, Tax certificate"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Dates & budget -->
                <div id="ctm-step-4" class="hidden">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Dates & Budget</h4>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start date</label>
                                <input type="date" name="start_date" id="tender_start_date" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End date</label>
                                <input type="date" name="end_date" id="tender_end_date" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Published date</label>
                                <input type="date" name="published_date" id="tender_published_date" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Clarification deadline</label>
                                <input type="date" name="clarification_deadline" id="tender_clarification_deadline" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Submission deadline *</label>
                                <input type="date" name="submission_deadline" id="tender_submission_deadline" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (fixed)</label>
                                <input type="number" step="0.01" min="0" name="amount" id="tender_amount" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                                <select name="currency" id="tender_currency" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                                    <option value="SCR">SCR</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Budget min</label>
                                <input type="number" step="0.01" min="0" name="budget_min" id="tender_budget_min" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Budget max</label>
                                <input type="number" step="0.01" min="0" name="budget_max" id="tender_budget_max" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Submission method</label>
                            <input type="text" name="submission_method" id="tender_submission_method" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500" placeholder="e.g. Online portal, Email">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status after create</label>
                            <select name="status" id="tender_status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                                <option value="draft">Draft</option>
                                <option value="pending_approval">Submit for approval</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Documents -->
                <div id="ctm-step-5" class="hidden">
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Documents</h4>
                    <p class="text-sm text-gray-600 mb-4">Upload documents that will be available for download on the tender page. Optionally set a display name before choosing the file.</p>
                    <div id="tender-docs-list" class="space-y-3 mb-4"></div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <input type="file" id="tender-doc-file" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                        <input type="text" id="tender-doc-name" class="flex-1 min-w-[180px] border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Document name (optional)">
                        <button type="button" id="tender-doc-add-btn" class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 text-sm font-medium hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Choose file &amp; upload
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 flex justify-between flex-shrink-0 bg-gray-50">
            <button type="button" id="ctm-prev" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium text-sm hover:bg-gray-100 hidden">Previous</button>
            <div class="flex-1"></div>
            <div class="flex gap-2">
                <button type="button" id="ctm-next" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium text-sm hover:bg-blue-700">Next</button>
                <button type="submit" id="ctm-submit" form="create-tender-form" class="hidden px-5 py-2.5 text-white rounded-lg font-medium text-sm bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">Create Tender</button>
            </div>
        </div>
    </div>
</div>

<!-- View Tender Modal -->
<div id="view-tender-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.4);backdrop-filter:blur(2px);">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between flex-shrink-0">
            <h3 class="text-lg font-bold text-gray-900">Tender Details</h3>
            <button type="button" id="view-tender-close" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="view-tender-body" class="flex-1 overflow-y-auto p-6 text-sm text-gray-700 space-y-4"></div>
        <div id="view-tender-footer" class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2 flex-shrink-0 bg-gray-50">
            <button type="button" id="view-tender-submit-btn" class="hidden px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium text-sm hover:bg-blue-700">Publish</button>
            <button type="button" id="view-tender-close-btn" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium text-sm hover:bg-gray-100">Close</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const modal = document.getElementById('create-tender-modal');
    const form = document.getElementById('create-tender-form');
    const stepLabel = document.getElementById('ctm-step-label');
    const ctmTitle = document.getElementById('ctm-title');
    const tenderEditIdInput = document.getElementById('tender_edit_id');
    let ctmStep = 1;
    const totalSteps = 5;
    const baseUrl = '{{ url("/employer/tenders") }}';
    const uploadDocUrl = '{{ route("employer.tenders.upload-document") }}';
    let tenderDocsArray = [];

    function renderTenderActionsCell(tenderId) {
        return '<div class="flex items-center gap-2">' +
            '<button type="button" class="btn-tender-view p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="View" data-tender-id="' + tenderId + '"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>' +
            '<button type="button" class="btn-tender-edit p-1.5 text-gray-600 hover:bg-gray-100 rounded" title="Edit" data-tender-id="' + tenderId + '"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>' +
            '<button type="button" class="btn-tender-delete p-1.5 text-red-600 hover:bg-red-50 rounded" title="Delete" data-tender-id="' + tenderId + '"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>' +
            '</div>';
    }

    function renderTenderDocsList() {
        var list = document.getElementById('tender-docs-list');
        if (!list) return;
        list.innerHTML = tenderDocsArray.map(function(doc, idx) {
            var name = (doc.name || 'Document').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            var meta = [doc.type || 'File'];
            if (doc.size) meta.push(doc.size);
            return '<div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-gray-50" data-doc-idx="' + idx + '">' +
                '<div class="flex-1 min-w-0">' +
                '<p class="text-sm font-medium text-gray-900 truncate">' + name + '</p>' +
                (meta.length ? '<p class="text-xs text-gray-500">' + meta.join(' \u2022 ') + '</p>' : '') +
                '</div>' +
                '<button type="button" class="tender-doc-remove p-2 text-red-600 hover:bg-red-50 rounded-lg transition" data-doc-idx="' + idx + '" title="Remove">' +
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>' +
                '</div>';
        }).join('');
    }

    function updateTenderRowInTable(tenderId, t) {
        var row = document.querySelector('tr[data-tender-id="' + tenderId + '"]');
        if (!row) return;
        var statusClass = t.status === 'active' ? 'bg-green-100 text-green-800' : (t.status === 'pending_approval' ? 'bg-amber-100 text-amber-800' : (t.status === 'draft' ? 'bg-gray-100 text-gray-800' : (t.status === 'expired' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600')));
        var statusLabel = (t.status || '').replace(/_/g, ' ');
        statusLabel = statusLabel.charAt(0).toUpperCase() + statusLabel.slice(1);
        var entityCell = t.entity_name ? '<p class="text-xs text-gray-500 mt-0.5">' + (t.entity_name || '') + '</p>' : '';
        row.children[0].innerHTML = '<span class="text-sm font-semibold text-gray-900">' + (t.title || '') + '</span>' + entityCell;
        row.children[1].textContent = t.reference_number || '—';
        row.children[2].textContent = t.tender_type || '—';
        row.children[3].innerHTML = '<span class="px-2.5 py-1 text-xs font-medium rounded ' + statusClass + '">' + statusLabel + '</span>';
        row.children[4].textContent = t.location || '—';
        row.children[5].textContent = t.submission_deadline || '—';
        row.children[6].innerHTML = renderTenderActionsCell(t.id);
    }

    document.getElementById('tenders-tbody').addEventListener('click', async function(e) {
        var viewBtn = e.target.closest('.btn-tender-view');
        var editBtn = e.target.closest('.btn-tender-edit');
        var deleteBtn = e.target.closest('.btn-tender-delete');
        if (viewBtn) {
            var id = viewBtn.getAttribute('data-tender-id');
            fetch(baseUrl + '/' + id, { headers: { 'Accept': 'application/json' } })
                .then(function(r) { return r.json().then(function(d) { if (!r.ok) throw new Error(d.message || 'Failed to load tender'); return d; }); })
                .then(function(data) {
                    var t = data.tender;
                    var body = document.getElementById('view-tender-body');
                    body.innerHTML = '<p><strong>Title:</strong> ' + (t.title || '') + '</p>' +
                        (t.reference_number ? '<p><strong>Reference:</strong> ' + t.reference_number + '</p>' : '') +
                        (t.tender_type ? '<p><strong>Type:</strong> ' + t.tender_type + '</p>' : '') +
                        '<p><strong>Status:</strong> ' + (t.status || '').replace(/_/g, ' ') + '</p>' +
                        (t.entity_name ? '<p><strong>Entity:</strong> ' + t.entity_name + '</p>' : '') +
                        (t.location ? '<p><strong>Location:</strong> ' + t.location + '</p>' : '') +
                        (t.description ? '<p><strong>Description:</strong></p><p class="whitespace-pre-wrap">' + t.description + '</p>' : '') +
                        (t.summary ? '<p><strong>Summary:</strong></p><p>' + t.summary + '</p>' : '') +
                        (t.submission_deadline ? '<p><strong>Submission deadline:</strong> ' + t.submission_deadline + '</p>' : '');
                    document.getElementById('view-tender-submit-btn').classList.toggle('hidden', t.status !== 'draft');
                    document.getElementById('view-tender-submit-btn').setAttribute('data-tender-id', id);
                    document.getElementById('view-tender-modal').classList.remove('hidden');
                    document.getElementById('view-tender-modal').classList.add('flex');
                })
                .catch(function(err) { alert(err.message || 'Could not load tender.'); });
        }
        if (editBtn) {
            var id = editBtn.getAttribute('data-tender-id');
            fetch(baseUrl + '/' + id, { headers: { 'Accept': 'application/json' } })
                .then(function(r) { return r.json().then(function(d) { if (!r.ok) throw new Error(d.message || 'Failed to load tender'); return d; }); })
                .then(function(data) {
                    var t = data.tender;
                    tenderEditIdInput.value = id;
                    ctmTitle.textContent = 'Edit Tender';
                    document.getElementById('tender_title').value = t.title || '';
                    document.getElementById('tender_reference_number').value = t.reference_number || '';
                    document.getElementById('tender_tender_type').value = t.tender_type || '';
                    document.getElementById('tender_category_id').value = t.category_id || '';
                    document.getElementById('tender_description').value = t.description || '';
                    document.getElementById('tender_summary').value = t.summary || '';
                    document.getElementById('tender_entity_name').value = t.entity_name || '';
                    document.getElementById('tender_sector').value = t.sector || '';
                    document.getElementById('tender_procuring_entity').value = t.procuring_entity || '';
                    document.getElementById('tender_country_region').value = t.country_region || '';
                    document.getElementById('tender_location').value = t.location || '';
                    document.getElementById('tender_scope_of_work').value = t.scope_of_work || '';
                    document.getElementById('tender_requirements_text').value = Array.isArray(t.requirements) ? t.requirements.join('\n') : '';
                    document.getElementById('tender_eligibility_text').value = Array.isArray(t.eligibility_criteria) ? t.eligibility_criteria.join('\n') : '';
                    document.getElementById('tender_required_docs_text').value = Array.isArray(t.required_documents) ? t.required_documents.join('\n') : '';
                    document.getElementById('tender_start_date').value = t.start_date || '';
                    document.getElementById('tender_end_date').value = t.end_date || '';
                    document.getElementById('tender_published_date').value = t.published_date || '';
                    document.getElementById('tender_clarification_deadline').value = t.clarification_deadline || '';
                    document.getElementById('tender_submission_deadline').value = t.submission_deadline || '';
                    document.getElementById('tender_amount').value = t.amount != null ? t.amount : '';
                    document.getElementById('tender_budget_min').value = t.budget_min != null ? t.budget_min : '';
                    document.getElementById('tender_budget_max').value = t.budget_max != null ? t.budget_max : '';
                    document.getElementById('tender_currency').value = t.currency || 'SCR';
                    document.getElementById('tender_submission_method').value = t.submission_method || '';
                    document.getElementById('tender_status').value = t.status || 'draft';
                    tenderDocsArray = Array.isArray(t.attachments) ? t.attachments.map(function(a) {
                        return typeof a === 'object' && a !== null
                            ? { name: a.name || 'Document', url: a.url || a.path || '', type: a.type || 'File', size: a.size || null }
                            : { name: 'Document', url: a, type: 'File', size: null };
                    }) : [];
                    renderTenderDocsList();
                    document.getElementById('ctm-submit').textContent = 'Update Tender';
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    showStep(1);
                })
                .catch(function(err) { alert(err.message || 'Could not load tender for editing.'); });
        }
        if (deleteBtn) {
            var id = deleteBtn.getAttribute('data-tender-id');
            var confirmed = false;
            if (typeof window.showConfirmDialog === 'function') {
                confirmed = await window.showConfirmDialog(
                    'This action cannot be undone. The tender will be permanently removed.',
                    { title: 'Delete tender?', confirmText: 'Delete', cancelText: 'Cancel' }
                );
            } else {
                confirmed = window.confirm('Are you sure you want to delete this tender? This action cannot be undone.');
            }
            if (!confirmed) return;
            var row = document.querySelector('tr[data-tender-id="' + id + '"]');
            var csrf = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').content;
            fetch(baseUrl + '/' + id, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf || '' }
            })
                .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
                .then(function(result) {
                    if (result.data.success && row) {
                        row.remove();
                        var tbody = document.getElementById('tenders-tbody');
                        if (tbody.children.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center text-gray-500"><p class="text-sm">No tenders yet.</p><p class="text-xs mt-1">Click "Create Tender" to add one.</p></td></tr>';
                        }
                    } else if (!result.ok && typeof window.showErrorToast === 'function') {
                        window.showErrorToast(result.data.message || 'Failed to delete tender.', 4000);
                    } else if (!result.ok) {
                        alert(result.data.message || 'Failed to delete tender.');
                    }
                })
                .catch(function() {
                    if (typeof window.showErrorToast === 'function') {
                        window.showErrorToast('Network error. Could not delete tender.', 4000);
                    } else {
                        alert('Network error. Could not delete tender.');
                    }
                });
        }
    });

    document.getElementById('view-tender-close').addEventListener('click', function() {
        document.getElementById('view-tender-modal').classList.add('hidden');
        document.getElementById('view-tender-modal').classList.remove('flex');
    });
    document.getElementById('view-tender-close-btn').addEventListener('click', function() {
        document.getElementById('view-tender-modal').classList.add('hidden');
        document.getElementById('view-tender-modal').classList.remove('flex');
    });
    document.getElementById('view-tender-modal').addEventListener('click', function(e) {
        if (e.target.id === 'view-tender-modal') {
            document.getElementById('view-tender-modal').classList.add('hidden');
            document.getElementById('view-tender-modal').classList.remove('flex');
        }
    });
    document.getElementById('view-tender-submit-btn').addEventListener('click', function() {
        var id = this.getAttribute('data-tender-id');
        if (!id) return;
        var btn = this;
        var origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-sm inline-block mr-2 align-middle"></span><span>Publishing...</span>';
        fetch(baseUrl + '/' + id + '/submit', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success && data.tender) {
                    updateTenderRowInTable(id, data.tender);
                    document.getElementById('view-tender-modal').classList.add('hidden');
                    document.getElementById('view-tender-modal').classList.remove('flex');
                }
            })
            .finally(function() {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            });
    });

    function showStep(step) {
        ctmStep = step;
        stepLabel.textContent = 'Step ' + step + ' of ' + totalSteps;
        for (let i = 1; i <= totalSteps; i++) {
            document.getElementById('ctm-step-' + i).classList.toggle('hidden', i !== step);
            const ind = document.getElementById('ctm-ind-' + i);
            if (ind) {
                ind.classList.remove('bg-blue-600', 'text-white', 'border-gray-300', 'text-gray-400', 'border-2');
                if (i < step) {
                    ind.classList.add('bg-blue-600', 'text-white');
                } else if (i === step) {
                    ind.classList.add('bg-blue-600', 'text-white');
                } else {
                    ind.classList.add('bg-white', 'border-2', 'border-gray-300', 'text-gray-400');
                }
            }
        }
        document.getElementById('ctm-prev').classList.toggle('hidden', step === 1);
        document.getElementById('ctm-next').classList.toggle('hidden', step === totalSteps);
        document.getElementById('ctm-submit').classList.toggle('hidden', step !== totalSteps);
    }

    document.getElementById('tender-doc-add-btn').addEventListener('click', function() {
        document.getElementById('tender-doc-file').click();
    });

    document.getElementById('tender-doc-file').addEventListener('change', function(e) {
        var fileInput = e.target;
        if (!fileInput.files || !fileInput.files.length) return;
        var file = fileInput.files[0];
        var nameInput = document.getElementById('tender-doc-name');
        var name = (nameInput && nameInput.value.trim()) ? nameInput.value.trim() : (file.name || 'Document');
        if (nameInput) nameInput.value = '';
        var row = document.createElement('div');
        row.className = 'flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-gray-50';
        row.innerHTML = '<div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-900">' + name.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</p><p class="text-xs text-blue-600 flex items-center gap-1"><span class="spinner-sm inline-block w-3 h-3"></span> Uploading...</p></div>';
        document.getElementById('tender-docs-list').appendChild(row);

        var formData = new FormData();
        formData.append('document', file);
        formData.append('name', name);
        var csrf = document.querySelector('meta[name="csrf-token"]');
        if (csrf && csrf.content) formData.append('_token', csrf.content);

        fetch(uploadDocUrl, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        })
        .then(function(r) { return r.json().then(function(d) { if (!r.ok) throw new Error(d.message || 'Upload failed'); return d; }); })
        .then(function(data) {
            tenderDocsArray.push({ name: data.name || name, url: data.url, type: data.type || 'File', size: data.size || null });
            renderTenderDocsList();
        })
        .catch(function(err) {
            row.remove();
            if (typeof window.showErrorToast === 'function') window.showErrorToast(err.message || 'Failed to upload document');
            else alert(err.message || 'Failed to upload document');
        });
        fileInput.value = '';
    });

    document.getElementById('tender-docs-list').addEventListener('click', function(e) {
        var btn = e.target.closest('.tender-doc-remove');
        if (!btn) return;
        var idx = parseInt(btn.getAttribute('data-doc-idx'), 10);
        if (!isNaN(idx) && idx >= 0 && idx < tenderDocsArray.length) {
            tenderDocsArray.splice(idx, 1);
            renderTenderDocsList();
        }
    });

    document.getElementById('btn-create-tender').addEventListener('click', function() {
        tenderEditIdInput.value = '';
        ctmTitle.textContent = 'Create Tender';
        document.getElementById('ctm-submit').textContent = 'Create Tender';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        form.reset();
        tenderEditIdInput.value = '';
        tenderDocsArray = [];
        renderTenderDocsList();
        document.getElementById('ctm-err-title').classList.add('hidden');
        showStep(1);
    });

    document.getElementById('ctm-close').addEventListener('click', function() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });

    document.getElementById('ctm-prev').addEventListener('click', function() {
        if (ctmStep > 1) showStep(ctmStep - 1);
    });

    document.getElementById('ctm-next').addEventListener('click', function() {
        if (ctmStep < totalSteps) {
            if (ctmStep === 1 && !document.getElementById('tender_title').value.trim()) {
                document.getElementById('ctm-err-title').textContent = 'Title is required.';
                document.getElementById('ctm-err-title').classList.remove('hidden');
                return;
            }
            document.getElementById('ctm-err-title').classList.add('hidden');
            showStep(ctmStep + 1);
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('ctm-submit');
        const editId = tenderEditIdInput.value.trim();
        const isEdit = !!editId;
        submitBtn.disabled = true;
        submitBtn.textContent = isEdit ? 'Updating...' : 'Creating...';

        const requirementsText = document.getElementById('tender_requirements_text').value.trim();
        const eligibilityText = document.getElementById('tender_eligibility_text').value.trim();
        const requiredDocsText = document.getElementById('tender_required_docs_text').value.trim();

        const requirements = requirementsText ? requirementsText.split('\n').map(s => s.trim()).filter(Boolean) : [];
        const eligibility_criteria = eligibilityText ? eligibilityText.split('\n').map(s => s.trim()).filter(Boolean) : [];
        const required_documents = requiredDocsText ? requiredDocsText.split('\n').map(s => s.trim()).filter(Boolean) : [];

        const payload = new FormData(form);
        payload.delete('requirements_text');
        payload.delete('eligibility_text');
        payload.delete('required_docs_text');
        payload.delete('tender_id');
        payload.append('requirements', JSON.stringify(requirements));
        payload.append('eligibility_criteria', JSON.stringify(eligibility_criteria));
        payload.append('required_documents', JSON.stringify(required_documents));
        payload.append('attachments', JSON.stringify(tenderDocsArray));

        var csrfToken = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').content;
        if (csrfToken) payload.append('_token', csrfToken);
        if (isEdit) payload.append('_method', 'PUT');

        var url = isEdit ? (baseUrl + '/' + editId) : '{{ route("employer.tenders.store") }}';
        var method = isEdit ? 'POST' : 'POST';

        fetch(url, {
            method: method,
            body: payload,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function(res) { return res.json().then(function(data) { return { ok: res.ok, data: data }; }); })
        .then(function(result) {
            if (result.ok && result.data.tender) {
                var t = result.data.tender;
                if (isEdit) {
                    updateTenderRowInTable(editId, t);
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    tenderEditIdInput.value = '';
                    ctmTitle.textContent = 'Create Tender';
                } else {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    var statusClass = t.status === 'active' ? 'bg-green-100 text-green-800' : (t.status === 'pending_approval' ? 'bg-amber-100 text-amber-800' : (t.status === 'draft' ? 'bg-gray-100 text-gray-800' : (t.status === 'expired' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600')));
                    var statusLabel = (t.status || '').replace(/_/g, ' ');
                    statusLabel = statusLabel.charAt(0).toUpperCase() + statusLabel.slice(1);
                    var entityCell = t.entity_name ? '<p class="text-xs text-gray-500 mt-0.5">' + (t.entity_name || '') + '</p>' : '';
                    var actionsCell = renderTenderActionsCell(t.id);
                    var newRow = '<tr class="border-b border-gray-100 hover:bg-gray-50/50 transition" data-tender-id="' + t.id + '">' +
                        '<td class="px-6 py-4"><span class="text-sm font-semibold text-gray-900">' + (t.title || '') + '</span>' + entityCell + '</td>' +
                        '<td class="px-6 py-4 text-sm text-gray-600">' + (t.reference_number || '—') + '</td>' +
                        '<td class="px-6 py-4 text-sm text-gray-600">' + (t.tender_type || '—') + '</td>' +
                        '<td class="px-6 py-4"><span class="px-2.5 py-1 text-xs font-medium rounded ' + statusClass + '">' + statusLabel + '</span></td>' +
                        '<td class="px-6 py-4 text-sm text-gray-600">' + (t.location || '—') + '</td>' +
                        '<td class="px-6 py-4 text-sm text-gray-600">' + (t.submission_deadline || '—') + '</td>' +
                        '<td class="px-6 py-4">' + actionsCell + '</td></tr>';
                    var tbody = document.getElementById('tenders-tbody');
                    var emptyRow = tbody.querySelector('tr td[colspan="7"]');
                    if (emptyRow) emptyRow.closest('tr').remove();
                    tbody.insertAdjacentHTML('afterbegin', newRow);
                }
            } else if (result.ok) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                window.location.reload();
            } else {
                var msg = result.data.message || (result.data.errors && result.data.errors.title ? result.data.errors.title[0] : (isEdit ? 'Failed to update tender.' : 'Failed to create tender.'));
                alert(msg);
            }
        })
        .catch(function() {
            alert('Network error. Please try again.');
        })
        .finally(function() {
            submitBtn.disabled = false;
            submitBtn.textContent = isEdit ? 'Update Tender' : 'Create Tender';
        });
    });
})();
</script>
@endpush
@endsection
