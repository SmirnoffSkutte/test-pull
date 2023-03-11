<?php
namespace App\Services;
use App\Models\User;
use App\Services\JwtService;
use Exception;
use Illuminate\Support\Facades\DB;

class AuthService{
    public function registration(string $username,string $password,string $userPhone){
        try
        {
            $jwt=new JwtService();
            $isOldUser=\App\Models\User::where('username',$username)->first();
            $isOldPhone=\App\Models\User::where('phone',$userPhone)->first();
            if($isOldUser){
                throw new Exception("Пользователь $username уже зарегистрирован",406);
            }
            if($isOldPhone){
                throw new Exception("Телефон $userPhone уже зарегистрирован",406);
            }
            if(strlen($password)<1){
                throw new Exception("Придумайте пароль",406);
            }
            $newUser=new \App\Models\User();
            $newUser->username=$username;
            $newUser->phone=$userPhone;
            $newUser->password=password_hash($password,PASSWORD_DEFAULT);
            $newUser->save();

            $userInfo=\App\Models\User::where('username',$username)->first();
            $data=[
                'username'=>$userInfo->username,
                'phone'=>$userInfo->phone,
                'userId'=>$userInfo->id,
            ];

            DB::table('users_roles')->insert([
                'userId' => $userInfo->id,
                'roleId' => 1
            ]);

            $tokens=$jwt->createNewTokenPair($data);
            $responce=[
                'user'=>[
                    'username'=>$userInfo->username,
                    'phone'=>$userInfo->phone,
                    'userId'=>$userInfo->id,
                ],
                'tokens'=>$tokens,
            ];
            return json_encode($responce);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'error' => $e->getMessage(),
            ],$e->getCode());
        }
    }

    public function login(string $username,string $password){
        $userData=$this->validateUser($username,$password);
        if($userData instanceof \Illuminate\Http\JsonResponse){
            return $userData;
        }
        $jwt=new JwtService();
        $tokens=$jwt->createNewTokenPair($userData);

        $userInfo=[
            'user'=>$userData,
            'tokens'=>$tokens
        ];
        return json_encode($userInfo);
    }

    public function validateUser(string $username,string $password){
        try
        {
        $user=User::where('username',$username)->first();
        if(!$user){
            throw new \Exception("Нет пользователя с логином $username",404);
        }
        $passwordDb=$user->password;
        if(!password_verify($password,$passwordDb)){
            throw new \Exception("Пароль неверен",406);
        }
        $data=[
            'username'=>$user->username,
            'phone'=>$user->phone,
            'userId'=>$user->id
        ];
        return $data;
        }
        catch (\Exception $e)
        {
            return response()->json([
                'error' => $e->getMessage(),
            ],$e->getCode());
        }
    }
}
