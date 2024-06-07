<?php

namespace Psys\SimpleOrderInvoice;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;


class SimpleOrderInvoiceBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('twitter')
                    ->children()
                        ->integerNode('client_id')->end()
                        ->scalarNode('client_secret')->end()
                    ->end()
                ->end() // twitter
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // the "$config" variable is already merged and processed so you can
        // use it directly to configure the service container (when defining an
        // extension class, you also have to do this merging and processing)
        $containerConfigurator->services()
            ->get('acme_social.twitter_client')
            ->arg(0, $config['twitter']['client_id'])
            ->arg(1, $config['twitter']['client_secret'])
        ;
    }
}