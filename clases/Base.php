<?php

/**
 * @property int    $modo
 */

class Base extends BasePhp {
	private $_modo;

	public $__CACHE_TIME = false; // -1 is default, 0 is forever, and false is NO-CACHE

    protected $__table;
    //protected $__primaryKey = array('id'); // No se puede descomentar esto porque falla el getPK (reconoce que SIEMPRE existe la property "_primaryKey")
    protected $__softDelete = true;
    protected $__autoIncrement = true;

    /**
     * @var array
     *
     * Es un array de mappings de los atributos con la DB. Además, tiene cierta inteligencia para reconocer common attributes (usuarios, fechas).
     * array(
     *   'nombre',
     *   'descripcionArticulo', // Lo transforma solito en descripcion_articulo
     *   'idUsuario', // Lo transforma solito en cod_usuario
     *   'idFormaDelZapato' => array('db' => 'cod_forma_zapato'),
     *   'fechaAlta',
     *   'unaFechaConOtroFormato' => array('transformer' => array('Funciones::formatearFecha', array(null, 'Y/m/d'))
     *  // El primer elemento del array transformer es la función, y el 2do un array con los parámetros (los "null" se reemplazan con el valor del campo, en este caso la fecha)
     * );
     */
    protected $__dbMappings = array();

    /**
     * @var array
     *
     * Es un array de configuracion de las relaciones, para poder guardarlas automáticamente.
     * array(
     *   'colores' => array(
     *     'cascadeDelete' => false // Por defecto NO borramos en cascada todos los items existentes de la relación
     *   )
     * );
     */
    protected $__relations = array();

	public function __construct(){
		$this->_modo = Modos::select;

		$this->extenderMappings();
		$this->extenderRelations();
	}
    private function extenderMappings() {
        $defaultTransformers = array(
            'fechaAlta' => array('Funciones::formatearFecha', array(null, 'd/m/Y')),
            'fechaBaja' => array('Funciones::formatearFecha', array(null, 'd/m/Y')),
            'fechaUltimaMod' => array('Funciones::formatearFecha', array(null, 'd/m/Y'))
        );

        $newMappings = array();
        foreach ($this->__dbMappings as $key => $mapping) {
            // Resuelvo para cuando no tiene mappings
            if (!is_string($key) && is_string($mapping)) {
                $key = $mapping;
                $mapping = array();
            }

            // Primero resolvemos el nombre del campo en la DB
            if (!array_key_exists('db', $mapping) || !$mapping['db']) {
                // Reemplazo "id" por "cod" cuando corresponde
                $mapping['db'] = preg_replace(array('/^(id)([0-9A-Z][0-9a-zA-Z]+)$/'), array('cod\2'), $key);

                // Transformo a snake_case
                $mapping['db'] = Funciones::snakeCase($mapping['db']);
            }

            // Luego resolvemos los transformers
            if ((!array_key_exists('transformer', $mapping) || !$mapping['transformer']) && array_key_exists($key, $defaultTransformers)) {
                $mapping['transformer'] = $defaultTransformers[$key];
            }

            $newMappings[$key] = $mapping;
        }
        $this->__dbMappings = $newMappings;
    }
    private function extenderRelations() {
        $newRelations = array();
        foreach ($this->__relations as $relation => $relationConfig) {
            // Resuelvo para cuando no tiene config
            if (!is_string($relation) && is_string($relationConfig)) {
                $relation = $relationConfig;
                $relationConfig = array();
            }

            // Primero resolvemos la config de FK
            if (!array_key_exists('fk', $relationConfig) || !$relationConfig['fk']) {
                $relationConfig['fk'] = array(
                    array('child' => 'id' . $this->getClass(), 'parent' => 'id')
                );
            }

            // Luego la de cascadeDelete
            if (!array_key_exists('cascadeDelete', $relationConfig) || $relationConfig['cascadeDelete'] !== true) {
                $relationConfig['cascadeDelete'] = false;
            }

            $newRelations[$relation] = $relationConfig;
        }
        $this->__relations = $newRelations;
    }

	public function getClass() {
		return get_class($this);
	}
    public function table() {
	    return $this->__table;
	}
	protected function autoIncrement() {
	    return $this->__autoIncrement;
	}
    public function getPK() {
        if (property_exists($this, '__primaryKey')) {
            return $this->__primaryKey;
        }

        // Legacy
        $constant = get_class($this) . '::_primaryKey';
        return json_decode(constant($constant), true);
    }
    public function getPKWithValues() {
        $pksWithValues = array();
        foreach ($this->getPK() as $key) {
            $pksWithValues[$this->$key] = $this->$key;
        }
        return $pksWithValues;
    }
    public function getPKSerializada() {
        $serial = '';
        foreach($this->getPK() as $pkName)
            $serial .= $pkName . '=' . $this->$pkName . '&';
        return trim($serial, '&');
    }
    protected function getUniqueId() {
        $pks = $this->getPK();
        if (count($pks) != 1) {
            return false;
        }
        return $pks[0];
    }

	public function anulado() {
		if (property_exists($this, 'anulado')) {
			/** @noinspection PhpUndefinedFieldInspection */
			return is_bool($this->anulado) ? $this->anulado : $this->anulado == 'S';
		}
		if (property_exists($this, 'fechaBaja')) {
			/** @noinspection PhpUndefinedFieldInspection */
			return !is_null($this->fechaBaja);
		}
		return false;
	}
	public function setAnulado($bool) {
		if (!property_exists($this, 'anulado')) {
			return false;
		}
		/** @noinspection PhpUndefinedFieldInspection */
		if (is_bool($this->anulado)) {
			/** @noinspection PhpUndefinedFieldInspection */
			$this->anulado = $bool;
		} else {
			/** @noinspection PhpUndefinedFieldInspection */
			$this->anulado = $bool ? 'S' : 'N';
		}
		/** @noinspection PhpUndefinedFieldInspection */
		return $this->anulado;
	}

	private function usesOldFactory() {
        $method = 'set' . ucfirst($this->getClass());
        return method_exists(Factory::getInstance(), $method);
    }

	public function guardar() {
		$this->validarGuardar();
		if ($this->modo != Modos::insert && $this->modo != Modos::update) {
			throw new FactoryExceptionCustomException('No se puede guardar un objeto que no esté en modo insert o update');
		}

        if ($this->usesOldFactory()) {
            // Legacy
            Factory::getInstance()->persistir($this);
            Factory::getInstance()->marcarParaModificar($this); //Una vez persistido, lo pongo en modo UPDATE
        } else {
            // New Factory
            $this->persistir();
            $this->marcarParaModificar();
        }

		return $this;
	}
	public function borrar() {
		$this->validarBorrar();

        if ($this->usesOldFactory()) {
            // Legacy
            Factory::getInstance()->marcarParaBorrar($this);
            Factory::getInstance()->persistir($this);
        } else {
            // New Factory
            $this->marcarParaBorrar();
            $this->persistir();
        }

		$this->setAnulado(true);

		return $this;
	}
	public function update() {
        if ($this->usesOldFactory()) {
            // Legacy
            Factory::getInstance()->marcarParaModificar($this);
            Factory::getInstance()->persistir($this);
        } else {
            // New Factory
            $this->marcarParaModificar();
            $this->persistir();
        }

		return $this;
	}

	protected function validarGuardar() {
	}
	protected function validarBorrar() {
		if ($this->anulado()) {
			throw new FactoryExceptionCustomException('El registro que intentó eliminar no existe');
		}
	}

    public function notificar($funcionalidad, $usuarios = array()) {
        try {
            return Notificacion::accionNotificar($this, $funcionalidad, $usuarios);
        } catch (Exception $ex) {
            //Si ocurre un error al notificar, no me importa, devuelvo false y si es necesario se manejará desde el controller
        }
        return false;
    }

    public function getObjectVars(){
		$array = array();
		foreach($this as $key => $val){
			if (substr($key, 0, 2) == '__') {
			    continue;
            }
			if (substr($key, 0, 1) == '_') {
                //Esta función se usa para listar las variables y pasarlas en el ECHOJSON que está en HTML.
                //Si el atributo empieza con _ es porque es un valor de LazyLoading, y si
                //es NULL es porque todavía no fue seteado, entonces no lo devuelvo como valor.
                //Para que un valor de LazyLoading pase a JSON hay que pedirlo antes (Ej: $notaDePedido->detalle)
                if (is_null($val)) {
                    continue;
                } else {
                    $key = substr($key, 1);
                }
            }
			$array[] = $key;
		}
		return $array;
	}

	public function getIdNombre($nameField = 'nombre', $idField = 'id') {
		$this->checkProperty($nameField);
		$this->checkProperty($idField);
		return '[' . $this->$idField . '] ' . $this->$nameField;
	}

	public function expand(){
		$array = array();
		foreach ($this as $key => $val) {
            if (substr($key, 0, 2) == '__') {
                continue;
            }
			if (substr($key, 0, 1) == '_') {
				$key = substr($key, 1);
				try {
                    $array[$key] = $this->__get($key);
                } catch (FactoryExceptionCustomException $ex) {
                    $array[$key] = null;
                }
			} else {
				$array[$key] = $val;
			}
		}
		if (property_exists($this, 'direccion') && get_class($this->direccion) == 'Direccion') {
			foreach ($this->direccion->getObjectVars() as $key) {
				$array['direccion' . ucfirst($key)]	= $this->direccion->$key;
			}
		}
		return $array;
	}

	protected function checkProperty($property, $throw = true) {
		if (!property_exists($this, $property)) {
		    if ($throw) {
			    throw new Exception('No existe la propiedad "' . $property . '" en la clase "' . get_class($this) . '"');
            }
		}
		return true;
	}

	//GETS y SETS
	protected function getModo() {
		return $this->_modo;
	}
	protected function setModo($modo) {
		$this->_modo = $modo;
		return $this;
	}
	protected $_usuario;
	protected function getUsuario() {
		if ($this->checkProperty('idUsuario', false) && !isset($this->_usuario)){
			/** @noinspection PhpUndefinedFieldInspection */
			$this->_usuario = Factory::getInstance()->getUsuario($this->idUsuario);
		}
		return $this->_usuario;
	}
	protected function setUsuario($usuario) {
		$this->checkProperty('idUsuario');
		/** @noinspection PhpUndefinedFieldInspection */
		$this->_usuario = $usuario;
		return $this;
	}
    protected $_usuarioBaja;
	protected function getUsuarioBaja() {
		if ($this->checkProperty('idUsuarioBaja', false) && !isset($this->_usuarioBaja)){
			/** @noinspection PhpUndefinedFieldInspection */
			$this->_usuarioBaja = Factory::getInstance()->getUsuario($this->idUsuarioBaja);
		}
		return $this->_usuarioBaja;
	}
	protected function setUsuarioBaja($usuarioBaja) {
		$this->checkProperty('idUsuarioBaja');
		/** @noinspection PhpUndefinedFieldInspection */
		$this->_usuarioBaja = $usuarioBaja;
		return $this;
	}
    protected $_usuarioUltimaMod;
	protected function getUsuarioUltimaMod() {
		if ($this->checkProperty('idUsuarioUltimaMod', false) && !isset($this->_usuarioUltimaMod)){
			/** @noinspection PhpUndefinedFieldInspection */
			$this->_usuarioUltimaMod = Factory::getInstance()->getUsuario($this->idUsuarioUltimaMod);
		}
		return $this->_usuarioUltimaMod;
	}
	protected function setUsuarioUltimaMod($usuarioUltimaMod) {
		$this->checkProperty('idUsuarioUltimaMod');
		/** @noinspection PhpUndefinedFieldInspection */
		$this->_usuarioUltimaMod = $usuarioUltimaMod;
		return $this;
	}


    // New Factory methods

    /**
     * @param $id
     * @return $this
     * @throws Exception
     */
    public function baseFind($id) {
        try {
            $this->modo = Modos::insert;

            // Si tiene seteados todos los IDs, llenamos el objeto con los ids y lo buscamos en la DB
            if (Funciones::tieneId($id)) {
                foreach ($this->getPK() as $index => $key) {
                    $this->$key = $id[$index];
                }

                $this->fill(Datos::EjecutarSQLItem($this->getQuery(Modos::select), get_class($this)));
            }

            return $this;
        } catch (Exception $ex) {
            // TODO: acá deberíamos loggear
            throw $ex;
        }
    }

    // Get List Objects
    public static function getListObject($clase, $clausulaWhere = '1 = 1', $limit = 0){
        $obj = new $clase;
        return $obj->getListObjectInstance($clausulaWhere, $limit);

    }
    public function getListObjectInstance($clausulaWhere = '1 = 1', $limit = 0){
        return $this->fillList(Datos::EjecutarSQL($this->cambiarWhere($this->getQuery(Modos::select), $clausulaWhere, $limit), $this->getClass()));
    }
    protected function cambiarWhere($sentencia, $clausulaWhere, $limit) {
        if ($limit != 0) {
            $iPos2 = strrpos($sentencia, 'SELECT');
            $sentencia = 'SELECT TOP ' . $limit . substr($sentencia, $iPos2 + 6);
        }
        $iPos1 = strrpos($sentencia, 'WHERE');

        return substr($sentencia, 0, $iPos1 + 6) . (trim($clausulaWhere) == '' ? '1 = 1; ' : $clausulaWhere);
    }

    /**
     * @param $dr
     * @return $this
     */
    protected function fill($dr) {
        foreach ($this->__dbMappings as $key => $mapping) {
            // Hago el fill del $dr (DataRow)
            $this->$key = $dr[$mapping['db']];

            // Y finalmente aplico el transformer
            if (array_key_exists('transformer', $mapping)) {

                // Si hay parámetros (que seguramente los haya, al menos un null) hay que reemplazar los "null" por el valor
                $currentTransformer = $mapping['transformer'];

                if (count($currentTransformer) > 1 && is_array($currentTransformer[1])) {
                    foreach ($currentTransformer[1] as $index => $param) {
                        if (is_null($param)) {
                            $currentTransformer[1][$index] = $this->$key;
                        }
                    }
                }

                // Llamo al transformer
                $this->$key = call_user_func_array($currentTransformer[0], $currentTransformer[1]);
            }
        }

        $this->modo = Modos::update;

        return $this;
    }
    protected function fillList($ds) {
        $list = array();
        $clase = $this->getClass();
        for ($i = 0; $i < count($ds); $i++) {
            $obj = new $clase();
            $obj->fill($ds[$i]);
            $list[] = $obj;
        }
        return $list;
    }

    public function marcarParaInsertar() {
        $this->modo = Modos::insert;
        return $this;
    }
    public function marcarParaModificar() {
        $this->modo = Modos::update;
        return $this;
    }
    public function marcarParaBorrar() {
        $this->modo = Modos::delete;
        return $this;
    }

    protected function puedePersistir($existe) {
        if ($this->modo == Modos::insert && $existe) {
            Logger::addError('Registro existente');
            throw new FactoryExceptionRegistroExistente();
        } elseif (($this->modo == Modos::update || $this->modo == Modos::select) && !$existe) {
            Logger::addError('El registro no existe');
            throw new FactoryExceptionRegistroNoExistente();
        }
    }
    protected function existeEnDB() {
        try {
            Datos::EjecutarSQLItem($this->getQuery(Modos::select), get_class($this));
            return true;
        } catch (FactoryExceptionRegistroNoExistente $ex) {
            return false;
        }
    }
    protected function persistir() {
        try {
            // Elimino de cache todos los objetos de esta clase
            Cache::deleteAllByTag($this->getClass());

            $existe = false;
            try {
                $mutex = new Mutex($this->getClass());
                $mutex->lock();
                if (Funciones::tieneId(array_values($this->getPKWithValues()))) {
                    $existe = $this->existeEnDB();
                }
                $this->puedePersistir($existe);
                Transaction::begin();
                $this->save();
                Transaction::commit();
                $mutex->unlock();
            } catch (Exception $ex) {
                $mutex->unlock();
                throw $ex;
            }
        } catch (Exception $ex) {
            Transaction::rollback();
            throw $ex;
        }
    }
    protected function push() {
        Datos::EjecutarSQLsinQuery($this->getQuery($this->modo));
    }
    protected function getNextId() {
        $row = Datos::EjecutarSQLItem($this->getQuery(Modos::id));
        if (count($row) != 1)
            throw new FactoryException('No se encontró el próximo ID');
        return $row['computed'];
    }

    /**
     * Este es el método que conviene extender cuando se necesita un comportamiento particular.
     * Para extender este método, se puede llamar primero al parent::save(); y después hacer el resto de las cosas (como guardar relaciones).
     *
     * TODO: ver si es posible hacer algo genérico para guardar relaciones de forma default
     */
    protected function save() {

        // Borramos las relaciones para poder crear las nuevas
        if ($this->modo == Modos::delete && count($this->__relations)) {
            foreach ($this->__relations as $relationName => $relationConfig) {
                if (!$relationConfig['cascadeDelete']) {
                    continue;
                }
                $aux = $this->$relationName;
                $this->$relationName = null;
                foreach ($this->$relationName as $item) {
                    $item->borrar();
                }
                $this->$relationName = $aux;
            }
        }

        // Creamos o actualizamos el objeto
        if ($this->modo == Modos::insert && ($uniqueId = $this->getUniqueId())) {
            $this->$uniqueId = $this->getNextId();
        }
        $this->push();

        return $this;
    }

    // Mapper methods

    /**
     * Devuelve una query SQL según el modo que se le envíe
     *
     * @param $modo
     * @return string
     * @throws FactoryException
     */
    protected function getQuery($modo) {
        if ($modo == Modos::select) {
            return $this->getQuerySelect();
        } elseif ($modo == Modos::insert) {
            return $this->getQueryInsert();
        } elseif ($modo == Modos::update) {
            return $this->getQueryUpdate();
        } elseif ($modo == Modos::delete) {
            return $this->getQueryDelete();
        } elseif ($modo == Modos::id) {
            return $this->getQueryNextId();
        } else {
            throw new FactoryException('Modo incorrecto');
        }
    }

    /**
     * Default SELECT query
     *
     * @return string
     */
    protected function getQuerySelect() {
        $fields = $this->getQuerySelectFields();
        if (is_array($fields)) {
            $fields = implode(', ', $fields);
        }

        $where = $this->getQuerySelectWhere();
        if (is_array($where)) {
            foreach ($where as $whereField => $whereValue) {
                $where[$whereField] = $whereField . ' = ' . $whereValue;
            }
            $where = implode(' AND ', $where);
        }

        return 'SELECT ' . $fields
            . ' FROM ' . $this->table()
            . ' WHERE ' . $where
            . '; ';
    }
    protected function getQuerySelectFields() {
        return '*';
    }
    protected function getQuerySelectWhere() {
        $where = array();
        foreach ($this->getPK() as $key) {
            $where[$this->__dbMappings[$key]['db']] = Datos::objectToDB($this->$key);
        }
        return $where;
    }

    /**
     * Default INSERT query
     *
     * @return string
     */
    protected function getQueryInsert() {

        $values = $this->getQueryInsertValues();
        if (is_array($values)) {
            $values = '('
                . implode(', ', array_keys($values))
                . ') VALUES ('
                . implode(', ', array_values($values))
                . ')';
        }

        return 'INSERT INTO ' . $this->table() . ' ' . $values . '; ';
    }
    protected function getQueryInsertValues() {
        // Armo los campos a skippear
        $defaultSkip = array(
            'idUsuarioBaja',
            'idUsuarioUltimaMod',
            'fechaBaja',
            'fechaUltimaMod'
        );
        if ($this->autoIncrement()) {
            foreach ($this->getPK() as $key) {
                $defaultSkip[] = $key;
            }
        }

        // Campos con valores default
        $defaultValues = array(
            'anulado' => Datos::objectToDB('N'),
            'idUsuario' => Datos::objectToDB(Usuario::logueado()->id),
            'fechaAlta' => 'GETDATE()'
        );

        $values = array();
        foreach ($this->__dbMappings as $key => $mapping) {

            // Si es parte del defaultSkip, no lo metemos
            if (!in_array($key, $defaultSkip)) {
                // Si tiene un valor default, lo usamos
                $values[$mapping['db']] = array_key_exists($key, $defaultValues) ? $defaultValues[$key] : Datos::objectToDB($this->$key);
            }
        }
        return $values;
    }

    /**
     * Default UPDATE query
     *
     * @return string
     */
    protected function getQueryUpdate() {

        $values = $this->getQueryUpdateValues();
        if (is_array($values)) {
            foreach ($values as $valuesField => $valuesValue) {
                $values[$valuesField] = $valuesField . ' = ' . $valuesValue;
            }
            $values = implode(', ', $values);
        }

        $where = $this->getQueryUpdateWhere();
        if (is_array($where)) {
            foreach ($where as $whereField => $whereValue) {
                $where[$whereField] = $whereField . ' = ' . $whereValue;
            }
            $where = implode(' AND ', $where);
        }

        return 'UPDATE ' . $this->table()
            . ' SET ' . $values
            . ' WHERE ' . $where
            . '; ';
    }
    protected function getQueryUpdateValues() {
        // Armo los campos a skippear
        $defaultSkip = array(
            'anulado',
            'idUsuario',
            'idUsuarioBaja',
            'fechaAlta',
            'fechaBaja'
        );
        foreach ($this->getPK() as $key) {
            $defaultSkip[] = $key;
        }

        // Campos con valores default
        $defaultValues = array(
            'idUsuarioUltimaMod' => Datos::objectToDB(Usuario::logueado()->id),
            'fechaUltimaMod' => 'GETDATE()'
        );

        $values = array();
        foreach ($this->__dbMappings as $key => $mapping) {

            // Si es parte del defaultSkip, no lo metemos
            if (!in_array($key, $defaultSkip)) {
                // Si tiene un valor default, lo usamos
                $values[$mapping['db']] = array_key_exists($key, $defaultValues) ? $defaultValues[$key] : Datos::objectToDB($this->$key);
            }
        }
        return $values;
    }
    protected function getQueryUpdateWhere() {
        $where = array();
        foreach ($this->getPK() as $key) {
            $where[$this->__dbMappings[$key]['db']] = Datos::objectToDB($this->$key);
        }
        return $where;
    }

    /**
     * Default DELETE query
     *
     * @return string
     */
    protected function getQueryDelete() {
        $values = $this->getQueryDeleteValues();
        if (is_array($values)) {
            foreach ($values as $valuesField => $valuesValue) {
                $values[$valuesField] = $valuesField . ' = ' . $valuesValue;
            }
            $values = implode(', ', $values);
        }

        $where = $this->getQueryDeleteWhere();
        if (is_array($where)) {
            foreach ($where as $whereField => $whereValue) {
                $where[$whereField] = $whereField . ' = ' . $whereValue;
            }
            $where = implode(' AND ', $where);
        }

        if ($this->__softDelete) {
            return 'UPDATE ' . $this->table()
                . ' SET ' . $values
                . ' WHERE ' . $where
                . '; ';
        } else {
            return 'DELETE FROM ' . $this->table()
                . ' WHERE ' . $where
                . '; ';
        }
    }
    protected function getQueryDeleteValues() {
        // Valores default
        $defaultValues = array(
            'anulado' => Datos::objectToDB('S'),
            'idUsuarioBaja' => Datos::objectToDB(Usuario::logueado()->id),
            'fechaBaja' => 'GETDATE()'
        );

        $values = array();
        foreach ($this->__dbMappings as $key => $mapping) {
            // Si existe el campo en los mappings, lo seteamos
            if (array_key_exists($key, $defaultValues)) {
                $values[$this->__dbMappings[$key]['db']] = $defaultValues[$key];
            }
        }
        return $values;
    }
    protected function getQueryDeleteWhere() {
        $where = array();
        foreach ($this->getPK() as $key) {
            $where[$this->__dbMappings[$key]['db']] = Datos::objectToDB($this->$key);
        }
        return $where;
    }

    /**
     * Default NEXT ID query
     *
     * @return string
     * @throws FactoryException
     */
    protected function getQueryNextId() {
        if ($this->autoIncrement()) {
            return 'SELECT IDENT_CURRENT(\'' . $this->table() . '\') + IDENT_INCR(\'' . $this->table() . '\');';
        } else {
            if (!($uniqueId = $this->getUniqueId())) {
                throw new FactoryException('No se puede calcular el NextId de la clase "' . $this->getClass() . '"" porque no tiene un único ID');
            }

            return 'SELECT ISNULL(MAX(' . $this->__dbMappings[$uniqueId]['db'] . '), 0) + 1 FROM ' . $this->table() . ';';
        }
    }
}

?>