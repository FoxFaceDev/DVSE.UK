<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_pages', function (Blueprint $table) {
            $table->string('admin_title')->nullable()->after('category_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('content_pages', function (Blueprint $table) {
            $table->dropColumn('admin_title');
        });
    }
};
