<?php

/**
 * @property Usuario						$usuario
 * @property Usuario						$usuarioBaja
 * @property Usuario						$usuarioUltimaMod
 */

class TipoPeriodoFiscal extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$nombre;
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

	const		TIPO_EJERCICIO_CONTABLE = 1;
	const		TIPO_IVA = 2;

	//GETS y SETS
}

?>