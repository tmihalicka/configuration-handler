<?php

namespace TMihalicka\ConfigurationHandler\Processor\Common;

/**
 * Interface ProcessorInterface
 */
interface ProcessorInterface
{
    const PROCESSOR_TYPE_ARRAY  = 'array';
    const PROCESSOR_TYPE_NULL   = 'null';

    /**
     * Process Given Configuration
     *
     * @return void
     */
    public function processConfiguration();
}
