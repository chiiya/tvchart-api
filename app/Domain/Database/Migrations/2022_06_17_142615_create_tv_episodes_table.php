<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('tv_episodes', function (Blueprint $table): void {
            $table->unsignedBigInteger('tmdb_id')->primary();
            $table->integer('number')->default(0);
            $table->string('name')->nullable();
            $table->date('first_air_date')->nullable();
            $table->text('overview')->nullable();
            $table->integer('runtime')->nullable();
            $table->string('still')->nullable();
            $table->unsignedBigInteger('tv_season_id');
            $table->bigInteger('tvdb_id')->unsigned()->nullable()->index();
            $table->json('locked_fields')->nullable();
            $table->timestamps();

            $table->foreign('tv_season_id')->references('tmdb_id')->on('tv_seasons')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_episodes');
    }
};
