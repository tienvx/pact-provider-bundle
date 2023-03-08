<?php

namespace Tienvx\Bundle\PactProviderBundle\Exception;

use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;

class BadRequestException extends \UnexpectedValueException implements RequestExceptionInterface
{
}
