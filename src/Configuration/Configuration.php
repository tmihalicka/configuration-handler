<?php

namespace TMihalicka\ConfigurationHandler\Configuration;

use TMihalicka\ConfigurationHandler\Processor\Common\ProcessorInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class Configuration
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Configuration
{
    const DEFAULT_DIST_SUFFIX = 'dist';
    const DEFAULT_PARAMETER_KEY = 'parameters';

    /**
     * Configuration Processor
     *
     * @var string
     *
     * @Serializer\SerializedName("processor")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $processor;

    /**
     * Output file
     *
     * WARNING: By default this output file is also dist file with .dist suffix
     *
     * @var string
     *
     * @Serializer\SerializedName("file")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $file;

    /**
     * Dist File
     *
     * WARNING: By default this dist file is same as output file with .dist suffix
     *
     * @var string
     *
     * @Serializer\SerializedName("dist-file")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $distFile;

    /**
     * Keep outdated parameters
     *
     * @var string
     *
     * @Serializer\SerializedName("keep-outdated")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $keepOutdated;

    /**
     * Top Parameter Key
     *
     * @var string
     *
     * @Serializer\SerializedName("parameter-key")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $parameterKey;

    /**
     * Environment Variables Parameters
     *
     * @var array
     *
     * @Serializer\SerializedName("env-map")
     * @Serializer\Type("array<string, string>")
     * @Serializer\Expose()
     */
    private $envMap;

    /**
     * Get Configuration Processor
     *
     * @return string
     */
    public function getProcessor()
    {
        return $this->processor ?: ProcessorInterface::PROCESSOR_TYPE_ARRAY;
    }

    /**
     * Get File
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get Dist File
     *
     * @return string
     */
    public function getDistFile()
    {
        return $this->distFile ?: $this->file.'.'.self::DEFAULT_DIST_SUFFIX;
    }

    /**
     * Get Keep Outdated Flag
     *
     * @return string
     */
    public function getKeepOutdated()
    {
        return $this->keepOutdated ?: false;
    }

    /**
     * Get Parameter Key
     *
     * @return string
     */
    public function getParameterKey()
    {
        return $this->parameterKey ?: self::DEFAULT_PARAMETER_KEY;
    }

    /**
     * Get Environment Variables Map
     *
     * Workaround for JMS serializer which don't know how to handle key, value pairs
     *
     * @return EnvMap[]
     */
    public function getEnvMap()
    {
        if ($this->envMap) {
            return array_map(function ($key, $value) {
                return new EnvMap($key, $value);
            }, array_keys($this->envMap), $this->envMap);
        }

        return [];
    }
}
