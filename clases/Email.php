<?php

/**
 * @property PHPMailer		$email
 * @property Usuario		$usuario
 * @property Usuario		$usuarioBaja
 * @property Usuario		$usuarioUltimaMod
 */

class Email extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$de;
	public		$para; //Array de direcciones (strings)
	public		$cc; //Array de direcciones (strings)
	public		$cco; //Array de direcciones (strings)
	public		$asunto;
	public		$contenido;
	public		$imagenes; //Array de paths a las imágenes
	public		$adjuntos; //Array de paths a los attachments
	public		$fechaProgramada;
	public		$fechaEnviado;
	public		$anulado;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$fechaAlta;
	public		$fechaUltimaMod;
	public		$fechaBaja;

	private		$email;
	private		$emailDesarrollo = 'developement@spiralshoes.com';

	//Acá se pueden agregar cuentas desde las cuales se quiera mandar los mails (es el parámetro "de", que por defecto trae spiralkoi@gmail.com, ver $emailConfig)
	private 	$mailAccounts = array(
		'spiralkoi@gmail.com' => array(
			'name'			=> 'Spiral Shoes',
			'address'		=> 'spiralkoi@gmail.com',
			'password'		=> 'Spiral666',
			'host'			=> 'mail.google.com',
			'port'			=> 25
		),
	);

	//Acá se puede poner valores default
	private		$emailDefaultData = array(
		'de'				=> 'spiralkoi@gmail.com',
		'para'				=> array(),
		'cc'				=> array(),
		'cco'				=> array(),
		'asunto'			=> '',
		'contenido'			=> '',
		'imagenes'			=> array(),
		'adjuntos'			=> array(),
		'fechaProgramada'	=> ''
	);

	private		$mailerConfig = array(
		'CharSet'		=> 'iso-8859-1',
		'ContentType'	=> 'text/html',
		'Sender'		=> '',
		'Mailer'		=> 'mail',
		'Sendmail'		=> '/xampp/sendmail',
		'SMTPSecure'	=> '',
		'SMTPAuth'		=> true,
		'Timeout'		=> 10
	);

	public static function enviar($data, $funcionalidad = false) {
		$email = Factory::getInstance()->getEmail();
		$email->fillEmail($data);
		$email->guardar()->notificar($funcionalidad);
	}

    private function fillEmail($data) {
		foreach ($this->emailDefaultData as $attr => $value) {
			$this->$attr = ($data[$attr]) ? $data[$attr] : $this->emailDefaultData[$attr];
		}
		$this->fechaProgramada = $data['fechaProgramada'] ? $data['fechaProgramada'] : Funciones::getDate();
	}

	protected function validarGuardar() {
		// 1) Validar dirección DE con las $mailAccounts
		if (!isset($this->mailAccounts[$this->de])) {
			throw new FactoryExceptionCustomException('La dirección "' . $this->de . '" ingresada en el campo "DE" no es una de las direcciones posibles para enviar emails');
		}

		// 2) Validar direcciones PARA, CC y BCC según la función de validación
		foreach (array('para', 'cc', 'cco') as $campo) {
			if (count($this->$campo)) {
				foreach ($this->$campo as $direccion) {
					if (!self::validarDireccion($direccion)) {
						throw new FactoryExceptionCustomException('La dirección "' . $direccion . '" ingresada en el campo "' . $campo . '" no tiene un formato válido de email');
					}
				}
			}
		}

		// 3) Validar que tenga que haber al menos una dirección entre PARA, CC y BCC
		if (!count($this->para) && !count($this->cc) && !count($this->cco)) {
			throw new FactoryExceptionCustomException('Debe ingresar al menos un destinatario para el email');
		}

		// 4) Validar paths de imágenes y adjuntos
		foreach (array('imagenes', 'adjuntos') as $campo) {
			if (count($this->$campo)) {
				foreach ($this->$campo as $path) {
					if (!file_exists($path)) {
						throw new FactoryExceptionCustomException('No se encuentra el archivo "' . $path . '" que intentó ingresar en el campo "' . $campo . '"');
					}
				}
			}
		}
	}

	public function enviarReal() {
		$mutex = new Mutex('EnvioEmail');
		$mutex->lock();

		$this->initMailer();
		$this->mapObjects();
		$this->email->Send();
		$this->email->ClearAddresses();
		$this->fechaEnviado = Funciones::getDate();
		$this->guardar();

		$mutex->unlock();
	}

	private function initMailer() {
		$this->email = new PHPMailer();
		foreach ($this->mailerConfig as $attr => $value) {
			$this->email->$attr = $value;
		}
	}

	private function mapObjects() {
		//Hago una salvación para cuando estoy en desarrollo, que no mande mails a los clientes
		if (Config::desarrollo()) {
			$this->para = array($this->emailDesarrollo);
			$this->cc = array();
			$this->cco = array();
		}

		//Datos de la cuenta
		$fromAccount = $this->mailAccounts[$this->de];
		$this->email->From = $fromAccount['address'];
		$this->email->FromName = $fromAccount['name'];
		$this->email->Host = $fromAccount['host'];
		$this->email->Port = $fromAccount['port'];
		$this->email->Username = $fromAccount['address'];
		$this->email->Password = $fromAccount['password'];

		//Ingreso las direcciones, imágenes y adjuntos correspondientes
		foreach (array(array('para', 'AddAddress'), array('cc', 'AddCC'), array('cco', 'AddBCC'), array('imagenes', 'AddEmbeddedImage'), array('adjuntos', 'AddAttachment')) as $campo) {
			$attr = $campo[0];
			$array = $this->$attr;
			if (count($array)) {
				$i = 1000;
				foreach ($array as $elemento) {
					$method = $campo[1];
					if ($attr == 'imagenes') {
						$this->email->$method($elemento, $i, basename($elemento));
						$i++;
					} else {
						$this->email->$method($elemento);
					}
				}
			}
		}

		//Otros datos del Email
		$this->email->Subject = $this->asunto;
		$this->email->Body = $this->contenido;
	}

	public static function validarDireccion($direccion) {
		return PHPMailer::ValidateAddress($direccion);
	}

	//GETS y SETS
}

?>