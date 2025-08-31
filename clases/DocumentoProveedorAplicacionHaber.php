<?php

/**
 * @property array	$hijas
 */

class DocumentoProveedorAplicacionHaber extends DocumentoProveedorAplicacion {
	public function aplicar(DocumentoProveedorAplicacionDebe $documentoProveedorAplicacionDebe) {
		$documentoProveedorAplicacionDebe->aplicar($this);
	}

	protected function getHijas() {
		if (!isset($this->_hijas)){
			$where = 'empresa = ' . Datos::objectToDB($this->empresa) . ' AND ';
			$where .= 'cod_cancel = ' . Datos::objectToDB($this->id) . ' AND ';
			$where .= 'tipo_docum_cancel = ' . Datos::objectToDB($this->tipoDocumento);
			$this->_hijas = Factory::getInstance()->getListObject('DocumentoProveedorHija', $where);
		}
		return $this->_hijas;
	}
}

?>