<?php

/**
 * @property int						$tipoImporte
 * @property ImportePorOperacionItem	$importePorOperacionItem
 */

abstract class Importe extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$empresa;
	public		$importe;
	protected	$_tipoImporte;
	public		$idImportePorOperacionItem;
	protected 	$_importePorOperacionItem;
	public		$arrayImportes = array();

	public abstract function getTipoImporte();
	public abstract function getImputacion();
	public abstract function getObservacionContabilidad();

	public function simularArrayImportes(){
		if(!isset($this->arrayImportes[$this->getTipoImporte()])){
			$this->arrayImportes[$this->getTipoImporte()] = array();
		}

		return $this->arrayImportes;
	}

	public static function validar($obj) {
		if (isset($obj['importe']) && $obj['importe'] < 0) {
			throw new FactoryExceptionCustomException('No se pueden ingresar importes negativos');
		}
	}

	//GETS y SETS
	protected function getImportePorOperacionItem() {
		if (!isset($this->_importePorOperacionItem)){
			$where = 'anulado = ' . Datos::objectToDB('N') . ' AND tipo_importe = ' . Datos::objectToDB($this->getTipoImporte()) . ' AND cod_importe = ' . Datos::objectToDB($this->id);
			$order = ' ORDER BY cod_importe_operacion DESC';
			$arr = Factory::getInstance()->getListObject('ImportePorOperacionItem', $where . $order);
			if (!count($arr)) {
				throw new FactoryExceptionCustomException('El importe no tiene ninguna operación vinculada');
			}
			$this->_importePorOperacionItem = $arr[0];
		}
		return $this->_importePorOperacionItem;
	}
	protected function setImportePorOperacionItem($importePorOperacionItem) {
		$this->_importePorOperacionItem = $importePorOperacionItem;
		return $this;
	}
}