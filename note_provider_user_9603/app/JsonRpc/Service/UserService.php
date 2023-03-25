<?php

namespace App\JsonRpc\Service;

use App\JsonRpc\Interface\UserServiceInterface;
use App\Model\User;
use App\Tools\ResponseTool;
use Hyperf\RpcServer\Annotation\RpcService;
use Hyperf\ServiceGovernanceConsul\ConsulAgent;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Contract\ConfigInterface;

#[RpcService(name: "UserService", protocol: "jsonrpc-http", server: "jsonrpc-http", publishTo: "consul")]
class UserService implements UserServiceInterface
{
    public function createUser(string $name, string $gender)
    {
        if (empty($name)) {
            throw new \RuntimeException('用户名不能为空');
        }

        $user = User::query()->create([
            'name'   => $name,
            'gender' => $gender,
        ]);

        return  $user ? ResponseTool::success() : ResponseTool::error('创建用户失败');
    }

    public function getUserInfo(int $id)
    {
        $user = User::query()->find($id);
        if (empty($user)) {
            throw new \RuntimeException('没有该用户');
        }

        return ResponseTool::success($user->toArray());
    }

    public function test()
    {
        $host = '';

        $config = ApplicationContext::getContainer()->get(ConfigInterface::class);
        $servers = $config->get('server.servers');
        $appName = $config->get('app_name');

        foreach ($servers as $server) {
            if ($server['name'] == 'jsonrpc-http') {
                $host = $server['host'];
                break;
            }
        }

        return ResponseTool::success([
            'app_name' => $appName,
            'host'     => $host,
        ]);

    }
}
