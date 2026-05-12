<?php

/**
 * @property TipoNotificacion	$tipoNotificacion
 * @property array				$usuarios
 */

class Notificacion extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idTipoNotificacion;
	protected	$_tipoNotificacion;
	public		$keyObjeto;	//Es la PK serializada del objeto en cuestión. Puede ser NULL. (Ej: id=15&tipo=M)
	public		$link;
	public		$detalle;
	protected	$_usuarios;
	public		$anulado;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	/**
	 * @param       $obj
	 * @param       $funcionalidad
	 * @param array $usuarios Esta lista será anexada a la establetica por el tipo de notificación.
	 *                        Si se quiere especificar el atributo "eliminable", deberá ser una lista de UsuarioPorTipoNotificacion
	 *
	 * @return bool
	 */
	public static function accionNotificar($obj, $funcionalidad, $usuarios = array()) {
		if ($funcionalidad) {
			//Verifico si la funcionalidad debe dar como cumplida alguna notificación. De ser así, esas notif las pongo como eliminables
			$cumplidos = Factory::getInstance()->getListObject('TipoNotificacion', 'anulado = \'N\' AND accion_cumplido = ' . Datos::objectToDB($funcionalidad));
			foreach ($cumplidos as $tipoCumplido) {
				/** @var $tipoCumplido TipoNotificacion */
				$where = 'tipo_notificacion = ' . Datos::objectToDB($tipoCumplido->id) . ' AND key_objeto = ' . Datos::objectToDB($obj->getPKSerializada());
				$notifs = Factory::getInstance()->getListObject('Notificacion', $where);
				if (count($notifs) == 1) {
					$noti = $notifs[0];
					foreach ($noti->usuarios as $nxu) {
						/** @var $nxu UsuarioPorTipoNotificacion */
						if ($tipoCumplido->anularAlCumplir == 'S') {
							$nxu->borrar();
						} else {
							$nxu->eliminable = 'S';
							$nxu->guardar();
						}
					}
				}
			}

			//Verifico si la funcionalidad debe eliminar alguna notificación. De ser así, esas notif las pongo como anuladas
			$anulados = Factory::getInstance()->getListObject('TipoNotificacion', 'anulado = \'N\' AND accion_anular = ' . Datos::objectToDB($funcionalidad));
			foreach ($anulados as $tipoAnulado) {
				$where = 'tipo_notificacion = ' . Datos::objectToDB($tipoAnulado->id) . ' AND key_objeto = ' . Datos::objectToDB($obj->getPKSerializada());
				$notifs = Factory::getInstance()->getListObject('Notificacion', $where);
				if (count($notifs) == 1) {
					$noti = $notifs[0];
					foreach ($noti->usuarios as $nxu) {
						$nxu->borrar();
					}
				}
			}

			//Verifico si la funcionalidad debe disparar alguna notificación
			$notificar = Factory::getInstance()->getListObject('TipoNotificacion', 'anulado = \'N\' AND accion_notificacion = ' . Datos::objectToDB($funcionalidad));
			if (count($notificar) == 1) {
				$tipoNotificar = $notificar[0];
				$noti = Factory::getInstance()->getNotificacion();
				$noti->tipoNotificacion = $tipoNotificar;
				$noti->keyObjeto = $obj->getPKSerializada();
				$noti->link = $tipoNotificar->link . '?' . $obj->getPKSerializada();
				$noti->detalle = self::formarDetalle($tipoNotificar->detalle, $obj);
				if (count($usuarios)) {
					$tipoNotificar->usuarios = array_merge($tipoNotificar->usuarios, $usuarios);
				}
				$noti->usuarios = self::formarUsuarios($tipoNotificar);
				$noti->guardar();
			}

			return true;
		}
		return false;
	}

	private static function formarDetalle($template, $obj) {
		$detalle = '';
		try {
			$arr = explode('///', $template);
			if (Funciones::esImpar(count($arr))) {
				for ($i = 0; $i < count($arr); $i++) {
					if (Funciones::esPar($i)) {
						$detalle .= $arr[$i];
					} else {
						$arrAttr = explode('->', $arr[$i]);
						$attr = $obj;
						foreach($arrAttr as $attrAct)
							$attr = $attr->$attrAct;
						$detalle .= $attr;
					}
				}
			}
		} catch (Exception $ex) {
			//Si ocurre un error al generar el detalle, no me importa
		}
		return $detalle;
	}

	private static function formarUsuarios($tipoNoti) {
		/** @var TipoNotificacion $tipoNoti */
		$a = array();
		$return = array();
		//Le doy prioridad a ->usuarios. Si superponen roles y usuarios con distinta acción (eliminable) se prioriza el usuario
		foreach($tipoNoti->usuarios as $usu) {
			if (!isset($a[$usu->id])) {
				$nxu = Factory::getInstance()->getNotificacionPorUsuario();
				if (property_exists($usu, 'eliminable')) {
					$nxu->eliminable = $usu->eliminable;
				} else {
					$nxu->eliminable = 'S';
				}
				$nxu->id = $usu->id;
				$a[$nxu->id] = $nxu;
			}
		}
		foreach($tipoNoti->roles as $rol) {
			foreach($rol->usuarios as $usu) {
				if (!isset($a[$usu->idUsuario])) {
					$nxu = Factory::getInstance()->getNotificacionPorUsuario();
					$nxu->eliminable = $rol->eliminable;
					$nxu->id = $usu->idUsuario;
					$a[$nxu->id] = $nxu;
				}
			}
		}
		foreach($a as $item) {
			$return[] = $item;
		}
		return $return;
	}

	//GETS y SETS
	protected function getTipoNotificacion() {
		if (!isset($this->_tipoNotificacion)){
			$this->_tipoNotificacion = Factory::getInstance()->getTipoNotificacion($this->idTipoNotificacion);
		}
		return $this->_tipoNotificacion;
	}
	protected function setTipoNotificacion($tipoNotificacion) {
		$this->_tipoNotificacion = $tipoNotificacion;
		return $this;
	}
	protected function getUsuarios() {
		if (!isset($this->_usuarios)){
			$this->_usuarios = Factory::getInstance()->getListObject('NotificacionPorUsuario', 'cod_notificacion = ' . Datos::objectToDB($this->id));
		}
		return $this->_usuarios;
	}
	protected function setUsuarios($usuarios){
		$this->_usuarios = $usuarios;
		return $this;
	}
	
}

?>