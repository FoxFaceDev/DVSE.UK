<x-layouts.app :showBack="true" :backUrl="route('frontend.section', $subSection->section_id)" title="Mock Test Rules">
    <div class="max-w-md mx-auto space-y-6">
        <div class="text-center mb-8">
            <h1 class="font-heading font-bold text-2xl text-primary-dark">Mock Test Theory</h1>
            <p class="text-gray-500 mt-2 text-sm">Official simulation of the real driving theory test.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">
            <div class="bg-blue-50 border-b border-blue-100 p-4">
                <h2 class="font-bold text-primary flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Important Instructions
                </h2>
            </div>
            <div class="p-6 space-y-5 text-sm text-gray-700">
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="mt-0.5 bg-blue-100 text-blue-600 p-1.5 rounded-md flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <strong class="block text-gray-900 text-base mb-0.5">50 Questions</strong>
                            You must answer all 50 multiple-choice questions.
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="mt-0.5 bg-amber-100 text-amber-600 p-1.5 rounded-md flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <strong class="block text-gray-900 text-base mb-0.5">57 Minutes Time Limit</strong>
                            A countdown timer will run during the test. When time expires, your test will submit automatically.
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="mt-0.5 bg-green-100 text-green-600 p-1.5 rounded-md flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <strong class="block text-gray-900 text-base mb-0.5">Pass Mark: 43 out of 50</strong>
                            You must score at least 43 correct answers to pass the mock test.
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="mt-0.5 bg-red-100 text-red-600 p-1.5 rounded-md flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </div>
                        <div>
                            <strong class="block text-gray-900 text-base mb-0.5">Exam Conditions</strong>
                            Please read the questions carefully. No explanations will be shown during the test, and translations are disabled.
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <a href="{{ route('theory.mock_test_start', $subSection->id) }}" class="mt-8 w-full block text-center py-4 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold text-lg shadow-[0_4px_14px_0_rgba(0,118,255,0.39)] transform hover:-translate-y-0.5 transition-all duration-200">
            Start Mock Test Now
        </a>
    </div>
</x-layouts.app>
