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
        return $this->__request(__FUNCTION__, compact('id'));
    }

    public function test()
    {
        return $this->__request(__FUNCTION__);
    }

    public function getServerInfo()
    {
        return $this->__request(__FUNCTION__);
    }

    public function timeout($id)
    {
        return $this->__request(__FUNCTION__, compact('id'));
    }

    public function getUserInfoFromCache(int $id)
    {
        return $this->__request(__FUNCTION__, compact('id'));
    }
}
