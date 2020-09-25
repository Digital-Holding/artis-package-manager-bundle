<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Tests\Console\Command;

use DH\ArtisPackageManagerBundle\Console\Command\InstallPackageCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class InstallPackageCommandTest extends KernelTestCase
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
        $application->add(new InstallPackageCommand($this->parameterBagMock));

        $command = $application->find('artis:package:install');
        $command->setPackageName('dh/artis-package-manager-bundle');
        $command->setConfigPath($this->appKernel->getProjectDir() . '/tests/fixtures/config/artis_package_manager_config.json');

        $this->commandTester = new CommandTester($command);
    }

    public function testExecute()
    {
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->

        $this->assertFileExists($this->appKernel->getProjectDir() . '/tests/fixtures/AddTrait/src/Entity/CustomerInterface.php');

        $content = file_get_contents($this->appKernel->getProjectDir() . '/tests/fixtures/AddTrait/src/Entity/CustomerInterface.php');

        $this->assertStringContainsString(
            'use DH\ArtisPackageManagerBundle\Tests\fixtures\AddTrait\src\Entity\Traits\ArchivableInterface;',
            $content
        );
        $this->assertStringContainsString('interface CustomerInterface extends ArchivableInterface', $content);
        $this->assertStringContainsString('Interface has been successfully added', $output);
    }
}
