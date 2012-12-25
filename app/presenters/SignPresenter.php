<?php

namespace App;

use Model\Authenticator;

use Nette,
	Model;


/**
 * Sign in/out presenters.
 */
class SignPresenter extends Nette\Application\UI\Presenter
{

	
	public function renderDefault(){
		$this->setLayout("login");
		
		$user = $this->getUser();
		if($user->isLoggedIn())
			$this->redirect("Homepage:");
	}
	
	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm(){
		$form = new Nette\Application\UI\Form;
		$form->addText('username', 'Jméno:')
			->setRequired('Zadejte přihlašovací jméno.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Zadejte heslo.');

		$form->addCheckbox('remember', 'Pamatovat si mé přihlášení?');

		$form->addSubmit('send', 'Přihlásit');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->signInFormSucceeded;
		return $form;
	}

	public function signInFormSucceeded($form){
		$values = $form->getValues();

		if ($values->remember) {
			$this->getUser()->setExpiration('+ 14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('+ 20 minutes', TRUE);
		}

		try {
			$this->getUser()->login($values->username, $values->password);
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
			return;
		}

		$this->redirect('Homepage:');
	}


	public function actionOut(){
		$this->getUser()->logout();
		$this->flashMessage('Uživatel odhlášen.');
		$this->redirect('Sign:default');
	}

}
