<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mock_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_section_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['theory', 'hazard']);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('question_count')->default(50);
            $table->unsignedSmallInteger('video_question_count')->default(3);
            $table->unsignedSmallInteger('duration_minutes')->default(57);
            $table->unsignedSmallInteger('pass_mark')->default(43);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('mock_test_topic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->unique(['mock_test_id', 'topic_id']);
        });

        $theorySubSectionId = DB::table('sub_sections')->whereRaw('LOWER(name) = ?', ['mock test theory'])->value('id');
        if ($theorySubSectionId) {
            DB::table('mock_tests')->insert(['sub_section_id' => $theorySubSectionId, 'name' => 'Mock Test Theory', 'type' => 'theory', 'description' => 'Official-style English theory test.', 'question_count' => 50, 'video_question_count' => 3, 'duration_minutes' => 57, 'pass_mark' => 43, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        }

        $hazardSubSectionId = DB::table('sub_sections')->whereRaw('LOWER(name) = ?', ['hazard perception'])->value('id');
        if ($hazardSubSectionId) {
            DB::table('mock_tests')->insert(['sub_section_id' => $hazardSubSectionId, 'name' => 'Hazard Mock Test', 'type' => 'hazard', 'description' => 'Official-style hazard perception test.', 'question_count' => 14, 'video_question_count' => 0, 'duration_minutes' => 15, 'pass_mark' => 44, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mock_test_topic');
        Schema::dropIfExists('mock_tests');
    }
};
