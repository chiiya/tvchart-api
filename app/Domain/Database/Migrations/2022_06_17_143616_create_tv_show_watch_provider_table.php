<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('tv_show_watch_provider', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tv_show_id');
            $table->unsignedBigInteger('watch_provider_id');
            $table->string('region');

            $table->foreign('tv_show_id')->references('tmdb_id')->on('tv_shows')->cascadeOnDelete();
            $table->foreign('watch_provider_id')->references('tmdb_id')->on('watch_providers')->cascadeOnDelete();
            $table->foreign('region')->references('country_code')->on('countries')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_show_watch_provider');
    }
};
