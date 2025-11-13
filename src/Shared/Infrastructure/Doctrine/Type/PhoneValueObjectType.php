<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Shared\Domain\ValueObject\PhoneValueObject;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;

abstract class PhoneValueObjectType extends Type
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

        return $value->getValue();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?PhoneValueObject
    {
        if($value === null){
            return null;
        }

        $class = $this->typeClassName();
        return new $class($value);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
