<?php
namespace Todo;
use Nette;

/**
 * Tabulka serie
 */
class SerieRepository extends Repository{
	
	public function createSerie($data){
		return $this->getTable()->insert($data);
	}
	
	public function deleteSerie($idSerie){
		return $this->getTable()->where('id_serie = ?', $idSerie)->delete();
	}
	
	public function updateSerie($data){
		return $this->getTable()->where('id_serie = ?', $data["id_serie"])->update($data);
	}
	
}