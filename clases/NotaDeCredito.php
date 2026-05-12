<?php

/**
 * @property array					$detalle
 * @property Factura				$documentoCancelatorio
 * @property CausaNotaDeCredito		$causa
 * @property int					$cantidadPares
 */

class NotaDeCredito extends DocumentoHaber implements OperacionStock {
	public		$cancelNumero;
	protected	$_documentoCancelatorio;
	public		$idCausa;		//Es exclusivo de las NCR. Es la causa que generó la NCR. Hay una tabla/ABM
	protected	$_causa;
	protected	$_cantidadPares;

	public function guardar() {
		try {
			Factory::getInstance()->beginTransaction();

			$this->tipoDocumento = TiposDocumento::notaDeCredito;
			$this->letra = $this->getLetra();
			$this->puntoDeVenta = ($this->empresa != 1 || $this->letra == 'E' ? 1 : (Config::encinitas() ? Config::PUNTO_VENTA_NCNTS : 2)); //Si es cuenta 2 o ncr 'E', no es electrónica

			//$this->descuentoComercialImporte = Funciones::toFloat($this->importeNeto * $this->descuentoComercialPorc / 100, 2);

			//$this->subtotal = $this->importeNeto + $this->importeNoGravado - $this->descuentoComercialImporte - $this->descuentoDespachoImporte;
			//$this->subtotal2 = $this->subtotal + $this->ivaImporte1 + $this->ivaImporte2 + $this->ivaImporte3;
			//$this->importeTotal = $this->subtotal2;

			$this->importePendiente = $this->importeTotal;

			parent::guardarSinCae(); //Lo hago primero por el ID (para el $keyObjeto)

			if (!$this->tieneDetalle()) {
				$this->stock();
			}

			if ($this->hayQuePedirCae()) {
				$this->obtenerCae();
			}

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		return $this;
	}

	private function calcularImporteSubtotal() {
		if (!isset($this->_importeSubtotal)) {
			$this->_importeSubtotal = 0;
			foreach ($this->getDetalleItems() as $item)
				$this->_importeSubtotal += Funciones::toFloat(($item->precioFactura * $item->cantidadTotal));
		}
		return $this->_importeSubtotal;
	}

	protected function calcularImporteDescuentoSinGravar() {
		if (!isset($this->_importeDescuento)) {
			$descuentoDespachos = 0;
			foreach ($this->getDetalleItems() as $item)
				$descuentoDespachos += ($item->cantidadTotal * $item->precioUnitario) * (Funciones::toFloat(($item->descuentoPedido - $item->recargoPedido) / 100));
			$multiplicador = ($this->llevaIvaDiscriminado() ? 1 : (1 + Funciones::toFloat($this->cliente->condicionIva->porcentajes[1] / 100)));
			$descuentoComercial = Funciones::toFloat($this->descuentoComercialImporte * $multiplicador);
			$this->_importeDescuento = $descuentoDespachos + $descuentoComercial;
		}
		return $this->_importeDescuento;
	}

	protected function calcularImporteTotal() {
		if (!isset($this->_importeTotal)) {
			$this->_importeTotal = $this->calcularImporteNetoGravado() + $this->calcularImporteNoGravado() + $this->calcularImporteIva() - $this->calcularImporteDescuentoSinGravar();
		}
		return $this->_importeTotal;
	}

	public function borrar() {
		try {
			Factory::getInstance()->beginTransaction();

			parent::borrar();
			if (!$this->tieneDetalle()) {
				$this->stock();
			}

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		return $this;
	}

	protected function validarGuardar() {
		parent::validarGuardar();

		if (self::CANT_MAX_DETALLE && $this->cantidadArticulos > self::CANT_MAX_DETALLE) {
			throw new FactoryExceptionCustomException('La nota de crédito que se intenta crear supera el máximo de artículos permitidos (' . self::CANT_MAX_DETALLE . ')');
		}

		//Hardcodeado a Clientes Varios (observación obligatoria)
		if (($this->cliente->id == 291 || $this->cliente->id == 589) && (is_null($this->observaciones) || empty($this->observaciones))) {
			throw new FactoryExceptionCustomException('La nota de crédito debe tener el nombre del empleado en "observaciones"');
		}
	}

	protected function crearFormulario() {
		$this->formulario = new FormularioNotaDeCredito();
	}

	protected function llenarFormulario() {
		//Lleno todas las variables particulares del formulario
		parent::llenarFormulario();
		$this->formulario->cantidadPares = $this->getCantidadPares();
	}

	/************************************** STOCK **************************************/

	public function stock() {
		return Stock::registrarOperacion($this);
	}

	public function stockTipoMovimiento() {
		return ($this->modo == Modos::delete) ? TiposMovimientoStock::negativo : TiposMovimientoStock::positivo;
	}

	public function stockTipoOperacion() {
		return TiposOperacionStock::notaDeCredito;
	}

	public function stockKeyObjeto() {
		return $this->getPKSerializada();
	}

	public function stockObservacion() {
		return 'NCR Nº ' . $this->empresa . '-' . $this->numeroComprobante . '-' . $this->letra;
	}

	public function stockDetalle() {
		$ret = array();
		foreach ($this->getDetalle() as $item) {
			/** @var DocumentoItem $item */
			if (!isset($ret[$item->almacen->id])) {
				$ret[$item->almacen->id] = array();
			}
			if (!isset($ret[$item->almacen->id][$item->articulo->id])) {
				$ret[$item->almacen->id][$item->articulo->id] = array();
			}
			if (!isset($ret[$item->almacen->id][$item->articulo->id][$item->colorPorArticulo->id])) {
				$ret[$item->almacen->id][$item->articulo->id][$item->colorPorArticulo->id] = $item->cantidad;
			} else {
				for ($i = 1; $i <= 10; $i++) {
					$ret[$item->almacen->id][$item->articulo->id][$item->colorPorArticulo->id][$i] += $item->cantidad[$i];
				}
			}
		}
		return $ret;
	}

	/************************************** ***** **************************************/

	//GETS y SETS
	protected function getCantidadPares() {
		if (!isset($this->_cantidadPares)){
			$cant = 0;
			if (!$this->tieneDetalle()) {
				foreach ($this->getDetalle() as $item) {
					$cant += $item->cantidadTotal;
				}
			}
			$this->_cantidadPares = $cant;
		}
		return $this->_cantidadPares;	
	}
	protected function setCantidadPares($cantidadPares) {
		$this->_cantidadPares = $cantidadPares;
		return $this;
	}
	protected function getCausa() {
		if (!isset($this->_causa)){
			$this->_causa = Factory::getInstance()->getCausaNotaDeCredito($this->idCausa);
		}
		return $this->_causa;
	}
	protected function setCausa($causa) {
		$this->_causa = $causa;
		return $this;
	}
	protected function getDocumentoCancelatorio() {
		if (!isset($this->_documentoCancelatorio)){
			$this->_documentoCancelatorio = Factory::getInstance()->getNotaDeCredito($this->empresa, $this->puntoDeVenta, 'FAC', $this->cancelNumero, $this->letra);
		}
		return $this->_documentoCancelatorio;
	}
	protected function setDocumentoCancelatorio($documentoCancelatorio) {
		$this->_documentoCancelatorio = $documentoCancelatorio;
		return $this;
	}
}

?>