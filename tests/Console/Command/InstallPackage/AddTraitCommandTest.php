<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Tests\Console\Command\InstallPackage;

use DH\ArtisPackageManagerBundle\Console\Command\InstallPackage\AddTraitCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class AddTraitCommandTest extends KernelTestCase
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
        $application->add(new AddTraitCommand($this->parameterBagMock));

        $command = $application->find('artis:add-traits');
        $command->setPackageName('dh/artis-package-manager-bundle');
        $command->setConfigPath($this->appKernel->getProjectDir() . '/tests/fixtures/config/artis_package_manager_config.json');

        $this->commandTester = new CommandTester($command);
    }

    public function testExecute()
    {
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->assertFileExists($this->appKernel->getProjectDir() . '/tests/fixtures/AddTrait/src/Entity/Customer.php');

        $content = file_get_contents($this->appKernel->getProjectDir() . '/tests/fixtures/AddTrait/src/Entity/Customer.php');

        $this->assertStringContainsString(
            'use DH\ArtisPackageManagerBundle\Tests\fixtures\AddTrait\src\Entity\Traits\ArchivableTrait;',
            $content
        );
        $this->assertStringContainsString('use ArchivableTrait;', $content);
        $this->assertStringContainsString('Trait has been successfully added', $output);
    }
}
