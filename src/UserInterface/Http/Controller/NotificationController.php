<?php

declare(strict_types=1);

namespace App\UserInterface\Http\Controller;

use App\Application\Command\SendNotificationCommand;
use App\Application\Common\NotificationListDTO;
use App\Application\Service\NotificationListService;
use App\Domain\Entity\Notification;
use App\Domain\Enum\NotificationStatus;
use App\UserInterface\Documentation\Response\PaginatorResponseDoc;
use App\UserInterface\Documentation\Response\ServerErrorResponseDoc;
use App\UserInterface\Documentation\Response\SuccessResponseDoc;
use App\UserInterface\Documentation\Response\ValidationErrorResponseDoc;
use App\UserInterface\Http\Response\SuccessResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

#[OA\Tag('Notification')]
#[ServerErrorResponseDoc]
class NotificationController extends AbstractController
{
    #[OA\Post(
        summary: 'Send notification',
        description: 'Adds new notification to the queue'
    )]
    #[SuccessResponseDoc(dataExample: ['notification_status' => NotificationStatus::PENDING->value])]
    #[ValidationErrorResponseDoc]
    #[Route('/notification', name: 'send_notification', methods: ['POST'])]
    public function sendNotification(
        #[MapRequestPayload] SendNotificationCommand $command, 
        MessageBusInterface $commandBus
    ): Response
    {
        $commandBus->dispatch($command);

        return new SuccessResponse(['notification_status' => NotificationStatus::PENDING->value]);
    }


    #[OA\Get(
        summary: 'List notifications',
        description: 'Returns paginated notification list'
    )]
    #[PaginatorResponseDoc(Notification::class)]
    #[Route('/notification', name: 'list_notifications', methods: ['GET'])]
    public function listNotifications(
        NotificationListService $notificationListService, 
        #[MapQueryString] NotificationListDTO $notificationListDTO
    ): Response
    {
        $result = $notificationListService->listNotifications($notificationListDTO);

        return new SuccessResponse($result);
    }
}
