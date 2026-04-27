<?php

class WS_Exception_ModelExists extends FactoryExceptionCustomException {}
class WS_Exception_ModelNotExists extends FactoryExceptionCustomException {}

class WS {
	protected	$error_code = 0;
	protected	$base_url = 'http://www.spiralshoes.com/';
	protected	$ws_path = '';
	protected	$log_path = '';
	private		$log_file = '';

	const	LOG_TIPO_SUCCESS = 'SUCCESS';
	const	LOG_TIPO_INFO = 'INFO';
	const	LOG_TIPO_WARNING = 'WARNING';
	const	LOG_TIPO_ERROR = 'ERROR';

	public function __construct($ws_path, $base_url = false) {
		$this->ws_path = $ws_path;
		$base_url && $this->base_url = $base_url;
		$this->log_path = Config::pathBase . '/tmp/logs/';
	}

	//TODO: Hacer alguna validación para ver si tengo los datos necesarios antes de hacer un request (como el ws_path, base_url, etc)

	public function post($action, $data, $data_id = null) {
		return $this->send_request($this->forge_request($action, 'POST', $data, $data_id));
	}

	public function get($action, $data, $data_id = null) {
		return $this->send_request($this->forge_request($action, 'GET', $data, $data_id));
	}

	private function forge_request($action, $method, $data, $data_id = false) {
		return array(
			'url' 		=> $this->base_url . $this->ws_path . $action,
			'method'	=> $method,
			'data_id'	=> $data_id,
			'data'		=> $data
		);
	}

	private function send_request($request) {
		/**
		 * array(
		 * 		'url'		=> $url,
		 * 		'method'	=> 'POST',
		 * 		'data'		=> $obj,
		 * 		'data_id'	=> $obj->id (Opcional. Si está, le pega a /$url/ID )
		 * )
		 */
		$result = null;
		$url = $request['url'] . ($request['data_id'] ?  '/' . $request['data_id'] : '');
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/json\r\n",
				'method'  => $request['method'],
				'content' => json_encode($request['data']),
			)
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		$result = json_decode($result, true);
		return $result;
	}

	protected function log($tipo, $metodo, $descripcion = '') {
		$echo = '<span style="font-weight: bold; color: ' . (($tipo == self::LOG_TIPO_ERROR) ? 'red' : ($tipo == self::LOG_TIPO_SUCCESS ? 'green' : ($tipo == self::LOG_TIPO_INFO ? 'blue' : 'black'))) . '">';
		$echo .= date('H:i:s', time()) . ' || ';
		$echo .= $tipo . ' || ';
		$echo .= $metodo . ' ';
		$echo .= '</span>';
		$echo .= ' [' . $descripcion . ']';
		$echo .= '<br><br>';

		if (!$this->log_file) {
			$this->log_file = $this->log_path . date('Y-m-d') . '.html';
		}
		$fp = fopen($this->log_file, 'a+');
		fwrite($fp, $echo);
		fclose($fp);
	}
}

?>