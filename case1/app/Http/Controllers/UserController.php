<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use App\Services\Interfaces\IUserService;

class UserController extends Controller
{
    protected IUserService $userService;

    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json($this->userService->getAllUsers($request)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(StoreUserRequest $request)
{
    return response()->json(
        $this->userService->createUser($request->validated()),
        201
    );
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json($this->userService->getUserById($id)
        );
    }

    /**
     * Update the specified resource in storage.
     */
 

public function update( int $id, UpdateUserRequest $request)
{
    return response()->json(
        $this->userService->updateUser(
            (int) $id,
            $request->validated()
        )
    );
}

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(string $id)
{
    $this->userService->deleteUser((int) $id);

    return response()->json([
        'message' => 'User deleted successfully'
    ]);
}
     public function changeStatus(int $id)
    {
        return response()->json(
            $this->userService->changeStatus($id)
        );
    }
}
