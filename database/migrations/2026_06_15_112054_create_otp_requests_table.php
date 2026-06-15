<?php

use App\Enums\OtpChannel;
use App\Enums\OtpPurpose;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone_number', 32)->index();
            $table->string('channel')->default(OtpChannel::SMS->value);
            $table->string('purpose')->default(OtpPurpose::LOGIN->value);
            $table->string('code_hash');
            $table->timestamp('expires_at')->index();
            $table->timestamp('consumed_at')->nullable();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['phone_number', 'purpose', 'expires_at'], 'otp_requests_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_requests');
    }
};
