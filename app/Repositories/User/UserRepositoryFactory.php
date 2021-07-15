<?php
namespace App\Repositories\User;
use RuntimeException;

class UserRepositoryFactory 
{
    const FIRESTORE = 'firestore';
    const MONGO = 'mongodb';

    public function make($database)
    {
        switch ($database) {
            case self::FIRESTORE:
                return new UserFirestore;
            case self::MONGO:
                return new UserMongo;
            default:
                throw new RuntimeException('Unknown Repository: ' . $database);
        }
    }
}

