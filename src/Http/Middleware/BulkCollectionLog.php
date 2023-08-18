<?php

namespace Itt\Logger\Http\Middleware;

use Closure;
use Itt\Logger\Logger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Itt\Logger\Jobs\SendDocuments;
use Monolog\Handler\OpenSearchHandler;

class BulkCollectionLog
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        if (config('ittlogger.enabled')) {
            $logger = app('itt.logger');
            if (empty($logger->records)) {
                return;
            }

            if (config('ittlogger.queue.enable')) {
                SendDocuments::dispatch($logger->records)->onQueue(config('ittlogger.queue.name'));
            } else {
                $handlers = $logger->getHandlers();
                $handler = reset($handlers);
                $handler->handleBatch($logger->records);
            }
        }
    }
}
