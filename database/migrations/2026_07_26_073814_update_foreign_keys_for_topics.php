<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('topic_id')->nullable()->constrained()->cascadeOnDelete();
        });
        Schema::table('content_pages', function (Blueprint $table) {
            $table->foreignId('topic_id')->nullable()->constrained()->cascadeOnDelete();
        });

        $categories = \Illuminate\Support\Facades\DB::table('categories')->get();
        foreach ($categories as $category) {
            $topicId = \Illuminate\Support\Facades\DB::table('topics')->insertGetId([
                'topicable_type' => 'App\Models\Category',
                'topicable_id' => $category->id,
                'name_en' => 'General Topic',
                'name_ku' => 'بابەتی گشتی',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            \Illuminate\Support\Facades\DB::table('questions')->where('category_id', $category->id)->update(['topic_id' => $topicId]);
            \Illuminate\Support\Facades\DB::table('content_pages')->where('category_id', $category->id)->update(['topic_id' => $topicId]);
        }

        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
        
        Schema::table('content_pages', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
        });
        Schema::table('content_pages', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
        });

        $topics = \Illuminate\Support\Facades\DB::table('topics')->where('topicable_type', 'App\Models\Category')->get();
        foreach ($topics as $topic) {
            \Illuminate\Support\Facades\DB::table('questions')->where('topic_id', $topic->id)->update(['category_id' => $topic->topicable_id]);
            \Illuminate\Support\Facades\DB::table('content_pages')->where('topic_id', $topic->id)->update(['category_id' => $topic->topicable_id]);
        }

        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            $table->dropColumn('topic_id');
        });
        Schema::table('content_pages', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            $table->dropColumn('topic_id');
        });
    }
};
