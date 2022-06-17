<?php declare(strict_types=1);

namespace App\Domain\Database\Seeders;

use App\Domain\Models\Genre;
use Illuminate\Database\Seeder;
use Symfony\Component\Yaml\Yaml;

class DomainDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = Yaml::parseFile(storage_path('fixtures/genres.yaml'));

        foreach ($items as $item) {
            Genre::query()->create(['name' => $item]);
        }
    }
}
