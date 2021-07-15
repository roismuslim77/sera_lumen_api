<?php

namespace App\Http\Controllers;

use App\Helpers\Format;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
    /**
     * @OA\Info(
     *   title="Example API",
     *   version="1.0",
     *   @OA\Contact(
     *     email="support@example.com",
     *     name="Support Team"
     *   )
     * )
     */

    protected function jwtEncode($id, $current_token = null)
    {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $id, // Subject of the token
            'iat' => time(), // Time when JWT was issued. 
            'exp' => time() + 60*60 // Expiration time
        ];

        return JWT::encode($payload, env('JWT_SECRET'));
    }

    protected function jwtDecode($token)
    {
        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);

            $errorStatus = false;
            $message = 'Decode jwt';
            $statusCode = 200;

            return Format::responses($credentials, null, $errorStatus, $message, $statusCode);
        } catch(ExpiredException $e) {
            $errorStatus = true;
            $message = 'Provided token is expired.';
            $statusCode = 200;
            $user = [];

            return Format::responses($user, null, $errorStatus, $message, $statusCode);
        } catch(Exception $e) {
            $errorStatus = true;
            $message = $e->getMessage();
            $statusCode = 200;
            $user = [];

            return Format::responses($user, null, $errorStatus, $message, $statusCode);
        }

        return $credentials;
    }
}
