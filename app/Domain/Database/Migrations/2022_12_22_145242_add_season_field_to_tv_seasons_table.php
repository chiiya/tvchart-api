<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('tv_seasons', function (Blueprint $table): void {
            $table->string('season_year')->nullable()->after('tvdb_id');
            $table->string('season')->nullable()->after('season_year');
        });
        Schema::table('tv_seasons', function (Blueprint $table): void {
            $table->index(['season_year', 'season']);
        });
    }

    public function down(): void
    {
        Schema::table('tv_seasons', function (Blueprint $table): void {
            $table->dropIndex(['season_year', 'season']);
            $table->dropColumn('season');
            $table->dropColumn('season_year');
        });
    }
};
