<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

final class AddPackageRoutingCommand extends Command
{
    protected static $defaultName = 'artis:add-package-routing';

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
            ->setDescription('Setting up package routing.')
            ->setHelp('This command allows you to set up package routing...');
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
                case 'routing':
                    $this->addPackageRoutingForConfig($elements, $projectDir);
                    break;
                default:
            }
        }
    }

    private function addPackageRoutingForConfig(array $elements, string $projectDir): void
    {
        foreach ($elements as $packageConfigName => $packageConfigs) {
            $packageConfigPath = $projectDir . '/' . $packageConfigName;

            if (!is_file($packageConfigPath)) {
                file_put_contents($packageConfigPath, '');
            }

            $yamlParser = new Parser();
            $packageConfigFile = $yamlParser->parseFile($packageConfigPath);

            foreach ($packageConfigs['add'] as $packageConfigName => $packageConfig) {
                $packageRouting[$packageConfigName] = [
                    'resource' => $packageConfig['resource']
                ];

                if (!array_key_exists($packageConfigName, $packageConfigFile)) {
                    $packageConfigFile = array_merge($packageConfigFile, $packageRouting);
                }
            }

            file_put_contents($packageConfigPath, Yaml::dump($packageConfigFile, 99, 4));
        }
    }
}
