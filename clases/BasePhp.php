<?php

require_once __DIR__ . '/BasePhp.php';

class BasePhp {
	public function __get($name)
	{
		$method = 'get' . $name;
		if (!method_exists($this, $method)) {
			throw new FactoryExceptionCustomException('No existe el m�todo ' . $method . ' en la clase "' . get_class($this) . '"');
		}
		return $this->$method();
	}

	public function __set($name, $value)
	{
		$method = 'set' . $name;
		if (!method_exists($this, $method)) {
			throw new FactoryExceptionCustomException('No existe el m�todo ' . $method . ' en la clase "' . get_class($this) . '"');
		}
		$this->$method($value);
	}
}

?>