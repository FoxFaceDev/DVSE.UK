<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hazard_learning_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('content_page_id')->constrained()->cascadeOnDelete();
            $table->timestamp('watched_at');
            $table->timestamps();

            $table->unique(['user_id', 'content_page_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hazard_learning_progress');
    }
};
