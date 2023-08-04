<?php

namespace Itt\Logger;

class OpensearchLogger
{
    public function __invoke(array $config)
    {
        return app('itt.logger');
    }
}
