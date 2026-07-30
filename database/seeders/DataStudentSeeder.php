<?php

namespace Database\Seeders;

use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Faker\Provider\Uuid;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DataStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::query()->where('name', RoleEnum::STUDENT->value)->first();

        for ($i = 1; $i <= 20; $i++) {
            $user = User::create([
                'id' => Uuid::uuid(),
                'name' => 'Student ' . $i,
                'email' => 'student' . $i . '@example.com',
                'password' => Hash::make('password'),
                'gender' => GenderEnum::MALE->value,
                'phone_number' => "08912345678",
                'photo' => 'profile/user-1.jpg',
                'address' => "lorem ipsum dolor sit amet is simply dummy text for industrial",
                'email_verified_at' => now(),
                'point' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('model_has_roles')->insert([
                'model_uuid' => $user->id,
                'model_type' => User::class,
                'role_uuid' => $role->uuid,
            ]);
            Student::create([
                'id' => Uuid::uuid(),
                'user_id' => $user->id,
                'school_id' => '1',
                'nisn' => $i,
                'date_birth' => now(),
            ]);
        }
    }
}
