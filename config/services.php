<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Psys\OrderInvoiceManagerBundle\Model\OrderManager\OrderManager;
use Psys\OrderInvoiceManagerBundle\Repository\OrderRepository;
use Psys\Utils\Math;

return function(ContainerConfigurator $container): void 
{
    $container->services()

        ->set('oim.order_manager', OrderManager::class)
            ->args([
                service('doctrine.orm.default_entity_manager'),
                service('filesystem'),
                service('psys_utils.math'),
            ])
            ->alias(OrderManager::class, 'oim.order_manager')
        
        ->set('oim.order_repository', OrderRepository::class)
        ->args([
            service('doctrine')
        ])
        ->alias(OrderRepository::class, 'oim.order_repository')
        
        ->set('psys_utils.math', Math::class)
    ;
}; 