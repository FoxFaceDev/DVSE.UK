<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['ad_id', 'category_id']);
        });

        Schema::table('ads', function (Blueprint $table) {
            $table->boolean('targets_all_categories')->default(false)->index()->after('category_id');
        });

        DB::table('ads')->orderBy('id')->each(function ($ad) {
            if ($ad->category_id) {
                DB::table('ad_category')->insertOrIgnore([
                    'ad_id' => $ad->id,
                    'category_id' => $ad->category_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return;
            }

            DB::table('ads')->where('id', $ad->id)->update([
                'targets_all_categories' => true,
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_category');

        Schema::table('ads', function (Blueprint $table) {
            $table->dropIndex(['targets_all_categories']);
            $table->dropColumn('targets_all_categories');
        });
    }
};
