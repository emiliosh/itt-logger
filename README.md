# Laravel log driver for opensearch

# Installation

```bash
$ composer require itt/logger
$ php artisan vendor:publish --tag=itt.logger
```
# Config

You can modify config in `config/ittlogger.php`.

Now we can add the `channel` of `channels` in `config/logging.php` file.

```php
use Itt\Logger\OpensearchLogger;

'channels' => [
    'opensearch' => [
        'driver' => 'custom',
        'via' => OpensearchLogger::class,
    ],
],
```
Add the `\Itt\Logger\Http\Middleware\BulkCollectionLog` middleware to `App\Http\Kernel.php` file.

```php
/**
 * The application's global HTTP middleware stack.
 *
 * These middleware are run during every request to your application.
 *
 * @var array
 */
protected $middleware = [
    \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
    \App\Http\Middleware\TrimStrings::class,
    \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    \App\Http\Middleware\TrustProxies::class,
    \Itt\Logger\Http\Middleware\BulkCollectionLog::class
];
```

Now define the environment variable in `.env` file like this:

```
LOG_CHANNEL=opensearch
OPENSEARCH_HOST=localhost
OPENSEARCH_PORT=9200
OPENSEARCH_SCHEME=http
OPENSEARCH_USER=
OPENSEARCH_PASS=
```

