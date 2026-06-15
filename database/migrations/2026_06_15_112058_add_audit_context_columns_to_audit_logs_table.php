<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(config('activitylog.table_name'), function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('causer_id')->constrained()->nullOnDelete();
            $table->string('request_id', 100)->nullable()->after('organization_id')->index();
            $table->string('ip_address', 45)->nullable()->after('request_id');
            $table->text('user_agent')->nullable()->after('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table(config('activitylog.table_name'), function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
            $table->dropIndex(['request_id']);
            $table->dropColumn([
                'request_id',
                'ip_address',
                'user_agent',
            ]);
        });
    }
};
