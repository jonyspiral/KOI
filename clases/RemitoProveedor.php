<?php

/**
 * @property RemitoProveedorItem[]		$detalle
 * @property Proveedor					$proveedor
 * @property DocumentoProveedor			$facturaProveedor
 * @property Almacen					$almacen
 * @property Int						$nroCompuestoRemito
 */

class RemitoProveedor extends Base implements OperacionStock {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idProveedor;
	protected	$_proveedor;
	public		$numero;
	public		$sucursal;
	public		$letra;
	public		$fechaRecepcion;
	public		$idAlmacen;
	protected	$_almacen;
	public		$conOrdenDeCompra;
	public		$idFacturaProveedor;
	protected	$_facturaProveedor;
	protected	$_detalle;
	protected	$_nroCompuestoRemito;
	protected	$_nroCompletoRemito;
	public		$esHexagono;

	public function guardar() {
		try {
			Factory::getInstance()->beginTransaction();

			if(!empty($this->idFacturaProveedor)) {
				throw new FactoryExceptionCustomException('No puede editar un remito que ya fue vinculado a una factura');
			}

			parent::guardar();
			//$this->stock();

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		return $this;
	}

	public function borrar() {
		try {
			Factory::getInstance()->beginTransaction();

			$remitosPorOrdenDeCompra = Factory::getInstance()->getListObject('RemitoPorOrdenDeCompra', 'cod_remito_proveedor = ' . $this->id);

			foreach($remitosPorOrdenDeCompra as $remitoPorOrdenDeCompra) {
				if ($remitoPorOrdenDeCompra->cantidad != $remitoPorOrdenDeCompra->cantidadPendiente) {
					throw new FactoryExceptionCustomException('No puede borrar o editar un remito que ya fue vinculado a una factura');
				}

				$documentosProveedorItem = Factory::getInstance()->getListObject('DocumentoProveedorItem', 'cod_remito_orden_de_compra = ' . Datos::objectToDB($remitoPorOrdenDeCompra->id));
				foreach ($documentosProveedorItem as $documentoProveedorItem) {
					/** @var DocumentoProveedorItem $documentoProveedorItem */
					$documentoProveedorItem->remitoPorOrdenDeCompra = Factory::getInstance()->getRemitoPorOrdenDeCompra();
					$documentoProveedorItem->guardar();
				}
			}

			foreach($this->detalle as $remitoItem) {
				/** @var RemitoProveedorItem $remitoItem */
				$remitoItem->reversarOrdenDeCompra();
				Factory::getInstance()->marcarParaBorrar($remitoItem);
			}

			parent::borrar();
			//$this->stock();

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		return $this;
	}

	public function esHexagono() {
		return $this->esHexagono == 'S';
	}

	public function getImporte() {
		$importe = 0.00;
		foreach($this->getDetalle() as $item)
			$importe += Funciones::toFloat($item->cantidadTotal * $item->precioUnitarioFinal);
		return $importe;
	}

	public function getImporteSinDescuentoRecargo() {
		$importe = 0.00;
		foreach($this->getDetalle() as $item)
			$importe += Funciones::toFloat($item->cantidadTotal * $item->precioUnitario);
		return $importe;
	}

	public function facturado() {
		return !is_null($this->facturaProveedor);
	}


	public function addDetalle($remitoItem) {
		$this->_detalle[] = $remitoItem;
	}

	/************************************** STOCK **************************************/

	public function stock() {
		return StockMP::registrarOperacion($this);
	}

	public function stockTipoMovimiento() {
		return ($this->modo == Modos::delete ? TiposMovimientoStock::negativo : TiposMovimientoStock::positivo);
	}

	public function stockTipoOperacion() {
		return TiposOperacionStockMP::remito;
	}

	public function stockKeyObjeto() {
		return $this->getPKSerializada();
	}

	public function stockObservacion() {
		return 'Remito Nº ' . $this->getNroCompletoRemito() . ' - Prov. ' . $this->proveedor->id;
	}

	public function stockDetalle() {
		$almacen = '01';
		$ret = array($almacen => array());
		foreach ($this->detalle as $item) {
			$factorConversion = $item->colorMateriaPrima->material->factorConversion;
			for ($i = 1; $i <= 10; $i++) {
				$ret[$almacen][$item->material->id][$item->colorMateriaPrima->idColor][$i] += $item->cantidades[$i] * $factorConversion;
			}
		}
		return $ret;
	}

	/************************************** ***** **************************************/

	//GETS y SETS
	protected function getAlmacen() {
		if (!isset($this->_almacen)){
			$this->_almacen = Factory::getInstance()->getProveedor($this->idAlmacen);
		}
		return $this->_almacen;
	}
	protected function setAlmacen($almacen) {
		$this->_almacen = $almacen;
		return $this;
	}
	protected function getDetalle() {
		if (!isset($this->_detalle)){
			$where = 'cod_remito_proveedor = ' . Datos::objectToDB($this->id);
			$this->_detalle = Factory::getInstance()->getListObject('RemitoProveedorItem', $where);
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
	protected function getProveedor() {
		if (!isset($this->_proveedor)){
			$this->_proveedor = Factory::getInstance()->getProveedor($this->idProveedor);
		}
		return $this->_proveedor;
	}
	protected function setProveedor($proveedor) {
		$this->_proveedor = $proveedor;
		return $this;
	}
	protected function getFacturaProveedor() {
		if (!isset($this->_facturaProveedor)){
			$this->_facturaProveedor = Factory::getInstance()->getDocumentoProveedor($this->idFacturaProveedor);
		}
		return $this->_facturaProveedor;
	}
	protected function setFacturaProveedor($facturaProveedor) {
		$this->_facturaProveedor = $facturaProveedor;
		return $this;
	}
	protected function getNroCompuestoRemito() {
		if (!isset($this->_nroCompuestoRemito)) {
			$this->_nroCompuestoRemito = 'R' . Funciones::padLeft($this->sucursal, 4, 0) . Funciones::padLeft($this->numero, 8, 0);
		}
		return $this->_nroCompuestoRemito;
	}
	protected function getNroCompletoRemito() {
		if (!isset($this->nroCompletoRemito)) {
			$this->_nroCompletoRemito = Funciones::padLeft($this->sucursal, 4, 0) . '-' . Funciones::padLeft($this->numero, 8, 0);
		}
		return $this->_nroCompletoRemito;
	}
}

?>