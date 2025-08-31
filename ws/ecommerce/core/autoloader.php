<?php

/**
 * The Autloader is responsible for all class loading.  It allows you to define
 * different load paths based on namespaces.  It also lets you set explicit paths
 * for classes to be loaded from.
 *
 * @package     Ecommerce
 * @subpackage  Core
 */
class Ecommerce_Core_Autoloader {
	/**
	 * @var  array  $classes  holds all the classes and paths
	 */
	protected static $classes = array();

	/**
	 * @var  array  holds all the namespace paths
	 */
	protected static $namespaces = array();

	/**
	 * @var  array  list off namespaces of which classes will be aliased to global namespace
	 */
	protected static $core_namespaces = array(
		'Ecommerce_Core'
	);


	/**
	 * Adds a namespace search path.  Any class in the given namespace will be
	 * looked for in the given path.
	 *
	 * @param mixed $namespace	the namespace
	 * @param mixed $path		the path
	 *
	 * @return  void
	 */
	public static function add_namespace($namespace, $path) {
		self::$namespaces[$namespace] = $path;
	}

	/**
	 * Adds an array of namespace paths. See {add_namespace}.
	 *
	 * @param   array mixed the namespaces
	 * @param   bool  mixed whether to prepend the namespace to the search path
	 * @return  void
	 */
	public static function add_namespaces(array $namespaces, $prepend = false) {
		if (!$prepend) {
			self::$namespaces = array_merge(self::$namespaces, $namespaces);
		} else {
			self::$namespaces = $namespaces + self::$namespaces;
		}
	}

	/**
	 * Adds a classes load path.  Any class added here will not be searched for
	 * but explicitly loaded from the path.
	 *
	 * @param   string mixed the class name
	 * @param   string mixed the path to the class file
	 * @return  void
	 */
	public static function add_class($class, $path) {
		self::$classes[$class] = $path;
	}

	/**
	 * Adds multiple class paths to the load path. See {@see Ecommerce_Core_Autoloader::add_class}.
	 *
	 * @param   array mixed the class names and paths
	 * @return  void
	 */
	public static function add_classes($classes) {
		foreach ($classes as $class => $path) {
			self::$classes[$class] = $path;
		}
	}

	/**
	 * Register's the autoloader to the SPL autoload stack.
	 *
	 * @return	void
	 */
	public static function register() {
		spl_autoload_register('Ecommerce_Core_Autoloader::load', true);
	}

	/**
	 * Returns the class with namespace prefix when available
	 *
	 * @param	string
	 * @return	bool|string
	 */
	protected static function find_core_class($class) {
		foreach (self::$core_namespaces as $ns) {
			if (array_key_exists($ns_class = $ns . '_' . $class, self::$classes)) {
				return $ns_class;
			}
		}

		return false;
	}

	/**
	 * Loads a class.
	 *
	 * @param   string  $class  Class to load
	 * @return  bool    If it loaded the class
	 */
	public static function load($class) {
		// Si están llamando a un método con 'self::classname' entonces ya está cargado
		if (strpos($class, 'self::') === 0) {
			return true;
		}

		$loaded = false;
		$class = ltrim($class, '_');

		if (isset(self::$classes[$class])) {
			include self::$classes[$class]; //str_replace('/', DIRECTORY_SEPARATOR, self::$classes[$class]);
			$loaded = true;
		} elseif ($full_class = self::find_core_class($class)) {
			if (!class_exists($full_class, false) and !interface_exists($full_class, false)) {
				include self::prep_path(self::$classes[$full_class]);
			}
			$loaded = true;
		} else {
			$path = APPPATH . self::class_to_path($class);

			if (file_exists($path)) {
				include $path;
				$loaded = true;
			}
		}

		return $loaded;
	}

	/**
	 * Takes a class name and turns it into a path.  It follows the PSR-0
	 * standard, except for makes the entire path lower case, unless you
	 * tell it otherwise.
	 *
	 * Note: This does not check if the file exists...just gets the path
	 *
	 * @param   string  $class  Class name
	 * @return  string  Path for the class
	 */
	protected static function class_to_path($class) {
		$file  = '';
		if ($last_ns_pos = strripos($class, '_')) {
			$namespace = substr($class, 0, $last_ns_pos);
			$class = substr($class, $last_ns_pos + 1);
			$file = str_replace('_', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$file .= str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
		$file = strtolower($file);

		return $file;
	}

	/**
	 * Prepares a given path by making sure the directory separators are correct.
	 *
	 * @param   string  $path  Path to prepare
	 * @return  string  Prepped path
	 */
	protected static function prep_path($path) {
		return str_replace(array('/', '_'), DIRECTORY_SEPARATOR, $path);
	}
}
