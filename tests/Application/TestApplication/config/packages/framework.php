<?php

use Tienvx\Bundle\PactProviderBundle\Tests\Application\TestApplication\Kernel;

$configuration = [
    'http_method_override' => false,
    'handle_all_throwables' => true,
    'php_errors' => [
        'log' => true,
    ],
    'test' => true,
];

if (Kernel::MAJOR_VERSION <= 5) {
    unset($configuration['handle_all_throwables']);
}

$container->loadFromExtension('framework', $configuration);
