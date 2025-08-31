<?php

/**
 * @property Almacen			$almacen
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 * @property int				$cantidadTotal
 * @property Usuario			$usuario
 */

class AjusteStock extends Base implements OperacionStock {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$tipoMovimiento; //Es 'INI', 'POS' o 'NEG'
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
		try {
			Factory::getInstance()->beginTransaction();

			for ($i = 1; $i <= 10; $i++) {
				$this->cantidad[$i] = Funciones::toInt($this->cantidad[$i]);
			}
			parent::guardar(); //Lo hago primero por el ID (para el $keyObjeto)
			$this->stock();

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}
		return $this;
	}

	protected function validarGuardar() {
		if (empty($this->motivo)) {
			throw new FactoryExceptionCustomException('El motivo del ajuste es obligatorio');
		}

		if ($this->cantidadTotal <= 0 && $this->tipoMovimiento != TiposMovimientoStock::inicial) {
			throw new FactoryExceptionCustomException('No puede hacer un ajuste por 0 (cero) pares (todas las columnas de cantidad están en cero)');
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
		return TiposOperacionStock::ajusteStock;
	}

	public function stockKeyObjeto() {
		return $this->getPKSerializada();
	}

	public function stockObservacion() {
		return 'Ajuste ' . $this->tipoMovimiento . ' Nº ' . $this->id;
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
}

?>