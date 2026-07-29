<?php

namespace App\Contracts\Repositories\Course;

use App\Models\Course;
use App\Models\Teacher;
use App\Models\Category;
use App\Models\Classroom;
use App\Models\CourseTask;
use Illuminate\Http\Request;
use App\Models\SubmissionTask;
use App\Contracts\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\CategoryInterface;
use App\Contracts\Interfaces\Course\CourseTaskInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Contracts\Interfaces\Course\SubmissionTaskInterface;

class SubmissionTaskRepository extends BaseRepository implements SubmissionTaskInterface
{
    /**
     * Method __construct
     *
     * @param SubmissionTask $submissionTask [explicite description]
     *
     * @return void
     */
    public function __construct(SubmissionTask $submissionTask)
    {
        $this->model = $submissionTask;
    }
    /**
     * Method getWhere
     *
     * @param array $data [explicite description]
     *
     * @return mixed
     */
    public function getWhere(array $data): mixed
    {
        return $this->model->query()->where($data)->get();
    }
    /**
     * Method getWhereSearch
     *
     * @param array $data [explicite description]
     * @param Request $request
     *
     * @return mixed
     */
    public function getWhereSearch(array $data, Request $request)
    {
        return $this->model->query()->where($data)
        ->when($request->filled('search'), function ($query) use ($request) {
            $query->whereHas('user', function ($query2) use ($request) {
                $query2->where('name', 'like', '%' . $request->input('search') . '%');
            });
        })->when($request->filled('date'), function ($query) use ($request) {
            $query->whereDate('created_at', $request->input('date'));
        })->when($request->filled('filter'), function ($query) use ($request) {
            if ($request->input('filter') === 'done') {
                $query->whereNotNull('grade');
            } else if ($request->input('grade' === 'undone')){
                $query->whereNull('grade');
            }
        })
        ->get();
    }
    /**
     * Method store
     *
     * @param array $data [explicite description]
     *
     * @return mixed
     */
    public function store(array $data): mixed
    {
        if ($submissionTask = $this->model->query()->where('user_id', $data['user_id'])->where('module_task_id', $data['module_task_id'])->first()) {
            $updated = $this->model->query()->where('user_id', $data['user_id'])->where('module_task_id', $data['module_task_id'])->update($data);
            return $updated ? ['status' => "updated", 'data' => $submissionTask] : "failed";
        } else {
            $created = $this->model->query()->create($data);
            return $created ? ["status" => "created"] : "failed";
        }
    }
    /**
     * Method show
     *
     * @param mixed $id [explicite description]
     *
     * @return mixed
     */
    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }
    /**
     * Method update
     *
     * @param mixed $id [explicite description]
     * @param array $data [explicite description]
     *
     * @return mixed
     */
    public function update(mixed $id, array $data): mixed
    {
        return $this->show($id)->update($data);
    }
    /**
     * Method delete
     *
     * @param mixed $id [explicite description]
     *
     * @return mixed
     */
    public function delete(mixed $id): mixed
    {
        return $this->show($id)->delete();
    }

    /**
     * @param string $teacherId
     * @return int
     *
     * @throws ModelNotFoundException
     */

    public function countTeacherPendingTasks(string $teacherId): int
    {
        $teacher = Teacher::find($teacherId);
        if (!$teacher) {
            throw new ModelNotFoundException("Teacher tidak ditemukan");
        }

        $classroomIds = Classroom::where('teacher_id', $teacher->id)->pluck('id');

        $count = SubmissionTask::whereHas('user.student', function ($query) use ($classroomIds) {
            $query->whereHas('studentClassrooms', function ($query2) use ($classroomIds) {
                $query2->whereIn('classroom_id', $classroomIds);
            });
        })
            ->whereNull('grade') // Hanya submission yang belum dinilai
            ->count();

        return $count;
    }

    public function getByStudentAndTask($userId, $taskId)
    {
        return $this->model->query()->where('user_id', $userId)->where('module_task_id', $taskId)->first();
    }

    public function getByUserAndModuleTask(mixed $user_id, mixed $module_task_id)
    {
        return $this->model->query()->where('user_id', $user_id)->where('module_task_id', $module_task_id)->first();
    }
}
