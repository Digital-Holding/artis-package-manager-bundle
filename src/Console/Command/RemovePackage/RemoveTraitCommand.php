<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Console\Command\RemovePackage;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Traitor\Traitor;

final class RemoveTraitCommand extends Command
{
    protected static $defaultName = 'artis:remove-traits';

    /** @var Traitor */
    private $traitor;

    /** @var ParameterBagInterface */
    private $parameterBag;

    /** @var string */
    private $packageName;

    /** @var string */
    private $configPath;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        parent::__construct();

        $this->traitor = new Traitor();
        $this->parameterBag = $parameterBag;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Removing traits.')
            ->setHelp('This command allows you to remove traits...');
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

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $configFile = file_get_contents($this->configPath);
        $config = json_decode($configFile, true);

        foreach ($config['install'] as $elementName => $elements) {
            switch ($elementName) {
                case 'trait':
                    $this->removeTraitsForConfig($elements);
                    break;
                default:
            }
        }

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Trait has been successfully removed</info>');
        $outputStyle->newLine();
    }

    private function removeTraitsForConfig(array $elements): void
    {
        foreach ($elements as $entity => $traits) {
            foreach ($traits['add'] as $trait) {
                $traitAssigned = $this->traitor->alreadyUses($entity, $trait);

                if ($traitAssigned) {
                    $this->traitor->removeTrait($trait)->toClass($entity);
                }
            }
        }
    }
}
