<?php

/**
 * @property Ecommerce_Usergroup	$usergroup
 * @property Usuario				$usuario
 * @property Usuario				$usuarioBaja
 * @property Usuario				$usuarioUltimaMod
 */
class Ecommerce_Customer extends Base {
	const	_primaryKey = '["id"]';

	public		$id;
	public		$idEcommerce;
	public		$idUsergroup;
	protected	$_usergroup;
	public		$email;
	public		$title;
	public		$firstname;
	public		$lastname;
	public		$birthday;
	public		$newsletters;
	public		$offers;
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

	public static function getFromIdEcommerce($idEcommerce) {
		$customers = Factory::getInstance()->getListObject('Ecommerce_Customer', 'cod_customer_ecommerce = ' . Datos::objectToDB($idEcommerce));
		if (count($customers)) {
			return $customers[0];
		}
		return Factory::getInstance()->getEcommerce_Customer();
	}

	public function fullname($lastnameFirst = false) {
		return ($lastnameFirst ? $this->lastname : $this->firstname) . ' ' . ($lastnameFirst ? $this->firstname : $this->lastname);
	}

	//GETS y SETS
	protected function getUsergroup() {
		if (!isset($this->_usergroup)){
			$this->_usergroup = Factory::getInstance()->getEcommerce_Usergroup($this->idUsergroup);
		}
		return $this->_usergroup;
	}
	protected function setUsergroup($usergroup) {
		$this->_usergroup = $usergroup;
		return $this;
	}
}

?>