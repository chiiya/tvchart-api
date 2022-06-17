<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('language_tv_show', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tv_show_id');
            $table->string('language_code');

            $table->foreign('tv_show_id')->references('tmdb_id')->on('tv_shows')->cascadeOnDelete();
            $table->foreign('language_code')->references('language_code')->on('languages')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('language_tv_show');
    }
};
