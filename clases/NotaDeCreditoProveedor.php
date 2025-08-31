<?php

/**
 * @property FacturaProveedor			$facturaCancelatoria
 */

class NotaDeCreditoProveedor extends DocumentoProveedor {
	public		$tipoDocum = 'NCR';
	public		$idFacturaCancelatoria;
	protected	$_facturaCancelatoria;

	/************************************** CONTABILIDAD **************************************/

	public function contabilidadDetalle() {
		$det = array();

		/* AGREGO LAS FILAS DE LOS IMPORTES */
		$importeTotal = 0;
		$i = 1;
		$agrupados = array();
		foreach ($this->detalle as $item) {
			/** @var DocumentoProveedorItem $item */
			if (!isset($agrupados[$item->imputacion->id])) {
				$agrupados[$item->imputacion->id] = 0;
			}
			$agrupados[$item->imputacion->id] += Funciones::toFloat($item->importe, 2);
		}
		foreach ($agrupados as $imputacion => $importe) {
			$fila = array();
			$fila['numeroFila'] = $i;
			$fila['fechaVencimiento'] = $this->fecha;
			$fila['imputacion'] = $imputacion;
			$fila['importeDebe'] = 0;
			$fila['importeHaber'] = Funciones::toFloat($importe, 2);
			$fila['observaciones'] = '';
			$det[] = $fila;
			$importeTotal += $fila['importeHaber'];
			$i++;
		}
		foreach ($this->impuestos as $impuesto) {
			/** @var ImpuestoPorDocumentoProveedor $impuesto */
			$fila = array();
			$fila['numeroFila'] = $i;
			$fila['fechaVencimiento'] = $this->fecha;
			$fila['imputacion'] = $impuesto->impuesto->imputacion->id;
			$fila['importeDebe'] = 0;
			$fila['importeHaber'] = Funciones::toFloat($impuesto->importe, 2);
			$fila['observaciones'] = '';
			$det[] = $fila;
			$importeTotal += $fila['importeHaber'];
			$i++;
		}

		/* AGREGO LA FILA DEL DEBE */
		$fila = array();
		$fila['numeroFila'] = $i;
		$fila['fechaVencimiento'] = $this->fecha;
		$fila['imputacion'] = $this->getImputacionHaber();
		$fila['importeDebe'] = $importeTotal;
		$fila['importeHaber'] = 0;
		$fila['observaciones'] = $this->observaciones;

		//Agrego la fila DEBE antes de los detalles, para que aparezca primera
		$det = array_merge(array($fila), $det);

		return $det;
	}

	/************************************** ************ **************************************/

	//GETS Y SETS
	protected function getFacturaCancelatoria() {
		if (!isset($this->_facturaCancelatoria)){
			$this->_facturaCancelatoria = Factory::getInstance()->getFacturaProveedor($this->idFacturaCancelatoria);
		}
		return $this->_facturaCancelatoria;
	}
	protected function setFacturaCancelatoria($facturaCancelatoria) {
		$this->_facturaCancelatoria = $facturaCancelatoria;
		return $this;
	}
}

?>