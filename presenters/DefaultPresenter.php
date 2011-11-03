<?php

namespace App\RegisterModule;

use Nette\Environment;
use Venne\Application\UI;

\App\RegisterModule\CaptchaControl::register();

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
		$form = new \Venne\Forms\Form($this,$name);
		$form->addGroup('Přihlašovací údaje');
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

		$form->addGroup('Osobní údaje');
		$form->addText('firstname',"Jméno:")
		->setRequired('Zadejte prosím jméno');
		$form->addText('lastname',"Příjmení:")
		->setRequired('Zadejte prosím příjmení');


		$form->addDate('birthdate',"Datum narození")
		->addRule(\Nette\Forms\Form::FILLED, 'Zadejte datum narození');

		$form->addText('street',"Ulice:")
		->addRule(\Nette\Forms\Form::FILLED, 'Zadejte ulici');
		$form->addText('streetnumber',"Číslo popisné:")
		->addRule(\Nette\Forms\Form::FILLED, 'Zadejte číslo popisné');
		$form->addText('city',"Město:")
		->addRule(\Nette\Forms\Form::FILLED, 'Zadejte město');
		$form->addText('zipcode',"PSČ:")
		->addRule(\Nette\Forms\Form::FILLED, 'Zadejte PSČ ve tvaru 12345')
		->addRule(\Nette\Forms\Form::LENGTH, 'Zadejte PSČ ve tvaru 12345',5)
		->addRule(\Nette\Forms\Form::INTEGER, 'Zadejte PSČ ve tvaru 12345',5);

		$form->addGroup('Podmínky registrace');
		$form->addCheckbox('sendmail',"Posílat e-maily");
		$form->addCheckbox('accept',"Souhlasím s podmínkami registrace")
		->addRule(\Nette\Forms\Form::FILLED, 'Musíte souhlasit s podmínkami');
		$form->addCaptcha('captcha')
		->addRule(\Nette\Forms\Form::FILLED, "Opište text z obrázku.")
		->addRule($form["captcha"]->getValidator(), 'Špatně opsaný text. Zkuste to znovu')
		->setFontSize(25)
		->setLength(5) //word length
		->setTextMargin(20) //px, set text margin on left and rigth side
		->setTextColor(\Nette\Image::rgb(0,0,0)) //array("red" => 0-255, "green" => 0-255, "blue" => 0-255)
		->setBackgroundColor(\Nette\Image::rgb(240,240,240)) //array("red" => 0-255, "green" => 0-255, "blue" => 0-255)
		->setImageHeight(60) //px, if not set (0), image height will be generated by font size
		->setImageWidth(0) //px, if not set (0), image width will be generated by font size
		->setExpire(1000) //ms, set expiration time to seession
		->setFilterSmooth(false) //int or false (disable)
		->setFilterContrast(false)  //int or false (disable)
		->useNumbers(false); // bool or void

		$form->addProtection('Vypršel časový limit, odešlete formulář znovu');
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