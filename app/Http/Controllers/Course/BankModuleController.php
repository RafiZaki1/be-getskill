<?php

namespace App\Http\Controllers\Course;

use App\Contracts\Repositories\Course\BankModuleRepository;
use App\Contracts\Repositories\Course\CourseRepository;
use App\Services\Course\BankModuleService;
use App\Http\Requests\IndustryClass\BankModuleRequest;
use App\Http\Resources\Course\BankModuleResource;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Course\CourseModuleNoDetailResource;
use App\Traits\PaginationTrait;

class BankModuleController extends Controller
{
    use PaginationTrait;

    private BankModuleRepository $bankModuleRepository;
    private CourseRepository $courseRepository;
    public function __construct(BankModuleRepository $bankModuleRepository, CourseRepository $courseRepository)
    {
        $this->bankModuleRepository = $bankModuleRepository;
        $this->courseRepository = $courseRepository;
    }

    public function list(Request $request): JsonResponse
    {
        try{
            $courses = $this->courseRepository->getSome($request);
            return ResponseHelper::success(CourseModuleNoDetailResource::collection($courses), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

     public function index(Request $request)
    {
        try {
            $banks = $this->bankModuleRepository->customPaginate($request);
            $payload['data'] = BankModuleResource::collection($banks);
            $payload['paginate'] = $this->customPaginate($banks->currentPage(), $banks->lastPage());
            return ResponseHelper::success($payload, trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed').'. '.$th->getMessage());
        }
    }

    public function store(BankModuleRequest $request)
    {
        DB::beginTransaction();
        try {
            $bank = $this->bankModuleRepository->store($request->validated());
            DB::commit();
            return ResponseHelper::success(BankModuleResource::make($bank->load('items')), trans('alert.store_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(null, trans('alert.add_failed').'. '.$th->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $bank = $this->bankModuleRepository->find($id);
            return ResponseHelper::success(BankModuleResource::make($bank), trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed').'. '.$th->getMessage());
        }
    }

    public function update(BankModuleRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $bank = $this->bankModuleRepository->update($id, $request->validated());
            DB::commit();
            return ResponseHelper::success(BankModuleResource::make($bank->load('items')), trans('alert.update_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(null, trans('alert.update_failed').'. '.$th->getMessage());
        }
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $this->bankModuleRepository->delete($id);
            DB::commit();
            return ResponseHelper::success(null, trans('alert.delete_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error(null, trans('alert.delete_failed').'. '.$th->getMessage());
        }
    }

}
