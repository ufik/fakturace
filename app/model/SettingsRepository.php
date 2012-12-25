<?php
namespace Todo;
use Nette;

/**
 * Tabulka settings
 */
class SettingsRepository extends Repository{
	
	public function insert($key, $value){
		return $this->getTable()->insert(array("name" => $key, "value" => $value));
	}
	
	public function update($key, $value){
		return $this->getTable()->where('name = ?', $key)->update(array("value" => $value));
	}
	
}