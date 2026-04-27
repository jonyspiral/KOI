<?php

/**
 * @property NotaDeCredito		$documentoCancelatorio
 * @property int				$cantidadPares
 * @property array				$detalle
 * @property array				$detalleItems
 * @property Ecommerce_Order	$ecommerceOrder
 */

class Factura extends DocumentoDebe {
	public		$cancelNumero;
	protected	$_documentoCancelatorio;
	protected	$_cantidadPares;
	public		$idEcommerceOrder;
	protected	$_ecommerceOrder;

	public static function facturar($datos, $funcionalidad = false) {
		/*
			Esta función se encargará de generar una factura de uno o más remitos.
			$datos será un array de distintos Remito a facturar (los Remito se facturan ENTEROS). Ejemplo:
				array(
					'empresa' => 1,
					'idCliente' => 33,
					'observaciones' => 'caca culo',
					'remitos' => array(
						array(
							'despachoNumero' => 33,
							'numeroDeItem' => 1
						)
					),
				)
		*/

		$factura = Factory::getInstance()->getFactura();

		$factura->empresa = $datos['empresa'];
		$factura->cliente = Factory::getInstance()->getCliente($datos['idCliente']);
		$factura->ecommerceOrder = Factory::getInstance()->getEcommerce_Order($datos['idEcommerceOrder']);
		$factura->observaciones = $datos['observaciones'];

		$auxDetalle = array();
		foreach ($datos['remitos'] as $rem) {
			$remito = Factory::getInstance()->getRemito($factura->empresa, $rem['numero'], $rem['letra']);
			foreach ($remito->detalle as $item) {
				($item->precioAlFacturar == 'S') && $item->actualizarPrecioFacturar();
			}
			$auxDetalle[] = $remito;
		}
		$factura->detalle = $auxDetalle;

		//Calculo el importe NETO
		$importeNeto = 0;
		$importeConDescuento = 0;
		$factura->tieneDetalle = 'N';
		foreach ($factura->detalle as $remito) {
			$importeNeto += $remito->getImporteSinDescuentoRecargo();
			$importeConDescuento += $remito->getImporte();
		}
		$factura->importeNeto = $importeNeto;

		//Este precio (descuentoDespachoImporte) tiene los descuentos Y LOS RECARGOS! Pero como sólo se usa para acá,
		//agrupo to_do en el campo DESCUENTO (si es negativo es porque fue recargo)
		$factura->descuentoDespachoImporte = Funciones::toFloat($importeNeto - $importeConDescuento);
		$factura->descuentoComercialPorc = $factura->cliente->creditoDescuentoEspecial;
		$factura->descuentoComercialImporte = Funciones::toFloat($factura->importeNeto * ($factura->descuentoComercialPorc / 100));

		if ($factura->empresa == 2) {
			$factura->ivaPorcentaje1 = 0;
			$factura->ivaPorcentaje2 = 0;
			$factura->ivaPorcentaje3 = 0;
			$factura->ivaImporte1 = 0;
			$factura->ivaImporte2 = 0;
			$factura->ivaImporte3 = 0;
		} else {
			$iva = array();
			foreach ($factura->detalle as $remito) {
				foreach ($remito->detalle as $item) {
					if (!isset($iva[Funciones::toString($item->ivaPorcentaje)]))
						$iva[Funciones::toString($item->ivaPorcentaje)] = 0;
					$iva[Funciones::toString($item->ivaPorcentaje)] += Funciones::toFloat($item->precioUnitarioFinal * $item->cantidadTotal);
				}
			}
			if (isset($iva['21'])) //Hardcodeo. Es el porcentaje en el cual se aplica el descuento comercial
				$iva['21'] = Funciones::toFloat($iva['21'] - $factura->descuentoComercialImporte);
			$j = 1;
			foreach ($iva as $porc => $valor) {
				$attr1 = 'ivaPorcentaje' . $j;
				$attr2 = 'ivaImporte' . $j;
				$factura->$attr1 = Funciones::toFloat($porc);
				$factura->$attr2 = Funciones::toFloat($valor * (Funciones::toFloat($porc) / 100));
				$j++;
			}
		}
		$factura->importeNoGravado = 0; //?
		$factura->importeTotal = $factura->importeNeto + $factura->importeNoGravado - $factura->descuentoComercialImporte - $factura->descuentoDespachoImporte + $factura->ivaImporte1 + $factura->ivaImporte2 + $factura->ivaImporte3;
		$factura->importePendiente = $factura->importeTotal;
		$factura->letra = $factura->getLetra();
		$factura->puntoDeVenta = Config::encinitas() ? Config::PUNTO_VENTA_NCNTS : ($factura->empresa != 1 || $factura->letra == 'E' ? 1 : 2); //Si es cuenta 2 o factura 'E', no es electrónica
		$factura->tipoDocumento = TiposDocumento::factura;

		if ($factura->getCantidadPares() <= 0) {
			throw new FactoryExceptionCustomException('No se puede generar una factura con CERO pares');
		}

		$factura->guardar()->notificar($funcionalidad);

		return $factura;
	}

	protected function validarGuardar() {
		$this->cliente->comprobarHabilitadoFacturar();
	}

	protected function comprobaciones() {
		if ($this->anulado == 'S')
			throw new FactoryExceptionCustomException('No se puede generar la factura porque fue anulada');
	}

	protected function crearFormulario() {
		$this->formulario = new FormularioFactura();
	}

	protected function llenarFormulario() {
		//Lleno todas las variables particulares del formulario
		parent::llenarFormulario();
		$this->formulario->cantidadPares = $this->getCantidadPares();
		//Mando a armar el array de remitos incluidos y el detalle
		$this->formulario->remitosIncluidos = $this->armoRemitosParaFormulario();
	}

	private function armoRemitosParaFormulario() {
		//Array con los número de remitos que componen la factura
		//No hace falta la letra (siempre R), ni la empresa (es la misma que la factura) ni nada
		$arr = array();
		if (!$this->tieneDetalle()) {
			foreach ($this->getDetalle() as $o) {
				$arr[] = $o->numero;
			}
		}
		return $arr;
	}

	//GETS y SETS
	protected function getCantidadPares() {
		if (!isset($this->_cantidadPares)){
			$cant = 0;
			if (!$this->tieneDetalle()) {
				foreach ($this->getDetalle() as $o) {
					$cant += $o->cantidadPares;
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
	protected function getDetalle() {
		//El detalle lo obtiene de DESPACHOS_D o de DOCUMENTOS_D, dependiendo de tieneDetalle 
		if (!isset($this->_detalle)){
			if ($this->tieneDetalle()) {
				$where = 'empresa = ' . Datos::objectToDB($this->empresa) . ' AND punto_venta = ' . Datos::objectToDB($this->puntoDeVenta) . ' AND tipo_docum = ' . Datos::objectToDB($this->tipoDocumento) . ' AND nro_documento = ' . Datos::objectToDB($this->numero) . ' AND letra = ' . Datos::objectToDB($this->letra);
				$this->_detalle = Factory::getInstance()->getListObject('DocumentoItem', $where);
			} else {
				$where = 'empresa = ' . Datos::objectToDB($this->empresa) . ' AND punto_venta_factura = ' . Datos::objectToDB($this->puntoDeVenta) . ' AND tipo_docum_factura = ' . Datos::objectToDB($this->tipoDocumento) . ' AND nro_factura = ' . Datos::objectToDB($this->numero) . ' AND letra_factura = ' . Datos::objectToDB($this->letra);
				$this->_detalle = Factory::getInstance()->getListObject('Remito', $where);
			}
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
	protected function getDetalleItems() {
		//El detalle lo obtiene de DESPACHOS_D o de DOCUMENTOS_D, dependiendo de tieneDetalle 
		if (!isset($this->_detalleItems)){
			if ($this->tieneDetalle()) {
				$this->_detalleItems = $this->getDetalle();
			} else {
				$items = array();
				foreach ($this->getDetalle() as $thiss)
					foreach ($thiss->detalle as $despachoItem)
						$items[] = $despachoItem;
				$this->_detalleItems = $items;
			}
		}
		return $this->_detalleItems;
	}
	protected function setDetalleItems($detalleItems) {
		$this->_detalleItems = $detalleItems;
		return $this;
	}
	protected function getDocumentoCancelatorio() {
		if (!isset($this->_documentoCancelatorio)){
			$this->_documentoCancelatorio = Factory::getInstance()->getNotaDeCredito($this->empresa, $this->puntoDeVenta, 'NCR', $this->cancelNumero, $this->letra);
		}
		return $this->_documentoCancelatorio;
	}
	protected function setDocumentoCancelatorio($documentoCancelatorio) {
		$this->_documentoCancelatorio = $documentoCancelatorio;
		return $this;
	}
	protected function getEcommerceOrder() {
		if (!isset($this->_ecommerceOrder)){
			$this->_ecommerceOrder = Factory::getInstance()->getEcommerce_Order($this->idEcommerceOrder);
		}
		return $this->_ecommerceOrder;
	}
	protected function setEcommerceOrder($ecommerceOrder) {
		$this->_ecommerceOrder = $ecommerceOrder;
		return $this;
	}
}

?>