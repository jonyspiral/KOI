<?php

/**
 * Format class
 *
 * Help convert between various formats such as XML, JSON, CSV, etc.
 *
 * @package    Fuel
 * @category   Core
 * @author     Fuel Development Team
 * @copyright  2010 - 2012 Fuel Development Team
 * @link       http://docs.fuelphp.com/classes/format.html
 */
class Ecommerce_Core_Format {
	/**
	 * @var  array|mixed  input to convert
	 */
	protected $_data = array();

	/**
	 * Returns an instance of the Format object.
	 *
	 *     echo Format::forge(array('foo' => 'bar'))->to_xml();
	 *
	 * @param   mixed $data  general date to be converted
	 * @param   string $from_type data format the file was provided in
	 *
	 * @return  Ecommerce_Core_Format
	 */
	public static function forge($data = null, $from_type = null) {
		return new self($data, $from_type);
	}

	/**
	 * Do not use this directly, call forge()
	 */
	public function __construct($data = null, $from_type = null) {
		// If the provided data is already formatted we should probably convert it to an array
		if ($from_type !== null) {
			if (method_exists($this, '_from_' . $from_type)) {
				$data = call_user_func(array($this, '_from_' . $from_type), $data);
			} else {
				throw new Ecommerce_Core_EcommerceException('Format class does not support conversion from "' . $from_type . '".');
			}
		}

		$this->_data = $data;
	}

	// FORMATING OUTPUT ---------------------------------------------------------

	/**
	 * To array conversion
	 *
	 * Goes through the input and makes sure everything is either a scalar value or array
	 *
	 * @param   mixed $data
	 *
	 * @return  array
	 */
	public function to_array($data = null) {
		if ($data === null) {
			$data = $this->_data;
		}

		$array = array();

		if (is_object($data) and !$data instanceof Iterator) {
			$data = get_object_vars($data);
		}

		if (empty($data)) {
			return array();
		}

		foreach ($data as $key => $value) {
			if (is_object($value) or is_array($value)) {
				$array[$key] = $this->to_array($value);
			} else {
				$array[$key] = $value;
			}
		}

		return $array;
	}

	/**
	 * To XML conversion
	 *
	 * @param   mixed       $data
	 * @param   null        $structure
	 * @param   null|string $basenode
	 *
	 * @return  string
	 */
	public function to_xml($data = null, $structure = null, $basenode = 'xml') {
		if ($data == null) {
			$data = $this->_data;
		}

		// turn off compatibility mode as simple xml throws a wobbly if you don't.
		if (ini_get('zend.ze1_compatibility_mode') == 1) {
			ini_set('zend.ze1_compatibility_mode', 0);
		}

		if ($structure == null) {
			$structure = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$basenode />");
		}

		// Force it to be something useful
		if (!is_array($data) and !is_object($data)) {
			$data = (array)$data;
		}

		foreach ($data as $key => $value) {
			// no numeric keys in our xml please!
			if (is_numeric($key)) {
				// make string key...
				//$key = (Inflector::singularize($basenode) != $basenode) ? Inflector::singularize($basenode) : 'item';
				$key = $basenode;
			}

			// replace anything not alpha numeric
			$key = preg_replace('/[^a-z_\-0-9]/i', '', $key);

			// if there is another array found recrusively call this function
			if (is_array($value) or is_object($value)) {
				$node = $structure->addChild($key);

				// recursive call if value is not empty
				if (!empty($value)) {
					$this->to_xml($value, $node, $key);
				}
			} else {
				// add single node.
				$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, "UTF-8");

				$structure->addChild($key, $value);
			}
		}

		// pass back as string. or simple xml object if you want!
		return $structure->asXML();
	}

	/**
	 * To JSON conversion
	 *
	 * @param   mixed $data
	 * @param         bool $pretty  wether to make the json pretty
	 *
	 * @return  string
	 */
	public function to_json($data = null, $pretty = false) {
		if ($data == null) {
			$data = $this->_data;
		}

		// To allow exporting ArrayAccess objects like Orm\Model instances they need to be
		// converted to an array first
		$data = (is_array($data) or is_object($data)) ? $this->to_array($data) : $data;
		//$data = (is_object($data)) ? $this->to_array($data) : $data; //Arreglo mio porque no funciona ->to_array de un array!
		return $pretty ? self::pretty_json($data) : json_encode($data);
	}

	/**
	 * To JSONP conversion
	 *
	 * @param   mixed  $data
	 * @param   bool   $pretty   wether to make the json pretty
	 * @param   string $callback JSONP callback
	 *
	 * @return  string  formatted JSONP
	 */
	public function to_jsonp($data = null, $pretty = false, $callback = null) {
		$callback or $callback = Ecommerce_Core_Input::param('callback');
		is_null($callback) and $callback = 'response';

		return $callback . '(' . $this->to_json($data, $pretty) . ')';
	}

	/**
	 * Serialize
	 *
	 * @param   mixed $data
	 *
	 * @return  string
	 */
	public function to_serialized($data = null) {
		if ($data == null) {
			$data = $this->_data;
		}

		return serialize($data);
	}

	/**
	 * Return as a string representing the PHP structure
	 *
	 * @param   mixed $data
	 *
	 * @return  string
	 */
	public function to_php($data = null) {
		if ($data == null) {
			$data = $this->_data;
		}

		return var_export($data, true);
	}

	/**
	 * Return as a string representing the HTML structure
	 *
	 * @param   mixed $data
	 *
	 * @return  string
	 */
	public function to_html($data = null) {
		if ($data == null) {
			$data = $this->_data;
		}

		return $data;
	}

	/**
	 * Import XML data
	 *
	 * @param   string $string
	 *
	 * @return  array
	 */
	protected function _from_xml($string) {
		$_arr = is_string($string) ? simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA) : $string;
		$arr = array();

		// Convert all objects SimpleXMLElement to array recursively
		foreach ((array)$_arr as $key => $val) {
			$arr[$key] = (is_array($val) or is_object($val)) ? $this->_from_xml($val) : $val;
		}

		return $arr;
	}

	/**
	 * Import CSV data
	 *
	 * @param   string $string
	 *
	 * @return  array
	 */
	protected function _from_csv($string) {
		$data = array();

		$rows = preg_split('/(?<=' . preg_quote('"') . ')' . '\n' . '/', trim($string));

		// csv config
		$delimiter = ',';
		$enclosure = '"';
		$escape = '\\';

		// Get the headings
		$headings = str_replace($escape . $enclosure, $enclosure, str_getcsv(array_shift($rows), $delimiter, $enclosure, $escape));

		foreach ($rows as $row) {
			$data_fields = str_replace($escape . $enclosure, $enclosure, str_getcsv($row, $delimiter, $enclosure, $escape));

			if (count($data_fields) == count($headings)) {
				$data[] = array_combine($headings, $data_fields);
			}

		}

		return $data;
	}

	/**
	 * Import JSON data
	 *
	 * @param   string $string
	 *
	 * @return  mixed
	 */
	private function _from_json($string) {
		return json_decode(trim($string));
	}

	/**
	 * Import Serialized data
	 *
	 * @param   string $string
	 *
	 * @return  mixed
	 */
	private function _from_serialize($string) {
		return unserialize(trim($string));
	}

	/**
	 * Makes json pretty the json output.
	 * Barrowed from http://www.php.net/manual/en/function.json-encode.php#80339
	 *
	 * @param $data
	 *
	 * @internal param string $json json encoded array
	 *
	 * @return  string|false  pretty json output or false when the input was not valid
	 */
	protected static function pretty_json($data) {
		$json = json_encode($data);

		if (!$json) {
			return false;
		}

		$tab = "\t";
		$newline = "\n";
		$new_json = "";
		$indent_level = 0;
		$in_string = false;
		$len = strlen($json);

		for ($c = 0; $c < $len; $c++) {
			$char = $json[$c];
			switch ($char) {
				case '{':
				case '[':
					if (!$in_string) {
						$new_json .= $char . $newline . str_repeat($tab, $indent_level + 1);
						$indent_level++;
					} else {
						$new_json .= $char;
					}
					break;
				case '}':
				case ']':
					if (!$in_string) {
						$indent_level--;
						$new_json .= $newline . str_repeat($tab, $indent_level) . $char;
					} else {
						$new_json .= $char;
					}
					break;
				case ',':
					if (!$in_string) {
						$new_json .= ',' . $newline . str_repeat($tab, $indent_level);
					} else {
						$new_json .= $char;
					}
					break;
				case ':':
					if (!$in_string) {
						$new_json .= ': ';
					} else {
						$new_json .= $char;
					}
					break;
				/** @noinspection PhpMissingBreakStatementInspection */
				case '"':
					if ($c > 0 and $json[$c - 1] !== '\\') {
						$in_string = !$in_string;
					}
				default:
					$new_json .= $char;
					break;
			}
		}

		return $new_json;
	}
}
