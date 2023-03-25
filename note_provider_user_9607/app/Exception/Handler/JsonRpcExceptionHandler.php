<?php

namespace App\Exception\Handler;

use Hyperf\Config\Annotation\Value;
use Hyperf\Contract\ConfigInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\ApplicationContext;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class JsonRpcExceptionHandler extends ExceptionHandler
{
    #[Value('app_name')]
    private string $appName;

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        /*
            $$responseContents 结构如下：

            Array
            (
                [jsonrpc] => 2.0
                [id] =>
                [error] => Array
                    (
                        [code] => -32000
                        [message] => 没有该用户
                        [data] => Array
                            (
                                [class] => RuntimeException
                                [code] => 0
                                [message] => 没有该用户
                            )
                    )

                [context] => Array()
            )
         */
        $responseContents = json_decode($response->getBody()->getContents(), true);
        $errorMessage     = $responseContents['error']['message'];
        if (! empty($responseContents['error'])) {
            $port    = 0;
            $host    = '';
            $config  = ApplicationContext::getContainer()->get(ConfigInterface::class);
            $servers = $config->get('server.servers');

            foreach ($servers as $server) {
                if ($server['name'] == 'jsonrpc-http') {
                    $port = $server['port'];
                    $host = $server['host'];
                    break;
                }
            }
            $responseContents['error']['message'] = $this->appName . '-' . $host .':'. $port . '-' . $errorMessage;
        }
        $data = json_encode($responseContents, JSON_UNESCAPED_UNICODE);

        return $response->withStatus(200)->withBody(new SwooleStream($data));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}