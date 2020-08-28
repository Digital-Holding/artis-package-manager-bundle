<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Traitor\Traitor;

final class RemoveTraitCommand extends Command
{
    protected static $defaultName = 'artis:remove-traits';

    /** @var Traitor */
    private $traitor;

    /** @var ParameterBagInterface */
    private $parameterBag;

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectDir = $this->parameterBag->get('kernel.project_dir');

        $configPath = $projectDir . '/src/Resources/config/artis_package_manager_config.json';
        $configFile = file_get_contents($configPath);
        $config = json_decode($configFile, true);

        foreach ($config['install'] as $entity => $traits) {
            foreach ($traits['add'] as $trait) {
                $traitAssigned = $this->traitor->alreadyUses($entity, $trait);

                if ($traitAssigned) {
                    $this->traitor->removeTrait($trait)->toClass($entity);
                }
            }
        }
    }
}
