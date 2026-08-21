<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface IAttractionService
{
    public function getAll(int $perPage = 10);

    public function getById(int $id);

    public function create(Request $request);

    public function update(Request $request, int $id);

    public function delete(int $id);
}