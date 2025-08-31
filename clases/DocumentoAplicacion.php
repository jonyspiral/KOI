<?php

/**
 * @property array		$hijas
 * @property int		$numero
 * @property Cliente	$cliente
 * @property Documento	$documento
 */

class DocumentoAplicacion extends Base {
	public		$diasPromedioPago;

	public		$empresa;
	public		$puntoDeVenta;
	public		$tipoDocumento;
	public		$nroDocumento;
	protected	$_numero;
	public		$nroComprobante;
	public		$letra;
	public		$idCliente;
	protected	$_cliente;
	public		$fecha;
	public		$importeTotal;
	public		$importePendiente;
	protected	$_hijas;

	protected	$_documento;

	public function anulado() {
		return $this->documento->anulado();
	}

	//GETS y SETS
	protected function getCliente() {
		if (!isset($this->_cliente)){
			$this->_cliente = Factory::getInstance()->getCliente($this->idCliente);
		}
		return $this->_cliente;
	}
	protected function setCliente($cliente) {
		$this->_cliente = $cliente;
		return $this;
	}
	protected function getDocumento() {
		if (!isset($this->_documento)){
			switch ($this->tipoDocumento) {
				case 'FAC':
					$this->_documento = Factory::getInstance()->getFactura($this->empresa, $this->puntoDeVenta, $this->tipoDocumento, $this->nroDocumento, $this->letra);
					break;
				case 'NCR':
					$this->_documento = Factory::getInstance()->getNotaDeCredito($this->empresa, $this->puntoDeVenta, $this->tipoDocumento, $this->nroDocumento, $this->letra);
					break;
				case 'NDB':
					$this->_documento = Factory::getInstance()->getNotaDeDebito($this->empresa, $this->puntoDeVenta, $this->tipoDocumento, $this->nroDocumento, $this->letra);
					break;
				case 'REC':
					$this->_documento = Factory::getInstance()->getRecibo($this->nroDocumento, $this->empresa);
					break;
			}
		}
		return $this->_documento;
	}
	protected function setDocumento($documento) {
		$this->_documento = $documento;
		return $this;
	}
	protected function getHijas() {
		if (!isset($this->_hijas)){
			$where = 'empresa = ' . Datos::objectToDB($this->empresa) .' AND ';
			$where .= 'cancel_punto_venta = ' . Datos::objectToDB($this->puntoDeVenta) . ' AND ';
			$where .= 'cancel_tipo_docum = ' . Datos::objectToDB($this->tipoDocumento) . ' AND ';
			$where .= 'cancel_nro_documento = ' . Datos::objectToDB($this->nroDocumento) . ' AND ';
			$where .= 'cancel_letra = ' . Datos::objectToDB($this->letra);

			$this->_hijas = Factory::getInstance()->getListObject('DocumentoHija', $where);
		}
		return $this->_hijas;
	}
	protected function setHijas($hijas) {
		$this->_hijas = $hijas;
		return $this;
	}
	protected function getNumero() {
		return ($this->nroComprobante ? $this->nroComprobante : $this->nroDocumento);
	}
}

?>