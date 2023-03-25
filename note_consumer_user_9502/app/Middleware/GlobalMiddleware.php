<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Context\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GlobalMiddleware implements MiddlewareInterface
{
    public function __construct(protected ContainerInterface $container)
    {
        $span = Context::get('tracer.root');
        $response = Context::get(ResponseInterface::class);
        $response = $response->withHeader('Trace-Id', $span->getContext()->getContext()->getTraceId());
        Context::set(ResponseInterface::class, $response);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
}
