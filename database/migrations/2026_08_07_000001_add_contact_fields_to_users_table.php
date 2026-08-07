<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number', 30)->nullable()->after('email');
            $table->string('country', 100)->nullable()->after('phone_number');
            $table->string('city', 100)->nullable()->after('country');
            $table->string('address', 500)->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['phone_number', 'country', 'city', 'address']));
    }
};
