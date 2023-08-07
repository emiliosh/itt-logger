<?php


namespace Itt\Logger\Handler;

use Throwable;
use RuntimeException;
use Itt\Logger\Logger;
use OpenSearch\Client;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\HandlerInterface;
use Monolog\Formatter\FormatterInterface;
use Itt\Logger\Formatter\OpensearchFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Opensearch\Common\Exceptions\RuntimeException as OpensearchRuntimeException;

class OpensearchHandler extends AbstractProcessingHandler
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array Handler config options
     */
    protected $options = [];

    public function __construct(Client $client, array $options = [], $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->client = $client;
        $this->options = array_merge(
            [
                'index'        => 'monolog',
                'ignore_error' => false,
            ],
            $options
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record): void
    {
        //for stack channels array from monolog
        if (!data_get($record['formatted'], 'extra')) $record['formatted']['extra'] = config('ittlogger.extra');

        $this->bulkSend([$record['formatted']]);
    }

    /**
     * {@inheritdoc}
     */
    public function setFormatter(FormatterInterface $formatter): HandlerInterface
    {
        if ($formatter instanceof OpensearchFormatter) {
            return parent::setFormatter($formatter);
        }

        throw new InvalidArgumentException('OpensearchHandler is only compatible with OpensearchFormatter');
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    protected function getDefaultFormatter(): FormatterInterface
    {
        return new OpensearchFormatter($this->options['index'], Arr::get($this->options, 'type', ''));
    }

    public function handleBatch(array $records): void
    {
        $documents = $this->getFormatter()->formatBatch($records);
        $this->bulkSend($documents);
    }

    protected function bulkSend(array $records): void
    {
        try {
            $params = [
                'body' => [],
            ];

            foreach ($records as $record) {
                $index = [];
                $index['_index'] = $record['_index'];
                if (Arr::has($record, '_type') && strlen($record['_type']) > 0) {
                    $index['_type'] = $record['_type'];
                }
                $params['body'][] = [
                    'index' => $index
                ];
                unset($record['_index'], $record['_type']);

                $params['body'][] = $record;
            }

            $responses = $this->client->bulk($params);

            if ($responses['errors'] === true) {
                throw new OpensearchRuntimeException('Opensearch returned error for one of the records');
            }
        } catch (Throwable $e) {
            if (!$this->options['ignore_error']) {
                Log::channel('opensearch_log')->error('Error sending messages to Opensearch' . $e . PHP_EOL);
            }
        }
    }
}
