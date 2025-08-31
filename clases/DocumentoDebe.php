<?php

/**
 * @property array $hijas
 */

class DocumentoDebe extends Documento {
	protected	$_hijas;

	//GETS y SETS
	protected function getHijas() {
		if (!isset($this->_hijas)){
			$where = 'anulada <> \'S\' AND ';
			$where .= 'cancel_punto_venta = ' . Datos::objectToDB($this->puntoDeVenta) . ' AND ';
			$where .= 'cancel_tipo_docum  = ' . Datos::objectToDB($this->tipoDocumento) . ' AND ';
			$where .= 'cancel_nro_documento = ' . Datos::objectToDB($this->numero) . ' AND ';
			$where .= 'cancel_letra = ' . Datos::objectToDB($this->letra) . ' AND ';
			$where .= 'empresa = ' . Datos::objectToDB($this->empresa);
			$this->_hijas = Factory::getInstance()->getListObject('DocumentoHija', $where);
		}
		return $this->_hijas;
	}
	protected function setHijas($hijas) {
		$this->_hijas = $hijas;
		return $this;
	}
}

?>