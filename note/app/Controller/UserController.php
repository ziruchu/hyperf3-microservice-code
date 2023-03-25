<?php

namespace App\Controller;

use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller]
class UserController
{
    #[Inject]
    protected UserService $userService;

    #[GetMapping('/users/info')]
    public function getUserInfo()
    {
        return $this->userService->getUserInfo();
    }
}