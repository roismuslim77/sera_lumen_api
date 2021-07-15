<?php

namespace App\Repositories\User;

use Kreait\Laravel\Firebase\Facades\Firebase;

class UserFirestore implements UserRepositoryInterface
{
    protected $userCollection;

    public function __construct()
    {
        $this->userCollection = Firebase::firestore()
            ->database()
            ->collection('users');
    }

    public function all()
    {
        $documents = $this->userCollection->documents();
        $data = [];
        foreach ($documents as $value) {
            $row['id'] = $value->id();
            $user = array_merge($row, $value->data());
            $data[] = $user;
        }

        return $data;
    }

    public function find($id)
    {
        $document = $this->userCollection->document($id);
        $snapshot = $document->snapshot();

        if ($snapshot->exists()) {

            $item['id'] = $document->id();
            $post = array_merge($item, $snapshot->data());

            return $post;

        } else {
            return null;
        }
    }

    public function store($data)
    {
        $user = $this->userCollection->add($data);

        if ($user->id()) {
            return $this->find($user->id());
        } else {
            return null;
        }     
    }

    public function update(array $data, string $id)
    {
        $this->userCollection
            ->document($id)
            ->set($data);

        return $this->find($id);
    }

    public function destroy($id) 
    {
        $delete = $this->userCollection->document($id)->delete();
        
        return $delete ? true : false;
    }

    public function login($data)
    {
        return null;
    }
}