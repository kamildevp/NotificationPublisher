<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Serializer;

use App\Shared\Domain\ValueObject\AggregateRootId;
use App\Shared\Domain\ValueObject\EmailValueObject;
use App\Shared\Domain\ValueObject\PhoneValueObject;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SimpleValueObjectNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): mixed
    {
        return $object->getValue();
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof AggregateRootId ||
            $data instanceof EmailValueObject ||
            $data instanceof PhoneValueObject;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            AggregateRootId::class => true,
            EmailValueObject::class => true,
            PhoneValueObject::class => true,
        ];
    }
}
