<?php

namespace BastSys\BackupBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class BackupExtension
 * @package BastSys\BackupBundle\DependencyInjection
 * @author mirkl
 */
class BackupExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->processConfiguration(new BackupConfiguration(), $configs);

        $config = $configs[0];
        $container->setParameter('backup.directory', $config['directory']);
        $container->setParameter('backup.maxBackups', $config['maxBackups']);
        $container->setParameter('backup.database', $config['database'] ?? null);
        $container->setParameter('backup.filesDirectory', $config['filesDirectory'] ?? null);

        $serviceLoader = new YamlFileLoader($container, new FileLocator(__DIR__ .'/../Resources/config'));
        $serviceLoader->load('command.yaml');
    }

}
