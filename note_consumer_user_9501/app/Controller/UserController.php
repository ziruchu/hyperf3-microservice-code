<?php

namespace App\Controller;

use App\Constants\ResponseCode;
use App\Exception\ApiException;
use App\JsonRpc\Interface\UserServiceInterface;
use App\Tools\ResponseTool;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;

#[Controller]
class UserController extends AbstractController
{
    #[Inject]
    protected UserServiceInterface $userService;

    // 添加用户
    #[PostMapping('/users/store')]
    public function store()
    {
        $name   = (string)$this->request->input('name', '');
        $gender = (int)$this->request->input('gender', 0);

        $user = $this->userService->createUser($name, $gender);
        if ($user['code'] != ResponseCode::SUCCESS) {
            throw new \RuntimeException($user['message']);
        }

        return ResponseTool::success($user['data']);
    }

    // 获取用户信息
    #[GetMapping('/users/show')]
    public function getUserInfo()
    {
        $id = (int) $this->request->input('id');
        $user = $this->userService->getUserInfo($id);

        if ($user['code'] != ResponseCode::SUCCESS) {
//            throw new \RuntimeException($user['message']);
            throw new ApiException($user['message'], 400);
        }

        return ResponseTool::success($user['data']);
    }

    #[GetMapping('/users/test')]
    public function test()
    {
        return ResponseTool::success($this->userService->test());
    }

}