<?php

/**
 * @property int	$posicionInicial
 * @property int	$posicionFinal
 */

class RangoTalle extends Base {
	const		_primaryKey = '["id"]';

	public		$id;			//También tiene un id varchar(2)
	public		$nombre;
	public		$anulado;
	public		$punto;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;
	public		$posicion;		//Array de 1 a 8
	protected	$_posicionInicial;
	protected	$_posicionFinal;

	//GETS y SETS
	protected function getPosicionFinal(){
		if (!isset($this->_posicionFinal)){
			$lastPos = '';
			foreach($this->posicion as $pos)
				if ($pos != null)
					$lastPos = $pos;
			$this->_posicionFinal = Funciones::toInt($lastPos);
		}
		return $this->_posicionFinal;
	}
	protected function getPosicionInicial(){
		if (!isset($this->_posicionInicial)){
			$this->_posicionInicial = $this->posicion[1];
		}
		return $this->_posicionInicial;
	}
}

?>