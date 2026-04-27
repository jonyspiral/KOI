<?php

/**
 * @property array	$detalle
 */

class NotaDeDebito extends DocumentoDebe {

	public function __construct() {
		parent::__construct();
		$this->tieneDetalle = 'S';
	}

	public function guardar() {
		try {
			Factory::getInstance()->beginTransaction();

			$this->tipoDocumento = TiposDocumento::notaDeDebito;
			$this->letra = $this->getLetra();
			$this->puntoDeVenta = ($this->empresa != 1 || $this->letra == 'E' ? 1 : (Config::encinitas() ? Config::PUNTO_VENTA_NCNTS : 2)); //Si es cuenta 2 o ncr 'E', no es electrónica

			$this->importePendiente = $this->importeTotal;

			parent::guardar();

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		return $this;
	}

	protected function comprobaciones() {
		if ($this->anulado == 'S')
			throw new FactoryExceptionCustomException('No se puede generar la nota de débito porque fue anulada');
	}

	protected function crearFormulario() {
		$this->formulario = new FormularioNotaDeDebito();
	}

	protected function llenarFormulario() {
		//Lleno todas las variables particulares del formulario
		parent::llenarFormulario();
	}

	//GETS y SETS
}

?>