<?php
namespace App\Services\Course;

use App\Base\Interfaces\uploads\ShouldHandleFileUpload;
use App\Enums\UploadDiskEnum;
use App\Http\Requests\CourseUpdateRequest;
use App\Http\Requests\StoreCourseRequest;
use App\Models\Course;
use App\Traits\UploadTrait;
use App\Contracts\Repositories\TransactionRepository;;
use App\Contracts\Repositories\Author\AuthorRepository;
use App\Contracts\Repositories\Author\AuthorCourseRepository;
use App\Contracts\Repositories\Course\ModuleRepository;
use App\Enums\AuthorCourseStatusEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CourseService implements ShouldHandleFileUpload
{
    // private TransactionRepository $transactionRepository;
    private ModuleRepository $moduleRepository;


    public function __construct(
        // TransactionRepository $transactionRepository,
        ModuleRepository $moduleRepository
    ) {
        // $this->transactionRepository = $transactionRepository;
        $this->moduleRepository = $moduleRepository;
    }

    use UploadTrait;

    /**
     * store
     *
     * @return array
     */
    public function store(StoreCourseRequest $request): array | bool
    {
        $data            = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $data['photo']   = $this->upload(UploadDiskEnum::COURSES->value, $request->file('photo'));
        if ($data['is_premium'] == 0) {
            $data['price']             = 0;
            $data['promotional_price'] = 0;
        }

        return $data;
    }

    /**
     * update
     *
     * @param  mixed $user
     * @param  mixed $request
     * @return array
     */
    public function update(Course $course, CourseUpdateRequest $request): array | bool
    {
        $data  = $request->validated();
        $photo = $course->photo;

        if ($request->hasFile('photo')) {
            if ($photo) {
                $this->remove($photo);
            }
            $photo = $this->upload(UploadDiskEnum::COURSES->value, $request->file('photo'));
        }

        if ($data['is_premium'] == 0) {
            $data['price']             = 0;
            $data['promotional_price'] = 0;
        }

        $data['photo'] = $photo;
        return $data;
    }

    public function publish(Course $course)
    {
        return [
            'modules'     => $course->modules->count(),
            'sub_modules' => $course->modules->sortBy('step')->first()?->subModules->count() ?? 0,
            'test'        => $course->courseTest?->courseTestQuestions,
        ];
    }

    public function statisticTransaction($transactions)
    {
        // return $transactions->groupBy('created_at');
    }

    /**
     * Return fund statistics and monthly totals for a course
     *
     * @param Course|null $course
     * @param int|null $year
     * @return array
     */
    // public function statisticFundByCourse(Course $course = null, ?int $year = null): array
    // {
    //     $monthly = $this->transactionRepository->getMonthlyTotalByCourseOrAll($course?->id, $year);
    //     $total = $monthly['totalYearIncome'] ?? 0;
    //     $monthlyTotals = $monthly['data'];
    //     $monthlyGetskill = array_map(fn($v) => (int) round($v * 0.3), $monthlyTotals);
    //     $monthlyAuthor = array_map(fn($v) => (int) round($v * 0.7), $monthlyTotals);

    //     $totalGetskill = array_sum($monthlyGetskill);
    //     $totalAuthor = array_sum($monthlyAuthor);

    //     return [
    //         'monthly' => $monthlyTotals,
    //         'months' => $monthly['months'],
    //         'total' => $total,
    //         'monthly_getskill' => $monthlyGetskill,
    //         'monthly_author' => $monthlyAuthor,
    //         'total_getskill' => $totalGetskill,
    //         'total_author' => $totalAuthor,
    //     ];
    // }

    // public function transferOwner(Course $course, string $userId): void
    // {
    //     $course->user_id = $userId;
    //     $course->save();
    //     $author = $this->authorRepository->getAuhtorByUserId($userId);
    //     $authorCourseData = [
    //         'author_id'    => $author->id,
    //         'course_id'    => $course->id,
    //         'course_title' => $course->title,
    //         'price'        => $course->price ?? 0,
    //         'is_premium'   => (bool) $course->is_premium,
    //         'status'       => AuthorCourseStatusEnum::ACCEPTED->value,
    //         'is_ready'     => 1,
    //     ];

    // $existing = $this->authorCourseRepository->findByCourseId($course->id);

    //     if ($existing) {
    //         $this->authorCourseRepository->update($existing->id, $authorCourseData);
    //     } else {
    //         $this->authorCourseRepository->store($authorCourseData);
    //     }
    // }

    public function arrangeModuleSteps(Course $course): void
    {
        $modules = $course->modules()->orderBy('step')->get();

        $case = 'CASE id ';
        foreach ($modules as $index => $module) {
            $case .= "WHEN '{$module->id}' THEN " . ($index + 1) . ' ';
        }
        $case .= 'END';

        $this->moduleRepository->bulkUpdateById($modules->pluck('id')->toArray(), ['step' => DB::raw($case)]);
    }
}
