<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

// Course interfaces
use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\CategoryInterface;
use App\Contracts\Interfaces\Course\SubCategoryInterface;
use App\Contracts\Interfaces\Course\ModuleInterface;
use App\Contracts\Interfaces\Course\SubModuleInterface;
use App\Contracts\Interfaces\Course\UserCourseInterface;
use App\Contracts\Interfaces\Course\CourseReviewInterface;
use App\Contracts\Interfaces\Course\CourseTestInterface;
use App\Contracts\Interfaces\Course\CourseTestQuestionInterface;
use App\Contracts\Interfaces\Course\CourseVoucherInterface;
use App\Contracts\Interfaces\Course\QuizInterface;
use App\Contracts\Interfaces\Course\ModuleQuestionInterface;
use App\Contracts\Interfaces\Course\ModuleTaskInterface;
use App\Contracts\Interfaces\Course\SubmissionTaskInterface;
use App\Contracts\Interfaces\Course\BankModuleInterface;
use App\Contracts\Interfaces\Course\PostTestIdInterface;
use App\Contracts\Interfaces\UserQuizInterface;
use App\Contracts\Interfaces\UserCourseTestInterface;
use App\Contracts\Interfaces\TransactionInterface;

// Course repositories
use App\Contracts\Repositories\Course\CourseRepository;
use App\Contracts\Repositories\Course\CategoryRepository;
use App\Contracts\Repositories\Course\SubCategoryRepository;
use App\Contracts\Repositories\Course\ModuleRepository;
use App\Contracts\Repositories\Course\SubModuleRepository;
use App\Contracts\Repositories\Course\UserCourseRepository;
use App\Contracts\Repositories\Course\CourseReviewRepository;
use App\Contracts\Repositories\Course\CourseTestRepository;
use App\Contracts\Repositories\Course\CourseTestQuestionRepository;
use App\Contracts\Repositories\Course\CourseVoucherRepository;
use App\Contracts\Repositories\Course\QuizRepository;
use App\Contracts\Repositories\Course\ModuleQuestionRepository;
use App\Contracts\Repositories\Course\ModuleTaskRepository;
use App\Contracts\Repositories\Course\SubmissionTaskRepository;
use App\Contracts\Repositories\Course\BankModuleRepository;
use App\Contracts\Repositories\UserQuizRepository;
use App\Contracts\Repositories\UserCourseTestRepository;
use App\Contracts\Repositories\TransactionRepository;

// Services
use App\Services\Course\CourseTestService;

class AppServiceProvider extends ServiceProvider
{
    private array $register = [
        // Course
        CourseInterface::class           => CourseRepository::class,
        CategoryInterface::class         => CategoryRepository::class,
        SubCategoryInterface::class      => SubCategoryRepository::class,
        ModuleInterface::class           => ModuleRepository::class,
        SubModuleInterface::class        => SubModuleRepository::class,
        UserCourseInterface::class       => UserCourseRepository::class,
        CourseReviewInterface::class     => CourseReviewRepository::class,
        CourseTestInterface::class       => CourseTestRepository::class,
        CourseTestQuestionInterface::class => CourseTestQuestionRepository::class,
        CourseVoucherInterface::class    => CourseVoucherRepository::class,
        QuizInterface::class             => QuizRepository::class,
        ModuleQuestionInterface::class   => ModuleQuestionRepository::class,
        ModuleTaskInterface::class       => ModuleTaskRepository::class,
        SubmissionTaskInterface::class   => SubmissionTaskRepository::class,
        BankModuleInterface::class       => BankModuleRepository::class,
        PostTestIdInterface::class       => CourseTestService::class,
        UserQuizInterface::class         => UserQuizRepository::class,
        UserCourseTestInterface::class   => UserCourseTestRepository::class,
        TransactionInterface::class      => TransactionRepository::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach ($this->register as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('id');

        if (config('app.env') === 'production' || config('app.env') === 'development') {
            URL::forceScheme('https');
        }

        \App\Models\Course::observe(\App\Observers\CourseObserver::class);
        \App\Models\CourseReview::observe(\App\Observers\CourseReviewObserver::class);
        \App\Models\Module::observe(\App\Observers\ModuleObserver::class);
        \App\Models\SubModule::observe(\App\Observers\SubModuleObserver::class);
    }
}
