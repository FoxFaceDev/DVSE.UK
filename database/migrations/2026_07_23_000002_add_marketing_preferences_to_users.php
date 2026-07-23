<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('marketing_email_opt_in')->default(false)->index();
            $table->timestamp('marketing_email_opted_in_at')->nullable();
            $table->timestamp('marketing_email_unsubscribed_at')->nullable();
            $table->string('marketing_email_consent_source', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'marketing_email_opt_in',
                'marketing_email_opted_in_at',
                'marketing_email_unsubscribed_at',
                'marketing_email_consent_source',
            ]);
        });
    }
};
