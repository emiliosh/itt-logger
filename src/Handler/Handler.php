<?php


namespace Itt\Logger\Handler;

abstract class Handler implements HandlerInterface
{
    public function handleBatch(array $records): void
    {
        foreach ($records as $record) {
            $this->handle($record);
        }
    }

    public function close(): void
    {
    }

    public function __destruct()
    {
        try {
            $this->close();
        } catch (\Throwable $e) {
        }
    }

    public function __sleep()
    {
        $this->close();

        return array_keys(get_object_vars($this));
    }
}
