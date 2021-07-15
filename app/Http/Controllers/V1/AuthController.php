<?php

namespace App\Http\Controllers\V1;

use App\Helpers\Format;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepositoryFactory;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $userRepository;

    public function __construct()
    {
        $database = 'mongodb';
        $userFactory = new UserRepositoryFactory();
        $this->userRepository = $userFactory->make($database);        
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     operationId="auth/login",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="The email parameter in path",
     *         required=true,
     *         example="test@mail.com",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="The password parameter in path",
     *         required=true,
     *         example="1234",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns some sample user things",
     *         @OA\JsonContent(
     *          @OA\Property(
     *              property="error",
     *              description="List of users",
     *              example=false,
     *              type="boolean"
     *          ),
     *          @OA\Property(
     *              property="message",
     *              description="List of users",
     *              example="Success",
     *              type="string"
     *          ),
     *          @OA\Property(
     *              property="data",
     *              description="List of users",
     *              ref="#/components/schemas/User"
     *          )
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     */
    public function login(Request $request)
    {
        $validatedData = $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        //generate jwt
        $user = $this->userRepository->login($validatedData);
    
        if($user){
            $id = $user['id'];
            $token = $this->jwtEncode($id);

            //update token
            $postData['token'] = $token;
            $postData['updated_at'] = Carbon::now('+07:00')->format('Y-m-d H:i:s');
            $user = $this->userRepository->update($postData, $id);
            unset($user['password']);

            $errorStatus = false;
            $message = 'Login Success.';
            $statusCode = 200;
        }else{
            $errorStatus = true;
            $message = 'Failed Login. Check email and password.';
            $statusCode = 200;
            $user = [];
        }

        return Format::responses($user, null, $errorStatus, $message, $statusCode);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/check",
     *     operationId="auth/check",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         description="The token parameter in path",
     *         required=true,
     *         example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsdW1lbi1qd3QiLCJzdWIiOiI3ZmNjZTIwNy0xOGE2LTRlOWUtYjUwNS02YTZkMmMwZjhjM2QiLCJpYXQiOjE2MjYzMzQ5NzksImV4cCI6MTYyNjMzODU3OX0.ry2RohXZqrmjV6HxI8MYKamBhpA2cWEuvgYfAW9hnIU",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns some sample user things",
     *         @OA\JsonContent(
     *          @OA\Property(
     *              property="error",
     *              description="List of users",
     *              example=false,
     *              type="boolean"
     *          ),
     *          @OA\Property(
     *              property="message",
     *              description="List of users",
     *              example="Success",
     *              type="string"
     *          ),
     *          @OA\Property(
     *              property="data",
     *              description="List of users",
     *              ref="#/components/schemas/User"
     *          )
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     */
    public function check(Request $request)
    {
        //get token request
        $token = $request->header('token');
        $jwt_decode = $this->jwtDecode($token);

        if(!$jwt_decode->original['error']){
            $jwt_decode = $jwt_decode->original['data'];
            $id = $jwt_decode->sub ?? '';
            $user = $this->userRepository->find($id);
            unset($user['password']);

            //cek current token
            if($token != $user['token']){
                $errorStatus = true;
                $message = 'Provided token is expired.';
                $statusCode = 200;
                $user = [];
            }else{
                $errorStatus = false;
                $message = 'Token valid.';
                $statusCode = 200;
            }
        }else{
            $errorStatus = true;
            $message = $jwt_decode->original['message'];
            $statusCode = 200;
            $user = [];
        }

        return Format::responses($user, null, $errorStatus, $message, $statusCode);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     operationId="auth/logout",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         description="The token parameter in path",
     *         required=true,
     *         example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsdW1lbi1qd3QiLCJzdWIiOiI3ZmNjZTIwNy0xOGE2LTRlOWUtYjUwNS02YTZkMmMwZjhjM2QiLCJpYXQiOjE2MjYzMzQ5NzksImV4cCI6MTYyNjMzODU3OX0.ry2RohXZqrmjV6HxI8MYKamBhpA2cWEuvgYfAW9hnIU",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns some sample user things",
     *         @OA\JsonContent(
     *          @OA\Property(
     *              property="error",
     *              description="List of users",
     *              example=false,
     *              type="boolean"
     *          ),
     *          @OA\Property(
     *              property="message",
     *              description="List of users",
     *              example="Success",
     *              type="string"
     *          ),
     *          @OA\Property(
     *              property="data",
     *              description="List of users",
     *              example=null
     *          )
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     */
    public function logout(Request $request){
        $check = $this->check($request);

        if(!$check->original['error']){
            //get id user
            $data = $check->original['data'];
            $id = $data['id'];

            //delete token in db
            //update token
            $postData['token'] = null;
            $postData['updated_at'] = Carbon::now('+07:00')->format('Y-m-d H:i:s');
            $this->userRepository->update($postData, $id);
            
            $errorStatus = false;
            $message = 'Destroy token.';
            $statusCode = 200;
            return Format::responses([], null, $errorStatus, $message, $statusCode);
        }

        return $check;
        print_r($check);die;
    }
}
