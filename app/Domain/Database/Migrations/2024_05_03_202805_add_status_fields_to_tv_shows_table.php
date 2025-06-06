<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tv_shows', function (Blueprint $table) {
            $table->dateTime('status_updated_at')->nullable();
            $table->string('blacklist_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tv_shows', function (Blueprint $table) {
            $table->dropColumn('status_updated_at');
            $table->dropColumn('blacklist_reason');
        });
    }
};
