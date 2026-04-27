<?php

/**
 * @property Almacen				$almacen
 * @property Material				$material
 * @property ColorMateriaPrima		$colorMateriaPrima
 * @property int					$cantidadTotal
 * @property Usuario				$usuario
 */

class ConsumoStockMP extends Base implements OperacionStock {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idAlmacen;
	protected	$_almacen;
	public		$idMaterial;
	protected	$_material;
	protected	$idColorMateriaPrima;
	protected	$_colorMateriaPrima;
	protected	$_cantidadTotal;
	public		$cantidad; //Array de 1 a 10
	public		$idUsuario;
	protected	$_usuario;
	public		$fechaAlta;

	public function guardar() {
		try {
			Factory::getInstance()->beginTransaction();

			for ($i = 1; $i <= 10; $i++) {
				$this->cantidad[$i] = Funciones::toFloat($this->cantidad[$i]);
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
        //Necesito saber si el usuario logueado tiene permiso en el almacén a realizar el consumo
        try {
            $idUsuario = $this->usuario->id ? $this->usuario->id : Usuario::logueado()->id;
            Factory::getInstance()->getUsuarioPorAlmacen($idUsuario, $this->almacen->id);
        } catch (FactoryExceptionRegistroNoExistente $ex) {
            throw new FactoryExceptionCustomException('No tiene permiso para realizar consumos de stock del almacen "' . $this->almacen->getIdNombre() . '"');
        }
		if ($this->cantidadTotal <= 0) {
			throw new FactoryExceptionCustomException('No puede hacer un consumo por 0 (cero) unidades (todas las columnas de cantidad están en cero)');
		}
	}

	/************************************** STOCK **************************************/

	public function stock() {
		return StockMP::registrarOperacion($this);
	}

	public function stockTipoMovimiento() {
        return ($this->modo == Modos::delete) ? TiposMovimientoStock::positivo : TiposMovimientoStock::negativo;
	}

	public function stockTipoOperacion() {
		return TiposOperacionStockMP::consumo;
	}

	public function stockKeyObjeto() {
		return $this->getPKSerializada();
	}

	public function stockObservacion() {
		return 'Consumo MP Nº ' . $this->id;
	}

	public function stockDetalle() {
		return array(
			$this->almacen->id => array(
				$this->material->id => array(
					$this->colorMateriaPrima->idColor => $this->cantidad
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
}

?>