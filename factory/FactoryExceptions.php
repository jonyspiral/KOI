<?php

class TransactionException extends Exception {
    public function __construct($msg = 'Ocurrió un error en la transacción'){
        parent::__construct($msg);
	}
}

class LoginFailException extends Exception {
	public function __construct($msg = 'Ocurrió un error al intentar realizar el login'){
		parent::__construct($msg);
	}
}

class FactoryExceptionRegistroExistente extends Exception {
    public function __construct($msg = 'Registro existente'){
        parent::__construct($msg);
	}
}

class FactoryExceptionRegistroNoExistente extends Exception {
    public function __construct($msg = 'Registro no existe'){
        parent::__construct($msg);
	}
}

class FactoryExceptionTipoDeObjetoDesconocido extends Exception {
    public function __construct($msg = 'El objeto no es conocido en este entorno'){
        parent::__construct($msg);
	}
}

class FactoryExceptionStoredProcedureDesconocido extends Exception {
    public function __construct($msg = 'El stored procedure número no es conocido en este entorno'){
        parent::__construct($msg);
	}
}

class FactoryExceptionCustomException extends Exception {
    public function __construct($msg = 'Ocurrió un error'){
        parent::__construct($msg);
	}
}

class FactoryException extends Exception {
    public function __construct($msg = 'Modo incorrecto'){
        parent::__construct($msg);
	}
}

?>