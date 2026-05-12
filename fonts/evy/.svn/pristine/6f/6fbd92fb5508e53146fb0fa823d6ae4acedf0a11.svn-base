<?php

/**
 * The Request class is used to create and manage new and existing requests.  There
 * is a main request which comes in from the browser or command line, then new
 * requests can be created for HMVC requests.
 *
 * Example Usage:
 *
 *     $request = Ecommerce_Core_Request::forge('foo/bar')->execute();
 *     echo $request->response();
 *
 * @package     Ecommerce
 * @subpackage  Core
 */
class Ecommerce_Core_Request {
	/**
	 * Holds the global active request instance
	 *
	 * @var  Ecommerce_Core_Request
	 */
	protected static $instance = false;

	/**
	 * Generates a new request.  The request is then set to be the active
	 * request.  If this is the first request, then save that as the main
	 * request for the app.
	 *
	 * @return  Ecommerce_Core_Request  The new request object
	 */
	public static function instance() {
		return self::$instance;
	}

	/**
	 * Generates a new request.  The request is then set to be the active
	 * request.  If this is the first request, then save that as the main
	 * request for the app.
	 *
	 * @param null $uri	The URI of the request
	 *
	 * @return  Ecommerce_Core_Request  The new request object
	 */
	public static function forge($uri = null) {
		if (!self::$instance) {
			$request = new self($uri);
		}

		return $request;
	}

	/**
	 * Holds the response body of the request.
	 *
	 * @var  string
	 */
	public $body = null;

	/**
	 * Holds the response object of the request.
	 *
	 * @var  Ecommerce_Core_Request
	 */
	public $response = null;

	/**
	 * The Request's URI object.
	 *
	 * @var  Ecommerce_Core_Uri
	 */
	public $uri = null;

	/**
	 * The request's route object
	 *
	 * @var  Ecommerce_Core_Route
	 */
	public $route = null;

	/**
	 * @var  string $method request method
	 */
	protected $method = null;

	/**
	 * The current controller directory
	 *
	 * @var  string
	 */
	public $directory = '';

	/**
	 * The request's controller
	 *
	 * @var  string
	 */
	public $controller = '';

	/**
	 * The request's controller action
	 *
	 * @var  string
	 */
	public $action = '';

	/**
	 * The request's method params
	 *
	 * @var  array
	 */
	public $method_params = array();

	/**
	 * The request's named params
	 *
	 * @var  array
	 */
	public $named_params = array();

	/**
	 * Controller instance once instantiated
	 *
	 * @var  Ecommerce_Core_Controller
	 */
	public $controller_instance;

	/**
	 * Search paths for the current active request
	 *
	 * @var  array
	 */
	public $paths = array();

	/**
	 * Request that created this one
	 *
	 * @var  Ecommerce_Core_Request
	 */
	protected $parent = null;

	/**
	 * Requests created by this request
	 *
	 * @var  array
	 */
	protected $children = array();

	/**
	 * Creates the new Request object by getting a new URI object, then parsing
	 * the uri with the Route class.
	 *
	 * @param string	$uri	The uri string
	 *
	 * @return Ecommerce_Core_Request
	 */
	public function __construct($uri) {
		$this->uri = new Ecommerce_Core_Uri($uri);
		$this->method = Ecommerce_Core_Input::method();
		$this->route = Ecommerce_Core_Router::process($this);
		if (!$this->route) {
			return;
		}
		$this->controller = $this->route->controller;
		$this->action = $this->route->action;
		$this->method_params = $this->route->method_params;
		$this->named_params = $this->route->named_params;
	}

	/**
	 * This executes the request and sets the output to be used later.
	 *
	 * Usage:
	 *
	 *     $request = Ecommerce_Core_Request::forge('hello/world')->execute();
	 *
	 * @param  array|null $method_params An array of parameters to pass to the method being executed
	 *
	 * @throws Ecommerce_Core_HttpNotFoundException
	 * @throws Ecommerce_Core_EcommerceException
	 * @throws Exception
	 * @return  Ecommerce_Core_Request  This request object
	 */
	public function execute($method_params = null) {
		if (!$this->route) {
			throw new Ecommerce_Core_HttpNotFoundException();
		}

		try {
			if ($this->route->callable !== null) {
				$response = call_user_func_array($this->route->callable, array($this));
				if (!$response instanceof Ecommerce_Core_Response) {
					$response = new Ecommerce_Core_Response($response);
				}
			} else {
				$method_prefix = $this->method . '_';
				$class = $this->controller;

				// Allow override of method params from execute
				if (is_array($method_params)) {
					$this->method_params = array_merge($this->method_params, $method_params);
				}

				// If the class doesn't exist then 404
				if (!class_exists($class)) {
					throw new Ecommerce_Core_HttpNotFoundException();
				}

				// Load the controller using reflection
				$class = new ReflectionClass($class);

				if ($class->isAbstract()) {
					throw new Ecommerce_Core_HttpNotFoundException();
				}

				// Create a new instance of the controller
				$this->controller_instance = $class->newInstance($this);

				!$this->action && $this->action = ($class->hasProperty('default_action') ? $class->getProperty('default_action')->getValue($this->controller_instance) : 'index');
				$method = $method_prefix . $this->action;

				// Allow to do in controller routing if method router(action, params) exists
				if ($class->hasMethod('router')) {
					$method = 'router';
					$this->method_params = array($this->action, $this->method_params);
				}

				if (!$class->hasMethod($method)) {
					$method = 'action_' . $this->action;
				}

				if ($class->hasMethod($method)) {
					$action = $class->getMethod($method);

					if (!$action->isPublic()) {
						throw new Ecommerce_Core_HttpNotFoundException();
					}

					$class->hasMethod('before') and $class->getMethod('before')->invoke($this->controller_instance);

					$response = $action->invokeArgs($this->controller_instance, $this->method_params);

					$class->hasMethod('after') and $response = $class->getMethod('after')->invoke($this->controller_instance, $response);
				} else {
					throw new Ecommerce_Core_HttpNotFoundException();
				}
			}
		} catch (Exception $e) {
			throw $e;
		}

		// Get the controller's output
		if ($response instanceof Ecommerce_Core_Response) {
			$this->response = $response;
		} else {
			throw new Ecommerce_Core_EcommerceException(get_class($this->controller_instance) . '::' . $method . '() or the controller after() method must return a Response object.');
		}

		return $this;
	}

	/**
	 * Sets the request method.
	 *
	 * @param   string $method request method
	 *
	 * @return  object  current instance
	 */
	public function set_method($method) {
		$this->method = strtoupper($method);
		return $this;
	}

	/**
	 * Returns the request method.
	 *
	 * @return  string  request method
	 */
	public function get_method() {
		return $this->method;
	}

	/**
	 * Gets this Request's Response object;
	 *
	 * Usage:
	 *
	 *     $response = Ecommerce_Core_Request::forge('foo/bar')->execute()->response();
	 *
	 * @return  Ecommerce_Core_Response  This Request's Response object
	 */
	public function response() {
		return $this->response;
	}

	/**
	 * Gets a specific named parameter
	 *
	 * @param   string $param   Name of the parameter
	 * @param   mixed  $default Default value
	 *
	 * @return  mixed
	 */
	public function param($param, $default = null) {
		if (!isset($this->named_params[$param])) {
			return $default;
		}

		return $this->named_params[$param];
	}

	/**
	 * Gets all of the named parameters
	 *
	 * @return  array
	 */
	public function params() {
		return $this->named_params;
	}

	/**
	 * PHP magic function returns the Output of the request.
	 *
	 * Usage:
	 *
	 *     $request = Ecommerce_Core_Request::forge('hello/world')->execute();
	 *     echo $request;
	 *
	 * @return  string  the response
	 */
	public function __toString() {
		return (string)$this->response;
	}
}
