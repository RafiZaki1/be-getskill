<?php

namespace Database\Seeders;

use App\Models\Reward;
use Faker\Provider\Uuid;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 16; $i++) {
            Reward::create([
                'id' => Uuid::uuid(),
                'image' => '',
                'stock' => 3,
                'name' => 'lorem ipsum ' . $i,
                'slug' => 'lorem-ipsum ' . $i,
                'description' => 'lorem ipsum dolor sit amet',
                'points_required' => 10
            ]);
        }
    }
}
