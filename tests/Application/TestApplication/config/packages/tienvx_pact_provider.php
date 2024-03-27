<?php

$container->loadFromExtension('tienvx_pact_provider', [
    'state_change' => [
        'body' => true,
        'url' => '/test-pact-change-state',
    ],
    'messages_url' => '/test-pact-messages',
]);
