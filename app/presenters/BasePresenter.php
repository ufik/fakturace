<?php

namespace App;

use Nette\Security\Permission;
use Nette\Security\User;

use Nette,	Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	
	protected function startup(){
		parent::startup();
		
		setlocale(LC_ALL, 'cs_CZ.utf8');
		
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:');
		}
		
		// overeni opravneni uzivatele
		$acl = new Nette\Security\Permission;
		
		// definice roli
		$acl->addRole('skladnik');
		$acl->addRole('administrator', 'skladnik');
		
		// definice zdroju
		$acl->addResource('error');
		$acl->addResource('homepage');
		$acl->addResource('contacts');
		$acl->addResource('invoices');
		$acl->addResource('products');
		$acl->addResource('series');
		$acl->addResource('settings');
		$acl->addResource('store');
		$acl->addResource('users');
		
		// propojeni roli a zdroju
		$acl->allow('skladnik', array('store', 'homepage'), Permission::ALL);
		
		// administrátor může prohlížet a editovat cokoliv
		$acl->allow('administrator', Permission::ALL, Permission::ALL);
		
		$roles = $this->getUser()->getRoles();
		
		
		$hasRigths = false;
		$check = false;
		foreach($roles as $r){
			$check = $acl->isAllowed($r, lcfirst($this->name), $this->action);
				
			if($check)
				$hasRigths = true;
		}
		
		if(!$hasRigths){
			$this->presenter->flashMessage("Nemáte oprávnění pro tuto operaci!", 'error');
			$this->redirect("Homepage:");
		}

	}
	
	public function beforeRender(){
		
		if ($this->presenter->isAjax()){
			$this->invalidateControl('content');
			$this->invalidateControl('actionMenu');
			$this->invalidateControl('flashMessages');
			
			$this->payload->presenter = $this->presenter->getName();
			
		}
		
		$this->template->user = $this->getUser()->getIdentity();
	}
	
}
