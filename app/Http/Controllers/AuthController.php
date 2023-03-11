<?php

namespace App\Http\Controllers;
use App\Services\AuthService;
use App\Services\JwtService;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    public function registration(Request $request){
        $body=json_decode($request->getContent(),true);
        $username=$body['username'];
        $phone=$body['phone'];
        $password=$body['password'];
        $authService=new AuthService();
        $newUser=$authService->registration($username,$password,$phone);
        return $newUser;
    }

    public function login(Request $request){
        $body=json_decode($request->getContent(),true);
        $username=$body['username'];
        $password=$body['password'];
        $authService=new AuthService();
        $user=$authService->login($username,$password);
        return $user;
    }

    public function refreshTokens(Request $request){
        $body=json_decode($request->getContent(),true);
        $refreshToken=$body['refreshToken'];
        $jwtService=new JwtService();
        $newTokens=$jwtService->refreshTokenPair($refreshToken);
        return $newTokens;
    }
}
