<?php

namespace App\Console\Commands;

use App\Contracts\Repositories\IndustryClass\SchoolPaymentDetailRepository;
use App\Contracts\Repositories\IndustryClass\SchoolYearRepository;
use Illuminate\Console\Command;

class SchoolPaymentAddSchoolYearIdCommand extends Command
{
    private SchoolYearRepository $schoolYear;
    private SchoolPaymentDetailRepository $schoolPaymentDetail;

    public function __construct(SchoolYearRepository $schoolYear, SchoolPaymentDetailRepository $schoolPaymentDetail)
    {
        parent::__construct();
        $this->schoolYear = $schoolYear;
        $this->schoolPaymentDetail = $schoolPaymentDetail;
    }
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:school-payment-add-school-year-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $updated = $this->schoolPaymentDetail->updateWhereColumnSchoolYearIdNull($activeSchoolYearId);

        $this->info("Success update {$updated} school_payment_details with school_year_id: {$activeSchoolYearId}");
    }
}
