<?php

namespace App\RegisterModule;

use Nette\Environment;
use Venne\Application\UI;

/**
 * @resource RegisterModule
 */
class DefaultPresenter extends \Venne\Developer\Presenter\FrontPresenter {
	
	/**
	 * @privilege register
	 */
	public function startup()
	{
		parent::startup();
	}

	public function createComponentRegisterForm($name)
	{
		$form = new \Nette\Application\UI\Form;
		$form->addText('name', 'Uživatelské jméno:')
		->setRequired('Zadejte prosím jméno');
		$form->addPassword('password', 'Heslo:')
		->setRequired('Zadejte prosím heslo');
		$form->addPassword('repass', 'Heslo znovu:')
		->setRequired('Zadejte prosím heslo znovu')
		->addRule(\Nette\Application\UI\Form::EQUAL, 'Hesla se neshodují', $form['password']);
		$form->addText('email', 'Email:')
		->setRequired('Zadejte prosím email')
		->addRule(\Nette\Application\UI\Form::EMAIL, 'Zadejte platný email');
		$form->addSubmit('register', 'Registrovat');
		$form->onSuccess[] = callback($this, 'signInFormSubmitted');
				
		return $form;
	}

	public function signInFormSubmitted($form)
	{
		$values = $form->getValues();
		$this->presenter->context->services->user->create($values);
		//$this->flashMessage("Registrovan");
		$this->redirect('success');
	}


	public function beforeRender()
	{
		parent::beforeRender();

		$this->setTitle("Registrovat");
		$this->setKeywords("keyword");
		$this->setDescription("description");
		$this->setRobots(self::ROBOTS_INDEX | self::ROBOTS_FOLLOW);
	}

}