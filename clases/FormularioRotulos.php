<?php

class FormularioRotulos extends Formulario {
	//Los que están comentados es porque están definidos en la clase padre, Formulario

	public	$razonSocial;

	public	$senores;
	public	$clienteNro;
	public	$sucursalNro;
	public	$direccionEntregaCalle;
	public	$direccionEntregaNumero;
	public	$direccionEntregaProvincia;
	public	$direccionEntregaPiso;
	public	$direccionEntregaDpto;
	public	$direccionEntregaLocalidad;
	public	$direccionEntregaCP;
	public	$transportistaNombre;
	public	$transportistaDomicilio;
	public	$transportistaCUIT;
	public	$transportistaDNI;
	public	$horarioEntrega1;
	public	$horarioEntrega2;

	public function abrir(){
		$this->crearPdf();
		$this->pdf->open();
	}

	protected function enviarDatos() {
		$_POST['form_razonSocial'] = $this->razonSocial;
		$_POST['form_clienteNro'] = $this->clienteNro;
		$_POST['form_sucursalNro'] = $this->sucursalNro;
		$_POST['form_direccionEntregaCalle'] = $this->direccionEntregaCalle;
		$_POST['form_direccionEntregaNumero'] = $this->direccionEntregaNumero;
		$_POST['form_direccionEntregaProvincia'] = $this->direccionEntregaProvincia;
		$_POST['form_direccionEntregaPiso'] = $this->direccionEntregaPiso;
		$_POST['form_direccionEntregaDpto'] = $this->direccionEntregaDpto;
		$_POST['form_direccionEntregaLocalidad'] = $this->direccionEntregaLocalidad;
		$_POST['form_direccionEntregaCP'] = $this->direccionEntregaCP;
		$_POST['form_transportistaNombre'] = $this->transportistaNombre;
		$_POST['form_transportistaDomicilio'] = $this->transportistaDomicilio;
		$_POST['form_transportistaCUIT'] = $this->transportistaCUIT;
		$_POST['form_transportistaDNI'] = $this->transportistaDNI;
		$_POST['form_horarioEntrega1'] = $this->horarioEntrega1;
		$_POST['form_horarioEntrega2'] = $this->horarioEntrega2;
	}

	protected function crearPdf(){
		if (!isset($this->pdf)) {
			$this->pdf = new Html2Pdf();
			$this->enviarDatos();

			$this->pdf->html = Html2Pdf::getHtmlFromPhp(Config::pathBase . 'includes/modelosFormularios/modeloRotulos.php');
			$this->pdf->llevaHeader = false;
			$this->pdf->llevaFooter = false;
			$this->pdf->fileName = 'Formulario_Rotulo_' . $this->clienteNro . '_' . $this->sucursalNro;
			$this->pdf->marginTop = '1';
			$this->pdf->marginBottom = '1';
			$this->pdf->marginLeft = '1';
			$this->pdf->marginRight = '1';
		}
	}

	//GETS y SETS
}

?>