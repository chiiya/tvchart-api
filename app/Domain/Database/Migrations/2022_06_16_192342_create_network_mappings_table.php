<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('network_mappings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('source');
            $table->unsignedBigInteger('target');
            $table->timestamps();

            $table->foreign('target')->references('tmdb_id')->on('networks')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('network_mappings');
    }
};
