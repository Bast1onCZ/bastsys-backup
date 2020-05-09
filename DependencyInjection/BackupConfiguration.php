<?php

namespace App\BackupBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class BackupConfiguration
 * @package App\BackupBundle\DependencyInjection
 * @author mirkl
 */
class BackupConfiguration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('backup');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('directory')->isRequired()->end()
                ->integerNode('maxBackups')->isRequired()->min(1)->end()
                ->scalarNode('database')->defaultNull()->end()
                ->scalarNode('filesDirectory')->defaultNull()->end()
            ->end();

        return $treeBuilder;
    }

}
