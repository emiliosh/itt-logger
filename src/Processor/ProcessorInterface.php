<?php

declare(strict_types=1);

namespace Itt\Logger\Processor;

interface ProcessorInterface
{
    /**
     * @param array $record
     * @return array The processed record
     */
    public function __invoke(array $record);
}
