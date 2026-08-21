<?php

namespace App\Services\Class;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Services\Interfaces\IUserService;
use Illuminate\Http\Request;
use App\Repo\Interfaces\UserInterface;


class UserService implements IUserService
{
protected $userRepository;

public function __construct(UserInterface $userRepository)
{
    $this->userRepository = $userRepository;
}
    public function getAllUsers(Request $request){
        return $this->userRepository->getAllUsers(
            $request->input('per_page',10)
        );
    }
    public function getUserById(int $id){
         return $this->userRepository->getUserById($id);

    }
public function createUser(array $data)
{
    return $this->userRepository->createUser($data);
}
   
public function updateUser(int $id, array $data)
{
    
    return $this->userRepository->updateUser($id, $data);
}
    public function deleteUser(int $id){
         return $this->userRepository->deleteUser($id);
      
    }
    public function changeStatus(int $id){
        $user = $this->userRepository->changeStatus($id);
        return $user;
        
    }

}