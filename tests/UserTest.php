<?php

class UserTest extends TestCase
{
    public function testShouldReturnAllUsers()
    {
        $this->get('api/v1/mongodb/user');

        $this->seeStatusCode(200);

        $this->seeJsonStructure([
            'error',
            'data' => ['*' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ]]
        ]);
    }

    public function testShouldReturnAUser()
    {
        $id = '7fcce207-18a6-4e9e-b505-6a6d2c0f8c3d';

        $this->get("api/v1/mongodb/user/$id");

        $this->seeStatusCode(200);

        $this->seeJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ]
        ]);
    }

    public function testShouldCreateAUser()
    {
        $parameters = [
            'name' => 'Unit Test Title',
            'email' => 'test@mail.com',
            'password' => '1222'
        ];

        $this->post('api/v1/mongodb/user', $parameters);

        $this->seeStatusCode(201);

        $this->seeJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ]
        ]);
    }
}
