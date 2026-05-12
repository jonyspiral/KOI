<?php

/**
 * @property Almacen			$almacen
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 * @property int				$cantidadTotal
 * @property Usuario			$usuario
 */

class MovimientoStock extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$tipoMovimiento; //Es 'INI', 'POS' o 'NEG'
	public		$tipoOperacion; //Es alguno de los valores del enum TiposOperacionStock
	public		$keyObjeto;	//Es la PK serializada del objeto en cuestión. Puede ser NULL. (Ej: id=15&tipo=M)
	public		$observaciones; //Acá va el dato del número de tarea o documento. Es sólo para tener una referencia en el reporte
	public		$idAlmacen;
	protected	$_almacen;
	public		$idArticulo;
	protected	$_articulo;
	public		$idColorPorArticulo;
	protected	$_colorPorArticulo;
	protected	$_cantidadTotal;
	public		$cantidad; //Array de 1 a 10
	public		$idUsuario;
	protected	$_usuario;
	public		$fechaAlta;

	public function guardar() {
		try {
			try {
				$stockActual = Factory::getInstance()->getStock($this->almacen->id, $this->articulo->id, $this->colorPorArticulo->id);
			} catch (FactoryExceptionRegistroNoExistente $ex) {
				if ($this->tipoMovimiento == TiposMovimientoStock::inicial || $this->tipoMovimiento == TiposMovimientoStock::positivo) {
					$stockActual = Factory::getInstance()->getStock();
					$stockActual->almacen = $this->almacen;
					$stockActual->articulo = $this->articulo;
					$stockActual->colorPorArticulo = $this->colorPorArticulo;
					$this->tipoMovimiento = TiposMovimientoStock::inicial;
				} else {
					throw $ex;
				}
			}
			foreach ($this->cantidad as $posicion => $cantMovimiento) {
				$stockActual->cantidad[$posicion] += (($this->tipoMovimiento == TiposMovimientoStock::negativo ? -1 : 1) * $cantMovimiento);
				if ($stockActual->cantidad[$posicion] < 0 && $this->tipoMovimiento != TiposMovimientoStock::inicial) {
					$art = '[' . $this->almacen->id . '-' . $this->articulo->id . '-' . $this->colorPorArticulo->id . '] ' . $this->articulo->nombre;
					throw new FactoryExceptionCustomException('No hay stock suficiente del artículo "' . $art . '" para realizar el movimiento');
				}
			}
			$stockActual->guardar();

			parent::guardar();
		} catch (FactoryExceptionRegistroNoExistente $ex) {
			throw new FactoryExceptionCustomException('No existe el artículo [' . $this->almacen->id . '_' . $this->articulo->id . '_' . $this->colorPorArticulo->id . '] en la tabla de stock');
		}

		return $this;
	}

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