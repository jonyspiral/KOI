<?php

/**
 * @property DocumentoProveedor            $documentoProveedor
 * @property Imputacion                    $imputacion
 * @property RemitoPorOrdenDeCompra        $remitoPorOrdenDeCompra
 */

class DocumentoProveedorItem extends Base {
	const        _primaryKey = '["idDocumentoProveedor","nroItem"]';

	public		$idDocumentoProveedor;
	protected 	$_documentoProveedor;
	public 		$nroItem;
	public 		$descripcion;
	public 		$precioUnitario;
	public		$precios;
	public		$cantidad;
	public		$cantidades;
	public		$importe;
	public		$usaRango;
	public		$idImputacion;
	protected	$_imputacion;
	public		$gravado;
	public		$origenDetalle;
	public		$idRemitoPorOrdenDeCompra;
	protected	$_remitoPorOrdenDeCompra;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$anulado;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	//Campos necesarios para vincular con Remitos
	public		$id;
	public		$idMaterialColor;
	public		$total;
	public		$talles;

	public function __construct() {
		$this->cantidades = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		$this->precios = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		parent::__construct();
	}

	public function usaRango() {
		return $this->usaRango == 'S';
	}

	public function borrar() {
		if(isset($this->idRemitoPorOrdenDeCompra)) {
			$this->remitoPorOrdenDeCompra->cantidadPendiente += $this->cantidad;

			if($this->usaRango()){
				for($i = 0; $i < 16; $i++) {
					$this->remitoPorOrdenDeCompra->cantidadesPendientes[$i] += $this->cantidades[$i];
				}
			}

			$this->remitoPorOrdenDeCompra->guardar();
		}

		$this->remitoPorOrdenDeCompra = Factory::getInstance()->getRemitoPorOrdenDeCompra();

		return parent::borrar();
	}

	//GETS y SETS
	protected function getDocumentoProveedor() {
		if (!isset($this->_documentoProveedor)) {
			$this->_documentoProveedor = Factory::getInstance()->getDocumentoProveedor($this->idDocumentoProveedor);
		}
		return $this->_documentoProveedor;
	}

	protected function setDocumentoProveedor($documentoProveedor) {
		$this->_documentoProveedor = $documentoProveedor;
		return $this;
	}

	protected function getImputacion() {
		if (!isset($this->_imputacion)) {
			$this->_imputacion = Factory::getInstance()->getImputacion($this->idImputacion);
		}
		return $this->_imputacion;
	}

	protected function setImputacion($imputacion) {
		$this->_imputacion = $imputacion;
		return $this;
	}

	protected function getRemitoPorOrdenDeCompra() {
		if (!isset($this->_remitoPorOrdenDeCompra)) {
			$this->_remitoPorOrdenDeCompra = Factory::getInstance()->getRemitoPorOrdenDeCompra($this->idRemitoPorOrdenDeCompra);
		}
		return $this->_remitoPorOrdenDeCompra;
	}

	protected function setRemitoPorOrdenDeCompra($remitoPorOrdenDeCompra) {
		$this->_remitoPorOrdenDeCompra = $remitoPorOrdenDeCompra;
		return $this;
	}
}

?>