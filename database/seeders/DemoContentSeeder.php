<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\Category;
use App\Models\Question;
use App\Models\Choice;
use App\Models\Ad;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure we have a sub-section to attach categories to
        $section = Section::firstOrCreate(['name' => 'Theory Test Practice'], [
            'color' => '#3b82f6'
        ]);

        $subSection = SubSection::firstOrCreate([
            'section_id' => $section->id,
            'name' => 'Theory Test Practice'
        ], [
            'color' => '#3b82f6'
        ]);

        // 2. Create a specific Category for Testing
        $category = Category::create([
            'sub_section_id' => $subSection->id,
            'name_en' => 'Video & Media Test Category',
            'name_ku' => 'پۆلی تاقیکردنەوەی ڤیدیۆ و میدیا'
        ]);

        // 3. Create Questions with different media types
        
        // Question 1: Image
        $q1 = Question::create([
            'category_id' => $category->id,
            'text_en' => 'What is the primary message of this visual warning?',
            'text_ku' => 'پەیامی سەرەکی ئەم ئاگادارکردنەوە بینراوە چییە؟',
            'media_type' => 'image',
            'media_url' => 'https://picsum.photos/id/237/800/400',
            'explanation_en' => 'This is a standard warning sign indicating potential hazards ahead.',
            'explanation_ku' => 'ئەمە نیشانەیەکی ئاگادارکردنەوەی ستانداردە کە ئاماژە بە مەترسییە ئەگەرییەکانی پێشەوە دەکات.'
        ]);
        $this->addChoices($q1, ['Stop immediately', 'Proceed with caution', 'Road closed', 'No entry'], 1);

        // Question 2: YouTube Video
        $q2 = Question::create([
            'category_id' => $category->id,
            'text_en' => 'Watch this video carefully. What should the driver do at the intersection?',
            'text_ku' => 'بە وریاییەوە سەیری ئەم ڤیدیۆیە بکە. شۆفێرەکە دەبێت لە یەکتربڕەکەدا چی بکات؟',
            'media_type' => 'video',
            'media_url' => 'https://www.youtube.com/watch?v=OCafg91DBgY',
            'explanation_en' => 'The video demonstrates the importance of yielding to traffic on the right.',
            'explanation_ku' => 'ڤیدیۆکە گرنگی پێدانی ئەولەویەت بە هاتووچۆی لای ڕاست نیشان دەدات.'
        ]);
        $this->addChoices($q2, ['Speed up', 'Yield to the right', 'Turn left immediately', 'Ignore the signs'], 1);

        // Question 3: GIF (Using an image URL but type set to gif)
        $q3 = Question::create([
            'category_id' => $category->id,
            'text_en' => 'This animation shows a common engine fault. Which part is failing?',
            'text_ku' => 'ئەم ئەنیمەیشنە هەڵەیەکی باوی بزوێنەر نیشان دەدات. کام بەشە تێکچووە؟',
            'media_type' => 'gif',
            'media_url' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExNHJndHlyZndyeXJyeXJyeXJyeXJyeXJyeXJyeXJyeXJyeXJyeXJ5JmVwPXYxX2ludGVybmFsX2dpZl9ieV9pZCZjdD1n/3o7TKMGpxx6xYF1Y6A/giphy.gif',
            'explanation_en' => 'The highlighted area shows the spark plug misfiring.',
            'explanation_ku' => 'ناوچە دیاریکراوەکە نیشان دەدات کە پلاکەکە بە باشی کار ناکات.'
        ]);
        $this->addChoices($q3, ['Piston', 'Spark Plug', 'Radiator', 'Battery'], 1);

        // Create 10 more generic questions to reach the 14-15 midpoint for ads
        for ($i = 4; $i <= 20; $i++) {
            $q = Question::create([
                'category_id' => $category->id,
                'text_en' => "Test Question Number $i",
                'text_ku' => "پرسیاری تاقیکردنەوە ژمارە $i",
                'explanation_en' => 'Generic explanation for demo purposes.',
                'explanation_ku' => 'شیکردنەوەی گشتی بۆ مەبەستی تاقیکردنەوە.'
            ]);
            $this->addChoices($q, ['Option A', 'Option B', 'Option C', 'Option D'], 0);
        }

        // 4. Create Ads
        
        // Ad 1: Global Image Ad
        Ad::create([
            'title' => 'Global Insurance Ad',
            'media_type' => 'image',
            'media_url' => 'https://picsum.photos/id/1/800/400',
            'link_url' => 'https://www.google.com/search?q=car+insurance',
            'is_active' => true,
            'category_id' => null // Global
        ]);

        // Ad 2: Category Specific YouTube Ad
        Ad::create([
            'title' => 'Category Specific Video Ad',
            'media_type' => 'video',
            'media_url' => 'https://www.youtube.com/watch?v=ScMzIvxBSi4',
            'link_url' => 'https://dvse.uk',
            'is_active' => true,
            'category_id' => $category->id
        ]);

        $this->command->info('Demo content seeded successfully!');
    }

    private function addChoices($question, $choices, $correctIndex)
    {
        foreach ($choices as $index => $text) {
            Choice::create([
                'question_id' => $question->id,
                'text_en' => $text,
                'text_ku' => 'وەڵامی تاقیکردنەوە ' . ($index + 1),
                'is_correct' => ($index === $correctIndex)
            ]);
        }
    }
}
