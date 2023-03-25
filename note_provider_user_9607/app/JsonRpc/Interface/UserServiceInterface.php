<?php

namespace App\JsonRpc\Interface;

interface UserServiceInterface
{
    // 创建用户
    public function createUser(string $name, string $gender);

    // 获取用户信息
    public function getUserInfo(int $id);
    public function test();

	public function getNacosServerInfo();
	public function getServerInfo();	

}
