<?php

class DocumentoAplicacionHaber extends DocumentoAplicacion {
	public function aplicar(DocumentoAplicacionDebe $documentoAplicacionDebe) {
		$documentoAplicacionDebe->aplicar($this);
	}
}

?>