<?php

namespace TMihalicka\ConfigurationHandler\Processor;

use RuntimeException;
use TMihalicka\ConfigurationHandler\Processor\Common\ProcessorInterface;
use TMihalicka\ConfigurationHandler\Processor\Exception\DistFileNotFoundException;
use TMihalicka\ConfigurationHandler\Processor\Exception\InvalidArgumentException;

/**
 * Class ArrayProcessor
 */
final class ArrayProcessor extends AbstractProcessor implements ProcessorInterface
{
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

        $actualValues = $this->getProcessedActualValuesParameters();

        file_put_contents(
            $config->getFile(),
            $this->generatePhpArrayTemplate($actualValues[$config->getParameterKey()])
        );
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
    protected function getParametersFromFile($filePath)
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
