<?php

namespace TMihalicka\ConfigurationHandler\Processor;

use Composer\IO\IOInterface;
use TMihalicka\ConfigurationHandler\Configuration\Configuration;
use TMihalicka\ConfigurationHandler\Processor\Common\ProcessorInterface;

/**
 * Class ArrayProcessor
 */
final class ArrayProcessor implements ProcessorInterface
{
    /**
     * Composer IO
     *
     * @var IOInterface
     */
    private $composerIo;

    /**
     * Parameter Configuration
     *
     * @var Configuration
     */
    private $configuration;

    /**
     * ArrayProcessor constructor.
     *
     * @param IOInterface $composerIo
     * @param Configuration $configuration
     */
    public function __construct(IOInterface $composerIo, Configuration $configuration)
    {
        $this->composerIo = $composerIo;
        $this->configuration = $configuration;
    }

    /**
     * Process Given Configuration
     *
     * @return void
     */
    public function processConfiguration()
    {
        // TODO: Implement processConfiguration() method.
    }
}
