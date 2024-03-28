# Pact Provider Bundle [![Build Status][actions_badge]][actions_link] [![Coverage Status][coveralls_badge]][coveralls_link] [![Version][version-image]][version-url] [![PHP Version][php-version-image]][php-version-url]

This Symfony Bundle allow testing Symfony project with [Pact PHP][pact-php].
It support:
* Verify sending messages
* Set up provider state
* Tear down provider state

## Installation

```shell
composer require tienvx/pact-provider-bundle
```

## Documentation

### Register State Handler

```php

namespace App\StateHandler;

use Tienvx\Bundle\PactProviderBundle\Attribute\AsStateHandler;
use Tienvx\Bundle\PactProviderBundle\StateHandler\SetUpInterface;
use Tienvx\Bundle\PactProviderBundle\StateHandler\TearDownInterface;

#[AsStateHandler(state: 'A user with id dcd79453-7346-4423-ae6e-127c60d8dd20 exists')]
class UserHandler implements SetUpInterface, TearDownInterface
{
    public function setUp(array $params): void
    {
    }

    public function tearDown(array $params): void
    {
    }
}
```

### Register Message Dispatcher

```php

namespace App\MessageDispatcher;

use Tienvx\Bundle\PactProviderBundle\Attribute\AsMessageDispatcher;
use Tienvx\Bundle\PactProviderBundle\Model\Message;
use Tienvx\Bundle\PactProviderBundle\MessageDispatcher\DispatcherInterface;

#[AsMessageDispatcher(description: 'User created message')]
class UserDispatcher implements DispatcherInterface
{
    public function dispatch(): Message
    {
    }
}
```

## License

[MIT](https://github.com/tienvx/pact-provider-bundle/blob/main/LICENSE)

[actions_badge]: https://github.com/tienvx/pact-provider-bundle/workflows/main/badge.svg
[actions_link]: https://github.com/tienvx/pact-provider-bundle/actions

[coveralls_badge]: https://coveralls.io/repos/tienvx/pact-provider-bundle/badge.svg?branch=main&service=github
[coveralls_link]: https://coveralls.io/github/tienvx/pact-provider-bundle?branch=main

[version-url]: https://packagist.org/packages/tienvx/pact-provider-bundle
[version-image]: http://img.shields.io/packagist/v/tienvx/pact-provider-bundle.svg?style=flat

[php-version-url]: https://packagist.org/packages/tienvx/pact-provider-bundle
[php-version-image]: http://img.shields.io/badge/php-8.0.0+-ff69b4.svg

[pact-php]: https://github.com/pact-foundation/pact-php
