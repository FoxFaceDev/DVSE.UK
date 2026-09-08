<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->string('description', 180)->nullable()->after('name');
        });

        Schema::table('sub_sections', function (Blueprint $table) {
            $table->string('description', 180)->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('sub_sections', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
