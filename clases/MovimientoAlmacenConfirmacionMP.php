<?php

/**
 * @property Almacen			$almacenOrigen
 * @property Almacen			$almacenDestino
 * @property Material			$material
 * @property ColorMateriaPrima	$colorMateriaPrima
 * @property int				$cantidadTotal
 * @property Usuario			$usuario
 * @property Usuario			$usuarioBaja
 * @property Usuario			$usuarioConfirmacion
 */

class MovimientoAlmacenConfirmacionMP extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idAlmacenOrigen;
	protected	$_almacenOrigen;
	public		$idAlmacenDestino;
	protected	$_almacenDestino;
	public		$idMaterial;
	protected	$_material;
	public		$idColorMateriaPrima;
	protected	$_colorMateriaPrima;
	public		$motivo;
	protected	$_cantidadTotal;
	public		$cantidad; //Array de 1 a 10
	public		$confirmado;
	public		$anulado;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioConfirmacion;
	protected	$_usuarioConfirmacion;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaConfirmacion;

	public function pendiente() {
		return ($this->confirmado == 'N' && $this->anulado == 'N');
	}

	public function guardar() {
		try {
			for ($i = 1; $i <= 10; $i++) {
				$this->cantidad[$i] = Funciones::toFloat($this->cantidad[$i]);
			}

			//Para mayor seguridad chequeo el stock (igualmente se vuelve a chequear en el momento de la confirmación)
			$stockActual = Factory::getInstance()->getStockMP($this->almacenOrigen->id, $this->material->id, $this->colorMateriaPrima->idColor);
			for ($i = 1; $i <= 10; $i++) {
				if ($this->cantidad[$i] > $stockActual->cantidad[$i]) {
					throw new FactoryExceptionCustomException('La cantidad actual en stock en la posición ' . $i . ' (' . ($stockActual->cantidad[$i]) . ') es mayor que la cantidad que se quiere mover (' . $this->cantidad[$i] . ')');
				}
			}

			//Hay que notificar a los usuarios del almacén de destino
			$usuariosNotificar = Factory::getInstance()->getListObject('UsuarioPorAlmacen', 'cod_almacen = ' . Datos::objectToDB($this->almacenDestino->id));

			parent::guardar()->notificar('produccion/stock_mp/confirmacion_movimiento_almacen/agregar/', $usuariosNotificar);
		} catch (Exception $ex) {
			throw $ex;
		}

		return $this;
	}

	protected function validarGuardar() {
		//Necesito saber si el usuario logueado tiene permiso en el almacén de origen
		try {
			$idUsuario = $this->usuario->id ? $this->usuario->id : Usuario::logueado()->id;
			Factory::getInstance()->getUsuarioPorAlmacen($idUsuario, $this->almacenOrigen->id);
		} catch (FactoryExceptionRegistroNoExistente $ex) {
			throw new FactoryExceptionCustomException('No tiene permiso para iniciar movimientos de stock del almacen "' . $this->almacenOrigen->getIdNombre() . '"');
		}

		if ($this->cantidadTotal <= 0) {
			throw new FactoryExceptionCustomException('No puede hacer un movimiento de stock por 0 (cero) unidades (todas las columnas de cantidad están en cero)');
		}
	}

	protected function validarBorrar() {
		try {
			Factory::getInstance()->getUsuarioPorAlmacen(Usuario::logueado()->id, $this->almacenDestino->id);
		} catch (FactoryExceptionRegistroNoExistente $ex) {
			throw new FactoryExceptionCustomException('No tiene permiso para confirmar/rechazar movimientos de stock del almacen "' . $this->almacenDestino->getIdNombre() . '"');
		}

		if ($this->confirmado == 'S') {
			throw new FactoryExceptionCustomException('El movimiento de stock ya fue confirmado previamente');
		}
		if ($this->anulado == 'S') {
			throw new FactoryExceptionCustomException('El movimiento de stock ya fue rechazado/anulado previamente');
		}

		parent::validarBorrar();
	}

	public function confirmar($funcionalidad = false) {
		$this->validarBorrar(); //Necesito saber si el usuario logueado tiene permiso

		try {
			Factory::getInstance()->beginTransaction();

			$movimiento = Factory::getInstance()->getMovimientoAlmacenMP();
			$movimiento->almacenOrigen = $this->almacenOrigen;
			$movimiento->almacenDestino = $this->almacenDestino;
			$movimiento->material = $this->material;
			$movimiento->colorMateriaPrima = $this->colorMateriaPrima;
			$movimiento->motivo = $this->motivo;
			for ($i = 1; $i <= 10; $i++) {
				$movimiento->cantidad[$i] = Funciones::keyIsSet($this->cantidad, $i, 0);
			}
			$movimiento->confirmacion = $this;
			$movimiento->guardar()->notificar($funcionalidad);

			$this->confirmado = 'S';
			$this->usuarioConfirmacion = Usuario::logueado();
			$this->fechaConfirmacion = Funciones::hoy();

			parent::guardar();

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}
	}

	//GETS y SETS
	protected function getAlmacenOrigen() {
		if (!isset($this->_almacenOrigen)){
			$this->_almacenOrigen = Factory::getInstance()->getAlmacen($this->idAlmacenOrigen);
		}
		return $this->_almacenOrigen;
	}
	protected function setAlmacenOrigen($almacenOrigen) {
		$this->_almacenOrigen = $almacenOrigen;
		return $this;
	}
	protected function getAlmacenDestino() {
		if (!isset($this->_almacenDestino)){
			$this->_almacenDestino = Factory::getInstance()->getAlmacen($this->idAlmacenDestino);
		}
		return $this->_almacenDestino;
	}
	protected function setAlmacenDestino($almacenDestino) {
		$this->_almacenDestino = $almacenDestino;
		return $this;
	}
	protected function getMaterial() {
		if (!isset($this->_material)){
			$this->_material = Factory::getInstance()->getMaterial($this->idMaterial);
		}
		return $this->_material;
	}
	protected function setMaterial($material) {
		$this->_material = $material;
		return $this;
	}
	protected function getCantidadTotal() {
		return Funciones::sumaArray($this->cantidad);
	}
	protected function getColorMateriaPrima() {
		if (!isset($this->_colorMateriaPrima)){
			$this->_colorMateriaPrima = Factory::getInstance()->getColorMateriaPrima($this->idMaterial, $this->idColorMateriaPrima);
		}
		return $this->_colorMateriaPrima;
	}
	protected function setColorMateriaPrima($colorMateriaPrima) {
		$this->_colorMateriaPrima = $colorMateriaPrima;
		return $this;
	}
	protected function getUsuarioConfirmacion() {
		if (!isset($this->_usuarioConfirmacion)){
			$this->_usuarioConfirmacion = Factory::getInstance()->getUsuario($this->idUsuarioConfirmacion);
		}
		return $this->_usuarioConfirmacion;
	}
	protected function setUsuarioConfirmacion($usuarioConfirmacion) {
		$this->_usuarioConfirmacion = $usuarioConfirmacion;
		return $this;
	}
}

?>