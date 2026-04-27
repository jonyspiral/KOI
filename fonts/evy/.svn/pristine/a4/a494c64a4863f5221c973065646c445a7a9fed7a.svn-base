<?php

/**
 * Input class
 *
 * The input class allows you to access HTTP parameters, load server variables
 * and user agent details.
 *
 * @package   Ecommerce
 * @category  Core
 *
 */
class Ecommerce_Core_Input {
	/**
	 * @var  $detected_uri string The URI that was detected automatically
	 */
	protected static $detected_uri = null;

	/**
	 * @var  $detected_ext string The URI extension that was detected automatically
	 */
	protected static $detected_ext = null;

	/**
	 * @var  $input array All of the input (GET, POST, PUT, DELETE)
	 */
	protected static $input = null;

	/**
	 * @var  $put_delete array All of the put or delete vars
	 */
	protected static $put_delete = null;

	/**
	 * @var  $php_input array Cache for the php://input stream
	 */
	protected static $php_input = null;

	/**
	 * Detects and returns the current URI based on a number of different server
	 * variables.
	 *
	 * @return  string
	 */
	public static function uri() {
		if (self::$detected_uri !== null) {
			return self::$detected_uri;
		}

		// We want to use PATH_INFO if we can.
		if (!empty($_SERVER['PATH_INFO'])) {
			$uri = $_SERVER['PATH_INFO'];
		}

		// Deal with any trailing dots
		$uri = rtrim(strpos($uri, '/' . WSNAME) === 0 ? substr($uri, strlen('/' . WSNAME) + 1) : $uri, '.');

		// Do we have a URI and does it not end on a slash?
		if ($uri and substr($uri, -1) !== '/') {
			// Strip the defined url suffix from the uri if needed
			$uri_info = pathinfo($uri);

			if (!empty($uri_info['extension'])) {
				if (strpos($uri_info['extension'], '/') === false) {
					self::$detected_ext = $uri_info['extension'];
					$uri = $uri_info['dirname'] . '/' . $uri_info['filename'];
				}
			}
		}

		// Do some final clean up of the uri
		self::$detected_uri = preg_replace(array("/\.+\//", '/\/+/'), '/', $uri);

		return self::$detected_uri;
	}

	/**
	 * Detects and returns the current URI extension
	 *
	 * @return  string
	 */
	public static function extension() {
		self::$detected_ext === null and self::uri();

		return self::$detected_ext;
	}

	/**
	 * Get the public ip address of the user.
	 *
	 * @param string $default
	 *
	 * @return  string
	 */
	public static function ip($default = '0.0.0.0') {
		return self::server('REMOTE_ADDR', $default);
	}

	/**
	 * Return's the protocol that the request was made with
	 *
	 * @return  string
	 */
	public static function protocol() {
		if (self::server('HTTPS') == 'on' or self::server('HTTPS') == 1 or self::server('SERVER_PORT') == 443) {
			return 'https';
		}

		return 'http';
	}

	/**
	 * Return's whether this is an AJAX request or not
	 *
	 * @return  bool
	 */
	public static function is_ajax() {
		return (self::server('HTTP_X_REQUESTED_WITH') !== null) and strtolower(self::server('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest';
	}

	/**
	 * Return's the referrer
	 *
	 * @param string $default
	 *
	 * @return  string
	 */
	public static function referrer($default = '') {
		return self::server('HTTP_REFERER', $default);
	}

	/**
	 * Return's the input method used (GET, POST, DELETE, etc.)
	 *
	 * @param string $default
	 *
	 * @return  string
	 */
	public static function method($default = 'GET') {
		// get the method from the current active request
		if ($request = Ecommerce_Core_Request::instance() and $method = $request->get_method()) {
			return $method;
		}

		// if called before a request is active, fall back to the global server setting
		return Ecommerce_Core_Input::server('HTTP_X_HTTP_METHOD_OVERRIDE', Ecommerce_Core_Input::server('REQUEST_METHOD', $default));
	}

	/**
	 * Return's the user agent
	 *
	 * @param string $default
	 *
	 * @return  string
	 */
	public static function user_agent($default = '') {
		return self::server('HTTP_USER_AGENT', $default);
	}

	/**
	 * Returns all of the GET, POST, PUT and DELETE variables.
	 *
	 * @return  array
	 */
	public static function all() {
		self::$input === null and self::hydrate();
		return self::$input;
	}

	/**
	 * Gets the specified GET variable.
	 *
	 * @param string $index   The index key
	 * @param string $default The default value
	 *
	 * @return  string|array
	 */
	public static function get($index = null, $default = null) {
		return (func_num_args() === 0) ? $_GET : (empty($_GET[$index]) ? $default : $_GET[$index]);
	}

	/**
	 * Fetch an item from the POST array
	 *
	 * @param string $index   The index key
	 * @param string $default The default value
	 *
	 * @return  string|array
	 */
	public static function post($index = null, $default = null) {
		return (func_num_args() === 0) ? $_POST : (empty($_POST[$index]) ? $default : $_POST[$index]);
	}

	/**
	 * Fetch an item from the php://input for put arguments
	 *
	 * @param string $index   The index key
	 * @param string $default The default value
	 *
	 * @return  string|array
	 */
	public static function put($index = null, $default = null) {
		self::$put_delete === null and self::hydrate();
		return (func_num_args() === 0) ? self::$put_delete : (empty(self::$put_delete[$index]) ? $default : self::$put_delete[$index]);
	}

	/**
	 * Fetch an item from the php://input for delete arguments
	 *
	 * @param string $index   The index key
	 * @param string $default The default value
	 *
	 * @return  string|array
	 */
	public static function delete($index = null, $default = null) {
		self::$put_delete === null and self::hydrate();
		return (is_null($index) and func_num_args() === 0) ? self::$put_delete : (empty(self::$put_delete[$index]) ? $default : self::$put_delete[$index]);
	}

	/**
	 * Fetch an item from either the GET, POST, PUT or DELETE array
	 *
	 * @param string $index   The index key
	 * @param string $default The default value
	 *
	 * @return  string|array
	 */
	public static function param($index = null, $default = null) {
		self::$input === null and self::hydrate();
		return (empty(self::$input[$index]) ? $default : self::$input[$index]);
	}

	/**
	 * Fetch an item from the SERVER array
	 *
	 * @param string $index   The index key
	 * @param string $default The default value
	 *
	 * @return  string|array
	 */
	public static function server($index = null, $default = null) {
		return (func_num_args() === 0) ? $_SERVER : (empty($_SERVER[strtoupper($index)]) ? $default : $_SERVER[strtoupper($index)]);
	}

	/**
	 * Hydrates the input array
	 *
	 * @return  void
	 */
	protected static function hydrate() {
		self::$input = array_merge($_GET, $_POST);

		if (self::method() == 'POST' or self::method() == 'PUT' or self::method() == 'DELETE') {
			self::$php_input === null and self::$php_input = file_get_contents('php://input');
			if ($_SERVER['CONTENT_TYPE'] == 'application/json') {
				self::$put_delete = Ecommerce_Core_Format::forge()->to_array(json_decode(self::$php_input));
			} else {
				parse_str(self::$php_input, self::$put_delete);
			}
			self::$input = array_merge(self::$input, self::$put_delete);
		}
	}
}
