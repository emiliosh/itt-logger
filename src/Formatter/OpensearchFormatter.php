<?php

namespace Itt\Logger\Formatter;

use DateTime;
use Monolog\Formatter\NormalizerFormatter;

class OpensearchFormatter extends NormalizerFormatter
{

    protected $index;

    protected $type;

    public function __construct(string $index, string $type = '')
    {
        parent::__construct(DateTime::ISO8601);

        $this->index = $index;
        $this->type = $type;
    }

    public function format(array $record)
    {
        $record = parent::format($record);

        return $this->getDocument($record);
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getType(): string
    {
        return $this->type;
    }

    protected function getDocument(array $record): array
    {
        $record['_index'] = $this->index;
        if (strlen($this->type) > 0) {
            $record['_type'] = $this->type;
        }

        return $record;
    }
}
