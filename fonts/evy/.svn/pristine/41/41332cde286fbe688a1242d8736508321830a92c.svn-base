<?php

/**
 * Uri Class
 *
 * @package   Ecommerce
 * @category  Core
 */
class Ecommerce_Core_Uri {

	/**
	 * Returns the desired segment, or $default if it does not exist.
	 *
	 * @param   int   $segment The segment number (1-based index)
	 * @param   mixed $default Default value to return
	 *
	 * @return  string
	 */
	public static function segment($segment, $default = null) {
		if ($request = Ecommerce_Core_Request::instance()) {
			return $request->uri->get_segment($segment, $default);
		}

		return null;
	}

	/**
	 * Returns all segments in an array
	 *
	 * @return  array
	 */
	public static function segments() {
		if ($request = Ecommerce_Core_Request::instance()) {
			return $request->uri->get_segments();
		}

		return null;
	}

	/**
	 * Returns the full uri as a string
	 *
	 * @return  string
	 */
	public static function string() {
		if ($request = Ecommerce_Core_Request::instance()) {
			return $request->uri->get();
		}

		return null;
	}

	/**
	 * Creates a url with the given uri, including the base url
	 *
	 * @param   string $uri           The uri to create the URL for
	 * @param   array  $get_variables Any GET urls to append via a query string
	 * @param   bool   $secure        If false, force http. If true, force https
	 *
	 * @return  string
	 */
	public static function create($uri = null, $get_variables = array(), $secure = null) {
		$url = '';
		!$uri && $uri = self::string();

		// If the given uri is not a full URL
		if (!preg_match("#^(http|https|ftp)://#i", $uri)) {
			$url .= self::base();
		}
		$url .= ltrim($uri, '/');

		if (!empty($get_variables)) {
			$char = strpos($url, '?') === false ? '?' : '&';
			$url .= (is_string($get_variables)) ? $char . str_replace('%3A', ':', $get_variables) : $char . str_replace('%3A', ':', http_build_query($get_variables));
		}

		is_bool($secure) and $url = http_build_url($url, array('scheme' => $secure ? 'https' : 'http'));

		return $url;
	}

	/**
	 * Gets the current URL, including the BASE_URL
	 *
	 * @return  string
	 */
	public static function current() {
		return self::create();
	}

	/**
	 * Gets the base URL, including the index_file if wanted.
	 *
	 * @return  string
	 */
	public static function base() {
		if (!self::$base_url) {
			$base_url = '';
			if (Ecommerce_Core_Input::server('http_host')) {
				$base_url .= Ecommerce_Core_Input::protocol() . '://' . Ecommerce_Core_Input::server('http_host');
			}
			if (Ecommerce_Core_Input::server('script_name')) {
				$base_url .= str_replace('\\', '/', dirname(Ecommerce_Core_Input::server('script_name')));
			}

			// Add a slash if it is missing and return it
			self::$base_url = rtrim($base_url, '/') . '/';
		}

		return self::$base_url;
	}

	/**
	 * @var  string  The base URI string
	 */
	protected static $base_url = '';

	/**
	 * @var  string  The URI string
	 */
	protected $uri = '';

	/**
	 * @var  array  The URI segments
	 */
	protected $segments = '';

	/**
	 * Construct takes a URI or detects it if none is given and generates
	 * the segments.
	 *
	 * @param  string $uri
	 */
	public function __construct($uri = null) {
		$this->uri = trim($uri ? $uri : Ecommerce_Core_Input::uri(), '/');
		$this->segments = (empty($this->uri)) ? array() : $this->segments = explode('/', $this->uri);
	}

	/**
	 * Returns the full URI string
	 *
	 * @return  string  The URI string
	 */
	public function get() {
		return $this->uri;
	}

	/**
	 * Returns all of the URI segments
	 *
	 * @return  array  The URI segments
	 */
	public function get_segments() {
		return $this->segments;
	}

	/**
	 * Get the specified URI segment, return default if it doesn't exist.
	 *
	 * Segment index is 1 based, not 0 based
	 *
	 * @param   string $segment The 1-based segment index
	 * @param   mixed  $default The default value
	 *
	 * @return  mixed
	 */
	public function get_segment($segment, $default = null) {
		if (isset($this->segments[$segment - 1])) {
			return $this->segments[$segment - 1];
		}

		return $default;
	}
}
