<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('watch_provider_regions', function (Blueprint $table): void {
            $table->string('country')->primary();
            $table->string('native_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_provider_regions');
    }
};
