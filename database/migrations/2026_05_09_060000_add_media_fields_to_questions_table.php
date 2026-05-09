<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->renameColumn('image_path', 'media_path');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->string('media_type')->nullable()->default(null)->after('media_path');
            $table->string('media_url')->nullable()->after('media_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'media_url']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->renameColumn('media_path', 'image_path');
        });
    }
};
