<?php

namespace Itt\Logger\Jobs;

use Itt\Logger\Logger;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Monolog\Handler\OpenSearchHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Send documents
 *
 * Date: 2019/11/23
 * @author George
 * @package Betterde\Logger\Jobs
 */
class SendDocuments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Log record
     *
     * @var array $records
     * Date: 2019/11/23
     * @author George
     */
    public $records = [];

    /**
     * Create a new job instance.
     *
     * @param array $records
     */
    public function __construct(array $records = [])
    {
        $this->records = $records;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * @var Logger $logger
         */
        $logger = app('itt.logger');
        $handlers = $logger->getHandlers();

        $handler = reset($handlers);

        if (config('ittlogger.batch')) {
            if (count($this->records) > 0) {
                $handler->handleBatch($this->records);
            }
        } else {
            if (isset($this->records['message'])) {
                $handler->handle($this->records);
            }
        }
    }
}
