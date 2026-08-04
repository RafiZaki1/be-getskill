<?php

namespace App\Contracts\Repositories\Course;

use App\Contracts\Interfaces\Course\BankModuleInterface;
use App\Contracts\Interfaces\Eloquent\BaseInterface;
use App\Models\BankModule;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Contracts\Repositories\BaseRepository;

class BankModuleRepository extends BaseRepository implements BankModuleInterface
{
    public function __construct(BankModule $bankModule)
    {
        $this->model = $bankModule;
    }

    /**
     * pagination for bank module list.
     */
    public function customPaginate(Request $request, int $pagination = 10): LengthAwarePaginator
    {
        return $this->model->query()->with('items')
            ->when($request->search, function ($q, $v) {
                $q->where(function ($qq) use ($v) {
                    $qq->where('class_level', 'LIKE', "%$v%")
                        ->orWhere('semester', 'LIKE', "%$v%")
                        ->orWhere('division_id', 'LIKE', "%$v%")
                        ->orWhere('school_year_id', 'LIKE', "%$v%");
                });
            })
            ->when($request->division_id, fn($q, $v) => $q->where('division_id', $v))
            ->when($request->school_year_id, fn($q, $v) => $q->where('school_year_id', $v))
            ->when($request->class_level, fn($q, $v) => $q->where('class_level', $v))
            ->when($request->semester, fn($q, $v) => $q->where('semester', $v))
            ->latest()
            ->paginate($pagination);
    }

    public function store(array $data): BankModule
    {
        $bankModule = $this->model->create($data);
        
        if (isset($data['modules'])) {
            foreach ($data['modules'] as $moduleId) {
                $bankModule->items()->firstOrCreate(['module_id' => $moduleId]);
            }
        }
        
        return $bankModule->load('items');
    }

    public function find(string $id): BankModule
    {
        return $this->model->with('items')->findOrFail($id);
    }

    public function update(string $id, array $data = []): BankModule
    {
        $bankModule = $this->model->findOrFail($id);
        $bankModule->update($data);
        
        if (isset($data['modules'])) {
            $bankModule->items()->whereNotIn('module_id', $data['modules'])->delete();
            
            foreach ($data['modules'] as $moduleId) {
                $bankModule->items()->firstOrCreate(['module_id' => $moduleId]);
            }
        }
        
        return $bankModule->load('items');
    }

    public function delete(string $id): bool
    {
        return $this->model->query()->findOrFail($id)->delete();       
    }
}
