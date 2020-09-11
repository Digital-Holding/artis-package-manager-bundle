<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Tests\Console\Command\InstallPackage;

use DH\ArtisPackageManagerBundle\Console\Command\InstallPackage\AddInterfaceCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AddInterfaceCommandTest extends KernelTestCase
{
    /** @var CommandTester */
    private $commandTester;

    protected function setUp(): void
    {
        $this->parameterBagMock = $this->getMockBuilder(ParameterBagInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $application = new Application();
        $application->add(new AddInterfaceCommand($this->parameterBagMock));
        $command = $application->find('artis:add-interfaces');
        $this->commandTester = new CommandTester($command);
    }

    public function testExecute()
    {
        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Interface has been successfully added', $output);
    }
}
