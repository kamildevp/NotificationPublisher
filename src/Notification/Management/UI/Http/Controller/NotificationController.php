<?php

declare(strict_types=1);

namespace App\Notification\Management\UI\Http\Controller;

use App\Notification\Management\Application\Command\SendNotificationCommand;
use App\Notification\Management\Application\DTO\RecipientDTO;
use App\Notification\Management\UI\Documentation\Content\SendNotificationDTOContentDoc;
use App\Notification\Management\UI\DTO\SendNotificationDTO;
use App\Shared\UI\Documentation\Response\ServerErrorResponseDoc;
use App\Shared\UI\Documentation\Response\SuccessResponseDoc;
use App\Shared\UI\Documentation\Response\ValidationErrorResponseDoc;
use App\Shared\UI\Http\Response\SuccessResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[OA\Tag('Notification')]
#[ServerErrorResponseDoc]
class NotificationController extends AbstractController
{
    #[OA\Post(
        summary: 'Send notification',
        description: 'Adds new notification to the queue',
        requestBody: new OA\RequestBody(
            content: new SendNotificationDTOContentDoc()
        )
    )]
    #[SuccessResponseDoc(dataExample: ['status' => 'queued'])]
    #[ValidationErrorResponseDoc]
    #[Route('/notification', name: 'send_notification', methods: ['POST'])]
    public function sendNotification(
        #[MapRequestPayload] SendNotificationDTO $dto, 
        MessageBusInterface $commandBus,
    ): Response
    {
        $commandBus->dispatch(new SendNotificationCommand(
            new RecipientDTO(
                $dto->getRecipient()->getCustomerIdentifier(), 
                $dto->getRecipient()->getEmail(), 
                $dto->getRecipient()->getPhone()
            ),
            $dto->getNotificationType(),
            $dto->getNotificationData()
        ));

        return new SuccessResponse(['status' => 'queued']);
    }
}
