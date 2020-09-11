<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Console\Command\InstallPackage;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Traitor\Traitor;

final class AddInterfaceCommand extends Command
{
    protected static $defaultName = 'artis:add-interfaces';

    /** @var Traitor */
    private $traitor;

    /** @var ParameterBagInterface */
    private $parameterBag;

    /** @var string */
    private $packageName;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        parent::__construct();

        $this->traitor = new Traitor();
        $this->parameterBag = $parameterBag;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Setting up interfaces.')
            ->setHelp('This command allows you to set up interfaces...');
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
                case 'interface':
                    $this->addInterfacesForConfig($elements);
                    break;
                default:
            }
        }

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Interface has been successfully added</info>');
        $outputStyle->newLine();
    }

    private function addInterfacesForConfig(array $elements): void
    {
        foreach ($elements as $entity => $interfaces) {
            foreach ($interfaces['add'] as $interface) {
                $interfaceAssigned = $this->traitor->alreadyUsesInterface($entity, $interface);

                if (!$interfaceAssigned) {
                    $this->traitor->addTrait($interface)->toInterface($entity);
                }
            }
        }
    }
}
