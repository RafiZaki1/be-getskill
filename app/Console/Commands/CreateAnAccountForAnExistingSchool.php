<?php

namespace App\Console\Commands;

use App\Contracts\Repositories\Auth\UserRepository;
use App\Contracts\Repositories\IndustryClass\SchoolRepository;
use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateAnAccountForAnExistingSchool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:create-account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command awal untuk membuat akun sekolah untuk login';

    protected SchoolRepository $school;
    protected UserRepository $user;
    public function __construct(SchoolRepository $school, UserRepository $user)
    {
        parent::__construct();
        $this->school = $school;
        $this->user = $user;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $this->info('Mulai membuat akun untuk semua sekolah...');

            $schools = $this->school->getDontHaveAnAccount();

            foreach ($schools as $school) {
                $this->info("ℹ️ Membuat akun untuk sekolah {$school->name}");

                $email = $school->email;
                $emailExists = $this->user->checkEmail($email);

                if ($emailExists) {
                    [$name, $domain] = explode('@', $email);
                    $randomNumber = rand(100, 999);
                    $newEmail = "{$name}{$randomNumber}@{$domain}";

                    while ($this->user->checkEmail($newEmail)) {
                        $randomNumber = rand(100, 999);
                        $newEmail = "{$name}{$randomNumber}@{$domain}";
                    }

                    $email = $newEmail;

                    $this->info("Email sudah digunakan, email sekolah diubah menjadi {$email}");
                }

                $data = [
                    'name' => $school->name,
                    'email' => $email,
                    'phone_number' => $school->phone_number,
                    'gender' => GenderEnum::MALE->value,
                    'password' => bcrypt('KI-getskill2025'),
                ];

                $user = $this->user->store($data);
                $user->assignRole(RoleEnum::SCHOOL->value);
                $this->school->update($school->id, ['user_id' => $user->id]);

                $this->info("✅ Akun sekolah {$school->name} berhasil dibuat.");
            }

            DB::commit();
            $this->info("Berhasil!, Semua sekolah berhasil dibuatkan akun untuk login.");
            return 0;
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->error("Error: " . $th->getMessage());
            return 1;
        }
    }
}
