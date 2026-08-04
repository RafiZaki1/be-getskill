<?php

namespace App\Contracts\Interfaces\Course;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\BankModule;
use Illuminate\Http\Request;

interface BankModuleInterface
{
    public function customPaginate(Request $request, int $pagination = 10): LengthAwarePaginator;
    public function store(array $data): BankModule;
    public function find(string $id): BankModule;
    public function update(string $id, array $data = []): BankModule;
    public function delete(string $id): bool;
}
