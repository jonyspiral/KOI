<?php

/**
 * @property Almacen			$almacen
 * @property Material			$material
 * @property ColorMateriaPrima	$colorMateriaPrima
 * @property int				$cantidadTotal
 * @property Usuario			$usuario
 */

class MovimientoStockMP extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$tipoMovimiento; //Es 'INI', 'POS' o 'NEG'
	public		$tipoOperacion; //Es alguno de los valores del enum TiposOperacionStock
	public		$keyObjeto;	//Es la PK serializada del objeto en cuestión. Puede ser NULL. (Ej: id=15&tipo=M)
	public		$observaciones; //Acá va el dato del número de tarea o documento. Es sólo para tener una referencia en el reporte
	public		$idAlmacen;
	protected	$_almacen;
	public		$idMaterial;
	protected	$_material;
	public		$idColorMateriaPrima;
	protected	$_colorMateriaPrima;
	protected	$_cantidadTotal;
	public		$cantidad; //Array de 1 a 10
	public		$idUsuario;
	protected	$_usuario;
	public		$fechaAlta;

	public function guardar() {
		try {
			try {
				$stockActual = Factory::getInstance()->getStockMP($this->almacen->id, $this->material->id, $this->colorMateriaPrima->idColor);
			} catch (FactoryExceptionRegistroNoExistente $ex) {
				if ($this->tipoMovimiento == TiposMovimientoStock::inicial || $this->tipoMovimiento == TiposMovimientoStock::positivo) {
					$stockActual = Factory::getInstance()->getStockMP();
					$stockActual->almacen = $this->almacen;
					$stockActual->material = $this->material;
					$stockActual->colorMateriaPrima = $this->colorMateriaPrima;
					$this->tipoMovimiento = TiposMovimientoStock::inicial;
				} else {
					throw $ex;
				}
			}
			foreach ($this->cantidad as $posicion => $cantMovimiento) {
				$stockActual->cantidad[$posicion] += (($this->tipoMovimiento == TiposMovimientoStock::negativo ? -1 : 1) * $cantMovimiento);
				if ($stockActual->cantidad[$posicion] < 0 && $this->tipoMovimiento != TiposMovimientoStock::inicial) {
					$mat = '[' . $this->almacen->id . '-' . $this->material->id . '-' . $this->colorMateriaPrima->idColor . '] ' . $this->material->nombre;
					throw new FactoryExceptionCustomException('No hay stock suficiente del material "' . $mat . '" para realizar el movimiento');
				}
			}
			$stockActual->guardar();

			parent::guardar();
		} catch (FactoryExceptionRegistroNoExistente $ex) {
			throw new FactoryExceptionCustomException('No existe el material [' . $this->almacen->id . '_' . $this->material->id . '_' . $this->colorMateriaPrima->idColor . '] en la tabla de stock de materia prima');
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
}

?>