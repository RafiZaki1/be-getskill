<?php

namespace App\Http\Controllers\Course;

use App\Contracts\Interfaces\Course\CategoryInterface;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\PaginationTrait;
use App\Traits\UploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private CategoryInterface $category;
    use PaginationTrait;
    /**
     * Method __construct
     *
     * @param CategoryInterface $category [explicite description]
     *
     * @return void
     */
    public function __construct(CategoryInterface $category)
    {
        $this->category = $category;
    }
    /**
     * Method index
     *
     * @param Request $request [explicite description]
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            if ($request->has('page')) {
                $categories = $this->category->customPaginate($request);
                $data['paginate'] = $this->customPaginate($categories->currentPage(), $categories->lastPage());
                $data['data'] = CategoryResource::collection($categories);
            } else {
                $categories = $this->category->search($request);
                $data['data'] = CategoryResource::collection($categories);
            }
            return ResponseHelper::success($data, trans('alert.fetch_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.fetch_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['name'] = strip_tags($request->name);
    
            $this->category->store($data);
            return ResponseHelper::success(true, trans('alert.add_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.add_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        try {
            $this->category->update($category->id, $request->validated());
            return ResponseHelper::success(true, trans('alert.update_success'));
        } catch (\Throwable $th) {
            return ResponseHelper::error(null, trans('alert.update_failed') . '. ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            $this->category->delete($category->id);
            return ResponseHelper::success(true, trans('alert.delete_success'));
        } catch (\Throwable $e) {
            return ResponseHelper::error(true, trans('alert.delete_constrained') . '. ' . $e->getMessage());
        }
    }
}
