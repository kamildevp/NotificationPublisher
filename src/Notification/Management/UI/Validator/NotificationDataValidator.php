<?php

declare(strict_types=1);

namespace App\Notification\Management\UI\Validator;

use App\Notification\Management\UI\DTO\SendNotificationDTO;
use App\Notification\Management\UI\Enum\NotificationType;
use App\Notification\Management\UI\Validator\Constraints\NotificationData;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotificationDataValidator extends ConstraintValidator
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
        private ValidatorInterface $validator
    )
    {
        
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotificationData) {
            throw new UnexpectedTypeException($constraint, NotificationData::class);
        }

        if (!$value instanceof SendNotificationDTO) {
            throw new UnexpectedValueException($value, SendNotificationDTO::class);
        }

        $notificationType = NotificationType::tryFrom($value->getNotificationType());
        if(!$notificationType){
            return;
        }

        $notificationDataDTOClass = $notificationType->getDataDTOClass();
        try{
            $dto = $this->denormalizer->denormalize(
                $value->getNotificationData(), 
                $notificationDataDTOClass, 
                context: [DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true]
            );
        } 
        catch (PartialDenormalizationException $e){
            foreach ($e->getErrors() as $error) {
                $message = 'This value was of an unexpected type.';
                if ($expectedTypes = $error->getExpectedTypes()) {
                    $message = 'This value should be of type '.implode('|', $expectedTypes).'.';
                }

                $this->context->buildViolation($message)
                    ->atPath('notificationData.'.$error->getPath())
                    ->addViolation();
            }
            return;
        }

        $violations = $this->validator->validate($dto);
        foreach($violations as $violation){
            $this->context->buildViolation($violation->getMessage())
                ->atPath('notificationData.'.$violation->getPropertyPath())
                ->addViolation();
        }
    }
}