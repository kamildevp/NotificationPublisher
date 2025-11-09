<?php

declare(strict_types=1);

namespace App\Notification\Management\UI\Http\Controller;

use App\Notification\Management\Application\Query\ListNotificationsQuery;
use App\Notification\Management\UI\Documentation\Schema\NotificationSchema;
use App\Notification\Management\UI\DTO\ListNotificationsDTO;
use App\Shared\UI\Documentation\Response\ServerErrorResponseDoc;
use App\Shared\UI\Documentation\Response\SuccessResponseDoc;
use App\Shared\UI\Documentation\Schema\PaginationResultSchema;
use App\Shared\UI\Http\Response\SuccessResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Messenger\HandleTrait;

#[OA\Tag('Notification')]
#[ServerErrorResponseDoc]
class ListNotificationsController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        private MessageBusInterface $messageBus,
    ) 
    {
        
    }

    #[OA\Get(
        summary: 'List notifications',
        description: 'Returns paginated notification list'
    )]
    #[SuccessResponseDoc(dataSchema: new PaginationResultSchema(new NotificationSchema()))]
    #[Route('/notification', name: 'list_notifications', methods: ['GET'])]
    public function listNotifications(
        #[MapQueryString] ListNotificationsDTO $dto
    ): Response
    {
        $result = $this->handle(new ListNotificationsQuery(
            $dto->getPage(),
            $dto->getPerPage(),
            $dto->getRecipientCustomerIdentifier()
        ));

        return new SuccessResponse($result);
    }
}
