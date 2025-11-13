<?php

declare(strict_types=1);

namespace App\Notification\Management\UI\Validator\Constraints;

use App\Notification\Management\UI\Validator\NotificationDataValidator;
use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class NotificationData extends Constraint
{
    #[HasNamedArguments]
    public function __construct(
        ?array $groups = null,
        mixed $payload = null,
    ) 
    {
        parent::__construct([], $groups, $payload);
    }

    public function validatedBy()
    {
        return NotificationDataValidator::class;
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}