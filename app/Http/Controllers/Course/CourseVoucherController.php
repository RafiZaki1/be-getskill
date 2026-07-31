<?php

namespace App\Http\Controllers\Course;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\CourseVoucher;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseVoucherRequest;
use App\Http\Resources\CourseVoucherResource;
use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\CourseVoucherInterface;
use App\Contracts\Interfaces\TransactionInterface;
use Illuminate\Http\Exceptions\HttpResponseException;

class CourseVoucherController extends Controller
{
    private CourseVoucherInterface $courseVoucher;
    private CourseInterface $course;
    private TransactionInterface $transaction;
    /**
     * Method __construct
     *
     * @param CourseVoucherInterface $courseVoucher [explicite description]
     *
     * @return void
     */
    public function __construct(CourseVoucherInterface $courseVoucher, CourseInterface $course, TransactionInterface $transaction)
    {
        $this->courseVoucher = $courseVoucher;
        $this->course = $course;
        $this->transaction = $transaction;
    }
    /**
     * Method index
     *
     * @param Course $course [explicite description]
     *
     * @return JsonResponse
     */
    public function index(string $courseSlug, Request $request)
    {
        try {
            $course = $this->course->showWithSlug($request, $courseSlug);
            $courseVouchers = $this->courseVoucher->getWhere(['course_id' => $course->id]);
            return ResponseHelper::success(CourseVoucherResource::collection($courseVouchers), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method store
     *
     * @param CourseVoucherRequest $request [explicite description]
     * @param Course $course [explicite description]
     *
     * @return JsonResponse
     */
    public function store(CourseVoucherRequest $request, string $courseSlug): JsonResponse
    {
        try {
            $course = $this->course->showWithSlugWithoutRequest($courseSlug);
            $data = $request->validated();
            $data['course_id'] = $course->id;
            $this->courseVoucher->store($data);
            return ResponseHelper::success(true, trans('alert.add_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.add_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method update
     *
     * @param CourseVoucherRequest $request [explicite description]
     * @param CourseVoucher $courseVoucher [explicite description]
     *
     * @return JsonResponse
     */
    public function update(CourseVoucherRequest $request, CourseVoucher $courseVoucher): JsonResponse
    {
        try {
            $this->courseVoucher->update($courseVoucher->id, $request->validated());
            return ResponseHelper::success(true, trans('alert.update_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method destroy
     *
     * @param CourseVoucher $courseVoucher [explicite description]
     *
     * @return JsonResponse
     */
    public function destroy(CourseVoucher $courseVoucher): JsonResponse
    {
        try {
            if ($courseVoucher->transactions->count() > 0) {
                $deleted = $this->transaction->deleteByCourseVoucher($courseVoucher->id);
                if ($deleted > 0) {
                    $this->courseVoucher->delete($courseVoucher->id);
                    return ResponseHelper::success(null, trans('alert.delete_success'));
                } else {
                    return ResponseHelper::error(null, 'Gagal menghapus, voucher sudah digunakan');
                }
            } else {
                $this->courseVoucher->delete($courseVoucher->id);
                return ResponseHelper::success(true, trans('alert.delete_success'));
            }
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.delete_failed') . '. ' . $th->getMessage());
        }
    }

    public function checkCode(Request $request)
    {
        try {
            $voucher = $this->courseVoucher->getByCode($request->voucher_code);
            if (!is_null($voucher)) {
                if ($voucher->transactions_count >= $voucher->usage_limit) {
                    return ResponseHelper::error($voucher, trans('alert.voucher_limit'));
                }
                return ResponseHelper::success($voucher, trans('alert.voucher_valid'));
            } else {
                return ResponseHelper::error(null, trans('alert.voucher_invalid'), 404);
            }
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
}
