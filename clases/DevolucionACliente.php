<?php

/**
 * @property DevolucionAClienteItem[]	$detalle
 * @property Cliente					$cliente
 * @property Sucursal					$sucursal
 * @property int						$cantidadPares
 * @property Usuario					$usuario
 * @property Usuario					$usuarioBaja
 * @property Usuario					$usuarioUltimaMod
 */

class DevolucionACliente extends Base implements OperacionStock {
	const		_primaryKey = '["id"]';

	public		$id;
	protected	$_detalle;
	public		$idCliente;
	protected	$_cliente;
	public		$idSucursal;
	protected	$_sucursal;
	protected	$_cantidadPares;
	public		$anulado;
	public		$observaciones;
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

	public function guardar() {
		try {
			Factory::getInstance()->beginTransaction();

			parent::guardar(); //Lo hago primero por el ID (para el $keyObjeto)
			$this->stock();

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		return $this;
	}

	public function borrar() {
		try {
			Factory::getInstance()->beginTransaction();

			parent::borrar();
			$this->stock();

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		return $this;
	}

	/************************************** STOCK **************************************/

	public function stock() {
		return Stock::registrarOperacion($this);
	}

	public function stockTipoMovimiento() {
		return ($this->modo == Modos::delete) ? TiposMovimientoStock::positivo : TiposMovimientoStock::negativo;
	}

	public function stockTipoOperacion() {
		return TiposOperacionStock::devolucionACliente;
	}

	public function stockKeyObjeto() {
		return $this->getPKSerializada();
	}

	public function stockObservacion() {
		return 'Devolución Nº ' . $this->id;
	}

	public function stockDetalle() {
		$ret = array();
		foreach ($this->detalle as $item) {
			if (!isset($ret[$item->almacen->id])) {
				$ret[$item->almacen->id] = array();
			}
			if (!isset($ret[$item->almacen->id][$item->articulo->id])) {
				$ret[$item->almacen->id][$item->articulo->id] = array();
			}
			if (!isset($ret[$item->almacen->id][$item->articulo->id][$item->colorPorArticulo->id])) {
				$ret[$item->almacen->id][$item->articulo->id][$item->colorPorArticulo->id] = $item->cantidad;
			} else {
				for ($i = 1; $i <= 10; $i++) {
					$ret[$item->almacen->id][$item->articulo->id][$item->colorPorArticulo->id][$i] += $item->cantidad[$i];
				}
			}
		}
		return $ret;
	}

	/************************************** ***** **************************************/

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
		$this->formulario = new FormularioDevolucionCliente();
	}

	protected function llenarFormulario() {
		$this->formulario->id = $this->id;
		$this->formulario->cliente = $this->cliente;
		$this->formulario->fecha = $this->fechaAlta;
		$this->formulario->detalle = $this->detalle;
		$this->formulario->observaciones = $this->observaciones;
	}

	//GETS y SETS
	protected function getCantidadPares() {
		$pares = 0;
		foreach($this->detalle as $item) {
			$pares += $item->cantidadTotal;
		}
		return $pares;
	}
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
	protected function getDetalle() {
		if (!isset($this->_detalle)){
			$this->_detalle = Factory::getInstance()->getListObject('DevolucionAClienteItem', 'cod_devolucion = ' . Datos::objectToDB($this->id));
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
	protected function getSucursal() {
		if (!isset($this->_sucursal)){
			$this->_sucursal = Factory::getInstance()->getSucursal($this->idCliente, $this->idSucursal);
		}
		return $this->_sucursal;
	}
	protected function setSucursal($sucursal) {
		$this->_sucursal = $sucursal;
		return $this;
	}
}

?>