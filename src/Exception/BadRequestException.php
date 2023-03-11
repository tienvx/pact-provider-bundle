<?php

namespace Tienvx\Bundle\PactProviderBundle\Exception;

// use Symfony\Component\HttpFoundation\Exception\BadRequestException as BaseBadRequestException;
use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;

class BadRequestException extends /* BaseBadRequestException */ \UnexpectedValueException implements RequestExceptionInterface
{
}
