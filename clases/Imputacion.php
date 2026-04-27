<?php

/**
 * @property Usuario		$usuario
 */

class Imputacion extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idNuevo;
	public		$nombre;
	public		$imputable;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$anulado;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function getWhereSql($nombreCampo) {
		$where = '';
		if ($this->esImputable()) {
			$where .= $nombreCampo . ' = ' . Datos::objectToDB($this->id);
		} else {
			$where .= $nombreCampo . ' IN (';
			foreach ($this->getImputacionesHijasImputables() as $imputacionHija) {
				/** @var Imputacion $imputacionHija */
				$where .= $imputacionHija->id . ', ';
			}
			$where = trim($where, ', ');
			$where .= ')';
		}

		return $where;
	}

	public function getImputacionesHijasImputables() {
		if ($this->esImputable()) {
			throw new FactoryExceptionCustomException('No se puede obtener las subcuentas de cuna cuenta imputable');
		}
		$raiz = Funciones::toString(rtrim($this->id, '0'));
		$where = 'imputable = ' . Datos::objectToDB('S') . ' AND SUBSTRING(CAST(cuenta AS VARCHAR), 1, ' . Datos::objectToDB(strlen($raiz)) . ') = ' . Datos::objectToDB($raiz);
		$imputacionesHija = Factory::getInstance()->getListObject('Imputacion', $where);

		if (count($imputacionesHija) == 0) {
			throw new FactoryExceptionCustomException('La imputación seleccionada no posee sub-cuentas asociadas');
		}

		return $imputacionesHija;
	}

	public function esImputable() {
		return $this->imputable == 'S';
	}

	//GETS y SETS
	protected function getUsuario() {
		if (!isset($this->_usuario)){
			$this->_usuario = Factory::getInstance()->getUsuario($this->idUsuario);
		}
		return $this->_usuario;
	}
	protected function setUsuario($usuario) {
		$this->_usuario = $usuario;
		return $this;
	}
}

?>