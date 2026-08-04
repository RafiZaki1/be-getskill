<?php

namespace App\Contracts\Interfaces;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Contracts\Interfaces\Eloquent\BaseInterface;
use App\Contracts\Interfaces\Eloquent\CustomPaginationInterface;
use App\Contracts\Interfaces\Eloquent\ShowInterface;
use App\Contracts\Interfaces\Eloquent\StoreInterface;
use App\Contracts\Interfaces\Eloquent\DeleteInterface;
use App\Contracts\Interfaces\Eloquent\GetInterface;
use App\Contracts\Interfaces\Eloquent\GetWhereInterface;
use App\Contracts\Interfaces\Eloquent\SearchInterface;
use App\Contracts\Interfaces\Eloquent\UpdateInterface;

interface TransactionInterface extends GetInterface, GetWhereInterface, StoreInterface, UpdateInterface, DeleteInterface, ShowInterface, CustomPaginationInterface, SearchInterface
{
    public function countByMonth(): mixed;
    /**
     * Method getLatest
     *
     * @return mixed
     */
    public function getLatest(): mixed;
    public function getUnfinishUser(mixed $userId): mixed;
    public function getByUser(mixed $userId, array|string|null $status = null, int $perPage = 15): mixed;
    public function checkPaidByCourseAndUser(mixed $courseId, mixed $userId): mixed;
    public function checkPaidByEventAndUser(mixed $eventId, mixed $userId): mixed;
    public function deleteByCourseVoucher(mixed $courseVoucherId): mixed;
    public function updateByReference(string $reference, array $data): mixed;

    /**
     * contract get monthly total by course or all
     * 
     * @param mixed $courseId
     * @param ?int $year
     * @return array
     */
    public function getMonthlyTotalByCourseOrAll(mixed $courseId = null, ?int $year = null): array;
}
