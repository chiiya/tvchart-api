<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tv_shows', function (Blueprint $table) {
            $table->dropColumn('popularity');
            $table->dropColumn('locked_fields');
        });
    }

    public function down(): void
    {
        Schema::table('tv_shows', function (Blueprint $table) {
            $table->float('popularity')->nullable();
            $table->json('locked_fields')->nullable();
        });
    }
};
