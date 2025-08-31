<?php

/**
 * @property Html2Pdf	$pdf
 */
class Formulario extends Base {
	protected	$nombreDocumento;
	public		$pdf;

	public		$empresa;
	public		$letra;
	public		$numero;
	public		$tipoDocumento;
	public		$fecha;
	public		$nombreCliente;
	public		$direccion;
	public		$nombreCondicionIva;
	public		$cuit;
	public		$condicionDeVenta;
	public		$subtotal;
	public		$subtotal2;
	public		$descuentos;
	public		$ivaPorc1;
	public		$ivaImporte1;
	public		$ivaPorc2;
	public		$ivaImporte2;
	public		$ivaPorc3;
	public		$ivaImporte3;
	public		$importeTotal;
	public		$cae;
	public		$caeVencimiento;
	protected	$_barcode;
	public		$observaciones;
	public		$detalle; //Array de objetos [{codArt: 350, nombreArt: 'Avril Woman', codColor: 'V', 'nombreColor': 'verde', cantidad: 2, precioUnitario: 310.50, precioTotal: 721.00}, {.}]

	public function crear(){
		$this->crearPdf();
		$this->pdf->create();
		return $this->pdf->pdfPath;
	}

	public function abrir(){
		$this->crearPdf();
		$this->pdf->open(false);
		//$this->pdf->deleteFiles();//Lo dejo? Lo saco? Lo guardo en otro lado el PDF? Sí, guardarlo en otro lado sería lo mejor!
	}

	protected function crearPdf(){
		if (!isset($this->pdf)) {
			$this->pdf = new Html2Pdf();
			$this->enviarDatos();
			
			switch (Funciones::toUpper($this->letra)) {
				case 'A':
				case 'B':
				case 'E':
					$letra = Funciones::toUpper($this->letra);
					break;
				default:
					throw new FactoryExceptionCustomException('La letra del documento es inválida');
			}
			switch ($this->empresa) {
				case 1:
				case 2:
					$empresa = $this->empresa;
					break;
				default:
					throw new FactoryExceptionCustomException('El número de empresa es inválido');
			}
			//Acá hay que definir qué modelo de Factura se usa según si es empresa 1 o 2, o si eso se lo delego al documento!!!
			$this->pdf->html = Html2Pdf::getHtmlFromPhp(Config::pathBase . 'includes/modelosFormularios/modelo' . $this->nombreDocumento . $letra . '_' . $empresa . (!$this->cae && $empresa == 1 ? '_I' : '') . (Config::encinitas() ? '_ncnts' : '') . '.php');
			$this->pdf->llevaHeader = false;
			$this->pdf->llevaFooter = false;
			$this->pdf->fileName = $this->nombreDocumento . '_' . Funciones::limpiarNombreDeArchivo(str_replace(' ', '_', $this->nombreCliente)) . '_' . $this->numero . '_' . $this->letra . '_' . $this->empresa;
			$this->pdf->marginTop = '1';
			$this->pdf->marginBottom = '1';
			$this->pdf->marginLeft = '1';
			$this->pdf->marginRight = '1';
		}
	}

	protected function enviarDatos() {
		$_POST['form_empresa'] = $this->empresa; //No sale impreso, pero es para saber qué documento se imprime
		$_POST['form_letra'] = $this->letra;
		$_POST['form_numero'] = $this->numero;
		$_POST['form_fecha'] = $this->fecha;
		$_POST['form_nombreCliente'] = $this->nombreCliente;
		$_POST['form_direccion'] = $this->direccion;
		$_POST['form_nombreCondicionIva'] = $this->nombreCondicionIva;
		$_POST['form_cuit'] = $this->cuit;
		$_POST['form_condicionDeVenta'] = $this->condicionDeVenta;
		$_POST['form_subtotal'] = $this->subtotal;
		$_POST['form_subtotal2'] = $this->subtotal2;
		$_POST['form_descuentos'] = $this->descuentos;
		$_POST['form_ivaPorc1'] = $this->ivaPorc1;
		$_POST['form_ivaImporte1'] = $this->ivaImporte1;
		$_POST['form_ivaPorc2'] = $this->ivaPorc2;
		$_POST['form_ivaImporte2'] = $this->ivaImporte2;
		$_POST['form_ivaPorc3'] = $this->ivaPorc3;
		$_POST['form_ivaImporte3'] = $this->ivaImporte3;
		$_POST['form_importeLetras'] = NumeroALetras::numero2Letras($this->importeTotal);
		$_POST['form_barcode'] = $this->getBarcode();
		$_POST['form_importeTotal'] = $this->importeTotal;
		$_POST['form_cae'] = $this->cae;
		$_POST['form_caeVencimiento'] = $this->caeVencimiento;
		$_POST['form_observaciones'] = $this->observaciones;
		$_POST['form_detalle'] = $this->detalle;
	}

	public static function calcularDigitoVerificador($string) {
		$suma = 0;
		for ($i = 0; $i < count($string); $i++)
			$suma += Funciones::toInt($string[$i]) * ($i % 2 ? 1 : 3);
		return (($suma % 10) ? (10 - ($suma % 10)) : 0);
	}

	//GETS y SETS
	protected function getBarcode() {
		if (!isset($this->_barcode)){
			$this->_barcode = Config::CUIT_SPIRAL;
			$this->_barcode .= CodigosComprobante::getCodigoComprobante($this->tipoDocumento, $this->letra);
			$this->_barcode .= '0001';
			$this->_barcode .= $this->cae;
			$this->_barcode .= Funciones::formatearFecha($this->caeVencimiento, 'Ymd');
			$this->_barcode .= self::calcularDigitoVerificador($this->_barcode); 
		}
		return $this->_barcode;
	}
	protected function setBarcode($barcode) {
		$this->_barcode = $barcode;
		return $this;
	}
}

?>