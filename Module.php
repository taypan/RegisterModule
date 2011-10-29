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
					'action' => 'default'
		)
		);
		$router[] = new \Nette\Application\Routers\Route($prefix . 'confirm/<id>/<hash>', array(
							'module' => 'Register',
							'presenter' => 'Default',
							'action' => 'confirm'
		)
		);
	}


	public function setServices(\Venne\Application\Container $container)
	{
		parent::setServices($container);
		$container->services->addService("register", function() use ($container) {
			return new \App\SecurityModule\UserService($container, "user", $container->doctrineContainer->entityManager);
		}
		);
	}

}
