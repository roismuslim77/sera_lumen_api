<?php

namespace App\Repositories;

interface BaseRepositoryInterface 
{
    public function all();

    public function find(string $id);

    public function store(array $data);

    public function update(array $data, string $id);
    
    public function destroy(string $id);

    public function login(array $data);
}