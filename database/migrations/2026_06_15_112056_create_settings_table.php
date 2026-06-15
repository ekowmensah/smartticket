<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('scope');
            $table->unsignedBigInteger('scope_id')->default(0);
            $table->string('key');
            $table->json('value')->nullable();
            $table->timestamps();

            $table->unique(['scope', 'scope_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
