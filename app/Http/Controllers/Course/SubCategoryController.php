<?php

namespace App\Http\Controllers\Course;

use App\Contracts\Interfaces\Course\SubCategoryInterface;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubCategoryRequest;
use App\Http\Resources\SubCategoryResource;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    private SubCategoryInterface $subCategory;

    /**
     * Method __construct
     *
     * @param SubCategoryInterface $subCategory [explicite description]
     *
     * @return void
     */
    public function __construct(SubCategoryInterface $subCategory)
    {
        $this->subCategory = $subCategory;
    }


    /**
     * Method index
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $subCategories = $this->subCategory->get();
            return ResponseHelper::success(SubCategoryResource::collection($subCategories), trans('alert.fetch_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
    /**
     * Method store
     *
     * @param SubCategoryRequest $request [explicite description]
     *
     * @return JsonResponse
     */
    public function store(SubCategoryRequest $request, Category $category): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['category_id'] = $category->id;
            $this->subCategory->store($data);
            return ResponseHelper::success(true, trans('alert.add_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.add_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * Method update
     *
     * @param SubCategoryRequest $request [explicite description]
     * @param SubCategory $subCategory [explicite description]
     *
     * @return JsonResponse
     */
    public function update(SubCategoryRequest $request, SubCategory $subCategory): JsonResponse
    {
        try {
            $this->subCategory->update($subCategory->id, $request->validated());
            return ResponseHelper::success(true, trans('alert.update_success'));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * Method destroy
     *
     * @param SubCategory $subCategory [explicite description]
     *
     * @return JsonResponse
     */
    public function destroy(SubCategory $subCategory): JsonResponse
    {
        try {
            $this->subCategory->delete($subCategory->id);
            return ResponseHelper::success(true, trans('alert.delete_success'));
        } catch (\Throwable $e) {
            return ResponseHelper::success(true, trans('alert.delete_constrained'), 422);
        }
    }

    /**
     * getByCategory
     *
     * @return JsonResponse
     */
    public function getByCategory(Category $category): JsonResponse
    {
        try {
            $subCategories = $this->subCategory->getByCategory($category->id);
            return ResponseHelper::success(SubCategoryResource::collection($subCategories));
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }
}
