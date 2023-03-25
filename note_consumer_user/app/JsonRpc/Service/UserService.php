<?php

namespace App\JsonRpc\Service;

use App\JsonRpc\Interface\UserServiceInterface;
use Hyperf\RpcClient\AbstractServiceClient;

class UserService extends AbstractServiceClient implements UserServiceInterface
{
    // 定义对应服务提供者的服务名称
    protected string $serviceName = 'UserService';

    // 定义对应服务提供者的服务协议
    protected string $protocol = 'jsonrpc-http';

    public function createUser(string $name, int $gender)
    {
        return $this->__request(__FUNCTION__, compact('name', 'gender'));
    }

    public function getUserInfo(int $id)
    {
        echo '111111111111111';
        return $this->__request(__FUNCTION__, compact('id'));
    }
}