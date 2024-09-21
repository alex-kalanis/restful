<?php

namespace Picabo\Restful\Application\Exceptions;

use Nette;
use Picabo\Restful\Validation\Error;
use Throwable;

/**
 * BadRequestException
 * @package Picabo\Restful\Application
 * @author Drahomír Hanák
 */
class BadRequestException extends Nette\Application\BadRequestException
{

    /** @var Error[] Some other errors appear in request */
    public array $errors = [];

    /****************** Simple factories ******************/

    /**
     * Is thrown when trying to reach secured resource without authentication
     */
    public static function unauthorized(string $message = '', ?Throwable $previous = NULL): self
    {
        return new self($message, 401, $previous);
    }

    /**
     * Is thrown when access to this resource is forbidden
     */
    public static function forbidden(string $message = '', ?Throwable $previous = NULL): self
    {
        return new self($message, 403, $previous);
    }

    /**
     * Is thrown when resource's not found
     */
    public static function notFound(string $message = '', ?Throwable $previous = NULL): self
    {
        return new self($message, 404, $previous);
    }

    /**
     * Is thrown when request method (e.g. POST or PUT) is not allowed for this resource
     */
    public static function methodNotSupported(string $message = '', ?Throwable $previous = NULL): self
    {
        return new self($message, 405, $previous);
    }

    /**
     * Is thrown when this resource is not no longer available (e.g. with new API version)
     */
    public static function gone(string $message = '', ?Throwable $previous = NULL): self
    {
        return new self($message, 410, $previous);
    }

    /**
     * Is thrown when incorrect (or unknown) Content-Type was provided in request
     */
    public static function unsupportedMediaType(string $message = '', ?Throwable $previous = NULL): self
    {
        return new self($message, 415, $previous);
    }

    /**
     * Is thrown when validation problem appears
     * @param Error[] $errors during validation
     * @param string $message
     * @param Throwable|null $previous
     * @return self
     */
    public static function unprocessableEntity(array $errors, string $message = '', ?Throwable $previous = NULL): self
    {
        $e = new self($message, 422, $previous);
        $e->errors = $errors;
        return $e;
    }

    /**
     * Is thrown to reject request due to rate limiting
     */
    public static function tooManyRequests(string $message = '', ?Throwable $previous = NULL): self
    {
        return new self($message, 429, $previous);
    }
}
