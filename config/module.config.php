<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'page-cache' => array(
        'cache' => array(
            'adapter' => array(
                'name' => 'filesystem',
                'options' => array(
                    'ttl' => 300,
                    'namespace' => 'page-cache',
                    'cache_dir' => TEMP_DIR,
                ),
            ),
            'plugins' => array(
                'exception_handler' => array(
                    'throw_exceptions' => true,
                ),
            )
        ),
        'page-to-cache' => array(
            array(
                'controller' => 'NewsFeed\Controller\Index',
                'action' => 'index',
            ),
            array(
                'controller' => 'NewsFeed\Controller\Service',
                'action' => 'get-post',
            ),
            array(
                'controller' => 'NewsFeed\Controller\Service',
                'action' => 'list-post',
            ),
            array(
                'controller' => 'NewsFeed\Controller\Service',
                'action' => 'list-video',
            ),
            array(
                'controller' => 'NewsFeed\Controller\Ng',
                'action' => 'index',
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'PageCache\Model\Cache' => 'PageCache\Model\Cache\CacheServiceFactory',
        ),
    ),
);
