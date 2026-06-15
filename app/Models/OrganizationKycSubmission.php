<?php

namespace App\Models;

use App\Enums\OrganizationKycStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationKycSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'submitted_by',
        'status',
        'reviewed_by',
        'submitted_at',
        'reviewed_at',
        'rejection_reason',
        'business_type',
        'registration_number',
        'tax_identifier',
        'legal_name',
        'contact_name',
        'contact_phone',
        'contact_email',
        'payout_method',
        'payout_account_name',
        'payout_account_number',
        'payout_provider',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrganizationKycStatus::class,
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(OrganizationDocument::class);
    }
}
