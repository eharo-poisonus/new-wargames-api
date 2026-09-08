<?php

namespace App\Shared\Infrastructure\Symfony\Middlewares;

use App\Shared\Domain\Exception\MappedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Throwable;

final readonly class ExceptionListenerMiddleware
{
    private const string UNMAPPED_ERROR_CODE = 'APP-000';

    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $targetException = $exception->getPrevious() ?? $exception;

        $errorCode = $this->errorCodeFor($targetException);
        $httpStatus = $this->httpStatusFor($targetException);

        $this->log($targetException, $errorCode, $httpStatus, $event->getRequest());

        $body = [
            'error_code' => $errorCode,
            'message' => $targetException->getMessage()
        ];

        $event->getRequest()->attributes->set('exception', $body);
        $event->setResponse(new JsonResponse($body, $httpStatus));
    }

    private function errorCodeFor(Throwable $exception): string
    {
        return $exception instanceof MappedException ? $exception::errorCode() : self::UNMAPPED_ERROR_CODE;
    }

    private function httpStatusFor(Throwable $exception): int
    {
        return $exception instanceof MappedException ? $exception::httpStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    private function log(Throwable $exception, string $errorCode, int $httpStatus, Request $request): void
    {
        $context = [
            'error_code' => $errorCode,
            'http_status' => $httpStatus,
            'exception_class' => $exception::class,
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),

            'ip' => $request->getClientIp()
        ];

        if ($httpStatus >= Response::HTTP_INTERNAL_SERVER_ERROR) {
            $this->logger->error($exception->getMessage(), [...$context, 'exception' => $exception]);
            return;
        }

        $this->logger->warning($exception->getMessage(), $context);
    }
}
