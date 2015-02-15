<?php
namespace LosWifi\Options;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

final class ModuleOptionsFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sl)
    {
        $config = $sl->get('Configuration');

        return new ModuleOptions(array_key_exists('loswifi', $config) ? $config['loswifi'] : []);
    }
}
