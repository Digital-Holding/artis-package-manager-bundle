<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Console\Command\InstallPackage;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
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

    /** @var string */
    private $configPath;

    /** @var string */
    private $projectDir;

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

    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    public function setConfigPath(string $configPath): void
    {
        $this->configPath = $configPath;
    }

    public function getProjectDir(): string
    {
        return $this->projectDir;
    }

    public function setProjectDir(string $projectDir): void
    {
        $this->projectDir = $projectDir;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $configFile = file_get_contents($this->configPath);
        $config = json_decode($configFile, true);

        foreach ($config['install'] as $elementName => $elements) {
            switch ($elementName) {
                case 'config':
                    $this->addPackageConfigForConfig($elements, $this->projectDir);
                    break;
                default:
            }
        }

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Config has been successfully added</info>');
        $outputStyle->newLine();
    }

    private function addPackageConfigForConfig(array $elements, ?string $projectDir): void
    {
        foreach ($elements as $packageConfigName => $packageConfigs) {
            $packageConfigPath = $projectDir . '/' . $packageConfigName;

            $yamlParser = new Parser();
            $packageConfigFile = $yamlParser->parseFile($packageConfigPath);

            foreach ($packageConfigs['add'] as $packageConfig) {
                $numberOfImports = $packageConfigFile['imports'] ? count($packageConfigFile['imports']) : 0;

                if ($numberOfImports === 0) {
                    $packageConfigFile['imports'][$numberOfImports] = ['resource' => $packageConfig];
                    continue;
                }

                if (false === array_search($packageConfig, array_column($packageConfigFile['imports'], 'resource'))) {
                    $packageConfigFile['imports'][$numberOfImports] = ['resource' => $packageConfig];
                }
            }

            file_put_contents($packageConfigPath, Yaml::dump($packageConfigFile, 99, 4));
        }
    }
}
