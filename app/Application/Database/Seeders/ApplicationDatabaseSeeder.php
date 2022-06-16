<?php declare(strict_types=1);

namespace App\Application\Database\Seeders;

use Chiiya\FilamentAccessControl\Database\Seeders\FilamentAccessControlSeeder;
use Chiiya\FilamentAccessControl\Enumerators\RoleName;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ApplicationDatabaseSeeder extends Seeder
{
    public static array $users = [
        [
            'first_name' => 'Elisha',
            'last_name' => 'Witte',
            'email' => 'github@chiiya.moe',
        ],
    ];
    public static array $permissions = [
        'shows.view',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(FilamentAccessControlSeeder::class);

        foreach (self::$users as $user) {
            $password = config('aral.admin.password');
            $admin = FilamentUser::query()->create(array_merge($user, [
                'password' => Hash::make($password ?: Str::random(40)),
            ]));
            $admin->assignRole(RoleName::SUPER_ADMIN);
        }

        /** @var Role $role */
        $role = Role::findByName(RoleName::SUPER_ADMIN, 'filament');

        foreach (self::$permissions as $permission) {
            Permission::query()->create([
                'name' => $permission,
                'guard_name' => 'filament',
            ]);
            $role->givePermissionTo($permission);
        }
    }
}
