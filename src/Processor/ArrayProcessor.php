<?php

namespace TMihalicka\ConfigurationHandler\Processor;

use Composer\IO\IOInterface;
use RuntimeException;
use TMihalicka\ConfigurationHandler\Configuration\Configuration;
use TMihalicka\ConfigurationHandler\Processor\Common\ProcessorInterface;
use TMihalicka\ConfigurationHandler\Processor\Exception\DistFileNotFoundException;
use TMihalicka\ConfigurationHandler\Processor\Exception\InvalidArgumentException;

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
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws DistFileNotFoundException
     */
    public function processConfiguration()
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


        $outputDirName = dirname($config->getFile());

        if (!@mkdir($outputDirName, 0755, true) && !is_dir($outputDirName)) {
            throw new \RuntimeException(sprintf('Unable to create %s directory', $config->getFile()));
        }

        file_put_contents(
            $config->getFile(),
            $this->generatePhpArrayTemplate($actualValues[$config->getParameterKey()])
        );
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
    private function processParameters(array $expectedParams, array $actualParams)
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
    private function getParamsFromEnv(Configuration $configuration)
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
     * @throws \RuntimeException
     */
    private function getParamsFromCLI(array $expectedParams, array $actualParams)
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
     *
     * @throws InvalidArgumentException
     * @throws DistFileNotFoundException
     */
    private function getParametersFromFile($filePath)
    {
        $config = $this->configuration;

        if (!file_exists($filePath)) {
            throw new DistFileNotFoundException('Unable to find '. $filePath .' file.');
        }

        // Load File
        $file = include $filePath;

        if (!is_array($file)) {
            throw new InvalidArgumentException(sprintf('Input file %s does not contains valid array.', $filePath));
        }


        if (!array_key_exists($config->getParameterKey(), $file)) {
            throw new InvalidArgumentException(sprintf('The top-level key %s is missing.', $config->getParameterKey()));
        }

        return $file;
    }

    /**
     * Generate PHP Template From Configuration
     *
     * @param array $data
     *
     * @return string
     */
    private function generatePhpArrayTemplate(array $data)
    {
        $string  = "<?php\n";
        $string .= "// This file is auto-generated during the composer install\n\n";
        $string .= "return [\n";
        $string .= "    '{$this->configuration->getParameterKey()}' => [\n";
        foreach ($data as $key => $value) {
            $string .= "        '{$key}' => '{$value}',\n";
        }
        $string .= "    ]\n";
        $string .= "];\n";

        return $string;
    }
}
