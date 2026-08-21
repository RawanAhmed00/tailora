<?php

namespace App\Services\Interfaces;
use Illuminate\Http\Request;


interface IUserService
{
    public function getAllUsers(Request $request);
    public function getUserById(int $id);
    public function createUser(array $data);
    public function updateUser(int $id,array $data);
    public function deleteUser(int $id);
    public function changeStatus( int $id);

}