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
            $table->unsignedInteger('position')->default(0)->after('icon_path');
            $table->index(['topicable_type', 'topicable_id', 'position']);
        });

        $positions = [];
        DB::table('topics')->orderBy('topicable_type')->orderBy('topicable_id')->orderBy('id')->each(function ($category) use (&$positions) {
            $key = $category->topicable_type.':'.$category->topicable_id;
            $positions[$key] = ($positions[$key] ?? 0) + 1;
            DB::table('topics')->where('id', $category->id)->update(['position' => $positions[$key]]);
        });
    }

    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropIndex(['topicable_type', 'topicable_id', 'position']);
            $table->dropColumn('position');
        });
    }
};
