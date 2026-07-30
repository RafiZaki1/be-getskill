<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Division;
use App\Models\School;
use App\Models\SchoolYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();
        $division = Division::first();
        $year = SchoolYear::first();

        if ($school && $division && $year) {
            Classroom::create([
                'id' => (string) Str::uuid(),
                'school_id' => $school->id,
                'division_id' => $division->id,
                'school_year_id' => 1,
                'name' => 'Kelas X-A',
                'slug' => 'kelas-x-a',
                'class_level' => '10',
            ]);
        }
    }
}
