<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 12)->unique();
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->string('question_type')->default('text')->after('topic_id');
            $table->json('translations')->nullable()->after('explanation_ku');
        });

        Schema::table('choices', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('text_ku');
            $table->json('translations')->nullable()->after('image_path');
        });

        Schema::table('content_pages', function (Blueprint $table) {
            $table->json('translations')->nullable()->after('additional_sign_images');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->boolean('is_superadmin')->default(false)->after('password');
        });

        DB::table('languages')->insert([
            ['name' => 'English', 'code' => 'en', 'direction' => 'ltr', 'is_active' => true, 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kurdish', 'code' => 'ku', 'direction' => 'rtl', 'is_active' => true, 'sort_order' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('admins')->update(['is_superadmin' => true]);
    }

    public function down(): void
    {
        Schema::table('admins', fn (Blueprint $table) => $table->dropColumn('is_superadmin'));
        Schema::table('content_pages', fn (Blueprint $table) => $table->dropColumn('translations'));
        Schema::table('choices', fn (Blueprint $table) => $table->dropColumn(['image_path', 'translations']));
        Schema::table('questions', fn (Blueprint $table) => $table->dropColumn(['question_type', 'translations']));
        Schema::dropIfExists('languages');
    }
};
