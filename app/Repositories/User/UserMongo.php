<?php

namespace App\Repositories\User;

use App\Helpers\Format;
use App\Models\Mongo\User;
use Ramsey\Uuid\Uuid;

class UserMongo implements UserRepositoryInterface
{

    public function all() : array
    {
        $users = User::all()->toArray();
        return $users;
    }

    public function find($id)
    {
        $user = User::where('id', $id)
            ->get()->toArray();
        return $user[0] ?? [];
    }

    public function store($data)
    {
        $uuid = Uuid::uuid4();
        $id = $uuid->toString();
        $requestData = array_merge(['id' => $id], $data);

        $store = User::create($requestData)->toArray();
        return $store;        
    }

    public function update(array $data, string $id)
    {
        User::where('id', $id)
            ->update($data);

        return $this->find($id);
    }

    public function destroy($id) 
    {
        return User::destroy($id);
    }

    public function login($data)
    {
        $user = User::where('email', $data['email'])
            ->first();

        if($user){
            //validasi password
            if(md5($data['password']) == $user->password){
                return $user;
            }
        }
        return null;
    }
}