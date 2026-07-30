<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Faker\Provider\Uuid;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $reflection = new \ReflectionClass(RoleEnum::class);

        foreach ($reflection->getConstants() as $case) {
            Role::firstOrCreate(
                ['name' => $case->value],
                ['uuid' => Uuid::uuid()],
            );
        }
    }
}
