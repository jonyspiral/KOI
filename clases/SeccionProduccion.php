<?php

/**
 * @property array					$subordinadas
 * @property SeccionProduccion		$seccionSuperior
 * @property UnidadDeMedida			$unidadDeMedida
 * @property AlmacenPorSeccion		$almacenDefault
 * @property AlmacenPorSeccion[]	$almacenes
 */

class SeccionProduccion extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$anulado;
	public		$nombre;
	public		$nombreCorto;
	public		$idUnidadDeMedida;
	protected	$_unidadDeMedida;
	public		$interrumpible;
	public		$color;
	public		$imprimeStickers;
	public		$jerarquiaSeccion;		// "P"rincipal o "S"ubordinado. No están bien los datos en la DB
	//public $tieneSubordinadas;	// No lo necesito. Uso el count de _subordinadas
	protected	$_subordinadas;
	public		$idSeccionSuperior;
	protected	$_seccionSuperior;
	public		$idAlmacenDefault;
	protected	$_almacenDefault;
	public		$ingresaAlStock;		// "N"
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	protected	$_almacenes;

    public function addAlmacen(Almacen $almacen) {
        if (!isset($this->_almacenes)) {
            $this->_almacenes = array();
        }
        $almacenPorSeccion = Factory::getInstance()->getAlmacenPorSeccion();
        $almacenPorSeccion->id = $almacen->id;
        $almacenPorSeccion->idSeccionProduccion = $this->id;
        $this->_almacenes[] = $almacenPorSeccion;
    }

    public function borrar() {
        foreach ($this->almacenes as $almacenPorSeccion) {
            Factory::getInstance()->marcarParaBorrar($almacenPorSeccion);
        }
        return parent::borrar();
    }

	//GETS y SETS
    protected function getAlmacenDefault() {
        if (!isset($this->_almacenDefault)){
            $this->_almacenDefault = Factory::getInstance()->getAlmacenPorSeccion($this->idAlmacenDefault, $this->id);
        }
        return $this->_almacenDefault;
    }
    protected function setAlmacenDefault($almacenDefault) {
        $this->_almacenDefault = $almacenDefault;
        return $this;
    }
	protected function getAlmacenes() {
		if (!isset($this->_almacenes) && isset($this->id)){
			$this->_almacenes = Factory::getInstance()->getListObject('AlmacenPorSeccion', 'cod_seccion = ' . Datos::objectToDB(Funciones::toString($this->id)));
		}
		return $this->_almacenes;
	}
	protected function setAlmacenes($almacenes) {
		$this->_almacenes = $almacenes;
		return $this;
	}
	protected function getSeccionSuperior() {
		if (!isset($this->_seccionSuperior)){
			$this->_seccionSuperior = Factory::getInstance()->getSeccionProduccion($this->idSeccionSuperior);
		}
		return $this->_seccionSuperior;
	}
	protected function setSeccionSuperior($seccionSuperior) {
		$this->_seccionSuperior = $seccionSuperior;
		return $this;
	}
	protected function getSubordinadas() {
		if (!isset($this->_subordinadas) && isset($this->id)){
			$this->_subordinadas = Factory::getInstance()->getListObject('SeccionProduccion', 'subordinada_de_seccion = ' . Datos::objectToDB($this->id));
		}
		return $this->_subordinadas;
	}
	protected function setSubordinadas($subordinadas) {
		$this->_subordinadas = $subordinadas;
		return $this;
	}
	protected function getUnidadDeMedida() {
		if (!isset($this->_unidadDeMedida) && isset($this->idUnidadDeMedida)){
			$this->_unidadDeMedida = Factory::getInstance()->getListObject('UnidadDeMedida', 'cod_unidad = ' . Datos::objectToDB($this->idUnidadDeMedida));
		}
		return $this->_unidadDeMedida;
	}
	protected function setUnidadDeMedida($unidadDeMedida) {
		$this->_unidadDeMedida = $unidadDeMedida;
		return $this;
	}
}

?>