<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Category;

class VehicleHandlingSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('name_en', 'Vehicle Handling')->first();

        if (!$category) {
            return;
        }

        $questions = [
            [
                'text_en' => 'How can you use your vehicle’s engine as a brake?',
                'text_ku' => 'چۆن دەتوانیت بزوێنەری ئۆتۆمبێلەکەت وەک فڕێن بەکاربهێنیت؟',
                'explanation_en' => 'Selecting a lower gear when driving down a steep hill will help you control your speed and prevent your brakes from overheating.',
                'explanation_ku' => 'هەڵبژاردنی گێڕێکی نزمتر کاتێک لەسەر زوورگێکی توند دەخوڕیت یارمەتیت دەدات کۆنترۆڵکردنی خێراییەکەت بکەیت و ڕێگری دەکات لە گەرمبوونی زۆری فڕێنەکانت.',
                'image_path' => '/storage/questions/hill.png',
                'choices' => [
                    ['text_en' => 'By shifting into a lower gear', 'text_ku' => 'بە گۆڕینی بۆ گێڕێکی نزمتر', 'is_correct' => true],
                    ['text_en' => 'By shifting into neutral', 'text_ku' => 'بە گۆڕینی بۆ گێڕی بەتاڵ', 'is_correct' => false],
                    ['text_en' => 'By turning off the engine', 'text_ku' => 'بە کوژاندنەوەی بزوێنەرەکە', 'is_correct' => false],
                    ['text_en' => 'By using the handbrake', 'text_ku' => 'بە بەکارهێنانی هاندبرێک', 'is_correct' => false],
                ]
            ],
            [
                'text_en' => 'You are driving in heavy rain. Why should you keep a good distance from the vehicle in front?',
                'text_ku' => 'تۆ لە بارانێکی بەهێزدا دەخوڕیت. بۆچی دەبێت مەودایەکی باش لە ئۆتۆمبێلی پێشەوەت بپارێزیت؟',
                'explanation_en' => 'Spray from other vehicles can significantly reduce your visibility and the road will be slippery, increasing your total stopping distance.',
                'explanation_ku' => 'پژانی ئاو لە ئۆتۆمبێلەکانی ترەوە دەتوانێت بە شێوەیەکی بەرچاو بینینت کەم بکاتەوە و ڕێگاکە نامۆ دەبێت، ئەمەش مەودای وەستانی گشتیت زیاد دەکات.',
                'image_path' => '/storage/questions/rain.png',
                'choices' => [
                    ['text_en' => 'Because your brakes will be less effective', 'text_ku' => 'چونکە فڕێنەکانت کەمتر کارا دەبن', 'is_correct' => true],
                    ['text_en' => 'To keep your car clean', 'text_ku' => 'بۆ پاک ڕاگرتنی ئۆتۆمبێلەکەت', 'is_correct' => false],
                    ['text_en' => 'To allow others to overtake you', 'text_ku' => 'بۆ ڕێگەدان بەوانی تر لە تۆ پێشبکەون', 'is_correct' => false],
                    ['text_en' => 'To save fuel', 'text_ku' => 'بۆ پاشەکەوتکردنی سووتەمەنی', 'is_correct' => false],
                ]
            ],
            [
                'text_en' => 'What’s the first thing you should do if your vehicle starts to skid?',
                'text_ku' => 'یەکەم شت کە دەبێت بیکەیت ئەگەر ئۆتۆمبێلەکەت دەستی کرد بە خلیسکان چییە؟',
                'explanation_en' => 'Release the footbrake. This will allow the wheels to turn again and help you regain steering control.',
                'explanation_ku' => 'فڕێنی پێ کوژێوە. ئەمە ڕێگە دەدات بە چەرخەکان دووبارە بسوڕێنەوە و یارمەتیت دەدات کۆنترۆڵی سوکانت وەربگریتەوە.',
                'image_path' => '/storage/questions/skid.png',
                'choices' => [
                    ['text_en' => 'Release the footbrake', 'text_ku' => 'فڕێنی پێ بەردە', 'is_correct' => true],
                    ['text_en' => 'Apply the handbrake', 'text_ku' => 'هاندبرێک بکێشە', 'is_correct' => false],
                    ['text_en' => 'Steer in the opposite direction', 'text_ku' => 'سوکان بە ئاڕاستەی پێچەوانە بسووڕێنە', 'is_correct' => false],
                    ['text_en' => 'Accelerate hard', 'text_ku' => 'بە توندی خێرایی زیاد بکە', 'is_correct' => false],
                ]
            ],
            [
                'text_en' => 'What is the primary reason for lower speed limits in residential areas?',
                'text_ku' => 'هۆکاری سەرەکی بۆ خێراییەکی کەمتر لە ناوچەی نیشتەجێبووندا چییە؟',
                'explanation_en' => 'Lower speed limits are designed to reduce hazards in areas with pedestrians, especially children and the elderly.',
                'explanation_ku' => 'خێراییە کەمەکان بۆ کەمکردنەوەی مەترسییەکان لەو شوێنانەی پیادەی لێیە، بەتایبەت منداڵ و بەتەمەنەکان.',
                'image_path' => '/storage/questions/speed.png',
                'choices' => [
                    ['text_en' => 'To protect vulnerable road users', 'text_ku' => 'بۆ پاراستنی بەکارهێنەرانی ڕێگا کە لاوازن', 'is_correct' => true],
                    ['text_en' => 'To reduce noise pollution', 'text_ku' => 'بۆ کەمکردنەوەی پیسبوونی دەنگ', 'is_correct' => false],
                    ['text_en' => 'To help residents park more easily', 'text_ku' => 'بۆ یارمەتیدانی نیشتەجێبووان بۆ پارکینگ بە ئاسانی', 'is_correct' => false],
                    ['text_en' => 'To keep the roads in better condition', 'text_ku' => 'بۆ پاراستنی ڕێگاکان بە دۆخێکی باشتر', 'is_correct' => false],
                ]
            ],
            [
                'text_en' => 'Why is it dangerous to drive in neutral for long periods (coasting)?',
                'text_ku' => 'بۆچی مەترسیدارە بە گێڕی بەتاڵ بۆ ماوەیەکی درێژ بخوڕیت؟',
                'explanation_en' => 'Driving in neutral reduces your control over the vehicle, particularly affecting steering and engine braking.',
                'explanation_ku' => 'خوڕین بە گێڕی بەتاڵ کۆنترۆڵت لەسەر ئۆتۆمبێلەکە کەم دەکاتەوە، بەتایبەتی کاریگەری لەسەر سوکان و فڕێنی بزوێنەر دەبێت.',
                'image_path' => '/storage/questions/neutral.png',
                'choices' => [
                    ['text_en' => 'It reduces your control over the vehicle', 'text_ku' => 'کۆنترۆڵت لەسەر ئۆتۆمبێلەکە کەم دەکاتەوە', 'is_correct' => true],
                    ['text_en' => 'It causes the engine to stall', 'text_ku' => 'دەبێتە هۆی کوژانەوەی بزوێنەرەکە', 'is_correct' => false],
                    ['text_en' => 'It uses more fuel', 'text_ku' => 'سووتەمەنی زیاتر بەکاردەهێنێت', 'is_correct' => false],
                    ['text_en' => 'It makes the car harder to steer', 'text_ku' => 'خوڕینی ئۆتۆمبێلەکە قورستر دەکات', 'is_correct' => false],
                ]
            ],
        ];

        foreach ($questions as $q) {
            $question = Question::create([
                'category_id' => $category->id,
                'text_en' => $q['text_en'],
                'text_ku' => $q['text_ku'],
                'explanation_en' => $q['explanation_en'],
                'explanation_ku' => $q['explanation_ku'],
                'image_path' => $q['image_path'],
            ]);

            foreach ($q['choices'] as $choice) {
                $question->choices()->create($choice);
            }
        }
    }
}
