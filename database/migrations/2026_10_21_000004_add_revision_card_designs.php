<?php

use App\Models\Category;
use App\Support\RevisionCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', fn (Blueprint $table) => $table->string('page_type', 30)->nullable());
        Schema::table('topics', fn (Blueprint $table) => $table->string('icon_preset', 80)->nullable());

        Category::whereRaw('LOWER(name_en) LIKE ?', ['%revision%question%'])->each(function (Category $group) {
            $group->update(['page_type' => 'revision']);
            RevisionCatalog::populate($group);
        });
    }

    public function down(): void
    {
        // Keep categories and their questions when removing presentation metadata.
        Schema::table('topics', fn (Blueprint $table) => $table->dropColumn('icon_preset'));
        Schema::table('categories', fn (Blueprint $table) => $table->dropColumn('page_type'));
    }
};
