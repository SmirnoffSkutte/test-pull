<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('/registration','registration');
    Route::post('/login','login');
    Route::post('/refresh','refreshTokens');
});

Route::controller(RoleController::class)->middleware('adminOnly')->prefix('/roles')->group(function () {
    Route::get('/all','getAllRoles');
    Route::get('/one/{roleId}','getRole');
    Route::post('/create','createRole');
    Route::put('/update/{roleId}','updateRole');
    Route::delete('/delete/{roleId}','deleteRole');
});

Route::controller(UserController::class)->middleware('adminOnly')->prefix('/users')->group(function () {
    Route::get('/all','getAllUsers');
    Route::get('/one/{userId}','getUser');
    Route::post('/add-role/{userId}/{newRoleId}','addUserRole');
    Route::delete('/delete-role/{userId}/{newRoleId}','deleteUserRole');
    Route::delete('/delete/{userId}','deleteUser');
});

Route::get('/test',function (){
   return 'hi from api';
});
