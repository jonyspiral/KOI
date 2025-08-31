<?php

/**
 * @property RemitoProveedor    		$remitoProveedor
 * @property Material           		$material
 * @property ColorMateriaPrima 			$colorMateriaPrima
 * @property RemitoPorOrdenDeCompra[]   $remitosPorOrdenesDeCompra
 */

class RemitoProveedorItem extends Base {
	const        _primaryKey = '["idRemitoProveedor", "numeroDeItem"]';

	public		$idRemitoProveedor;
	protected	$_remitoProveedor;
	public		$numeroDeItem;
	public		$idMaterial;
	public		$idColorMaterial;
	protected	$_material;
	protected	$_colorMateriaPrima;
	public		$cantidad;
	public		$cantidades;
	public		$fueraDeOrden;
	public		$embalaje;
	public		$remitosPorOrdenesDeCompra;

	public function __construct() {
		$this->remitosPorOrdenesDeCompra = array();
		$this->cantidades = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		parent::__construct();
	}

	public function addRemitoPorOrdenDeCompra($remitoPorOrdenDeCompra) {
		$this->remitosPorOrdenesDeCompra[] = $remitoPorOrdenDeCompra;
	}

	public function reversarOrdenDeCompra() {
		$where = 'cod_remito_proveedor = ' . Datos::objectToDB($this->remitoProveedor->id);
		$where .= ' AND nro_item_remito_proveedor = ' . Datos::objectToDB($this->numeroDeItem);
		$orderBy = 'ORDER BY cod_remito_orden_de_compra ASC;';
		$remitosPorOrdenDeCompra = Factory::getInstance()->getListObject('RemitoPorOrdenDeCompra', $where . $orderBy);

		foreach ($remitosPorOrdenDeCompra as $remitoPorOrdenDeCompra) {
			/** @var RemitoPorOrdenDeCompra $remitoPorOrdenDeCompra */
			$ordenDeCompraItem = Factory::getInstance()->getOrdenDeCompraItem($remitoPorOrdenDeCompra->idOrdenDeCompra, $remitoPorOrdenDeCompra->numeroDeItemOrdenDeCompra);
			$ordenDeCompraItem->cantidadPendiente += $remitoPorOrdenDeCompra->cantidadOc;
			for ($i = 1; $i < 16; $i++) {
				$ordenDeCompraItem->cantidadesPendientes[$i] += $remitoPorOrdenDeCompra->cantidadesOc[$i];
			}
			$ordenDeCompraItem->guardar();
			$remitoPorOrdenDeCompra->borrar();
		}
	}

	public function borrar() {
		$this->reversarOrdenDeCompra();
		return parent::borrar();
	}

	//GETS y SETS
	protected function getRemitoProveedor() {
		if (!isset($this->_remitoProveedor)) {
			$this->_remitoProveedor = Factory::getInstance()->getRemitoProveedor($this->idRemitoProveedor);
		}
		return $this->_remitoProveedor;
	}

	protected function setRemitoProveedor($remitoProveedor) {
		$this->_remitoProveedor = $remitoProveedor;
		return $this;
	}

	protected function getMaterial() {
		if (!isset($this->_material)) {
			$this->_material = Factory::getInstance()->getMaterial($this->idMaterial);
		}
		return $this->_material;
	}

	protected function setMaterial($material) {
		$this->_material = $material;
		return $this;
	}

	protected function getColorMateriaPrima() {
		if (!isset($this->_colorMateriaPrima)) {
			$this->_colorMateriaPrima = Factory::getInstance()->getColorMateriaPrima($this->material->id, $this->idColorMaterial);
		}
		return $this->_colorMateriaPrima;
	}

	protected function setColorMateriaPrima($colorMateriaPrima) {
		$this->_colorMateriaPrima = $colorMateriaPrima;
		return $this;
	}
}

?>