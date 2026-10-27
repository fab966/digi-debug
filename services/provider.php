<?php
defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Digitest\Plugin\System\DigitestDebug\Extension\DigitestDebug;
use Joomla\Event\DispatcherInterface;

return new class () implements ServiceProviderInterface {
    public function register(Container $container) {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $dispatcher = $container->get(DispatcherInterface::class);
                $plugin     = new DigitestDebug(
                    $dispatcher,
                    (array) PluginHelper::getPlugin('system', 'digitestdebug')
                );
                return $plugin;
            }
        );
    }
};