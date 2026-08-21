<?php
namespace App\Repo\Interfaces;
interface UserInterface
{
    public function getAllUsers(int $perPage = 10);
    public function getUserById(int $id);
    public function createUser(array $data);
    public function updateUser(int $id, array $data);
    public function deleteUser(int $id);
    public function changeStatus(int $id);
}