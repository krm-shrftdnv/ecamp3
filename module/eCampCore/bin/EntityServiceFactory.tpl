<?php

namespace eCamp\Core\EntityServiceFactory;

use eCamp\Core\EntityService\[ServiceName];
use eCamp\Core\ServiceManager\ServiceInjector;
use Interop\Container\ContainerInterface;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

final class [ServiceName]Factory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $proxyFactory = $container->get(LazyLoadingValueHolderFactory::class);

        $initializer = function(&$service, LazyLoadingInterface $proxy, $method, array $parameters, &$initializer) use ($container) {
            $service = new [ServiceName]();
            ServiceInjector::Inject($container, $service);

            $initializer = null;
            return true;
        };
        $proxy = $proxyFactory->createProxy([ServiceName]::class, $initializer);

        return $proxy;
    }
}
