<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Tests\Console\Command\InstallPackage;

use DH\ArtisPackageManagerBundle\Console\Command\InstallPackage\AddPackageConfigCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class AddPackageConfigCommandTest extends KernelTestCase
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
        $application->add(new AddPackageConfigCommand($this->parameterBagMock));

        $command = $application->find('artis:add-package-config');
        $command->setPackageName('dh/artis-package-manager-bundle');
        $command->setConfigPath($this->appKernel->getProjectDir() . '/tests/fixtures/config/artis_package_manager_config.json');
        $command->setProjectDir($this->appKernel->getProjectDir());

        $this->commandTester = new CommandTester($command);
    }

    public function testExecute()
    {
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->assertFileExists($this->appKernel->getProjectDir() . '/tests/fixtures/config/main_config.yml');

        $content = file_get_contents($this->appKernel->getProjectDir() . '/tests/fixtures/config/main_config.yml');

        $this->assertStringContainsString(
            "resource: '@DHArtisPackageManagerBundle/tests/fixtures/config/external_config.yml'",
            $content
        );
        $this->assertStringContainsString('Config has been successfully added', $output);
    }
}
