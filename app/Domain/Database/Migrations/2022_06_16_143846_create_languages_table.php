<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table): void {
            $table->string('language_code')->primary();
            $table->string('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
