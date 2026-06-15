<?php

namespace App\Models;

use App\Enums\OrganizationApprovalStatus;
use App\Enums\OrganizationStatus;
use App\Enums\OrganizationType;
use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    protected $fillable = [
        'public_id',
        'name',
        'slug',
        'type',
        'public_email',
        'public_phone',
        'status',
        'approval_status',
        'suspension_reason',
        'timezone',
        'currency_code',
        'country_code',
        'approved_at',
        'approved_by',
        'suspended_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => OrganizationType::class,
            'status' => OrganizationStatus::class,
            'approval_status' => OrganizationApprovalStatus::class,
            'approved_at' => 'datetime',
            'suspended_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(OrganizationMembership::class)
            ->withPivot([
                'role',
                'status',
                'is_owner',
                'invited_by',
                'invited_at',
                'joined_at',
            ])
            ->withTimestamps();
    }

    public function kycSubmissions(): HasMany
    {
        return $this->hasMany(OrganizationKycSubmission::class);
    }

    public function latestKycSubmission(): HasOne
    {
        return $this->hasOne(OrganizationKycSubmission::class)->latestOfMany();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(OrganizationDocument::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(OrganizationInvitation::class);
    }
}
