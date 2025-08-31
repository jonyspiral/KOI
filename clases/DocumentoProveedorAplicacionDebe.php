<?php

class DocumentoProveedorAplicacionDebe extends DocumentoProveedorAplicacion {
	public function aplicar(DocumentoProveedorAplicacionHaber $documentoProveedorAplicacionHaber) {
		if ($this->anulado() || $documentoProveedorAplicacionHaber->anulado()){
			throw new FactoryExceptionCustomException('Alguno de los documentos se encuentra anulado. Por favor actualice la lista');
		}
		if (!($this->importePendiente > 0 && $documentoProveedorAplicacionHaber->importePendiente > 0)){
			throw new FactoryExceptionCustomException('Alguno de los documentos ya se encuentra completamente aplicado. Por favor actualice la lista');
		}

		$haberMayorDebe = ($documentoProveedorAplicacionHaber->importePendiente >= $this->importePendiente);

		$pendDebe = $this->importePendiente;
		$pendHaber = $documentoProveedorAplicacionHaber->importePendiente;
		$this->importePendiente = ($haberMayorDebe || (($pendDebe - $pendHaber) < 0.01)) ? 0 : ($pendDebe - $pendHaber);
		$documentoProveedorAplicacionHaber->importePendiente = ($haberMayorDebe && (($pendHaber - $pendDebe) > 0.01)) ? ($pendHaber - $pendDebe) : 0;

		$hija = Factory::getInstance()->getDocumentoProveedorHija();
		$hija->empresa = $this->empresa;
		$hija->importe = $haberMayorDebe ? $pendDebe : $pendHaber;
		$hija->documentoCancelatorio = $documentoProveedorAplicacionHaber;
		$hija->madre = $this;

		$hija->guardar();
	}
}

?>