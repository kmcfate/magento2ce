<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MagentoCloud\Command;

use Magento\MagentoCloud\Cli;
use Magento\MagentoCloud\Filesystem\ConfigFileList;
use Magento\MagentoCloud\Config\Environment\ReaderInterface;
use Magento\MagentoCloud\Filesystem\Driver\File;
use Magento\MagentoCloud\Filesystem\FileSystemException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use InvalidArgumentException;

/**
 * Updates .magento.env.yaml.
 *
 * @api
 */
class ConfigUpdate extends Command
{
    public const NAME = 'cloud:config:update';
    public const ARG_CONFIGURATION = 'configuration';

    /**
     * @var ConfigFileList
     */
    private $configFileList;

    /**
     * @var File
     */
    private $file;

    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @param ConfigFileList $configFileList
     * @param File $file
     * @param ReaderInterface $reader
     */
    public function __construct(ConfigFileList $configFileList, File $file, ReaderInterface $reader)
    {
        $this->configFileList = $configFileList;
        $this->file = $file;
        $this->reader = $reader;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName(static::NAME)
            ->setDescription(
                'Updates the existing `.magento.env.yaml` file with the specified configuration. ' .
                'Creates `.magento.env.yaml` file if it does not exist.'
            )
            ->addArgument(
                self::ARG_CONFIGURATION,
                InputArgument::REQUIRED,
                'Configuration in JSON format'
            );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws FileSystemException
     * @throws InvalidArgumentException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $configuration = $input->getArgument(self::ARG_CONFIGURATION);
        $decodedConfig = json_decode($configuration, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('Wrong JSON format: ' . json_last_error_msg());
        }

        $config = array_replace_recursive($this->reader->read(), $decodedConfig);

        $yaml = Yaml::dump($config, 10, 2);
        $filePath = $this->configFileList->getEnvConfig();

        $this->file->filePutContents($filePath, $yaml);

        $output->writeln(sprintf("Config file %s was updated", $filePath));

        return Cli::SUCCESS;
    }
}
