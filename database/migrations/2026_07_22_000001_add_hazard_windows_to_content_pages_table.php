<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_pages', function (Blueprint $table) {
            $table->json('hazard_windows')->nullable()->after('hazard_window_end');
        });

        DB::table('content_pages')
            ->whereNotNull('hazard_window_start')
            ->whereNotNull('hazard_window_end')
            ->orderBy('id')
            ->get(['id', 'hazard_window_start', 'hazard_window_end'])
            ->each(function ($page) {
                DB::table('content_pages')
                    ->where('id', $page->id)
                    ->update([
                        'hazard_windows' => json_encode([[
                            'start' => (float) $page->hazard_window_start,
                            'end' => (float) $page->hazard_window_end,
                            'points' => 5,
                        ]]),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('content_pages', function (Blueprint $table) {
            $table->dropColumn('hazard_windows');
        });
    }
};
