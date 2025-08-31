<?php

class UsuarioLogin extends Usuario {
	const		DEFAULT_SESSION_USERNAME = 'usuarioLogueadoUser';
	const		DEFAULT_SESSION_PASSWORD = 'usuarioLogueadoPass';
	const		DEFAULT_POST_USERNAME = 'user';
	const		DEFAULT_POST_PASSWORD = 'pass';
	const		DEFAULT_POST_EMPRESA = 'empresa';
	public		$password;

	private static function loginPorHacer() {
		return isset($_POST[self::DEFAULT_POST_USERNAME]) && isset($_POST[self::DEFAULT_POST_PASSWORD]);
	}

	private static function yaLogueado() {
		return isset($_SESSION[self::DEFAULT_SESSION_USERNAME]) && isset($_SESSION[self::DEFAULT_SESSION_PASSWORD]);
	}

	private static function logout() {
		self::$_usuarioLogueado = null;
		unset($_SESSION[self::DEFAULT_SESSION_USERNAME]);
		unset($_SESSION[self::DEFAULT_SESSION_PASSWORD]);
	}

	public static function login($username = null, $password = null) {
		if (is_null($username) || is_null($password)) {
			if (!self::yaLogueado()) {
				if (!self::loginPorHacer()) {
					//Sale por el curso del login, es decir, el master
					return;
				}
				$username = $_POST[self::DEFAULT_POST_USERNAME];
				$password = Funciones::toSHA1($_POST[self::DEFAULT_POST_PASSWORD]);
				$empresa = $_POST[self::DEFAULT_POST_EMPRESA];
				$_SESSION['empresa'] = ($empresa == '1' || $empresa == '2' ? $empresa : '1');
			} else {
				$username = $_SESSION[self::DEFAULT_SESSION_USERNAME];
				$password = $_SESSION[self::DEFAULT_SESSION_PASSWORD];
			}
		}

		try {
			$usuarioLogueado =  Factory::getInstance()->getUsuarioLogin($username);
			if ($usuarioLogueado->anulado == 'S'){
				throw new LoginFailException('El usuario se encuentra inhabilitado');
			}
			if ($usuarioLogueado->password !== $password) {
				throw new FactoryExceptionRegistroNoExistente();
			}
			self::$_usuarioLogueado = $usuarioLogueado;
			$_SESSION[self::DEFAULT_SESSION_USERNAME] = $username;
			$_SESSION[self::DEFAULT_SESSION_PASSWORD] = $password;

			// Cliente debe ser siempre empresa 1
			if ($usuarioLogueado->esCliente() && $_SESSION['empresa'] != '1') {
                $_SESSION['empresa'] = '1';
            }
		} catch (FactoryExceptionRegistroNoExistente $ex){
			//Va a entrar por acá si el usuario no existe (username) o si la contraseña es incorrecta
			self::logout();
			throw new LoginFailException('Los datos ingresados son incorrectos');
		}
	}
}

?>