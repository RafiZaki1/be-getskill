<?php

namespace App\Services;

use App\Base\Interfaces\uploads\ShouldHandleFileUpload;
use App\Contracts\Interfaces\BlogInterface;
use App\Contracts\Interfaces\BlogViewInterface;
use App\Contracts\Interfaces\Course\ModuleQuestionInterface;
use App\Contracts\Interfaces\Course\UserCourseInterface;
use App\Contracts\Interfaces\EventDetailInterface;
use App\Contracts\Interfaces\EventInterface;
use App\Contracts\Interfaces\UserCourseTestInterface;
use App\Contracts\Interfaces\UserQuizInterface;
use App\Enums\TestEnum;
use App\Enums\UploadDiskEnum;
use App\Helpers\CourcePercentaceHelper;
use App\Helpers\ResponseHelper;
use App\Http\Requests\BlogRequest;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\CustomCourseTestRequest;
use App\Http\Requests\EventRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\UserCourseTestRequest;
use App\Http\Requests\UserQuizRequest;
use App\Models\Blog;
use App\Models\CourseTest;
use App\Models\Event;
use App\Models\EventDetail;
use App\Models\ModuleQuestion;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserCourseTest;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;

class CourseTestService implements ShouldHandleFileUpload
{

    use UploadTrait;
    private UserCourseTestInterface $userCourseTest;
    private UserCourseInterface $userCourse;
    private ModuleQuestionInterface $module;
    public function __construct(UserCourseTestInterface $userCourseTest, ModuleQuestionInterface $module, UserCourseInterface $userCourse)
    {
        $this->userCourseTest = $userCourseTest;
        $this->userCourse = $userCourse;
        $this->module = $module;
    }
    public function store(CourseTest $courseTest)
    {
        $data = [];
        $questions = ModuleQuestion::query()->inRandomOrder()->limit($courseTest->total_question)->get();
        $moduleIds = $questions->pluck('id')->toArray();
        $data['module_question_id'] = implode(',', $moduleIds);
        $data['user_id'] = auth()->user()->id;
        $data['course_id'] = $courseTest->id;
        $this->userCourseTest->store($data);
    }
    public function preTest(CourseTest $courseTest)
    {
        try {


            $preTest = $courseTest
                ->userCourseTests()
                ->where([
                    'user_id' => auth()->user()->id,
                    'test_type' => TestEnum::PRETEST->value
                ])
                ->latest()
                ->firstOrFail();
            if ($preTest->score) {
                return 'anda sudah menyelesaikan pre-test ini';
            }
            return [
                'preTest' => $preTest,
                'questions' => explode(',', $preTest->module_question_id)
            ];
        } catch (\Throwable $e) {

            if ($courseTest->courseTestQuestions) {
                $questions = [];

                foreach ($courseTest->courseTestQuestions as $index => $question) {
                    $total_question = $question;

                    $moduleQuestions = ModuleQuestion::where('module_id', $question->module_id)
                        ->inRandomOrder()
                        ->limit($question->question_count)
                        ->get();

                    $questions = array_merge($questions, $moduleQuestions->toArray());
                }

                $module_question_id = implode(',', array_column($questions, 'id'));
            } else {
                $questions = $this->module->getQuestions($courseTest->course_id, $courseTest->total_question);
                $module_question_id = implode(',', $questions->pluck('id')->toArray());
            }


            $data = [];
            $data['module_question_id'] = $module_question_id;
            $data['user_id'] = auth()->user()->id;
            $data['test_type'] = TestEnum::PRETEST->value;
            $data['course_test_id'] = $courseTest->id;
            $preTest = $this->userCourseTest->store($data);
            return [
                'preTest' => $preTest,
                'questions' => explode(',', $preTest->module_question_id)
            ];
        }
    }
    public function postTest(CourseTest $courseTest)
    {
        try {
            $postTest = $courseTest
                ->userCourseTests()
                ->where([
                    'user_id' => auth()->user()->id,
                    'test_type' => TestEnum::POSTTEST->value
                ])
                ->latest()
                ->firstOrFail();
            if ($postTest->score) {
                return 'already';
            }
            return [
                'postTest' => $postTest,
                'questions' => explode(',', $postTest->module_question_id)
            ];
        } catch (\Throwable $e) {
            $preTest = $courseTest
                ->userCourseTests()
                ->where([
                    'user_id' => auth()->user()->id,
                    'test_type' => TestEnum::PRETEST->value
                ])
                ->whereNotNull('score')
                ->latest()
                ->firstOrFail();
            $data['module_question_id'] = $preTest->module_question_id;
            $data['user_id'] = auth()->user()->id;
            $data['course_test_id'] = $courseTest->id;
            $data['test_type'] = TestEnum::POSTTEST->value;
            $postTest = $this->userCourseTest->store($data);
            return [
                'postTest' => $postTest,
                'questions' => explode(',', $preTest->module_question_id)
            ];
        }
    }
    public function submit(UserCourseTestRequest $request, UserCourseTest $userCourseTest)
    {

        $data = $request->validated();
        $answers = array_map(function ($answer) {
            return $answer ?? 'null';
        }, $data['answer']);
        $questionIds = explode(',', $userCourseTest->module_question_id);
        $moduleQuestions = ModuleQuestion::query()
            ->whereIn('id', $questionIds)
            ->orderByRaw("FIELD(id, '" . implode("', '", $questionIds) . "')")
            ->get('answer');
        $questionAnswers = $moduleQuestions->pluck('answer')->toArray();
        $score = 0;

        foreach ($answers as $index => $userAnswer) {
            if (isset($questionAnswers[$index]) && $userAnswer == $questionAnswers[$index]) {
                $score++;
            }
        }
        $score = count(value: $moduleQuestions) > 0 ? ($score / count($moduleQuestions)) * 100 : 0;
        $userCourseTestData = [
            'score' => $score,
            'answer' => implode(',', $answers),
        ];
        if ($userCourseTest->test_type == TestEnum::PRETEST->value) {
            $userCourseData = [
                'has_pre_test' => true,
            ];
        } else {
            $userCourseData = [
                'has_post_test' => true,
            ];
        }
        $this->userCourseTest->update($userCourseTest->id, $userCourseTestData);
        $this->userCourse->customUpdate($userCourseTest->courseTest->course_id, $userCourseData);
    }

    public function canAccessPostTest($course_test)
    {
        $user_course = $this->userCourse->findByCourseAndUser($course_test->course_id, auth()->user()->id);
        $completionPercentage = CourcePercentaceHelper::getPercentace($user_course);
        if($completionPercentage < 95) {
            throw new \Exception("Kamu harus menyelesaikan semua tugas dan quiz telebih dahulu. Persentase penyelesaian: {$completionPercentage}");
        }

        return true;
    }
}
