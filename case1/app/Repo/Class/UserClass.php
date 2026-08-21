<?php
namespace App\Repo\Class;
use App\Repo\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserClass implements UserInterface
{
    
    public function getAllUsers(int $perPage = 10)
    {
        return User::paginate($perPage);
    }
    public function getUserById(int $id)
    {
        return User::findOrFail($id);
    }
    public function createUser(array $data)
{
    $data['password'] = Hash::make($data['password']);

    return User::create($data);
}
public function updateUser(int $id, array $data)
{
    $user = User::findOrFail($id);

    if (isset($data['password'])) {
        $data['password'] = Hash::make($data['password']);
    }

    $user->update($data);

    return $user;
}
   public function deleteUser(int $id)
{
    $user = User::findOrFail($id);

    $user->delete();

    return true;
}
    public function changeStatus(int $id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();
        return $user;
}
}