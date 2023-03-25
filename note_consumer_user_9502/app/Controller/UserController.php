<?php
declare(strict_types=1);

namespace App\Controller;

use _PHPStan_e0e4f009c\Symfony\Component\String\Exception\RuntimeException;
use App\Constants\ResponseCode;
use App\JsonRpc\Interface\UserServiceInterface;
use App\Model\User;
use App\Tools\ResponseTool;
use Hyperf\CircuitBreaker\Annotation\CircuitBreaker;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Metric\Annotation\Counter;
use Hyperf\RateLimit\Annotation\RateLimit;
use Hyperf\RateLimit\Exception\RateLimitException;
use Hyperf\Utils\ApplicationContext;

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
    public function getUserInfo($id)
    {
        $id = (int) $this->request->input('id');
        $user = $this->userService->getUserInfo($id);

        if ($user['code'] != ResponseCode::SUCCESS) {
            throw new \RuntimeException($user['message']);
        }

        return ResponseTool::success($user['data']);
    }

    #[GetMapping('/users/test')]
    #[RateLimit(create: 1, consume: 1, waitTimeout: 1, limitCallback: [UserController::class, 'limitCallback'], key: [UserController::class, 'getUserId'])]
    public function test()
    {
        return ResponseTool::success($this->userService->test());
    }

    #[GetMapping('/users/getServerInfo')]
    public function getServerInfo()
    {
        return ResponseTool::success($this->userService->getServerInfo());
    }


    public static function limitCallback(float $seconds, ProceedingJoinPoint $proceedingJoinPoint)
    {
        throw new RateLimitException('请求过于频繁，请稍后再试！！！', 500);
    }

    // 针对用户进行显示
    public static function getUserId(ProceedingJoinPoint $proceedingJoinPoint)
    {

        $request = ApplicationContext::getContainer()->get(RequestInterface::class);
        echo $request->input('user_id') . PHP_EOL;

        // 业务逻辑处理
    }

    #[GetMapping('/users/testCircuitBreaker')]
    #[CircuitBreaker(options: ['timeout' => 0.05], failCounter: 1, successCounter: 1, fallback: "app\Controller\UserController::testCircuitBreakerFallback")]
    public function testCircuitBreaker()
    {
        $id     = (int)$this->request->input('id');
        $result = $this->userService->timeout($id);
        if ($result['code'] != ResponseCode::SUCCESS) {
            throw new RuntimeException($result['message']);
        }

        return ResponseTool::success($result['data']);
    }

    #[GetMapping('/users/testCircuitBreakerFallback')]
    public function testCircuitBreakerFallback()
    {
        return ResponseTool::error(message: 'The server is busy, please try again later ...');
    }


    #[GetMapping('/users/getUserInfoFromCache')]
    public function getUserInfoFromCache()
    {
        $id = (int) $this->request->input('id');


        $result = $this->userService->getUserInfoFromCache($id);
        if ($result['code'] != ResponseCode::SUCCESS) {
            throw new  \RuntimeException($result['message']);
        }

        return ResponseTool::success($result['data']);
    }


    #[Counter(name:"counter_test")]
    #[GetMapping('/users/conutTest')]
    public function conutTest()
    {
        $result['data'] = ['message'=>'this is a test'];
        return ResponseTool::success($result['data']);
    }
}
