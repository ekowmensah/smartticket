<?php

use App\Enums\OrganizationApprovalStatus;
use App\Enums\OrganizationStatus;
use App\Enums\OrganizationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default(OrganizationType::BUSINESS->value);
            $table->string('public_email')->nullable();
            $table->string('public_phone', 32)->nullable();
            $table->string('status')->default(OrganizationStatus::PENDING->value)->index();
            $table->string('approval_status')->default(OrganizationApprovalStatus::PENDING->value)->index();
            $table->text('suspension_reason')->nullable();
            $table->string('timezone')->default('Africa/Accra');
            $table->char('currency_code', 3)->default('GHS');
            $table->char('country_code', 2)->default('GH');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
