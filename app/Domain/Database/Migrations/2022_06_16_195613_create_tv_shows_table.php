<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('tv_shows', function (Blueprint $table): void {
            $table->unsignedBigInteger('tmdb_id')->primary();
            $table->string('name');
            $table->boolean('adult');
            $table->integer('runtime')->nullable();
            $table->string('backdrop')->nullable();
            $table->string('poster')->nullable();
            $table->date('first_air_date')->nullable()->index();
            $table->integer('release_year')->nullable()->index();
            $table->text('summary')->nullable();
            $table->text('overview')->nullable();
            $table->string('homepage')->nullable();
            $table->string('production_status')->nullable();
            $table->string('type')->nullable();
            $table->string('primary_language')->nullable();
            $table->string('content_rating')->nullable();
            $table->decimal('imdb_score', 9, 2)->default(0);
            $table->bigInteger('imdb_votes')->unsigned()->default(0);
            $table->string('imdb_id', 9)->nullable()->index();
            $table->bigInteger('tvdb_id')->unsigned()->nullable()->index();
            $table->json('locked_fields')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_shows');
    }
};
