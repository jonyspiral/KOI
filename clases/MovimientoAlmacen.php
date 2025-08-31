<?php

/**
 * @property MovimientoAlmacenConfirmacion	$confirmacion
 * @property Almacen						$almacenDestino
 * @property Almacen						$almacenOrigen
 * @property Almacen						$almacen
 * @property Articulo						$articulo
 * @property ColorPorArticulo				$colorPorArticulo
 * @property int							$cantidadTotal
 * @property Usuario						$usuario
 */

class MovimientoAlmacen extends Base implements OperacionStock {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idConfirmacion;
	protected	$_confirmacion;
	public		$tipoMovimiento; //Es 'INI', 'POS' o 'NEG'
	public		$idAlmacenDestino;
	protected	$_almacenDestino;
	public		$idAlmacenOrigen;
	protected	$_almacenOrigen;
	public		$idAlmacen;
	protected	$_almacen;
	public		$idArticulo;
	protected	$_articulo;
	public		$idColorPorArticulo;
	protected	$_colorPorArticulo;
	public		$motivo;
	protected	$_cantidadTotal;
	public		$cantidad; //Array de 1 a 10
	public		$idUsuario;
	protected	$_usuario;
	public		$fechaAlta;

	public function guardar() {
		$mutex = new Mutex(Funciones::getType($this) . '_verificaciones');
		try {
			Factory::getInstance()->beginTransaction();

			for ($i = 1; $i <= 10; $i++) {
				$this->cantidad[$i] = Funciones::toInt($this->cantidad[$i]);
			}

			$stockActual = Factory::getInstance()->getStock($this->almacenOrigen->id, $this->articulo->id, $this->colorPorArticulo->id);
			for ($i = 1; $i <= 10; $i++) {
				if ($this->cantidad[$i] > $stockActual->cantidad[$i]) {
					throw new FactoryExceptionCustomException('La cantidad actual en stock en la posición ' . $i . ' (' . ($stockActual->cantidad[$i]) . ') es mayor que la cantidad que se quiere mover (' . $this->cantidad[$i] . ')');
				}
			}

			$this->almacen = $this->almacenOrigen;
			$this->tipoMovimiento = TiposMovimientoStock::negativo;
			parent::guardar(); //Lo hago primero por el ID (para el $keyObjeto)
			$this->stock();

			//Limpio el objeto para hacer el movimiento positivo
			$this->id = null;
			Factory::getInstance()->marcarParaInsertar($this);

			$this->almacen = $this->almacenDestino;
			$this->tipoMovimiento = TiposMovimientoStock::positivo;
			parent::guardar(); //Lo hago primero por el ID (para el $keyObjeto)
			$this->stock();

			Factory::getInstance()->commitTransaction();
			$mutex->unlock();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			$mutex->unlock();
			throw $ex;
		}

		return $this;
	}

	protected function validarGuardar() {
		if ($this->cantidadTotal <= 0) {
			throw new FactoryExceptionCustomException('No puede hacer un movimiento de stock por 0 (cero) pares (todas las columnas de cantidad están en cero)');
		}
	}

	/************************************** STOCK **************************************/

	public function stock() {
		return Stock::registrarOperacion($this);
	}

	public function stockTipoMovimiento() {
		return ($this->modo == Modos::delete) ? ($this->tipoMovimiento == TiposMovimientoStock::negativo ? TiposMovimientoStock::positivo : TiposMovimientoStock::negativo) : $this->tipoMovimiento;
	}

	public function stockTipoOperacion() {
		return TiposOperacionStock::movimientoAlmacen;
	}

	public function stockKeyObjeto() {
		return $this->getPKSerializada();
	}

	public function stockObservacion() {
		return 'Mov. almacén Nº ' . $this->id;
	}

	public function stockDetalle() {
		return array(
			$this->almacen->id => array(
				$this->articulo->id => array(
					$this->colorPorArticulo->id => $this->cantidad
				)
			)
		);
	}

	/************************************** ***** **************************************/

	//GETS y SETS
	protected function getAlmacen() {
		if (!isset($this->_almacen)){
			$this->_almacen = Factory::getInstance()->getAlmacen($this->idAlmacen);
		}
		return $this->_almacen;
	}
	protected function setAlmacen($almacen) {
		$this->_almacen = $almacen;
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
	protected function getArticulo() {
		if (!isset($this->_articulo)){
			$this->_articulo = Factory::getInstance()->getArticulo($this->idArticulo);
		}
		return $this->_articulo;
	}
	protected function setArticulo($articulo) {
		$this->_articulo = $articulo;
		return $this;
	}
	protected function getCantidadTotal() {
		return Funciones::sumaArray($this->cantidad);
	}
	protected function getColorPorArticulo() {
		if (!isset($this->_colorPorArticulo)){
			$this->_colorPorArticulo = Factory::getInstance()->getColorPorArticulo($this->idArticulo, $this->idColorPorArticulo);
		}
		return $this->_colorPorArticulo;
	}
	protected function setColorPorArticulo($colorPorArticulo) {
		$this->_colorPorArticulo = $colorPorArticulo;
		return $this;
	}
	protected function getConfirmacion() {
		if (!isset($this->_confirmacion)){
			$this->_confirmacion = Factory::getInstance()->getMovimientoAlmacenConfirmacion($this->idConfirmacion);
		}
		return $this->_confirmacion;
	}
	protected function setConfirmacion($confirmacion) {
		$this->_confirmacion = $confirmacion;
		return $this;
	}
}

?>