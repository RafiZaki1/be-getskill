<?php

namespace App\Contracts\Repositories\Course;

use App\Contracts\Interfaces\Course\CategoryInterface;
use App\Contracts\Interfaces\Course\CourseInterface;
use App\Contracts\Interfaces\Course\CourseVoucherInterface;
use App\Contracts\Interfaces\Eloquent\BaseInterface;
use App\Contracts\Repositories\BaseRepository;
use App\Enums\InvoiceStatusEnum;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseVoucher;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseVoucherRepository extends BaseRepository implements CourseVoucherInterface
{
    /**
     * Method __construct
     *
     * @param CourseVoucher $CourseVoucher [explicite description]
     *
     * @return void
     */
    public function __construct(CourseVoucher $courseVoucher)
    {
        $this->model = $courseVoucher;
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
        return $this->model->query()->withCount(['transactions' => function ($query) {
            $query->where('invoice_status', InvoiceStatusEnum::PAID);
        }])
        ->where($data)
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function getByCode($code): mixed
    {
        return $this->model->query()
            ->withCount(['transactions' => function ($query) {
                $query->where('invoice_status', InvoiceStatusEnum::PAID);
            }])
            ->where('code', $code)
            ->first();
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
        return $this->model->query()->create($data);
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
        return $this->model->query()->with('transactions')->findOrFail($id);
    }
    /**
     * Method update
     *
     * @param mixed $id [explicite description]
     * @param array $data [explicite description]
     *
     * @return mixed
     */
    public function update(mixed $id, array $data): mixed
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
        return $this->show($id)->delete();
    }
}
