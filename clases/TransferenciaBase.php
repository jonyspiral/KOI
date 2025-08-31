<?php

/**
 * @property ImportePorOperacion 	$importePorOperacion
 * @property Usuario				$usuario
 * @property Array     				$importesSinValidar
 * @property string					$entradaSalida
 * @property Retencion[]			$arrayRetenciones
 */

abstract class TransferenciaBase extends Base {
	const		_primaryKey = '["numero", "empresa"]';
	protected	$_entradaSalida;

	public		$numero;
	public		$empresa;
	public		$idImportePorOperacion;
	protected	$_importePorOperacion;
	public		$importeTotal;
	public		$observaciones;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$anulado;
	protected	$_arrayRetenciones;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	//Campos extra
	public		$datosSinValidar;
	public		$importesSinValidar = array('E' => array(), 'S' => array());
	private		$importesSinValidarBorrar; //Cuando se haga el editar, se borra
	public		$validarEfectivoParcial = false;

	protected static	$ids = array();
	protected static	$cajas = array();
	protected static	$transaction;
	protected static	$mutex;

	abstract public function getCodigoPermiso();
	abstract public function getTipoTransferenciaBase();
	abstract public function calcularNuevoImporteCaja($importeViejo, $importe, $delete = false);
	abstract public function validarCantidadPermitidaEfectivo($cantidad);
	abstract public function validarCantidadPermitidaCheque($cantidad);
	abstract public function validarCantidadPermitidaTransferenciaBancaria($cantidad);
	abstract public function validarCantidadPermitidaRetencionEfectuada($cantidad);
	abstract public function validarCantidadPermitidaRetencionSufrida($cantidad);

	public function getTextoDe($conTipo = false) {
		return ($this->entradaSalida == 'S') ? $this->getTextoCaja($conTipo) : '';
	}
	public function getTextoPara($conTipo = false) {
		return ($this->entradaSalida == 'E') ? $this->getTextoCaja($conTipo) : '';
	}
	protected function getTextoCaja($conTipo = false) {
		return ($conTipo ? 'Caja: ' : '') . $this->importePorOperacion->caja->getIdNombre();
	}

	protected function mutex($unlock = false) {
		if (!isset(self::$mutex)) {
			self::$mutex = new Mutex('TransferenciaBase');
			self::$mutex->lock();
		}
		$unlock && self::$mutex->unlock();
	}

	/**
	 * @param bool $commit
	 *
	 * @return Factory
	 */
	protected function transaction($commit = false) {
		if (!isset(self::$transaction) && !Factory::getInstance()->transaction()) {
			Factory::getInstance()->beginTransaction();
			self::$transaction = true;
		}
		if ($commit && self::$transaction) {
			Factory::getInstance()->commitTransaction();
			self::$transaction = null;
			self::$ids = array();
			self::$cajas = array();
		}
		return Factory::getInstance();
	}

	public function expand(){
		try {
			Factory::getInstance()->getPermisoPorUsuarioPorCaja($this->importePorOperacion->caja->id, Usuario::logueado()->id, PermisosUsuarioPorCaja::verCaja);
		} catch(Exception $ex) {
			throw new FactoryExceptionCustomException('No tiene permiso para visualizar este documento.');
		}
		foreach($this->importePorOperacion->detalle as $item) {
			/** @var ImportePorOperacionItem $item */
			$item->importe->expand();
		}
		return parent::expand();
	}

	protected function beforeSave(){
		return true;
	}
	protected function beforeUpdate(){
		return true;
	}
	protected function beforeInsert(){
		return true;
	}
	protected function beforeDelete(){
		return true;
	}
	protected function beforeCommit() {
		foreach($this->importesSinValidar[$this->entradaSalida][TiposImporte::cheque] as $cheque) {
			/** @var Cheque $cheque */
			$where = 'librador_cuit = ' . Datos::objectToDB($cheque->libradorCuit) . ' AND ';
			$where .= 'numero = ' . Datos::objectToDB($cheque->numero) . ' AND ';
			$where .= 'cod_banco = ' . Datos::objectToDB($cheque->banco->idBanco) . ' AND ';
			$where .= 'anulado = ' . Datos::objectToDB('N') . ' AND ';
			$where .= 'concluido = ' . Datos::objectToDB('N') . ' AND ';
			$where .= 'cod_rechazo_cheque IS NULL';
			$cheques = Factory::getInstance()->getListObject('Cheque', $where);

			if(count($cheques) > 1){
				throw new FactoryExceptionCustomException('El cheque ingresado para el librador con cuit "' . $cheque->libradorCuit . '", banco "' . $cheque->banco->nombre . '" y numero "' . $cheque->numero . '" ya existe en el sistema');
			}
		}

		return true;
	}
	protected function beforeCommitDelete() {
		return true;
	}

	public function guardar() {
		$this->mutex();
		try {
			$this->transaction();
			if ($this->modo == Modos::insert || $this->modo == Modos::update) {
				if ($this->modo == Modos::update) {
					$this->borrar(false);
					$this->importesSinValidar = $this->importesSinValidarBorrar;
				}
				$this->validarNuevo();
				$this->beforeSave();
				($this->modo == Modos::update) && $this->beforeUpdate();
				($this->modo == Modos::insert) && $this->beforeInsert();
				$this->guardarNuevo();
				$this->beforeCommit();
				$this->transaction(true);
			} else {
				throw new FactoryExceptionCustomException('No se puede guardar un objeto que no esté en modo insert o update');
			}
			$this->mutex(true);
		} catch (Exception $ex) {
			$this->mutex(true);
			throw $ex;
		}

		return $this;
	}

	protected function validarSiHayChequesConcluidos(){
		foreach($this->importesSinValidar[$this->entradaSalida]['C'] as $cheque){
			/** Cheque $cheque */
			if($cheque->concluido == 'S'){
				throw new FactoryExceptionCustomException('El cheque número ' . $cheque->numero . ' está concluido.');
			}
		}
	}

	protected function validarNuevo() {
		$arrayAux = array('E' => array(), 'S' => array());
		$eos = $this->getEntradaSalida();
		//Valido la estructura de cada importe que estoy recibiendo, y voy llenando un array con los objetos ya llenos que me devuelve
		foreach($this->importesSinValidar[$eos] as $tipoImporte => $importesEos) {
			$arrayAux[$eos][$tipoImporte] = array();
			foreach($importesEos as $obj) {
				$obj = (array)$obj;
				$obj['entradaSalida'] = $eos;
				$arrayAux[$eos][$tipoImporte][] = $this->llenarValidar($tipoImporte, $obj);
			}
		}
		$this->importesSinValidar = $arrayAux;

		//Valido que al menos me mande 1 importe
		if(!(count($this->importesSinValidar['E']) > 0) && !(count($this->importesSinValidar['S']) > 0)) {
			throw new FactoryExceptionCustomException('No puede realizar una operación sin importes');
		}

		//Valido las cantidades de importes que estoy recibiendo. Por ejemplo, sólo puedo recibir 1 efectivo, en un recibo.
		$this->validarCantidadPermitidaEfectivo(count($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::efectivo]));
		$this->validarCantidadPermitidaCheque(count($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::cheque]));
		$this->validarCantidadPermitidaTransferenciaBancaria(count($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::transferenciaBancariaImporte]));
		$this->validarCantidadPermitidaRetencionEfectuada(count($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::retencionEfectuada]));
		$this->validarCantidadPermitidaRetencionSufrida(count($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::retencionSufrida]));

		//Si es una transferencia doble, no se valida la parte de entrada, ya que el usuario MUY probablemente no tenga permisos sobre la caja de destino
		if ($this->esOperacionSalida() || !($this instanceof TransferenciaDoble)) {
			//Valido si el usuario puede hacer la operación en esta caja
			$caja = Factory::getInstance()->getCaja($this->datosSinValidar['idCaja_' . $this->getEntradaSalida()]);
			if(!($caja->usuarioPuede($this->datosSinValidar['usuario']->id, $this->getCodigoPermiso()))){
				throw new FactoryExceptionCustomException('No tiene permiso para realizar la operación ' . $this->getCodigoPermiso() . ' sobre la caja ' . $caja->nombre);
			}
		}

		//Lleno los datos que tienen en común todas las TransferenciaBase y que luego no se van
		if ($this->modo == Modos::insert) {
			$this->usuario = $this->datosSinValidar['usuario'];
		}
		$this->observaciones = $this->datosSinValidar['observaciones'];

		if ($this->esOperacionSalida()) {
			$this->validarImportesExistentes(Factory::getInstance()->getCaja($this->datosSinValidar['idCaja_' . $this->getEntradaSalida()]));
		}
	}

	private function llenarValidar($tipoImporte, $obj) {
		switch($tipoImporte) {
			case TiposImporte::efectivo:
				return Efectivo::validar($obj);
				break;
			case TiposImporte::cheque:
				return Cheque::validar($obj);
				break;
			case TiposImporte::transferenciaBancariaImporte:
				return TransferenciaBancariaImporte::validar($obj);
				break;
			case TiposImporte::retencionEfectuada:
				return RetencionEfectuada::validar($obj);
				break;
			case TiposImporte::retencionSufrida:
				return RetencionSufrida::validar($obj);
				break;
			default:
				throw new FactoryExceptionCustomException('No se reconoce el importe "' . $tipoImporte . '"');
				break;
		}
	}

	protected function guardarNuevo() {
		$impPorOpC = Factory::getInstance()->getImportePorOperacion();
		$impPorOpC->idImportePorOperacion = $this->getIds($impPorOpC);
		$impPorOpC->tipoOperacion = $this->getTipoTransferenciaBase();
		$impPorOpC->caja = $this->getCaja($this->datosSinValidar['idCaja_' . $this->getEntradaSalida()]);
		//$impPorOpC->fechaCaja = ''; REVISAR! ES IMPORTANTE. TIENE QUE IR EL PRÓXIMO DÍA HÁBIL LUEGO DEL ÚLTIMO CIERRE. Y si el cierre fue hace 1 semana... LUEGO DE HOY(?) Pensar
		$this->transaction()->persistir($impPorOpC);

		if (count($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::transferenciaBancariaImporte]) > 0) {
			foreach($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::transferenciaBancariaImporte] as $tbImp) {
				/** @var $tbImp TransferenciaBancariaImporte */
				$tEfectivo = Factory::getInstance()->getEfectivo();
				$tEfectivo->id = $this->getIds($tEfectivo);
				$tEfectivo->empresa = $this->empresa;
				$tEfectivo->importe = $tbImp->importe;
				$this->transaction()->persistir($tEfectivo);

				$tIxOC = Factory::getInstance()->getImportePorOperacion();
				$tIxOC->idImportePorOperacion = $this->getIds($tIxOC);
				$tIxOC->tipoOperacion = TiposTransferenciaBase::transferenciaBancariaOperacion;
				$tIxOC->caja = $this->getCaja($tbImp->cuentaBancaria->caja->id);
				//$tIxOC->fechaCaja = ''; REVISAR! ES IMPORTANTE. TIENE QUE IR EL PRÓXIMO DÍA HÁBIL LUEGO DEL ÚLTIMO CIERRE. Y si el cierre fue hace 1 semana... LUEGO DE HOY(?) Pensar
				$this->transaction()->persistir($tIxOC);

				$tIxOD = Factory::getInstance()->getImportePorOperacionItem();
				$tIxOD->importePorOperacion = $tIxOC;
				$tIxOD->tipoImporte = TiposImporte::efectivo;
				$tIxOD->idImporte = $tEfectivo->id;
				$this->transaction()->persistir($tIxOD);

				$tIxOC->caja->importeEfectivo = $this->calcularNuevoImporteCaja($tIxOC->caja->importeEfectivo, $tEfectivo->importe);
				$this->setCaja($tIxOC->caja);

				$tOp = Factory::getInstance()->getTransferenciaBancariaOperacion();
				$tOp->empresa = $this->empresa;
				$tOp->numero = $this->getIds($tOp);
				$tOp->importePorOperacion = $tIxOC;
				//$tOp-> COMPLETAR EL RESTO DE LOS CAMPOS
				if($this->esOperacionSalida()){
					$tOp->numeroTransferencia = $tbImp->numeroTransferencia;
				}
				$tOp->observaciones = $this->datosSinValidar['observaciones'];
				$tOp->fechaTransferencia = $tbImp->fechaTransferencia;
				$tOp->usuario = $this->usuario;
				$tOp->cuentaBancaria = $tbImp->cuentaBancaria;
				$tOp->entradaSalida = $tbImp->entradaSalida;
				$tOp->importeTotal = $tbImp->importe;
				$tOp->haciaDesde = $this->getHaciaDesdeTransferenciaBancariaOperacion();
				$this->transaction()->persistir($tOp);

				$tbImp->id = $this->getIds($tbImp);
				$tbImp->empresa = $this->empresa;
				$tbImp->numeroTransferenciaBancariaOperacion = $tOp->numero;
				$this->transaction()->persistir($tbImp);

				$impPorOpD = Factory::getInstance()->getImportePorOperacionItem();
				$impPorOpD->importePorOperacion = $impPorOpC;
				$impPorOpD->tipoImporte = TiposImporte::transferenciaBancariaImporte;
				$impPorOpD->idImporte = $tbImp->id;
				$this->transaction()->persistir($impPorOpD);
			}
		}

		if (count($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::efectivo]) > 0) {
			foreach($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::efectivo] as $efImp) {
				/** @var $efImp Efectivo */
				$efImp->id = $this->getIds($efImp);
				$efImp->empresa = $this->empresa;
				$this->transaction()->persistir($efImp);

				$impPorOpD = Factory::getInstance()->getImportePorOperacionItem();
				$impPorOpD->importePorOperacion = $impPorOpC;
				$impPorOpD->tipoImporte = TiposImporte::efectivo;
				$impPorOpD->idImporte = $efImp->id;
				$this->transaction()->persistir($impPorOpD);

				$impPorOpC->caja->importeEfectivo = $this->calcularNuevoImporteCaja($impPorOpC->caja->importeEfectivo, $efImp->importe);
				$this->setCaja($impPorOpC->caja);
			}
		}

		if (count($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::cheque]) > 0) {
			foreach($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::cheque] as $chImp) {
				/** @var $chImp Cheque */
				if ($chImp->modo == Modos::insert) {
					$chImp->id = $this->getIds($chImp);
				}

				$chImp->empresa = $this->empresa;
				$chImp->cajaActual = $impPorOpC->caja;
				$this->transaction()->persistir($chImp);

				$impPorOpD = Factory::getInstance()->getImportePorOperacionItem();
				$impPorOpD->importePorOperacion = $impPorOpC;
				$impPorOpD->tipoImporte = TiposImporte::cheque;
				$impPorOpD->idImporte = $chImp->id;
				$this->transaction()->persistir($impPorOpD);
			}
		}

		if (count($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::retencionEfectuada]) > 0) {
			foreach($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::retencionEfectuada] as $reImp) {
				/** @var $reImp RetencionEfectuada */
				$reImp->id = $this->getIds($reImp);
				$reImp->empresa = $this->empresa;
				$this->transaction()->persistir($reImp);

				$impPorOpD = Factory::getInstance()->getImportePorOperacionItem();
				$impPorOpD->importePorOperacion = $impPorOpC;
				$impPorOpD->tipoImporte = TiposImporte::retencionEfectuada;
				$impPorOpD->idImporte = $reImp->id;
				$this->transaction()->persistir($impPorOpD);
			}
		}

		if (count($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::retencionSufrida]) > 0) {
			foreach($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::retencionSufrida] as $reImp) {
				/** @var $reImp RetencionSufrida */
				$reImp->id = $this->getIds($reImp);
				$reImp->empresa = $this->empresa;
				$this->transaction()->persistir($reImp);

				$impPorOpD = Factory::getInstance()->getImportePorOperacionItem();
				$impPorOpD->importePorOperacion = $impPorOpC;
				$impPorOpD->tipoImporte = TiposImporte::retencionSufrida;
				$impPorOpD->idImporte = $reImp->id;
				$this->transaction()->persistir($impPorOpD);
			}
		}

		$this->numero = $this->getIds($this);
		$this->importeTotal = $this->calcularImporteTotal();
		$this->importePorOperacion = $impPorOpC;
		$this->transaction()->persistir(Factory::getInstance()->marcarParaInsertar($this));

		foreach (self::$cajas as $caja) {
			$this->transaction()->persistir($caja);
		}
	}

	protected function validarBorrar() {
		parent::validarBorrar();
		//Valido si el usuario puede hacer la operación sobre la caja
		if (!($this->importePorOperacion->caja->usuarioPuede(Usuario::logueado()->id, $this->getCodigoPermiso()))) {
			throw new FactoryExceptionCustomException('No tiene permiso para realizar la operación ' . $this->getCodigoPermiso() . ' sobre la caja ' . $this->importePorOperacion->caja->nombre);
		}

		$this->importesSinValidarBorrar = $this->importesSinValidar;
		if ($this->esOperacionEntrada()) {
			$this->importesSinValidar = array();
			foreach ($this->importePorOperacion->detalle as $item) {
				$this->importesSinValidar[$this->getEntradaSalida()][$item->tipoImporte][] = $item->importe;
			}
			$this->validarImportesExistentes($this->importePorOperacion->caja);
		}
	}

	protected function validarImportesExistentes($caja) {
		$acumulador = array();
		foreach ($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::efectivo] as $sinValidar) {
			$acumulador[TiposImporte::efectivo] += $sinValidar->importe;
		}
		foreach ($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::cheque] as $sinValidar) {
			$acumulador[TiposImporte::cheque][] = $sinValidar->id;
		}
		foreach ($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::transferenciaBancariaImporte] as $sinValidar) {
			$acumulador[TiposImporte::transferenciaBancariaImporte][$sinValidar->cuentaBancaria->caja->id] += $sinValidar->importe;
		}
		/* No es necesario validar existencia de retenciones (ni sufridas ni efectuadas)
		foreach ($this->importesSinValidar[$this->getEntradaSalida()][TiposImporte::retencion] as $sinValidar) {
			$acumulador[TiposImporte::retencion] += $sinValidar->importe;
		}
		*/

		Efectivo::validarExistencia($caja, $acumulador[TiposImporte::efectivo], $this->validarEfectivoParcial);
		Cheque::validarExistencia($caja, $acumulador[TiposImporte::cheque]);
		TransferenciaBancariaImporte::validarExistencia($acumulador[TiposImporte::transferenciaBancariaImporte]);
		//Retencion::validarExistencia($caja, $acumulador[TiposImporte::retencion]); //No es necesario validar existencia de las retenciones
	}

	public function borrar($commit = true) {
		$this->validarBorrar();
		$this->mutex();
		try {
			$this->transaction();
			$this->beforeDelete();
			$this->borrarExistente();
			$this->beforeCommitDelete();
			$commit && $this->transaction(true);
			$this->mutex(true);
		} catch (Exception $ex) {
			$this->mutex(true);
			throw $ex;
		}

		return $this;
	}

	//Logica de borrado
	protected function borrarCheque(Cheque $cheque){
		$cheque->concluido = 'N';
		$cheque->esperandoEnBanco = null;
		$cheque->idProveedor = null;
		$cheque->proveedor = null;
		$cheque->cajaActual = $this->importePorOperacion->caja;
		$this->transaction()->persistir($cheque);
	}

	protected function borrarTransferenciaBancaria(TransferenciaBancariaImporte $transferenciaBancariaImporte){
		$caja = $this->getCaja($transferenciaBancariaImporte->cuentaBancaria->caja->id);
		$caja->importeEfectivo = $this->calcularNuevoImporteCaja($caja->importeEfectivo, $transferenciaBancariaImporte->importe, true);
		$this->setCaja($caja);
		foreach ($transferenciaBancariaImporte->transferenciaBancariaOperacion->importePorOperacion->detalle as $tItem) {
			Factory::getInstance()->marcarParaBorrar($tItem);
			$this->transaction()->persistir($tItem);
		}
		Factory::getInstance()->marcarParaBorrar($transferenciaBancariaImporte->transferenciaBancariaOperacion);
		$this->transaction()->persistir($transferenciaBancariaImporte->transferenciaBancariaOperacion);
	}

	protected function borrarEfectivo(Efectivo $efectivo){
		$caja = $this->getCaja($this->importePorOperacion->caja->id);
		$caja->importeEfectivo = $this->calcularNuevoImporteCaja($caja->importeEfectivo, $efectivo->importe, true);
		$this->setCaja($caja);
	}

	protected function borrarRetencionEfectuada(RetencionEfectuada $retencionEfectuada){
		Factory::getInstance()->marcarParaBorrar($retencionEfectuada);
		$this->transaction()->persistir($retencionEfectuada);
	}

	protected function borrarRetencionSufrida(RetencionSufrida $retencionSufrida){
		Factory::getInstance()->marcarParaBorrar($retencionSufrida);
		$this->transaction()->persistir($retencionSufrida);
	}

	protected function borrarExistente() {
		foreach ($this->importePorOperacion->detalle as $item) {
			if ($item->tipoImporte == TiposImporte::cheque) {
				$this->borrarCheque($item->importe);
			} elseif ($item->tipoImporte == TiposImporte::transferenciaBancariaImporte) {
				$this->borrarTransferenciaBancaria($item->importe);
			} elseif ($item->tipoImporte == TiposImporte::efectivo) {
				$this->borrarEfectivo($item->importe);
			} elseif ($item->tipoImporte == TiposImporte::retencionEfectuada) {
				$this->borrarRetencionEfectuada($item->importe);
			} elseif ($item->tipoImporte == TiposImporte::retencionSufrida) {
				$this->borrarRetencionSufrida($item->importe);
			}
			Factory::getInstance()->marcarParaBorrar($item);
			$this->transaction()->persistir($item);
		}
		Factory::getInstance()->marcarParaBorrar($this);
		$this->transaction()->persistir($this);

		foreach (self::$cajas as $caja) {
			$this->transaction()->persistir($caja);
		}
	}

	protected function calcularImporteTotal() {
		$t = 0;
		foreach($this->importesSinValidar[$this->getEntradaSalida()] as $imps) {
			foreach($imps as $imp) {
				/* @var $imp Importe */
				$t += $imp->importe;
			}
		}
		return $t;
	}

	protected function getIds($obj) {
		$clase = Funciones::getType($obj);
		if (!isset(self::$ids[$clase])) {
			self::$ids[$clase] = Factory::getInstance()->getNextId($obj) - 1;
		}
		self::$ids[$clase] = self::$ids[$clase] + 1;
		return self::$ids[$clase];
	}

	protected function getCaja($idCaja) {
		if (!isset(self::$cajas[$idCaja])) {
			self::$cajas[$idCaja] = Factory::getInstance()->getCaja($idCaja);
		}
		return self::$cajas[$idCaja];
	}

	protected function setCaja($caja) {
		self::$cajas[$caja->id] = $caja;
	}

	protected function esOperacionEntrada() {
		return $this->getEntradaSalida() == 'E';
	}

	protected function esOperacionSalida() {
		return $this->getEntradaSalida() == 'S';
	}

	/**
	 * Este método devuelve la fecha del documento (si es que tiene).
	 * De lo contrario devuelve la fechaAlta
	 *
	 * @return string
	 */
	public function fecha() {
		try {
			$this->checkProperty('fecha');
			/** @noinspection PhpUndefinedFieldInspection */
			return $this->fecha;
		} catch (Exception $ex) {
			if ($this instanceof TransferenciaDoble) {
				try {
					$this->cabecera->checkProperty('fecha');
					/** @noinspection PhpUndefinedFieldInspection */
					return $this->cabecera->fecha;
				} catch (Exception $ex) {
					return $this->cabecera->fechaAlta;
				}
			}
			return $this->fechaAlta;
		}
	}

	public function getEfectivo(){
		return $this->importePorOperacion->getEfectivo();
	}

	public function getCheques(){
		return $this->importePorOperacion->getCheques();
	}

	public function getTransferencias(){
		return $this->importePorOperacion->getTransferencias();
	}

	public function getRetencionesEfectuadas(){
		return $this->importePorOperacion->getRetencionesEfectuadas();
	}

	public function getRetencionesSufridas(){
		return $this->importePorOperacion->getRetencionesSufridas();
	}

	public function getArrayRetenciones() {
		if (!isset($this->_arrayRetenciones)){
			$retencionesSufridas = $this->getRetencionesSufridas();
			$retencionesEfectuadas = $this->getRetencionesEfectuadas();

			$retenciones = array_merge($retencionesSufridas, $retencionesEfectuadas);
			$array = array();
			foreach($retenciones as $retencion){
				$array[$retencion->idTipoRetencion] = $retencion->importe;
			}
			$this->_arrayRetenciones = $array;
		}
		return $this->_arrayRetenciones;
	}

	public static function getFromImportePorOperacion($tipoTransferencia, $idImportePorOperacion) {
		$obj = null;
		$clases = TiposTransferenciaBase::getConstants();
		foreach ($clases as $clase => $valor) {
			if ($tipoTransferencia == $valor) {
				$clase = ucfirst($clase);
				$list = Factory::getInstance()->getListObject($clase, 'cod_importe_operacion = ' . Datos::objectToDB($idImportePorOperacion));
				if (count($list) != 1) {
					$err = 'Ocurrió un error al intentar obtener la operación del tipo "' . $clase . '":';
					$err .= 'existen ' . count($list) . ' operaciones con el código de importe por operación Nº ' . $idImportePorOperacion;
					throw new FactoryExceptionCustomException($err);
				}
				$obj = $list[0];
				break;
			}
		}
		if (is_null($obj)) {
			throw new FactoryExceptionCustomException('Ocurrió un error al intentar obtener la operación del tipo Nº ' . $tipoTransferencia);
		}
		return $obj;
	}

	//GETS y SETS
	protected function getHaciaDesdeTransferenciaBancariaOperacion() {
		return '';
	}
	protected function getImportePorOperacion() {
		if (!isset($this->_importePorOperacion)){
			$this->_importePorOperacion = Factory::getInstance()->getImportePorOperacion($this->idImportePorOperacion);
		}
		return $this->_importePorOperacion;
	}
	protected function setImportePorOperacion($importeOperacion) {
		$this->_importePorOperacion = $importeOperacion;
		return $this;
	}
	protected function getEntradaSalida() {
		return $this->_entradaSalida;
	}
	protected function setEntradaSalida($entradaSalida) {
		$this->_entradaSalida = $entradaSalida;
		return $this;
	}
}

?>