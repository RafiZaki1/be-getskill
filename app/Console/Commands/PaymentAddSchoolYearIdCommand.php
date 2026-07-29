<?php

namespace App\Console\Commands;

use App\Contracts\Repositories\IndustryClass\PaymentRepository;
use App\Contracts\Repositories\IndustryClass\SchoolYearRepository;
use Illuminate\Console\Command;

class PaymentAddSchoolYearIdCommand extends Command
{
    private SchoolYearRepository $schoolYear;
    private PaymentRepository $payment;

    public function __construct(SchoolYearRepository $schoolYear, PaymentRepository $payment)
    {
        parent::__construct();
        $this->schoolYear = $schoolYear;
        $this->payment = $payment;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:add-school-year-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to add value in column school_year_id with school_year_id active';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $activeSchoolYearId = $this->schoolYear->active()?->id;

        if(!$activeSchoolYearId) {
            $this->error('No active school year.');
            return;
        }

        $updated = $this->payment->updateWhereColumnSchoolYearIdNull($activeSchoolYearId);

        $this->info("Success update {$updated} payments with school_year_id: {$activeSchoolYearId}");
    }
}
