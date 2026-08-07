<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->foreignId('language_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        $englishLanguageId = DB::table('languages')->where('code', 'en')->value('id');

        if ($englishLanguageId) {
            DB::table('ads')->whereNull('language_id')->update(['language_id' => $englishLanguageId]);
        }
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('language_id');
        });
    }
};
