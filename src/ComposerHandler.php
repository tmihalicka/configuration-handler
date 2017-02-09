<?php

namespace TMihalicka\ConfigurationHandler;

use Assert\Assertion;
use Composer\Script\Event;
use TMihalicka\ConfigurationHandler\Configuration\Factory\ConfigurationFactory;
use TMihalicka\ConfigurationHandler\Processor\Factory\ProcessorFactory;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class ComposerHandler
 */
final class ComposerHandler
{
    const COMPOSER_PARAMETER_KEY = 'tmihalicka-params';

    /**
     * Build Parameters from composer configuration and replace this parameters
     * from your dist config file.
     *
     * @param Event $event
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     * @throws \Assert\AssertionFailedException
     * @throws \TMihalicka\ConfigurationHandler\Processor\Exception\InvalidProcessorTypeException
     */
    public static function buildConfigurationParameters(Event $event)
    {
        // REGISTER DOCTRINE ANNOTATION
        AnnotationRegistry::registerLoader('class_exists');

        $extras = $event->getComposer()->getPackage()->getExtra();

        Assertion::keyIsset(
            $extras,
            self::COMPOSER_PARAMETER_KEY,
            'The composer handler needs to be configured through the extra.'.self::COMPOSER_PARAMETER_KEY.' setting.'
        );

        $configs = $extras[self::COMPOSER_PARAMETER_KEY];

        self::assertConfigArray($configs);

        if (array_keys($configs) !== range(0, count($configs) - 1)) {
            $configs = [$configs];
        }

        $configurationFactory = new ConfigurationFactory();
        $processorFactory = new ProcessorFactory($event->getIO(), $configurationFactory);

        /** @var array $config */
        foreach ($configs as $config) {
            self::assertConfigArray($configs);

            $processor = $processorFactory->getProcessor($config);
            $processor->processConfiguration();
        }
    }

    /**
     * Check for configuration is array
     *
     * @param array $configs
     *
     * @throws \Assert\AssertionFailedException
     *
     * @return void
     */
    private static function assertConfigArray(array $configs)
    {
        Assertion::isArray(
            $configs,
            'The composer extra.'.self::COMPOSER_PARAMETER_KEY.' must be an array or a configuration object.'
        );
    }
}
