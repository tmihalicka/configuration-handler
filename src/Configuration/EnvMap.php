<?php

namespace TMihalicka\ConfigurationHandler\Configuration;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class EnvMap
 *
 * @Serializer\ExclusionPolicy("all")
 */
class EnvMap
{
    /**
     * Env Map Key
     *
     * @var string
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $key;

    /**
     * Env Map Value
     *
     * @var string
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $value;

    /**
     * EnvMap constructor.
     *
     * @param string $key
     * @param string $value
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Get Key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get Value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get Env Value from Key
     *
     * @return EnvMap
     */
    public function getEnvValue()
    {
        return new self($this->key, getenv($this->value));
    }
}
