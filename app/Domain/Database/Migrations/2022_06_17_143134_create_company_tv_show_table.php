<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('company_tv_show', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('tv_show_id');

            $table->foreign('company_id')->references('tmdb_id')->on('companies')->cascadeOnDelete();
            $table->foreign('tv_show_id')->references('tmdb_id')->on('tv_shows')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_tv_show');
    }
};
