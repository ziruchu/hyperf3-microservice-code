<?php

namespace App\JsonRpc\Service;

use App\JsonRpc\Interface\UserServiceInterface;
use App\Model\User;
use App\Tools\ResponseTool;
use Hyperf\Contract\ConfigInterface;
use Hyperf\RpcServer\Annotation\RpcService;
use Hyperf\ServiceGovernanceConsul\ConsulAgent;
use Hyperf\ServiceGovernanceNacos\Client;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Codec\Json;


#[RpcService(name: "UserService", protocol: "jsonrpc-http", server: "jsonrpc-http", publishTo: "nacos")]
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

    public function getNacosServerInfo()
    {
        $config = ApplicationContext::getContainer()->get(ConfigInterface::class);
        $groupName = $config->get('services.drivers.nacos.group_name');
        $namespaceId = $config->get('services.drivers.nacos.namespace_id');

        $client = ApplicationContext::getContainer()->get(Client::class);
        $services = Json::decode((string) $client->service->list(1, 10, $groupName, $namespaceId)->getBody());
        $details = [];
        if (!empty($services['doms'])) {
            $optional = [
                'groupName'   => $groupName,
                'namespaceId' => $namespaceId,
            ];
            foreach ($services['doms'] as $service) {
                $details[] = Json::decode((string) $client->instance->list($service, $optional)->getBody());
            }
        }

        return ResponseTool::success($details);
    }

    public function getServerInfo()
    {
        $port = 0;
        $config = ApplicationContext::getContainer()->get(ConfigInterface::class);
        $servers = $config->get('server.servers');
        $appName = $config->get('app_name');
        foreach ($servers as $k => $server) {
            if ($server['name'] == 'jsonrpc-http') {
                $port = $server['port'];
                break;
            }
        }

        return ResponseTool::success([
            'appName'=>$appName,
            'port'=>$port,
        ]);
    }


public function timeout($id)
{
    try {
        // 暂停1秒模拟业务耗时
        if ($id > 0) {
            sleep(1);
        }
    } catch (\Exception $e) {
        throw new \RuntimeException($e->getMessage());
    }
    echo '测试熔断' . PHP_EOL;
    return ResponseTool::success([]);
}


}
