<?php

namespace App\Contracts\Interfaces\Course;

use App\Contracts\Interfaces\Eloquent\DeleteInterface;
use App\Contracts\Interfaces\Eloquent\GetWhereInterface;
use App\Contracts\Interfaces\Eloquent\ShowInterface;
use App\Contracts\Interfaces\Eloquent\StoreInterface;
use App\Contracts\Interfaces\Eloquent\UpdateInterface;
use Illuminate\Http\Request;

interface SubmissionTaskInterface extends GetWhereInterface, StoreInterface, UpdateInterface, ShowInterface, DeleteInterface
{
    public function getWhereSearch(array $data, Request $request);
    public function getByUserAndModuleTask(mixed $user_id, mixed $module_task_id);
}