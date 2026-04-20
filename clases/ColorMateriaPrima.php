<?php
/**
 * @property Material		$material
 * @property int[]			$stock
 * @property Usuario		$usuario
 */

class ColorMateriaPrima extends Base {
	const		_primaryKey = '["idMaterial","idColor"]';

	public		$idMaterial;
	protected	$_material;
	public		$idColor;
	public		$anulado;
	public		$nombreColor;
	protected	$_stock;
	public		$precioUnitario;
	public		$precioVentaUnitario;
	public		$idUsuario;
	protected	$_usuario;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function getStockAlmacen($idAlmacen) {
		if (empty($this->stock[$idAlmacen])) {
			return array(
				'1' => 0,
				'2' => 0,
				'3' => 0,
				'4' => 0,
				'5' => 0,
				'6' => 0,
				'7' => 0,
				'8' => 0,
				'9' => 0,
				'10' => 0,
			);
		} else {
			return $this->stock[$idAlmacen];
		}
	}

	//GETS y SETS
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
	protected function getStock() {
		if (!isset($this->_stock)){
			$where = 'cod_material = ' . Datos::objectToDB($this->idMaterial) . ' AND ';
			$where .= 'cod_color = ' . Datos::objectToDB($this->idColor) . ' ';
			$stocks = Factory::getInstance()->getListObject('StockMP', $where);
			if (count($stocks)) {
				$aux = array();
				foreach ($stocks as $stock) {
					/** @var Stock $stock */
					$aux[$stock->idAlmacen] = $stock->cantidad;
				}
				$this->_stock = $aux;
			}
		}
		return $this->_stock;
	}
	protected function setStock($stock) {
		$this->_stock = $stock;
		return $this;
	}
	protected function getUsuario() {
		if (!isset($this->_usuario)){
			$this->_usuario = Factory::getInstance()->getUsuario($this->idUsuario);
		}
		return $this->_usuario;
	}
	protected function setUsuario($usuario) {
		$this->_usuario = $usuario;
		return $this;
	}
}

?>