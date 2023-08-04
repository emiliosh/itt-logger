<?php

namespace Itt\Logger\Handler;

use LogicException;
use Monolog\ResettableInterface;
use Monolog\Handler\HandlerInterface;

trait ProcessableHandlerTrait
{
    protected $processors = [];
    public function pushProcessor(callable $callback): HandlerInterface
    {
        array_unshift($this->processors, $callback);

        return $this;
    }

    public function popProcessor(): callable
    {
        if (!$this->processors) {
            throw new LogicException('You tried to pop from an empty processor stack.');
        }

        return array_shift($this->processors);
    }

    protected function processRecord(array $record): array
    {
        foreach ($this->processors as $processor) {
            $record = $processor($record);
        }

        return $record;
    }
    protected function resetProcessors(): void
    {
        foreach ($this->processors as $processor) {
            if ($processor instanceof ResettableInterface) {
                $processor->reset();
            }
        }
    }
}
