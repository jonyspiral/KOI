<?php

abstract class Ecommerce_Core_HttpException extends Ecommerce_Core_EcommerceException {
	/**
	 * Must return a response object for the handle method
	 *
	 * @return  Ecommerce_Core_Response
	 */
	abstract protected function response();
}
class Ecommerce_Core_HttpNotFoundException extends Ecommerce_Core_HttpException {
	public function response() {
		return new Ecommerce_Core_Response(array('error' => 'The controller does not exist'), 404);
	}
}

class Ecommerce_Core_HttpServerErrorException extends Ecommerce_Core_HttpException {
	public function response() {
		return new Ecommerce_Core_Response(array('error' => 'Internal Server Error'), 500);
	}
}
