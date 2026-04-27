<?php

/**
 * @property string						$tipoUsuario
 * @property Contacto					$contacto
 * @property array						$funcionalidades
 * @property string						$nombre
 * @property string						$apellido
 * @property string						$nombreApellido
 * @property Personal|Operador|Vendedor	$personal
 * @property Cliente					$cliente
 * @property int 						$codigoPersonal
 * @property array						$roles
 */
class Usuario extends Base {
	const		_primaryKey = '["id"]';
	protected static $_usuarioLogueado = null;

	public		$id;
	protected	$_tipoUsuario;	//Personal (P) para P-O-V o Contacto (C) para C-R-X en TipoPersona
	public		$tipoPersona;	//"P"ersonal, "O"perador o "V"endedor || "C"liente, Proveedor ("R") u Otro ("X") ¡HAY ENUM!
	public		$anulado;
	public		$idContacto;
	protected	$_contacto;
    protected	$_cliente;
	public		$idUsuarioAlta;
	protected	$_usuarioAlta;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaAct;
	public		$fechaUltimaMod;
	protected	$_funcionalidades;
	protected	$_nombre;
	protected	$_apellido;
	protected	$_nombreApellido;
	public		$idPersonal;
	protected	$_personal;
	protected	$_codigoPersonal; //Acá voy a devolver el ID Personal si es un personal o el ID de operador si es Operador o Vendedor. Si es contacto, el id.
	protected	$_roles;
	public		$mensajeHome;

	/**
	 * @param bool $usuarioDummy
	 *
	 * @return Usuario
	 */
	public static function logueado($usuarioDummy = false) {
		return isset(self::$_usuarioLogueado) ? self::$_usuarioLogueado : ($usuarioDummy ? Factory::getInstance()->getUsuarioLogin() : false);
	}

	public function borrar() {
		foreach ($this->roles as $rol) {
			Factory::getInstance()->marcarParaBorrar($rol);
		}

		return parent::borrar();
	}

	public function esCliente() {
		return $this->tipoPersona == TiposContacto::cliente;
	}

	public function esVendedor() {
		return $this->tipoPersona == TiposOperador::vendedor;
	}

	public function esPersonal() {
		return !($this->esVendedor() || $this->esCliente());
	}

	public function getCodigoPersonal() {
		try {
			switch ($this->tipoPersona){
				case TiposPersonal::personal:
					return $this->getPersonal()->idPersonal;
					break;
				case TiposPersonal::operador:
				case TiposPersonal::vendedor:
					return $this->getPersonal()->id; //Problemas. Acá debería ir ID pq si no no anda la cta cte
					break;
				default:
					return $this->getContacto()->id;
					break;
			}
		} catch (Exception $ex){
			throw $ex;
		}
	}

	public function heartbeat() {
		$hb = Factory::getInstance()->getHeartbeat($this->id);
		Factory::getInstance()->persistir($hb);
	}

	public function getHtmlIndicadores() {
		$indicadores = array();
		foreach($this->roles as $rol) {
			foreach($rol->indicadores as $indic) {
				$indicadores[$indic->id] = $indic;
			}
		}
		$html = '<div class="indicadores"><ul>';
		foreach($indicadores as $i) {
			/** @var $i Indicador */
			$valores = $i->getValores();
			$valoresColores = $i->getValoresColores();
			$html .= '<li>';
			$html .= '<span class="indicador-nombre" title="' . $i->descripcion . '">' . $i->nombre . '</span><span class="indicador-valores">';
			$k = 0;
			foreach ($valores as $nombre => $valorShow) {
				$valorShow = str_replace('*', ',', str_replace(',', '.', str_replace('.', '*', $valorShow)));
				$valor =  Funciones::toInt(Funciones::formatearDecimales($valorShow, 0));
				$color = '';
				$valCol = array(
					'1' => Funciones::keyIsSet($valoresColores[1], $k, 0),
					'2' => Funciones::keyIsSet($valoresColores[2], $k, 0),
					'3' => Funciones::keyIsSet($valoresColores[3], $k, 0)
				);
				$combinaciones = array(1 => array(array(1), array(2), array(3)), 2 => array(array(1, 2), array(1, 3), array(2, 3)), 3 => array(array(1, 2, 3)));
				if (count($valCol) > 0) {
					$colores = array(1 => 'verde', 2 => 'amarillo', 3 => 'rojo');
					$combinaciones = $combinaciones[count($valCol)];
					$modo = count($valCol) == 1 || $valCol[1] < $valCol[2] || $valCol[1] < $valCol[3] || $valCol[2] < $valCol[3] ? 'A' : 'D'; //Modo ascendente o descendente

					$combinacion = true;
					foreach ($combinaciones as $arrComb) {
						foreach ($arrComb as $itemComb) {
							if (!array_key_exists($itemComb, $valCol)) {
								$combinacion = false;
								break;
							}
						}
						if ($combinacion) {
							$combinacion = $arrComb;
							break;
						}
					}
					if ($modo == 'A') {
						if ($valor >= $valCol[$combinacion[0]]) {
							for ($j = 0; $j < count($combinacion); $j++) {
								if ($j == count($combinacion) - 1) {
									$color = $colores[$combinacion[$j]];
								} elseif ($valor >= $valCol[$combinacion[$j]] && $valor < $valCol[$combinacion[$j + 1]]) {
									$color = $colores[$combinacion[$j]];
									break;
								}
							}
						}
					} else {
						if ($valor >= $valCol[$combinacion[count($combinacion) - 1]]) {
							for ($j = count($combinacion) - 1; $j >= 0; $j--) {
								if ($j == 0) {
									$color = $colores[$combinacion[$j]];
								} elseif ($valor >= $valCol[$combinacion[$j]] && $valor < $valCol[$combinacion[$j - 1]]) {
									$color = $colores[$combinacion[$j]];
									break;
								}
							}
						}
					}
				}
				$nombre = $nombre == 'computed' ? '' : ucfirst(str_replace('_', ' ', $nombre));
				$html .= '<span class="indicador ' . $color . '" title="' . $nombre . '">' . $valorShow . '</span>';
				$k++;
			}
			$html .= '</span></li>';
		}
		$html .= '</ul></div>';
		return $html;
	}

	public function anularNotificaciones($arrayAnuladas) {
		foreach($arrayAnuladas as $anulada) {
			try {
				$notif = Factory::getInstance()->getNotificacionPorUsuario($this->id, $anulada);
				if ($notif->eliminable == 'S')
					$notif->borrar();
			} catch (FactoryExceptionRegistroNoExistente $ex) {
			} catch (Exception $ex) {
			}
		}
	}

	public function visarNotificaciones($arrayVistas) {
		if (!is_array($arrayVistas)) {
			return false;
		}
		foreach($arrayVistas as $vista) {
			try {
				$notif = Factory::getInstance()->getNotificacionPorUsuario($this->id, $vista);
				if ($notif->vista != 'S') {
					$notif->vista = 'S';
					$notif->guardar();
				}
			} catch (FactoryExceptionRegistroNoExistente $ex) {
			} catch (Exception $ex) {
			}
		}
	}
	public function getNotificaciones($fechaHora = '') {
		$order = ' ORDER BY fecha_ultima_mod DESC, cod_notificacion DESC';
		$where = 'cod_usuario = ' . Datos::objectToDB($this->id) . ' ';
		//$where .= 'AND anulado = \'N\' AND (vista = \'N\' OR eliminable = \'N\')';
		if (!empty($fechaHora)) {
			//Hay que mandarle sólo las notificaciones posteriores a la hora enviada
			$where .= ' AND dbo.toDate(fecha_ultima_mod) > dbo.toDate(' . Datos::objectToDB($fechaHora) . ')';
		} else {
			$temp = Factory::getInstance()->getListObject('NotificacionPorUsuario', $where . $order, '1');//Esto es para que la primera vez tenga
			if (count($temp) > 0)															// la fecha de la última notif modificada 
				$ultimaHora = $temp[0]->fechaUltimaMod; 									// así no se mandan después
			$where .= ' AND anulado = \'N\'';
		}
		$arr = array();
		foreach(Factory::getInstance()->getListObject('NotificacionPorUsuario', $where . $order) as $notif) {
			$arr[] = array(
				'id' => Funciones::toInt($notif->notificacion->id),
				'imagen' => $notif->notificacion->tipoNotificacion->imagen,
				'nombre' => $notif->notificacion->tipoNotificacion->nombre,
				'detalle' => $notif->notificacion->detalle,
				'link' => $notif->notificacion->link,
				'anulado' => $notif->anulado,
				'vista' => $notif->vista,
				'eliminable' => $notif->eliminable,
				'fechaUltimaMod' => $notif->fechaUltimaMod
			);
		}
		if (isset($ultimaHora) && count($arr) > 0) { //Entra sólo la primera vez.
			$arr[0]['fechaUltimaMod'] = $ultimaHora;
		}
		return $arr;
	}
	public function puede($funcionalidad) {
		$arrayExcepciones = array(
			'logout/',
			'cliente/logout/',
			'fichaje/'
		);
		$this->getFuncionalidades();
		if ($funcionalidad == '' || in_array($funcionalidad, $arrayExcepciones))
			return true;
		return isset($this->_funcionalidades[$funcionalidad]) && $this->_funcionalidades[$funcionalidad] == 1;
	}
	public function tieneRol($nombreRol) {
		foreach ($this->getRoles() as $rol){
			if ($rol->nombre == $nombreRol)
				return true;
		}
		return false;
	}

	//GETS y SETS
	protected function getApellido() {
		if (!isset($this->_apellido)){
			switch ($this->getTipoUsuario()){
				case 'P':
					$this->_apellido = $this->getPersonal()->apellido;
					break;
				case 'C':
					$this->_apellido = $this->getContacto()->apellido;
					break;
			}
		}
		return $this->_apellido;
	}
	protected function setApellido($apellido) {
		$this->_apellido = $apellido;
		return $this;
	}
	protected function getCliente() {
		if (!isset($this->_cliente) && $this->esCliente() && $this->contacto->esCliente()) {
            $this->_cliente = $this->contacto->cliente;
		}
		return $this->_cliente;
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
	protected function getFuncionalidades() {
		if (!isset($this->_funcionalidades)){
			$this->_funcionalidades = array();
			foreach ($this->getRoles() as $rol){
				foreach ($rol->funcionalidades as $funcionalidad){
					$this->_funcionalidades[$funcionalidad->nombre] = 1;
				}
			}
		}
		return $this->_funcionalidades;
	}
	protected function getNombre() {
		if (!isset($this->_nombre)){
			switch ($this->getTipoUsuario()){
				case 'P':
					$this->_nombre = $this->getPersonal()->nombre;
					break;
				case 'C':
					$this->_nombre = $this->getContacto()->nombre;
					break;
			}
		}
		return $this->_nombre;
	}
	protected function setNombre($nombre) {
		$this->_nombre = $nombre;
		return $this;
	}
	protected function getNombreApellido() {
		if (!isset($this->_nombreApellido)){
			$this->_nombreApellido = $this->getNombre() . ' ' . $this->getApellido();
		}
		return $this->_nombreApellido;
	}
	protected function setNombreApellido($nombreApellido) {
		$this->_nombreApellido = $nombreApellido;
		return $this;
	}

	/**
	 * @return Personal|Operador|Vendedor
	 */
	protected function getPersonal() {
		if (!isset($this->_personal)){
			try {
				$clase = '';
				switch ($this->tipoPersona){
					case TiposPersonal::personal:
						$clase = 'Personal';
						break;
					case TiposPersonal::operador:
						$clase = 'Operador';
						break;
					case TiposPersonal::vendedor:
						$clase = 'Vendedor';
						break;
				}
				if ($clase != '' && isset($this->idPersonal))
					$arr = Factory::getInstance()->getListObject($clase, 'cod_personal = ' . Datos::objectToDB($this->idPersonal) . ($clase == 'Personal' ? '' : ' AND tipo_operador = ' . Datos::objectToDB($this->tipoPersona)));
				if (count($arr) == 1)
					$this->_personal = $arr[0];
			} catch (Exception $ex){
				$this->_personal = Factory::getInstance()->getPersonal();
			}
		}
		return $this->_personal;
	}
	protected function setPersonal($personal) {
		$this->_personal = $personal;
		return $this;
	}
	protected function getRoles() {
		if (!isset($this->_roles) && isset($this->id)){
			$this->_roles = Factory::getInstance()->getListObject('RolPorUsuario', 'anulado = \'N\' AND cod_usuario = ' . Datos::objectToDB($this->id));
		}
		return $this->_roles;
	}
	protected function setRoles($roles) {
		$this->_roles = $roles;
		return $this;
	}
	protected function getTipoUsuario() {
		if (!isset($this->_tipoUsuario)){
			switch ($this->tipoPersona) {
				case TiposPersonal::operador:
				case TiposPersonal::personal:
				case TiposPersonal::vendedor:
					$this->_tipoUsuario = 'P';
					break;
				case TiposContacto::cliente:
				case TiposContacto::proveedor:
				case TiposContacto::otro:
					$this->_tipoUsuario = 'C';
					break;
			}
		}
		return $this->_tipoUsuario;
	}
	protected function setTipoUsuario($tipoUsuario) {
		$this->_tipoUsuario = $tipoUsuario;
		return $this;
	}
	protected function getUsuarioAlta() {
		if (!isset($this->_usuarioAlta)){
			$this->_usuarioAlta = Factory::getInstance()->getUsuario($this->idUsuarioAlta);
		}
		return $this->_usuarioAlta;
	}
	protected function setUsuarioAlta($usuarioAlta) {
		$this->_usuarioAlta = $usuarioAlta;
		return $this;
	}
}

?>