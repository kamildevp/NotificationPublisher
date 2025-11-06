<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Type;

use BackedEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;

abstract class BackedStringEnumType extends Type
{
    abstract protected function typeClassName(): string;
    abstract public function getName(): string;

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if($value === null){
            return null;
        }

        $class = $this->typeClassName();
        if(get_class($value) != $class){
            throw new InvalidArgumentException(sprintf('Expected %s, got %s', $class, get_debug_type($value)));
        }

        if(!is_string($value->value)){
            throw new InvalidArgumentException(sprintf('Expected backing type string, got %s', get_debug_type($value->value)));
        }

        return $value->value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?BackedEnum
    {
        if($value === null){
            return null;
        }

        $class = $this->typeClassName();
        return $class::from($value);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
