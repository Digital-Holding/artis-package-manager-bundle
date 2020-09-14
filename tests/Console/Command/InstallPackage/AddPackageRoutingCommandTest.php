<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Tests\Console\Command\InstallPackage;

use DH\ArtisPackageManagerBundle\Console\Command\InstallPackage\AddPackageRoutingCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class AddPackageRoutingCommandTest extends KernelTestCase
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
        $application->add(new AddPackageRoutingCommand($this->parameterBagMock));

        $command = $application->find('artis:add-package-routing');
        $command->setPackageName('dh/artis-package-manager-bundle');
        $command->setConfigPath($this->appKernel->getProjectDir() . '/tests/fixtures/config/artis_package_manager_config.json');
        $command->setProjectDir($this->appKernel->getProjectDir());

        $this->commandTester = new CommandTester($command);
    }

    public function testExecute()
    {
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->assertFileExists($this->appKernel->getProjectDir() . '/tests/fixtures/config/main_routing.yml');

        $content = file_get_contents($this->appKernel->getProjectDir() . '/tests/fixtures/config/main_routing.yml');

        $this->assertStringContainsString('external_routing:', $content);
        $this->assertStringContainsString(
            "resource: '@DHArtisPackageManagerBundle/tests/fixtures/config/external_routing.yml'",
            $content
        );
        $this->assertStringContainsString('Routing has been successfully added', $output);
    }
}
