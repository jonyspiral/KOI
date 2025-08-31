<?php
/**
 * @property OrdenDePAgo		$ordenDePago
 */
class EgresoDeFondosItem extends Base {

	public		$numeroOrdenDePago;
	public		$empresa;
	protected 	$_ordenDePago;
	public		$proveedor;
	public		$imputacionGeneral;
	public		$imputacionEspecifica;
	public		$denomEspecifica;
	public		$denomGeneral;
	public		$efectivo;
	public		$cheques;
	public		$chequesPropios;
	public		$chequesTerceros;
	public		$transferencias;
	public		$total;
	public		$idCaja;
	public		$fecha;

	public function getArrayRetenciones() {
		return $this->ordenDePago->getArrayRetenciones();
	}

	//GETS y SETS
	protected function getOrdenDePago() {
		if (!isset($this->_ordenDePago)){
			$this->_ordenDePago = Factory::getInstance()->getOrdenDePago($this->numeroOrdenDePago, $this->empresa);
		}
		return $this->_ordenDePago;
	}
	protected function setOrdenDePago($ordenDePago) {
		$this->_ordenDePago = $ordenDePago;
		return $this;
	}
}

?>