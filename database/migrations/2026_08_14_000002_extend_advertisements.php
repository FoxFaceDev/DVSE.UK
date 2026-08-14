<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->enum('display_type', ['question', 'site'])->default('question')->after('title')->index();
            $table->json('placements')->nullable()->after('display_type');
            $table->string('advertiser_email')->nullable()->after('link_url');
            $table->dateTime('starts_at')->nullable()->after('advertiser_email')->index();
            $table->dateTime('expires_at')->nullable()->after('starts_at')->index();
            $table->dateTime('activation_notified_at')->nullable()->after('expires_at');
            $table->dateTime('expiry_warning_notified_at')->nullable()->after('activation_notified_at');
        });

        Schema::table('cgi_clips', function (Blueprint $table) {
            $table->string('thumbnail_path')->nullable()->after('media_url');
        });

        Schema::table('content_pages', function (Blueprint $table) {
            $table->string('library_category')->nullable()->after('admin_title')->index();
        });
    }

    public function down(): void
    {
        Schema::table('content_pages', function (Blueprint $table) {
            $table->dropIndex(['library_category']);
            $table->dropColumn('library_category');
        });
        Schema::table('cgi_clips', fn (Blueprint $table) => $table->dropColumn('thumbnail_path'));
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn(['display_type', 'placements', 'advertiser_email', 'starts_at', 'expires_at', 'activation_notified_at', 'expiry_warning_notified_at']);
        });
    }
};
