<?php

/**
 * @property Cliente		$cliente
 * @property Proveedor		$proveedor
 * @property AreaEmpresa	$areaEmpresa
 * @property Localidad		$direccionLocalidad
 * @property Pais			$direccionPais
 * @property Provincia		$direccionProvincia
 * @property string			$nombreApellido
 * @property Sucursal		$sucursal
 * @property Usuario[]		$usuarios
 */

class Contacto extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$tipo;					//"C"liente, Proveedor ("R") u Otro ("X")
	public		$idCliente;
	protected	$_cliente;
	public		$idProveedor;
	protected	$_proveedor;
	public		$anulado;
	public		$idAreaEmpresa;
	protected	$_areaEmpresa;
	public		$apellido;
	public		$celular;
	public		$direccionCalle;
	public		$direccionCodigoPostal;
	public		$direccionDepartamento;
	public		$idDireccionLocalidad;
	protected	$_direccionLocalidad;
	public		$direccionNumero;
	public		$idDireccionPais;
	protected	$_direccionPais;
	public		$direccionPiso;
	public		$idDireccionProvincia;
	protected	$_direccionProvincia;
	public		$email1;
	public		$email2;
	public		$interno1;
	public		$interno2;
	public		$nombre;
	protected	$_nombreApellido;
	public		$observaciones;
	public		$referencia;
	public		$idSucursal;
	protected	$_sucursal;
	public		$telefono1;
	public		$telefono2;
	protected	$_usuarios;

	public function __construct() {
		parent::__construct();
		$this->tipo = TiposContacto::otro;
	}

	public function getLink() {
		return '/abm/contactos/?buscar=' . $this->id;
	}

	public function esCliente() {
		return $this->tipo == 'C';
	}

	//GETS y SETS
	protected function getAreaEmpresa() {
		if (!isset($this->_areaEmpresa)){
			$this->_areaEmpresa = Factory::getInstance()->getAreaEmpresa($this->idAreaEmpresa);
		}
		return $this->_areaEmpresa;
	}
	protected function setAreaEmpresa($areaEmpresa) {
		$this->_areaEmpresa = $areaEmpresa;
		return $this;
	}
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
	protected function getNombreApellido() {
		if (!isset($this->_nombreApellido)){
			$this->_nombreApellido = $this->nombre . ' ' . $this->apellido;
		}
		return $this->_nombreApellido;
	}
	protected function setNombreApellido($nombreApellido) {
		$this->_nombreApellido = $nombreApellido;
		return $this;
	}
	protected function getProveedor() {
		if (!isset($this->_proveedor)){
			$this->_proveedor = Factory::getInstance()->getProveedorTodos($this->idProveedor);
		}
		return $this->_proveedor;
	}
	protected function setProveedor($proveedor) {
		$this->_proveedor = $proveedor;
		return $this;
	}
	protected function getSucursal() {
		if (!isset($this->_sucursal)){
			$this->_sucursal = Factory::getInstance()->getSucursal($this->idCliente, $this->idSucursal);
		}
		return $this->_sucursal;
	}
	protected function setSucursal($sucursal) {
		$this->_sucursal = $sucursal;
		return $this;
	}
	protected function getUsuarios() {
		if (!isset($this->_usuarios)){
			$this->_usuarios = Factory::getInstance()->getListObject('Usuario', 'cod_contacto = ' . Datos::objectToDB($this->id));
		}
		return $this->_usuarios;
	}
	protected function setUsuarios($usuarios) {
		$this->_usuarios = $usuarios;
		return $this;
	}
}

?>