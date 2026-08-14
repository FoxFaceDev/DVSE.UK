<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('preferred_language_id')->nullable()->after('account_type')->constrained('languages')->nullOnDelete();
            $table->timestamp('privacy_accepted_at')->nullable()->after('preferred_language_id');
        });

        DB::table('site_settings')->insert([
            ['key' => 'privacy_policy', 'value' => 'We respect your privacy. Your account information is used to provide and improve DVSE Platform learning services and is not sold to third parties.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'about_us', 'value' => 'DVSE Platform helps learners prepare confidently with current theory and hazard-perception practice.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_us', 'value' => 'Need help? Contact the DVSE Platform team using any of the channels below.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_email', 'value' => 'support@dvse.uk', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'whatsapp_number', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_links', 'value' => '{}', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('preferred_language_id');
            $table->dropColumn('privacy_accepted_at');
        });
        Schema::dropIfExists('site_settings');
    }
};
