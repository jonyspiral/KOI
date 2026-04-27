<?php

/**
 * @property Cliente 		$cliente
 * @property Contacto		$contacto
 * @property Localidad		$direccionLocalidad
 * @property Pais			$direccionPais
 * @property Provincia		$direccionProvincia
 * @property Sucursal		$sucursalEntrega
 * @property Transporte		$transporte
 * @property Vendedor		$vendedor
 * @property ZonaTransporte	$zonaTransporte
 */

class Sucursal extends Base {
	const		_primaryKey = '["idCliente", "id"]';

	public		$id;
	public		$idCliente;
	protected	$_cliente;
	public		$activo;
	public		$anulado;
	public		$celular;
	public		$idContacto;
	protected	$_contacto;
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
	public		$esCasaCentral;
	public		$esPuntoDeVenta;
	public		$fax;
	public		$horarioAtencion;
	public		$nombre;
	public		$observaciones;
	public		$reparto;
	public		$idSucursalEntrega;
	protected	$_sucursalEntrega;
	public		$telefono1;
	public		$telefono2;
	public		$idTransporte;
	protected	$_transporte;
	public		$idVendedor;
	protected	$_vendedor;
	public		$idZonaTransporte;
	protected	$_zonaTransporte;
	public		$formulario;
	public		$direccionLatitud;
	public		$direccionLongitud;
	public		$direccionFormateada;
	public		$horarioEntrega1;
	public		$horarioEntrega2;

	//formulario predespacho
	public function abrirPredespachados() {
		$this->crearFormulario();
		$this->llenarFormulario();
		$this->formulario->abrir();
	}

	public function crear() {
		$this->crearFormulario();
		$this->llenarFormulario();
		return $this->formulario->crear();
	}

	protected function crearFormulario() {
		$this->formulario = new FormularioPredespacho();
	}

	protected function llenarFormulario() {
		$where = 'predespachados > 0  AND ';
		$where .= 'cod_cliente = ' . Datos::objectToDB($this->idCliente) . ' AND ';
		$where .= 'cod_sucursal = ' . Datos::objectToDB($this->id);
		$orderBy = ' ORDER BY cod_articulo ASC, cod_color_articulo ASC';

		$predespachos = Factory::getInstance()->getListObject('Predespacho', $where . $orderBy);

		$this->formulario->detalle = $predespachos;
		$this->formulario->esPedido = false;
		$this->formulario->idCliente = $this->idCliente;
		$this->formulario->idSucursal = $this->id;
	}

	public function guardar() {
		$original = false; //Hago esto del $original por el StoreUpdater
		if (isset($this->id)) {
			$original = Factory::getInstance()->getSucursal($this->idCliente, $this->id);
		}

		parent::guardar();

		//No hago tanto foco en cada caso (si antes era punto de venta y ahora no y blabla porque el script del server se encargar de redirigir el request hacia donde corresponde
		if ($original === false) {
			if ($this->esPuntoDeVenta == 'S') {
				StoreLocatorUpdater::create_sucursal($this);
			}
		} else {
			if ($this->esPuntoDeVenta == 'N') {
				StoreLocatorUpdater::delete_sucursal($this);
			} else {
				StoreLocatorUpdater::update_sucursal($this, $original);
			}
		}

		return $this;
	}

	public function borrar() {
		parent::borrar();

		StoreLocatorUpdater::delete_sucursal($this);

		return $this;
	}

	protected function validarGuardar() {
		if ($this->esPuntoDeVenta == 'S' && (!isset($this->direccionLatitud) || !isset($this->direccionLongitud))) {
			throw new FactoryExceptionCustomException('Si la sucursal es punto de venta deberá completar los campos "latitud" y "longitud" para el Store Locator');
		}
	}

	//GETS y SETS
	protected function getCliente() {
		if (!isset($this->_cliente)){
			$this->_cliente = Factory::getInstance()->getClienteTodos($this->idCliente);
		}
		return $this->_cliente;
	}
	protected function setCliente($cliente) {
		$this->_cliente = $cliente;
		return $this;
	}
	protected function getContacto() {
		if (!isset($this->_contacto)){
			$this->_contacto = Factory::getInstance()->getContacto($this->idContacto);
		}
		return $this->_contacto;
	}
	protected function setContacto($contacto) {
		$this->_contacto = $contacto;
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
	protected function getSucursalEntrega() {
		if (!isset($this->_sucursalEntrega)){
			if (!$this->idSucursalEntrega) {
				$this->idSucursalEntrega = $this->id;
			}
			$this->_sucursalEntrega = Factory::getInstance()->getSucursal($this->idCliente, $this->idSucursalEntrega);
		}
		return $this->_sucursalEntrega;
	}
	protected function setSucursalEntrega($sucursalEntrega) {
		$this->_sucursalEntrega = $sucursalEntrega;
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
	protected function getZonaTransporte() {
		if (!isset($this->_zonaTransporte)){
			$this->_zonaTransporte = Factory::getInstance()->getZonaTransporte($this->idZonaTransporte);
		}
		return $this->_zonaTransporte;
	}
	protected function setZonaTransporte($zonaTransporte) {
		$this->_zonaTransporte = $zonaTransporte;
		return $this;
	}
}

?>