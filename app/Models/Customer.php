<?php

namespace App\Models;

use App\Enums\CustomerStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_id',
        'phone_number',
        'email',
        'first_name',
        'last_name',
        'status',
        'last_verified_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => CustomerStatus::class,
            'last_verified_at' => 'datetime',
        ];
    }
}
