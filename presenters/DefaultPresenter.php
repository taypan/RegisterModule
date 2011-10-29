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
		$values['enable'] = false;
		$entity = $this->presenter->context->services->register->create($values);

		$hash = substr(md5($entity->salt . $entity->email), 0, 20);
		$adminMail = $this->context->params['adminMail'];
				
		$link = $this->link('//confirm',array("hash" => $hash, "email" => $values->email));
		$mail = new \Nette\Mail\Message;
		$mail->setFrom("Registrace <$adminMail>")  // gmail pravdepodobne ignoruje
		->addTo($values->email)
		->setSubject("Potvrzení registrace")
		->setBody("Pro dokončení registrace navštivte následující adresu: $link");


		try {
			$this->send($mail);
		} catch (Exception $e) {
			$msg = "Odeslání selhalo!";
		}

		
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

	public function renderConfirm($email,$hash){

		$entity = $this->context->services->register->getRepository()->findOneBy(array("email" => $email));
		if(!$entity){
			$this->template->success = false;
			return;
		}

		$hashDB = substr(md5($entity->salt . $entity->email), 0, 20);

		if($hash == $hashDB){
			$this->template->success = true;
			$entity->enable = true;
		} else {
			$this->template->success = false;
			return;
		}

		$this->presenter->context->doctrineContainer->entityManager->flush();


			
	}


	public function contactFormSubmitted($form)
	{
		$values = $form->getValues();
		$msg = "Odesláno!";

		$mail = new \Nette\Mail\Message;
		$mail->setFrom("$values->name <$values->email>")  // gmail pravdepodobne ignoruje
		->addTo($this->context->params["adminMail"])
		->setSubject("Zpráva - Venne")
		->setBody($values->message);


		try {
			$this->send($mail);
		} catch (Exception $e) {
			$msg = "Odeslání selhalo!";
		}
		$this->flashMessage($msg);
		$this->redirect('this');

	}


	public function send($mail){
		$mailer = new \Nette\Mail\SmtpMailer($this->context->params["smtp"]);
		$mailer->send($mail);

	}
























}