<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_pages', function (Blueprint $table) {
            $table->text('what_to_do_en')->nullable()->after('explanation_ku');
            $table->text('what_to_do_ku')->nullable()->after('what_to_do_en');
            $table->json('additional_sign_images')->nullable()->after('what_to_do_ku');
        });
    }

    public function down(): void
    {
        Schema::table('content_pages', function (Blueprint $table) {
            $table->dropColumn(['what_to_do_en', 'what_to_do_ku', 'additional_sign_images']);
        });
    }
};
