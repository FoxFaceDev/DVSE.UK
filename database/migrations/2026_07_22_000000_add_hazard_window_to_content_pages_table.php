<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_pages', function (Blueprint $table) {
            $table->decimal('hazard_window_start', 8, 2)->nullable()->after('text_ku');
            $table->decimal('hazard_window_end', 8, 2)->nullable()->after('hazard_window_start');
        });
    }

    public function down(): void
    {
        Schema::table('content_pages', function (Blueprint $table) {
            $table->dropColumn(['hazard_window_start', 'hazard_window_end']);
        });
    }
};
