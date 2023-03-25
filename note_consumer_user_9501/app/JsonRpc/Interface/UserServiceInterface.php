<?php

namespace App\JsonRpc\Interface;

interface UserServiceInterface
{
    public function createUser(string $name, int $gender);

    public function getUserInfo(int $id);

    public function test();
}