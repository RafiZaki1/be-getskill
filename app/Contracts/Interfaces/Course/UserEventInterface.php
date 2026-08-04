<?php

namespace App\Contracts\Interfaces\Course;

use App\Contracts\Interfaces\Eloquent\BaseInterface;
use App\Contracts\Interfaces\Eloquent\CustomPaginationInterface;
use App\Contracts\Interfaces\Eloquent\DeleteInterface;
use App\Contracts\Interfaces\Eloquent\ShowInterface;
use App\Contracts\Interfaces\Eloquent\StoreInterface;
use App\Contracts\Interfaces\Eloquent\UpdateInterface;
use Illuminate\Http\Request;

interface UserEventInterface extends CustomPaginationInterface, StoreInterface, DeleteInterface, ShowInterface, UpdateInterface
{
    /**
     * showByCourse
     *
     * @param  mixed $userId
     * @param  mixed $courseId
     * @return mixed
     */
    public function showByEvent($userId, $eventId): mixed;
    /**
     * Method customUpdate
     *
     * @param mixed $eventId [explicit description]
     * @param array $data [explicit description]
     *
     * @return mixed
     */
    public function eventActivity(Request $request, int $pagination = 10): mixed;
    /**
     * Find user event by user, event, and status array
     * @param int $userId
     * @param int $eventId
     * @param array $statuses
     * @return mixed
     */
    public function findByUserAndEvent($userId, $eventId, $statuses);

    /**
     * Get registrations by event
     * @param int $eventId
     * @param int $perPage
     * @return mixed
     */

    public function updateStatusByUserIdAndEventId(mixed $userId, mixed $eventId, array $data): mixed;

    public function getAllWithSearchAndFilter(Request $request, string $slug, int $pagination = 6);

    public function getByPanes(Request $request, string $panes, int $pagination = 6);

    public function getApprovedByEvent(mixed $id, Request $request, int $pagination = 10);

    public function getCountByUserAndAcceptedStatus(mixed $user_id);

    public function checkByEvent($eventId, $user_id = null): mixed;
}
