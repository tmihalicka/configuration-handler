<?php

namespace TMihalicka\ConfigurationHandler\Processor;

use TMihalicka\ConfigurationHandler\Processor\Common\ProcessorInterface;

/**
 * Class NullProcessor
 * @package TMihalicka\ConfigurationHandler\Processor\Factory
 */
final class NullProcessor implements ProcessorInterface
{
    /**
     * Process Given Configuration
     *
     * @return void
     */
    public function processConfiguration()
    {
        // NULL do nothing ;)
    }
}
