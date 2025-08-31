<?php

class Ecommerce_Core_Router {
	/**
	 *
	 */
	public static $routes = array();

	/**
	 * Defines the controller class prefix. This allows you to namespace controllers
	 */
	protected static $prefix = 'Controller_';

	/**
	 * Add one or multiple routes
	 *
	 * @param string $path
	 * @param null   $options either the translation for $path, an array for verb routing or an instance of Route
	 * @param bool   $prepend whether to prepend the route(s) to the routes array
	 * @param null   $case_sensitive
	 */
	public static function add($path, $options = null, $prepend = false, $case_sensitive = null) {
		if (is_array($path)) {
			// Reverse to keep correct order in prepending
			$prepend and $path = array_reverse($path, true);
			foreach ($path as $p => $t) {
				self::add($p, $t, $prepend);
			}
			return;
		} elseif ($options instanceof Route) {
			self::$routes[$path] = $options;
			return;
		}

		$name = $path;
		if (is_array($options) and array_key_exists('name', $options)) {
			$name = $options['name'];
			unset($options['name']);
			if (count($options) == 1 and !is_array($options[0])) {
				$options = $options[0];
			}
		}

		if ($prepend) {
			$newRoute = new Route($path, $options, $case_sensitive);
			self::$routes = (is_array($name) ? $name : array($name => $newRoute)) + self::$routes;
			return;
		}

		self::$routes[$name] = new Route($path, $options, $case_sensitive);
	}

	/**
	 * Delete one or multiple routes
	 *
	 * @param string $path
	 * @param null   $case_sensitive
	 */
	public static function delete($path, $case_sensitive = null) {
		// support the usual route path placeholders
		$path = str_replace(array(
								 ':any',
								 ':alnum',
								 ':num',
								 ':alpha',
								 ':segment',
							), array(
									'.+',
									'[[:alnum:]]+',
									'[[:digit:]]+',
									'[[:alpha:]]+',
									'[^/]*',
							   ), $path);

		foreach (self::$routes as $name => $route) {
			if ($case_sensitive) {
				if (preg_match('#^' . $path . '$#uD', $name)) {
					unset(self::$routes[$name]);
				}
			} else {
				if (preg_match('#^' . $path . '$#uiD', $name)) {
					unset(self::$routes[$name]);
				}
			}
		}
	}

	/**
	 * Processes the given request using the defined routes
	 *
	 * @param    Ecommerce_Core_Request    $request    the given Request object
	 *
	 * @return    mixed        the match array or false
	 */
	public static function process(Ecommerce_Core_Request $request) {
		$match = false;

		foreach (self::$routes as $route) {
			if ($match = $route->parse($request)) {
				break;
			}
		}

		if (!$match) {
			// Since we didn't find a match, we will create a new route.
			$match = new Ecommerce_Core_Route(preg_quote($request->uri->get(), '#'), $request->uri->get());
			$match->parse($request);
		}

		if ($match->callable !== null) {
			return $match;
		}

		return self::parse_match($match);
	}

	/**
	 * Find the controller that matches the route requested
	 *
	 * @param    Route $match the given Route object
	 *
	 * @return    mixed  the match array or false
	 */
	protected static function parse_match($match) {
		$namespace = '';
		$segments = $match->segments;
		$module = false;

		if ($info = self::parse_segments($segments, $namespace, $module)) {
			$match->controller = $info['controller'];
			$match->action = $info['action'];
			$match->method_params = $info['method_params'];
			return $match;
		} else {
			return null;
		}
	}

	protected static function parse_segments($segments, $namespace = '', $module = false) {
		$temp_segments = $segments;

		foreach (array_reverse($segments, true) as $key => $segment) {
			$class = $namespace . self::$prefix . str_replace(' ', '_', ucwords(str_replace('_', ' ', implode('_', $temp_segments))));
			array_pop($temp_segments);
			if (class_exists($class)) {
				return array(
					'controller'    => $class,
					'action'        => isset($segments[$key + 1]) ? $segments[$key + 1] : null,
					'method_params' => array_slice($segments, $key + 2),
				);
			}
		}

		// Fall back for default module controllers
		if ($module) {
			$class = $namespace . self::$prefix . ucfirst($module);
			if (class_exists($class)) {
				return array(
					'controller'    => $class,
					'action'        => isset($segments[0]) ? $segments[0] : null,
					'method_params' => array_slice($segments, 1),
				);
			}
		}
		return false;
	}
}


