<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserTheoryExam;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Jobs\TheoryExam\CalculateScoreJob;
use App\Jobs\TheoryExam\StoreSingleAnswerJob;
use App\Contracts\Repositories\IndustryClass\UserTheoryExamRepository;
use App\Contracts\Repositories\IndustryClass\TheoryExamQuestionRepository;

class ProcessStuckTheoryExams extends Command
{
    protected $signature = 'theory-exam:process-stuck';
    protected $description = 'Process stuck theory exams (finished_at not null but batch_id is null)';

    private UserTheoryExamRepository $userTheoryExamRepository;
    private TheoryExamQuestionRepository $theoryExamQuestionRepository;

    public function __construct(
        UserTheoryExamRepository $userTheoryExamRepository,
        TheoryExamQuestionRepository $theoryExamQuestionRepository
    ) {
        parent::__construct();
        $this->userTheoryExamRepository = $userTheoryExamRepository;
        $this->theoryExamQuestionRepository = $theoryExamQuestionRepository;
    }

    public function handle()
    {
        $stuckExams = $this->getStuckExams();

        if ($stuckExams->isEmpty()) {
            $this->info('No stuck exams found.');
            return 0;
        }

        $this->info("Found {$stuckExams->count()} stuck exams. Processing...");

        $stuckExams->each(fn($exam) => $this->processExam($exam));

        $this->info('Done processing stuck exams.');
        return 0;
    }

    private function getStuckExams()
    {
        return UserTheoryExam::whereNotNull('finished_at')
            ->where(fn($q) => $q->whereNull('batch_id')->orWhereNull('score'))
            ->with(['userTheoryExamAnswers', 'user'])
            ->get();
    }

    private function processExam(UserTheoryExam $exam)
    {
        try {
            $jobs = $this->buildJobs($exam);
            $batch = Bus::batch($jobs)->dispatch();

            $this->userTheoryExamRepository->saveBatchId($exam->id, $batch->id);

            $userName = $exam->user->name ?? 'Unknown';
            $this->info("✓ Processed exam {$exam->id} for {$userName}");

            Log::info("Processed stuck exam", [
                'exam_id' => $exam->id,
                'user' => $userName,
                'batch_id' => $batch->id,
            ]);
        } catch (\Throwable $e) {
            $this->error("✗ Failed exam {$exam->id}: {$e->getMessage()}");
            Log::error("Failed processing exam {$exam->id}", ['error' => $e->getMessage()]);
        }
    }

    private function buildJobs(UserTheoryExam $exam): array
    {
        $jobs = $exam->userTheoryExamAnswers
            ->filter(fn($answer) => $answer->theory_exam_question_id)
            ->map(fn($answer) => new StoreSingleAnswerJob([
                'user_theory_exam_id' => $exam->id,
                'theory_exam_question_id' => $answer->theory_exam_question_id,
                'chosen_answer' => $answer->chosen_answer,
                'is_correct' => $answer->is_correct,
            ]))
            ->toArray();

        $jobs[] = new CalculateScoreJob($exam->id);
        
        return $jobs;
    }
}
