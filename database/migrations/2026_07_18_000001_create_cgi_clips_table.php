<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cgi_clips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_page_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('slot');
            $table->string('media_path')->nullable();
            $table->string('media_url', 2048)->nullable();
            $table->timestamps();

            $table->unique(['content_page_id', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cgi_clips');
    }
};
