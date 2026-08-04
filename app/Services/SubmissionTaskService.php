<?php

namespace App\Services;

use App\Http\Requests\SubmissionTaskRequest;
use App\Traits\UploadTrait;

class SubmissionTaskService
{
    use UploadTrait;
    public function handleStoreFile(SubmissionTaskRequest $request)
    {
        return $this->upload('submissionTask', $request->file);
    }

    public function handleRemoveFile(string $file)
    {
        return $this->remove($file);
    }

    public function handleAddPoint(int $points)
    {
        $user = \App\Models\User::find(auth()->user()->id);
        $user->point += $points;
        return $user->save();
    }

    public function handleDecPoint(int $points)
    {
        $user = \App\Models\User::find(auth()->user()->id);
        $user->point -= $points;
        return $user->save();
    }
}
