<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('dh_artis_package_manager_bundle');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
