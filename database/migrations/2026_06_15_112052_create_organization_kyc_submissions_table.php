<?php

use App\Enums\OrganizationKycStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_kyc_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default(OrganizationKycStatus::DRAFT->value)->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('business_type')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('tax_identifier')->nullable();
            $table->string('legal_name')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 32)->nullable();
            $table->string('contact_email')->nullable();
            $table->string('payout_method')->nullable();
            $table->string('payout_account_name')->nullable();
            $table->string('payout_account_number')->nullable();
            $table->string('payout_provider')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_kyc_submissions');
    }
};
