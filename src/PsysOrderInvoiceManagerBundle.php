<?php

namespace Psys\OrderInvoiceManagerBundle;

use Psys\OrderInvoiceManagerBundle\Model\CustomerInterface;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;


class PsysOrderInvoiceManagerBundle extends AbstractBundle implements CompilerPassInterface
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->stringNode('customer_class')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;
    }

    public function process(ContainerBuilder $container): void
    {
        $target = $container->getParameter('oim.customer_class');
        $def = $container->getDefinition('doctrine.orm.listeners.resolve_target_entity');
        $def->addMethodCall('addResolveTargetEntity', [
            CustomerInterface::class,
            $target,
            [],
        ]);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        $builder->setParameter('oim.customer_class', $config['customer_class']);
    }
}