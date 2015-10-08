<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PageCache;

use Zend\Cache\Storage\ClearByNamespaceInterface;
use Zend\Cache\Storage\ClearExpiredInterface;
use Zend\Cache\StorageFactory;
use Zend\Http\Response;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\RequestInterface;
use Zend\Cache\Storage\StorageInterface;
use Zend\ModuleManager\ModuleManager;

class Module
{
    public function init(ModuleManager $moduleManager)
    {
        $config = $this->getConfig();
        $cache = StorageFactory::factory($config['page-cache']['cache']);

        if(filter_input(INPUT_GET, 'clear-cache')) {
            if($cache instanceof ClearByNamespaceInterface) {
                $cache->clearByNameSpace($config['page-cache']['cache']['adapter']['options']['namespace']);
            }
        }

        if(filter_input(INPUT_GET, 'clear-expired')) {
            if($cache instanceof ClearExpiredInterface) {
                $cache->clearExpired();
            }
        }

        $responseStr = $cache->getItem($this->getKey(), $success);
        if($success) {
            $response = Response::fromString($responseStr);
            foreach($response->getHeaders() as $header) {
                header($header->toString());
            }
            echo $response->getBody();
            exit();
        }
    }

    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_FINISH, function(MvcEvent $e) {
            $config = $e->getApplication()->getServiceManager()->get('Config');
            $routeMatch = $e->getRouteMatch();
            if(!($routeMatch instanceof RouteMatch)) return;
            if(empty($config['page-cache'])) return;
            if(empty($config['page-cache']['page-to-cache'])) return;
            foreach($config['page-cache']['page-to-cache'] as $match) {
                if((strtolower($match['controller']) == strtolower($routeMatch->getParam('controller'))) && (strtolower($match['action']) == strtolower($routeMatch->getParam('action')))) {
                    $cache = $e->getApplication()->getServiceManager()->get('PageCache\Model\Cache');
                    $response = $e->getResponse();
                    $response->getHeaders()->addHeaderLine('Cache-Created', date("D M j G:i:s T Y"));
                    $cache->addItem($this->getKey(), $e->getResponse()->toString());
                    break;
                }
            }
        });
    }

    public function getKey()
    {
        return md5(strtolower($_SERVER['REQUEST_URI']));
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
