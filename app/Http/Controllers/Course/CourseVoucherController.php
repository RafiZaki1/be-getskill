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
use OpenApi\Attributes as OA;

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
    #[OA\Get(
        path: "/api/course-vouchers/{courseSlug}",
        operationId: "adminCourseVoucherIndex",
        summary: "Daftar voucher kursus (Admin)",
        description: "Melihat daftar voucher yang ada di sebuah kursus",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Course Vouchers"]
    )]
    #[OA\Parameter(name: "courseSlug", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Berhasil mengambil daftar voucher kursus")]
    public function index(string $courseSlug, Request $request)
    {
        try {
            $course = $this->course->showWithSlug($request, $courseSlug);
            $courseVouchers = $this->courseVoucher->getWhere(['course_id' => $course->id]);
            return ResponseHelper::success(CourseVoucherResource::collection($courseVouchers), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    #[OA\Post(
        path: "/api/course-vouchers/{courseSlug}",
        operationId: "adminCourseVoucherStore",
        summary: "Tambah voucher kursus (Admin)",
        description: "Menambahkan voucher baru pada kursus",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Course Vouchers"]
    )]
    #[OA\Parameter(name: "courseSlug", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "code", type: "string", example: "DISKON50"),
                new OA\Property(property: "discount", type: "integer", example: 50000),
                new OA\Property(property: "usage_limit", type: "integer", example: 10),
                new OA\Property(property: "expired_date", type: "string", format: "date", example: "2026-12-31")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Berhasil menambahkan voucher")]
    public function store(CourseVoucherRequest $request, string $courseSlug): JsonResponse
    {
        try {
            $course = $this->course->showWithSlugWithoutRequest($courseSlug);
            $data = $request->validated();
            $data['course_id'] = $course->id;
            $this->courseVoucher->store($data);
            return ResponseHelper::success(true, trans('alert.add_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.add_failed') . '. ' . $th->getMessage());
        }
    }

    #[OA\Put(
        path: "/api/course-vouchers/{course_voucher}",
        operationId: "adminCourseVoucherUpdate",
        summary: "Edit voucher kursus (Admin)",
        description: "Mengedit voucher kursus",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Course Vouchers"]
    )]
    #[OA\Parameter(name: "course_voucher", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "code", type: "string", example: "DISKON100"),
                new OA\Property(property: "discount", type: "integer", example: 100000),
                new OA\Property(property: "usage_limit", type: "integer", example: 20),
                new OA\Property(property: "expired_date", type: "string", format: "date", example: "2027-12-31")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Berhasil mengupdate voucher")]
    public function update(CourseVoucherRequest $request, CourseVoucher $courseVoucher): JsonResponse
    {
        try {
            $this->courseVoucher->update($courseVoucher->id, $request->validated());
            return ResponseHelper::success(true, trans('alert.update_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }

    #[OA\Delete(
        path: "/api/course-vouchers/{course_voucher}",
        operationId: "adminCourseVoucherDestroy",
        summary: "Hapus voucher kursus (Admin)",
        description: "Menghapus voucher kursus",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Course Vouchers"]
    )]
    #[OA\Parameter(name: "course_voucher", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Berhasil menghapus voucher")]
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
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
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
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
}
