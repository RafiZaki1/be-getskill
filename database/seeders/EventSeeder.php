<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventAttendance;
use Faker\Provider\Uuid;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::create([
            'id' => Uuid::uuid(),
            'image' => '',
            'title' => 'lorem ipsum',
            'slug' => Str::slug('lorem ipsum'),
            'description' => 'lorem ipsum dolor sit amet',
            'location' => 'malangraya',
            'capacity' => 100,
            'price' => 100000,
            'start_date' => now(),
            'end_date' => now()->addDays(3),
            'has_certificate' => 1,
            'is_online' => 1,
        ]);
        // for ($i = 1; $i <= 3; $i++) {
        //     EventAttendance::create([

        //         'event_id'=>
        //         'attendance_date'=>
        //         'attendance_link'=>
        //     ]);
        // }
    }
}
