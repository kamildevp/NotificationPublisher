<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Serializer;

use App\Shared\Domain\ValueObject\AggregateRootId;
use App\Shared\Domain\ValueObject\EmailValueObject;
use App\Shared\Domain\ValueObject\PhoneValueObject;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SimpleValueObjectNormalizer implements NormalizerInterface
{
    /**
     * @param array<string, mixed> $context
     * @return string|int
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): mixed
    {
        return $object->getValue();
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof AggregateRootId ||
            $data instanceof EmailValueObject ||
            $data instanceof PhoneValueObject;
    }

    /**
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            AggregateRootId::class => true,
            EmailValueObject::class => true,
            PhoneValueObject::class => true,
        ];
    }
}
