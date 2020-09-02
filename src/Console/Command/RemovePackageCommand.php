<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class RemovePackageCommand extends Command
{
    protected static $defaultName = 'artis:package:remove';

    /** @var ParameterBagInterface */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        parent::__construct();

        $this->parameterBag = $parameterBag;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Removing package.')
            ->setHelp('This command allows you to remove package with its configuration...')
            ->addArgument('packageName', InputOption::VALUE_REQUIRED, 'Set package name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packageName = $input->getArgument('packageName');

        if (false !== strpos($packageName, ":")) {
            $packageName = substr($packageName, 0, strpos($packageName, ":"));
        }

        passthru('composer remove ' . $packageName);

        $removeTraitCommand = $this->getApplication()->find('artis:remove-traits');
        $removeTraitCommand->setPackageName($packageName);

        $arguments = [
            RemovePackageCommand::getDefaultName(),
        ];

        $removeTraitInput = new ArrayInput($arguments);
        $removeTraitCommand->run($removeTraitInput, $output);

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Package has been successfully removed.</info>');
        $outputStyle->newLine();
    }
}
