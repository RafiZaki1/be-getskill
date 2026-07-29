<?php

namespace App\Console\Commands;

use App\Contracts\Repositories\IndustryClass\PaymentRepository;
use App\Contracts\Repositories\IndustryClass\SchoolPaymentRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Helpers\PaymentHelper;
use Carbon\Carbon;

class GenerateReceiptNumbers extends Command
{
    protected $signature = 'generate:receipts';
    protected $description = 'Mengisi kolom receipt_number untuk payments & school_payments berdasarkan urutan created_at';

    protected PaymentRepository $paymentRepo;
    protected SchoolPaymentRepository $schoolPaymentRepo;

    public function __construct(PaymentRepository $paymentRepo, SchoolPaymentRepository $schoolPaymentRepo)
    {
        parent::__construct();
        $this->paymentRepo = $paymentRepo;
        $this->schoolPaymentRepo = $schoolPaymentRepo;
    }

    public function handle(): int
    {
        DB::beginTransaction();

        try {
            $this->info('Mulai generate nomor kwitansi...');

            $payments = $this->paymentRepo->getWithoutReceipt();
            $schoolPayments = $this->schoolPaymentRepo->getWithoutReceipt();

            $lastPaymentNumber = $this->paymentRepo->lastReceiptNumber();
            $lastSchoolPaymentNumber = $this->schoolPaymentRepo->lastReceiptNumber();

            // gabungkan semua record dan urutkan
            $merged = collect()
                ->merge($payments->map(fn($payment) => [
                    'model' => $payment,
                    'table' => 'payments',
                    'created_at' => $payment->created_at,
                ]))
                ->merge($schoolPayments->map(fn($schoolPayment) => [
                    'model' => $schoolPayment,
                    'table' => 'school_payments',
                    'created_at' => $schoolPayment->created_at,
                ]))
                ->sortBy('created_at')
                ->values();

            $count = 0;

            foreach ($merged as $entry) {
                $model = $entry['model'];
                $table = $entry['table'];

                $newReceiptNumber = PaymentHelper::nextReceiptNumber(
                    $lastPaymentNumber,
                    $lastSchoolPaymentNumber
                );

                if ($table === 'payments') {
                    $this->paymentRepo->update($model->id, ['receipt_number' => $newReceiptNumber]);
                    $lastPaymentNumber = $newReceiptNumber;
                } else {
                    $this->schoolPaymentRepo->update($model->id, ['receipt_number' => $newReceiptNumber]);
                    $lastSchoolPaymentNumber = $newReceiptNumber;
                }

                $count++;
                $this->line("✅ {$table} → {$model->id} → {$newReceiptNumber}");
            }

            DB::commit();
            $this->info("Berhasil! {$count} nomor kwitansi berhasil digenerate.");

            return 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
