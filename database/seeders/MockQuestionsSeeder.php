<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Choice;
use App\Models\Category;

class MockQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first category to attach these extra questions to.
        $category = Category::first();
        if (!$category) {
            $this->command->error("No categories found. Please run DemoContentSeeder first.");
            return;
        }

        $this->command->info("Seeding 35 extra mock questions to pad the database...");

        for ($i = 1; $i <= 35; $i++) {
            $question = Question::create([
                'category_id' => $category->id,
                'text_en' => "Mock Exam Question Number $i - What should you do in this traffic scenario?",
                'text_ku' => "پرسیاری تاقیکردنەوەی ئەزموونی ژمارە $i",
                'explanation_en' => "Explanation for mock question $i (Hidden during mock test).",
                'explanation_ku' => "ڕوونکردنەوە شاراوە",
                'media_type' => null,
                'media_url' => null
            ]);

            $correctIndex = rand(0, 3);
            
            $options = ['Stop completely', 'Speed up and pass', 'Honk your horn', 'Flash your headlights'];

            foreach ($options as $index => $text) {
                Choice::create([
                    'question_id' => $question->id,
                    'text_en' => $text . ' (Option ' . ($index + 1) . ')',
                    'text_ku' => 'هەڵبژاردەی ' . ($index + 1),
                    'is_correct' => ($index === $correctIndex)
                ]);
            }
        }

        $this->command->info("Successfully seeded 35 extra questions.");
    }
}
