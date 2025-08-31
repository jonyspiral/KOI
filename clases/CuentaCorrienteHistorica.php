<?php

/**
 * @property Cliente	$cliente
 * @property array		$documentosPorFecha
 * @property array		$saldoInicial
 */

class CuentaCorrienteHistorica extends CuentaCorriente {
	public		$idCliente;
	protected	$_cliente;
	public		$empresa;
	public		$fechaDesde;
	public		$fechaHasta;
	public		$fechaVtoDesde;
	public		$fechaVtoHasta;
	protected	$_documentosPorFecha;		//Lista de CuentaCorrienteHistoricaDocumento
	protected	$_saldoInicial;

	//GETS y SETS
	protected function getDocumentosPorFecha() {
		if (!isset($this->_documentosPorFecha)){
			$whereEempresa = ($this->empresa == 1 || $this->empresa == 2 ? '(empresa = ' . Datos::objectToDB($this->empresa) . ') AND ' : '');
			$strFechas = Funciones::strFechas($this->fechaDesde, $this->fechaHasta, 'fecha');

			$whereEempresa .= (empty($strFechas) ? '' : $strFechas . ' AND ');
			$whereEempresa .= 'cod_cliente = ' . Datos::objectToDB($this->idCliente) . ' AND ';
			$whereEempresa = trim($whereEempresa, ' AND ');
			$orderBy = ' ORDER BY fecha ASC, tipo_docum ASC, numero ASC, empresa ASC';

			$this->_documentosPorFecha = Factory::getInstance()->getListObject('CuentaCorrienteHistoricaDocumento', $whereEempresa . $orderBy);
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
			$where .= 'cod_cliente = ' . Datos::objectToDB($this->idCliente) . ' AND ';
			$where .= Funciones::strFechas($nullVar, Funciones::sumarTiempo($this->fechaDesde, -1), 'fecha') . ' AND ';
			$where = trim($where, ' AND ');

			$array = Factory::getInstance()->getArrayFromView('cuenta_corriente_historica', $where, 1, 'SUM(case when empresa = ' . Datos::objectToDB('1') . ' then importe_total else 0 end) saldo1, SUM(case when empresa = ' . Datos::objectToDB('1') . ' then 0 else importe_total end) saldo2');

			$this->_saldoInicial = $array[0];
		}

		return $this->_saldoInicial;
	}
}

?>