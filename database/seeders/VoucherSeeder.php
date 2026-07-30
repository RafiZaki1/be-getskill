<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseVoucher;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $course = Course::first();
        CourseVoucher::create([
            'id' => '1',
            "course_id" => $course->id,
            "code" => "qwerty123",
            "discount" => 25,
            "usage_limit" => 1
        ]);
    }
}
