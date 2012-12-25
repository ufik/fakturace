<?php

namespace App;

use Nette,
	Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	
	protected function startup(){
		parent::startup();
		
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:');
		}
	}
	
	public function beforeRender(){
		
		if ($this->presenter->isAjax()){
			$this->invalidateControl('content');
			$this->invalidateControl('actionMenu');
			$this->invalidateControl('flashMessages');
		}
		
	}
	
}
