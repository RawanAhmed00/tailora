<?php

namespace App\Repo\Interfaces;

use App\Models\City;
use Illuminate\Http\Request;

interface ICityRepository
{
    public function getAll();

    public function paginate(Request $request);

    public function getById(int $id): ?City;

    public function create(array $data): City;

    public function update(int $id, array $data): ?City;

    public function delete(int $id): bool;
}