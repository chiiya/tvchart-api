<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('country_tv_show', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tv_show_id');
            $table->string('country_code');

            $table->foreign('tv_show_id')->references('tmdb_id')->on('tv_shows')->cascadeOnDelete();
            $table->foreign('country_code')->references('country_code')->on('countries')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_tv_show');
    }
};
