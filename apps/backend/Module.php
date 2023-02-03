<?php

namespace Bincg\Backend;

use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

class Module implements ModuleDefinitionInterface
{

    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders(\Phalcon\DiInterface $di = NULL)
    {

        $loader = new Loader();

        $loader->registerNamespaces(array(
            'Bincg\Backend\Controllers' => __DIR__ . '/controllers/',
        ));

        $loader->register();
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di
     */
    public function registerServices(\Phalcon\DiInterface $di = NULL)
    {

        /**
         * Read configuration
         */
        $config = include __DIR__ . "/../../config/config.php";
		
		//Registering a dispatcher
		$di->set('dispatcher', function () use ($di) {
			$dispatcher = new \Phalcon\Mvc\Dispatcher();
			//Attach a event listener to the dispatcher
			$eventManager = new \Phalcon\Events\Manager();
			//$eventManager = $di->getShared('eventsManager');
			$eventManager->attach('dispatch', new \Security('backend'));
			$dispatcher->setEventsManager($eventManager);
			$dispatcher->setDefaultNamespace("Bincg\Backend\Controllers");
			return $dispatcher;
		});

        /**
         * Setting up the view component
         */
        $di['view'] = function () {
			$view = new \Phalcon\Mvc\View();
			$view->setViewsDir(__DIR__ . '/views/');
			$view->registerEngines(array(
				'.volt' => function ($view, $di) {
		
					$volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
		
					$volt->setOptions(array(
						'compiledPath' => __DIR__ . '/cache/',
						'compiledSeparator' => '_'
					));
		
					return $volt;
				},
				'.phtml' => '\Phalcon\Mvc\View\Engine\Php'
			));
			$view->setLayoutsDir('layouts/');
			return $view;
        };

        $di['url'] = function () {
            $url = new UrlResolver();
            $url->setBaseUri('/');
            $url->setStaticBaseUri('/');
            return $url;
        };

        $di['dbSlave'] = function () use ($config) {
            return new DbAdapter(array(
                "host" => $config->database->host,
                "username" => $config->database->username,
                "password" => $config->database->password,
                "dbname" => $config->database->name,
                'charset'=> $config->database->charset
            ));
        };
    }

}
