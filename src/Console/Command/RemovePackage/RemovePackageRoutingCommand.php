<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Console\Command\RemovePackage;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

final class RemovePackageRoutingCommand extends Command
{
    protected static $defaultName = 'artis:remove-package-routing';

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
            ->setDescription('Removing package routing.')
            ->setHelp('This command allows you to remove package routing...');
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
                case 'routing':
                    $this->removePackageRoutingForConfig($elements, $this->projectDir);
                    break;
                default:
            }
        }

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Routing has been successfully removed</info>');
        $outputStyle->newLine();
    }

    private function removePackageRoutingForConfig(array $elements, string $projectDir): void
    {
        foreach ($elements as $packageConfigName => $packageConfigs) {
            $packageRoutingPath = $projectDir . '/' . $packageConfigName;

            if (!is_file($packageRoutingPath)) {
                file_put_contents($packageRoutingPath, '');
            }

            $yamlParser = new Parser();
            $packageConfigFile = $yamlParser->parseFile($packageRoutingPath);

            if (null === $packageConfigFile) {
                continue;
            }

            foreach ($packageConfigs['add'] as $packageRoutingName => $packageConfig) {
                if (array_key_exists($packageRoutingName, $packageConfigFile)) {
                    unset($packageConfigFile[$packageRoutingName]);
                }
            }

            file_put_contents($packageRoutingPath, Yaml::dump($packageConfigFile, 99, 4));
        }
    }
}
