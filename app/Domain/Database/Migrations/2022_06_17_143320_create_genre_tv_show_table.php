<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('genre_tv_show', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('genre_id');
            $table->unsignedBigInteger('tv_show_id');

            $table->foreign('genre_id')->references('id')->on('genres')->cascadeOnDelete();
            $table->foreign('tv_show_id')->references('tmdb_id')->on('tv_shows')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('genre_tv_show');
    }
};
