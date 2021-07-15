<?php

namespace App\Http\Controllers\V1;

use App\Helpers\Format;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepositoryFactory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct()
    {
        $database = request()->route()[2]['database'];
        $userFactory = new UserRepositoryFactory();
        $this->userRepository = $userFactory->make($database);        
    }

    /**
     * @OA\Get(
     *     path="/api/v1/{database}/user",
     *     operationId="user/get",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="database",
     *         in="path",
     *         description="The database parameter in path",
     *         required=true,
     *         example="mongodb",
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
    public function index()
    {
        $users = $this->userRepository->all();
        return Format::responses($users);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/{database}/user/{id}",
     *     operationId="user/show",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="database",
     *         in="path",
     *         description="The database parameter in path",
     *         required=true,
     *         example="mongodb",
     *         @OA\Schema(type="string")
     *     ),
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The id user parameter in path",
     *         required=true,
     *         example="e9e341be-218a-4701-987c-6b5ff7f9b0fd",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns some sample category things",
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
    public function show($id)
    {
        $user = $this->userRepository->find($id);

        if ($user) {
            $errorStatus = false;
            $message = 'Success';
        } else {
            $errorStatus = true;
            $message = 'User not found';
        }

        return Format::responses($user, false, $errorStatus, $message);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/{database}/user",
     *     operationId="user/post",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="database",
     *         in="path",
     *         description="The database parameter in path",
     *         required=true,
     *         example="mongodb",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="The name user parameter in path",
     *         required=true,
     *         example="ahmad",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="The email user parameter in path",
     *         required=true,
     *         example="tes@mail.com",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="The password user parameter in path",
     *         required=true,
     *         example="1234",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns some sample category things",
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
    public function store(Request $request)
    {
        $validatedData = $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        $validatedData['password'] = md5($validatedData['password']);
        $validatedData['token'] = null;
        $validatedData['created_at'] = Carbon::now('+07:00')->format('Y-m-d H:i:s');
        $validatedData['updated_at'] = Carbon::now('+07:00')->format('Y-m-d H:i:s');
        $user = $this->userRepository->store($validatedData);

        if($user){
            $errorStatus = false;
            $message = 'A user has been created.';
            $statusCode = 201;
            unset($user['password']);
        }else{
            $errorStatus = true;
            $message = 'A user failed to create.';
            $statusCode = 200;
        }
        return Format::responses($user, null, $errorStatus, $message, $statusCode);
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/{database}/user/{id}",
     *     operationId="user/update",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="database",
     *         in="path",
     *         description="The database parameter in path",
     *         required=true,
     *         example="mongodb",
     *         @OA\Schema(type="string")
     *     ),
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The id user parameter in path",
     *         required=true,
     *         example="e9e341be-218a-4701-987c-6b5ff7f9b0fd",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="The name user parameter in path",
     *         required=true,
     *         example="sanusi",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="The email user parameter in path",
     *         required=true,
     *         example="tes@mail.com",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="The password user parameter in path",
     *         required=false,
     *         example="",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns some sample category things",
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
    public function update(Request $request, $id)
    {
        $validatedData = $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'password' => 'nullable'
        ]);

        $user = $this->userRepository->find($id);
        
        if($user){
            unset($user['id']);
            unset($user['_id']);
            
            $postData = $user;
            $postData['name'] = $validatedData['name'];
            $postData['email'] = $validatedData['email'];
            $postData['updated_at'] = Carbon::now('+07:00')->format('Y-m-d H:i:s');

            if($request->has('password') && $request->password != ''){
                $postData['password'] = md5($validatedData['password']);
            }

            $user = $this->userRepository->update($postData, $id);
        }

        if($user){
            $errorStatus = false;
            $message = 'A user has been updated.';
            $statusCode = 201;
            unset($user['password']);
        }else{
            $errorStatus = true;
            $message = 'A user failed to update.';
            $statusCode = 200;
        }

        return Format::responses($user, null, $errorStatus, $message, $statusCode);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/{database}/user/{id}",
     *     operationId="user/delete",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="database",
     *         in="path",
     *         description="The database parameter in path",
     *         required=true,
     *         example="mongodb",
     *         @OA\Schema(type="string")
     *     ),
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The id user parameter in path",
     *         required=true,
     *         example="e9e341be-218a-4701-987c-6b5ff7f9b0fd",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns some sample category things",
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
    public function delete($id)
    {
        $user = $this->userRepository->destroy($id);

        if($user){
            $errorStatus = false;
            $message = 'A user has been deleted.';
            $statusCode = 201;
        }else{
            $errorStatus = true;
            $message = 'A user failed to delete.';
            $statusCode = 200;
            $user = [];
        }

        return Format::responses($user, null, $errorStatus, $message, $statusCode);
    }
}