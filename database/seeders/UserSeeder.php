<?php

namespace Database\Seeders;

use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use App\Models\Outlet;
use App\Models\Store;
use App\Models\User;
use Faker\Provider\Uuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::query()
        ->where('name', RoleEnum::ADMIN->value)->orWhere('name', RoleEnum::GUEST->value)
        ->get();

        foreach ($roles as $role) {
            $user = User::updateOrCreate([
                'id' => Uuid::uuid(),
                'name' => $role->name,
            ], [
                'id' => Uuid::uuid(),
                'name' => $role->name,
                'phone_number' => "08912345678",
                'photo' => 'profile/user-1.jpg',
                'gender' => GenderEnum::FEMALE->value,
                'address' => "lorem ipsum dolor sit amet is simply dummy text for industrial",
                'email' => str_replace(' ', '', $role->name) . "@gmail.com",
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            DB::table('model_has_roles')->insert([
                'model_uuid' => $user->id,
                'model_type' => User::class,
                'role_uuid' => $role->uuid,
            ]);
        }
    }
}
