<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\{
    User,
    School,
    Division,
    SchoolYear,
    Classroom,
    Student,
    StudentClassroom,
    TheoryExam,
    TheoryExamQuestion,
    UserTheoryExam,
    UserTheoryExamAnswer
};
use App\Enums\{ClassEnum, SemesterEnum, AnswerEnum};

class TheoryExamTestSeeder extends Seeder
{
    public function run(): void
    {
        // === 1️⃣ Sekolah ===
        $school = School::create([
            'id' => Str::uuid(),
            'npsn' => 'r43534532454352345',
            'slug' => '325',
            'name' => 'SMK 1 Ban',
            'head_master' => 'Jokowi',
            'description' => 'Sekolah kejuruan unggulan di Bandung',
            'email' => 'smkBan@smkn1bdg.sch.id',
            'address' => 'Jl. Merdeka No. 45, Bandung',
            'phone_number' => '3453424324',
        ]);

        // === 2️⃣ Divisi ===
        $division = Division::create([
            'id' => Str::uuid(),
            'name' => 'Rekayasa Perangkat Lunak',
        ]);

        // === 3️⃣ Tahun Ajaran ===
        $schoolYear = SchoolYear::create([
            'id' => rand(1, 1000),
            'school_year' => '2038/2083',
            'active' => true,
        ]);

        // === 4️⃣ Kelas (3 kelas level 10) ===
        $classrooms = collect([
            Classroom::create([
                'id' => Str::uuid(),
                'school_id' => $school->id,
                'division_id' => $division->id,
                'school_year_id' => $schoolYear->id,
                'name' => 'X RPL 1',
                'class_level' => '10',
                'price' => 10000,
            ]),
            Classroom::create([
                'id' => Str::uuid(),
                'school_id' => $school->id,
                'division_id' => $division->id,
                'school_year_id' => $schoolYear->id,
                'name' => 'X RPL 2',
                'class_level' => '10',
                'price' => 10000,
            ]),
            Classroom::create([
                'id' => Str::uuid(),
                'school_id' => $school->id,
                'division_id' => $division->id,
                'school_year_id' => $schoolYear->id,
                'name' => 'X RPL 3',
                'class_level' => '10',
                'price' => 10000,
            ]),
        ]);

        // === 5️⃣ Buat siswa untuk setiap kelas ===
        $students = collect();
        $counter = 1;

        foreach ($classrooms as $classroom) {
            for ($i = 1; $i <= 10; $i++, $counter++) {

                $user = User::create([
                    'id' => Str::uuid(),
                    'name' => "Siswa {$counter}",
                    'email' => "siswa{$counter}@example.com",
                    'password' => Hash::make('password'),
                    'gender' => $i % 2 === 0 ? 'laki-laki' : 'perempuan',
                    'phone_number' => '081234567' . $counter,
                    'address' => 'Jl. Pelajar Pejuang No. ' . $counter,
                    'point' => 0,
                ]);

                $student = Student::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'school_id' => $school->id,
                    'nisn' => '9900' . $counter,
                    'date_birth' => '2008-01-01',
                    'has_clicked' => false,
                ]);

                StudentClassroom::create([
                    'student_id' => $student->id,
                    'classroom_id' => $classroom->id,
                ]);

                $students->push($user);
            }
        }

        // === 6️⃣ Buat 2 ujian teori (duplikat: semester, class_level, school sama) ===
        $exam1 = TheoryExam::create([
            'id' => Str::uuid(),
            'title' => 'Ujian Akhir Semester Matematika',
            'class_level' => '10',
            'semester' => SemesterEnum::GANJIL->value ?? 'ganjil',
            'exam_date' => now()->addDays(3),
            'duration' => 90,
            'total_questions' => 5,
            'minimum_score' => 70,
            'rule' => 'Peserta wajib hadir tepat waktu dan tidak membawa alat komunikasi.',
            'school_id' => $school->id,
            'division_id' => $division->id,
            'school_year_id' => $schoolYear->id,
        ]);

        $exam2 = TheoryExam::create([
            'id' => Str::uuid(),
            'title' => 'Ujian Akhir Semester Matematika Gelombang 2',
            'class_level' => '10',
            'semester' => SemesterEnum::GANJIL->value ?? 'ganjil',
            'exam_date' => now()->addDays(4),
            'duration' => 90,
            'total_questions' => 5,
            'minimum_score' => 70,
            'rule' => 'Peserta wajib hadir tepat waktu dan tidak membawa alat komunikasi.',
            'school_id' => $school->id,
            'division_id' => $division->id,
            'school_year_id' => $schoolYear->id,
        ]);

        // === 7️⃣ Buat soal ujian ===
        $questions = collect();
        for ($i = 1; $i <= 5; $i++) {
            $questions->push(
                TheoryExamQuestion::create([
                    'id' => Str::uuid(),
                    'division_id' => $division->id,
                    'class_level' => '10',
                    'question' => "Berapa hasil dari {$i} + {$i}?",
                    'option_a' => $i + 1,
                    'option_b' => $i + 2,
                    'option_c' => $i + $i,
                    'option_d' => $i * $i,
                    'option_e' => $i,
                    'answer' => AnswerEnum::OPTION_C->value,
                ])
            );
        }

        // === 8️⃣ Buat hasil ujian (untuk 2 ujian) ===
        foreach ([$exam1, $exam2] as $exam) {
            foreach ($students as $user) {
                $userExam = UserTheoryExam::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'theory_exam_id' => $exam->id,
                    'score' => rand(50, 100),
                    'started_at' => now()->subDays(rand(1, 3)),
                ]);

                foreach ($questions as $question) {
                    UserTheoryExamAnswer::create([
                        'id' => Str::uuid(),
                        'user_theory_exam_id' => $userExam->id,
                        'theory_exam_question_id' => $question->id,
                        'choosen_answer' => collect([
                            AnswerEnum::OPTION_A->value,
                            AnswerEnum::OPTION_B->value,
                            AnswerEnum::OPTION_C->value,
                            AnswerEnum::OPTION_D->value,
                            AnswerEnum::OPTION_E->value,
                        ])->random(),
                        'is_correct' => (bool) rand(0, 1),
                    ]);
                }
            }
        }

        $this->command->info('✅ Seeder TheoryExamTestSeeder berhasil dijalankan!');
    }
}
