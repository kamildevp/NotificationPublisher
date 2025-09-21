<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum EmailType
{
    case INFO;
    case ALERT;

    public function getTemplatePath(): string
    {
        return match($this){
            self::INFO => 'emails/info.html.twig',
            self::ALERT => 'emails/alert.html.twig'
        };
    }

    public function getSubject(): string
    {
        return match($this){
            self::INFO => 'Support Info',
            self::ALERT => 'Important Alert'
        };
    }
}