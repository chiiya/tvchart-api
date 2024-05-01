<?php declare(strict_types=1);

use App\Domain\Models\TvShow;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('tv_shows', function (Blueprint $table): void {
            $table->renameColumn('whitelist', 'status');
        });
        TvShow::query()->where('status', '=', 0)->update(['status' => 3]);
        TvShow::query()->whereNull('status')->update(['status' => 0]);
        Schema::table('tv_shows', function (Blueprint $table): void {
            $table->integer('status')->nullable(false)->default(0)->change();
        });
        Schema::table('tv_shows', function (Blueprint $table): void {
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::table('tv_shows', function (Blueprint $table): void {
            $table->renameColumn('status', 'whitelist');
        });
        Schema::table('tv_shows', function (Blueprint $table): void {
            $table->tinyInteger('status')->nullable()->default(null)->change();
        });
    }
};
