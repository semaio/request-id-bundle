# Symfony Request ID Bundle

This bundle adds request IDs to the request and response in your Symfony application.

Why? It is a great and simple way to add some additional information to logs and to present to users. For example, if a
request fails or an exception is thrown you'll be able to show the user the request ID which they can pass on to you to
locate their specific issue.

## Installation

The request ID bundle can be installed at any point during a project's lifecycle.

### Requirements

* PHP 8.0
* Symfony 6.0

### Install the bundle

Please install the bundle using [composer](https://getcomposer.org/):

```
composer require semaio/request-id-bundle
```

### Enable the bundle

Then, enable the bundle by adding the following line in `bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Semaio\RequestId\SemaioRequestIdBundle::class => ['all' => true],
    // ...
];
```

## Configure the bundle

Now, that the bundle is installed and enabled, you have to add some configuration:

```yaml
# config/packages/semaio_request_id.yml

semaio_request_id:
    # Service that implements `Semaio\RequestId\Generator\GeneratorInterface`.
    # Defaults to `Semaio\RequestId\Generator\RamseyUuid4Generator`.
    generator_service: ~

    # Service that implements `Semaio\RequestId\Policy\PolicyInterface`.
    # Defaults to `Semaio\RequestId\Policy\DefaultPolicy`.
    policy_service: ~

    # Service that implements `Semaio\RequestId\Provider\ProviderInterface`.
    # Defaults to `Semaio\RequestId\Provider\SimpleRequestIdProvider`.
    provider_service: ~

    # The header which will contain the request ID on the response.
    response_header: "X-Request-Id"

    # The header which will contain the request ID that will be checked on incoming requests.
    request_header: "X-Request-Id"

    # Whether to add the request ID to monolog messages (see below), defaults to true.
    enable_monolog: true

    # Whether to add the twig extension (see below), defaults to true.
    enable_twig: true

    # Configuration for additional generators.
    generators:
        # Additional configuration for `Md5Generator`
        md5:
            # Specify which generator should be used to generate a request id that is then hashed by PHP's md5 function.
            # Defaults to `RamseyUuid4Generator`
            generator_service: ~

        # Additional configuration for `PhpUniqidGenerator`. 
        # See http://php.net/manual/en/function.uniqid.php for more information about PHP's uniqid function parameters.
        phpuniqid:
            prefix: ''
            more_entropy: false 
```

## How it works

When a request comes in your Symfony application and if your configured policy allows it, the bundle inspects
the `X-Request-Id` header. If present, the given value will be used throught the rest of your request lifecycle in your
Symfony application. This lets you use request ID's from somewhere higher up in the stack (like in the web server itself
or a load balancer).

If no request ID is found or configured policy rejects using any given `X-Request-Id` header from the incoming request,
the bundle creates a new request ID based on the configured generator. By default, a UUID version 4 request ID is
generated (example: `31c70a8e-8a1e-47af-9c31-3285e9bc2eb3`).

Before sending the response to the client, the `X-Request-Id` header is also set on the response using the generated
request ID value.

## Components

### Generators

Generators create a random string that will be used as request ID throughout the request lifecycle of your Symfony
application.

All generators must implement the `Semaio\RequestId\Generator\GeneratorInterface`. By default, there are three possible
generators:

* `RamseyUuid4Generator`
    * This generator creates a UUID v4 request ID by leveraging the [ramsey/uuid](https://github.com/ramsey/uuid)
      library.
    * This is the default generator!
* `PhpUniqidGenerator`
    * This generator creates a request ID with PHP's native [uniqid](http://php.net/manual/en/function.uniqid.php)
      function.
* `Md5Generator`
    * This generator creates a request ID through an injected generator (default: `RamseyUuid4Generator`) and then
      hashes the request ID with PHP's native [md5](https://www.php.net/manual/en/function.md5.php) function.

### Policies

Policies check the incoming request for two reasons:

* Should a request ID be added to the current request?
* If the current request already contains a request ID, should this value be trusted or should the bundle create a new
  request ID?

All policies must implement the `Semaio\RequestId\Policy\PolicyInterface`. By default, there are two possible policies:

* `DefaultPolicy`
    * Allows request ID to be added to the request and also accepts any given incoming request ID.
    * This is the default policy!
* `RejectRequestIdHeaderPolicy`
    * Allows request ID to be added to the request but rejects any given incoming request ID.

### Providers

Providers hold the generated request ID and provide it to any part of your code where you might need the request ID. By
default, there is only one provider:

* `SimpleRequestIdProvider`
    * Just a simple getter-setter PHP object.
    * This is the default provider!

## Extensions

### Monolog integration

This bundle provides a monolog *processor* which adds the request ID to `extra` array on the record. This can be turned
off by setting `enable_monolog` to `false` in bundle configuration.

To use the request ID in your logs, include `%extra.request_id%` in your formatter. Here's a configuration example from
this bundle's tests.

```yaml
# https://symfony.com/doc/current/logging.html#changing-the-formatter

services:
    request_id_formatter:
        class: Monolog\Formatter\LineFormatter
        arguments:
            - "[%%level_name%% - %%extra.request_id%%] %%message%%"

monolog:
    handlers:
        file:
            type: stream
            level: debug
            formatter: request_id_formatter
```

### Twig integration

This bundle provides a global `request_id` function in your Twig environment. This can be turned off by
setting `enable_twig` to `false` in bundle configuration.

Here's an example of a template.

```html
<!DOCTYPE html>
<html>
<head>
    <title>Hello World!</title>
</head>
<body>
    <h1>{{ request_id() }}</h1>
</body>
</html>
```

## License

[MIT](LICENSE.md)
