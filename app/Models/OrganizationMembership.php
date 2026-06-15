<?php

namespace App\Models;

use App\Enums\OrganizationMembershipStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrganizationMembership extends Pivot
{
    protected $table = 'organization_user';

    public $incrementing = true;

    protected $fillable = [
        'organization_id',
        'user_id',
        'role',
        'status',
        'is_owner',
        'invited_by',
        'invited_at',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrganizationMembershipStatus::class,
            'is_owner' => 'boolean',
            'invited_at' => 'datetime',
            'joined_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
