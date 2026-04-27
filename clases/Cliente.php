<?php

/**
 * @property Autorizaciones 	$autorizaciones
 * @property CondicionIva	 	$condicionIva
 * @property Contacto[]			$contactos
 * @property FormaDePago		$creditoFormaDePago
 * @property string				$direccion
 * @property Localidad			$direccionLocalidad
 * @property Pais				$direccionPais
 * @property Provincia			$direccionProvincia
 * @property GrupoEmpresa		$grupoEmpresa
 * @property Vendedor			$vendedor
 * @property Rubro				$rubro
 * @property Sucursal[]			$sucursales
 * @property Sucursal	 		$casaCentral
 * @property Sucursal			$sucursalCentral
 * @property Sucursal			$sucursalFiscal
 * @property Sucursal	 		$sucursalCobranza
 * @property Sucursal	 		$sucursalEntrega
 * @property Int		 		$totalAPredespachar
 */
class Cliente extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$anulado;
	public		$autorizado;
	protected	$_autorizaciones;
	public		$calificacion; //Es el estado del cliente. 04 es incobrable
	public		$calificacionOriginal; //Para saber si hubo cambio
	public		$idCasaCentral;
	protected	$_casaCentral;
	public		$cobranzaEmail1;
	public		$cobranzaEmail2;
	public		$cobranzaTelefono1;
	public		$cobranzaTelefono2;
	public		$cobranzaTelefono3;
	public		$idCondicionIva;
	protected	$_condicionIva;
	protected	$_contactos;
	public		$creditoDescuentoEspecial;
	public		$idCreditoFormaDePago;
	protected	$_creditoFormaDePago;
	public		$creditoLimite;
	public		$creditoPlazoMaximo;
	public		$creditoPrimeraEntrega;
	public		$cuit;
	public		$dni;
	protected	$_direccion;
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
	public		$fechaUltimaCalificacion;
	public		$idGrupoEmpresa;
	protected	$_grupoEmpresa;
	public		$listaAplicable;
	public		$nombre;
	public		$marcasQueComercializa;
	public		$observaciones;
	public		$observacionesCobranza;
	public		$observacionesGestionCobranza;	//Para el reporte de gestión cobranzas
	public		$observacionesVendedor;			//Para el reporte de gestión cobranzas
	public		$razonSocial;
	public		$referenciasBancarias;
	public		$referenciasComerciales;
	public		$idVendedor;
	protected	$_vendedor;
	public		$idRubro;
	protected	$_rubro;
	protected	$_sucursales;
	public		$idSucursalCentral;
	protected	$_sucursalCentral;
	public		$idSucursalFiscal;
	protected	$_sucursalFiscal;
	public		$idSucursalCobranza;
	protected	$_sucursalCobranza;
	public		$idSucursalEntrega;
	protected	$_sucursalEntrega;
	public		$telefono1;
	public		$interno1;
	public		$habilitadoCae;				//Para no cometer errores con aquellos clientes que son exclusivamente de cuenta 2. Rebota al intentar obtenerles CAE
	protected	$_totalAPredespachar;

	public function guardar() {
		$original = false; //Hago esto del $original por el StoreUpdater
		if (isset($this->id)) {
			$original = Factory::getInstance()->getClienteTodos($this->id);
		}

		parent::guardar();

		if ($this->calificacion != $this->calificacionOriginal) {
			$cambiosSituacionCliente = Factory::getInstance()->getCambiosSituacionCliente();
			$cambiosSituacionCliente->calificacionNueva = $this->calificacion;
			$cambiosSituacionCliente->calificacionAnterior = ($this->calificacionOriginal == '00' ? null : $this->calificacionOriginal);
			$cambiosSituacionCliente->cliente = $this;
			$cambiosSituacionCliente->guardar();
		}

		if ($original && isset($this->_sucursales)) {
			foreach ($this->sucursales as $suc) {
				$suc->cliente = $original;
				if (!$suc->anulado()) {
					$suc->guardar();
				}
			}
		}

		return $this;
	}

	public function comprobarHabilitadoDespachar() {
		if (Funciones::toInt($this->calificacion) > 2) {
			throw new FactoryExceptionCustomException('El cliente ' . $this->getIdNombre() . ' no cumple los requisitos de calificación para despachar');
		}
	}

	public function comprobarHabilitadoRemitir() {
		if (Funciones::toInt($this->calificacion) > 2) {
			throw new FactoryExceptionCustomException('El cliente ' . $this->getIdNombre() . ' no cumple los requisitos de calificación para remitir');
		}
	}

	public function comprobarHabilitadoFacturar() {
		if (Funciones::toInt($this->calificacion) > 2) {
			throw new FactoryExceptionCustomException('El cliente ' . $this->getIdNombre() . ' no cumple los requisitos de calificación para facturar');
		}
	}

	public function suVendedorEs(Vendedor $vendedor) {
		return ($this->vendedor->id == $vendedor->id);
	}

	public function getIdNombre() {
		return parent::getIdNombre('razonSocial');
	}

	public function tieneSucursalEntrega() {
		return isset($this->idSucursalEntrega);
	}

    public function direccionFacturacion() {
        $sf = $this->sucursalFiscal;
        $dir = $sf->direccionCalle . ' ' . $sf->direccionNumero . ' ';
        if ($sf->direccionPiso) $dir .= 'Piso ' . $sf->direccionPiso . ' ';
        if ($sf->direccionDepartamento) $dir .= 'Dpto ' . $sf->direccionDepartamento . ' ';
        $dir .= '</br>' . $sf->direccionCodigoPostal . ' ' . $sf->direccionLocalidad->nombre . ' - ';
        $dir .= '</br>' . $sf->direccionProvincia->nombre . ' - ' . $sf->direccionPais->nombre;
        return $dir;
    }

	//GETS y SETS
	protected function getAutorizaciones() {
		if (!isset($this->_autorizaciones) && isset($this->id)){
			$this->_autorizaciones = new Autorizaciones(TiposAutorizacion::altaCliente, $this->id);
		}
		return $this->_autorizaciones;
	}
	protected function setAutorizaciones($autorizaciones) {
		$this->_autorizaciones = $autorizaciones;
		return $this;
	}
	protected function getCasaCentral() {
		if (!isset($this->_casaCentral)){
			$this->_casaCentral = Factory::getInstance()->getSucursal($this->id, $this->idCasaCentral);
		}
		return $this->_casaCentral;
	}
	protected function setCasaCentral($casaCentral) {
		$this->_casaCentral = $casaCentral;
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
			$this->_contactos = Factory::getInstance()->getListObject('Contacto', 'cod_cliente = ' . Datos::objectToDB($this->id));
		}
		return $this->_contactos;
	}
	protected function getCreditoFormaDePago() {
		if (!isset($this->_creditoFormaDePago)){
			$this->_creditoFormaDePago = Factory::getInstance()->getFormaDePago($this->idCreditoFormaDePago);
		}
		return $this->_creditoFormaDePago;
	}
	protected function setCreditoFormaDePago($creditoFormaDePago) {
		$this->_creditoFormaDePago = $creditoFormaDePago;
		return $this;
	}
	protected function getDireccion() {
		if (!isset($this->_direccion)){
			$dir = $this->direccionCalle . ' ';
			$dir .= $this->direccionNumero . ' ';
			$dir .= ($this->direccionPiso ? $this->direccionPiso . 'º ' : '');
			$dir .= ($this->direccionDepartamento ? $this->direccionDepartamento . ' - ' : '- ');
			$dir .= $this->direccionLocalidad->nombre . ', ';
			$dir .= $this->direccionProvincia->nombre . ', ';
			$dir .= $this->direccionPais->nombre;
			$this->_direccion = $dir;
		}
		return $this->_direccion;
	}
	protected function setDireccion($direccion) {
		$this->_direccion = $direccion;
		return $this;
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
	protected function getGrupoEmpresa() {
		if (!isset($this->_grupoEmpresa)){
			$this->_grupoEmpresa = Factory::getInstance()->getGrupoEmpresa($this->idGrupoEmpresa);
		}
		return $this->_grupoEmpresa;
	}
	protected function setGrupoEmpresa($grupoEmpresa) {
		$this->_grupoEmpresa = $grupoEmpresa;
		return $this;
	}
	protected function getRubro() {
		if (!isset($this->_rubro)){
			$this->_rubro = Factory::getInstance()->getRubro($this->idRubro);
		}
		return $this->_rubro;
	}
	protected function setRubro($rubro) {
		$this->_rubro = $rubro;
		return $this;
	}
	protected function getSucursales() {
		if (!isset($this->_sucursales) && isset($this->id)){
			$this->_sucursales = Factory::getInstance()->getListObject('Sucursal', 'cod_cli = ' . Datos::objectToDB($this->id) . ' AND anulado = \'N\'');
		}
		return $this->_sucursales;
	}
	protected function setSucursales($sucursales) {
		$this->_sucursales = $sucursales;
		return $this;
	}
	protected function getSucursalCentral() {
		if (!isset($this->_sucursalCentral)){
			$this->_sucursalCentral = Factory::getInstance()->getSucursal($this->id, $this->idSucursalCentral);
		}
		return $this->_sucursalCentral;
	}
	protected function setSucursalCentral($sucursalCentral) {
		$this->_sucursalCentral = $sucursalCentral;
		return $this;
	}
	protected function getSucursalCobranza() {
		if (!isset($this->_sucursalCobranza)){
			$this->_sucursalCobranza = Factory::getInstance()->getSucursal($this->id, $this->idSucursalCobranza);
		}
		return $this->_sucursalCobranza;
	}
	protected function setSucursalCobranza($sucursalCobranza) {
		$this->_sucursalCobranza = $sucursalCobranza;
		return $this;
	}
	protected function getSucursalFiscal() {
		if (!isset($this->_sucursalFiscal)){
			$this->_sucursalFiscal = Factory::getInstance()->getSucursal($this->id, $this->idSucursalFiscal);
		}
		return $this->_sucursalFiscal;
	}
	protected function setSucursalFiscal($sucursalFiscal) {
		$this->_sucursalFiscal = $sucursalFiscal;
		return $this;
	}
	protected function getSucursalEntrega() {
		if (!isset($this->_sucursalEntrega)){
			$this->_sucursalEntrega = Factory::getInstance()->getSucursal($this->id, $this->idSucursalEntrega);
		}
		return $this->_sucursalEntrega;
	}
	protected function setSucursalEntrega($sucursalEntrega) {
		$this->_sucursalEntrega = $sucursalEntrega;
		return $this;
	}
	protected function getTotalAPredespachar() {
		if (!isset($this->_totalAPredespachar)){
			$where = 'predespachados > 0  AND ';
			$where .= 'cod_cliente = ' . Datos::objectToDB($this->id);
			$predespachos = Factory::getInstance()->getListObject('Predespacho', $where);

			$this->_totalAPredespachar = 0;
			foreach ($predespachos as $item) {
				/** @var Predespacho $item */
				$this->_totalAPredespachar += $item->getTotalPredespachados();
			}
		}
		return $this->_totalAPredespachar;
	}
	protected function getVendedor() {
		if (!isset($this->_vendedor)){
			$this->_vendedor = Factory::getInstance()->getVendedor($this->idVendedor);
		}
		return $this->_vendedor;
	}
	protected function setVendedor($vendedor) {
		$this->_vendedor = $vendedor;
		return $this;
	}
}

?>