<?php declare(strict_types=1);

use App\Domain\Models\TvShow;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('tv_shows', function (Blueprint $table): void {
            $table->tinyInteger('whitelist')->nullable()->after('tvdb_id');
        });
        TvShow::query()
            ->whereNotNull('is_whitelisted')
            ->get()
            ->each(fn (TvShow $show) => $show->update(['whitelist' => $show->is_whitelisted ? 1 : 0]));
        Schema::table('tv_shows', function (Blueprint $table): void {
            $table->dropColumn('is_whitelisted');
        });
    }

    public function down(): void
    {
        Schema::table('tv_shows', function (Blueprint $table): void {
            $table->dropColumn('is_whitelisted');
            $table->boolean('is_whitelisted');
        });
    }
};
