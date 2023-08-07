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
        if (config('ittlogger.enabled')) {
            $this->app->singleton('itt.logger', function () {
                $client = new ClientBuilder();
                $client->setHosts(config('ittlogger.opensearch.hosts'));
                $client->setRetries(config('ittlogger.opensearch.retries'));
                $client->setBasicAuthentication('admin', 'admin');
                $client->setSSLVerification(false);
                $client->setConnectionParams(config('ittlogger.opensearch.params'));
                $client = $client->build();
                // dd($client->info());
                return new Logger('opensearch', [
                    new OpensearchHandler($client, config('ittlogger.options'), config('ittlogger.level'), config('ittlogger.bubble'))
                ]);
            });
        }
    }
}
