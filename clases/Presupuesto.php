<?php

/**
 * @property Proveedor					$proveedor
 * @property LoteDeProduccion			$loteDeProduccion
 * @property Usuario					$usuario
 * @property PresupuestoItem[]			$detalle
 * @property PresupuestoItem[]			$detalleNoSaciado
 * @property FormularioPresupuesto		$formulario
 */

class Presupuesto extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idProveedor;
	protected	$_proveedor;
	protected	$_detalle;
	protected	$_detalleNoSaciado;
	public		$productivo;
	public		$modalidadCreacion;
	public		$idLoteDeProduccion;
	protected	$_loteDeProduccion;
	public		$observaciones;
	public		$anulado;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;
	public		$formulario;

	public function borrar() {
		if($this->tieneDetallesSaciados()){
			throw new FactoryExceptionCustomException('No puede anular un presupuesto que contenga detalles utilizados en alguna orden de compra');
		}
		//TODO lóica de rollback para el presupuesto generado por explosión

		foreach($this->detalle as $item){
			$item->borrar();
		}

		return parent::borrar();
	}

	public function tieneDetallesSaciados() {
		foreach($this->detalle as $item){
			if($item->saciado()){
				return true;
			}
		}
		return false;
	}

	public function tieneDetalle() {
		return (count($this->detalle) > 0 ? true : false);
	}

	public function addDetalle($presupuestoItem) {
		$this->_detalle[] = $presupuestoItem;
	}

	//formulario
	public function abrir() {
		$this->crearFormulario();
		$this->llenarFormulario();
		$this->formulario->abrir();
	}

	public function crear() {
		$this->crearFormulario();
		$this->llenarFormulario();
		return $this->formulario->crear();
	}

	protected function crearFormulario() {
		$this->formulario = new FormularioPresupuesto();
	}

	protected function llenarFormulario() {
		$this->formulario->id = $this->id;
		$this->formulario->fecha = $this->fechaAlta;
		$this->formulario->proveedor = $this->proveedor;
		$this->formulario->detalle = $this->detalle;
		$this->formulario->observaciones = $this->observaciones;
	}

	//GETS y SETS
	protected function getDetalle() {
		if (!isset($this->_detalle)){
			$this->_detalle = Factory::getInstance()->getListObject('PresupuestoItem', 'anulado = ' . Datos::objectToDB('N') . ' AND cod_presupuesto = ' . Datos::objectToDB($this->id));
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
	protected function getDetalleNoSaciado() {
		if (!isset($this->_detalleNoSaciado)){
			$this->_detalleNoSaciado = Factory::getInstance()->getListObject('PresupuestoItem', 'saciado = ' . Datos::objectToDB('N') .  ' AND anulado = ' . Datos::objectToDB('N') . ' AND cod_presupuesto = ' . Datos::objectToDB($this->id));
		}
		return $this->_detalleNoSaciado;
	}
	protected function getLoteDeProduccion() {
		if (!isset($this->_loteDeProduccion)){
			$this->_loteDeProduccion = Factory::getInstance()->getLoteDeProduccion($this->idLoteDeProduccion);
		}
		return $this->_loteDeProduccion;
	}
	protected function setLoteDeProduccion($loteDeProduccion) {
		$this->_loteDeProduccion = $loteDeProduccion;
		return $this;
	}
	protected function getProveedor() {
		if (!isset($this->_proveedor)){
			$this->_proveedor = Factory::getInstance()->getProveedor($this->idProveedor);
		}
		return $this->_proveedor;
	}
	protected function setProveedor($proveedor) {
		$this->_proveedor = $proveedor;
		return $this;
	}
}

?>