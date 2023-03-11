<?php
namespace App\Services;
use Exception;

class JwtService {
    private function base64url_encode($data):string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64url_decode($data):string {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    public function verifyToken(string $token){
        try {
            $tokenParts = explode('.', $token);
            if (count($tokenParts) < 3) {
                return false;
            }
            $headers_encoded = $tokenParts[0];
            $payload_encoded = $tokenParts[1];
            $userSignature = $tokenParts[2];

            $payload_decodedArray = json_decode($this->base64url_decode($payload_encoded), true);
            //checking time
            $currentTime = time();
            if ($currentTime > $payload_decodedArray['exp']) {
                throw new Exception('Токен истек',402);
            }

            //build the signature to verify
            $key = 'wefjnnjwjef34230r0fewf';
            $signature = hash_hmac('sha256', "$headers_encoded.$payload_encoded", $key, true);
            $signature_encoded = $this->base64url_encode($signature);

            if ($signature_encoded === $userSignature) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage(),
            ],$exception->getCode());
        }
    }

    public function issueAccessToken(array $userData){
        $userTokenInfo=[];
            if (isset($userData['userId']) && isset($userData['username']) && isset($userData['phone'])) {
                $userTokenInfo['userId'] = $userData['userId'];
                $userTokenInfo['username'] = $userData['username'];
                $userTokenInfo['phone']=$userData['phone'];

                $userTokenInfo['iat'] = time();
                $userTokenInfo['exp'] = time() + 7200;
//                7200
                //build the headers
                $headers = ['alg' => 'HS256', 'typ' => 'JWT'];
                $headers_encoded = $this->base64url_encode(json_encode($headers));

                //build the payload
                //$payload = ['sub'=>'1234567890','name'=>'John Doe', 'admin'=>true];
                $payload_encoded = $this->base64url_encode(json_encode($userTokenInfo));

                //build the signature
                $key = 'wefjnnjwjef34230r0fewf';
                $signature = hash_hmac('sha256', "$headers_encoded.$payload_encoded", $key, true);
                $signature_encoded = $this->base64url_encode($signature);

                //build and return the token
                $token = "$headers_encoded.$payload_encoded.$signature_encoded";
                return $token;
            }
    }

    public function issueRefreshToken(array $userData){
        $userTokenInfo=[];
            if (isset($userData['userId']) && isset($userData['username']) && isset($userData['phone'])) {
                $userTokenInfo['userId'] = $userData['userId'];
                $userTokenInfo['phone']=$userData['phone'];
                $userTokenInfo['username'] = $userData['username'];
                $userTokenInfo['iat'] = time();
                $userTokenInfo['exp'] = time() + 604800;
//                604800
                //build the headers
                $headers = ['alg' => 'HS256', 'typ' => 'JWT'];
                $headers_encoded = $this->base64url_encode(json_encode($headers));

                //build the payload
                $payload_encoded = $this->base64url_encode(json_encode($userTokenInfo));

                //build the signature
                $key = 'wefjnnjwjef34230r0fewf';
                $signature = hash_hmac('sha256', "$headers_encoded.$payload_encoded", $key, true);
                $signature_encoded = $this->base64url_encode($signature);

                //build and return the token
                $token = "$headers_encoded.$payload_encoded.$signature_encoded";
                return $token;
            }
    }

    public function refreshTokenPair(string $refreshToken){
        try {
            $isValidToken = $this->verifyToken($refreshToken);
            if ($isValidToken instanceof \Illuminate\Http\JsonResponse) {
                return $isValidToken;
            }
            if ($isValidToken === true) {
                $tokenParts = explode('.', $refreshToken);
                $payload_encoded = $tokenParts[1];

                $userData = json_decode($this->base64url_decode($payload_encoded), true);
//                dd($userData);
                $newAccessToken = '';
                $newRefreshToken = '';
                $newAccessToken = $this->issueAccessToken($userData);
                $newRefreshToken = $this->issueRefreshToken($userData);
                return json_encode([
                    'accessToken' => $newAccessToken,
                    'refreshToken' => $newRefreshToken
                ]);
            } else {
                throw new Exception('Токен невалиден', 406);
            }
        } catch (\Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
            ],$e->getCode());
        }
    }

    public function createNewTokenPair(array $userData){
            $accessToken=$this->issueAccessToken($userData);
            $refreshToken=$this->issueRefreshToken($userData);
            return [
                'accessToken'=>$accessToken,
                'refreshToken'=>$refreshToken
            ];
    }

    public function identifyUsersId(string $token){
        if ($token==null){
            return null;
        }

        $tokenParts=explode('.',$token);
        $payload=$tokenParts[1];
        $decodedTokenPayload=json_decode($this->base64url_decode($payload),true);
        $userId=$decodedTokenPayload['userId'];
        return $userId;
    }
}
