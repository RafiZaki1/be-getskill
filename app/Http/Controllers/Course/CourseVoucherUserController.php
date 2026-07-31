<?php

namespace App\Http\Controllers\Course;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\CourseVoucher;
use App\Models\CourseVoucherUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseVoucherUserController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required',
            'course_voucher_id' => 'required',
        ], [
            'user_id.required' => 'user wajib diisi',
            'course_voucher_id' => 'required',
        ]);
        $courseVoucher = CourseVoucher::first();
        // return response()->json(['limit' => $courseVoucher->usage_limit, 'pengguna' => $courseVoucher->courseVoucherUsers->count()]);
        if ($courseVoucher->courseVoucherUsers->count() >= $courseVoucher->usage_limit) {
            return ResponseHelper::error(false, "batas penggunaan voucher sudah tercapai");
        } else {
            CourseVoucherUser::create($data);
            return ResponseHelper::success(true, "berhasil menggunakan voucher");
        }
    }
}
