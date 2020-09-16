<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Tests\Console\Command\RemovePackage;

use DH\ArtisPackageManagerBundle\Console\Command\RemovePackage\RemoveInterfaceCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class RemoveInterfaceCommandTest extends KernelTestCase
{
    /** @var CommandTester */
    private $commandTester;

    /** @var KernelInterface */
    private $appKernel;

    protected function setUp(): void
    {
        $this->parameterBagMock = $this->getMockBuilder(ParameterBagInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->appKernel = static::createKernel();

        $application = new Application();
        $application->add(new RemoveInterfaceCommand($this->parameterBagMock));

        $command = $application->find('artis:remove-interfaces');
        $command->setPackageName('dh/artis-package-manager-bundle');
        $command->setConfigPath($this->appKernel->getProjectDir() . '/tests/fixtures/config/artis_package_manager_config.json');

        $this->commandTester = new CommandTester($command);
    }

    public function testExecute()
    {
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->assertFileExists($this->appKernel->getProjectDir() . '/tests/fixtures/AddTrait/src/Entity/CustomerInterface.php');

        $content = file_get_contents($this->appKernel->getProjectDir() . '/tests/fixtures/AddTrait/src/Entity/CustomerInterface.php');

        $this->assertStringNotContainsString(
            'use DH\ArtisPackageManagerBundle\Tests\fixtures\AddTrait\src\Entity\Traits\ArchivableInterface;',
            $content
        );
        $this->assertStringNotContainsString('interface CustomerInterface extends ArchivableInterface', $content);
        $this->assertStringContainsString('Interface has been successfully removed', $output);
    }
}
