<?php

namespace TMihalicka\ConfigurationHandler\Configuration;

/**
 * Class EnvMap
 */
class EnvMap
{
    /**
     * Env Map Key
     *
     * @var string
     */
    private $key;

    /**
     * Env Map Value
     *
     * @var string
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
     * Get Env Value from Value
     *
     * @return EnvMap
     */
    public function getEnvValue()
    {
        return new self($this->key, getenv($this->value));
    }
}
