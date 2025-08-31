<?php

/**
 * @property Usuario					$usuario
 * @property Usuario					$usuarioBaja
 * @property Usuario					$usuarioUltimaMod
 */
class Ecommerce_ServicioAndreani extends Base {
	const		_primaryKey = '["id"]';

	const		ENVIO_URGENTE = 1;
	const		CAMBIO = 2;
	const		RETIRO_EN_CLIENTE = 3;

	public		$id;
	public		$nombre;
	public		$numeroDeContrato;
	public		$anulado;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	//GETS y SETS

}

?>