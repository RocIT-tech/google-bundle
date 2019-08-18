<?php

declare(strict_types=1);

namespace RocIT\GoogleMapBundle\DependencyInjection;

use RocIT\GoogleMapBundle\Twig\GoogleExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class GoogleMapExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');

        $container
            ->getDefinition(GoogleExtension::class)
            ->setArgument('$googleApiKey', $config['api_key'])
            ->setArgument('$defaultOptions', true === empty($config['default_options']) ? null : $config['default_options'])
        ;
    }
}
