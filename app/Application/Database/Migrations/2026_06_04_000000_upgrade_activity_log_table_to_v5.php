<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table): void {
            $table->json('attribute_changes')->nullable()->after('causer_id');
            $table->dropColumn('batch_uuid');
        });

        // Move tracked model changes from `properties` to the new `attribute_changes`
        // column, as required by the activitylog v4 -> v5 upgrade guide.
        DB::table('activity_log')
            ->where(function ($query): void {
                $query->whereNotNull('properties->attributes')
                    ->orWhereNotNull('properties->old');
            })
            ->eachById(function ($row): void {
                $properties = json_decode((string) $row->properties, true);
                $changes = array_intersect_key($properties, array_flip(['attributes', 'old']));
                $remaining = array_diff_key($properties, array_flip(['attributes', 'old']));

                DB::table('activity_log')->where('id', $row->id)->update([
                    'attribute_changes' => $changes === [] ? null : json_encode($changes),
                    'properties' => $remaining === [] ? null : json_encode($remaining),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table): void {
            $table->dropColumn('attribute_changes');
            $table->uuid('batch_uuid')->nullable()->after('properties');
        });
    }
};
