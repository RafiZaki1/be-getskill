<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\EventInterface;
use App\Contracts\Interfaces\TransactionInterface;
use App\Contracts\Repositories\BaseRepository;
use App\Enums\InvoiceStatusEnum;
use App\Helpers\IncomeHelper;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionRepository extends BaseRepository implements TransactionInterface
{
    public function __construct(Transaction $transaction)
    {
        $this->model = $transaction;
    }
    /**
     * Method get
     *
     * @return mixed
     */
    public function get(): mixed
    {
        return $this->model->query()->get();
    }
    public function getLatest(): mixed
    {
        return $this->model->query()->latest()->limit(5)->get();
    }
    /**
     * Method getWhere
     *
     * @param array $data [explicite description]
     *
     * @return mixed
     */
    public function getWhere(array $data): mixed
    {
        return $this->model->where($data)->get();
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
        return $this->model->with(['courseVoucher', 'user', 'course.subcategory', 'event'])->findOrFail($id);
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
        return $this->model->create($data);
    }
    /**
     * Method update
     *
     * @param $id $id [explicite description]
     * @param array $data [explicite description]
     *
     * @return mixed
     */
    public function update($id, array $data): mixed
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
        return $this->show($id)->destroy();
    }

    /**
     * customPaginate
     *
     * @param  Request $request
     * @param  int $pagination
     * @return LengthAwarePaginator
     */
    public function customPaginate(Request $request, int $pagination = 10): LengthAwarePaginator
    {
        return $this->model->query()
            ->when($request->name, function ($query) use ($request) {
                $query->whereRelation('user', 'name', 'LIKE', '%' . $request->name . '%');
            })
            ->when($request->created_at, function ($query) use ($request) {
                // Filter berdasarkan tanggal transaksi (field created_at)
                $query->whereDate('created_at', $request->created_at);
            })
            ->when($request->invoice_status, function ($query) use ($request) {
                // Filter berdasarkan status transaksi (invoice_status)
                $query->where('invoice_status', $request->invoice_status);
            })
            ->latest()
            ->fastPaginate($pagination);
    }

    /**
     * search
     *
     * @param  Request $request
     * @return mixed
     */
    public function search(Request $request): mixed
    {
        return $this->model->query()
            ->when($request->name, function ($query) use ($request) {
                $query->whereRelation('user', 'name', 'LIKE', '%' . $request->name . '%');
            })
            ->when($request->created_at, function ($query) use ($request) {
                $query->whereDate('created_at', $request->created_at);
            })
            ->when($request->invoice_status, function ($query) use ($request) {
                $query->where('invoice_status', $request->invoice_status);
            })
            ->get();
    }

    public function countByMonth(): mixed
    {
        
        $currentYear = Carbon::now()->year;
        $transactions = $this->model->query()
            ->where('invoice_status', InvoiceStatusEnum::PAID)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month_number, SUM(paid_amount) as total_paid')
            ->groupBy('month_number')
            ->orderBy('month_number')
            ->get()
            ->keyBy('month_number');

        $result = IncomeHelper::mapMonthlyResult($transactions, 'total_paid');
        $months = IncomeHelper::months();
        $totalYearIncome = array_sum($result);

        return [
            'data' => $result,
            'months' => $months,
            'totalYearIncome' => $totalYearIncome,
        ];
    }

    public function getUnfinishUser(mixed $userId): mixed
    {
        return $this->model->query()->where('user_id', $userId)->whereIn('invoice_status', [InvoiceStatusEnum::PENDING->value, InvoiceStatusEnum::UNPAID->value])->first();
    }

    public function getByUser(mixed $userId, array|string|null $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $allowedStatuses = [
            InvoiceStatusEnum::UNPAID->value,
            InvoiceStatusEnum::PENDING->value,
            InvoiceStatusEnum::PAID->value,
        ];

        $query = $this->model->query()->where('user_id', $userId);

        $query->when($status, function ($q) use ($status, $allowedStatuses) {
            if (is_array($status)) {
                $valid = array_values(array_intersect($allowedStatuses, $status));
                if (!empty($valid)) {
                    $q->whereIn('invoice_status', $valid);
                }
            } elseif (is_string($status) && in_array($status, $allowedStatuses, true)) {
                $q->where('invoice_status', $status);
            }
        });

        return $query->orderByDesc('created_at')->paginate($perPage);
    }


    public function checkPaidByCourseAndUser(mixed $courseId, mixed $userId): mixed
    {
        return $this->model->where('course_id', $courseId)
            ->where('user_id', $userId)
            ->where('invoice_status', InvoiceStatusEnum::PAID->value)
            ->exists();
    }

    public function checkPaidByEventAndUser(mixed $eventId, mixed $userId): mixed
    {
        return $this->model->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->where('invoice_status', InvoiceStatusEnum::PAID->value)
            ->exists();
    }

    public function deleteByCourseVoucher(mixed $courseVoucherId): mixed
    {
        return $this->model->query()->where('course_voucher_id', $courseVoucherId)->where('invoice_status', '!=', InvoiceStatusEnum::PAID->value)->delete();
    }

    public function updateByReference(string $reference, array $data): mixed
    {
        return $this->show($reference)->update($data);
    }

    /**
     * get monthly total by course or all
     * 
     * @param mixed $courseId
     * @param ?int $year
     * @return array
     */
    public function getMonthlyTotalByCourseOrAll(mixed $courseId = null, ?int $year = null): array
    {
        $year = $year ?? Carbon::now()->year;

        $transactions = $this->model->query()
            ->where('invoice_status', InvoiceStatusEnum::PAID->value)
            ->when($courseId, function($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month_number, SUM(amount) as total_amount')
            ->groupBy('month_number')
            ->orderBy('month_number')
            ->get()
            ->keyBy('month_number');

        $result = IncomeHelper::mapMonthlyResult($transactions, 'total_amount');
        $months = IncomeHelper::months();
        $totalYearIncome = array_sum($result);

        return [
            'data' => $result,
            'months' => $months,
            'totalYearIncome' => $totalYearIncome,
        ];
    }
}
