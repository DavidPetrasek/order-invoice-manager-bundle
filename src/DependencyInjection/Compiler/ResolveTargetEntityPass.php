<?php
namespace Psys\OrderInvoiceManagerBundle\DependencyInjection\Compiler;

use Psys\OrderInvoiceManagerBundle\Model\CustomerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;


// THIS CURRENTLY DOES NOT WORK
//
class ResolveTargetEntityPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $target = $container->getParameter('oim.customer_class');

        $config = $container->getExtensionConfig('doctrine');

        $mapping = isset($config[0]['orm']['resolve_target_entities']) ? $config[0]['orm']['resolve_target_entities'] : [];

        $mapping[CustomerInterface::class] = $target;

        $container->setParameter('doctrine.orm.resolve_target_entities', $mapping);
    }
}