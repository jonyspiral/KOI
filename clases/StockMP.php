<?php

/**
 * @property Almacen			$almacen
 * @property Material			$material
 * @property ColorMateriaPrima	$colorMateriaPrima
 * @property int				$cantidadTotal
 */

class StockMP extends Base {
	public		$idAlmacen;
	protected	$_almacen;
	public		$idMaterial;
	protected	$_material;
	public		$idColorMateriaPrima;
	protected	$_colorMateriaPrima;
	protected	$_cantidadTotal;
	public		$cantidad; //Array de 1 a 10

	public static function getStock($idAlmacen) {
		$array = array();
		$stocks = Factory::getInstance()->getArrayFromView('stock_mp_vw', 'cod_almacen = ' . Datos::objectToDB($idAlmacen));
		for ($i = 0; $i < count($stocks); $i++) {
			$item = $stocks[$i];
			for ($j = 1; $j <= 10; $j++)
				$array[$item['cod_material']][$item['cod_color']][$j] = $item['S' . $j];
		}
		return $array;
	}

	public static function registrarOperacion(OperacionStock $operacion) {
		$mutex = new Mutex('OperacionStock');
		try {
			$mutex->lock();

			$tipoMovimiento = $operacion->stockTipoMovimiento();
			$tipoOperacion = $operacion->stockTipoOperacion();
			$keyObjeto = $operacion->stockKeyObjeto();
			$observaciones = $operacion->stockObservacion();
			$arrayDetalle = $operacion->stockDetalle(); //Array del tipo $arrayDetalle['almacen']['material']['color']['posicion'] = $cantidad ("posicion" de  1 a 10)

			$movimientos = array();
			foreach ($arrayDetalle as $idAlmacen => $arrayAlmacen) {
				foreach ($arrayAlmacen as $idMaterial => $arrayMaterial) {
					foreach ($arrayMaterial as $idColor => $arrayCantidades) {
						$movimientos[] = self::registrarMovimiento($tipoMovimiento, $tipoOperacion, $keyObjeto, $observaciones, $idAlmacen, $idMaterial, $idColor, $arrayCantidades);
					}
				}
			}

			$mutex->unlock();
			return $movimientos;
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}

	private static function registrarMovimiento($tipoMovimiento, $tipoOperacion, $keyObjeto, $observaciones, $idAlmacen, $idMaterial, $idColor, $arrayCantidades) {
		$movimiento = Factory::getInstance()->getMovimientoStockMP();
		$movimiento->tipoMovimiento = $tipoMovimiento;
		$movimiento->tipoOperacion = $tipoOperacion;
		$movimiento->keyObjeto = $keyObjeto;
		$movimiento->observaciones = $observaciones;
		$movimiento->almacen = Factory::getInstance()->getAlmacen($idAlmacen);
		$movimiento->material = Factory::getInstance()->getMaterial($idMaterial);
		$movimiento->colorMateriaPrima = Factory::getInstance()->getColorMateriaPrima($idMaterial, $idColor);
		$movimiento->cantidad = $arrayCantidades;

		$movimiento->guardar();

		return $movimiento;
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