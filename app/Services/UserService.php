<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    public static function deleteUser(int $id)
    {
        $deleted = DB::table('users')->where('id', '=', $id)->delete();
        if($deleted===0){
            return response()->json([
                'error' => "Пользователя с айди $id не найдено",
            ],404);
        }
        return $deleted;
    }

    public static function addUserRole(int $id,int $newRoleId)
    {
        $isRole=DB::table('roles')->find($newRoleId);
        if(!$isRole){
            return response()->json([
                'error' => "Роли с таким id:$newRoleId не существует",
            ],404);
        }

        $isUser=DB::table('users')->find($id);
        if(!$isUser){
            return response()->json([
                'error' => "Пользователя с таким id:$id не существует",
            ],404);
        }

        $isOldRole=DB::table('users_roles')
            ->where('userId','=',$id)
            ->where('roleId','=',$newRoleId)
            ->get();
        if(count($isOldRole)>0){
            return response()->json([
                'error' => "У пользователя с id:$id есть роль с id:$newRoleId",
            ],400);
        }

        DB::table('users_roles')->insert([
            'userId' => $id,
            'roleId' => $newRoleId
        ]);
    }

    public static function deleteUserRole(int $id,int $roleId)
    {
        $isRole=DB::table('roles')->find($roleId);
        if(!$isRole){
            return response()->json([
                'error' => "Роли с таким id:$roleId не существует",
            ],404);
        }

        $isUser=DB::table('users')->find($id);
        if(!$isUser){
            return response()->json([
                'error' => "Пользователя с таким id:$id не существует",
            ],404);
        }

        $deleted=DB::table('users_roles')
            ->where('userId', '=', $id)
            ->where('roleId', '=', $roleId)
            ->delete();
        if($deleted===0){
            return response()->json([
                'error' => "У пользователя с таким id:$id нет роли с id:$roleId",
            ],404);
        }

        return $deleted;
    }

    public static function getOneUser(int $id)
    {
        $data=DB::table('users')->find($id);
        if(!$data){
            return response()->json([
                'error' => "Пользователя с айди $id не найдено",
            ],404);
        }
        return $data;
    }

    public static function getAllUsers()
    {
        return DB::table('users')->get();
    }
}
