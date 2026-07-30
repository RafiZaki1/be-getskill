<?php

namespace App\Http\Resources;

use App\Enums\TestEnum;
use App\Models\UserCourse;
use App\Models\UserCourseTest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CourseStatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $transactions = $this->transactions;

        $groupedTransactions = $transactions->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('m');
        });

        $groupedTransactionsWithMonthName = $groupedTransactions->mapWithKeys(function ($items, $key) {
            $monthName = Carbon::createFromFormat('m', $key)->locale('id')->isoFormat('MMMM');
            $monthNameLowerCase = strtolower($monthName);
            return [$monthNameLowerCase => $items->sum('amount')];
        });

        $preTestAvg = UserCourseTest::whereIn('course_test_id', $this->courseTests->pluck('id'))
            ->where('test_type', TestEnum::PRETEST->value)
            ->avg('score');

        $postTestAvg = UserCourseTest::whereIn('course_test_id', $this->courseTests->pluck('id'))
            ->where('test_type', TestEnum::POSTTEST->value)
            ->avg('score');

        $preTestScores = UserCourseTest::whereIn('course_test_id', $this->courseTests->pluck('id'))
            ->where('test_type', TestEnum::PRETEST->value)
            ->orderBy('created_at')
            ->pluck('score')
            ->toArray();

        $postTestScores = UserCourseTest::whereIn('course_test_id', $this->courseTests->pluck('id'))
            ->where('test_type', TestEnum::POSTTEST->value)
            ->orderBy('created_at')
            ->pluck('score')
            ->toArray();

        $totalRatings = $this->courseReviews()->count();
        $ratingsDistribution = collect([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0])
            ->merge(
                $this->courseReviews->groupBy('rating')
                    ->map(fn($group) => $group->count())
            );

        $totalUsers = UserCourse::where('course_id', $this->id)->count();
        $completedCount = UserCourse::where('course_id', $this->id)->where('has_post_test', true)->count();
        $uncompletedCount = UserCourse::where('course_id', $this->id)->where('has_post_test', false)->count();

        $completedPercentage = $totalUsers > 0 ? ($completedCount / $totalUsers) * 100 : 0;
        $uncompletedPercentage = $totalUsers > 0 ? ($uncompletedCount / $totalUsers) * 100 : 0;

        $ratingsPercentageDistribution = $ratingsDistribution->map(function ($count) use ($totalRatings) {
            return $totalRatings > 0 ? round(($count / $totalRatings) * 100, 2) : 0;
        });

        $completedByMonth = UserCourse::where('course_id', $this->id)
            ->where('has_post_test', true)
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            })
            ->mapWithKeys(function ($items, $key) {
                $monthName = Carbon::createFromFormat('m', $key)->locale('id')->isoFormat('MMMM');
                $monthNameLowerCase = strtolower($monthName);
                return [$monthNameLowerCase => $items->count()];
            });

        return [
            'total_purchases' => $this->userCourses ? $this->userCourses()->count() : 0,
            'total_revenue' => optional($this->userCourses->first())->course->price ?? 0,
            'total_tasks' => $this->modules()->withCount('moduleTasks'),
            'completed' => $this->userCourses()->whereNotNull('has_post_test')->count(),
            'completed_by_month' => $completedByMonth->all(),

            'complete_percentage' => number_format($completedPercentage, 2),
            'uncomplete_percentage' => number_format($uncompletedPercentage, 2),

            'transaction' => $groupedTransactionsWithMonthName,

            'pre_test_average' => number_format($preTestAvg, 1),
            'post_test_average' => number_format($postTestAvg, 1),
            'rating_count' => $totalRatings,
            'average_rating' => number_format($this->courseReviews->avg('rating'), 1) ?? 0,

            'ratings_distribution' => $ratingsDistribution->all(),
            'ratings_percentage_distribution' => $ratingsPercentageDistribution->all(),

            'score_average' => UserCourseTest::whereIn('course_test_id', $this->courseTests->pluck('id'))->avg('score'),
            'pre_test_score_distribution' => $preTestScores,
            'post_test_score_distribution' => $postTestScores,
        ];
    }
}
