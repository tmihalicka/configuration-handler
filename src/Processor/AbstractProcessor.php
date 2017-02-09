<?php

namespace TMihalicka\ConfigurationHandler\Processor;

use Composer\IO\IOInterface;
use RuntimeException;
use TMihalicka\ConfigurationHandler\Configuration\Configuration;

/**
 * Class AbstractProcessor
 */
abstract class AbstractProcessor
{
    /**
     * Composer IO
     *
     * @var IOInterface
     */
    protected $composerIo;

    /**
     * Parameter Configuration
     *
     * @var Configuration
     */
    protected $configuration;

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
     * Processed Actual Values Parameters
     *
     * @return array
     * @throws \RuntimeException
     */
    protected function getProcessedActualValuesParameters()
    {
        $config = $this->configuration;

        $outputFileExists = is_file($config->getFile());

        $this->composerIo->write(
            sprintf('<info>%s the "%s" file.</info>', $outputFileExists ? 'Updating' : 'Creating', $config->getFile())
        );

        $expectedParams = $this->getParametersFromFile($config->getDistFile())[$config->getParameterKey()];

        $actualValues = array_merge($expectedParams, [$config->getParameterKey() => []]);

        if ($outputFileExists) {
            $existingValues = $this->getParametersFromFile($config->getFile());

            $actualValues = array_merge($actualValues, $existingValues);
        }

        $actualValues[$config->getParameterKey()] = $this->processParameters(
            $expectedParams,
            $actualValues[$config->getParameterKey()]
        );

        $this->handleOutputDirectory();

        return $actualValues;
    }

    /**
     * Handle Output Directory
     *
     * @return void
     */
    protected function handleOutputDirectory()
    {
        $outputDirName = dirname($this->configuration->getFile());

        if (!@mkdir($outputDirName, 0755, true) && !is_dir($outputDirName)) {
            throw new \RuntimeException(sprintf('Unable to create %s directory', $this->configuration->getFile()));
        }
    }

    /**
     * Process Parameters
     *
     * @param array $expectedParams
     * @param array $actualParams
     *
     * @return array
     *
     * @throws RuntimeException
     */
    protected function processParameters(array $expectedParams, array $actualParams)
    {
        // Outdated Map Replace
        if (!$this->configuration->getKeepOutdated()) {
            $actualParams = array_intersect($actualParams, $expectedParams);
        }

        // ENV Map Replace
        $actualParams = array_replace($actualParams, $this->getParamsFromEnv($this->configuration));

        return $this->getParamsFromCLI($expectedParams, $actualParams);
    }

    /**
     * Generate parameters from ENV Variables
     *
     * @param Configuration $configuration
     *
     * @return array
     */
    protected function getParamsFromEnv(Configuration $configuration)
    {
        $envVariablesMap = $configuration->getEnvMap();
        $params = [];

        foreach ($envVariablesMap as $envVariable) {
            $value = $envVariable->getEnvValue();
            if ($value) {
                $params[$envVariable->getKey()] = trim($value);
            }
        }

        return $params;
    }

    /**
     * Generate parameters from user input
     *
     * @param array $expectedParams
     * @param array $actualParams
     *
     * @return array
     *
     * @throws RuntimeException
     */
    protected function getParamsFromCLI(array $expectedParams, array $actualParams)
    {
        if (!$this->composerIo->isInteractive()) {
            return array_replace($expectedParams, $actualParams);
        }

        $started = false;

        foreach ($expectedParams as $key => $message) {
            if (array_key_exists($key, $actualParams)) {
                continue;
            }

            if (!$started) {
                $started = true;
                $this->composerIo->write('<comment>Some parameters are missing. Please provide them.</comment>');
            }


            $value = $this->composerIo->ask(
                sprintf('<question>%s</question> (<comment>%s</comment>): ', $key, $message),
                $message
            );

            $actualParams[$key] = trim($value);
        }

        return $actualParams;
    }


    /**
     * Load Values From Dist File
     *
     * @param string $filePath
     *
     * @return array
     */
    abstract protected function getParametersFromFile($filePath);
}
