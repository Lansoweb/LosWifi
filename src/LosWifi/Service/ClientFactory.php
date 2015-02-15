<?php
namespace LosWifi\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

final class ClientFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sl)
    {
        $options = $sl->get('loswifi.options');

        if ($options->getBackend() == 'unifi') {
            return new UnifiClient($options->getUsername(), $options->getPassword(), $options->getBaseUrl(), $options->getSite(), $options->getController(), $options->getDebug());
        }

        return null;
    }
}
