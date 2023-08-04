<?php

namespace Itt\Logger\Providers;

use Itt\Logger\Logger;
use OpenSearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use Itt\Logger\Handler\OpensearchHandler;

class LoggerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/ittlogger.php' => config_path('ittlogger.php'),
        ], 'itt.logger');
    }

    public function register()
    {
        $this->app->singleton('itt.logger', function () {
            $client = ClientBuilder::create()
                ->setHosts(config('ittlogger.opensearch.hosts'))
                ->setRetries(config('ittlogger.opensearch.retries'))
                ->setConnectionParams(config('ittlogger.opensearch.params'))
                ->build();
            return new Logger('opensearch', [
                new OpensearchHandler($client, config('ittlogger.options'), config('ittlogger.level'), config('ittlogger.bubble'))
            ]);
        });
    }
}
