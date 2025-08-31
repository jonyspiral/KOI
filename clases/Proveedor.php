<?php

/**
 * @property Autorizaciones 	$autorizaciones
 * @property CondicionIva		$condicionIva
 * @property Contacto[]			$contactos
 * @property Imputacion			$imputacionGeneral
 * @property Imputacion			$imputacionEspecifica
 * @property Imputacion			$imputacionHaber
 * @property Localidad			$direccionLocalidad
 * @property Pais				$direccionPais
 * @property Provincia			$direccionProvincia
 * @property RetencionTabla		$retencionTabla
 * @property TipoProveedor		$tipoProveedor
 * @property Transporte			$transporte
 */

class Proveedor extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$anulado;
	public		$autorizado;
	protected	$_autorizaciones;
	public		$conceptoRetenGanancias;
	public		$idCondicionIva;
	protected	$_condicionIva;
	protected	$_contactos;
	public		$cuentaAcumuladora;
	public		$cuit;
	public		$nombre;
	public		$denominacionCuentaAcumuladora;
	public		$direccionCalle;
	public		$direccionCodigoPostal;
	public		$direccionDepartamento;
	public		$idDireccionLocalidad;
	protected	$_direccionLocalidad;
	public		$direccionNumero;
	public		$idDireccionPais;
	protected	$_direccionPais;
	public		$direccionPartidoDepartamento;
	public		$direccionPiso;
	public		$idDireccionProvincia;
	protected	$_direccionProvincia;
	public		$email;
	public		$fax;
	public		$horariosAtencion;
	public		$importeAcumuladoMes;
	public		$importeRetenidoMes;
	public		$imputacionEnCompra;
	public		$idImputacionGeneral;
	protected	$_imputacionGeneral;
	public		$idImputacionEspecifica;
	protected	$_imputacionEspecifica;
	public		$idImputacionHaber;
	protected	$_imputacionHaber;
	public		$jurisdiccion1IngresosBrutos;
	public		$jurisdiccion2IngresosBrutos;
	public		$limiteCredito;
	public		$observaciones;
	public		$observacionesGestion;
	public		$paginaWeb;
	public		$plazoPago;
	public		$plazoPagoPrimeraEntrega;
	public		$razonSocial;
	public		$retencionEspecial;
	protected	$_retencionTabla;
	public		$retenerImpuestoGanancias;
	public		$retenerIngresosBrutos;
	public		$retenerIva;
	public		$rubroPalabra;
	public		$telefono1;
	public		$telefono2;
	public		$idTipoProveedor;
	protected	$_tipoProveedor;
	public		$idTransporte;
	protected	$_transporte;
	public		$vencimiento;
	public		$saldo;
	public		$saldo1;
	public		$saldo2;

	public function calcularRetencion(&$importe, $ayuda = false, $importeAcumuladoRestar = 0) {
		$iva = Funciones::toFloat($this->condicionIva->porcentajes[1]);
		$coefIva = ($iva / 100);
		$acumulado = $this->importeAcumuladoMes - $importeAcumuladoRestar;
		$baseImponible = $this->retencionTabla->baseImponible;
		$escala = $this->retencionTabla->escalaDirecto == 'E';
		$porcRetener = Funciones::toFloat(($this->condicionIva->letraFactura == 'A') ? $this->retencionTabla->inscriptoAlicuota : $this->retencionTabla->noInscriptoAlicuota);
		$minimo = Funciones::toFloat($this->retencionTabla->noCorrespondeMenor);
		$coefRetencion = ($porcRetener / 100);
		$resto = ($acumulado >= $baseImponible) ? 0 : ($baseImponible - $acumulado);

		$neto = $importe;
		$retencion = 0;
		$bruto = $neto;

		$noRetener = false;

		if ($escala) {
			$where = 'ano = ' . Datos::objectToDB(Funciones::hoy('Y')) . ' AND ';
			$where .= 'mes = ' . Datos::objectToDB(Funciones::toInt(Funciones::hoy('m'))) . ' ';
			$order = 'ORDER BY tramo_escala ASC; ';
			$escalas = Factory::getInstance()->getListObject('RetencionEscala', $where . $order);
		}
		$iteraciones = $ayuda ? 1 : 10;
		for ($i = 0; $i < $iteraciones; $i++) {
			$sinImpuestos = $bruto / (1 + $coefIva);
			$sinBase = $sinImpuestos - $resto;
			if ($sinImpuestos < $resto) {
				$noRetener = true;
				break;
			}
			if ($escala) {
				foreach ($escalas as $e) {
					/** @var RetencionEscala $e */
					if ($sinBase < $e->final) {
						$retencion = $e->fijo + ($sinBase - $e->sobreExcedente) * ($e->masPorcentaje / 100);
						break;
					}
				}
			} else {
				$retencion = $sinBase * $coefRetencion;
			}
			(!$ayuda) && $bruto = ($neto + $retencion);
		}
		if (($noRetener || ($retencion < $minimo))) {
			$retencion = 0;
		} else {
			$importe = $importe / (1 + $coefIva);
		}

		return $retencion;
	}

	public function getIdNombre() {
		return parent::getIdNombre('razonSocial');
	}

	//GETS y SETS
	protected function getAutorizaciones() {
		if (!isset($this->_autorizaciones) && isset($this->id)){
			$this->_autorizaciones = new Autorizaciones(TiposAutorizacion::altaProveedor, $this->id);
		}
		return $this->_autorizaciones;
	}
	protected function setAutorizaciones($autorizaciones) {
		$this->_autorizaciones = $autorizaciones;
		return $this;
	}
	protected function getCondicionIva() {
		if (!isset($this->_condicionIva)){
			$this->_condicionIva = Factory::getInstance()->getCondicionIva($this->idCondicionIva);
		}
		return $this->_condicionIva;
	}
	protected function setCondicionIva($condicionIva) {
		$this->_condicionIva = $condicionIva;
		return $this;
	}
	protected function getContactos() {
		if (!isset($this->_contactos) && isset($this->id)){
			$this->_contactos = Factory::getInstance()->getListObject('Contacto', 'cod_proveedor = ' . Datos::objectToDB($this->id));
		}
		return $this->_contactos;
	}
	protected function getDireccionPais() {
		if (!isset($this->_direccionPais)){
			$this->_direccionPais = Factory::getInstance()->getPais($this->idDireccionPais);
		}
		return $this->_direccionPais;
	}
	protected function setDireccionPais($pais) {
		$this->_direccionPais = $pais;
		return $this;
	}
	protected function getDireccionLocalidad() {
		if (!isset($this->_direccionLocalidad)){
			$this->_direccionLocalidad = Factory::getInstance()->getLocalidad($this->idDireccionPais, $this->idDireccionProvincia, $this->idDireccionLocalidad);
		}
		return $this->_direccionLocalidad;
	}
	protected function setDireccionLocalidad($localidad) {
		$this->_direccionLocalidad = $localidad;
		return $this;
	}
	protected function getDireccionProvincia() {
		if (!isset($this->_direccionProvincia)){
			$this->_direccionProvincia = Factory::getInstance()->getProvincia($this->idDireccionPais, $this->idDireccionProvincia);
		}
		return $this->_direccionProvincia;
	}
	protected function setDireccionProvincia($provincia) {
		$this->_direccionProvincia = $provincia;
		return $this;
	}
	protected function getImputacionEspecifica() {
		if (!isset($this->_imputacionEspecifica)){
			$this->_imputacionEspecifica = Factory::getInstance()->getImputacion($this->idImputacionEspecifica);
		}
		return $this->_imputacionEspecifica;
	}
	protected function setImputacionEspecifica($imputacionEspecifica) {
		$this->_imputacionEspecifica = $imputacionEspecifica;
		return $this;
	}
	protected function getImputacionGeneral() {
		if (!isset($this->_imputacionGeneral)){
			$this->_imputacionGeneral = Factory::getInstance()->getImputacion($this->idImputacionGeneral);
		}
		return $this->_imputacionGeneral;
	}
	protected function setImputacionGeneral($imputacionGeneral) {
		$this->_imputacionGeneral = $imputacionGeneral;
		return $this;
	}
	protected function getImputacionHaber() {
		if (!isset($this->_imputacionHaber)){
			$this->_imputacionHaber = Factory::getInstance()->getImputacion($this->idImputacionHaber);
		}
		return $this->_imputacionHaber;
	}
	protected function setImputacionHaber($imputacionHaber) {
		$this->_imputacionHaber = $imputacionHaber;
		return $this;
	}
	protected function getRetencionTabla() {
		if (!isset($this->_retencionTabla)){
			$this->_retencionTabla = Factory::getInstance()->getRetencionTabla(Funciones::hoy('Y'), Funciones::hoy('n'), $this->conceptoRetenGanancias);
		}
		return $this->_retencionTabla;
	}
	protected function setRetencionTabla($retencionTabla) {
		$this->_retencionTabla = $retencionTabla;
		return $this;
	}
	protected function getTipoProveedor() {
		if (!isset($this->_tipoProveedor)){
			$this->_tipoProveedor = Factory::getInstance()->getTipoProveedor($this->idTipoProveedor);
		}
		return $this->_tipoProveedor;
	}
	protected function setTipoProveedor($tipoProveedor) {
		$this->_tipoProveedor = $tipoProveedor;
		return $this;
	}
	protected function getTransporte() {
		if (!isset($this->_transporte)){
			$this->_transporte = Factory::getInstance()->getTransporte($this->idTransporte);
		}
		return $this->_transporte;
	}
	protected function setTransporte($transporte) {
		$this->_transporte = $transporte;
		return $this;
	}
}

?>