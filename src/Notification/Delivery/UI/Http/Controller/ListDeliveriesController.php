<?php

declare(strict_types=1);

namespace App\Notification\Delivery\UI\Http\Controller;

use App\Notification\Delivery\Application\Query\ListDeliveriesQuery;
use App\Notification\Delivery\UI\Documentation\Schema\DeliveryListItems;
use App\Notification\Delivery\UI\DTO\ListDeliveriesDTO;
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

#[OA\Tag('Notification Delivery')]
#[ServerErrorResponseDoc]
class ListDeliveriesController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $messageBus,
    ) 
    {
        $this->messageBus = $messageBus;
    }

    #[OA\Get(
        summary: 'List deliveries',
        description: 'Returns paginated notification deliveries list'
    )]
    #[SuccessResponseDoc(dataSchema: new PaginationResultSchema(new DeliveryListItems()))]
    #[Route('/delivery', name: 'list_deliveries', methods: ['GET'])]
    public function listNotifications(
        #[MapQueryString] ListDeliveriesDTO $dto
    ): Response
    {
        $result = $this->handle(new ListDeliveriesQuery(
            $dto->getPage(),
            $dto->getPerPage(),
            $dto->getRecipientCustomerIdentifier()
        ));

        return new SuccessResponse($result);
    }
}
