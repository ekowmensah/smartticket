<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'organization_kyc_submission_id',
        'document_type',
        'storage_disk',
        'storage_path',
        'original_name',
        'mime_type',
        'file_size',
        'uploaded_by',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function kycSubmission(): BelongsTo
    {
        return $this->belongsTo(OrganizationKycSubmission::class, 'organization_kyc_submission_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
