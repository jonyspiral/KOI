<?php

class HtmlAutoSuggestBox extends Html {
	public	$id;
	public	$class;
	public	$style;
	public	$name;
	public	$defVal;
	public	$defName;
	public	$linkedTo;
	public	$alts;

	public function __construct($config = array()) {
		$this->linkedTo = array();
		$this->alts = array();
		$this->style = new HtmlStyle();
		$this->constructFromArray($config);
	}

	public function toString(){
		$string = '';
		$string .= '<input ';
		$string .= 'id="' . Funciones::iIsSet($this->id) . '" ';
		$string .= 'class="autoSuggestBox ' . Funciones::iIsSet($this->class) . '" ';
		$string .= 'style="' . $this->style->toString() . '" ';
		$string .= 'name="' . $this->name . '" ';
		if (count($this->linkedTo) > 0) {
			$string .= 'linkedTo="';
			foreach($this->linkedTo as $link) {
				$string .= $link['input'] . ',' . $link['name'] . ';';
			}
			$string = trim($string, ';');
			$string .= '" ';
		}
		if (count($this->alts) > 0) {
			$string .= 'alt="';
			foreach($this->alts as $altId => $altVal) {
				$string .= '&' . Html::escapeUrl($altId) . '=' . Html::escapeUrl($altVal);
			}
			$string .= '" ';
		}
		if (!empty($this->defVal)) {
			$string .= 'defVal="' . $this->defVal . '" defName="' . $this->defName . '" ';
		}
		$string .= '/>';
		return $string;
	}

	static function getSuggestRows($name, $key, $limit = 10){
		$array = array();
		$where = '';
		$WHEREFALSO = '1 = 2';
		switch ($name){
			case 'Almacen':
				if (Usuario::logueado()->puede('listar/abm/almacenes/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') ';
						$where .= 'AND (cod_almacen LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_almacen LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'AporteSocio':
				if (Usuario::logueado()->puede('listar/administracion/cobranzas/ingresos/aporte_socios/')) {
					try {
						$where .= 'empresa = ' . Datos::objectToDB(Funciones::session('empresa')) . ' AND ';
						$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= 'nro_aporte_socio LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'ORDER BY fecha_documento DESC; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->numero, 'nombre' => $obj->fechaAlta);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'AreaEmpresa':
				if (Usuario::logueado()->puede('listar/abm/areas_empresa/')) {
					try {
						$habilitadaTicket = Funciones::get('habilitadaTicket');
						if ($habilitadaTicket) {
							$where .= 'habilitada_ticket = ' . Datos::objectToDB('S') . ' AND ';
						}
						$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= '(id LIKE ' . Datos::objectToDB('%' . $key . '%') . ' OR ';
						$where .= 'nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
				break;
			case 'Articulo':
				if (Usuario::logueado()->puede('listar/abm/articulos/')) {
					try {
						$where .= 'vigente = ' . Datos::objectToDB('S') . ' AND naturaleza = ' . Datos::objectToDB('PT') . ' ';
						$where .= 'AND (cod_articulo LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_articulo LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'ArticuloTodos':
				if (Usuario::logueado()->puede('listar/abm/articulos/')) {
					try {
						$where .= 'vigente = ' . Datos::objectToDB('S') . ' AND ';
						$where .= '(cod_articulo LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_articulo LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject('Articulo', $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'AsientoContableModelo':
				if (Usuario::logueado()->puede('listar/administracion/contabilidad/asientos_modelo/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') ';
						$where .= 'AND (cod_asiento_modelo LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Banco':
				if (Usuario::logueado()->puede('listar/abm/bancos/')) {
					try {
						$where .= '(fecha_baja IS NULL) ';
						$where .= 'AND (cod_banco LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR numero_banco LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->idBanco, 'nombre' => '[' . $obj->codigoBanco .'] ' . $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'BancoPropio':
				if (Usuario::logueado()->puede('listar/abm/bancos_propios/')) {
					try {
						$banco = Funciones::get('idBanco');
						if ($banco != '')
							$where .= '(cod_banco = ' . Datos::objectToDB($banco) . ') AND ';
						$where .= '(fecha_baja IS NULL) AND ';
						$where .= '(cod_sucursal LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre_sucursal LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->idSucursal, 'nombre' => $obj->nombreSucursal);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Caja':
				if (Usuario::logueado()->puede('listar/abm/caja/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') ';
						$where .= 'AND (cod_caja > 1) ';
						$where .= 'AND (cod_caja LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'CajaBanco':
				if (true) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') ';
						$where .= 'AND (cod_caja > 1) ';
						$where .= 'AND (es_caja_banco = ' . Datos::objectToDB('S') . ') ';
						$where .= 'AND (cod_usuario = ' . Datos::objectToDB(Usuario::logueado()->id) . ') ';
						$where .= 'AND (cod_permiso = ' . Datos::objectToDB(PermisosUsuarioPorCaja::verCaja) . ') ';
						$where .= 'AND (cod_caja LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject('PermisoPorUsuarioPorCaja', $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->idCaja, 'nombre' => $obj->caja->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'CajaPorUsuario':
				if (true) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') ';
						$where .= 'AND (cod_caja > 1) ';
						$where .= 'AND (cod_usuario = ' . Datos::objectToDB(Usuario::logueado()->id) . ') ';
						$where .= 'AND (cod_permiso = ' . Datos::objectToDB(PermisosUsuarioPorCaja::verCaja) . ') ';
						$where .= 'AND (cod_caja LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject('PermisoPorUsuarioPorCaja', $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->idCaja, 'nombre' => $obj->caja->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'CajaPosiblesTransferenciaInterna':
				if (true) {
					try {
						$idCajaSalida = Funciones::get('idCaja');

						$where .= 'cod_caja_salida = ' . Datos::objectToDB($idCajaSalida) . ' ';
						$where .= 'AND (cod_caja_entrada LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre_caja_entrada LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject('CajaPosiblesTransferenciaInterna', $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->idCajaEntrada, 'nombre' => $obj->cajaEntrada->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'CategoriaCalzadoUsuario':
				if (Usuario::logueado()->puede('listar/abm/categorias_calzado_usuario/')) {
					try {
						$where .= '(cod_categoria LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_categoria LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'CausaNotaDeCredito':
				if (Usuario::logueado()->puede('listar/abm/causas_notas_de_credito/')) {
					try {
						$where .= '(clave_tabla LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR causa_nota_credito LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Cheque':
				if (Usuario::logueado()->puede('listar/administracion/tesoreria/cheques/')) {
					try {
						$caja = Factory::getInstance()->getCaja(Funciones::get('idCaja'));
						if ($caja->esUsuario(Usuario::logueado())) {
							$where .= '(cod_caja_actual = ' . Datos::objectToDB($caja->id) . ') AND ';
							$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND concluido = ' . Datos::objectToDB('N') . ' AND ';
							$where .= '((cod_cheque LIKE ' . Datos::objectToDB('%' . $key . '%') . ') OR ';
							$where .= '(banco_nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . ')); ';
							$objs = Factory::getInstance()->getListObject($name, $where, $limit);
							foreach ($objs as $obj){
								$array[] = array('id' => $obj->id, 'nombre' => $obj->numero . ' (' . $obj->banco->nombre . ')', 'data' => array(
									'importe' => $obj->importe,
									'fechaVencimiento' => $obj->fechaVencimiento,
									'numero' => $obj->numero
								));
							}
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'ChequePropio':
				if (Usuario::logueado()->puede('listar/administracion/tesoreria/cheques/')) {
					try {
						$idCuentaBancaria = Funciones::get('idCuentaBancaria');
						if ($idCuentaBancaria != '') {
							$where = 'cod_cuenta_bancaria = ' . Datos::objectToDB($idCuentaBancaria) . ' AND ';
							$where .= 'numero LIKE ' . Datos::objectToDB('%' . $key . '%') . ' AND ';
							$where .= 'concluido = ' . Datos::objectToDB('N') . 'AND ';
							$where .= 'anulado = ' . Datos::objectToDB('N') . '; ';
						} else {
							$where .= $WHEREFALSO;
						}
						$objs = Factory::getInstance()->getListObject('Cheque', $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->numero);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'ChequeTerceros':
				if (Usuario::logueado()->puede('listar/administracion/tesoreria/cheques/')) {
					try {
						$idBanco = Funciones::get('idBanco');
						if ($idBanco != '') {
							$where = 'cod_banco = ' . Datos::objectToDB($idBanco) . ' AND ';
							$where .= 'numero LIKE ' . Datos::objectToDB('%' . $key . '%') . 'AND ';
							$where .= 'concluido = ' . Datos::objectToDB('N') . 'AND ';
							$where .= 'cod_cuenta_bancaria is null AND ';
							$where .= 'anulado = ' . Datos::objectToDB('N') . '; ';
						} else {
							$where .= $WHEREFALSO;
						}
						$objs = Factory::getInstance()->getListObject('Cheque', $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->numero);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'ChequeraItem':
				if (Usuario::logueado()->puede('listar/administracion/tesoreria/cheques/chequera/')) {
					try {
						$cuentaBancaria = Funciones::get('idCuentaBancaria');
						if ($cuentaBancaria != '') {
							$where .= 'cod_cuenta_bancaria = ' . Datos::objectToDB($cuentaBancaria) . ' AND ';
							$where .= 'numero LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
							$where .= ' ORDER BY fecha';
							$objs = Factory::getInstance()->getListObject($name, $where, $limit);
							foreach ($objs as $obj){
								$array[] = array('id' => $obj->id, 'nombre' => '[' . $obj->numero . '] ' . $obj->chequera->fecha);
							}
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Cliente':
				if (Usuario::logueado()->puede('listar/abm/clientes/')) {
					try {
						$vendedor = Funciones::get('idVendedor');
						$whereEspecial = '';
						if (Usuario::logueado()->esCliente()){
							$whereEspecial = '(cod_cli = ' . Datos::objectToDB(Usuario::logueado()->contacto->cliente->id) . ') AND ';
						} elseif (Usuario::logueado()->esVendedor()) {
							$whereEspecial = '(cod_vendedor = ' . Datos::objectToDB(Usuario::logueado()->getCodigoPersonal()) . ') AND ';
						}
						if ($vendedor != '')
							$where .= '(cod_vendedor = ' . Datos::objectToDB($vendedor) . ') AND ';
						$where .= $whereEspecial;
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND (cod_cli LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR razon_social LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_fantasia LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->razonSocial);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'ClienteTodos':
				if (Usuario::logueado()->puede('listar/abm/clientes/')) {
					try {
						$whereEspecial = '';
						if (Usuario::logueado()->esCliente()){
							$whereEspecial = '(cod_cli = ' . Datos::objectToDB(Usuario::logueado()->contacto->cliente->id) . ') AND ';
						} elseif (Usuario::logueado()->esVendedor()) {
							$whereEspecial = '(cod_vendedor = ' . Datos::objectToDB(Usuario::logueado()->getCodigoPersonal()) . ') AND ';
						}
						$where .= $whereEspecial;
						$where .= '((anulado = ' . Datos::objectToDB('N') . ' AND autorizado = ' . Datos::objectToDB('S') . ') OR (anulado = ' . Datos::objectToDB('S') . ' AND autorizado = ' . Datos::objectToDB('N') . ')) AND (cod_cli LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR razon_social LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_fantasia LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject('Cliente', $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->razonSocial);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'CobroChequeVentanillaTemporal':
				if (Usuario::logueado()->puede('listar/administracion/tesoreria/cobro_cheques_ventanilla/')) {
					try {
						$fechaDesde = Funciones::get('fechaDesde');
						$fechaHasta = Funciones::get('fechaHasta');

						$where = (empty($fechaDesde) && empty($fechaHasta) ? '' : Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha') . 'AND ');
						$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= 'confirmado = ' . Datos::objectToDB('N') . ';';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->fecha);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Color':
				if (Usuario::logueado()->puede('listar/abm/colores_por_articulo/')) {
					try {
						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= '(cod_color LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_color LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'ColorMateriaPrima':
				if (Usuario::logueado()->puede('listar/abm/color_materia_prima/')) {
					try {
						$idMaterial = Funciones::get('idMaterial');

						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= (empty($idMaterial) ? '' : 'cod_material = ' . Datos::objectToDB($idMaterial) . ' AND ');
						$where .= '(cod_color LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
                        $where .= 'OR denom_color LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
                        $where .= 'OR abrev_color LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->idColor, 'nombre' => $obj->nombreColor);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'ColorPorArticulo':
				if (Usuario::logueado()->puede('listar/abm/colores_por_articulo/')) {
					try {
						$articulo = Funciones::get('idArticulo');
						if ($articulo != '') {
							$where .= '(vigente = ' . Datos::objectToDB('S') . ') AND (';
							$where .= '(cod_color_articulo LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
							$where .= 'OR denom_color LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
							$where .= 'AND (cod_articulo = ' . Datos::objectToDB($articulo) . ')' . '); ';
						} else {
							$where .= $WHEREFALSO;
						}
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Concepto':
				if (Usuario::logueado()->puede('listar/abm/conceptos/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
						$where .= 'nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'ConceptoRetencionGanancias':
				if (Usuario::logueado()->puede('listar/abm/concepto_retencion_ganancias/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
						$where .= '(cod_concepto_reten_ganan LIKE ' . Datos::objectToDB('%' . $key . '%') . ' OR ';
						$where .= 'concepto LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->concepto);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'CondicionIva':
				if (Usuario::logueado()->puede('listar/abm/condiciones_iva/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
						$where .= '(denom_cond_iva LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_completa LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Contacto':
				if (Usuario::logueado()->puede('listar/abm/contactos/')) {
					try {
						$whereEspecial = '';
						if (Usuario::logueado()->esCliente()){
							$whereEspecial = '(cod_cliente = ' . Datos::objectToDB(Usuario::logueado()->contacto->cliente->id) . ') AND ';
						} elseif (Usuario::logueado()->esVendedor()) {
							$whereEspecial = '(tipo = \'C\') AND ';
						}
						$where .= $whereEspecial;
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
						$where .= '(cod_contacto LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR apellido LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombreApellido);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Conjunto':
				if (Usuario::logueado()->puede('listar/abm/conjunto/')) {
					try {
						$where .= 'conjunto LIKE ' . Datos::objectToDB('%' . $key . '%') . ' OR ';
						$where .= 'denom_conjunto LIKE ' . Datos::objectToDB('%' . $key . '%') . '; ';

						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'CuentaBancaria':
				if (Usuario::logueado()->puede('listar/abm/cuenta_bancaria/')) {
					try {
						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= '(cod_cuenta_bancaria LIKE ' . Datos::objectToDB('%' . $key . '%') . ' OR ';
						$where .= 'nombre_cuenta LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombreCuenta);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Curva':
				if (Usuario::logueado()->puede('listar/abm/articulos/')) {
					try {
						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= '(cod_curva LIKE ' . Datos::objectToDB('%' . $key . '%') . ' OR ';
						$where .= 'denom_curva LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre, 'cantidad' => $obj->cantidad);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'DepositoBancarioTemporal':
				if (Usuario::logueado()->puede('listar/administracion/tesoreria/deposito_bancario/')) {
					try {
						$idCuentaBancaria = Funciones::get('idCuentaBancaria');
						$fechaDesde = Funciones::get('fechaDesde');
						$fechaHasta = Funciones::get('fechaHasta');
						$numeroBoleta = Funciones::get('numeroBoleta');

						$where = (empty($fechaDesde) && empty($fechaHasta) ? '' : Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha') . 'AND ');
						$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= 'confirmado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= '(cod_deposito_bancario_temporal LIKE ' . Datos::objectToDB('%' . $key . '%') . 'OR ';
						$where .= 'numero_boleta LIKE ' . Datos::objectToDB('%' . Datos::objectToDB($numeroBoleta) . '%') . ') ';
						$where .= (empty($idCuentaBancaria) ? '' : 'AND cod_cuenta_bancaria = ' . Datos::objectToDB($idCuentaBancaria) . ' ');
						$where .= ';';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->fecha);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'DocumentoGastos':
				if (Usuario::logueado()->puede('listar/administracion/tesoreria/gastos/documento_gastos/')) {
					try {
						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= 'factura_gastos = ' . Datos::objectToDB('S') . ' AND ';
						$where .= 'empresa = ' . Datos::objectToDB(Funciones::session('empresa')) . ' AND ';
						$where .= '(nro_documento LIKE ' . Datos::objectToDB('%' . $key . '%') . ' OR ';
						$where .= 'cod_documento_proveedor LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$where .= 'ORDER BY fecha DESC; ';
						$objs = Factory::getInstance()->getListObject('DocumentoProveedor', $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nroDocumentoCompleto . ' [' . $obj->fecha . ']');
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'DocumentoProveedor':
				if (Usuario::logueado()->puede('listar/administracion/tesoreria/documentos_proveedor/documento_proveedor/')) {
					try {
						$idProveedor = Funciones::get('idProveedor');

						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= 'factura_gastos = ' . Datos::objectToDB('N') . ' AND ';
						$where .= 'empresa = ' . Datos::objectToDB(Funciones::session('empresa')) . ' AND ';
						$where .= 'cod_proveedor = ' . Datos::objectToDB($idProveedor) . ' AND ';
						$where .= 'nro_documento LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'ORDER BY cod_documento_proveedor DESC; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nroDocumentoCompleto . ' [' . $obj->fecha . ']');
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Ecommerce_Customer':
				if (Usuario::logueado()->puede('comercial/ecommerce/')) {
					try {
						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= '(firstname LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR lastname LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->fullname());
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Ecommerce_Usergroup':
				if (Usuario::logueado()->puede('comercial/ecommerce/')) {
					try {
						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= '(cod_usergroup LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'EjercicioContable':
				if (Usuario::logueado()->puede('listar/abm/ejercicios_contables/')) {
					try {
						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= '(cod_ejercicio_contable LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'FajaHoraria':
				if (Usuario::logueado()->puede('listar/abm/fajas_horarias/')) {
					try {
						$where .= '(cod_faja_horaria LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denominacion_horario LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$where .= 'AND anulado = ' . Datos::objectToDB('N') . '; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'FacturaProveedorEnConflicto':
				if (Usuario::logueado()->puede('listar/administracion/tesoreria/documentos_proveedor/')) {
					try {
						$where .= 'nro_documento LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'AND documento_en_conflicto = ' . Datos::objectToDB('S') . ' ';
						$where .= 'AND anulado = ' . Datos::objectToDB('N') . '; ';
						$objs = Factory::getInstance()->getListObject('FacturaProveedor', $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nroDocumentoCompleto . ' - [' . $obj->fecha . ']');
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
            case 'Forecast':
                if (true /* TODO no tiene una funcionalidad asociada */) {
                    try {
                        $where .= '(IdForecast LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
                        $where .= 'OR Denom_Forecast LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
                        $where .= 'AND anulado = ' . Datos::objectToDB('N') . ' ';
                        $where .= 'AND aprobado = ' . Datos::objectToDB('N') . '; ';
                        $objs = Factory::getInstance()->getListObject($name, $where, $limit);
                        foreach ($objs as $obj){
                            $array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
                        }
                    } catch (Exception $ex) {
                        return false;
                    }
                    return $array;
                }
                break;
            case 'FormaDePago':
				if (Usuario::logueado()->puede('listar/abm/formas_de_pago/')) {
					try {
						$where .= '(cod_forma_pago_num LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_forma_pago LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$where .= 'AND anulado = ' . Datos::objectToDB('N') . '; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Garantia':
				if (Usuario::logueado()->puede('listar/comercial/garantias/')) {
					try {
						$cliente = Funciones::get('idCliente');
						$whereEspecial = '';
						if ($cliente != ''){
							$whereEspecial .= '(cod_cliente = ' . Datos::objectToDB($cliente) . ') AND ';
						}
						$where .= $whereEspecial;
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
						$where .= '(cod_garantia LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR fecha_alta LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$where .= 'ORDER BY fecha_alta ASC; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => Funciones::formatearFecha($obj->fechaAlta, 'd/m/Y'));
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'GrupoEmpresa':
				if (Usuario::logueado()->puede('listar/abm/grupo_empresa/')) {
					try {
						$where .= '(cod_grupo_empresa LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denominacion LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Horma':
				if (Usuario::logueado()->puede('listar/abm/hormas/')) {
					try {
						$where = 'activa = ' . Datos::objectToDB('S') . ' AND ';
						$where .= '(cod_horma LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_horma LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Imputacion':
				if (Usuario::logueado()->puede('listar/abm/plan_cuentas/')) {
					try {
						$esImputable = Funciones::get('esImputable');
						if ($esImputable) {
							$where = 'imputable = ' . Datos::objectToDB(($esImputable == 'S' ? 'S' : 'N')) . ' AND ';
						}

						$where .= '(denominacion LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR cuenta LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Impuesto':
				if (Usuario::logueado()->puede('listar/abm/impuestos/')) {
					try {
						$tipo = Funciones::get('tipo');

						$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= (empty($tipo) ? '' : 'tipo = ' . Datos::objectToDB($tipo) . ' AND ');
						$where .= '(nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR cod_impuesto LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Indicador':
				if (Usuario::logueado()->puede('listar/sistema/indicadores/')) {
					try {
						$where .= '(cod_indicador LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'LineaProducto':
				if (Usuario::logueado()->puede('listar/abm/lineas_productos/')) {
					try {
						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= '(cod_linea_nro LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_linea LIKE ' . Datos::objectToDB('%' . $key . '%') . ');';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Localidad':
				if (Usuario::logueado()->puede('listar/abm/regiones/localidades/')) {
					try {
						$pais = Funciones::get('idPais');
						$provincia = Funciones::get('idProvincia');
						if ($pais != '' && $provincia != '') {
							$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
							$where .= '((cod_localidad_nro LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
							$where .= 'OR denom_localidad LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
							$where .= 'AND (cod_pais = ' . Datos::objectToDB($pais) . ')' . ' ';
							$where .= 'AND (cod_provincia = ' . Datos::objectToDB($provincia) . ')); ';
						} else {
							$where .= $WHEREFALSO;
						}
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Marca':
				if (Usuario::logueado()->puede('listar/abm/marca/')) {
					try {
						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= '(cod_marca LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_marca LIKE ' . Datos::objectToDB('%' . $key . '%') . ');';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Material':
				if (Usuario::logueado()->puede('listar/abm/material/')) {
					try {
						$idProveedor = Funciones::get('idProveedor');

						$fields = 'cod_material, denom_material, anulado' . ($idProveedor ? ', cod_proveedor' : '');

						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= (empty($idProveedor) || $idProveedor == 'undefined' ? '' : 'cod_proveedor = ' . Datos::objectToDB($idProveedor) . ' AND ');
						$where .= '(cod_material LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_material LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$where .= 'GROUP BY cod_material, denom_material, anulado' . ($idProveedor ? ', cod_proveedor' : '') . ';';
						$objs = Factory::getInstance()->getArrayFromView('proveedores_materias_primas_v', $where, $limit, $fields);

						foreach ($objs as $obj){
							$array[] = array('id' => $obj['cod_material'], 'nombre' => $obj['denom_material']);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'MotivoAusentismo':
				if (Usuario::logueado()->puede('listar/abm/motivos_ausentismo/')) {
					try {
						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= '(id LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Motivo':
				if (Usuario::logueado()->puede('listar/abm/motivo/')) {
					try {
						$tipo = Funciones::get('tipo');

						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$tipo && $where .= 'tipo_motivo = ' . Datos::objectToDB($tipo) . ' AND ';
						$where .= '(cod_motivo LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre_motivo LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR descripcion LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'OrdenDeCompra':
				if (Usuario::logueado()->puede('listar/produccion/compras/orden_de_compra/')) {
					try {
						$proveedor = Funciones::get('idProveedor');
						$where = '';
						if ($proveedor != '')
							$where .= 'cod_proveedor = ' . Datos::objectToDB($proveedor) . ' AND ';

						$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= 'es_hexagono = ' . Datos::objectToDB('N') . ' AND ';
						$where .= 'cod_orden_de_compra LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= ' ORDER BY cod_orden_de_compra DESC; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->fechaAlta);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'OrdenDeFabricacion':
				if (Usuario::logueado()->puede('listar/produccion/gestion_produccion/ordenes_produccion/')) {
					try {
                        $lote = Funciones::get('idLoteDeProduccion');
                        $where = '';
                        if ($lote != '') {
                            $where .= 'nro_plan = ' . Datos::objectToDB($lote) . ' AND ';
                        }

						$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
                        $where .= '(nro_orden_fabricacion LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
                        $where .= 'OR cod_articulo LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
                        $where .= 'OR cod_color_articulo LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$where .= ' ORDER BY nro_orden_fabricacion DESC; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => '[' . $obj->idArticulo . '-' . $obj->idColorPorArticulo . ']');
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'OrdenDePago':
				if (Usuario::logueado()->puede('listar/administracion/tesoreria/egresos/orden_de_pago/')) {
					try {
						$proveedor = Funciones::get('idProveedor');
						$where = '';
						if ($proveedor != '')
							$where .= '(cod_proveedor = ' . Datos::objectToDB($proveedor) . ') AND ';

						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
						$where .= '(empresa = ' . Datos::objectToDB(Funciones::session('empresa')) . ') AND ';
						$where .= '(nro_orden_de_pago LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR fecha_documento LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$where .= ' ORDER BY nro_orden_de_pago DESC; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->numero, 'nombre' => $obj->fecha);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Pais':
				if (Usuario::logueado()->puede('listar/abm/regiones/paises/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
						$where .= '(cod_pais LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_pais LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
            case 'Patron':
                if (true /*Usuario::logueado()->puede('listar/abm/vendedores/') TODO no tiene una funcionalidad asociada xq esto es para la ficha técnica */) {
                    try {
                        $articulo = Funciones::get('idArticulo');
                        $color = Funciones::get('idColorPorArticulo');
                        $where .= '(cod_articulo = ' . Datos::objectToDB($articulo) . ') AND ';
                        $where .= '(cod_color_articulo = ' . Datos::objectToDB($color) . ') AND ';
                        $where .= '(version LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ); ';
                        $objs = Factory::getInstance()->getListObject('Patron', $where, $limit);
                        foreach ($objs as $obj){
                            $array[] = array('id' => $obj->version);
                        }
                    } catch (Exception $ex) {
                        return false;
                    }
                    return $array;
                }
                break;
			case 'Pedido':
				if (Usuario::logueado()->puede('listar/comercial/pedidos/nota_de_pedido/')) {
					try {
						$aprobado = Funciones::get('aprobado');
						$cliente = Funciones::get('idCliente');
						$whereEspecial = '';
						if ($aprobado != ''){
							$whereEspecial .= '(aprobado = ' . Datos::objectToDB($aprobado) . ') AND ';
						}
						if ($cliente != ''){
							$whereEspecial .= '(cod_cliente = ' . Datos::objectToDB($cliente) . ') AND ';
						}
						if (Usuario::logueado()->esCliente()){
							$whereEspecial .= '(cod_cliente = ' . Datos::objectToDB(Usuario::logueado()->contacto->cliente->id) . ') AND ';
						} elseif (Usuario::logueado()->esVendedor()) {
							$whereEspecial .= '(cod_vendedor = ' . Datos::objectToDB(Usuario::logueado()->getCodigoPersonal()) . ') AND ';
						}
						$where .= $whereEspecial;
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
						$where .= '(nro_pedido LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR fecha_alta LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$where .= 'ORDER BY fecha_alta ASC; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->numero, 'nombre' => Funciones::formatearFecha($obj->fechaAlta, 'd/m/Y') . ' (' . $obj->cliente->razonSocial . ')');
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'PersonaGasto':
				if (Usuario::logueado()->puede('listar/abm/persona_gasto/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
						$where .= '(id LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Personal':
				if (Usuario::logueado()->puede('listar/abm/personal/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
						$where .= '(cod_personal LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR legajo_nro LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR apellido LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombres LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->idPersonal, 'nombre' => $obj->nombreApellido);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
            case 'LoteDeProduccion':
                if (Usuario::logueado()->puede('listar/produccion/gestion_produccion/lotes_produccion/')) {
                    try {
                        $where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
                        $where .= '(nro_plan LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
                        $where .= 'OR denom_plan LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
                        $where .= ' ORDER BY nro_plan DESC; ';
                        $objs = Factory::getInstance()->getListObject($name, $where, $limit);
                        foreach ($objs as $obj){
                            $array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
                        }
                    } catch (Exception $ex) {
                        return false;
                    }
                    return $array;
                }
                break;
			case 'Prestamo':
				if (Usuario::logueado()->puede('listar/administracion/cobranzas/ingresos/prestamo/')) {
					try {
						$where = '';

						$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= 'empresa = ' . Datos::objectToDB(Funciones::session('empresa')) . ' AND ';
						$where .= '(nro_prestamo LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR fecha_documento LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$where .= ' ORDER BY nro_prestamo DESC; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->numero, 'nombre' => $obj->fecha);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Presupuesto':
				if (Usuario::logueado()->puede('listar/produccion/compras/presupuesto/')) {
					try {
						$idProveedor = Funciones::get('idProveedor');

						$where = 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= 'cod_proveedor = ' . Datos::objectToDB($idProveedor) . ' AND ';
						$where .= '(cod_presupuesto LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR fecha_alta LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$where .= ' ORDER BY cod_presupuesto DESC; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->fechaAlta);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Proveedor':
				if (Usuario::logueado()->puede('listar/abm/proveedores/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
						$where .= '(cod_prov LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR razon_social LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_fantasia LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->razonSocial);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'ProveedorTodos':
				if (Usuario::logueado()->puede('listar/abm/proveedores/')) {
					try {
						$where .= '((anulado = ' . Datos::objectToDB('N') . ' AND autorizado = ' . Datos::objectToDB('S') . ') OR (anulado = ' . Datos::objectToDB('S') . ' AND autorizado = ' . Datos::objectToDB('N') . ')) AND (cod_prov LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR razon_social LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_fantasia LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject('Proveedor', $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->razonSocial);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Provincia':
				if (Usuario::logueado()->puede('listar/abm/regiones/provincias/')) {
					try {
						$pais = Funciones::get('idPais');
						if ($pais != '') {
							$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
							$where .= '((cod_provincia LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
							$where .= 'OR denom_provincia LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
							$where .= 'AND (cod_pais = ' . Datos::objectToDB($pais) . ')); ';
						} else {
							$where .= $WHEREFALSO;
						}
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'RangoTalle':
				if (Usuario::logueado()->puede('listar/abm/rango_talle/')) {
					try {
						$where .= 'cod_rango_nro LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_rango LIKE ' . Datos::objectToDB('%' . $key . '%') . '; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Recibo':
				if (Usuario::logueado()->puede('listar/administracion/cobranzas/ingresos/recibos/')) {
					try {
						$cliente = Funciones::get('idCliente');
						$where = '';
						if ($cliente != '')
							$where .= '(cod_cliente = ' . Datos::objectToDB($cliente) . ') AND ';

						$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= 'empresa = ' . Datos::objectToDB(Funciones::session('empresa')) . ' AND ';
						$where .= '(nro_recibo LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR fecha_documento LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$where .= ' ORDER BY nro_recibo DESC; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->numero, 'nombre' => $obj->fecha);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'RemitoProveedor':
				if (Usuario::logueado()->puede('listar/administracion/tesoreria/documentos_proveedor/documento_proveedor/remitos/')) {
					$idProveedor = Funciones::get('idProveedor');
					$esHexagono = Funciones::get('esHexagono') == 'N';
					try {
						$where .= '(nro_remito LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR cod_remito_proveedor LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR fecha_recepcion LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$where .= 'AND cod_proveedor = ' . Datos::objectToDB($idProveedor) . ' ';
						$where .= ($esHexagono ? 'AND es_hexagono = ' . Datos::objectToDB('N') . ' ' : '');
						$where .= ' ORDER BY cod_remito_proveedor DESC; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => Funciones::padLeft($obj->sucursal, 4, 0) . '-' . Funciones::padLeft($obj->numero, 8, 0) . ' (' . $obj->fechaRecepcion . ')');
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Rol':
				if (Usuario::logueado()->puede('listar/sistema/roles/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
						$where .= '(cod_rol LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'RetiroSocio':
				if (Usuario::logueado()->puede('listar/administracion/tesoreria/egresos/retiro_socios/')) {
					try {
						$where .= 'empresa = ' . Datos::objectToDB(Funciones::session('empresa')) . ' AND ';
						$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= 'nro_retiro_socio LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'ORDER BY fecha_documento DESC; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->numero, 'nombre' => $obj->fechaAlta);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Rubro':
				if (Usuario::logueado()->puede('listar/abm/rubros/')) {
					try {
						$where .= 'cod_grupo LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_grupo LIKE ' . Datos::objectToDB('%' . $key . '%') . '; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'RubroIva':
				if (Usuario::logueado()->puede('listar/abm/rubros_iva/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
						$where .= '(cod_rubro_iva LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'RutaProduccion':
				if (Usuario::logueado()->puede('listar/abm/rutas_produccion/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND ';
						$where .= '(cod_ruta LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_ruta LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'SeccionProduccion':
				if (Usuario::logueado()->puede('listar/abm/seccion_produccion/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND (cod_seccion LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_seccion LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Socio':
				if (Usuario::logueado()->puede('listar/abm/socios/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND (cod_socio LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Sucursal':
				if (Usuario::logueado()->puede('listar/abm/sucursales/')) {
					try {
						$cliente = Funciones::get('idCliente');
						if ($cliente != ''){
							$where .= '(cod_suc LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
							$where .= 'OR denom_sucursal LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
							$where .= 'AND (cod_cli = ' . Datos::objectToDB($cliente) . ') ';
							$where .= 'AND (anulado = ' . Datos::objectToDB('N') . '); ';
						} else {
							$where .= $WHEREFALSO;
						}
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Temporada':
				if (Usuario::logueado()->puede('listar/abm/temporadas/')) {
					try {
						$where .= 'tipo_tempo = ' . Datos::objectToDB('P') .  ' ';
						$where .= 'AND (cod_tempo LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_tempo LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$where .= ' ORDER BY cod_tempo DESC; ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'TipoFactura':
				if (Usuario::logueado()->puede('listar/abm/tipo_factura/')) {
					try {
						$where .= 'anulado = ' . Datos::objectToDB('N') .  ' ';
						$where .= 'AND (cod_tipo_factura LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'TipoNotificacion':
				if (Usuario::logueado()->puede('listar/sistema/notificaciones/tipos_de_notificaciones/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') ';
						$where .= 'AND (cod_tipo_notificacion LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'TipoPeriodoFiscal':
				if (Usuario::logueado()->puede('listar/administracion/contabilidad/periodos_fiscales/tipos/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') ';
						$where .= 'AND (cod_tipo_periodo LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'TipoProductoStock':
				if (Usuario::logueado()->puede('listar/abm/articulos/')) {
					try {
						$where .= '(id_tipo_producto_stock_nro LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_tipo_producto LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'TipoProveedor':
				if (Usuario::logueado()->puede('listar/abm/tipos_proveedores/')) {
					try {
						$where .= '(cod_tipo_proveedor LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_tipo_proveedor LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'TipoRetencion':
				if (true) {
					try {
						$where .= '(cod_tipo_retencion LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Transporte':
				if (Usuario::logueado()->puede('listar/abm/transportes/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') ';
						$where .= 'AND (cod_transporte LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_transporte LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'UnidadDeMedida':
				if (Usuario::logueado()->puede('listar/sistema/usuarios/')) {
					try {
						$where .= 'cod_unidad LIKE ' . Datos::objectToDB('%' . $key . '%') . ' OR ';
						$where .= 'denom_unidad LIKE ' . Datos::objectToDB('%' . $key . '%') . '; ';

						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Usuario':
				if (Usuario::logueado()->puede('listar/sistema/usuarios/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') AND (cod_usuario LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'UsuarioPorAreaEmpresa':
				if (Usuario::logueado()->puede('listar/abm/areas_empresa/')) {
					try {
						$idAreaEmpresa = Funciones::get('idAreaEmpresa');
						$where .= '(id_area_empresa = ' . Datos::objectToDB($idAreaEmpresa) . ' AND ';
						$where .= 'cod_usuario LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'UsuarioPorAlmacen':
				if (Usuario::logueado()->puede('listar/abm/almacenes/')) {
					try {
						$idUsuario = Funciones::get('idUsuario');
						$where .= '(cod_usuario = ' . Datos::objectToDB($idUsuario) . ' AND ';
						$where .= 'cod_almacen  LIKE ' . Datos::objectToDB('%' . $key . '%') . '); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->idAlmacen, 'nombre' => $obj->almacen->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Vendedor':
				if (Usuario::logueado()->puede('listar/abm/vendedores/')) {
					try {
						$whereEspecial = '';
						if (Usuario::logueado()->esVendedor()) {
							$whereEspecial = '(cod_operador = ' . Datos::objectToDB(Usuario::logueado()->getCodigoPersonal()) . ') AND ';
						} elseif (Usuario::logueado()->esCliente()) {
							$whereEspecial = '(cod_operador = ' . Datos::objectToDB(Usuario::logueado()->contacto->cliente->vendedor->id) . ') AND ';
						}
						$where .= $whereEspecial;
						$where .= '(tipo_operador = \'V\') AND ';
						$where .= '((cod_operador LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR apellido LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombres LIKE ' . Datos::objectToDB('%' . $key . '%') . ')); ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombreApellido);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'VentaChequesTemporal':
				if (Usuario::logueado()->puede('listar/administracion/tesoreria/venta_cheques/')) {
					try {
						$idCuentaBancaria = Funciones::get('idCuentaBancaria');
						$fechaDesde = Funciones::get('fechaDesde');
						$fechaHasta = Funciones::get('fechaHasta');

						$where = (empty($fechaDesde) && empty($fechaHasta) ? '' : Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha') . 'AND ');
						$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= 'confirmado = ' . Datos::objectToDB('N') . ' AND ';
						$where .= 'cod_venta_cheques_temporal LIKE ' . Datos::objectToDB('%' . $key . '%') . ' OR ';
						$where .= (empty($idCuentaBancaria) ? '' : 'AND cod_cuenta_bancaria = ' . Datos::objectToDB($idCuentaBancaria) . ' ');
						$where = trim($where, ' OR ');
						$where .= ';';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->fecha);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'Zona':
				if (Usuario::logueado()->puede('listar/abm/zonas/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') ';
						$where .= 'AND (cod_zona LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR nombre LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			case 'ZonaTransporte':
				if (Usuario::logueado()->puede('listar/abm/zonas_transporte/')) {
					try {
						$where .= '(anulado = ' . Datos::objectToDB('N') . ') ';
						$where .= 'AND (cod_zona LIKE ' . Datos::objectToDB('%' . $key . '%') . ' ';
						$where .= 'OR denom_zona LIKE ' . Datos::objectToDB('%' . $key . '%') . ') ';
						$objs = Factory::getInstance()->getListObject($name, $where, $limit);
						foreach ($objs as $obj){
							$array[] = array('id' => $obj->id, 'nombre' => $obj->nombre);
						}
					} catch (Exception $ex) {
						return false;
					}
					return $array;
				}
				break;
			default:
				Html::jsonEmpty();
				break;
		}
		return false;
	}
}

?>