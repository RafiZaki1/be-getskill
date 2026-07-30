<?php

namespace App\Http\Controllers;

use App\Contracts\Interfaces\Course\CourseTestQuestionInterface;
use App\Helpers\ResponseHelper;
use App\Http\Requests\CourseTestQuestionRequest;
use App\Http\Resources\CourseTestQuestionResource;
use App\Models\CourseTest;
use App\Models\CourseTestQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseTestQuestionController extends Controller
{
    private CourseTestQuestionInterface $model;
    public function __construct(CourseTestQuestionInterface $model)
    {
        $this->model = $model;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(CourseTest $courseTest): JsonResponse
    {
        $courseTestQuestions = $this->model->getWhere(['course_test_id' => $courseTest->id]);
        return ResponseHelper::success(CourseTestQuestionResource::collection($courseTestQuestions), trans('alert.fetch_success'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseTestQuestionRequest $request, CourseTest $courseTest): JsonResponse
    {
        $data = $request->validated();
        foreach ($request['question_count'] as $index => $questionCount) {
            $storeData = [
                'course_test_id' => $courseTest->id,
                'question_count' => $questionCount,
                'module_id' => $data['module_id'][$index]
            ];
            $this->model->store($storeData);
        }
        return ResponseHelper::success(null, trans('alert.add_success'));
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseTestQuestion $courseTestQuestion): JsonResponse
    {
        return ResponseHelper::success(CourseTestQuestionResource::make($courseTestQuestion));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseTestQuestion $courseTestQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseTestQuestionRequest $request, CourseTestQuestion $courseTestQuestion): JsonResponse
    {
        $this->model->update($courseTestQuestion->id, $request->validated());
        return ResponseHelper::success(null, trans('alert.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseTestQuestion $courseTestQuestion): JsonResponse
    {
        $this->model->delete($courseTestQuestion->id);
        return ResponseHelper::success(null, trans('alert.delete_success'));
    }
}
