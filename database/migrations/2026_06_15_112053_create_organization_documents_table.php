<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_kyc_submission_id')->constrained()->cascadeOnDelete();
            $table->string('document_type');
            $table->string('storage_disk')->default('local');
            $table->string('storage_path');
            $table->string('original_name');
            $table->string('mime_type', 127);
            $table->unsignedBigInteger('file_size')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_documents');
    }
};
