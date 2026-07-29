<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatusEnum;
use App\Models\Payment;
use App\Models\PaymentStudentAllocation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SynchronizationStudentPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:synchronization-student-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill and recalculate semester allocations for all paid payments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting payment semester fix...');

        $userCount = User::whereHas('payments', fn ($q) => $q->where('invoice_status', InvoiceStatusEnum::PAID->value))->count();

        if ($userCount === 0) {
            $this->warn('No users with paid payments found. Exiting.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($userCount);
        $bar->start();

        $errors = [];

        User::whereHas('payments', fn ($q) => $q->where('invoice_status', InvoiceStatusEnum::PAID->value))
            ->chunkById(100, function ($users) use ($bar, &$errors) {
                foreach ($users as $user) {
                    try {
                        $this->processUser($user);
                    } catch (Throwable $e) {
                        $errors[] = "User {$user->id}: {$e->getMessage()}";
                        $this->newLine();
                        $this->error("Failed for user {$user->id}: {$e->getMessage()}");
                    }

                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine(2);

        if (!empty($errors)) {
            $this->warn(count($errors) . ' user(s) failed. Review errors above.');
            return self::FAILURE;
        }

        $this->info('All payment semesters fixed successfully.');
        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Core Processing
    // -------------------------------------------------------------------------

    private function processUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            $semesterLimit = $this->getSemesterLimit($user);

            // Load paid payments ordered by date ASC, id ASC
            $payments = $user->payments()
                ->where('invoice_status', InvoiceStatusEnum::PAID->value)
                ->orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // Delete existing allocations for these payments in one query
            $paymentIds = $payments->pluck('id');
            PaymentStudentAllocation::whereIn('payment_id', $paymentIds)->delete();

            $cumulative = 0; // Running total of allocated amount across all payments

            foreach ($payments as $payment) {
                $allocations = $this->allocatePayment(
                    payment:       $payment,
                    cumulative:    $cumulative,
                    semesterLimit: $semesterLimit,
                );

                // Determine and update the payment's semester label
                $semesterLabel = $this->resolveSemesterLabel($allocations);
                $payment->semester = $semesterLabel;
                $payment->saveQuietly(); // Avoid triggering observers/events during backfill

                if ($semesterLabel == 'mixed') {
                    PaymentStudentAllocation::insert($allocations);
                }

                // Advance cumulative pointer
                $cumulative += $payment->amount;
            }
        });
    }

    // -------------------------------------------------------------------------
    // Allocation Logic
    // -------------------------------------------------------------------------

    /**
     * Splits a single payment into one or more allocation rows.
     *
     * Returns an array of raw rows ready for PaymentAllocation::insert().
     */
    private function allocatePayment(Payment $payment, int &$cumulative, int $semesterLimit): array
    {
        $allocations   = [];
        $remaining     = $payment->amount;
        $localCumul    = $cumulative; // local pointer — $cumulative is passed by reference and updated by caller

        while ($remaining > 0) {
            $semesterIndex = (int) floor($localCumul / $semesterLimit);
            $semesterName  = $semesterIndex % 2 === 0 ? 'ganjil' : 'genap';

            $usedInBucket  = $localCumul % $semesterLimit;
            $quotaLeft     = $semesterLimit - $usedInBucket;

            $allocate = min($remaining, $quotaLeft);

            $allocations[] = [
                'id'        => Str::uuid(),
                'payment_id' => $payment->id,
                'semester'   => $semesterName,
                'amount'     => $allocate,
            ];

            $localCumul += $allocate;
            $remaining  -= $allocate;
        }

        return $allocations;
    }

    // -------------------------------------------------------------------------
    // Semester Label Resolver
    // -------------------------------------------------------------------------

    /**
     * Derives the payment-level semester label from its allocation rows.
     *
     * - All ganjil          → 'ganjil'
     * - All genap           → 'genap'
     * - Mix of both         → 'mixed'
     */
    private function resolveSemesterLabel(array $allocations): string
    {
        $semesters = array_unique(array_column($allocations, 'semester'));

        if (count($semesters) === 1) {
            return $semesters[0]; // 'ganjil' or 'genap'
        }

        return 'mixed';
    }

    // -------------------------------------------------------------------------
    // Semester Limit Helper
    // -------------------------------------------------------------------------

    /**
     * Returns the semester billing cap for a given user.
     *
     */
    private function getSemesterLimit(User $user): int
    {
        $classPrice = $user->student?->currentStudentClassroom?->classroom?->price ?? 0;

        Log::info("Calculated class price for user {$user->id}: {$user->student?->currentStudentClassroom?->classroom}: {$classPrice} ");

        if ($classPrice <= 0) {
            throw new \RuntimeException(
                "Cannot determine class price for user {$user->id}. Check studentDetail/classroom relationship."
            );
        }

        return $classPrice * 5;
    }
}
