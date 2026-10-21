<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->string('content_type', 30)->default('questions')->after('name_ku');
            $table->string('color', 50)->nullable()->after('content_type');
            $table->string('icon_path')->nullable()->after('color');
        });

        DB::table('topics')->orderBy('id')->each(function ($category) {
            $hasQuestions = DB::table('questions')->where('topic_id', $category->id)->exists();
            $hasPages = DB::table('content_pages')->where('topic_id', $category->id)->exists();

            if ($hasQuestions && $hasPages) {
                $learningCategoryId = DB::table('topics')->insertGetId([
                    'topicable_type' => $category->topicable_type,
                    'topicable_id' => $category->topicable_id,
                    'name_en' => $category->name_en.' Learning',
                    'name_ku' => $category->name_ku,
                    'content_type' => 'learning_pages',
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                ]);
                DB::table('content_pages')->where('topic_id', $category->id)->update(['topic_id' => $learningCategoryId]);
            } elseif ($hasPages) {
                DB::table('topics')->where('id', $category->id)->update(['content_type' => 'learning_pages']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn(['content_type', 'color', 'icon_path']);
        });
    }
};
