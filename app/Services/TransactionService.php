<?php

namespace App\Services;

use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\UserCourseInterface;
use App\Contracts\Interfaces\Course\UserEventInterface;
use App\Contracts\Interfaces\EventInterface;
use App\Contracts\Interfaces\IndustryClass\PaymentInterface;
use App\Contracts\Interfaces\TransactionInterface;
use App\Enums\InvoiceStatusEnum;
use App\Enums\UserEventStatusEnum;
use App\Helpers\ResponseHelper;
use App\Jobs\SendEmailPaymentJob;
use App\Jobs\SendEmailTransactionJob;
use App\Models\Course;
use App\Models\Module;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class TransactionService
{
    private TransactionInterface $transaction;
    private CourseInterface $course;
    private UserCourseInterface $userCourse;
    private PaymentInterface $payment;
    private UserEventInterface $userEvent;
    private EventInterface $event;

    public function __construct(TransactionInterface $transaction, UserCourseInterface $userCourse, UserEventInterface $userEvent, PaymentInterface $payment, CourseInterface $course, EventInterface $event)
    {
        $this->transaction = $transaction;
        $this->course = $course;
        $this->payment = $payment;
        $this->userCourse = $userCourse;
        $this->userEvent = $userEvent;
        $this->event = $event;
    }
    public function handlePaymentChannels()
    {
        $res = Http::withToken(config('tripay.api_key'))
            ->get(config('tripay.api_url') . "merchant/payment-channel")
            ->json();

        return collect($res['data'])->groupBy('group');
    }


    public function handleCreateUserCourseOrUserEvent($product, $transaction): mixed
    {
        if (is_object($product) && get_class($product) == Course::class) {
            $course = $this->course->show($transaction->course_id);

            $data = $this->userCourse->store([
                'user_id' => $transaction->user_id,
                'course_id' => $transaction->course_id,
                'sub_module_id' => Module::where('course_id', $product->id)->whereHas('subModules')->orderBy('step', 'asc')->first()->subModules->first()->id
            ]);
            $data->sub_module_slug = Module::where('course_id', $product->id)->whereHas('subModules')->orderBy('step', 'asc')->first()->subModules->first()->slug;
            $data->test_id = $product->courseTest->id;
            return $data;
        } else {
            if($transaction->auto_approve) {
                $status = UserEventStatusEnum::ACCEPTED->value;
            } else {
                $status = UserEventStatusEnum::PENDING->value;
            }
            return $this->userEvent->store([
                'user_id' => $transaction->user_id,
                'event_id' => $transaction->event_id,
                'status' => $status,
            ]);
        }
    }

    /**
     * handlePaymentCallback
     *
     * @param  mixed $request
     * @return mixed
     */
    public function handlePaymentCallback($request): mixed
    {
        $payment = $this->payment->show($request->reference);
        $data = [];
        if ($payment) {
            switch ($request->status) {
                case 'UNPAID':
                    $data = [
                        'invoice_status' => InvoiceStatusEnum::UNPAID->value
                    ];
                    break;
                case 'PAID':
                    $data = [
                        'invoice_status' => InvoiceStatusEnum::PAID->value
                    ];

                    $to = $payment->user->email;
                    $subject = 'Yeay, berhasil menyelesaikan pembayaran kelas industri';
                    $data = [
                        'user_name' => $payment->user->name,
                        'payment_nominal' => 'Rp. ' . number_format($payment->amount, 0, ',', '.'),
                        'payment_method' => $payment->payment_method,
                        'is_done' => true,
                    ];

                    $this->sendEmailPayment($to, $subject, $data);
                    break;
                case 'EXPIRED':
                    $data = [
                        'invoice_status' => InvoiceStatusEnum::EXPIRED->value
                    ];
                    break;
                case 'FAILED':
                    $data = [
                        'invoice_status' => InvoiceStatusEnum::FAILED->value
                    ];
                    break;
                default:
                    return $request;
                    break;
            }
            $payment->update($data);
            return ResponseHelper::success(null, "Callback success");
        } else {
            $transaction = $this->transaction->show($request->reference);
            $product = $transaction->course ?? $transaction->event;
            $product->type = $transaction->course ? 'course' : 'event';

            $to = $transaction->user->email;
            $typeName = $product->type === 'course' ? 'Kursus' : 'Event';
            $dataEmail = $this->mappingSendEmailData($product->type, $product, $transaction);
            
            switch ($request->status) {
                case 'UNPAID':
                    $data = [
                        'invoice_status' => 'unpaid'
                    ];
                    break;
                case 'PAID':
                    $data = [
                        'id' => $request->reference,
                        'invoice_id' => $request->merchant_ref,
                        'fee_amount' => $request->fee_merchant,
                        'paid_amount' => $request->total_amount,
                        'payment_channel' => $request->payment_method,
                        'payment_method' => $request->payment_method_code,
                        'invoice_status' => $request->status
                    ];

                    $dataEmail['is_done'] = true;
                    $this->checkStatus($product->type, $product->id, InvoiceStatusEnum::PAID->value, $transaction->user_id);

                    $subject = "Pembayaran $typeName {$product->title}";
                    $this->sendEmailTransaction($to, $subject, $dataEmail);
                    break;
                case 'EXPIRED':
                    $data = [
                        'invoice_status' => 'expired'
                    ];
                    break;
                case 'FAILED':
                    $data = [
                        'invoice_status' => 'failed'
                    ];
                    break;
                default:
                    return $request;
                    break;
            }

            $updated =  $this->transaction->update($request->reference, $data);
            return $updated
                ? ResponseHelper::success(null, "Callback success")
                : ResponseHelper::error(null, "Callback gagal");
        }
    }

    /**
     * Send email transaction course or event
     * 
     * @param string $to email recipient
     * @param string $subject email subject
     * @param array $data email data
     * @return void
     */
    public function sendEmailTransaction(string $to, string $subject, array $data): void
    {
        SendEmailTransactionJob::dispatch($to, $subject, $data);
    }

    /**
     * Send email payment for industry class
     * 
     * @param string $to email recipient
     * @param string $subject email subject
     * @param array $data email data
     * @return void
     */
    public function sendEmailPayment(string $to, string $subject, array $data): void
    {
        SendEmailPaymentJob::dispatch($to, $subject, $data);
    }

    /**
     * Update user access to course or event based on payment status.
     *
     * @param string $type 'course' or 'event'
     * @param mixed $product_id id from product
     * @param string $status transaction status
     * @param mixed $user_id user id 
     * @return void
     */
    public function checkStatus(string $type, mixed $product_id, string $status, mixed $user_id = null): void
    {
        if ($type === 'course') {
            $course = $this->course->show($product_id);
            $userCourse = $this->userCourse->checkByCourse($course->id, $user_id);
            $isPaid = $status === InvoiceStatusEnum::PAID->value;

            if ($isPaid && !$userCourse) {
                $this->handleCreateUserCourseOrUserEvent($course, (object) [
                    'user_id' => $user_id ?? auth()->id(),
                    'course_id' => $course->id
                ]);
            } elseif (!$isPaid && $userCourse) {
                $otherPaid = $this->transaction->checkPaidByCourseAndUser($course->id, auth()->id());
                if (!$otherPaid) {
                    $this->userCourse->delete($userCourse->id);
                }
            }

        } elseif ($type === 'event') {
            $event = $this->event->show($product_id);
            $userEvent = $this->userEvent->checkByEvent($event->id, $user_id);
            $isPaid = $status === InvoiceStatusEnum::PAID->value;

            if ($isPaid && !$userEvent) {
                $this->handleCreateUserCourseOrUserEvent($event, (object) [
                    'user_id' => $user_id ?? auth()->id(),
                    'event_id' => $event->id,
                    'auto_approve' => $event->is_auto_approve,
                ]);
            } elseif (!$isPaid && $userEvent) {
                $otherPaid = $this->transaction->checkPaidByEventAndUser($event->id, auth()->id());
                if (!$otherPaid) {
                    $this->userEvent->delete($userEvent->id);
                }
            }
        }
    }

    /**
     * map data to be sent to email
     * 
     * @param string $product_type type of product
     * @param mixed $product event product or course product
     * @param mixed $transaction transaction
     * @return array 
     */
    public function mappingSendEmailData(string $product_type, mixed $product, mixed $transaction): array
    {
        $typeName = $product_type === 'course' ? 'Kursus' : 'Event';

        $data = [
            'user_name' => $transaction->user->name,
            'product_name' => $product->title,
            'product_price' => 'Rp. ' . number_format($product->price, 0, ',', '.'),
            'product_type' => $typeName,
            'reference' => $transaction->id,
            'is_done' => false,
        ];

        if ($product_type === 'event') {
            $firstDetail = $product->eventDetails()
                ->orderBy('event_date', 'asc')
                ->orderBy('start', 'asc')
                ->first();

            $lastDetail = $product->eventDetails()
                ->orderBy('event_date', 'desc')
                ->orderBy('start', 'desc')
                ->first();

            $data = array_merge($data, [
                'event_date' => Carbon::parse($product->start_date)->translatedFormat('l, d F Y'),
                'event_time' => Carbon::parse($firstDetail->start)->format('H.i') . ' - ' . Carbon::parse($lastDetail->end)->format('H.i'),
                'event_is_online' => $product->is_online,
                'event_location' => $product->location,
                'event_map_link' => $product->map_link,
            ]);
        }

        return $data;
    }
}
