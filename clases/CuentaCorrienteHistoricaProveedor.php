<?php

/**
 * @property Proveedor				$proveedor
 * @property Array					$documentosPorFecha
 */

class CuentaCorrienteHistoricaProveedor extends CuentaCorriente {
	public		$idProveedor;
	protected	$_proveedor;
	public		$empresa;
	public		$fechaDesde;
	public		$fechaHasta;
	protected	$_documentosPorFecha;		//Lista de CuentaCorrienteHistoricaDocumento
	protected	$_saldoInicial;

	//GETS y SETS
	protected function getDocumentosPorFecha() {
		if (!isset($this->_documentosPorFecha)){
			$where = ($this->empresa == 1 || $this->empresa == 2 ? '(empresa = ' . Datos::objectToDB($this->empresa) . ') AND ' : '');

			$strFechas = Funciones::strFechas($this->fechaDesde, $this->fechaHasta, 'fecha');

			if(isset($this->fechaDesde) || isset($this->fechaHasta)){
				$where .= $strFechas . ' AND ';
			}

			$where .= 'cod_proveedor = ' . Datos::objectToDB($this->idProveedor) . ' AND ';
			$where = trim($where, ' AND ');
			$orderBy = ' ORDER BY fecha ASC, tipo_docum ASC, nro_documento ASC, empresa ASC';

			$this->_documentosPorFecha = Factory::getInstance()->getListObject('CuentaCorrienteHistoricaDocumentoProveedor', $where . $orderBy);
		}
		return $this->_documentosPorFecha;
	}
	protected function setDocumentosPorFecha($documentosPorFecha) {
		$this->_documentosPorFecha = $documentosPorFecha;
		return $this;
	}
	public function getSaldosIniciales() {
		if (!isset($this->_saldoInicial)){
			$where = ($this->empresa == 1 || $this->empresa == 2 ? '(empresa = ' . Datos::objectToDB($this->empresa) . ') AND ' : '');
			$where .= 'cod_proveedor = ' . Datos::objectToDB($this->idProveedor) . ' AND ';
			$where .= Funciones::strFechas($nullVar, Funciones::sumarTiempo($this->fechaDesde, -1), 'fecha') . ' AND ';
			$where = trim($where, ' AND ');

			$array = Factory::getInstance()->getArrayFromView('cuenta_corriente_historica_proveedor_v', $where, 1, 'SUM(case when empresa = ' . Datos::objectToDB('1') . ' then importe_total else 0 end) saldo1, SUM(case when empresa = ' . Datos::objectToDB('1') . ' then 0 else importe_total end) saldo2');

			$this->_saldoInicial = $array[0];
		}

		return $this->_saldoInicial;
	}
}

?>