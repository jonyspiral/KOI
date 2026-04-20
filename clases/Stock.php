<?php

/**
 * @property Almacen			$almacen
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 * @property int				$cantidadTotal
 */

class Stock extends Base {
	public 		$__CACHE_TIME = false;

	public		$idAlmacen;
	protected	$_almacen;
	public		$idArticulo;
	protected	$_articulo;
	public		$idColorPorArticulo;
	protected	$_colorPorArticulo;
	protected	$_cantidadTotal;
	public		$cantidad; //Array de 1 a 10

	public static function getStock($idAlmacen, $tarerStockCero = true) {
		$array = array();
		$stocks = Factory::getInstance()->getArrayFromView('stock_pt', 'cod_almacen = ' . Datos::objectToDB($idAlmacen) . ($tarerStockCero ? '' : ' AND cant_s > 0'));
		for ($i = 0; $i < count($stocks); $i++) {
			$item = $stocks[$i];
			for ($j = 1; $j <= 10; $j++)
				$array[$item['cod_articulo']][$item['cod_color_articulo']][$j] = $item['S' . $j];
		}
		return $array;
	}

	public static function getStockMenosPendiente($idAlmacen, $idArticulo = null, $idColor = null, $tarerStockCero = true) {
		$array = array();
		$where = 'cod_almacen = ' . Datos::objectToDB($idAlmacen) . ' AND ';
		$where .= ($tarerStockCero ? '' : 'cant_s > 0 AND ');
		$where .= ($idArticulo ? 'cod_articulo = ' . Datos::objectToDB($idArticulo) . ' AND ' : '');
		$where .= ($idColor ? 'cod_color_articulo = ' . Datos::objectToDB($idColor) . ' AND ' : '');
		$stocks = Factory::getInstance()->getArrayFromView('stock_menos_pendiente_vw', trim($where, ' AND '));
		for ($i = 0; $i < count($stocks); $i++) {
			$item = $stocks[$i];
			for ($j = 1; $j <= 10; $j++)
				$array[$item['cod_articulo']][$item['cod_color_articulo']][$j] = $item['S' . $j];
		}
		return $array;
	}

	public static function getStockMenosAsignado($idAlmacen, $idArticulo = null, $idColor = null, $tarerStockCero = true) {
		$array = array();
		$where = 'cod_almacen = ' . Datos::objectToDB($idAlmacen) . ' AND ';
		$where .= ($tarerStockCero ? '' : 'cant_s > 0 AND ');
		$where .= ($idArticulo ? 'cod_articulo = ' . Datos::objectToDB($idArticulo) . ' AND ' : '');
		$where .= ($idColor ? 'cod_color_articulo = ' . Datos::objectToDB($idColor) . ' AND ' : '');
		$stocks = Factory::getInstance()->getArrayFromView('stock_menos_asignado_vw', trim($where, ' AND '));
		for ($i = 0; $i < count($stocks); $i++) {
			$item = $stocks[$i];
			for ($j = 1; $j <= 10; $j++)
				$array[$item['cod_articulo']][$item['cod_color_articulo']][$j] = $item['S' . $j];
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
			$arrayDetalle = $operacion->stockDetalle(); //Array del tipo $arrayDetalle['almacen']['articulo']['color']['posicion'] = $cantidad ("posicion" de  1 a 10)

			$movimientos = array();
			foreach ($arrayDetalle as $idAlmacen => $arrayAlmacen) {
				foreach ($arrayAlmacen as $idArticulo => $arrayArticulo) {
					foreach ($arrayArticulo as $idColorPorArticulo => $arrayCantidades) {
						$movimientos[] = self::registrarMovimiento($tipoMovimiento, $tipoOperacion, $keyObjeto, $observaciones, $idAlmacen, $idArticulo, $idColorPorArticulo, $arrayCantidades);
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

	private static function registrarMovimiento($tipoMovimiento, $tipoOperacion, $keyObjeto, $observaciones, $idAlmacen, $idArticulo, $idColorPorArticulo, $arrayCantidades) {
		$movimiento = Factory::getInstance()->getMovimientoStock();
		$movimiento->tipoMovimiento = $tipoMovimiento;
		$movimiento->tipoOperacion = $tipoOperacion;
		$movimiento->keyObjeto = $keyObjeto;
		$movimiento->observaciones = $observaciones;
		$movimiento->almacen = Factory::getInstance()->getAlmacen($idAlmacen);
		$movimiento->articulo = Factory::getInstance()->getArticulo($idArticulo);
		$movimiento->colorPorArticulo = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColorPorArticulo);
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