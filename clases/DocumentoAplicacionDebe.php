<?php

class DocumentoAplicacionDebe extends DocumentoAplicacion {
	public function aplicar(DocumentoAplicacionHaber $documentoAplicacionHaber) {
		if ($this->anulado() || $documentoAplicacionHaber->anulado()){
			throw new FactoryExceptionCustomException('Alguno de los documentos se encuentra anulado. Por favor actualice la lista');
		}
		if (!($this->importePendiente > 0 && $documentoAplicacionHaber->importePendiente > 0)){
			throw new FactoryExceptionCustomException('Alguno de los documentos ya se encuentra completamente aplicado. Por favor actualice la lista');
		}

		$haberMayorDebe = ($documentoAplicacionHaber->importePendiente >= $this->importePendiente);

		$pendDebe = $this->importePendiente;
		$pendHaber = $documentoAplicacionHaber->importePendiente;
		$this->importePendiente = ($haberMayorDebe || (($pendDebe - $pendHaber) < 0.01)) ? 0 : ($pendDebe - $pendHaber);
		$documentoAplicacionHaber->importePendiente = ($haberMayorDebe && (($pendHaber - $pendDebe) > 0.01)) ? ($pendHaber - $pendDebe) : 0;

		$hija = Factory::getInstance()->getDocumentoHija();
		$hija->empresa = $this->empresa;
		$hija->importe = $haberMayorDebe ? $pendDebe : $pendHaber;
		$hija->documentoCancelatorio = $documentoAplicacionHaber;

		if ($this->importePendiente == 0) {
			$hijas = $this->hijas;
			$hijas[] = $hija;
			$acumuladoDias = 0;
			foreach ($hijas as $h) {
				/** @var DocumentoHija $hija */
				$porcentaje = $h->importe / $this->importeTotal;
				$rec = $h->documentoCancelatorio->documento;
				$auxFecha = $rec->fecha;
				/** @var Recibo $rec */ //TAL VEZ NO LO ES!
				if ($h->documentoCancelatorio->tipoDocumento == TiposDocumento::recibo && !is_null($rec->fechaPonderadaPago)) {
					$auxFecha = $rec->fechaPonderadaPago;
				}
				$dias = Funciones::diferenciaFechas($auxFecha, $this->fecha, false);
				$acumuladoDias += ($porcentaje * $dias);
			}
			$acumuladoDias = round($acumuladoDias);
			$this->diasPromedioPago = $acumuladoDias;
		}

		$hija->madre = $this;
		$hija->guardar();
	}

	protected function getHijas() {
		if (!isset($this->_hijas)){
			$where = 'empresa = ' . Datos::objectToDB($this->empresa) .' AND ';
			$where .= 'madre_punto_venta = ' . Datos::objectToDB($this->puntoDeVenta) . ' AND ';
			$where .= 'madre_tipo_docum = ' . Datos::objectToDB($this->tipoDocumento) . ' AND ';
			$where .= 'madre_nro_documento = ' . Datos::objectToDB($this->nroDocumento) . ' AND ';
			$where .= 'madre_letra = ' . Datos::objectToDB($this->letra);

			$this->_hijas = Factory::getInstance()->getListObject('DocumentoHija', $where);
		}
		return $this->_hijas;
	}
}

?>