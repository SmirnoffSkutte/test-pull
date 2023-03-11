<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function addUserRole(int $userId,int $newRoleId)
    {
        return UserService::addUserRole($userId,$newRoleId);
    }

    public function deleteUserRole(int $userId,int $newRoleId)
    {
        return UserService::deleteUserRole($userId,$newRoleId);
    }

    public function deleteUser(int $userId)
    {
        return UserService::deleteUser($userId);
    }

    public function getUser(int $userId)
    {
        return UserService::getOneUser($userId);
    }

    public function getAllUsers()
    {
        return UserService::getAllUsers();
    }
}
