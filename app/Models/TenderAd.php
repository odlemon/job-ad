<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderAd extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'summary',
        'scope_of_work',
        'requirements',
        'reference_number',
        'tender_type',
        'category_id',
        'entity_name',
        'sector',
        'procuring_entity',
        'country_region',
        'location',
        'views_count',
        'applications_count',
        'start_date',
        'end_date',
        'amount',
        'budget_min',
        'budget_max',
        'currency',
        'submission_method',
        'required_documents',
        'eligibility_criteria',
        'attachments',
        'published_date',
        'clarification_deadline',
        'submission_deadline',
        'status',
        'edit_request_message',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'published_date' => 'date',
        'clarification_deadline' => 'date',
        'submission_deadline' => 'date',
        'amount' => 'decimal:2',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'views_count' => 'integer',
        'applications_count' => 'integer',
        'requirements' => 'array',
        'required_documents' => 'array',
        'eligibility_criteria' => 'array',
        'attachments' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
