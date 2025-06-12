<?php

namespace Psys\OrderInvoiceManagerBundle;

use Psys\OrderInvoiceManagerBundle\DependencyInjection\Compiler\ResolveTargetEntityPass;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;


class PsysOrderInvoiceManagerBundle extends AbstractBundle
{
    // public function configure(DefinitionConfigurator $definition): void
    // {
    //     $definition->rootNode()
    //         ->children()
    //             ->stringNode('customer_class')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
    //         ->end()
    //     ;
    // }

    // THIS CURRENTLY DOES NOT WORK
    // public function build(ContainerBuilder $container): void
    // {
    //     parent::build($container);

    //     $container->addCompilerPass(new ResolveTargetEntityPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 0);
    // }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        // $builder->setParameter('oim.customer_class', $config['customer_class']);
    }
}