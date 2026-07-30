<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@sekolah.com'],
            [
                'name' => 'Admin Sekolah',
                'password' => bcrypt('password'),
            ]
        );

        $school = \App\Models\School::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'name' => 'Sekolah Percobaan',
            'slug' => 'sekolah-percobaan',
            'npsn' => '12345678',
            'email' => 'info@sekolahpercobaan.sch.id',
            'address' => 'Jl. Percobaan No. 1',
            'head_master' => 'Bapak Kepala Sekolah',
            'phone_number' => '081234567890',
            'description' => 'Ini adalah sekolah percobaan untuk TA.',
            'payment_method' => 'from_school',
        ]);
    }
}
