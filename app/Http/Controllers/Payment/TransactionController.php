<?php

namespace App\Http\Controllers\Payment;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Services\TripayService;
use App\Traits\PaginationTrait;
use App\Enums\InvoiceStatusEnum;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Http;
use App\Services\DashboardIncomeService;
use App\Http\Resources\TransactionResource;
use App\Contracts\Interfaces\EventInterface;
use App\Http\Resources\DashboardIncomeResource;
use App\Contracts\Interfaces\TransactionInterface;
use Illuminate\Auth\Access\AuthorizationException;
use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\UserEventInterface;
use App\Contracts\Interfaces\Course\UserCourseInterface;
use App\Contracts\Interfaces\Course\CourseVoucherInterface;
use App\Contracts\Interfaces\IndustryClass\PaymentInterface;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    use PaginationTrait;
    private TransactionInterface $transaction;
    private UserCourseInterface $userCourse;
    private UserEventInterface $userEvent;
    private CourseVoucherInterface $courseVoucher;
    private TripayService $service;
    private TransactionService $transactionService;
    private EventInterface $event;
    private CourseInterface $course;
    private PaymentInterface $payment;
    public function __construct(TransactionInterface $transaction, CourseVoucherInterface $courseVoucher, EventInterface $event, UserEventInterface $userEvent, CourseInterface $course, UserCourseInterface $userCourse, TransactionService $transactionService, TripayService $service, PaymentInterface $payment)
    {
        $this->transaction = $transaction;
        $this->courseVoucher = $courseVoucher;
        $this->userEvent = $userEvent;
        $this->userCourse = $userCourse;
        $this->event = $event;
        $this->course = $course;
        $this->transactionService = $transactionService;
        $this->service = $service;
        $this->payment = $payment;
    }

    /**
     * get payment channels from tripay
     *
     * @return void
     */
    public function getPaymentChannels(): JsonResponse
    {
        try {
            $paymentChannels = $this->service->handlePaymentChannels();
            return ResponseHelper::success($paymentChannels, trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            Log::error('payment channel error: ' . $th->getMessage());
            return ResponseHelper::error(null, $th->getMessage());
        }
    }

    /**
     * get payment instructions by payment method from tripay
     *
     * @return void
     */
    public function getPaymentInstructions(Request $request): JsonResponse
    {
        try {
            return response()->json($paymentInstructions = $this->service->handlePaymentInstructions($request->code));
            // return ResponseHelper::success($paymentInstructions, trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, $th->getMessage());
        }
    }

    /**
     * index
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            if ($request->has('page')) {
                $transactions = $this->transaction->customPaginate($request);
                $data['paginate'] = $this->customPaginate($transactions->currentPage(), $transactions->lastPage());
                $data['data'] = TransactionResource::collection($transactions);
            } else {
                $transactions = $this->transaction->search($request);
                $data['data'] = TransactionResource::collection($transactions);
            }
            return ResponseHelper::success($data, trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, $th->getMessage());
        }
    }

    /**
     * get latest transactions
     *
     * @return void
     */
    public function getLatest(): JsonResponse
    {
        try {
            $transactions = $this->transaction->getLatest();
            return ResponseHelper::success(TransactionResource::collection($transactions), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, $th->getMessage());
        }
    }


    /**
     * get transactions by user
     *
     * @return void
     */ 
    public function getByUser(Request $request): JsonResponse
    {
        try {
            $userId  = $request->user()->id;
            $perPage = (int) $request->get('per_page', 4);
            $status  = $request->get('invoice_status');

            $transactions = $this->transaction->getByUser($userId, $status, $perPage);

            return ResponseHelper::success([
                'data'     => TransactionResource::collection($transactions),
                'paginate' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page'    => $transactions->lastPage(),
                ],
            ], trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * get transaction detail by reference
     *
     * @return void
     */
    public function show(mixed $id): mixed
    {
        try {
            $transaction = $this->transaction->show($id);
            $this->authorize('view', $transaction);
            $response = Http::withToken(config('tripay.api_key'))->get(config('tripay.api_url') . 'transaction/detail?reference=' . $transaction->id);
            $transaction->setAttribute('paid_at', $response->json()['success'] ? Carbon::createFromTimestamp($response->json()['data']['paid_at'])->format('Y-m-d') : null);
            return ResponseHelper::success(TransactionResource::make($transaction), trans('alert.fetch_success'));
        } catch (AuthorizationException $e) {
            return ResponseHelper::error(null, 'Anda tidak memiliki akses!', 403);
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * store
     *
     * @param  mixed $request
     * @param  mixed $course
     * @return mixed
     */
    public function store(Request $request, $productType, string $id): mixed
    {
        try {
            $voucher = $this->courseVoucher->getByCode($request->voucher_code);

            if ($productType == 'course') {
                $course = $this->course->show($id);
                if (!$course) {
                    return ResponseHelper::error(null, "Course tidak ditemukan");
                }

                $currentUserCourse = $course->currentUserCourse;
                if ($currentUserCourse && $currentUserCourse->user->id == auth()->user()->id) {
                    return ResponseHelper::error(null, "anda sudah membeli kursus ini");
                }

                // Jika course GRATIS - langsung proses tanpa cek transaksi pending
                if (!$course->is_premium) {
                    try {
                        $userCourse = $this->transactionService->handleCreateUserCourseOrUserEvent($course, (object) ['user_id' => auth()->user()->id, 'course_id' => $course->id]);
                        return ResponseHelper::success($userCourse, 'Berhasil');
                    } catch (\Throwable $th) {
                        return ResponseHelper::error(null, $th->getMessage());
                    }
                }

                // Jika course PREMIUM - cek transaksi pending dulu
                $paymentUser = $this->transaction->getUnfinishUser(auth()->user()->id);
                if ($paymentUser != null) {
                    $paymentUser['message'] = "Selesaikan Transaksi Sebelumnya";
                    return ResponseHelper::error($paymentUser, $paymentUser['message']);
                }

                $transaction = json_decode($this->service->handelCreateTransaction($request, $course, $voucher), 1);
            } else if ($productType == 'event') {
                $event = $this->event->show($id);
                if (!$event) {
                    return ResponseHelper::error(null, "Event tidak ditemukan");
                }

                $autoApprove = $event->is_auto_approve;
                $currentUserEvent = $event->currentUserEvent;

                if ($currentUserEvent && $currentUserEvent->user->id == auth()->user()->id) {
                    return ResponseHelper::error(null, "anda sudah bergabung event ini");
                }

                // Jika event GRATIS - langsung proses tanpa cek transaksi pending
                if ($event->price == 0) {
                    $userEvent = $this->transactionService->handleCreateUserCourseOrUserEvent($event, (object) ['user_id' => auth()->user()->id, 'event_id' => $event->id, 'auto_approve' => $autoApprove]);
                    return ResponseHelper::success($userEvent, 'Berhasil');
                }

                // Jika event PREMIUM - cek transaksi pending dulu
                $paymentUser = $this->transaction->getUnfinishUser(auth()->user()->id);
                if ($paymentUser != null) {
                    $paymentUser['message'] = "Selesaikan Transaksi Sebelumnya";
                    return ResponseHelper::error($paymentUser, $paymentUser['message']);
                }

                $transaction = json_decode($this->service->handelCreateTransaction($request, $event, $voucher), 1);
            } else {
                return ResponseHelper::error(null, 'Produk Tidak Ditemukan');
            }

            // Proses hasil transaksi untuk premium course/event
            if (isset($transaction) && $transaction['success']) {
                $data = [
                    'id' => $transaction['data']['reference'],
                    'user_id' => auth()->user()->id,
                    'course_id' => $course->id ?? null,
                    'event_id' => $event->id ?? null,
                    'invoice_id' => $transaction['data']['merchant_ref'],
                    'fee_amount' => $transaction['data']['fee_merchant'],
                    'amount' => $course->price ?? $event->price,
                    'invoice_url' => $transaction['data']['checkout_url'],
                    'expiry_date' => Carbon::createFromTimestamp($transaction['data']['expired_time'])->toDateTimeString(),
                    'paid_amount' => 0,
                    'payment_channel' => $transaction['data']['payment_name'],
                    'payment_method' => $transaction['data']['payment_method'],
                    'course_voucher_id' => $voucher->id ?? null,
                ];
                $transactionResult = $this->transaction->store($data);

                $product = $productType == 'course' ? $course : $event;
                $typeName = $productType == 'course' ? 'Kursus' : 'Event';

                $dataEmail = $this->transactionService->mappingSendEmailData($productType, $product, $transactionResult);

                $to = $transactionResult->user->email;
                $subject = 'Silahkan Lanjutkan ' . $typeName == 'Kursus' ? 'Pembelian Kursus ' : 'Pendaftaran Event ' . $product->title;
                $this->transactionService->sendEmailTransaction($to, $subject, $dataEmail);

                $transactionResult->reference = $transaction['data']['reference'];
                return ResponseHelper::success(['transaction' => $transactionResult, 'voucher' => $voucher], 'Transaksi berhasil');
            }

            return ResponseHelper::error(null, "Gagal membuat transaksi");
        } catch (HttpResponseException $th) {
            return $th->getResponse();
        }
         catch (\Throwable $th) {
            Log::error('create transaction error: ' . $transaction['message']);
            return ResponseHelper::error(null, $transaction['message']);
        }
    }

    /**
     * callback
     *
     * @param  mixed $request
     * @return void
     */
    public function callback(Request $request)
    {
        try {
            return $this->transactionService->handlePaymentCallback($request);
        } catch (\Throwable $th) {
            Log::error('payment callback error: ' . $th->getMessage());
            return ResponseHelper::error(null, $th->getMessage());
        }
    }

    public function returnCallback(Request $request)
    {
        return 'return callback';
    }

    /**
     * checkStatus
     *
     * @param  mixed $request
     * @param  mixed $reference
     * @return void
     */
    public function checkStatus(mixed $type, mixed $reference)
    {
        try {
            $transaction = $this->transaction->show($reference);
            $this->authorize('view', $transaction);
            $response = Http::withToken(config('tripay.api_key'))
                ->get(config('tripay.api_url') . 'transaction/detail?reference=' . $reference);

            if ($transaction->invoice_status != InvoiceStatusEnum::CANCELED->value && $response->successful() && isset($response['data'])) {
                $status = strtolower($response['data']['status']);
                $now = Carbon::now();

                if ($status != InvoiceStatusEnum::PAID->value && $now->timestamp > $response['data']['expired_time']) {
                    $status = InvoiceStatusEnum::EXPIRED->value;
                }

                $this->transaction->update($reference, [
                    'paid_amount' => $response['data']['amount'],
                    'fee_amount' => $response['data']['fee_customer'],
                    'invoice_status' => $status,
                ]);

                $product_id = $transaction->course_id ?? $transaction->event_id;

                $this->transactionService->checkStatus($type, $product_id, $status);

                return $response;
            } else {
                return ResponseHelper::success(['reference' => $reference], 'Tripay tidak merespon');
            }
        } catch (AuthorizationException $e) {
            return ResponseHelper::error(null, 'Anda tidak memiliki akses!', 403);
        } catch (\Throwable $th) {
            Log::error('check status error: ' . $th->getMessage());
            return ResponseHelper::error(
                null,
                trans('alert.fetch_failed') . '. ' . $th->getMessage()
            );
        }
    }


    /**
     * delete transaction 
     *
     * @return void
     */
    public function delete(mixed $id): mixed
    {
        try {
            return $this->delete($id);
        } catch (\Throwable $th) {
            Log::error('delete transaction error: ' . $th->getMessage());
            return ResponseHelper::error(null, $th->getMessage());
        }
    }

    /**
     * group by month
     *
     * @return void
     */
    public function groupByMonth(DashboardIncomeService $dashboardIncomeService): JsonResponse
    {
        try {
            $income = $dashboardIncomeService->getTotalIncomeByMonth();
            return ResponseHelper::success(new DashboardIncomeResource($income));
        } catch (\Throwable $th) {
            Log::error('group by month error: ' . $th->getMessage());
            return ResponseHelper::error(null, $th->getMessage());
        }
    }

    /**
     * cancel transaction user
     *
     * @return void
     */
    public function cancel(string $reference)
    {
        try {
            $data = $this->transaction->show($reference);
            $this->authorize('cancel', $data);
            $this->transaction->updateByReference($reference, [
                'invoice_status' => InvoiceStatusEnum::CANCELED->value,
            ]);

            return ResponseHelper::success($data, 'Transaksi berhasil dibatalkan');
        } catch (AuthorizationException $e) {
            return ResponseHelper::error(null, 'Anda tidak memiliki akses!', 403);
        } catch (\Throwable $th) {
            Log::error('cancel transaction error: ' . $th->getMessage());
            return ResponseHelper::error(null, 'Gagal membatalkan transaksi, ' . $th->getMessage());
        }
    }
}
