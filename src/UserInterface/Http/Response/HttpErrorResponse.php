<?php

declare(strict_types=1);

namespace App\UserInterface\Http\Response;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

class HttpErrorResponse implements ExceptionResponseInterface
{
    public static function createFromException(Throwable $exception): ApiResponse
    {
        $previousException = $exception->getPrevious();

        if($previousException instanceof ValidationFailedException){
            $errors = [];
            $camelCaseConverter = new CamelCaseToSnakeCaseNameConverter();
            foreach ($previousException->getViolations() as $violation) {
                $propertyPath = $violation->getPropertyPath();
                $requestParameterName = $camelCaseConverter->normalize($propertyPath);
                $errors[$requestParameterName][] = $violation->getMessage();
            }

            return new ValidationErrorResponse(array_undot($errors));
        }
        else{
            return new ServerErrorResponse();
        }
    }
} 