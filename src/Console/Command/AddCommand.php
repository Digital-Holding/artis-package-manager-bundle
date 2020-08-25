<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Traitor\Traitor;

final class AddCommand extends Command
{
    protected static $defaultName = 'artis:add-traits';

    /** @var Traitor */
    private $traitor;

    public function __construct()
    {
        parent::__construct();

        $this->traitor = new Traitor();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Setting up traits.')
            ->setHelp('This command allows you to set up traits...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $class = '';
        $traitClass = '';

        $traitAssigned = $this->traitor->alreadyUses($class, $traitClass);

        if (!$traitAssigned) {
            $this->traitor->addTrait($traitClass)->toClass($class);
        }
    }
}
