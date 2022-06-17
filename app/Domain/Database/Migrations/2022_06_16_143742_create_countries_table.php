<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table): void {
            $table->string('country_code')->primary();
            $table->string('name');
            $table->string('native_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
