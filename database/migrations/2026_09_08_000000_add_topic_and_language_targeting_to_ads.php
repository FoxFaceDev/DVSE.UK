<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_topic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['ad_id', 'topic_id']);
        });

        Schema::create('ad_language', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['ad_id', 'language_id']);
        });

        Schema::table('ads', function (Blueprint $table) {
            $table->boolean('targets_all_topics')->default(false)->index()->after('targets_all_categories');
        });

        DB::table('ads')->orderBy('id')->each(function ($ad) {
            DB::table('ads')->where('id', $ad->id)->update([
                'targets_all_topics' => (bool) $ad->targets_all_categories,
            ]);

            if ($ad->language_id) {
                DB::table('ad_language')->insertOrIgnore([
                    'ad_id' => $ad->id,
                    'language_id' => $ad->language_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $categoryIds = DB::table('ad_category')->where('ad_id', $ad->id)->pluck('category_id');
            $topicIds = DB::table('topics')
                ->where('topicable_type', 'App\\Models\\Category')
                ->whereIn('topicable_id', $categoryIds)
                ->pluck('id');

            foreach ($topicIds as $topicId) {
                DB::table('ad_topic')->insertOrIgnore([
                    'ad_id' => $ad->id,
                    'topic_id' => $topicId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_language');
        Schema::dropIfExists('ad_topic');
        Schema::table('ads', fn (Blueprint $table) => $table->dropColumn('targets_all_topics'));
    }
};
