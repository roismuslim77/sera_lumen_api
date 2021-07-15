<?php

namespace App\Models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="User",
 *     description="User model",
 *     @OA\Xml(
 *         name="User"
 *     )
 * )
 */
class User extends Model
{
    protected $connection = 'mongodb';
	protected $collection = 'users';
    protected $primaryKey = 'id';
    protected $guarded = [];

    /**
     * @OA\Property(
     *     title="ID",
     *     description="Id User",
     *     example="e9e341be-218a-4701-987c-6b5ff7f9b0fd"
     * )
     *
     * @var string
     */
    public $id;

    /**
     * @OA\Property(
     *     title="Name",
     *     description="Name User",
     *     example="sanusi"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *     title="Email",
     *     description="Email User",
     *     example="test@mail.com"
     * )
     *
     * @var string
     */
    public $email;


    /**
     * @OA\Property(
     *     description="Token User",
     *     example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsdW1lbi1qd3QiLCJzdWIiOiI3ZmNjZTIwNy0xOGE2LTRlOWUtYjUwNS02YTZkMmMwZjhjM2QiLCJpYXQiOjE2MjYzMzQ5NzksImV4cCI6MTYyNjMzODU3OX0.ry2RohXZqrmjV6HxI8MYKamBhpA2cWEuvgYfAW9hnIU"
     * )
     *
     * @var string
     */
    public $token;

}