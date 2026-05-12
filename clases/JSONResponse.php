<?php

class JSONResponse {
	const	JSON_NULL = -1;
	const	JSON_EMPTY = 0;
	const	JSON_OBJECT = 1; //También puede ser un array
	const	JSON_ERROR = 2;
	const	JSON_SUCCESS = 3;
	const	JSON_CONFIRM = 4;
	const	JSON_ALERT = 5;
	const	JSON_INFO = 6;
	
	public	$responseType;
	public	$responseMsg;
	public	$data;

	public function __construct($data = null, $responseType = null, $responseMsg = null) {
		$this->data = $data;
		$this->responseType = $responseType;
		$this->responseMsg = $responseMsg;
	}
}

?>