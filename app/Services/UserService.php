<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\UserRepositpry;

Class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {

    }

    public function getUserById()
    {

    }

    public function createUser()
    {

    }

    public function updateUser()
    {

    }

    public function deleteUser()
    {
        
    }
}





