<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Config;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ConfigFactory
{
    public function __construct(private DenormalizerInterface $denormalizer) {}

    public function create(mixed $config, string $type): mixed
    {
        return $this->denormalizer->denormalize($config, $type);
    }
}