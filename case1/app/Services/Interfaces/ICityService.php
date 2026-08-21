<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface ICityService
{
    public function getAll();

    public function getById(int $id);

    public function create(Request $request);

    public function update(int $id, Request $request);

    public function delete(int $id);
}