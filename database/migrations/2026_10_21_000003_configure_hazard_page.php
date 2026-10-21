<?php

use App\Support\HazardPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sub_sections', function (Blueprint $table) {
            $table->string('page_type', 30)->nullable();
        });

        DB::table('sub_sections')->whereRaw('LOWER(name) LIKE ?', ['%hazard%perception%'])->update(['page_type' => 'hazard']);
        DB::table('site_settings')->insertOrIgnore([
            'key' => 'hazard_page', 'value' => json_encode(HazardPage::defaults()),
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('sub_sections', fn (Blueprint $table) => $table->dropColumn('page_type'));
        DB::table('site_settings')->where('key', 'hazard_page')->delete();
    }
};
