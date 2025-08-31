<?php

/**
 * @property array	$aplicaciones
 */

class DocumentoHaber extends Documento {
	protected	$_aplicaciones;

	/************************************** CONTABILIDAD **************************************/

	public function contabilidadDetalle() {
		$det = array();

		/* AGREGO LAS FILAS DE LOS IMPORTES */
		$fecha = ($this->fecha ? $this->fecha : Funciones::hoy());

		$i = 1;

		if ($this->tieneDetalle()) {
			foreach ($this->detalleItems as $item) {
				/** @var DocumentoItem $item */
				$fila = array();
				$fila['numeroFila'] = $i;
				$fila['fechaVencimiento'] = $fecha;
				$fila['imputacion'] = $item->imputacion->id;
				$fila['importeDebe'] = Funciones::toFloat($item->importeTotal, 2);
				$fila['importeHaber'] = 0;
				$fila['observaciones'] = '';
				$det[] = $fila;
				$i++;
			}
		} else {
			$fila = array();
			$fila['numeroFila'] = $i;
			$fila['fechaVencimiento'] = $fecha;
			$fila['imputacion'] = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::ingresosPorVentas)->idImputacion;
			$fila['importeDebe'] = Funciones::toFloat($this->importeNeto, 2);
			$fila['importeHaber'] = 0;
			$fila['observaciones'] = '';
			$det[] = $fila;
		}

		$fila = array();
		$fila['numeroFila'] = $i;
		$fila['fechaVencimiento'] = $fecha;
		$fila['imputacion'] = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::ivaDebitoFiscal)->idImputacion;
		$fila['importeDebe'] = Funciones::toFloat($this->ivaImporte1, 2) + Funciones::toFloat($this->ivaImporte2, 2) + Funciones::toFloat($this->ivaImporte3, 2);
		$fila['importeHaber'] = 0;
		$fila['observaciones'] = '';
		$det[] = $fila;
		$i++;

		if ($this->descuentoComercialImporte || $this->descuentoDespachoImporte) {
			$dosDescuentos = (Funciones::toFloat($this->descuentoComercialImporte) >= 0 && Funciones::toFloat($this->descuentoDespachoImporte) >= 0);
			$dosRecargos = (Funciones::toFloat($this->descuentoComercialImporte) <= 0 && Funciones::toFloat($this->descuentoDespachoImporte) <= 0);
			if (!$dosDescuentos && !$dosRecargos) {
				$fila = array();
				$fila['numeroFila'] = $i;
				$fila['fechaVencimiento'] = $fecha;
				$fila['imputacion'] = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::descuentosComerciales)->idImputacion;
				$fila['importeDebe'] = 0;
				$fila['importeHaber'] = Funciones::toFloat((Funciones::toFloat($this->descuentoComercialImporte) > 0) ? $this->descuentoComercialImporte : $this->descuentoDespachoImporte, 2);
				$fila['observaciones'] = '';
				$det[] = $fila;
				$i++;

				$fila = array();
				$fila['numeroFila'] = $i;
				$fila['fechaVencimiento'] = $fecha;
				$fila['imputacion'] = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::recargosComerciales)->idImputacion;
				$fila['importeDebe'] = Funciones::toFloat((Funciones::toFloat($this->descuentoComercialImporte) < 0) ? $this->descuentoComercialImporte : $this->descuentoDespachoImporte, 2);
				$fila['importeHaber'] = 0;
				$fila['observaciones'] = '';
				$det[] = $fila;
			} else {
				$imp = Funciones::toFloat(abs($this->descuentoComercialImporte) + abs($this->descuentoDespachoImporte), 2);
				$fila = array();
				$fila['numeroFila'] = $i;
				$fila['fechaVencimiento'] = $fecha;
				$fila['imputacion'] = Factory::getInstance()->getParametroContabilidad(($dosDescuentos ? ParametrosContabilidad::descuentosComerciales : ParametrosContabilidad::recargosComerciales))->idImputacion;
				$fila['importeDebe'] = $dosRecargos ? $imp : 0;
				$fila['importeHaber'] = $dosDescuentos ? $imp : 0;
				$fila['observaciones'] = '';
				$det[] = $fila;
			}
			$i++;
		}

		$fila = array();
		$fila['numeroFila'] = $i;
		$fila['fechaVencimiento'] = $fecha;
		$fila['imputacion'] = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::deudoresPorVentas)->idImputacion;
		$fila['importeDebe'] = 0;
		$fila['importeHaber'] = Funciones::toFloat($this->importeTotal, 2);
		$fila['observaciones'] = '';
		$det[] = $fila;

		return $det;
	}

	/************************************** ************ **************************************/

	//GETS y SETS
	protected function getAplicaciones() {
		if (!isset($this->_aplicaciones)){
			$where = 'anulada <> \'S\' AND ';
			$where .= 'cancel_punto_venta = ' . Datos::objectToDB($this->puntoDeVenta) . ' AND ';
			$where .= 'cancel_tipo_docum = ' . Datos::objectToDB($this->tipoDocumento) . ' AND ';
			$where .= 'cancel_nro_documento = ' . Datos::objectToDB($this->numero) . ' AND ';
			$where .= 'cancel_letra = ' . Datos::objectToDB($this->letra) . ' AND ';
			$where .= 'empresa = ' . Datos::objectToDB($this->empresa);
			$this->_aplicaciones = Factory::getInstance()->getListObject('DocumentoHija', $where);
		}
		return $this->_aplicaciones;
	}
	protected function setAplicaciones($aplicaciones) {
		$this->_aplicaciones = $aplicaciones;
		return $this;
	}
}

?>