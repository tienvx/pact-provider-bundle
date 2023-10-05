<?php

namespace Tienvx\Bundle\PactProviderBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ControllerInterface
{
    public function handle(Request $request): ?Response;
}
