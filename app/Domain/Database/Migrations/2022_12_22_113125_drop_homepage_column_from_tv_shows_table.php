<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('tv_shows', function (Blueprint $table): void {
            $table->dropColumn('homepage');
        });
    }

    public function down(): void
    {
        Schema::table('tv_shows', function (Blueprint $table): void {
            $table->string('homepage')->nullable()->after('overview');
        });
    }
};
