<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('keyword_tv_show', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('keyword_id');
            $table->unsignedBigInteger('tv_show_id');

            $table->foreign('keyword_id')->references('tmdb_id')->on('keywords')->cascadeOnDelete();
            $table->foreign('tv_show_id')->references('tmdb_id')->on('tv_shows')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keyword_tv_show');
    }
};
