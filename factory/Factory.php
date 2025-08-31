<?php
require_once __DIR__.'/Mapper.php';
require_once __DIR__.'/Config.php';
require_once __DIR__ .'/drivers/DbMysql.php';   

class Factory {
    private static $_factory = null;
    private $mapper = null;

    /* NUEVO: handler DB cuando usamos MySQL */
    private $db = null;

    public function __construct() {
        $this->mapper = new Mapper();

        // Inicializa DB si el driver es MySQL (KOI2 en Ubuntu)
        if (Config::DB_DRIVER === 'mysql') {
            $this->db = new DbMysql([
                'host'    => Config::mysql_host,
                'port'    => Config::mysql_port,
                'name'    => Config::mysql_db,
                'user'    => Config::mysql_user,
                'pass'    => Config::mysql_pass,
                'charset' => Config::mysql_charset,
                'timeout' => 5,
            ]);
        }
    }
	

    public static function getInstance(){
        if(!isset(self::$_factory)){
            self::$_factory = new Factory();
        }
        return self::$_factory;
    }

    /* NUEVO: expositor de DB (para módulos que quieran usar MySQL directo) */
    public function db() {
        if ($this->db) return $this->db;
        throw new FactoryException('DB no inicializada para el driver actual');
    }
	

	public	function marcarParaBorrar(&$obj) {
		try {
			$obj->modo = Modos::delete;
			return $obj;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function marcarParaModificar(&$obj) {
		try {
			$obj->modo = Modos::update;
			return $obj;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function marcarParaInsertar(&$obj) {
		try {
			$obj->modo = Modos::insert;
			return $obj;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function puedePersistir($existe, $modo) {
		if ($modo == Modos::insert && $existe) {
			Logger::addError('Registro existente');
			throw new FactoryExceptionRegistroExistente();
		} elseif (($modo == Modos::update || $modo == Modos::select) && !$existe) {
			Logger::addError('El registro no existe');
			throw new FactoryExceptionRegistroNoExistente();
		}
	}
	public	function persistir($obj) {
		try {
			// Elimino de cache todos los objetos de esta clase
			Cache::deleteAllByTag(Funciones::getType($obj));

			$method = 'set' . ucfirst(Funciones::getType($obj));
			if (!method_exists($this, $method)) {
				throw new Exception('No existe el m�todo ' . $method . ' en la clase "Factory".');
			}
			return $this->$method($obj);
		} catch (Exception $ex) {
			$this->rollbackTransaction();
			throw $ex;
		}
	}
	public function push($obj) {
		Datos::EjecutarSQLsinQuery($this->mapper->getQueryInstancia($obj, $obj->modo));
	}
	public	function getNextId($obj){
		try {
			$row = Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($obj, Modos::id));
			if (count($row) != 1)
				throw new FactoryException('No se encontr� el pr�ximo ID');
			return $row['computed'];
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getListObject($clase, $clausulaWhere = '1 = 1', $limit = 0){
		try {
			$list = array();
			$obj = new $clase;
			$list = $this->mapper->fillListObject(Datos::EjecutarSQL($this->mapper->getQueryInstanciaWhere($obj, Modos::select, $clausulaWhere, $limit), $clase), $list, $clase);

			return $list;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	/**/public	function getListObjectFromStoredProcedure($clase, $storedProcedureName, $parametros = ''){
		try {
			$list = array();
			//$obj = new $clase;
			$list = $this->mapper->fillListObject(Datos::EjecutarSQL($this->mapper->getQueryStoredProcedure($storedProcedureName, $parametros), $clase), $list, $clase);
			return $list;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	/**/public	function getArrayFromStoredProcedure($storedProcedureName, $parametros = ''){
		try {
			$dataArray = array();

			$dataArray = $this->mapper->fillArray(Datos::EjecutarSQL($this->mapper->getQueryStoredProcedure($storedProcedureName, $parametros)), $dataArray);
			return $dataArray;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getArrayFromView($viewName, $clausulaWhere = '1 = 1', $limit = 0, $fields = '*'){
		try {
			$dataArray = array();
			$dataArray = $this->mapper->fillArray(Datos::EjecutarSQL($this->mapper->getQueryView($viewName, $clausulaWhere, $limit, $fields)), $dataArray);
			return $dataArray;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	/**/public	function getArrayFromQuery($query){
		try {
			$dataArray = array();
			$dataArray = $this->mapper->fillArray(Datos::EjecutarSQL($query, 'Indicador'), $dataArray);
			return $dataArray;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function transaction() {
		return Transaction::exists();
	}
	public	function beginTransaction() {
		return Transaction::begin();
	}
	public	function commitTransaction() {
		return Transaction::commit();
	}
	public	function rollbackTransaction() {
		return Transaction::rollback();
	}

	//GETS y SETS
	public	function getAcreditarCheque($numero = -1, $empresa = -1, $entradaSalida = -1) {
		try {
			$acreditarCheque = new AcreditarCheque();
			$acreditarCheque->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa, $entradaSalida))){
				$acreditarCheque->numero = $numero;
				$acreditarCheque->empresa = $empresa;
				$acreditarCheque->entradaSalida = $entradaSalida;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($acreditarCheque, Modos::select), get_class($acreditarCheque)), $acreditarCheque);
			}
			return $acreditarCheque;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setAcreditarCheque(AcreditarCheque $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa, $obj->entradaSalida))) {
					$this->getAcreditarCheque($obj->numero, $obj->empresa, $obj->entradaSalida);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getAcreditarChequeCabecera($numero = -1, $empresa = -1) {
		try {
			$acreditarChequeCabecera = new AcreditarChequeCabecera();
			$acreditarChequeCabecera->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa))){
				$acreditarChequeCabecera->numero = $numero;
				$acreditarChequeCabecera->empresa = $empresa;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($acreditarChequeCabecera, Modos::select), get_class($acreditarChequeCabecera)), $acreditarChequeCabecera);
			}
			return $acreditarChequeCabecera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setAcreditarChequeCabecera(AcreditarChequeCabecera $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getAcreditarChequeCabecera($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getAjusteStock($id = -1) {
		try {
			$ajusteStock = new AjusteStock();
			$ajusteStock->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ajusteStock->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ajusteStock, Modos::select), get_class($ajusteStock)), $ajusteStock);
			}
			return $ajusteStock;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setAjusteStock(AjusteStock $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getAjusteStock($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getAjusteStockMP($id = -1) {
		try {
			$ajusteStockMP = new AjusteStockMP();
			$ajusteStockMP->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ajusteStockMP->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ajusteStockMP, Modos::select), get_class($ajusteStockMP)), $ajusteStockMP);
			}
			return $ajusteStockMP;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setAjusteStockMP(AjusteStockMP $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getAjusteStockMP($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getAlmacen($id = -1) {
		try {
			$almacen = new Almacen();
			$almacen->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$almacen->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($almacen, Modos::select), get_class($almacen)), $almacen);
			}
			return $almacen;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setAlmacen(Almacen $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getAlmacen($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
    public	function getAlmacenPorSeccion($id = -1, $idSeccion = -1) {
        try {
            $almacenPorSeccion = new AlmacenPorSeccion();
            $almacenPorSeccion->modo = Modos::insert;
            if (Funciones::tieneId(array($id, $idSeccion))){
                $almacenPorSeccion->id = $id;
                $almacenPorSeccion->idSeccionProduccion = $idSeccion;
                $this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($almacenPorSeccion, Modos::select), get_class($almacenPorSeccion)), $almacenPorSeccion);
            }
            return $almacenPorSeccion;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    private function setAlmacenPorSeccion(AlmacenPorSeccion $obj) {
        $existe = false;
        try {
            $mutex = new Mutex(Funciones::getType($obj));
            $mutex->lock();
            try {
                if (Funciones::tieneId(array($obj->id, $obj->idSeccionProduccion))) {
                    $this->getAlmacenPorSeccion($obj->id, $obj->idSeccionProduccion);
                    $existe = true;
                }
            } catch (Exception $ex) {
                $existe = false;
            }
            $this->puedePersistir($existe, $obj->modo);
            $this->push($obj);
            $mutex->unlock();
        } catch (Exception $ex) {
            $mutex->unlock();
            throw $ex;
        }
    }
	public	function getAporteSocio($numero = -1, $idEmpresa = -1) {
		try {
			$aporteSocio = new AporteSocio();
			$aporteSocio->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $idEmpresa))){
				$aporteSocio->empresa = $idEmpresa;
				$aporteSocio->numero = $numero;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($aporteSocio, Modos::select), get_class($aporteSocio)), $aporteSocio);
			}
			return $aporteSocio;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setAporteSocio(AporteSocio $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getAporteSocio($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getAreaEmpresa($id = -1) {
		try {
			$areaEmpresa = new AreaEmpresa();
			$areaEmpresa->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$areaEmpresa->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($areaEmpresa, Modos::select), get_class($areaEmpresa)), $areaEmpresa);
			}
			return $areaEmpresa;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setAreaEmpresa(AreaEmpresa $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getAreaEmpresa($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach($obj->usuarios as $usuario){
				$usuario->idAreaEmpresa = $obj->id;
				$this->persistir($usuario);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getArticulo($id = -1) {
		try {
			$articulo = new Articulo();
			$articulo->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$articulo->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($articulo, Modos::select), get_class($articulo)), $articulo);
			}
			return $articulo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setArticulo(Articulo $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getArticulo($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->colores as $item) {
				$item->articulo->id = $obj->id;
				$this->persistir($item);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getAsientoContable($id = -1) {
		try {
			$asientoContable = new AsientoContable();
			$asientoContable->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$asientoContable->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($asientoContable, Modos::select), get_class($asientoContable)), $asientoContable);
			}
			return $asientoContable;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setAsientoContable(AsientoContable $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getAsientoContable($obj->id);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->detalle as $item) {
				/** @var FilaAsientoContable $item */
				$item->idAsientoContable = $obj->id;
				$this->persistir($item);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getAsientoContableModelo($id = -1) {
		try {
			$asientoContableModelo = new AsientoContableModelo();
			$asientoContableModelo->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$asientoContableModelo->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($asientoContableModelo, Modos::select), get_class($asientoContableModelo)), $asientoContableModelo);
			}
			return $asientoContableModelo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setAsientoContableModelo(AsientoContableModelo $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getAsientoContableModelo($obj->id);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->detalle as $item) {
				/** @var AsientoContableModeloFila $item */
				$item->idAsientoContableModelo = $obj->id;
				$this->persistir($item);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getAsientoContableModeloFila($idAsientoContableModelo = -1, $numeroFila = -1) {
		try {
			$asientoContableModeloFila = new AsientoContableModeloFila();
			$asientoContableModeloFila->modo = Modos::insert;
			if (Funciones::tieneId(array($idAsientoContableModelo, $numeroFila))){
				$asientoContableModeloFila->idAsientoContableModelo = $idAsientoContableModelo;
				$asientoContableModeloFila->numeroFila = $numeroFila;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($asientoContableModeloFila, Modos::select), get_class($asientoContableModeloFila)), $asientoContableModeloFila);
			}
			return $asientoContableModeloFila;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setAsientoContableModeloFila(AsientoContableModeloFila $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idAsientoContableModelo, $obj->numeroFila))) {
					$this->getAsientoContableModeloFila($obj->idAsientoContableModelo, $obj->numeroFila);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getAutorizacion($idAutorizacionTipo = -1, $nroAutorizacion = -1, $idEspecifico = -1) {
		try {
			$autorizacion = new Autorizacion();
			$autorizacion->modo = Modos::insert;
			if (Funciones::tieneId(array($idAutorizacionTipo, $nroAutorizacion, $idEspecifico))){
				$autorizacion->idAutorizacionTipo = $idAutorizacionTipo;
				$autorizacion->numero = $nroAutorizacion;
				$autorizacion->idEspecifico = $idEspecifico;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($autorizacion, Modos::select), get_class($autorizacion)), $autorizacion);
			}
			return $autorizacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setAutorizacion(Autorizacion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idAutorizacionTipo, $obj->numero, $obj->idEspecifico))) {
					$this->getAutorizacion($obj->idAutorizacionTipo, $obj->numero, $obj->idEspecifico);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getAutorizacionPersona($idAutorizacionTipo = -1, $nroAutorizacion = -1, $idUsuario = -1) {
		try {
			$autorizacionPersona = new AutorizacionPersona();
			$autorizacionPersona->modo = Modos::insert;
			if (Funciones::tieneId(array($idAutorizacionTipo, $nroAutorizacion, $idUsuario))){
				$autorizacionPersona->idAutorizacionTipo = $idAutorizacionTipo;
				$autorizacionPersona->numero = $nroAutorizacion;
				$autorizacionPersona->idUsuario = $idUsuario;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($autorizacionPersona, Modos::select), get_class($autorizacionPersona)), $autorizacionPersona);
			}
			return $autorizacionPersona;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setAutorizacionPersona(Autorizacion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idAutorizacionTipo, $obj->numero, $obj->idUsuario))) {
					$this->getAutorizacionPersona($obj->idAutorizacionTipo, $obj->numero, $obj->idUsuario);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getAutorizacionTipo($id = -1) {
		try {
			$autorizacionTipo = new AutorizacionTipo();
			$autorizacionTipo->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$autorizacionTipo->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($autorizacionTipo, Modos::select), get_class($autorizacionTipo)), $autorizacionTipo);
			}
			return $autorizacionTipo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setAutorizacionTipo(AutorizacionTipo $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getAutorizacionTipo($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getBanco($id = -1) {
		try {
			$banco = new Banco();
			$banco->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$banco->idBanco = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($banco, Modos::select), get_class($banco)), $banco);
			}
			return $banco;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setBanco(Banco $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idBanco))) {
					$this->getBanco($obj->idBanco);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->idBanco = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getBancoPropio($idBanco = -1, $idSucursal = -1) {
		try {
			$bancoPropio = new BancoPropio();
			$bancoPropio->modo = Modos::insert;
			if (Funciones::tieneId(array($idBanco, $idSucursal))){
				$bancoPropio->idBanco = $idBanco;
				$bancoPropio->idSucursal = $idSucursal;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($bancoPropio, Modos::select), get_class($bancoPropio)), $bancoPropio);
			}
			return $bancoPropio;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setBancoPropio(BancoPropio $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idBanco, $obj->idSucursal))) {
					$this->getBancoPropio($obj->idBanco, $obj->idSucursal);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->idSucursal = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getCaja($numero = -1) {
		try {
			$caja = new Caja();
			$caja->modo = Modos::insert;
			if (Funciones::tieneId(array($numero))){
				$caja->id = $numero;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($caja, Modos::select), get_class($caja)), $caja);
			}
			return $caja;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setCaja(Caja $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getCaja($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);

			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			if (count($obj->cajasPosiblesTransferenciaInterna) && $obj->cajasPosiblesTransferenciaInterna[0]->modo == Modos::insert) {
				if ($obj->modo != Modos::insert) {
					$cajaPosibleAux = $obj->cajasPosiblesTransferenciaInterna[0];
					$this->persistir(self::marcarParaBorrar($cajaPosibleAux));
				}
				foreach ($obj->cajasPosiblesTransferenciaInterna as $cajaPosible) {
					self::marcarParaInsertar($cajaPosible);
					$cajaPosible->idCajaSalida = $obj->id;
					$this->persistir($cajaPosible);
				}
			}
			if (count($obj->permisos) && $obj->permisos[0]->modo == Modos::insert) {
				if ($obj->modo != Modos::insert) {
					$permisoAux = $obj->permisos[0];
					$this->persistir(self::marcarParaBorrar($permisoAux));
				}
				foreach ($obj->permisos as $permiso) {
					self::marcarParaInsertar($permiso);
					$permiso->idCaja = $obj->id;
					$this->persistir($permiso);
				}
			}
			$this->commitTransaction();
			//Termina la transacci�n

			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getCajaPosiblesTransferenciaInterna($idCajaEntrada = -1, $idCajaSalida = -1) {
		try {
			$cajaPosiblesTransferenciaInterna = new CajaPosiblesTransferenciaInterna();
			$cajaPosiblesTransferenciaInterna->modo = Modos::insert;
			if (Funciones::tieneId(array($idCajaEntrada, $idCajaSalida))){
				$cajaPosiblesTransferenciaInterna->idCajaEntrada = $idCajaEntrada;
				$cajaPosiblesTransferenciaInterna->idCajaSalida = $idCajaSalida;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($cajaPosiblesTransferenciaInterna, Modos::select), get_class($cajaPosiblesTransferenciaInterna)), $cajaPosiblesTransferenciaInterna);
			}
			return $cajaPosiblesTransferenciaInterna;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setCajaPosiblesTransferenciaInterna(CajaPosiblesTransferenciaInterna $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idCajaEntrada, $obj->idCajaSalida))) {
					$this->getCajaPosiblesTransferenciaInterna($obj->idCajaEntrada, $obj->idCajaSalida);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getCambiosSituacionCliente($id = -1) {
		try {
			$cheque = new CambiosSituacionCliente();
			$cheque->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$cheque->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($cheque, Modos::select), get_class($cheque)), $cheque);
			}
			return $cheque;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setCambiosSituacionCliente(CambiosSituacionCliente $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getCambiosSituacionCliente($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getCategoriaCalzadoUsuario($id = -1) {
		try {
			$categoriaCalzadoUsuario = new CategoriaCalzadoUsuario();
			$categoriaCalzadoUsuario->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$categoriaCalzadoUsuario->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($categoriaCalzadoUsuario, Modos::select), get_class($categoriaCalzadoUsuario)), $categoriaCalzadoUsuario);
			}
			return $categoriaCalzadoUsuario;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setCategoriaCalzadoUsuario(CategoriaCalzadoUsuario $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getCategoriaCalzadoUsuario($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getCausaNotaDeCredito($id = -1) {
		try {
			$causaNotaDeCredito = new CausaNotaDeCredito();
			$causaNotaDeCredito->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$causaNotaDeCredito->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($causaNotaDeCredito, Modos::select), get_class($causaNotaDeCredito)), $causaNotaDeCredito);
			}
			return $causaNotaDeCredito;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setCausaNotaDeCredito(CausaNotaDeCredito $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getCausaNotaDeCredito($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getCierrePeriodoFiscal($id = -1) {
		try {
			$cierrePeriodoFiscal = new CierrePeriodoFiscal();
			$cierrePeriodoFiscal->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$cierrePeriodoFiscal->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($cierrePeriodoFiscal, Modos::select), get_class($cierrePeriodoFiscal)), $cierrePeriodoFiscal);
			}
			return $cierrePeriodoFiscal;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setCierrePeriodoFiscal(CierrePeriodoFiscal $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getCierrePeriodoFiscal($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getCheque($id = -1) {
		try {
			$cheque = new Cheque();
			$cheque->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$cheque->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($cheque, Modos::select), get_class($cheque)), $cheque);
			}
			return $cheque;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setCheque(Cheque $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getCheque($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getChequera($id = -1) {
		try {
			$chequera = new Chequera();
			$chequera->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$chequera->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($chequera, Modos::select), get_class($chequera)), $chequera);
			}
			return $chequera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setChequera(Chequera $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getChequera($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->detalle as $item) {
				$item->idChequera = $obj->id;
				$this->persistir($item);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getChequeraItem($id = -1) {
		try {
			$chequera = new ChequeraItem();
			$chequera->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$chequera->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($chequera, Modos::select), get_class($chequera)), $chequera);
			}
			return $chequera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setChequeraItem(ChequeraItem $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getChequeraItem($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getCliente($id = -1) {
		try {
			$cliente = new Cliente();
			$cliente->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$cliente->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($cliente, Modos::select), get_class($cliente)), $cliente);
			}
			return $cliente;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setCliente(Cliente $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getCliente($obj->id);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getClienteTodos($id = -1) {
		try {
			$clienteTodos = new ClienteTodos();
			$clienteTodos->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$clienteTodos->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($clienteTodos, Modos::select), get_class($clienteTodos)), $clienteTodos);
			}
			return $clienteTodos;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setClienteTodos(ClienteTodos $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getClienteTodos($obj->id);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getCobroChequeVentanilla($numero = -1, $empresa = -1, $entradaSalida = -1) {
		try {
			$cobroChequeVentanilla = new CobroChequeVentanilla();
			$cobroChequeVentanilla->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa, $entradaSalida))){
				$cobroChequeVentanilla->numero = $numero;
				$cobroChequeVentanilla->empresa = $empresa;
				$cobroChequeVentanilla->entradaSalida = $entradaSalida;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($cobroChequeVentanilla, Modos::select), get_class($cobroChequeVentanilla)), $cobroChequeVentanilla);
			}
			return $cobroChequeVentanilla;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setCobroChequeVentanilla(CobroChequeVentanilla $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa, $obj->entradaSalida))) {
					$this->getCobroChequeVentanilla($obj->numero, $obj->empresa, $obj->entradaSalida);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getCobroChequeVentanillaCabecera($numero = -1, $empresa = -1) {
		try {
			$cobroChequeVentanillaCabecera = new CobroChequeVentanillaCabecera();
			$cobroChequeVentanillaCabecera->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa))){
				$cobroChequeVentanillaCabecera->numero = $numero;
				$cobroChequeVentanillaCabecera->empresa = $empresa;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($cobroChequeVentanillaCabecera, Modos::select), get_class($cobroChequeVentanillaCabecera)), $cobroChequeVentanillaCabecera);
			}
			return $cobroChequeVentanillaCabecera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setCobroChequeVentanillaCabecera(CobroChequeVentanillaCabecera $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getCobroChequeVentanillaCabecera($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getCobroChequeVentanillaTemporal($id = -1) {
		try {
			$cobroChequeVentanillaTemporal = new CobroChequeVentanillaTemporal();
			$cobroChequeVentanillaTemporal->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$cobroChequeVentanillaTemporal->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($cobroChequeVentanillaTemporal, Modos::select), get_class($cobroChequeVentanillaTemporal)), $cobroChequeVentanillaTemporal);
			}
			return $cobroChequeVentanillaTemporal;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setCobroChequeVentanillaTemporal(CobroChequeVentanillaTemporal $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getCobroChequeVentanillaTemporal($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getColor($id = -1) {
		try {
			$color = new Color();
			$color->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$color->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($color, Modos::select), get_class($color)), $color);
			}
			return $color;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setColor(Color $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getColor($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getColorMateriaPrima($idMaterial = -1, $idColor = -1) {
		try {
			$colorMateriaPrima = new ColorMateriaPrima();
			$colorMateriaPrima->modo = Modos::insert;
			if (Funciones::tieneId(array($idMaterial, $idColor))){
				$colorMateriaPrima->idMaterial = $idMaterial;
				$colorMateriaPrima->idColor = $idColor;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($colorMateriaPrima, Modos::select), get_class($colorMateriaPrima)), $colorMateriaPrima);
			}
			return $colorMateriaPrima;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setColorMateriaPrima(ColorMateriaPrima $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idMaterial, $obj->idColor))) {
					$this->getColorMateriaPrima($obj->idMaterial, $obj->idColor);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getColorPorArticulo($idArticulo = -1, $id = -1) {
		try {
			$colorPorArticulo = new ColorPorArticulo();
			$colorPorArticulo->modo = Modos::insert;
			if (Funciones::tieneId(array($idArticulo, $id))){
				$colorPorArticulo->idArticulo = $idArticulo;
				$colorPorArticulo->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($colorPorArticulo, Modos::select), get_class($colorPorArticulo)), $colorPorArticulo);
			}
			return $colorPorArticulo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setColorPorArticulo(ColorPorArticulo $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idArticulo, $obj->id))) {
					$this->getColorPorArticulo($obj->idArticulo, $obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			//Empieza la transacci�n
			$this->beginTransaction();
			//$obj->curvas se hace para que al momento de actualizar color por art�culo se puedan dar de alta en la BD las curvas que se dropearon en el mapper.
			$obj->curvas;
			$this->push($obj);
			foreach ($obj->curvas as $item) {
				$item->idColorPorArticulo = $obj->id;
				$item->idArticulo = $obj->idArticulo;
				$item->idCurva = $item->curva->id;
                $this->marcarParaInsertar($item);
				$this->persistir($item);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getConcepto($id = -1) {
		try {
			$impuesto = new Concepto();
			$impuesto->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$impuesto->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($impuesto, Modos::select), get_class($impuesto)), $impuesto);
			}
			return $impuesto;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setConcepto(Concepto $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getConcepto($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getConceptoRetencionGanancias($id = -1) {
		try {
			$conceptoRetencionGanancias = new ConceptoRetencionGanancias();
			$conceptoRetencionGanancias->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$conceptoRetencionGanancias->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($conceptoRetencionGanancias, Modos::select), get_class($conceptoRetencionGanancias)), $conceptoRetencionGanancias);
			}
			return $conceptoRetencionGanancias;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setConceptoRetencionGanancias(ConceptoRetencionGanancias $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getConceptoRetencionGanancias($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getCondicionIva($id = -1) {
		try {
			$condicionIva = new CondicionIva();
			$condicionIva->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$condicionIva->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($condicionIva, Modos::select), get_class($condicionIva)), $condicionIva);
			}
			return $condicionIva;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setCondicionIva(CondicionIva $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getCondicionIva($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getConfirmacionStock($id = -1) {
		try {
			$confirmacionStock = new ConfirmacionStock();
			$confirmacionStock->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$confirmacionStock->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($confirmacionStock, Modos::select), get_class($confirmacionStock)), $confirmacionStock);
			}
			return $confirmacionStock;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setConfirmacionStock(ConfirmacionStock $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getConfirmacionStock($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getContacto($id = -1) {
		try {
			$contacto = new Contacto();
			$contacto->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$contacto->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($contacto, Modos::select), get_class($contacto)), $contacto);
			}
			return $contacto;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setContacto(Contacto $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getContacto($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getConjunto($id = -1) {
		try {
			$contacto = new Conjunto();
			$contacto->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$contacto->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($contacto, Modos::select), get_class($contacto)), $contacto);
			}
			return $contacto;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setConjunto(Conjunto $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getConjunto($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
    public	function getConsumoStockMP($id = -1) {
        try {
            $consumoStockMP = new ConsumoStockMP();
            $consumoStockMP->modo = Modos::insert;
            if (Funciones::tieneId(array($id))){
                $consumoStockMP->id = $id;
                $this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($consumoStockMP, Modos::select), get_class($consumoStockMP)), $consumoStockMP);
            }
            return $consumoStockMP;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    private function setConsumoStockMP(ConsumoStockMP $obj) {
        $existe = false;
        try {
            $mutex = new Mutex(Funciones::getType($obj));
            $mutex->lock();
            try {
                if (Funciones::tieneId(array($obj->id))) {
                    $this->getConsumoStockMP($obj->id);
                    $existe = true;
                }
            } catch (Exception $ex) {
                $existe = false;
            }
            $this->puedePersistir($existe, $obj->modo);
            if ($obj->modo == Modos::insert)
                $obj->id = $this->getNextId($obj);
            $this->push($obj);
            $mutex->unlock();
        } catch (Exception $ex) {
            $mutex->unlock();
            throw $ex;
        }
    }
    public	function getCuentaBancaria($id = -1) {
        try {
            $cuentaBancaria = new CuentaBancaria();
            $cuentaBancaria->modo = Modos::insert;
            if (Funciones::tieneId(array($id))){
                $cuentaBancaria->id = $id;
                $this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($cuentaBancaria, Modos::select), get_class($cuentaBancaria)), $cuentaBancaria);
            }
            return $cuentaBancaria;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    private function setCuentaBancaria(CuentaBancaria $obj) {
        $existe = false;
        try {
            $mutex = new Mutex(Funciones::getType($obj));
            $mutex->lock();
            try {
                if (Funciones::tieneId(array($obj->id))) {
                    $this->getCuentaBancaria($obj->id);
                    $existe = true;
                }
            } catch (Exception $ex) {
                $existe = false;
            }
            $this->puedePersistir($existe, $obj->modo);
            if ($obj->modo == Modos::insert)
                $obj->id = $this->getNextId($obj);
            $this->push($obj);
            $mutex->unlock();
        } catch (Exception $ex) {
            $mutex->unlock();
            throw $ex;
        }
    }
	public	function getCuentaCorrienteHistorica($idCliente = -1, $empresa = -1) {
		try {
			$cuentaCorrienteHistorica = new CuentaCorrienteHistorica();
			$cuentaCorrienteHistorica->modo = Modos::insert;
			if (Funciones::tieneId(array($idCliente))){
				$cuentaCorrienteHistorica->idCliente= $idCliente;
				$cuentaCorrienteHistorica->empresa = $empresa;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($cuentaCorrienteHistorica, Modos::select), get_class($cuentaCorrienteHistorica)), $cuentaCorrienteHistorica);
			}
			return $cuentaCorrienteHistorica;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getCuentaCorrienteHistoricaProveedor($idProveedor = -1, $empresa = -1) {
		try {
			$cuentaCorrienteHistoricaProveedor = new CuentaCorrienteHistoricaProveedor();
			$cuentaCorrienteHistoricaProveedor->modo = Modos::insert;
			if (Funciones::tieneId(array($idProveedor))){
				$cuentaCorrienteHistoricaProveedor->idProveedor = $idProveedor;
				$cuentaCorrienteHistoricaProveedor->empresa = $empresa;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($cuentaCorrienteHistoricaProveedor, Modos::select), get_class($cuentaCorrienteHistoricaProveedor)), $cuentaCorrienteHistoricaProveedor);
			}
			return $cuentaCorrienteHistoricaProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getCuentaCorrienteHistoricaDocumento($idEmpresa = -1, $tipoDocumento = -1, $numeroDocumento = -1, $letraDocumento = -1) {
		try {
			$cuentaCorrienteHistoricaDocumento = new CuentaCorrienteHistoricaDocumento();
			$cuentaCorrienteHistoricaDocumento->modo = Modos::insert;
			if (Funciones::tieneId(array($idEmpresa, $tipoDocumento, $numeroDocumento, $letraDocumento))){
				$cuentaCorrienteHistoricaDocumento->empresa = $idEmpresa;
				$cuentaCorrienteHistoricaDocumento->tipoDocumento = $tipoDocumento;
				$cuentaCorrienteHistoricaDocumento->numeroDocumento = $numeroDocumento;
				$cuentaCorrienteHistoricaDocumento->letra = $letraDocumento;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($cuentaCorrienteHistoricaDocumento, Modos::select), get_class($cuentaCorrienteHistoricaDocumento)), $cuentaCorrienteHistoricaDocumento);
			}
			return $cuentaCorrienteHistoricaDocumento;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getCuentaCorrienteHistoricaDocumentoProveedor($idDocumento = -1) {
		try {
			$cuentaCorrienteHistoricaDocumentoProveedor = new CuentaCorrienteHistoricaDocumentoProveedor();
			$cuentaCorrienteHistoricaDocumentoProveedor->modo = Modos::insert;
			if (Funciones::tieneId(array($idDocumento))){
				$cuentaCorrienteHistoricaDocumentoProveedor->idDocumento = $idDocumento;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($cuentaCorrienteHistoricaDocumentoProveedor, Modos::select), get_class($cuentaCorrienteHistoricaDocumentoProveedor)), $cuentaCorrienteHistoricaDocumentoProveedor);
			}
			return $cuentaCorrienteHistoricaDocumentoProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getCurva($id = -1) {
		try {
			$curva = new Curva();
			$curva->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$curva->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($curva, Modos::select), get_class($curva)), $curva);
			}
			return $curva;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setCurva(Curva $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getCurva($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
    public	function getCurvaPorArticulo($idArticulo = -1, $idColorPorArticulo = -1, $idCurva = -1) {
        try {
            $curvaPorArticulo = new CurvaPorArticulo();
            $curvaPorArticulo->modo = Modos::insert;
            if (Funciones::tieneId(array($idArticulo, $idColorPorArticulo, $idCurva))){
                $curvaPorArticulo->idArticulo = $idArticulo;
                $curvaPorArticulo->idColorPorArticulo = $idColorPorArticulo;
                $curvaPorArticulo->idCurva = $idCurva;
                $this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($curvaPorArticulo, Modos::select), get_class($curvaPorArticulo)), $curvaPorArticulo);
            }
            return $curvaPorArticulo;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    private function setCurvaPorArticulo(CurvaPorArticulo $obj) {
        $existe = false;
        try {
            $mutex = new Mutex(Funciones::getType($obj));
            $mutex->lock();
            try {
                if (Funciones::tieneId(array($obj->idArticulo, $obj->idColorPorArticulo, $obj->idCurva))) {
                    $this->getCurvaPorArticulo($obj->idArticulo, $obj->idColorPorArticulo, $obj->idCurva);
                    $existe = true;
                }
            } catch (Exception $ex) {
                $existe = false;
            }
            $this->puedePersistir($existe, $obj->modo);
            $this->push($obj);
            $mutex->unlock();
        } catch (Exception $ex) {
            $mutex->unlock();
            throw $ex;
        }
    }
    public	function getCurvaProduccionPorArticulo($id = -1) {
        try {
            $curvaProduccionPorArticulo = new CurvaProduccionPorArticulo();
            $curvaProduccionPorArticulo->modo = Modos::insert;
            if (Funciones::tieneId(array($id))){
                $curvaProduccionPorArticulo->id = $id;
                $this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($curvaProduccionPorArticulo, Modos::select), get_class($curvaProduccionPorArticulo)), $curvaProduccionPorArticulo);
            }
            return $curvaProduccionPorArticulo;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    private function setCurvaProduccionPorArticulo(CurvaProduccionPorArticulo $obj) {
        $existe = false;
        try {
            $mutex = new Mutex(Funciones::getType($obj));
            $mutex->lock();
            try {
                if (Funciones::tieneId(array($obj->id))) {
                    $this->getCurvaProduccionPorArticulo($obj->id);
                    $existe = true;
                }
            } catch (Exception $ex) {
                $existe = false;
            }
            $this->puedePersistir($existe, $obj->modo);
            $this->push($obj);
            $mutex->unlock();
        } catch (Exception $ex) {
            $mutex->unlock();
            throw $ex;
        }
    }
	public	function getDebitarCheque($numero = -1, $empresa = -1, $entradaSalida = -1) {
		try {
			$debitarCheque = new DebitarCheque();
			$debitarCheque->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa, $entradaSalida))){
				$debitarCheque->numero = $numero;
				$debitarCheque->empresa = $empresa;
				$debitarCheque->entradaSalida = $entradaSalida;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($debitarCheque, Modos::select), get_class($debitarCheque)), $debitarCheque);
			}
			return $debitarCheque;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDebitarCheque(DebitarCheque $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa, $obj->entradaSalida))) {
					$this->getDebitarCheque($obj->numero, $obj->empresa, $obj->entradaSalida);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDebitarChequeCabecera($numero = -1, $empresa = -1) {
		try {
			$debitarChequeCabecera = new DebitarChequeCabecera();
			$debitarChequeCabecera->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa))){
				$debitarChequeCabecera->numero = $numero;
				$debitarChequeCabecera->empresa = $empresa;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($debitarChequeCabecera, Modos::select), get_class($debitarChequeCabecera)), $debitarChequeCabecera);
			}
			return $debitarChequeCabecera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDebitarChequeCabecera(DebitarChequeCabecera $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getDebitarChequeCabecera($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDepositoBancario($numero = -1, $empresa = -1, $entradaSalida = -1) {
		try {
			$depositoCheque = new DepositoBancario();
			$depositoCheque->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa, $entradaSalida))){
				$depositoCheque->numero = $numero;
				$depositoCheque->empresa = $empresa;
				$depositoCheque->entradaSalida = $entradaSalida;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($depositoCheque, Modos::select), get_class($depositoCheque)), $depositoCheque);
			}
			return $depositoCheque;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDepositoBancario(DepositoBancario $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa, $obj->entradaSalida))) {
					$this->getDepositoBancario($obj->numero, $obj->empresa, $obj->entradaSalida);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			//if ($obj->modo == Modos::insert)
			//	$obj->numero = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDepositoBancarioCabecera($numero = -1, $empresa = -1) {
		try {
			$depositoChequeCabecera = new DepositoBancarioCabecera();
			$depositoChequeCabecera->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa))){
				$depositoChequeCabecera->numero = $numero;
				$depositoChequeCabecera->empresa = $empresa;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($depositoChequeCabecera, Modos::select), get_class($depositoChequeCabecera)), $depositoChequeCabecera);
			}
			return $depositoChequeCabecera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDepositoBancarioCabecera(DepositoBancarioCabecera $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getDepositoBancarioCabecera($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDepositoBancarioTemporal($id = -1) {
		try {
			$depositoBancarioTemporal = new DepositoBancarioTemporal();
			$depositoBancarioTemporal->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$depositoBancarioTemporal->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($depositoBancarioTemporal, Modos::select), get_class($depositoBancarioTemporal)), $depositoBancarioTemporal);
			}
			return $depositoBancarioTemporal;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDepositoBancarioTemporal(DepositoBancarioTemporal $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getDepositoBancarioTemporal($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDespacho($numero = -1) {
		try {
			$despacho = new Despacho();
			$despacho->modo = Modos::insert;
			if (Funciones::tieneId(array($numero))){
				$despacho->numero = $numero;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($despacho, Modos::select), get_class($despacho)), $despacho);
			}
			return $despacho;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDespacho(Despacho $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero))) {
					$this->getDespacho($obj->numero);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->detalle as $item) {
				$item->despachoNumero = $obj->numero;
				$this->persistir($item);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDespachoItem($despachoNumero = -1, $numeroDeItem = -1) {
		try {
			$despachoItem = new DespachoItem();
			$despachoItem->modo = Modos::insert;
			if (Funciones::tieneId(array($despachoNumero, $numeroDeItem))){
				$despachoItem->despachoNumero = $despachoNumero;
				$despachoItem->numeroDeItem = $numeroDeItem;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($despachoItem, Modos::select), get_class($despachoItem)), $despachoItem);
			}
			return $despachoItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDespachoItem(DespachoItem $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->despachoNumero, $obj->numeroDeItem))) {
					$this->getDespachoItem($obj->despachoNumero, $obj->numeroDeItem);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDevolucionACliente($id = -1) {
		try {
			$devolucionACliente = new DevolucionACliente();
			$devolucionACliente->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$devolucionACliente->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($devolucionACliente, Modos::select), get_class($devolucionACliente)), $devolucionACliente);
			}
			return $devolucionACliente;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDevolucionACliente(DevolucionACliente $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getDevolucionACliente($obj->id);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->detalle as $item) {
				$item->devolucionACliente = $obj;
				$this->persistir($item);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDevolucionAClienteItem($id = -1) {
		try {
			$devolucionAClienteItem = new DevolucionAClienteItem();
			$devolucionAClienteItem->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$devolucionAClienteItem->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($devolucionAClienteItem, Modos::select), get_class($devolucionAClienteItem)), $devolucionAClienteItem);
			}
			return $devolucionAClienteItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDevolucionAClienteItem(DevolucionAClienteItem $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getDevolucionAClienteItem($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDocumento($idEmpresa = -1, $puntoDeVenta = -1, $tipoDocumento = -1, $numeroDocumento = -1, $letraDocumento = -1) {
		try {
			switch ($tipoDocumento) {
				case 'FAC':
					$documento = new Factura(); break;
				case 'NDB':
					$documento = new NotaDeDebito(); break;
				case 'NCR':
					$documento = new NotaDeCredito(); break;
				default:
					$documento = new Documento(); break;
			}
			$documento->modo = Modos::insert;
			if (Funciones::tieneId(array($idEmpresa, $puntoDeVenta, $tipoDocumento, $numeroDocumento, $letraDocumento))){
				$documento->empresa = $idEmpresa;
				$documento->puntoDeVenta = $puntoDeVenta;
				$documento->tipoDocumento = $tipoDocumento;
				$documento->numero = $numeroDocumento;
				$documento->letra = $letraDocumento;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documento, Modos::select), get_class($documento)), $documento);
			}
			return $documento;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDocumento(Documento $obj) {
		//$existe = false;
		try {
			switch ($obj->tipoDocumento) {
				case 'FAC':
				case 'NDB':
					$this->setDocumentoDebe($obj); break;
				case 'REC':
				case 'NCR':
					$this->setDocumentoHaber($obj); break;
			}
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getDocumentoAplicacion($idEmpresa = -1, $puntoDeVenta = -1, $tipoDocumento = -1, $numeroDocumento = -1, $letraDocumento = -1) {
		try {
			$documentoAplicacion = new DocumentoAplicacion();
			$documentoAplicacion->modo = Modos::insert;
			if (Funciones::tieneId(array($idEmpresa, $puntoDeVenta, $tipoDocumento, $numeroDocumento, $letraDocumento))){
				$documentoAplicacion->empresa = $idEmpresa;
				$documentoAplicacion->puntoDeVenta = $puntoDeVenta;
				$documentoAplicacion->tipoDocumento = $tipoDocumento;
				$documentoAplicacion->nroDocumento = $numeroDocumento;
				$documentoAplicacion->letra = $letraDocumento;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documentoAplicacion, Modos::select), get_class($documentoAplicacion)), $documentoAplicacion);
			}
			return $documentoAplicacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDocumentoAplicacion(DocumentoAplicacion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->empresa, $obj->puntoDeVenta, $obj->tipoDocumento, $obj->nroDocumento, $obj->letra))) {
					$this->getDocumentoAplicacion($obj->empresa, $obj->puntoDeVenta, $obj->tipoDocumento, $obj->nroDocumento, $obj->letra);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDocumentoAplicacionDebe($idEmpresa = -1, $puntoDeVenta = -1, $tipoDocumento = -1, $numeroDocumento = -1, $letraDocumento = -1) {
		try {
			$documentoAplicacionDebe = new DocumentoAplicacionDebe();
			$documentoAplicacionDebe->modo = Modos::insert;
			if (Funciones::tieneId(array($idEmpresa, $puntoDeVenta, $tipoDocumento, $numeroDocumento, $letraDocumento))){
				$documentoAplicacionDebe->empresa = $idEmpresa;
				$documentoAplicacionDebe->puntoDeVenta = $puntoDeVenta;
				$documentoAplicacionDebe->tipoDocumento = $tipoDocumento;
				$documentoAplicacionDebe->nroDocumento = $numeroDocumento;
				$documentoAplicacionDebe->letra = $letraDocumento;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documentoAplicacionDebe, Modos::select), get_class($documentoAplicacionDebe)), $documentoAplicacionDebe);
			}
			return $documentoAplicacionDebe;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDocumentoAplicacionDebe($obj) {
		$this->setDocumentoAplicacion($obj);
	}
	public	function getDocumentoAplicacionHaber($idEmpresa = -1, $puntoDeVenta = -1, $tipoDocumento = -1, $numeroDocumento = -1, $letraDocumento = -1) {
		try {
			$documentoAplicacionHaber = new DocumentoAplicacionHaber();
			$documentoAplicacionHaber->modo = Modos::insert;
			if (Funciones::tieneId(array($idEmpresa, $puntoDeVenta, $tipoDocumento, $numeroDocumento, $letraDocumento))){
				$documentoAplicacionHaber->empresa = $idEmpresa;
				$documentoAplicacionHaber->puntoDeVenta = $puntoDeVenta;
				$documentoAplicacionHaber->tipoDocumento = $tipoDocumento;
				$documentoAplicacionHaber->nroDocumento = $numeroDocumento;
				$documentoAplicacionHaber->letra = $letraDocumento;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documentoAplicacionHaber, Modos::select), get_class($documentoAplicacionHaber)), $documentoAplicacionHaber);
			}
			return $documentoAplicacionHaber;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDocumentoAplicacionHaber($obj) {
		$this->setDocumentoAplicacion($obj);
	}
	public	function getDocumentoDebe($idEmpresa = -1, $puntoDeVenta = -1, $tipoDocumento = -1, $numeroDocumento = -1, $letraDocumento = -1) {
		try {
			$documentoDebe = new DocumentoDebe();
			$documentoDebe->modo = Modos::insert;
			if (Funciones::tieneId(array($idEmpresa, $puntoDeVenta, $tipoDocumento, $numeroDocumento, $letraDocumento))){
				$documentoDebe->empresa = $idEmpresa;
				$documentoDebe->puntoDeVenta = $puntoDeVenta;
				$documentoDebe->tipoDocumento = $tipoDocumento;
				$documentoDebe->numero = $numeroDocumento;
				$documentoDebe->letra = $letraDocumento;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documentoDebe, Modos::select), get_class($documentoDebe)), $documentoDebe);
			}
			return $documentoDebe;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDocumentoDebe(DocumentoDebe $obj) {
	//$existe = false;
		try {
			switch ($documento->tipoDocumento) {
				case 'FAC':
					$this->setFactura($obj); break;
				case 'NDB':
					$this->setNotaDeDebito($obj); break;
			}
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getDocumentoGastoDatos($id = -1) {
		try {
			$documentoGastosDatos = new DocumentoGastoDatos();
			$documentoGastosDatos->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$documentoGastosDatos->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documentoGastosDatos, Modos::select), get_class($documentoGastosDatos)), $documentoGastosDatos);
			}
			return $documentoGastosDatos;
		} catch (Exception $ex){
			throw $ex;
		}
	}
	private function setDocumentoGastoDatos(DocumentoGastoDatos $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getDocumentoGastoDatos($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDocumentoHaber($idEmpresa = -1, $puntoDeVenta = -1, $tipoDocumento = -1, $numeroDocumento = -1, $letraDocumento = -1) {
		try {
			$documentoHaber = new DocumentoHaber();
			$documentoHaber->modo = Modos::insert;
			if (Funciones::tieneId(array($idEmpresa, $puntoDeVenta, $tipoDocumento, $numeroDocumento, $letraDocumento))){
				$documentoHaber->empresa = $idEmpresa;
				$documentoHaber->puntoDeVenta = $puntoDeVenta;
				$documentoHaber->tipoDocumento = $tipoDocumento;
				$documentoHaber->numero = $numeroDocumento;
				$documentoHaber->letra = $letraDocumento;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documentoHaber, Modos::select), get_class($documentoHaber)), $documentoHaber);
			}
			return $documentoHaber;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDocumentoHaber(DocumentoHaber $obj) {
		//$existe = false;
		try {
			switch ($documento->tipoDocumento) {
				case 'NCR':
					$this->setNotaDeCredito($obj); break;
			}
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getDocumentoHija($id = -1) {
		try {
			$documentoHija = new DocumentoHija();
			$documentoHija->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$documentoHija->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documentoHija, Modos::select), get_class($documentoHija)), $documentoHija);
			}
			return $documentoHija;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDocumentoHija(DocumentoHija $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getDocumentoHija($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			$this->persistir($obj->documentoCancelatorio);
			$this->persistir($obj->madre);
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDocumentoItem($idEmpresa = -1, $puntoDeVenta = -1, $tipoDocumento = -1, $numeroDocumento = -1, $letraDocumento = -1, $numeroDeItem = -1) {
		try {
			$documentoItem = new DocumentoItem();
			$documentoItem->modo = Modos::insert;
			if (Funciones::tieneId(array($idEmpresa, $puntoDeVenta, $tipoDocumento, $numeroDocumento, $letraDocumento, $numeroDeItem))){
				$documentoItem->empresa = $idEmpresa;
				$documentoItem->puntoDeVenta = $puntoDeVenta;
				$documentoItem->documentoTipoDocumento = $tipoDocumento;
				$documentoItem->documentoNumero = $numeroDocumento;
				$documentoItem->documentoLetra = $letraDocumento;
				$documentoItem->numeroDeItem = $numeroDeItem;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documentoItem, Modos::select), get_class($documentoItem)), $documentoItem);
			}
			return $documentoItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDocumentoItem(DocumentoItem $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->empresa, $obj->puntoDeVenta, $obj->documentoTipoDocumento, $obj->documentoNumero, $obj->documentoLetra, $obj->numeroDeItem))) {
					$this->getDocumentoItem($obj->empresa, $obj->puntoDeVenta, $obj->documentoTipoDocumento, $obj->documentoNumero, $obj->documentoLetra, $obj->numeroDeItem);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDocumentoProveedor($id = -1) {
		try {
			$documentoProveedor = new DocumentoProveedor();
			$documentoProveedor->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$documentoProveedor->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documentoProveedor, Modos::select), get_class($documentoProveedor)), $documentoProveedor);
			}
			return $documentoProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDocumentoProveedor(DocumentoProveedor $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$metodo = 'get';
					switch ($obj->tipoDocum) {
						case 'FAC': $metodo .= 'FacturaProveedor'; break;
						case 'NCR': $metodo .= 'NotaDeCreditoProveedor'; break;
						case 'NDB': $metodo .= 'NotaDeDebitoProveedor'; break;
					}
					$this->$metodo($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();

			if(!$obj->esProveedor){
				if ($obj->documentoGastoDatos->modo == Modos::insert)
					$obj->documentoGastoDatos->id = $this->getNextId($obj->documentoGastoDatos);

				$this->persistir($obj->documentoGastoDatos);
			}

			$this->push($obj);
			foreach ($obj->detalle as $item) {
				$item->idDocumentoProveedor = $obj->id;
				$this->persistir($item);
			}
			foreach ($obj->impuestos as $impuesto) {
				$impuesto->idDocumentoProveedor = $obj->id;
				$this->persistir($impuesto);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDocumentoProveedorAplicacion($id = -1, $empresa = -1, $tipoDocumento = -1) {
		try {
			$documentoProveedorAplicacion = new DocumentoProveedorAplicacion();
			$documentoProveedorAplicacion->modo = Modos::insert;
			if (Funciones::tieneId(array($id, $empresa))){
				$documentoProveedorAplicacion->id = $id;
				$documentoProveedorAplicacion->empresa = $empresa;
				if ($tipoDocumento && $tipoDocumento !== -1) {
                    $documentoProveedorAplicacion->tipoDocumento = $tipoDocumento;
                }
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documentoProveedorAplicacion, Modos::select), get_class($documentoProveedorAplicacion)), $documentoProveedorAplicacion);
			}
			return $documentoProveedorAplicacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDocumentoProveedorAplicacion(DocumentoProveedorAplicacion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id, $obj->empresa))) {
					$this->getDocumentoProveedorAplicacion($obj->id, $obj->empresa, $obj->tipoDocumento);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDocumentoProveedorAplicacionDebe($empresa = -1, $id = -1) {
		try {
			$documentoProveedorAplicacionDebe = new DocumentoProveedorAplicacionDebe();
			$documentoProveedorAplicacionDebe->modo = Modos::insert;
			if (Funciones::tieneId(array($id, $empresa))){
				$documentoProveedorAplicacionDebe->id = $id;
				$documentoProveedorAplicacionDebe->empresa = $empresa;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documentoProveedorAplicacionDebe, Modos::select), get_class($documentoProveedorAplicacionDebe)), $documentoProveedorAplicacionDebe);
			}
			return $documentoProveedorAplicacionDebe;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDocumentoProveedorAplicacionDebe($obj) {
		$this->setDocumentoProveedorAplicacion($obj);
	}
	public	function getDocumentoProveedorAplicacionHaber($empresa = -1, $id = -1, $tipoDocumento = -1) {
		try {
			$documentoProveedorAplicacionHaber = new DocumentoProveedorAplicacionHaber();
			$documentoProveedorAplicacionHaber->modo = Modos::insert;
			if (Funciones::tieneId(array($id, $empresa))){
				$documentoProveedorAplicacionHaber->empresa = $empresa;
				$documentoProveedorAplicacionHaber->id = $id;
				$documentoProveedorAplicacionHaber->tipoDocumento = $tipoDocumento;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documentoProveedorAplicacionHaber, Modos::select), get_class($documentoProveedorAplicacionHaber)), $documentoProveedorAplicacionHaber);
			}
			return $documentoProveedorAplicacionHaber;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDocumentoProveedorAplicacionHaber($obj) {
		$this->setDocumentoProveedorAplicacion($obj);
	}
	public	function getDocumentoProveedorHija($id = -1) {
		try {
			$documentoProveedorHija = new DocumentoProveedorHija();
			$documentoProveedorHija->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$documentoProveedorHija->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documentoProveedorHija, Modos::select), get_class($documentoProveedorHija)), $documentoProveedorHija);
			}
			return $documentoProveedorHija;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDocumentoProveedorHija(DocumentoProveedorHija $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getDocumentoProveedorHija($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			$this->persistir($obj->documentoCancelatorio);
			$this->persistir($obj->madre);
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getDocumentoProveedorItem($id = -1, $nroItem = -1) {
		try {
			$documentoProveedorItem = new DocumentoProveedorItem();
			$documentoProveedorItem->modo = Modos::insert;
			if (Funciones::tieneId(array($id, $nroItem))){
				$documentoProveedorItem->idDocumentoProveedor = $id;
				$documentoProveedorItem->nroItem = $nroItem;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($documentoProveedorItem, Modos::select), get_class($documentoProveedorItem)), $documentoProveedorItem);
			}
			return $documentoProveedorItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setDocumentoProveedorItem(DocumentoProveedorItem $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idDocumentoProveedor, $obj->nroItem))) {
					$this->getDocumentoProveedorItem($obj->idDocumentoProveedor, $obj->nroItem);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->nroItem = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getEfectivo($id = -1) {
		try {
			$efectivo = new Efectivo();
			$efectivo->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$efectivo->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($efectivo, Modos::select), get_class($efectivo)), $efectivo);
			}
			return $efectivo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEfectivo(Efectivo $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getEfectivo($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getEcommerce_Coupon($id = -1) {
		try {
			$ecommerce_Coupon = new Ecommerce_Coupon();
			$ecommerce_Coupon->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_Coupon->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_Coupon, Modos::select), get_class($ecommerce_Coupon)), $ecommerce_Coupon);
			}
			return $ecommerce_Coupon;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_Coupon(Ecommerce_Coupon $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getEcommerce_Coupon($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getEcommerce_Customer($id = -1) {
		try {
			$ecommerce_Customer = new Ecommerce_Customer();
			$ecommerce_Customer->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_Customer->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_Customer, Modos::select), get_class($ecommerce_Customer)), $ecommerce_Customer);
			}
			return $ecommerce_Customer;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_Customer(Ecommerce_Customer $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getEcommerce_Customer($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getEcommerce_Delivery($id = -1) {
		try {
			$ecommerce_Delivery = new Ecommerce_Delivery();
			$ecommerce_Delivery->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_Delivery->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_Delivery, Modos::select), get_class($ecommerce_Delivery)), $ecommerce_Delivery);
			}
			return $ecommerce_Delivery;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_Delivery(Ecommerce_Delivery $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getEcommerce_Delivery($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getEcommerce_Order($id = -1) {
		try {
			$ecommerce_Order = new Ecommerce_Order();
			$ecommerce_Order->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_Order->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_Order, Modos::select), get_class($ecommerce_Order)), $ecommerce_Order);
			}
			return $ecommerce_Order;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_Order(Ecommerce_Order $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getEcommerce_Order($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getEcommerce_OrderDetail($id = -1) {
		try {
			$ecommerce_OrderDetail = new Ecommerce_OrderDetail();
			$ecommerce_OrderDetail->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_OrderDetail->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_OrderDetail, Modos::select), get_class($ecommerce_OrderDetail)), $ecommerce_OrderDetail);
			}
			return $ecommerce_OrderDetail;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_OrderDetail(Ecommerce_OrderDetail $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getEcommerce_OrderDetail($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getEcommerce_OrderStatus($id = -1) {
		try {
			$ecommerce_OrderStatus = new Ecommerce_OrderStatus();
			$ecommerce_OrderStatus->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_OrderStatus->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_OrderStatus, Modos::select), get_class($ecommerce_OrderStatus)), $ecommerce_OrderStatus);
			}
			return $ecommerce_OrderStatus;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_OrderStatus(Ecommerce_OrderStatus $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getEcommerce_OrderStatus($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getEcommerce_OrderStatus_Cobrado($id = -1) {
		try {
			$ecommerce_OrderStatus_Cobrado = new Ecommerce_OrderStatus_Cobrado();
			$ecommerce_OrderStatus_Cobrado->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_OrderStatus_Cobrado->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_OrderStatus_Cobrado, Modos::select), get_class($ecommerce_OrderStatus_Cobrado)), $ecommerce_OrderStatus_Cobrado);
			}
			return $ecommerce_OrderStatus_Cobrado;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_OrderStatus_Cobrado(Ecommerce_OrderStatus_Cobrado $obj) {
		$this->setEcommerce_OrderStatus($obj);
	}
	public	function getEcommerce_OrderStatus_Despachado($id = -1) {
		try {
			$ecommerce_OrderStatus_Despachado = new Ecommerce_OrderStatus_Despachado();
			$ecommerce_OrderStatus_Despachado->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_OrderStatus_Despachado->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_OrderStatus_Despachado, Modos::select), get_class($ecommerce_OrderStatus_Despachado)), $ecommerce_OrderStatus_Despachado);
			}
			return $ecommerce_OrderStatus_Despachado;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_OrderStatus_Despachado(Ecommerce_OrderStatus_Despachado $obj) {
		$this->setEcommerce_OrderStatus($obj);
	}
	public	function getEcommerce_OrderStatus_EnTransito($id = -1) {
		try {
			$ecommerce_OrderStatus_EnTransito = new Ecommerce_OrderStatus_EnTransito();
			$ecommerce_OrderStatus_EnTransito->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_OrderStatus_EnTransito->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_OrderStatus_EnTransito, Modos::select), get_class($ecommerce_OrderStatus_EnTransito)), $ecommerce_OrderStatus_EnTransito);
			}
			return $ecommerce_OrderStatus_EnTransito;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_OrderStatus_EnTransito(Ecommerce_OrderStatus_EnTransito $obj) {
		$this->setEcommerce_OrderStatus($obj);
	}
	public	function getEcommerce_OrderStatus_Facturado($id = -1) {
		try {
			$ecommerce_OrderStatus_Facturado = new Ecommerce_OrderStatus_Facturado();
			$ecommerce_OrderStatus_Facturado->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_OrderStatus_Facturado->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_OrderStatus_Facturado, Modos::select), get_class($ecommerce_OrderStatus_Facturado)), $ecommerce_OrderStatus_Facturado);
			}
			return $ecommerce_OrderStatus_Facturado;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_OrderStatus_Facturado(Ecommerce_OrderStatus_Facturado $obj) {
		$this->setEcommerce_OrderStatus($obj);
	}
	public	function getEcommerce_OrderStatus_FacturadoCae($id = -1) {
		try {
			$ecommerce_OrderStatus_FacturadoCae = new Ecommerce_OrderStatus_FacturadoCae();
			$ecommerce_OrderStatus_FacturadoCae->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_OrderStatus_FacturadoCae->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_OrderStatus_FacturadoCae, Modos::select), get_class($ecommerce_OrderStatus_FacturadoCae)), $ecommerce_OrderStatus_FacturadoCae);
			}
			return $ecommerce_OrderStatus_FacturadoCae;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_OrderStatus_FacturadoCae(Ecommerce_OrderStatus_FacturadoCae $obj) {
		$this->setEcommerce_OrderStatus($obj);
	}
	public	function getEcommerce_OrderStatus_Finalizado($id = -1) {
		try {
			$ecommerce_OrderStatus_Finalizado = new Ecommerce_OrderStatus_Finalizado();
			$ecommerce_OrderStatus_Finalizado->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_OrderStatus_Finalizado->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_OrderStatus_Finalizado, Modos::select), get_class($ecommerce_OrderStatus_Finalizado)), $ecommerce_OrderStatus_Finalizado);
			}
			return $ecommerce_OrderStatus_Finalizado;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_OrderStatus_Finalizado(Ecommerce_OrderStatus_Finalizado $obj) {
		$this->setEcommerce_OrderStatus($obj);
	}
	public	function getEcommerce_OrderStatus_Pedido($id = -1) {
		try {
			$ecommerce_OrderStatus_Pedido = new Ecommerce_OrderStatus_Pedido();
			$ecommerce_OrderStatus_Pedido->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_OrderStatus_Pedido->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_OrderStatus_Pedido, Modos::select), get_class($ecommerce_OrderStatus_Pedido)), $ecommerce_OrderStatus_Pedido);
			}
			return $ecommerce_OrderStatus_Pedido;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getEcommerce_OrderStatus_PendienteDeCambio($id = -1) {
		try {
			$ecommerce_OrderStatus_PendienteDeCambio = new Ecommerce_OrderStatus_PendienteDeCambio();
			$ecommerce_OrderStatus_PendienteDeCambio->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_OrderStatus_PendienteDeCambio->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_OrderStatus_PendienteDeCambio, Modos::select), get_class($ecommerce_OrderStatus_PendienteDeCambio)), $ecommerce_OrderStatus_PendienteDeCambio);
			}
			return $ecommerce_OrderStatus_PendienteDeCambio;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getEcommerce_OrderStatus_PendienteDeDevolucion($id = -1) {
		try {
			$ecommerce_OrderStatus_PendienteDeDevolucion = new Ecommerce_OrderStatus_PendienteDeDevolucion();
			$ecommerce_OrderStatus_PendienteDeDevolucion->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_OrderStatus_PendienteDeDevolucion->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_OrderStatus_PendienteDeDevolucion, Modos::select), get_class($ecommerce_OrderStatus_PendienteDeDevolucion)), $ecommerce_OrderStatus_PendienteDeDevolucion);
			}
			return $ecommerce_OrderStatus_PendienteDeDevolucion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_OrderStatusPedido(Ecommerce_OrderStatus_Pedido $obj) {
		$this->setEcommerce_OrderStatus($obj);
	}
	public	function getEcommerce_OrderStatus_Predespachado($id = -1) {
		try {
			$ecommerce_OrderStatus_Predespachado = new Ecommerce_OrderStatus_Predespachado();
			$ecommerce_OrderStatus_Predespachado->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_OrderStatus_Predespachado->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_OrderStatus_Predespachado, Modos::select), get_class($ecommerce_OrderStatus_Predespachado)), $ecommerce_OrderStatus_Predespachado);
			}
			return $ecommerce_OrderStatus_Predespachado;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_OrderStatusPredespachado(Ecommerce_OrderStatus_Predespachado $obj) {
		$this->setEcommerce_OrderStatus($obj);
	}
	public	function getEcommerce_OrderStatus_Remitido($id = -1) {
		try {
			$ecommerce_OrderStatus_Remitido = new Ecommerce_OrderStatus_Remitido();
			$ecommerce_OrderStatus_Remitido->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_OrderStatus_Remitido->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_OrderStatus_Remitido, Modos::select), get_class($ecommerce_OrderStatus_Remitido)), $ecommerce_OrderStatus_Remitido);
			}
			return $ecommerce_OrderStatus_Remitido;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_OrderStatus_Remitido(Ecommerce_OrderStatus_Remitido $obj) {
		$this->setEcommerce_OrderStatus($obj);
	}
	public	function getEcommerce_Payment($id = -1) {
		try {
			$ecommerce_Payment = new Ecommerce_Payment();
			$ecommerce_Payment->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_Payment->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_Payment, Modos::select), get_class($ecommerce_Payment)), $ecommerce_Payment);
			}
			return $ecommerce_Payment;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_Payment(Ecommerce_Payment $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getEcommerce_Payment($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getEcommerce_PaymentMethod($id = -1) {
		try {
			$ecommerce_PaymentMethod = new Ecommerce_PaymentMethod();
			$ecommerce_PaymentMethod->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_PaymentMethod->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_PaymentMethod, Modos::select), get_class($ecommerce_PaymentMethod)), $ecommerce_PaymentMethod);
			}
			return $ecommerce_PaymentMethod;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_PaymentMethod(Ecommerce_PaymentMethod $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getEcommerce_PaymentMethod($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getEcommerce_ServicioAndreani($id = -1) {
		try {
			$ecommerce_ServicioAndreani = new Ecommerce_ServicioAndreani();
			$ecommerce_ServicioAndreani->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_ServicioAndreani->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_ServicioAndreani, Modos::select), get_class($ecommerce_ServicioAndreani)), $ecommerce_ServicioAndreani);
			}
			return $ecommerce_ServicioAndreani;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_ServicioAndreani(Ecommerce_ServicioAndreani $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getEcommerce_ServicioAndreani($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getEcommerce_Usergroup($id = -1) {
		try {
			$ecommerce_Usergroup = new Ecommerce_Usergroup();
			$ecommerce_Usergroup->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ecommerce_Usergroup->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ecommerce_Usergroup, Modos::select), get_class($ecommerce_Usergroup)), $ecommerce_Usergroup);
			}
			return $ecommerce_Usergroup;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEcommerce_Usergroup(Ecommerce_Usergroup $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getEcommerce_Usergroup($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getEjercicioContable($id = -1) {
		try {
			$ejercicioContable = new EjercicioContable();
			$ejercicioContable->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ejercicioContable->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ejercicioContable, Modos::select), get_class($ejercicioContable)), $ejercicioContable);
			}
			return $ejercicioContable;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEjercicioContable(EjercicioContable $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getEjercicioContable($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getEmail($id = -1) {
		try {
			$email = new Email();
			$email->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$email->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($email, Modos::select), get_class($email)), $email);
			}
			return $email;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setEmail(Email $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getEmail($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getFactura($idEmpresa = -1, $puntoDeVenta = -1, $tipoDocumento = -1, $numeroDocumento = -1, $letraDocumento = -1) {
		try {
			$factura = new Factura();
			$factura->modo = Modos::insert;
			if (Funciones::tieneId(array($idEmpresa, $puntoDeVenta, $tipoDocumento, $numeroDocumento, $letraDocumento))){
				$factura->empresa = $idEmpresa;
				$factura->puntoDeVenta = $puntoDeVenta;
				$factura->tipoDocumento = $tipoDocumento;
				$factura->numero = $numeroDocumento;
				$factura->letra = $letraDocumento;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($factura, Modos::select), get_class($factura)), $factura);
			}
			return $factura;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setFactura(Factura $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->empresa, $obj->puntoDeVenta, $obj->tipoDocumento, $obj->numero, $obj->letra))) {
					$this->getFactura($obj->empresa, $obj->puntoDeVenta, $obj->tipoDocumento, $obj->numero, $obj->letra);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			if ($obj->tieneDetalle()) {
				//Si la factura tiene detalle, �ste va en documentos_d y lo manejo desde ac�
				//Empieza la transacci�n
				$this->beginTransaction();
				$this->push($obj);
				if ($obj->modo == Modos::insert) {
					foreach ($obj->detalle as $item) {
						$item->documentoNumero = $obj->numero;
						$this->persistir($item);
					}
				}
				$this->commitTransaction();
				//Termina la transacci�n
			} else {
				//Si la factura no tiene detalle, es el camino convencional, manejado desde el mapper
				$this->push($obj);
			}
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getFacturaProveedor($id = -1) {
		try {
			$facturaProveedor = new FacturaProveedor();
			$facturaProveedor->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$facturaProveedor->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($facturaProveedor, Modos::select), get_class($facturaProveedor)), $facturaProveedor);
			}
			return $facturaProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setFacturaProveedor($obj) {
		$this->setDocumentoProveedor($obj);
	}
	public	function getFajaHoraria($id = -1) {
		try {
			$fajaHoraria = new FajaHoraria();
			$fajaHoraria->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$fajaHoraria->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($fajaHoraria, Modos::select), get_class($fajaHoraria)), $fajaHoraria);
			}
			return $fajaHoraria;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setFajaHoraria(FajaHoraria $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getFajaHoraria($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getFasonier($id = -1) {
		try {
			$fasonier = new Fasonier();
			$fasonier->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$fasonier->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($fasonier, Modos::select), get_class($fasonier)), $fasonier);
			}
			return $fasonier;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setFasonier(Fasonier $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getFasonier($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getFichaje($id = -1) {
		try {
			$fichaje = new Fichaje();
			$fichaje->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$fichaje->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($fichaje, Modos::select), get_class($fichaje)), $fichaje);
			}
			return $fichaje;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setFichaje(Fichaje $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getFichaje($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getFilaAsientoContable($idAsientoContable = -1, $numeroFila = -1) {
		try {
			$filaAsientoContable = new FilaAsientoContable();
			$filaAsientoContable->modo = Modos::insert;
			if (Funciones::tieneId(array($idAsientoContable, $numeroFila))){
				$filaAsientoContable->idAsientoContable = $idAsientoContable;
				$filaAsientoContable->numeroFila = $numeroFila;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($filaAsientoContable, Modos::select), get_class($filaAsientoContable)), $filaAsientoContable);
			}
			return $filaAsientoContable;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setFilaAsientoContable(FilaAsientoContable $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idAsientoContable, $obj->numeroFila))) {
					$this->getFilaAsientoContable($obj->idAsientoContable, $obj->numeroFila);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
    public	function getForecast($id) {
        try {
            $forecast = new Forecast();
            $forecast->modo = Modos::insert;
            if (Funciones::tieneId(array($id))){
                $forecast->id = $id;
                $this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($forecast, Modos::select), get_class($forecast)), $forecast);
            }
            return $forecast;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    private function setForecast(Forecast $obj) {
        $existe = false;
        try {
            $mutex = new Mutex(Funciones::getType($obj));
            $mutex->lock();
            try {
                if (Funciones::tieneId(array($obj->id))) {
                    $this->getForecast($obj->id);
                    $existe = true;
                }
            } catch (Exception $ex) {
                $existe = false;
            }
            $this->puedePersistir($existe, $obj->modo);
            if ($obj->modo == Modos::insert)
                $obj->id = $this->getNextId($obj);
            $this->beginTransaction();
            $this->push($obj);
            foreach ($obj->detalle as $item) {
                $item->forecast = $obj;
                $item->id = $this->getNextId($item);
                $this->marcarParaInsertar($item);
                $this->persistir($item);
            }
            $this->commitTransaction();
            $mutex->unlock();
        } catch (Exception $ex) {
            $mutex->unlock();
            throw $ex;
        }
    }
    public	function getForecastItem($id) {
        try {
            $forecastItem = new ForecastItem();
            $forecastItem->modo = Modos::insert;
            if (Funciones::tieneId(array($id))){
                $forecastItem->id = $id;
                $this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($forecastItem, Modos::select), get_class($forecastItem)), $forecastItem);
            }
            return $forecastItem;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    private function setForecastItem(ForecastItem $obj) {
        $existe = false;
        try {
            $mutex = new Mutex(Funciones::getType($obj));
            $mutex->lock();
            try {
                if (Funciones::tieneId(array($obj->id))) {
                    $this->getForecastItem($obj->id);
                    $existe = true;
                }
            } catch (Exception $ex) {
                $existe = false;
            }
            $this->puedePersistir($existe, $obj->modo);
            $this->push($obj);
            $mutex->unlock();
        } catch (Exception $ex) {
            $mutex->unlock();
            throw $ex;
        }
    }
	public	function getFormaDePago($id = -1) {
		try {
			$formaDePago = new FormaDePago();
			$formaDePago->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$formaDePago->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($formaDePago, Modos::select), get_class($formaDePago)), $formaDePago);
			}
			return $formaDePago;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setFormaDePago(FormaDePago $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getFormaDePago($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getFormulario($id = -1) {
		/*
		try {
			$formulario = new Formulario();
			$formulario->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$formulario->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($formulario, Modos::select), get_class($formulario)), $formulario);
			}
			return $formulario;
		} catch (Exception $ex) {
			throw $ex;
		}
		*/
	}
	private function setFormulario(Formulario $obj) {
		/*
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getFormulario($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
		*/
	}
	public	function getFuncionalidadPorRol($idRol = -1, $idFuncionalidad = -1) {
		try {
			$funcionalidadPorRol = new FuncionalidadPorRol();
			$funcionalidadPorRol->modo = Modos::insert;
			if (Funciones::tieneId(array($idRol, $idFuncionalidad))){
				$funcionalidadPorRol->idRol = $idRol;
				$funcionalidadPorRol->idFuncionalidad = $idFuncionalidad;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($funcionalidadPorRol, Modos::select), get_class($funcionalidadPorRol)), $funcionalidadPorRol);
			}
			return $funcionalidadPorRol;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setFuncionalidadPorRol(FuncionalidadPorRol $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idRol, $obj->idFuncionalidad))) {
					$this->getFuncionalidadPorRol($obj->idRol, $obj->idFuncionalidad);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getGarantia($id = -1) {
		try {
			$garantia = new Garantia();
			$garantia->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$garantia->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($garantia, Modos::select), get_class($garantia)), $garantia);
			}
			return $garantia;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setGarantia(Garantia $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getGarantia($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			if ($obj->modo == Modos::insert) {
				foreach ($obj->detalle as $item) {
					$item->id = $this->getNextId($item);
					$item->idGarantia = $obj->id;
					$this->persistir($item);
				}
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getGarantiaItem($id = -1) {
		try {
			$garantiaItem = new GarantiaItem();
			$garantiaItem->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$garantiaItem->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($garantiaItem, Modos::select), get_class($garantiaItem)), $garantiaItem);
			}
			return $garantiaItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setGarantiaItem(GarantiaItem $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getGarantiaItem($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getSeguimientoCliente($id = -1) {
		try {
			$seguimientoCliente = new SeguimientoCliente();
			$seguimientoCliente->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$seguimientoCliente->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($seguimientoCliente, Modos::select), get_class($seguimientoCliente)), $seguimientoCliente);
			}
			return $seguimientoCliente;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setSeguimientoCliente(SeguimientoCliente $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getSeguimientoCliente($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getGastito($id = -1) {
		try {
			$gastito = new Gastito();
			$gastito->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$gastito->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($gastito, Modos::select), get_class($gastito)), $gastito);
			}
			return $gastito;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setGastito(Gastito $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getGastito($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getGrupoEmpresa($id = -1) {
		try {
			$grupoEmpresa = new GrupoEmpresa();
			$grupoEmpresa->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$grupoEmpresa->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($grupoEmpresa, Modos::select), get_class($grupoEmpresa)), $grupoEmpresa);
			}
			return $grupoEmpresa;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setGrupoEmpresa(GrupoEmpresa $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getGrupoEmpresa($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getHeartbeat($idUsuario = -1) {
		try {
			$heartbeat = new Heartbeat();
			$heartbeat->modo = Modos::insert;
			if (Funciones::tieneId(array($idUsuario))){
				$heartbeat->idUsuario = $idUsuario;
				//No necesito que entre por ac� porque no quiero traer nada de la DB.
				//$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($heartBeat, Modos::select), get_class($heartBeat)), $heartBeat);
			}
			return $heartbeat;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setHeartbeat(HeartBeat $obj) {
		//$existe = false;
		try {
			//Me salteo to_do lo que no se hace. Ya s� que el usuario existe.
			$this->push($obj);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getKoiTicket($id = -1) {
		try {
			$koiTicket = new KoiTicket();
			$koiTicket->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$koiTicket->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($koiTicket, Modos::select), get_class($koiTicket)), $koiTicket);
			}
			return $koiTicket;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setKoiTicket(KoiTicket $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getKoiTicket($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getHorma($id = -1) {
		try {
			$horma = new Horma();
			$horma->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$horma->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($horma, Modos::select), get_class($horma)), $horma);
			}
			return $horma;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setHorma(Horma $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getHorma($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getImportePorOperacion($idImporteOperacion = -1) {
		try {
			$importePorOperacion = new ImportePorOperacion();
			$importePorOperacion->modo = Modos::insert;
			if (Funciones::tieneId(array($idImporteOperacion))){
				$importePorOperacion->idImportePorOperacion = $idImporteOperacion;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($importePorOperacion, Modos::select), get_class($importePorOperacion)), $importePorOperacion);
			}
			return $importePorOperacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setImportePorOperacion(ImportePorOperacion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idImportePorOperacion))) {
					$this->getImportePorOperacion($obj->idImportePorOperacion);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getImportePorOperacionItem($idImportePorOperacion = -1, $tipoImporte = -1, $idImporte = -1) {
		try {
			$importePorOperacion = new ImportePorOperacionItem();
			$importePorOperacion->modo = Modos::insert;
			if (Funciones::tieneId(array($idImportePorOperacion,$tipoImporte,$idImporte))){
				$importePorOperacion->idImportePorOperacion = $idImportePorOperacion;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($importePorOperacion, Modos::select), get_class($importePorOperacion)), $importePorOperacion);
			}
			return $importePorOperacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setImportePorOperacionItem(ImportePorOperacionItem $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idImportePorOperacion, $obj->tipoImporte, $obj->idImporte))) {
					$this->getImportePorOperacion($obj->idImportePorOperacion, $obj->tipoImporte, $obj->idImporte);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getImputacion($id = -1) {
		try {
			$imputacion = new Imputacion();
			$imputacion->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$imputacion->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($imputacion, Modos::select), get_class($imputacion)), $imputacion);
			}
			return $imputacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setImputacion(Imputacion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getImputacion($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getImpuesto($id = -1) {
		try {
			$impuesto = new Impuesto();
			$impuesto->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$impuesto->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($impuesto, Modos::select), get_class($impuesto)), $impuesto);
			}
			return $impuesto;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setImpuesto(Impuesto $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getImpuesto($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getImpuestoPorDocumentoProveedor($idImpuesto = -1, $idDocumentoProveedor = -1) {
		try {
			$impuestoPorDocumentoProveedor = new ImpuestoPorDocumentoProveedor();
			$impuestoPorDocumentoProveedor->modo = Modos::insert;
			if (Funciones::tieneId(array($idImpuesto, $idDocumentoProveedor))){
				$impuestoPorDocumentoProveedor->idImpuesto = $idImpuesto;
				$impuestoPorDocumentoProveedor->idDocumentoProveedor = $idDocumentoProveedor;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($impuestoPorDocumentoProveedor, Modos::select), get_class($impuestoPorDocumentoProveedor)), $impuestoPorDocumentoProveedor);
			}
			return $impuestoPorDocumentoProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setImpuestoPorDocumentoProveedor(ImpuestoPorDocumentoProveedor $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idImpuesto, $obj->idDocumentoProveedor))) {
					$this->getImpuesto($obj->idImpuesto, $obj->idDocumentoProveedor);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getIndicador($id = -1) {
		try {
			$indicador = new Indicador();
			$indicador->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$indicador->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($indicador, Modos::select), get_class($indicador)), $indicador);
			}
			return $indicador;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setIndicador(Indicador $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getIndicador($obj->id);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->roles as $rol) {
				$rol->idIndicador = $obj->id;
				$this->persistir($rol);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getIndicadorPorRol($idRol = -1, $idIndicador = -1) {
		try {
			$indicadorPorRol = new IndicadorPorRol();
			$indicadorPorRol->modo = Modos::insert;
			if (Funciones::tieneId(array($idRol, $idIndicador))){
				$indicadorPorRol->id = $idRol;
				$indicadorPorRol->idIndicador = $idIndicador;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($indicadorPorRol, Modos::select), get_class($indicadorPorRol)), $indicadorPorRol);
			}
			return $indicadorPorRol;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setIndicadorPorRol(IndicadorPorRol $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id, $obj->idIndicador))) {
					$this->getIndicadorPorRol($obj->id, $obj->idIndicador);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getIngresoChequePropio($numero = -1, $empresa = -1) {
		try {
			$ingresoChequePropio = new IngresoChequePropio();
			$ingresoChequePropio->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa))){
				$ingresoChequePropio->numero = $numero;
				$ingresoChequePropio->empresa = $empresa;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ingresoChequePropio, Modos::select), get_class($ingresoChequePropio)), $ingresoChequePropio);
			}
			return $ingresoChequePropio;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setIngresoChequePropio(IngresoChequePropio $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getIngresoChequePropio($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getInstruccionArticulo($idArticulo = -1, $idSeccion = -1, $interna = -1) {
		try {
			$instruccionArticulo = new InstruccionArticulo();
			$instruccionArticulo->modo = Modos::insert;
			if (Funciones::tieneId(array($idArticulo, $idSeccion, $interna))){
				$instruccionArticulo->idArticulo = $idArticulo;
				$instruccionArticulo->idSeccion = $idSeccion;
				$instruccionArticulo->interna = $interna;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($instruccionArticulo, Modos::select), get_class($instruccionArticulo)), $instruccionArticulo);
			}
			return $instruccionArticulo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setInstruccionArticulo(InstruccionArticulo $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idArticulo, $obj->idSeccion, $obj->interna))) {
					$this->getInstruccionArticulo($obj->idArticulo, $obj->idSeccion, $obj->interna);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getLineaProducto($id = -1) {
		try {
			$lineaProducto = new LineaProducto();
			$lineaProducto->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$lineaProducto->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($lineaProducto, Modos::select), get_class($lineaProducto)), $lineaProducto);
			}
			return $lineaProducto;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setLineaProducto(LineaProducto $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getLineaProducto($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getLocalidad($idPais = -1, $idProvincia = -1, $id = -1) {
		try {
			$localidad = new Localidad();
			$localidad->modo = Modos::insert;
			if (Funciones::tieneId(array($idPais, $idProvincia, $id))){
				$localidad->idPais = $idPais;
				$localidad->idProvincia = $idProvincia;
				$localidad->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($localidad, Modos::select), get_class($localidad)), $localidad);
			}
			return $localidad;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setLocalidad(Localidad $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idPais, $obj->idProvincia, $obj->id))) {
					$this->getLocalidad($obj->idPais, $obj->idProvincia, $obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getManejadorFtp($server, $ftpUser, $ftpPassword, $isPassive = false) {
		try {
			$manejadorFtp = ManejadorFtp::getInstance();
			$manejadorFtp->connect($server, $ftpUser, $ftpPassword, $isPassive);

			return $manejadorFtp;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getMarca($id = -1) {
		try {
			$marca = new Marca();
			$marca->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$marca->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($marca, Modos::select), get_class($marca)), $marca);
			}
			return $marca;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setMarca(Marca $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getMarca($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getMaterial($id = -1) {
		try {
			$marca = new Material();
			$marca->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$marca->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($marca, Modos::select), get_class($marca)), $marca);
			}
			return $marca;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setMaterial(Material $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getMaterial($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getMotivo($id = -1) {
		try {
			$motivo = new Motivo();
			$motivo->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$motivo->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($motivo, Modos::select), get_class($motivo)), $motivo);
			}
			return $motivo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setMotivo(Motivo $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getMotivo($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getMotivoAusentismo($id = -1) {
		try {
			$motivoAusentismo = new MotivoAusentismo();
			$motivoAusentismo->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$motivoAusentismo->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($motivoAusentismo, Modos::select), get_class($motivoAusentismo)), $motivoAusentismo);
			}
			return $motivoAusentismo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setMotivoAusentismo(MotivoAusentismo $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getMotivoAusentismo($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getMovimientoAlmacen($id = -1) {
		try {
			$movimientoAlmacen = new MovimientoAlmacen();
			$movimientoAlmacen->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$movimientoAlmacen->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($movimientoAlmacen, Modos::select), get_class($movimientoAlmacen)), $movimientoAlmacen);
			}
			return $movimientoAlmacen;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setMovimientoAlmacen(MovimientoAlmacen $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getMovimientoAlmacen($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getMovimientoAlmacenConfirmacion($id = -1) {
		try {
			$movimientoAlmacenConfirmar = new MovimientoAlmacenConfirmacion();
			$movimientoAlmacenConfirmar->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$movimientoAlmacenConfirmar->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($movimientoAlmacenConfirmar, Modos::select), get_class($movimientoAlmacenConfirmar)), $movimientoAlmacenConfirmar);
			}
			return $movimientoAlmacenConfirmar;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setMovimientoAlmacenConfirmacion(MovimientoAlmacenConfirmacion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getMovimientoAlmacenConfirmacion($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getMovimientoAlmacenMP($id = -1) {
		try {
			$movimientoAlmacenMP = new MovimientoAlmacenMP();
			$movimientoAlmacenMP->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$movimientoAlmacenMP->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($movimientoAlmacenMP, Modos::select), get_class($movimientoAlmacenMP)), $movimientoAlmacenMP);
			}
			return $movimientoAlmacenMP;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setMovimientoAlmacenMP(MovimientoAlmacenMP $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getMovimientoAlmacenMP($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getMovimientoAlmacenConfirmacionMP($id = -1) {
		try {
			$movimientoAlmacenConfirmarMP = new MovimientoAlmacenConfirmacionMP();
			$movimientoAlmacenConfirmarMP->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$movimientoAlmacenConfirmarMP->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($movimientoAlmacenConfirmarMP, Modos::select), get_class($movimientoAlmacenConfirmarMP)), $movimientoAlmacenConfirmarMP);
			}
			return $movimientoAlmacenConfirmarMP;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setMovimientoAlmacenConfirmacionMP(MovimientoAlmacenConfirmacionMP $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getMovimientoAlmacenConfirmacionMP($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getMovimientoStock($id = -1) {
		try {
			$movimientoStock = new MovimientoStock();
			$movimientoStock->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$movimientoStock->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($movimientoStock, Modos::select), get_class($movimientoStock)), $movimientoStock);
			}
			return $movimientoStock;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setMovimientoStock(MovimientoStock $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getMovimientoStock($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getMovimientoStockMP($id = -1) {
		try {
			$movimientoStockMP = new MovimientoStockMP();
			$movimientoStockMP->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$movimientoStockMP->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($movimientoStockMP, Modos::select), get_class($movimientoStockMP)), $movimientoStockMP);
			}
			return $movimientoStockMP;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setMovimientoStockMP(MovimientoStockMP $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getMovimientoStockMP($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getNotaDeCredito($idEmpresa = -1, $puntoDeVenta = -1, $tipoDocumento = -1, $numeroDocumento = -1, $letraDocumento = -1) {
		try {
			$notaDeCredito = new NotaDeCredito();
			$notaDeCredito->modo = Modos::insert;
			if (Funciones::tieneId(array($idEmpresa, $puntoDeVenta, $tipoDocumento, $numeroDocumento, $letraDocumento))){
				$notaDeCredito->empresa = $idEmpresa;
				$notaDeCredito->puntoDeVenta = $puntoDeVenta;
				$notaDeCredito->tipoDocumento = $tipoDocumento;
				$notaDeCredito->numero = $numeroDocumento;
				$notaDeCredito->letra = $letraDocumento;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($notaDeCredito, Modos::select), get_class($notaDeCredito)), $notaDeCredito);
			}
			return $notaDeCredito;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setNotaDeCredito(NotaDeCredito $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->empresa, $obj->puntoDeVenta, $obj->tipoDocumento, $obj->numero, $obj->letra))) {
					$this->getNotaDeCredito($obj->empresa, $obj->puntoDeVenta, $obj->tipoDocumento, $obj->numero, $obj->letra);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			if ($obj->modo == Modos::insert) {
				foreach ($obj->detalle as $item) {
					$item->empresa = $obj->empresa;
					$item->puntoDeVenta = $obj->puntoDeVenta;
					$item->documentoTipoDocumento = $obj->tipoDocumento;
					$item->documentoLetra = $obj->letra;
					$item->documentoNumero = $obj->numero;
					$this->persistir($item);
				}
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getNotaDeCreditoProveedor($id = -1) {
		try {
			$notaDeCreditoProveedor = new NotaDeCreditoProveedor();
			$notaDeCreditoProveedor->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$notaDeCreditoProveedor->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($notaDeCreditoProveedor, Modos::select), get_class($notaDeCreditoProveedor)), $notaDeCreditoProveedor);
			}
			return $notaDeCreditoProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setNotaDeCreditoProveedor($obj) {
		$this->setDocumentoProveedor($obj);
	}
	public	function getNotaDeDebito($idEmpresa = -1, $puntoDeVenta = -1, $tipoDocumento = -1, $numeroDocumento = -1, $letraDocumento = -1) {
		try {
			$notaDeDebito = new NotaDeDebito();
			$notaDeDebito->modo = Modos::insert;
			if (Funciones::tieneId(array($idEmpresa, $puntoDeVenta, $tipoDocumento, $numeroDocumento, $letraDocumento))){
				$notaDeDebito->empresa = $idEmpresa;
				$notaDeDebito->puntoDeVenta = $puntoDeVenta;
				$notaDeDebito->tipoDocumento = $tipoDocumento;
				$notaDeDebito->numero = $numeroDocumento;
				$notaDeDebito->letra = $letraDocumento;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($notaDeDebito, Modos::select), get_class($notaDeDebito)), $notaDeDebito);
			}
			return $notaDeDebito;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setNotaDeDebito(NotaDeDebito $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->empresa, $obj->puntoDeVenta, $obj->tipoDocumento, $obj->numero, $obj->letra))) {
					$this->getNotaDeDebito($obj->empresa, $obj->puntoDeVenta, $obj->tipoDocumento, $obj->numero, $obj->letra);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			if ($obj->modo == Modos::insert) {
				foreach ($obj->detalle as $item) {
					$item->empresa = $obj->empresa;
					$item->puntoDeVenta = $obj->puntoDeVenta;
					$item->documentoTipoDocumento = $obj->tipoDocumento;
					$item->documentoLetra = $obj->letra;
					$item->documentoNumero = $obj->numero;
					$this->persistir($item);
				}
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getNotaDeDebitoProveedor($id = -1) {
		try {
			$notaDeDebitoProveedor = new NotaDeDebitoProveedor();
			$notaDeDebitoProveedor->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$notaDeDebitoProveedor->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($notaDeDebitoProveedor, Modos::select), get_class($notaDeDebitoProveedor)), $notaDeDebitoProveedor);
			}
			return $notaDeDebitoProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setNotaDeDebitoProveedor($obj) {
		$this->setDocumentoProveedor($obj);
	}
	public	function getNotificacion($id = -1) {
		try {
			$notificacion = new Notificacion();
			$notificacion->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$notificacion->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($notificacion, Modos::select), get_class($notificacion)), $notificacion);
			}
			return $notificacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setNotificacion(Notificacion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getNotificacion($obj->id);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->usuarios as $usuario) {
				$usuario->idNotificacion = $obj->id;
				$this->persistir($usuario);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getNotificacionPorUsuario($idUsuario = -1, $idNotificacion = -1) {
		try {
			$notificacionPorUsuario = new NotificacionPorUsuario();
			$notificacionPorUsuario->modo = Modos::insert;
			if (Funciones::tieneId(array($idUsuario, $idNotificacion))){
				$notificacionPorUsuario->id = $idUsuario;
				$notificacionPorUsuario->idNotificacion = $idNotificacion;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($notificacionPorUsuario, Modos::select), get_class($notificacionPorUsuario)), $notificacionPorUsuario);
			}
			return $notificacionPorUsuario;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setNotificacionPorUsuario(NotificacionPorUsuario $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id, $obj->idNotificacion))) {
					$this->getNotificacionPorUsuario($obj->id, $obj->idNotificacion);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getOperador($id = -1) {
		try {
			$operador = new Operador();
			$operador->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$operador->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($operador, Modos::select), get_class($operador)), $operador);
			}
			return $operador;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setOperador(Operador $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getOperador($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->idPersonal = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getOrdenDeCompra($id = -1) {
		try {
			$ordenDeCompra = new OrdenDeCompra();
			$ordenDeCompra->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ordenDeCompra->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ordenDeCompra, Modos::select), get_class($ordenDeCompra)), $ordenDeCompra);
			}
			return $ordenDeCompra;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setOrdenDeCompra(OrdenDeCompra $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getOrdenDeCompra($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->detalle as $item) {
				$item->ordenDeCompra = $obj;
				$this->persistir($item);
			}
			$this->commitTransaction();
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getOrdenDeCompraItem($idOrdenDeCompra = -1, $nroItem = -1) {
		try {
			$ordenDeCompraItem = new OrdenDeCompraItem();
			$ordenDeCompraItem->modo = Modos::insert;
			if (Funciones::tieneId(array($idOrdenDeCompra))){
				$ordenDeCompraItem->idOrdenDeCompra = $idOrdenDeCompra;
				$ordenDeCompraItem->numeroDeItem = $nroItem;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ordenDeCompraItem, Modos::select), get_class($ordenDeCompraItem)), $ordenDeCompraItem);
			}
			return $ordenDeCompraItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setOrdenDeCompraItem(OrdenDeCompraItem $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idOrdenDeCompra, $obj->numeroDeItem))) {
					$this->getOrdenDeCompra($obj->idOrdenDeCompra, $obj->numeroDeItem);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numeroDeItem = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getOrdenDeFabricacion($id = -1) {
		try {
			$ordenDeFabricacion = new OrdenDeFabricacion();
			$ordenDeFabricacion->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ordenDeFabricacion->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ordenDeFabricacion, Modos::select), get_class($ordenDeFabricacion)), $ordenDeFabricacion);
			}
			return $ordenDeFabricacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setOrdenDeFabricacion(OrdenDeFabricacion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getOrdenDeFabricacion($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getOrdenDePago($numero = -1, $idEmpresa = -1) {
		try {
			$ordenDePago = new OrdenDePago();
			$ordenDePago->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $idEmpresa))){
				$ordenDePago->empresa = $idEmpresa;
				$ordenDePago->numero = $numero;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ordenDePago, Modos::select), get_class($ordenDePago)), $ordenDePago);
			}
			return $ordenDePago;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setOrdenDePago(OrdenDePago $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getOrdenDePago($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPais($id = -1) {
		try {
			$pais = new Pais();
			$pais->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$pais->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($pais, Modos::select), get_class($pais)), $pais);
			}
			return $pais;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPais(Pais $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getPais($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getParametro($id = -1) {
		try {
			$parametro = new Parametro();
			$parametro->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$parametro->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($parametro, Modos::select), get_class($parametro)), $parametro);
			}
			return $parametro;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setParametro(Parametro $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getParametro($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getParametroContabilidad($id = -1) {
		try {
			$parametroContabilidad = new ParametroContabilidad();
			$parametroContabilidad->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$parametroContabilidad->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($parametroContabilidad, Modos::select), get_class($parametroContabilidad)), $parametroContabilidad);
			}
			return $parametroContabilidad;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setParametroContabilidad(ParametroContabilidad $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getParametroContabilidad($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPatron($idArticulo = -1, $idColorPorArticulo = -1, $version = -1) {
		try {
			$patron = new Patron();
			$patron->modo = Modos::insert;
			if (Funciones::tieneId(array($idArticulo, $idColorPorArticulo, $version))){
				$patron->idArticulo = $idArticulo;
				$patron->idColorPorArticulo = $idColorPorArticulo;
				$patron->version = $version;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($patron, Modos::select), get_class($patron)), $patron);
			}
			return $patron;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPatron(Patron $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idArticulo, $obj->idColorPorArticulo, $obj->version))) {
					$this->getPatron($obj->idArticulo, $obj->idColorPorArticulo, $obj->version);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->detalle as $item) {
				$item->patron = $obj;
				$item->numeroDeItem = $this->getNextId($item);
				$this->marcarParaInsertar($item);
				$this->persistir($item);
			}
			$this->commitTransaction();
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPatronItem($idArticulo = -1, $idColorPorArticulo = -1, $version = -1, $numeroDeItem = -1) {
		try {
			$patronItem = new PatronItem();
			$patronItem->modo = Modos::insert;
			if (Funciones::tieneId(array($idArticulo, $idColorPorArticulo, $version, $numeroDeItem))){
				$patronItem->idArticulo = $idArticulo;
				$patronItem->idColorPorArticulo = $idColorPorArticulo;
				$patronItem->version = $version;
				$patronItem->numeroDeItem = $numeroDeItem;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($patronItem, Modos::select), get_class($patronItem)), $patronItem);
			}
			return $patronItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPatronItem(PatronItem $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idArticulo, $obj->idColorPorArticulo, $obj->version, $obj->numeroDeItem))) {
					$this->getPatronItem($obj->idArticulo, $obj->idColorPorArticulo, $obj->version, $obj->numeroDeItem);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPedido($id = -1) {
		try {
			$pedido = new Pedido();
			$pedido->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$pedido->numero = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($pedido, Modos::select), get_class($pedido)), $pedido);
			}
			return $pedido;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPedido(Pedido $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero))) {
					$this->getPedido($obj->numero);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->detalle as $item) {
				$item->numero = $obj->numero;
				$this->persistir($item);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPedidoItem($numero = -1, $numeroDeItem = -1) {
		try {
			$pedidoItem = new PedidoItem();
			$pedidoItem->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $numeroDeItem))){
				$pedidoItem->numero = $numero;
				$pedidoItem->numeroDeItem = $numeroDeItem;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($pedidoItem, Modos::select), get_class($pedidoItem)), $pedidoItem);
			}
			return $pedidoItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPedidoItem(PedidoItem $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->numeroDeItem))) {
					$this->getPedidoItem($obj->numero, $obj->numeroDeItem);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPermisoPorCaja($idCaja = -1, $idPermiso = -1) {
		try {
			$permisoPorCaja = new PermisoPorCaja();
			$permisoPorCaja->modo = Modos::insert;
			if (Funciones::tieneId(array($idCaja, $idPermiso))){
				$permisoPorCaja->idCaja = $idCaja;
				$permisoPorCaja->idPermiso = $idPermiso;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($permisoPorCaja, Modos::select), get_class($permisoPorCaja)), $permisoPorCaja);
			}
			return $permisoPorCaja;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPermisoPorCaja(PermisoPorCaja $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idCaja, $obj->idPermiso))) {
					$this->getPermisoPorCaja($obj->idCaja, $obj->idPermiso);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPermisoPorUsuarioPorCaja($idCaja = -1, $idUsuario = -1, $idPermiso = -1) {
		try {
			$permisoPorUsuarioPorCaja = new PermisoPorUsuarioPorCaja();
			$permisoPorUsuarioPorCaja->modo = Modos::insert;
			if (Funciones::tieneId(array($idCaja, $idUsuario, $idPermiso))){
				$permisoPorUsuarioPorCaja->idCaja = $idCaja;
				$permisoPorUsuarioPorCaja->idUsuario = $idUsuario;
				$permisoPorUsuarioPorCaja->idPermiso = $idPermiso;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($permisoPorUsuarioPorCaja, Modos::select), get_class($permisoPorUsuarioPorCaja)), $permisoPorUsuarioPorCaja);
			}
			return $permisoPorUsuarioPorCaja;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPermisoPorUsuarioPorCaja(PermisoPorUsuarioPorCaja $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idCaja, $obj->idUsuario, $obj->idPermiso))) {
					$a = $this->getPermisoPorUsuarioPorCaja($obj->idCaja, $obj->idUsuario, $obj->idPermiso);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPersonaGasto($id = -1) {
		try {
			$personaGasto = new PersonaGasto();
			$personaGasto->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$personaGasto->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($personaGasto, Modos::select), get_class($personaGasto)), $personaGasto);
			}
			return $personaGasto;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPersonaGasto(PersonaGasto $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getPersonaGasto($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPersonal($idPersonal = -1) {
		try {
			$personal = new Personal();
			$personal->modo = Modos::insert;
			if (Funciones::tieneId(array($idPersonal))){
				$personal->idPersonal = $idPersonal;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($personal, Modos::select), get_class($personal)), $personal);
			}
			return $personal;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPersonal(Personal $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idPersonal))) {
					$this->getPersonal($obj->idPersonal);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->idPersonal = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPersonalOperador($id = -1) {
		try {
			$personalOperador = new PersonalOperador();
			$personalOperador->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$personalOperador->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($personalOperador, Modos::select), get_class($personalOperador)), $personalOperador);
			}
			return $personalOperador;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPersonalOperador(PersonalOperador $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getPersonalOperador($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->idPersonal = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getLoteDeProduccion($id = -1) {
		try {
			$loteDeProduccion = new LoteDeProduccion();
			$loteDeProduccion->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$loteDeProduccion->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($loteDeProduccion, Modos::select), get_class($loteDeProduccion)), $loteDeProduccion);
			}
			return $loteDeProduccion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setLoteDeProduccion(LoteDeProduccion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getLoteDeProduccion($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
            //Empieza la transacción
            $this->beginTransaction();
            $this->push($obj);
            foreach ($obj->ordenesDeFabricacion as $item) {
                $item->loteDeProduccion = $obj;
                $this->persistir($item);
            }
            $this->commitTransaction();
            //Termina la transacción
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPredespacho($pedidoNumero = -1, $pedidoNumeroDeItem = -1) {
		try {
			$predespacho = new Predespacho();
			$predespacho->modo = Modos::insert;
			if (Funciones::tieneId(array($pedidoNumero, $pedidoNumeroDeItem))){
				$predespacho->pedidoNumero = $pedidoNumero;
				$predespacho->pedidoNumeroDeItem = $pedidoNumeroDeItem;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($predespacho, Modos::select), get_class($predespacho)), $predespacho);
			}
			return $predespacho;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPredespacho(Predespacho $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->pedidoNumero, $obj->pedidoNumeroDeItem))) {
					$this->getPredespacho($obj->pedidoNumero, $obj->pedidoNumeroDeItem);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPrestamo($numero = -1, $idEmpresa = -1) {
		try {
			$prestamo = new Prestamo();
			$prestamo->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $idEmpresa))){
				$prestamo->empresa = $idEmpresa;
				$prestamo->numero = $numero;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($prestamo, Modos::select), get_class($prestamo)), $prestamo);
			}
			return $prestamo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPrestamo(Prestamo $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getPrestamo($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPresupuesto($id = -1) {
		try {
			$presupuesto = new Presupuesto();
			$presupuesto->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$presupuesto->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($presupuesto, Modos::select), get_class($presupuesto)), $presupuesto);
			}
			return $presupuesto;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPresupuesto(Presupuesto $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getPresupuesto($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->detalle as $item) {
				$item->presupuesto = $obj;
				$this->persistir($item);
			}
			$this->commitTransaction();
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPresupuestoItem($id = -1, $nroDetalle = -1) {
		try {
			$remitoItem = new PresupuestoItem();
			$remitoItem->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$remitoItem->idPresupuesto = $id;
				$remitoItem->numeroDeItem = $nroDetalle;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($remitoItem, Modos::select), get_class($remitoItem)), $remitoItem);
			}
			return $remitoItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPresupuestoItem(PresupuestoItem $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idPresupuesto, $obj->numeroDeItem))) {
					$this->getPresupuestoItem($obj->idPresupuesto, $obj->numeroDeItem);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numeroDeItem = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getPresupuestoOrdenCompra($id = -1) {
		try {
			$PresupuestoOrdenCompra = new PresupuestoOrdenCompra();
			$PresupuestoOrdenCompra->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$PresupuestoOrdenCompra->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($PresupuestoOrdenCompra, Modos::select), get_class($PresupuestoOrdenCompra)), $PresupuestoOrdenCompra);
			}
			return $PresupuestoOrdenCompra;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setPresupuestoOrdenCompra(PresupuestoOrdenCompra $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getPresupuestoOrdenCompra($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getProveedor($idProveedor = -1) {
		try {
			$proveedor = new Proveedor();
			$proveedor->modo = Modos::insert;
			if (Funciones::tieneId(array($idProveedor))){
				$proveedor->id = $idProveedor;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($proveedor, Modos::select), get_class($proveedor)), $proveedor);
			}
			return $proveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setProveedor(Proveedor $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getProveedor($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getProveedorMateriaPrima($idProveedor = -1, $idMaterial = -1, $idColor = -1) {
		try {
			$proveedor = new ProveedorMateriaPrima();
			$proveedor->modo = Modos::insert;
			if (Funciones::tieneId(array($idProveedor))){
				$proveedor->idProveedor = $idProveedor;
				$proveedor->idMaterial = $idMaterial;
				$proveedor->idColor = $idColor;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($proveedor, Modos::select), get_class($proveedor)), $proveedor);
			}
			return $proveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setProveedorMateriaPrima(ProveedorMateriaPrima $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idProveedor, $obj->idMaterial, $obj->idColor))) {
					$this->getProveedorMateriaPrima($obj->idProveedor, $obj->idMaterial, $obj->idColor);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getProveedorTodos($id = -1) {
		try {
			$proveedorTodos = new ProveedorTodos();
			$proveedorTodos->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$proveedorTodos->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($proveedorTodos, Modos::select), get_class($proveedorTodos)), $proveedorTodos);
			}
			return $proveedorTodos;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setProveedorTodos(ProveedorTodos $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getProveedorTodos($obj->id);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getProvincia($idPais = -1, $id = -1) {
		try {
			$provincia = new Provincia();
			$provincia->modo = Modos::insert;
			if (Funciones::tieneId(array($idPais, $id))){
				$provincia->idPais = $idPais;
				$provincia->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($provincia, Modos::select), get_class($provincia)), $provincia);
			}
			return $provincia;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setProvincia(Provincia $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idPais, $obj->id))) {
					$this->getProvincia($obj->idPais, $obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRangoTalle($id = -1) {
		try {
			$rangoTalle = new RangoTalle();
			$rangoTalle->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$rangoTalle->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($rangoTalle, Modos::select), get_class($rangoTalle)), $rangoTalle);
			}
			return $rangoTalle;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRangoTalle(RangoTalle $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getRangoTalle($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRechazoChequeCabecera($numero = -1, $empresa = -1) {
		try {
			$depositoChequeCabecera = new RechazoChequeCabecera();
			$depositoChequeCabecera->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa))){
				$depositoChequeCabecera->numero = $numero;
				$depositoChequeCabecera->empresa = $empresa;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($depositoChequeCabecera, Modos::select), get_class($depositoChequeCabecera)), $depositoChequeCabecera);
			}
			return $depositoChequeCabecera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRechazoChequeCabecera(RechazoChequeCabecera $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getRechazoChequeCabecera($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRechazoCheque($numero = -1, $empresa = -1, $entradaSalida = -1) {
		try {
			$depositoCheque = new RechazoCheque();
			$depositoCheque->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa, $entradaSalida))){
				$depositoCheque->numero = $numero;
				$depositoCheque->empresa = $empresa;
				$depositoCheque->entradaSalida = $entradaSalida;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($depositoCheque, Modos::select), get_class($depositoCheque)), $depositoCheque);
			}
			return $depositoCheque;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRechazoCheque(RechazoCheque $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa, $obj->entradaSalida))) {
					$this->getRechazoCheque($obj->numero, $obj->empresa, $obj->entradaSalida);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRecibo($numero = -1, $idEmpresa = -1) {
		try {
			$recibo = new Recibo();
			$recibo->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $idEmpresa))){
				$recibo->empresa = $idEmpresa;
				$recibo->numero = $numero;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($recibo, Modos::select), get_class($recibo)), $recibo);
			}
			return $recibo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRecibo(Recibo $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getRecibo($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getReingresoChequeCartera($numero = -1, $idEmpresa = -1) {
		try {
			$reingresoChequeCartera = new ReingresoChequeCartera();
			$reingresoChequeCartera->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $idEmpresa))){
				$reingresoChequeCartera->empresa = $idEmpresa;
				$reingresoChequeCartera->numero = $numero;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($reingresoChequeCartera, Modos::select), get_class($reingresoChequeCartera)), $reingresoChequeCartera);
			}
			return $reingresoChequeCartera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setReingresoChequeCartera(ReingresoChequeCartera $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getReingresoChequeCartera($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRemito($idEmpresa = -1, $idRemito = -1, $letraRemito = -1) {
		try {
			$remito = new Remito();
			$remito->modo = Modos::insert;
			if (Funciones::tieneId(array($idEmpresa, $idRemito, $letraRemito))){
				$remito->empresa = $idEmpresa;
				$remito->numero = $idRemito;
				$remito->letra = $letraRemito;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($remito, Modos::select), get_class($remito)), $remito);
			}
			return $remito;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRemito(Remito $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->empresa, $obj->numero, $obj->letra))) {
					$this->getRemito($obj->empresa, $obj->numero, $obj->letra);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRemitoPorOrdenDeCompra($id = -1) {
		try {
			$remitoPorOrdenDeCompra = new RemitoPorOrdenDeCompra();
			$remitoPorOrdenDeCompra->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$remitoPorOrdenDeCompra->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($remitoPorOrdenDeCompra, Modos::select), get_class($remitoPorOrdenDeCompra)), $remitoPorOrdenDeCompra);
			}
			return $remitoPorOrdenDeCompra;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRemitoPorOrdenDeCompra(RemitoPorOrdenDeCompra $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getRemitoPorOrdenDeCompra($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRemitoProveedor($id = -1) {
		try {
			$remito = new RemitoProveedor();
			$remito->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$remito->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($remito, Modos::select), get_class($remito)), $remito);
			}
			return $remito;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRemitoProveedor(RemitoProveedor $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getRemitoProveedor($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->detalle as $item) {
				$item->remitoProveedor = $obj;
				$this->persistir($item);
			}
			$this->commitTransaction();
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRemitoProveedorItem($id = -1, $nroDetalle = -1) {
		try {
			$remitoItem = new RemitoProveedorItem();
			$remitoItem->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$remitoItem->idRemitoProveedor = $id;
				$remitoItem->numeroDeItem = $nroDetalle;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($remitoItem, Modos::select), get_class($remitoItem)), $remitoItem);
			}
			return $remitoItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRemitoProveedorItem(RemitoProveedorItem $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idRemitoProveedor, $obj->numeroDeItem))) {
					$this->getRemitoProveedorItem($obj->idRemitoProveedor, $obj->numeroDeItem);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numeroDeItem = $this->getNextId($obj);
			$this->push($obj);
			foreach ($obj->remitosPorOrdenesDeCompra as $item) {
				$item->remitoProveedor = $obj->remitoProveedor;
				$item->numeroDeItemRemitoProveedor = $obj->numeroDeItem;
				$this->persistir($item);
			}
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRendicionGastos($numero = -1, $empresa = -1) {
		try {
			$rendicionGastos = new RendicionGastos();
			$rendicionGastos->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa))){
				$rendicionGastos->numero = $numero;
				$rendicionGastos->empresa = $empresa;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($rendicionGastos, Modos::select), get_class($rendicionGastos)), $rendicionGastos);
			}
			return $rendicionGastos;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRendicionGastos(RendicionGastos $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getRendicionGastos($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRetencionEfectuada($id = -1) {
		try {
			$retencionSalida = new RetencionEfectuada();
			$retencionSalida->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$retencionSalida->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($retencionSalida, Modos::select), get_class($retencionSalida)), $retencionSalida);
			}
			return $retencionSalida;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRetencionEfectuada(RetencionEfectuada $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getRetencionEfectuada($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRetencionEscala($ano = -1, $mes = -1, $item = -1) {
		try {
			$retencionEscala = new RetencionEscala();
			$retencionEscala->modo = Modos::insert;
			if (Funciones::tieneId(array($ano, $mes, $item))){
				$retencionEscala->ano = $ano;
				$retencionEscala->mes = $mes;
				$retencionEscala->item = $item;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($retencionEscala, Modos::select), get_class($retencionEscala)), $retencionEscala);
			}
			return $retencionEscala;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRetencionEscala(RetencionEscala $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->ano, $obj->mes, $obj->item))) {
					$this->getRetencionEscala($obj->ano, $obj->mes, $obj->item);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			//if ($obj->modo == Modos::insert)
			//	$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRetencionSufrida($id = -1) {
		try {
			$retencionEntrada = new RetencionSufrida();
			$retencionEntrada->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$retencionEntrada->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($retencionEntrada, Modos::select), get_class($retencionEntrada)), $retencionEntrada);
			}
			return $retencionEntrada;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRetencionSufrida(RetencionSufrida $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getRetencionSufrida($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRetencionTabla($ano = -1, $mes = -1, $item = -1) {
		try {
			$retencionTabla = new RetencionTabla();
			$retencionTabla->modo = Modos::insert;
			if (Funciones::tieneId(array($ano, $mes, $item))){
				$retencionTabla->ano = $ano;
				$retencionTabla->mes = $mes;
				$retencionTabla->item = $item;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($retencionTabla, Modos::select), get_class($retencionTabla)), $retencionTabla);
			}
			return $retencionTabla;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRetencionTabla(RetencionTabla $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->ano, $obj->mes, $obj->item))) {
					$this->getRetencionTabla($obj->ano, $obj->mes, $obj->item);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			//if ($obj->modo == Modos::insert)
			//	$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRetiroSocio($numero = -1, $idEmpresa = -1) {
		try {
			$retiroSocio = new RetiroSocio();
			$retiroSocio->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $idEmpresa))){
				$retiroSocio->empresa = $idEmpresa;
				$retiroSocio->numero = $numero;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($retiroSocio, Modos::select), get_class($retiroSocio)), $retiroSocio);
			}
			return $retiroSocio;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRetiroSocio(RetiroSocio $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getRetiroSocio($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRol($id = -1) {
		try {
			$rol = new Rol();
			$rol->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$rol->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($rol, Modos::select), get_class($rol)), $rol);
			}
			return $rol;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRol(Rol $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getRol($obj->id);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->funcionalidades as $fun) {
				$fun->idRol = $obj->id;
				$this->persistir($fun);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRolPorTipoNotificacion($idTipoNotificacion = -1, $id = -1) {
		try {
			$rolPorTipoNotificacion = new RolPorTipoNotificacion();
			$rolPorTipoNotificacion->modo = Modos::insert;
			if (Funciones::tieneId(array($idTipoNotificacion, $id))){
				$rolPorTipoNotificacion->idTipoNotificacion = $idTipoNotificacion;
				$rolPorTipoNotificacion->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($rolPorTipoNotificacion, Modos::select), get_class($rolPorTipoNotificacion)), $rolPorTipoNotificacion);
			}
			return $rolPorTipoNotificacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRolPorTipoNotificacion(RolPorTipoNotificacion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idTipoNotificacion, $obj->id))) {
					$this->getRolPorTipoNotificacion($obj->idTipoNotificacion, $obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRolPorUsuario($idUsuario = -1, $id = -1) {
		try {
			$rolPorUsuario = new RolPorUsuario();
			$rolPorUsuario->modo = Modos::insert;
			if (Funciones::tieneId(array($idUsuario, $id))){
				$rolPorUsuario->idUsuario = $idUsuario;
				$rolPorUsuario->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($rolPorUsuario, Modos::select), get_class($rolPorUsuario)), $rolPorUsuario);
			}
			return $rolPorUsuario;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRolPorUsuario(RolPorUsuario $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idUsuario, $obj->id))) {
					$this->getRolPorUsuario($obj->idUsuario, $obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRubro($id = -1) {
		try {
			$rubro = new Rubro();
			$rubro->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$rubro->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($rubro, Modos::select), get_class($rubro)), $rubro);
			}
			return $rubro;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRubro(Rubro $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getRubro($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRubroIva($id = -1) {
		try {
			$rubroIva = new RubroIva();
			$rubroIva->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$rubroIva->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($rubroIva, Modos::select), get_class($rubroIva)), $rubroIva);
			}
			return $rubroIva;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRubroIva(RubroIva $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getRubroIva($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRutaProduccion($id = -1) {
		try {
			$rutaProduccion = new RutaProduccion();
			$rutaProduccion->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$rutaProduccion->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($rutaProduccion, Modos::select), get_class($rutaProduccion)), $rutaProduccion);
			}
			return $rutaProduccion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRutaProduccion(RutaProduccion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getRutaProduccion($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getRutaProduccionPaso($idRutaProduccion = -1, $nroPaso = -1) {
		try {
			$rutaProduccionPaso = new RutaProduccionPaso();
			$rutaProduccionPaso->modo = Modos::insert;
			if (Funciones::tieneId(array($idRutaProduccion, $nroPaso))){
				$rutaProduccionPaso->idRutaProduccion = $idRutaProduccion;
				$rutaProduccionPaso->nroPaso = $nroPaso;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($rutaProduccionPaso, Modos::select), get_class($rutaProduccionPaso)), $rutaProduccionPaso);
			}
			return $rutaProduccionPaso;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setRutaProduccionPaso(RutaProduccionPaso $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idRutaProduccion, $obj->nroPaso))) {
					$this->getRutaProduccionPaso($obj->idRutaProduccion, $obj->nroPaso);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getSeccionProduccion($id = -1) {
		try {
			$seccionProduccion = new SeccionProduccion();
			$seccionProduccion->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$seccionProduccion->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($seccionProduccion, Modos::select), get_class($seccionProduccion)), $seccionProduccion);
			}
			return $seccionProduccion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
    private function setSeccionProduccion(SeccionProduccion $obj) {
        $existe = false;
        try {
            $mutex = new Mutex(Funciones::getType($obj));
            $mutex->lock();
            try {
                if (Funciones::tieneId(array($obj->id))) {
                    $this->getSeccionProduccion($obj->id);
                    $existe = true;
                }
            } catch (Exception $ex) {
                $existe = false;
            }
            $this->puedePersistir($existe, $obj->modo);
            if ($obj->modo == Modos::insert)
                $obj->id = $this->getNextId($obj);
            //Empieza la transacci�n
            $this->beginTransaction();
            $this->push($obj);
            foreach ($obj->almacenes as $item) {
                $item->seccionProduccion->id = $obj->id;
                $this->persistir($item);
            }
            $this->commitTransaction();
            //Termina la transacci�n
            $mutex->unlock();
        } catch (Exception $ex) {
            $mutex->unlock();
            throw $ex;
        }
    }
	public	function getSocio($id = -1) {
		try {
			$socio = new Socio();
			$socio->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$socio->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($socio, Modos::select), get_class($socio)), $socio);
			}
			return $socio;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setSocio(Socio $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getSocio($obj->id);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getSolicitudDeFondos($id = -1) {
		try {
			$solicitudDeFondos = new SolicitudDeFondos();
			$solicitudDeFondos->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$solicitudDeFondos->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($solicitudDeFondos, Modos::select), get_class($solicitudDeFondos)), $solicitudDeFondos);
			}
			return $solicitudDeFondos;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setSolicitudDeFondos(SolicitudDeFondos $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getSolicitudDeFondos($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);

			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->detalle as $unDetalle) {
				$this->persistir($unDetalle);
			}
			$this->commitTransaction();
			//Termina la transacci�n

			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getSolicitudDeFondosItem($id = -1) {
		try {
			$solicitudDeFondosItem = new SolicitudDeFondosItem();
			$solicitudDeFondosItem->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$solicitudDeFondosItem->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($solicitudDeFondosItem, Modos::select), get_class($solicitudDeFondosItem)), $solicitudDeFondosItem);
			}
			return $solicitudDeFondosItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setSolicitudDeFondosItem(SolicitudDeFondosItem $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getSolicitudDeFondosItem($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getStock($idAlmacen = -1, $idArticulo = -1, $idColorPorArticulo = -1) {
		try {
			$stock = new Stock();
			$stock->modo = Modos::insert;
			if (Funciones::tieneId(array($idAlmacen, $idArticulo, $idColorPorArticulo))){
				$stock->idAlmacen = $idAlmacen;
				$stock->idArticulo = $idArticulo;
				$stock->idColorPorArticulo = $idColorPorArticulo;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($stock, Modos::select), get_class($stock)), $stock);
			}
			return $stock;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setStock(Stock $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idAlmacen, $obj->idArticulo, $obj->idColorPorArticulo))) {
					$this->getStock($obj->idAlmacen, $obj->idArticulo, $obj->idColorPorArticulo);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getStockMP($idAlmacen = -1, $idMaterial = -1, $idColor = -1) {
		try {
			$stockMP = new StockMP();
			$stockMP->modo = Modos::insert;
			if (Funciones::tieneId(array($idAlmacen, $idMaterial, $idColor))){
				$stockMP->idAlmacen = $idAlmacen;
				$stockMP->idMaterial = $idMaterial;
				$stockMP->idColorMateriaPrima = $idColor;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($stockMP, Modos::select), get_class($stockMP)), $stockMP);
			}
			return $stockMP;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setStockMP(StockMP $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idAlmacen, $obj->idMaterial, $obj->idColorMateriaPrima))) {
					$this->getStockMP($obj->idAlmacen, $obj->idMaterial, $obj->idColorMateriaPrima);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getSucursal($idCliente = -1, $id = -1) {
		try {
			$sucursal = new Sucursal();
			$sucursal->modo = Modos::insert;
			if (Funciones::tieneId(array($idCliente, $id))){
				$sucursal->idCliente = $idCliente;
				$sucursal->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($sucursal, Modos::select), get_class($sucursal)), $sucursal);
			}
			return $sucursal;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setSucursal(Sucursal $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idCliente, $obj->id))) {
					$this->getSucursal($obj->idCliente, $obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getTareaProduccion($idOrdenDeFabricacion = -1, $numero = -1) {
		try {
			$tareaProduccion = new TareaProduccion();
			$tareaProduccion->modo = Modos::insert;
			if (Funciones::tieneId(array($idOrdenDeFabricacion, $numero))){
				$tareaProduccion->idOrdenDeFabricacion = $idOrdenDeFabricacion;
				$tareaProduccion->numero = $numero;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($tareaProduccion, Modos::select), get_class($tareaProduccion)), $tareaProduccion);
			}
			return $tareaProduccion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setTareaProduccion(TareaProduccion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idOrdenDeFabricacion, $obj->numero))) {
					$this->getTareaProduccion($obj->idOrdenDeFabricacion, $obj->numero);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->detalle as $item) {
				$item->ordenDeFabricacion = $obj->ordenDeFabricacion;
				$item->numeroTarea = $obj->numero;
				$this->persistir($item);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getTareaProduccionItem($idOrdenDeFabricacion = -1, $numero = -1, $idSeccionProduccion = -1) {
		try {
			$tareaProduccionItem = new TareaProduccionItem();
			$tareaProduccionItem->modo = Modos::insert;
			if (Funciones::tieneId(array($idOrdenDeFabricacion, $numero, $idSeccionProduccion))){
				$tareaProduccionItem->idOrdenDeFabricacion = $idOrdenDeFabricacion;
				$tareaProduccionItem->numeroTarea = $numero;
				$tareaProduccionItem->idSeccionProduccion = $idSeccionProduccion;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($tareaProduccionItem, Modos::select), get_class($tareaProduccionItem)), $tareaProduccionItem);
			}
			return $tareaProduccionItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setTareaProduccionItem(TareaProduccionItem $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idOrdenDeFabricacion, $obj->numeroTarea, $obj->idSeccionProduccion))) {
					$this->getTareaProduccionItem($obj->idOrdenDeFabricacion, $obj->numeroTarea, $obj->idSeccionProduccion);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getTemporada($id = -1) {
		try {
			$temporada = new Temporada();
			$temporada->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$temporada->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($temporada, Modos::select), get_class($temporada)), $temporada);
			}
			return $temporada;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setTemporada(Temporada $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getTemporada($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getTipoFactura($id = -1) {
		try {
			$impuesto = new TipoFactura();
			$impuesto->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$impuesto->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($impuesto, Modos::select), get_class($impuesto)), $impuesto);
			}
			return $impuesto;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setTipoFactura(TipoFactura $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getTipoFactura($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getTipoNotificacion($id = -1) {
		try {
			$tipoNotificacion = new TipoNotificacion();
			$tipoNotificacion->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$tipoNotificacion->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($tipoNotificacion, Modos::select), get_class($tipoNotificacion)), $tipoNotificacion);
			}
			return $tipoNotificacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setTipoNotificacion(TipoNotificacion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getTipoNotificacion($obj->id);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->usuarios as $usu) {
				$usu->idTipoNotificacion = $obj->id;
				$this->persistir($usu);
			}
			foreach ($obj->roles as $rol) {
				$usu->idTipoNotificacion = $obj->id;
				$this->persistir($rol);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getTipoPeriodoFiscal($id = -1) {
		try {
			$tipoPeriodoFiscal = new TipoPeriodoFiscal();
			$tipoPeriodoFiscal->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$tipoPeriodoFiscal->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($tipoPeriodoFiscal, Modos::select), get_class($tipoPeriodoFiscal)), $tipoPeriodoFiscal);
			}
			return $tipoPeriodoFiscal;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setTipoPeriodoFiscal(TipoPeriodoFiscal $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getTipoPeriodoFiscal($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getTipoProductoStock($id = -1) {
		try {
			$tipoProductoStock = new TipoProductoStock();
			$tipoProductoStock->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$tipoProductoStock->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($tipoProductoStock, Modos::select), get_class($tipoProductoStock)), $tipoProductoStock);
			}
			return $tipoProductoStock;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setTipoProductoStock(TipoProductoStock $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getTipoProductoStock($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getTipoProveedor($id = -1) {
		try {
			$tipoProveedor = new TipoProveedor();
			$tipoProveedor->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$tipoProveedor->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($tipoProveedor, Modos::select), get_class($tipoProveedor)), $tipoProveedor);
			}
			return $tipoProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setTipoProveedor(TipoProveedor $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getTipoProveedor($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getTipoRetencion($id = -1) {
		try {
			$tipoProveedor = new TipoRetencion();
			$tipoProveedor->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$tipoProveedor->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($tipoProveedor, Modos::select), get_class($tipoProveedor)), $tipoProveedor);
			}
			return $tipoProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setTipoRetencion(TipoRetencion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getTipoRetencion($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getTransporte($id = -1) {
		try {
			$transporte = new Transporte();
			$transporte->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$transporte->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($transporte, Modos::select), get_class($transporte)), $transporte);
			}
			return $transporte;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setTransporte(Transporte $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getTransporte($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getTransferenciaBancariaOperacion($id = -1) {
		try {
			$transfBancariaOperacion = new TransferenciaBancariaOperacion();
			$transfBancariaOperacion->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$transfBancariaOperacion->numero = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($transfBancariaOperacion, Modos::select), get_class($transfBancariaOperacion)), $transfBancariaOperacion);
			}
			return $transfBancariaOperacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setTransferenciaBancariaOperacion(TransferenciaBancariaOperacion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero))) {
					$this->getTransferenciaBancariaOperacion($obj->numero);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getTransferenciaBancariaImporte($id = -1) {
		try {
			$transfBancariaImporte = new TransferenciaBancariaImporte();
			$transfBancariaImporte->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$transfBancariaImporte->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($transfBancariaImporte, Modos::select), get_class($transfBancariaImporte)), $transfBancariaImporte);
			}
			return $transfBancariaImporte;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setTransferenciaBancariaImporte(TransferenciaBancariaImporte $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getTransferenciaBancariaImporte($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getTransferenciaInterna($numero = -1, $empresa = -1, $entradaSalida = -1) {
		try {
			$transferenciaInterna = new TransferenciaInterna();
			$transferenciaInterna->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa, $entradaSalida))){
				$transferenciaInterna->numero = $numero;
				$transferenciaInterna->empresa = $empresa;
				$transferenciaInterna->entradaSalida = $entradaSalida;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($transferenciaInterna, Modos::select), get_class($transferenciaInterna)), $transferenciaInterna);
			}
			return $transferenciaInterna;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setTransferenciaInterna(TransferenciaInterna $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa, $obj->entradaSalida))) {
					$this->getTransferenciaInterna($obj->numero, $obj->empresa, $obj->entradaSalida);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getTransferenciaInternaCabecera($numero = -1, $empresa = -1) {
		try {
			$transferenciaInternaCabecera = new TransferenciaInternaCabecera();
			$transferenciaInternaCabecera->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa))){
				$transferenciaInternaCabecera->numero = $numero;
				$transferenciaInternaCabecera->empresa = $empresa;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($transferenciaInternaCabecera, Modos::select), get_class($transferenciaInternaCabecera)), $transferenciaInternaCabecera);
			}
			return $transferenciaInternaCabecera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setTransferenciaInternaCabecera(TransferenciaInternaCabecera $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getTransferenciaInternaCabecera($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getUnidadDeMedida($id = -1) {
		try {
			$unidadDeMedida = new UnidadDeMedida();
			$unidadDeMedida->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$unidadDeMedida->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($unidadDeMedida, Modos::select), get_class($unidadDeMedida)), $unidadDeMedida);
			}
			return $unidadDeMedida;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setUnidadDeMedida(UnidadDeMedida $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getUnidadDeMedida($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getUsuario($id = -1) {
		try {
			$usuario= new Usuario();
			$usuario->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$usuario->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($usuario, Modos::select), get_class($usuario)), $usuario);
			}
			return $usuario;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setUsuario(Usuario $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getUsuario($obj->id);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->roles as $rol) {
				$this->persistir($rol);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getUsuarioCalzado($id = -1) {
		try {
			$usuarioCalzado = new UsuarioCalzado();
			$usuarioCalzado->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$usuarioCalzado->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($usuarioCalzado, Modos::select), get_class($usuarioCalzado)), $usuarioCalzado);
			}
			return $usuarioCalzado;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setUsuarioCalzado(UsuarioCalzado $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getUsuarioCalzado($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getUsuarioLogin($id = -1) {
		try {
			$usuarioLogin= new UsuarioLogin();
			$usuarioLogin->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$usuarioLogin->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($usuarioLogin, Modos::select), get_class($usuarioLogin)), $usuarioLogin);
			}
			return $usuarioLogin;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setUsuarioLogin(UsuarioLogin $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getUsuarioLogin($obj->id);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			//Empieza la transacci�n
			$this->beginTransaction();
			$this->push($obj);
			foreach ($obj->roles as $rol) {
				$this->persistir($rol);
			}
			$this->commitTransaction();
			//Termina la transacci�n
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getUsuarioPorAlmacen($id = -1, $idAlmacen = -1) {
		try {
			$usuarioPorAlmacen = new UsuarioPorAlmacen();
			$usuarioPorAlmacen->modo = Modos::insert;
			if (Funciones::tieneId(array($id, $idAlmacen))){
				$usuarioPorAlmacen->id = $id;
				$usuarioPorAlmacen->idAlmacen = $idAlmacen;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($usuarioPorAlmacen, Modos::select), get_class($usuarioPorAlmacen)), $usuarioPorAlmacen);
			}
			return $usuarioPorAlmacen;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setUsuarioPorAlmacen(UsuarioPorAlmacen $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id, $obj->idAlmacen))) {
					$this->getUsuarioPorAlmacen($obj->id, $obj->idAlmacen);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getUsuarioPorAreaEmpresa($id = -1, $idAreaEmpresa = -1) {
		try {
			$usuarioPorAreaEmpresa = new UsuarioPorAreaEmpresa();
			$usuarioPorAreaEmpresa->modo = Modos::insert;
			if (Funciones::tieneId(array($id, $idAreaEmpresa))){
				$usuarioPorAreaEmpresa->id = $id;
				$usuarioPorAreaEmpresa->idAreaEmpresa = $idAreaEmpresa;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($usuarioPorAreaEmpresa, Modos::select), get_class($usuarioPorAreaEmpresa)), $usuarioPorAreaEmpresa);
			}
			return $usuarioPorAreaEmpresa;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setUsuarioPorAreaEmpresa(UsuarioPorAreaEmpresa $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id, $obj->idAreaEmpresa))) {
					$this->getUsuarioPorAreaEmpresa($obj->id, $obj->idAreaEmpresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getUsuarioPorCaja($idCaja = -1, $idUsuario = -1) {
		try {
			$usuarioPorCaja = new UsuarioPorCaja();
			$usuarioPorCaja->modo = Modos::insert;
			if (Funciones::tieneId(array($idCaja, $idUsuario))){
				$usuarioPorCaja->idCaja = $idCaja;
				$usuarioPorCaja->idUsuario = $idUsuario;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($usuarioPorCaja, Modos::select), get_class($usuarioPorCaja)), $usuarioPorCaja);
			}
			return $usuarioPorCaja;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setUsuarioPorCaja(UsuarioPorCaja $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idCaja, $obj->idUsuario))) {
					$this->getUsuarioPorTipoNotificacion($obj->idCaja, $obj->idUsuario);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
    public	function getUsuarioPorSeccionProduccion($id = -1, $idSeccionProduccion = -1) {
        try {
            $usuarioPorSeccionProduccion = new UsuarioPorSeccionProduccion();
            $usuarioPorSeccionProduccion->modo = Modos::insert;
            if (Funciones::tieneId(array($id, $idSeccionProduccion))){
                $usuarioPorSeccionProduccion->id = $id;
                $usuarioPorSeccionProduccion->idSeccionProduccion = $idSeccionProduccion;
                $this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($usuarioPorSeccionProduccion, Modos::select), get_class($usuarioPorSeccionProduccion)), $usuarioPorSeccionProduccion);
            }
            return $usuarioPorSeccionProduccion;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    private function setUsuarioPorSeccionProduccion(UsuarioPorSeccionProduccion $obj) {
        $existe = false;
        try {
            $mutex = new Mutex(Funciones::getType($obj));
            $mutex->lock();
            try {
                if (Funciones::tieneId(array($obj->id, $obj->idSeccionProduccion))) {
                    $this->getUsuarioPorSeccionProduccion($obj->id, $obj->idSeccionProduccion);
                    $existe = true;
                }
            } catch (Exception $ex) {
                $existe = false;
            }
            $this->puedePersistir($existe, $obj->modo);
            $this->push($obj);
            $mutex->unlock();
        } catch (Exception $ex) {
            $mutex->unlock();
            throw $ex;
        }
    }
	public	function getUsuarioPorTipoNotificacion($idTipoNotificacion = -1, $idUsuario = -1) {
		try {
			$usuarioPorTipoNotificacion = new UsuarioPorTipoNotificacion();
			$usuarioPorTipoNotificacion->modo = Modos::insert;
			if (Funciones::tieneId(array($idTipoNotificacion, $idUsuario))){
				$usuarioPorTipoNotificacion->idTipoNotificacion = $idTipoNotificacion;
				$usuarioPorTipoNotificacion->id = $idUsuario;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($usuarioPorTipoNotificacion, Modos::select), get_class($usuarioPorTipoNotificacion)), $usuarioPorTipoNotificacion);
			}
			return $usuarioPorTipoNotificacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setUsuarioPorTipoNotificacion(UsuarioPorTipoNotificacion $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->idTipoNotificacion, $obj->id))) {
					$this->getUsuarioPorTipoNotificacion($obj->idTipoNotificacion, $obj->id);
					$existe = true;
				}
			} catch (Exception $eex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getVendedor($id = -1) {
		try {
			$vendedor= new Vendedor();
			$vendedor->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$vendedor->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($vendedor, Modos::select), get_class($vendedor)), $vendedor);
			}
			return $vendedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setVendedor(Vendedor $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getVendedor($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->idPersonal = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getVentaCheques($numero = -1, $empresa = -1, $entradaSalida = -1) {
		try {
			$depositoCheque = new VentaCheques();
			$depositoCheque->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa, $entradaSalida))){
				$depositoCheque->numero = $numero;
				$depositoCheque->empresa = $empresa;
				$depositoCheque->entradaSalida = $entradaSalida;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($depositoCheque, Modos::select), get_class($depositoCheque)), $depositoCheque);
			}
			return $depositoCheque;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setVentaCheques(VentaCheques $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa, $obj->entradaSalida))) {
					$this->getVentaCheques($obj->numero, $obj->empresa, $obj->entradaSalida);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			//if ($obj->modo == Modos::insert)
			//	$obj->numero = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getVentaChequesCabecera($numero = -1, $empresa = -1) {
		try {
			$depositoChequeCabecera = new VentaChequesCabecera();
			$depositoChequeCabecera->modo = Modos::insert;
			if (Funciones::tieneId(array($numero, $empresa))){
				$depositoChequeCabecera->numero = $numero;
				$depositoChequeCabecera->empresa = $empresa;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($depositoChequeCabecera, Modos::select), get_class($depositoChequeCabecera)), $depositoChequeCabecera);
			}
			return $depositoChequeCabecera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setVentaChequesCabecera(VentaChequesCabecera $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->numero, $obj->empresa))) {
					$this->getVentaChequesCabecera($obj->numero, $obj->empresa);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->numero = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getVentaChequesTemporal($id = -1) {
		try {
			$ventaChequesTemporal = new VentaChequesTemporal();
			$ventaChequesTemporal->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$ventaChequesTemporal->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($ventaChequesTemporal, Modos::select), get_class($ventaChequesTemporal)), $ventaChequesTemporal);
			}
			return $ventaChequesTemporal;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setVentaChequesTemporal(VentaChequesTemporal $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getVentaChequesTemporal($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getZona($id = -1) {
		try {
			$zona = new Zona();
			$zona->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$zona->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($zona, Modos::select), get_class($zona)), $zona);
			}
			return $zona;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setZona(Zona $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getZona($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
	public	function getZonaTransporte($id = -1) {
		try {
			$zonaTransporte = new ZonaTransporte();
			$zonaTransporte->modo = Modos::insert;
			if (Funciones::tieneId(array($id))){
				$zonaTransporte->id = $id;
				$this->mapper->fillObject(Datos::EjecutarSQLItem($this->mapper->getQueryInstancia($zonaTransporte, Modos::select), get_class($zonaTransporte)), $zonaTransporte);
			}
			return $zonaTransporte;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function setZonaTransporte(ZonaTransporte $obj) {
		$existe = false;
		try {
			$mutex = new Mutex(Funciones::getType($obj));
			$mutex->lock();
			try {
				if (Funciones::tieneId(array($obj->id))) {
					$this->getZonaTransporte($obj->id);
					$existe = true;
				}
			} catch (Exception $ex) {
				$existe = false;
			}
			$this->puedePersistir($existe, $obj->modo);
			if ($obj->modo == Modos::insert)
				$obj->id = $this->getNextId($obj);
			$this->push($obj);
			$mutex->unlock();
		} catch (Exception $ex) {
			$mutex->unlock();
			throw $ex;
		}
	}
}


