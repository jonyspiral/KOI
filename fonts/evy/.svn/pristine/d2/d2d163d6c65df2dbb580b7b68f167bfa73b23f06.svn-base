<?php

class Model_Exception_Handler extends Exception {
	const		ERRORES_VARIOS = 1;
	const		REGISTRO_EXISTE = 11;
	const		REGISTRO_NO_EXISTE = 21;

	public function __construct($ex) {
		/** @var Exception $ex */
		switch (get_class($ex)) {
			case 'Model_Exception_RecordExists':
			case 'FactoryExceptionRegistroExistente':
				throw new Model_Exception_AppException($ex->getMessage(), self::REGISTRO_EXISTE);
				break;
			case 'Model_Exception_RecordNotFound':
			case 'FactoryExceptionRegistroNoExistente':
				throw new Model_Exception_AppException($ex->getMessage(), self::REGISTRO_NO_EXISTE);
				break;
			case 'FactoryExceptionCustomException':
				throw new Model_Exception_AppException($ex->getMessage(), self::ERRORES_VARIOS);
				break;
			default:
				throw $ex;
				break;
		}

	}
}

?>