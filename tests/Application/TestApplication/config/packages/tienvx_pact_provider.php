<?php

$container->loadFromExtension('tienvx_pact_provider', [
    'state_change' => [
        'body' => '%env(bool:STATE_CHANGE_BODY)%',
        'url' => '/test-pact-change-state',
    ],
    'messages_url' => '/test-pact-messages',
]);
