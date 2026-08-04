<?php
namespace App\Contracts\Repositories\Course;

use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Repositories\BaseRepository;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class CourseRepository extends BaseRepository implements CourseInterface
{

    /**
     * Method __construct
     *
     * @param Course $course [explicite description]
     *
     * @return void
     */
    public function __construct(Course $course)
    {
        $this->model              = $course;
    }
    /**
     * Method customPaginate
     *
     * @param Request $request [explicite description]
     * @param int $pagination [explicite description]
     *
     * @return LengthAwarePaginator
     */
    public function customPaginate(Request $request, int $pagination = 9): LengthAwarePaginator
    {
        $authorizationHeader = $request->header('Authorization');
        if ($authorizationHeader && str_starts_with($authorizationHeader, 'Bearer ')) {
            $token = substr($authorizationHeader, 7); // Ekstrak token dari header
        } else {
            $token = null;
        }
        $user = $token ? PersonalAccessToken::findToken($token)?->tokenable : null;

        return $this->model->query()
            ->with(['modules', 'subCategory.category'])
            ->when($request->is_ready, function ($query) use ($request) {
                $query->where('is_ready', $request->is_ready);
            })
            ->withCount('userCourses')
            ->when($request->title, function ($query) use ($request) {
                return $query->where('title', 'LIKE', '%' . $request->title . '%');
            })
            ->when($request->categories, function ($query) use ($request) {
                $query->when(
                    collect($request->categories)->filter()->isNotEmpty(),
                    function ($query) use ($request) {
                        $query->whereIn('sub_category_id', $request->categories);
                    }
                );
            })
            ->when($request->sub_category_id, function ($query) use ($request) {
                $query->when(
                    function ($query) use ($request) {
                        $query->where('sub_category_id', $request->sub_category_id);
                    }
                );
            })
            ->when($request->has('status') && $request->status != null, function ($query) use ($request) {
                return $query->where('is_ready', $request->status ?? 0);
            })
            ->when($request->minimum || $request->maximum, function ($query) {
                $query->where('is_premium', 1);
            })
            ->when($request->is_free && (! $request->minimum || ! $request->maximum), function ($query) {
                $query->where('is_premium', 0);
            })
            ->when(filled($request->minimum) && filled($request->maximum), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->whereBetween('promotional_price', [$request->minimum, $request->maximum])
                        ->orWhere(function ($query) use ($request) {
                            $query->where(function ($query) {
                                $query->whereNull('promotional_price')
                                    ->orWhere('promotional_price', 0);
                            })
                                ->whereBetween('price', [$request->minimum, $request->maximum]);
                        });
                });
            })
            ->when(filled($request->minimum) && blank($request->maximum), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('promotional_price', '>=', $request->minimum)
                        ->orWhere(function ($query) use ($request) {
                            $query->where(function ($query) {
                                $query->whereNull('promotional_price')
                                    ->orWhere('promotional_price', 0);
                            })
                                ->where('price', '>=', $request->minimum);
                        });
                });
            })
            ->when(blank($request->minimum) && filled($request->maximum), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('promotional_price', '<=', $request->maximum)
                        ->orWhere(function ($query) use ($request) {
                            $query->where(function ($query) {
                                $query->whereNull('promotional_price')
                                    ->orWhere('promotional_price', 0);
                            })
                                ->where('price', '<=', $request->maximum);
                        });
                });
            })

            ->when($user?->hasRole('guest') || ! $user, function ($query) {
                $query->where('is_ready', 1);
            })
            ->when($request->rating, function ($query) use ($request) {
                $query->withAvg('courseReviews', 'rating')
                    ->whereHas('courseReviews', function ($query) use ($request) {
                        $query->whereIn('rating', $request->rating);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($pagination);
    }

    /**
     * Method getTop
     *
     * @return mixed
     */
    public function getTop(): mixed
    {
        return $this->model
            ->with(['subCategory', 'courseReviews', 'userCourses'])
            ->withCount(['modules', 'userCourses', 'userCourses'])
            ->where('is_ready', true)
            ->orderBy('user_courses_count', 'desc') // Mengurutkan berdasarkan userCourses count
            ->limit(4)
            ->get();
    }
    public function getCourseById(mixed $courseId): mixed
    {
        return $this->model
            ->where('id', $courseId)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function topRatings(): mixed
    {
        return $this->model
            ->with(['subCategory', 'courseReviews', 'userCourses'])
            ->withCount(['modules', 'userCourses', 'courseReviews'])
            ->where('is_ready', true)
            ->orderBy('course_reviews_count', 'desc') // Mengurutkan berdasarkan courseReviews count
            ->limit(4)
            ->get();
    }


    /**
     * search
     *
     * @param  mixed $request
     * @return mixed
     */
    public function search(Request $request): mixed
    {
        $authorizationHeader = $request->header('Authorization');
        if ($authorizationHeader && str_starts_with($authorizationHeader, 'Bearer ')) {
            $token = substr($authorizationHeader, 7); // Ekstrak token dari header
        } else {
            $token = null;
        }
        $user = $token ? PersonalAccessToken::findToken($token)?->tokenable : null;

        return $this->model->query()
            ->with('modules')
            ->when($request->is_ready, function ($query) use ($request) {
                $query->where('is_ready', $request->is_ready);
            })
            ->withCount('userCourses')
            ->when($request->title, function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->title . '%');
            })
            ->when($request->order === "best seller", function ($query) {
                $query->orderBy('user_courses_count', 'desc');
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('is_ready', $request->status);
            })
            ->when($request->maximum, function ($query) use ($request) {
                $query->where('price', '<=', $request->maximum);
            })
            ->when($request->minimum, function ($query) use ($request) {
                $query->where('price', '>=', $request->minimum);
            })
        // Tambahkan filter untuk guest
            ->when($user?->hasRole('guest') || ! $user || $request->order === "best seller", function ($query) {
                $query->where('is_ready', 1);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function count(): mixed
    {
        return $this->model->query()->count();
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
        return $this->model->query()->create($data);
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
     * Method showWithSlug
     *
     * @param string $slug [explicite description]
     *
     * @return mixed
     */
    public function showWithSlug(Request $request, string $slug): mixed
    {
        return $this->model->query()->where('slug', $slug)
            ->when($request?->transaction, function ($query) use ($request) {
                $query->with('transactions');
            })->firstOrFail();
    }

    /**
     * showWithSlugWithoutRequest
     *
     * @param  mixed $slug
     * @return mixed
     */
    public function showWithSlugWithoutRequest(string $slug): mixed
    {
        return $this->model->query()->where(['slug' => $slug])->firstOrFail();
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
     * getWhere
     *
     * @param  mixed $data
     * @return mixed
     */
    public function get(): mixed
    {
        return $this->model->query()->whereHas('userCourses', function ($query) {
            $query->where('user_id', auth()->user()->id);
        })->paginate(9);
    }

    public function getCourseWithModulesAndTasks($courseId)
    {
        return $this->model->query()->with('modules.moduleTasks')->find($courseId);
    }

    public function getByCourseIds(array $courseIds)
    {
        return $this->model->query()
            ->whereIn('id', $courseIds)
            ->get();
    }

    public function getByCourseIdsWithRelations(array $courseIds)
    {
        return $this->model->query()
            ->whereIn('id', $courseIds)
            ->with(['userCourses', 'courseReviews'])
            ->get();
    }

    public function getBySubModuleSlug($slug)
    {
        return $this->model->whereRelation('modules.subModules', 'slug', $slug)->first();
    }
}
