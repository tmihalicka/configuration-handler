<?php

namespace TMihalicka\ConfigurationHandler\Configuration\Factory;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use TMihalicka\ConfigurationHandler\Configuration\Configuration;

/**
 * Class ConfigurationFactory
 */
class ConfigurationFactory
{
    /**
     * Serializer
     *
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Build Configuration from Composer file
     *
     * @param array $configuration
     *
     * @return Configuration
     */
    public function buildConfiguration(array $configuration)
    {
        return $this->getSerializer()->fromArray($configuration, Configuration::class);
    }

    /**
     * Get Serializer
     *
     * @return Serializer
     */
    private function getSerializer()
    {
        if (!$this->serializer) {
            $this->serializer = SerializerBuilder::create()->build();
        }

        return $this->serializer;
    }
}
