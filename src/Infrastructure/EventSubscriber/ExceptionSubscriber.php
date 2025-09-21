<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSubscriber;

use App\UserInterface\Http\Response\ExceptionResponseInterface;
use App\UserInterface\Http\Response\HttpErrorResponse;
use App\UserInterface\Http\Response\MethodNotAllowedResponse;
use App\UserInterface\Http\Response\NotFoundResponse;
use App\UserInterface\Http\Response\ServerErrorResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private const EXCEPTION_RESPONSE_MAP = [
        'default' => ServerErrorResponse::class,
        NotFoundHttpException::class => NotFoundResponse::class,
        HttpException::class => HttpErrorResponse::class,
        MethodNotAllowedHttpException::class => MethodNotAllowedResponse::class,
    ];

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        $throwableClass = get_class($throwable);
        $responseKey = array_key_exists($throwableClass, self::EXCEPTION_RESPONSE_MAP) ? $throwableClass : 'default';
        $responseClass = self::EXCEPTION_RESPONSE_MAP[$responseKey];
        
        $response = is_a($responseClass, ExceptionResponseInterface::class, true) ?  $responseClass::createFromException($throwable) :  new $responseClass;

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

}
