<?php

use App\Models\Category;
use App\Models\SubSection;
use App\Models\Topic;
use App\Support\RevisionCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->foreignId('revision_group_id')->nullable()->constrained('categories')->nullOnDelete();
        });

        Topic::whereIn('topicable_type', [SubSection::class, Category::class])
            ->whereRaw('LOWER(name_en) LIKE ?', ['%revision%question%'])
            ->orderBy('id')->each(fn (Topic $entry) => RevisionCatalog::connectEntry($entry));
    }

    public function down(): void
    {
        // Keep the revision cards and all questions when removing the navigation link.
        Schema::table('topics', fn (Blueprint $table) => $table->dropConstrainedForeignId('revision_group_id'));
    }
};
