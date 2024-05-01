<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('tv_seasons', function (Blueprint $table): void {
            $table->unsignedBigInteger('tmdb_id')->primary();
            $table->unsignedBigInteger('tv_show_id');
            $table->integer('number');
            $table->string('name')->nullable();
            $table->text('overview')->nullable();
            $table->string('poster')->nullable();
            $table->date('first_air_date')->nullable()->index();
            $table->integer('release_year')->nullable()->index();
            $table->decimal('trakt_score', 9, 2)->default(0);
            $table->bigInteger('tvdb_id')->unsigned()->nullable()->index();
            $table->json('locked_fields')->nullable();
            $table->timestamps();

            $table->foreign('tv_show_id')->references('tmdb_id')->on('tv_shows')->cascadeOnDelete();
            $table->index(['number']);
            $table->index(['tv_show_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_seasons');
    }
};
