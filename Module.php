<?php

namespace App\RegisterModule;

use \Venne\Developer\Module\Service\IRouteService;

/**
 * @author Jiří Müller
 */
class Module extends \Venne\Developer\Module\AutoModule {


	public function getName()
	{
		return "register";
	}


	public function getDescription()
	{
		return "Basic register";
	}


	public function getVersion()
	{
		return "0.1";
	}


	public function setRoutes(\Nette\Application\Routers\RouteList $router, $prefix = "")
	{
		$router[] = new \Nette\Application\Routers\Route($prefix . '<action>', array(
					'module' => 'Register',
					'presenter' => 'Default',
					'action' => 'default',
					'url' => array(
		\Nette\Application\Routers\Route::VALUE => NULL,
		\Nette\Application\Routers\Route::FILTER_IN => NULL,
		\Nette\Application\Routers\Route::FILTER_OUT => NULL,
		)
		)
		);
	}


	public function setServices(\Venne\Application\Container $container)
	{
		parent::setServices($container);
		$container->services->addService("user", function() use ($container) {
			return new \App\SecurityModule\UserService($container, "user", $container->doctrineContainer->entityManager);
		}
		);
	}

}
