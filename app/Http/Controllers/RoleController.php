<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function createRole(Request $request)
    {
        $body=json_decode($request->getContent(),true);
        $name=$body['name'];
        return RoleService::createRole($name);
    }

    public function updateRole(int $roleId,Request $request)
    {
        $body=json_decode($request->getContent(),true);
        $newName=$body['newName'];
        return RoleService::updateRole($roleId,$newName);
    }

    public function deleteRole(int $roleId)
    {
        return RoleService::deleteRole($roleId);
    }

    public function getRole(int $roleId)
    {
        return RoleService::getOneRole($roleId);
    }

    public function getAllRoles()
    {
        return RoleService::getAllRoles();
    }
}
