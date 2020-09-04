<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Console\Command\InstallPackage;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

final class AddPackageConfigCommand extends Command
{
    protected static $defaultName = 'artis:add-package-config';

    /** @var ParameterBagInterface */
    private $parameterBag;

    /** @var string */
    private $packageName;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        parent::__construct();

        $this->parameterBag = $parameterBag;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Setting up package config.')
            ->setHelp('This command allows you to set up package config...');
    }

    public function setPackageName(string $packageName): void
    {
        $this->packageName = $packageName;
    }

    public function getPackageName(): ?string
    {
        return $this->packageName;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');

        $configPath = $projectDir . '/vendor/' . $this->packageName . '/src/Resources/config/artis_package_manager_config.json';

        $configFile = file_get_contents($configPath);
        $config = json_decode($configFile, true);

        foreach ($config['install'] as $elementName => $elements) {
            switch ($elementName) {
                case 'config':
                    $this->addPackageConfigForConfig($elements, $projectDir);
                    break;
                default:
            }
        }
    }

    private function addPackageConfigForConfig(array $elements, string $projectDir): void
    {
        foreach ($elements as $packageConfigName => $packageConfigs) {
            $packageConfigPath = $projectDir . '/' . $packageConfigName;

            $yamlParser = new Parser();
            $packageConfigFile = $yamlParser->parseFile($packageConfigPath);

            foreach ($packageConfigs['add'] as $packageConfig) {
                $numberOfImports = count($packageConfigFile['imports']);

                if (false === array_search($packageConfig, array_column($packageConfigFile['imports'], 'resource'))) {
                    $packageConfigFile['imports'][$numberOfImports] = ['resource' => $packageConfig];
                }
            }

            file_put_contents($packageConfigPath, Yaml::dump($packageConfigFile, 99, 4));
        }
    }
}
