<?php

class Html {
	protected function constructFromArray($config){
		foreach($config as $attr => $value){
			if (property_exists($this, $attr)) {
				if ($attr == 'style')
					$this->$attr = HtmlStyle::init(Funciones::keyIsSet($config, 'style'));
				else
					$this->$attr = $value;
			}
		}
	}

	/** @noinspection PhpInconsistentReturnPointsInspection */
	static function echoBotonera($array, $returnNoEcho = false){ //boton, permiso, accion, style
		$permiso = Funciones::keyIsSet($array, 'permiso');
		$accion = (Usuario::logueado()->puede($permiso) ? $array['accion'] : '#');
		$nombreBoton = $array['boton'];
		$boton = $nombreBoton . (Usuario::logueado()->puede($permiso) ? '' : '_off');
		$tamanio = Funciones::keyIsSet($array, 'tamanio', '40');
		$id = Funciones::keyIsSet($array, 'id', 'btn' . ucfirst($nombreBoton) . ($tamanio != '40' ? '_' . $tamanio : ''));
		$class = Funciones::keyIsSet($array, 'class', '');
		$title = Funciones::keyIsSet($array, 'title', ucfirst($nombreBoton));
		$style = in_array('style', $array) ? $array['style'] : '';
		$echo = '<a id="' . $id . '" class="boton ' . $class . '" href="#" onclick="' . $accion . '" title="' . $title . '" style="' . $style . '"><img src="/img/botones/' . $tamanio . '/' . $boton . '.gif" /></a>';
		if ($returnNoEcho)
			return $echo;
		echo $echo;
	}

	/** @noinspection PhpInconsistentReturnPointsInspection */
	static function echoTabla($array, $returnNoEcho = false){
		/* $array estarÃ¡ compuesto de la siguiente manera:
		HTML::echoTabla('config' => arrayDeConfigTable, 'content' => arrayDeContenidoDeLaTable(
				arrayTr('config' => arrayDeConfigTr(), 'content' => arrayDeContenidoDelTr(
					arrayTd('config' => arrayDeConfigTd(), 'content' => '<label>ContenidoDelTd</label>'),
					arrayTd('config' => arrayDeConfigTd(), 'content' => '<input id="" class="textbox obligatorio" type="text" value="ContenidoDelTd" />'),
				),
				arrayTr('config' => arrayDeConfigTr(), 'content' => arrayDeContenidoDelTr(
					arrayTd('config' => arrayDeConfigTd(), 'content' => '<label>ContenidoDelTd</label>'),
					arrayTd('config' => arrayDeConfigTd(), 'content' => '<input id="" class="textbox obligatorio" type="text" value="ContenidoDelTd" />'),
				)
			)
		); */
		$echo = '';
		$configTable = $array['config'];
		$headerTable = $array['header'];
		$contentTable = $array['content'];
		$id = 'id="' . Funciones::keyIsSet($configTable, 'id') . '" ';
		$class = 'class="' . Funciones::keyIsSet($configTable, 'class') . '" ';
		$cellpadding = 'cellpadding="' . Funciones::keyIsSet($configTable, 'cellpadding', '0') . '" ';
		$cellspacing = 'cellspacing="' . Funciones::keyIsSet($configTable, 'cellspacing', '0') . '" ';
		$width = 'width="' . Funciones::keyIsSet($configTable, 'width') . '" ';
		$border = 'border="' . Funciones::keyIsSet($configTable, 'border', '0') . '" ';
		$style = 'style="' . Funciones::keyIsSet($configTable, 'style') . '" ';
		$echo .= '<table ' . $id . $class . $cellpadding . $cellspacing . $width . $border . $style . '>';
		if (isset($headerTable)){
			$idHeader = 'id="' . Funciones::keyIsSet($configTable, 'idHeader') . '" ';
			$echo .= '<thead ' . $idHeader . '>';
			foreach($headerTable as $theadRow){
				$configTheadRow = $theadRow['config'];
				$contentTheadRow = $theadRow['content'];
				$id = 'id="' . Funciones::keyIsSet($configTheadRow, 'id') . '" ';
				$class = 'class="' . Funciones::keyIsSet($configTheadRow, 'class') . '" ';
				$style = 'style="' . Funciones::keyIsSet($configTheadRow, 'style') . '" ';
				$echo .= '<tr ' . $id . $class . $style . '>';
				foreach($contentTheadRow as $th){
					$configTh = $th['config'];
					$contentTh = $th['content'];
					$id = 'id="' . Funciones::keyIsSet($configTh, 'id') . '" ';
					$class = 'class="' . Funciones::keyIsSet($configTh, 'class') . '" ';
					$colspan = 'colspan="' . Funciones::keyIsSet($configTh, 'colspan') . '" ';
					$style = 'style="' . Funciones::keyIsSet($configTh, 'style') . '" ';
					$echo .= '<th ' . $id . $class . $colspan . $style . '>';
					$echo .= $contentTh;
					$echo .= '</th>';
				}
				$echo .= '</tr>';
			}
			$echo .= '</thead>';
		}
		$idContent = 'id="' . Funciones::keyIsSet($configTable, 'idContent') . '" ';
		$echo .= '<tbody ' . $idContent . '>';
		foreach($contentTable as $tr){
			$configTr = $tr['config'];
			$contentTr = $tr['content'];
			$id = 'id="' . Funciones::keyIsSet($configTr, 'id') . '" ';
			$class = 'class="' . Funciones::keyIsSet($configTr, 'class') . '" ';
			$rowspan = 'rowspan="' . Funciones::keyIsSet($configTr, 'rowspan') . '" ';
			$style = 'style="' . Funciones::keyIsSet($configTr, 'style') . '" ';
			$echo .= '<tr ' . $id . $class . $rowspan . $style . '>';
			foreach($contentTr as $td){
				$configTd = $td['config'];
				$contentTd = $td['content'];
				$id = 'id="' . Funciones::keyIsSet($configTd, 'id') . '" ';
				$class = 'class="' . Funciones::keyIsSet($configTd, 'class') . '" ';
				$colspan = 'colspan="' . Funciones::keyIsSet($configTd, 'colspan') . '" ';
				$style = 'style="' . Funciones::keyIsSet($configTd, 'style') . '" ';
				$echo .= '<td ' . $id . $class . $colspan . $style . '>';
				$echo .= $contentTd;
				$echo .= '</td>';
			}
			$echo .= '</tr>';
		}
		$echo .= '</tbody>';
		$echo .= '</table>';
		if ($returnNoEcho)
			return $echo;
		echo $echo;
	}
	static function echoTableFromDataSet($ds, $arrayHeaders, $arrayConfig = array()){
		/*
		 * En arrayHeaders viene algo como:
		 * array('campo_db_feo' => 'Campo DB Lindo')
		 */
		$table = array();
		$table['config'] = array('id' => Funciones::keyIsSet($arrayConfig, 'tableId'), 'class' => Funciones::keyIsSet($arrayConfig, 'tableClass', 'registrosAlternados'));
		$header = array();
		$header[0]['config'] = array('class' => Funciones::keyIsSet($arrayConfig, 'theadClass', 'tableHeader'));
		foreach($arrayHeaders as $headName){
			$th['config'] = array('class' => Funciones::keyIsSet($arrayConfig, 'thClass'));
			$th['content'] = $headName;
			$header[0]['content'][] = $th;
		}
		$table['header'] = $header;
		$content = array();
		foreach($ds as $row) {
			$tr = array();
			$tr['config'] = array('class' => Funciones::keyIsSet($arrayConfig, 'trClass', 'tableRow'));
			foreach($arrayHeaders as $headId => $headName){
				$tr['content'][] = array('config' => Funciones::keyIsSet($arrayConfig, 'tdClass'), 'content' => $row[$headId]);
			}
			$content[] = $tr;
		}
		$table['content'] = $content;
		Html::echoTabla($table);
	}
	static function jsonEncode($msg = '', $json = null, $responseType = JSONResponse::JSON_OBJECT, $nivelMaximo = -1, $nivel = 0, &$acumulador = array(), $abuelo = null){
		//$nivelMaximo -1 = to_do, 0 = RAIZ
		//Esta funciÃ³n recorre objetos segÃºn sus atributos y los va pasando a un JSON.
		//TambiÃ©n lista los atributos protected (para lazy loading).
		//Como parÃ¡metro se le puede indicar el nivel mÃ¡ximo de profundidad a recorrer, siendo el mÃ¡s superficial el CERO.

		//Si no pongo lo de is_array y lo de is_object, un array con count 0 me lo tomaba como NULL
		if (!is_array($json) && !is_object($json) && $json == null && $nivel == 0 && $msg == ''){
			$json = new JSONResponse();
			$json->responseType = JSONResponse::JSON_NULL;
			echo json_encode($json);
			return false;
		}
		$aux = array();
		if (is_object($json) && method_exists($json, 'getObjectVars')) {
			$loopBy = $json->getObjectVars();
		} else {
			$loopBy = $json;
		}
		if (!is_null($loopBy)) {
			foreach($loopBy as $id => $val){
				if (is_object($json)){
					if (isset($acumulador[($abuelo != null ? Funciones::getType($abuelo) . '->' : '') . Funciones::getType($json) . '->' . $val]) && $acumulador[Funciones::getType($json) . '->' . $val] < $nivel)
						return null;
					$acumulador[Funciones::getType($json) . '->' . $val] = $nivel;
					$id = $val;
					$val = $json->$val;
				}
				if (is_scalar($val))
					$val = utf8_encode($val);
				else {
					if (count($val) != 0 && ($nivelMaximo < 0 || ($nivel + 1) <= $nivelMaximo)) //Si el nivelMaximo es negativo o el nivel actual + 1 es menor q el mÃ¡ximo
						$val = Html::jsonEncode($msg, $val, $responseType, $nivelMaximo, $nivel + 1, $acumulador, $json);
				}
				$aux[$id] = $val;
			}
		}
		if ($nivel == 0) {
			$json = new JSONResponse();
			$json->responseType = $responseType;
			$json->responseMsg = Html::utfEncode($msg);
			$json->data = $aux;
			echo json_encode($json);
		} else {
			return $aux;
		}
	}


	
	static function jsonSuccess($msg = 'Completado con exito', $obj = null){
		echo Html::jsonEncode($msg, $obj, JSONResponse::JSON_SUCCESS);
	}
	static function jsonError($msg = 'Ocurrio un error', $obj = null){
        Logger::addError($msg, $obj);
		echo Html::jsonEncode($msg, $obj, JSONResponse::JSON_ERROR);
	}
	static function jsonNull(){
		echo Html::jsonEncode();
	}
	static function jsonEmpty(){
		echo Html::jsonEncode('', array(), JSONResponse::JSON_EMPTY);
	}
	static function jsonConfirm($msg, $codigo = 'confirm'){
		echo Html::jsonEncode($msg, array('&' . $codigo . '=1'), JSONResponse::JSON_CONFIRM);
	}
	static function jsonAlert($msg = 'Completado con advertencia', $obj = null){
        Logger::addWarning($msg, $obj);
		echo Html::jsonEncode($msg, $obj, JSONResponse::JSON_ALERT);
	}
	static function jsonInfo($msg = 'Completado con informacion adicional', $obj = null){
        Logger::addInfo($msg, $obj);
		echo Html::jsonEncode($msg, $obj, JSONResponse::JSON_INFO);
	}
	static function escapeUrl($str){
		return urlencode($str);
	}
	static function unescapeUrl($str){
		return urldecode($str);
	}
	// En clase Html (reemplaza el mÃ©todo actual)
static function utfDecode($obj) {
    // Mantener nulls y vacÃ­os tal cual
    if (!isset($obj)) return $obj;

    // helper interno para quitar slashes si magic_quotes_gpc estÃ¡ activo
    $unslash = function($s) {
        if (!is_string($s)) return $s;
        // get_magic_quotes_gpc existe en 5.6 (deprecado, pero aÃºn disponible)
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            return stripslashes($s);
        }
        return $s;
    };

    // Escalares: NO convertir encoding; las entradas ya estÃ¡n en UTF-8
    if (is_scalar($obj)) {
        return $unslash((string)$obj);
    }

    // Arrays/objetos: procesar recursivo
    // (conservar claves y subestructuras)
    foreach ($obj as $id => $val) {
        if (is_scalar($val)) {
            $val = $unslash((string)$val);
        } else {
            if (!empty($val)) {
                $val = self::utfDecode($val); // recursiÃ³n (no usar Html::utfDecode si esta clase cambia de nombre)
            }
        }
        $obj[$id] = $val;
    }
    return $obj;
}

	static function utfEncode($obj){
		if (is_scalar($obj))
			return utf8_encode($obj);
		else {
			foreach($obj as $id => $val){
				if (is_scalar($val))
					$val = utf8_encode($val);
				else {
					if (count($val) != 0)
						$val = Html::utfEncode($val);
				}
				$obj[$id] = $val;
			}
		}
		return $obj;
	}
	/**
	 * Genera un <select> reutilizable para relaciones con modelos
	 * Compatible con PHP 5.6 y el framework KOI1 legacy
	 * 
	 * @param string $id           ID del select (ej: 'inputCondicionIva')
	 * @param string $clase        Clase del modelo (ej: 'CondicionIva')
	 * @param string $where        Filtro SQL opcional (default: "anulado = 'N'")
	 * @param string|null $selected Valor pre-seleccionado (se castea a string)
	 * @param bool $obligatorio    Si true, no incluye opción vacía
	 * @param array $config        Configuración adicional: 
	 *                             - 'class' => clases CSS adicionales
	 *                             - 'rel' => valor del atributo rel (default: lcfirst($clase))
	 *                             - 'nameField' => campo para el label (default: 'nombre')
	 *                             - 'idField' => campo para el value (default: 'id')
	 * @return string HTML del select
	 */
	public static function select($id, $clase, $where = '', $selected = null, $obligatorio = false, $config = array()) {
		try {
			// Configuración por defecto
			$defaults = array(
				'class' => 'textbox inputForm w230',
				'rel' => lcfirst($clase),
				'nameField' => 'nombre',
				'idField' => 'id'
			);

			// Merge seguro de arrays
			if (!is_array($config)) {
				$config = array();
			}
			$config = array_merge($defaults, $config);

			// Si no se especifica WHERE, usar el default
			if (empty($where)) {
				$where = "anulado = 'N'";
			}

			// Obtener los objetos
			$objs = array();
			try {
				$objs = Factory::getInstance()->getListObject($clase, $where);
				if (!is_array($objs)) {
					$objs = array();
				}
			} catch (Exception $ex) {
				// Log del error para debugging
				error_log("Error obteniendo datos para Html::select() clase $clase: " . $ex->getMessage());
				$objs = array();
			}

			// Construir el HTML
			$html = '<select id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '" ';
			$html .= 'class="' . htmlspecialchars($config['class'], ENT_QUOTES, 'UTF-8') . '" ';
			$html .= 'rel="' . htmlspecialchars($config['rel'], ENT_QUOTES, 'UTF-8') . '"';
			$html .= '>';

			// Opción vacía si no es obligatorio
			if (!$obligatorio) {
				$html .= '<option value="">-- Seleccionar --</option>';
			}

			// Opciones desde los objetos
			$idField = $config['idField'];
			$nameField = $config['nameField'];

			foreach ($objs as $obj) {
				try {
					// Verificar que el objeto tenga las propiedades necesarias
					if (!property_exists($obj, $idField) || !property_exists($obj, $nameField)) {
						continue;
					}

					// Cast a string para evitar problemas de comparación débil
					$value = (string)$obj->$idField;
					$label = (string)$obj->$nameField;

					// Comparación estricta con cast a string
					$isSelected = ((string)$selected === $value);
					$selectedAttr = $isSelected ? ' selected="selected"' : '';

					$html .= '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"' . $selectedAttr . '>';
					$html .= htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
					$html .= '</option>';
				} catch (Exception $ex) {
					// Si falla una opción, continuar con las demás
					error_log("Error procesando opción en Html::select(): " . $ex->getMessage());
					continue;
				}
			}

			$html .= '</select>';

			return $html;

		} catch (Exception $ex) {
			// Si todo falla, devolver un input de texto con mensaje de error
			error_log("Error crítico en Html::select() para $id / $clase: " . $ex->getMessage());
			return '<input type="text" id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '" class="textbox inputForm w230" placeholder="Error al cargar opciones" />';
		}
	}

	/**
	 * Versión que hace echo directo (para mantener consistencia con echoBotonera)
	 */
	public static function echoSelect($id, $clase, $where = '', $selected = null, $obligatorio = false, $config = array()) {
		echo self::select($id, $clase, $where, $selected, $obligatorio, $config);
	}

	/**
	 * Genera un select con búsqueda integrada (Select2) para catálogos grandes
	 * Compatible con PHP 5.6 - Usa Select2 3.5.4 (última versión PHP 5.6 compatible)
	 * 
	 * @param string $id           ID del select
	 * @param string $clase        Clase del modelo para AJAX endpoint
	 * @param string|null $selected Valor pre-seleccionado
	 * @param bool $obligatorio    Si true, no permite vacío
	 * @param array $config        Configuración:
	 *                             - 'placeholder' => texto cuando está vacío
	 *                             - 'minimumInputLength' => caracteres mínimos para buscar (default: 2)
	 *                             - 'rel' => atributo rel (para loadJSON)
	 *                             - 'class' => clases CSS adicionales
	 *                             - 'linkedTo' => array con dependencias ej: ['inputPais' => 'Pais']
	 *                             - 'nameField' => campo para mostrar (default: 'nombre')
	 *                             - 'idField' => campo para value (default: 'id')
	 * @return string HTML del select + script de inicialización
	 */
	public static function selectSearch($id, $clase, $selected = null, $obligatorio = false, $config = array()) {
		try {
			$defaults = array(
				'placeholder' => 'Buscar...',
				'minimumInputLength' => 2,
				'rel' => lcfirst($clase),
				'class' => 'textbox inputForm w230',
				'linkedTo' => array(),
				'nameField' => 'nombre',
				'idField' => 'id'
			);
			$config = array_merge($defaults, $config);

			$html = '';

			// Select HTML con data attributes para inicialización
			$html .= '<select id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '" ';
			$html .= 'class="' . htmlspecialchars($config['class']) . ' select2-search" ';
			$html .= 'rel="' . htmlspecialchars($config['rel'], ENT_QUOTES, 'UTF-8') . '" ';
			$html .= 'data-clase="' . htmlspecialchars($clase, ENT_QUOTES, 'UTF-8') . '" ';
			$html .= 'data-placeholder="' . htmlspecialchars($config['placeholder'], ENT_QUOTES, 'UTF-8') . '" ';
			$html .= 'data-min-length="' . intval($config['minimumInputLength']) . '"';

			// LinkedTo (dependencias)
			if (!empty($config['linkedTo'])) {
				$linkedParts = array();
				foreach ($config['linkedTo'] as $inputId => $filterClase) {
					$linkedParts[] = $inputId . ',' . $filterClase;
				}
				$html .= ' data-linked-to="' . htmlspecialchars(implode(';', $linkedParts), ENT_QUOTES, 'UTF-8') . '"';
			}

			$html .= '>';

			// Opción vacía si no es obligatorio
			if (!$obligatorio) {
				$html .= '<option value=""></option>';
			}

			// Si hay un valor pre-seleccionado, cargar el objeto para mostrarlo
			if ($selected) {
				try {
					$getMethod = 'get' . $clase;
					if (method_exists(Factory::getInstance(), $getMethod)) {
						$obj = Factory::getInstance()->$getMethod($selected);
						$idField = $config['idField'];
						$nameField = $config['nameField'];
						$html .= '<option value="' . htmlspecialchars($obj->$idField, ENT_QUOTES, 'UTF-8') . '" selected="selected">';
						$html .= htmlspecialchars($obj->$nameField, ENT_QUOTES, 'UTF-8');
						$html .= '</option>';
					}
				} catch (Exception $ex) {
					error_log("Error cargando valor seleccionado en selectSearch: " . $ex->getMessage());
				}
			}

			$html .= '</select>';

			// Script de inicialización
			$html .= '<script type="text/javascript">';
			$html .= '$(document).ready(function() {';
			$html .= '  if (typeof $.fn.select2 === "undefined") {';
			$html .= '    console.warn("Select2 no está cargado para #' . $id . '");';
			$html .= '  } else {';
			$html .= '    initSelect2_' . $id . '();';
			$html .= '  }';
			$html .= '});';

			$html .= 'function initSelect2_' . $id . '() {';
			$html .= '  var $select = $("#' . $id . '");';
			$html .= '  var clase = $select.data("clase");';
			$html .= '  var linkedTo = $select.data("linked-to") || "";';
			$html .= '  $select.select2({';
			$html .= '    placeholder: $select.data("placeholder"),';
			$html .= '    allowClear: ' . ($obligatorio ? 'false' : 'true') . ',';
			$html .= '    minimumInputLength: $select.data("min-length"),';
			$html .= '    ajax: {';
			$html .= '      url: "/js/autoSuggestBox/autoSuggestBox.php",';
			$html .= '      dataType: "json",';
			$html .= '      quietMillis: 250,';
			$html .= '      data: function(term, page) {';
			$html .= '        var params = { name: clase, key: term };';
			$html .= '        if (linkedTo) {';
			$html .= '          var links = linkedTo.split(";");';
			$html .= '          for (var i = 0; i < links.length; i++) {';
			$html .= '            var parts = links[i].split(",");';
			$html .= '            var linkedInputId = parts[0];';
			$html .= '            var linkedClase = parts[1];';
			$html .= '            var linkedValue = $("#" + linkedInputId).val();';
			$html .= '            if (linkedValue) {';
			$html .= '              params["id" + linkedClase] = linkedValue;';
			$html .= '            }';
			$html .= '          }';
			$html .= '        }';
			$html .= '        return params;';
			$html .= '      },';
			$html .= '      results: function(data, page) {';
			$html .= '        var items = [];';
			$html .= '        if (data && data.data) {';
			$html .= '          $.each(data.data, function(i, item) {';
			$html .= '            items.push({ id: item.id, text: item.id + " - " + item.nombre });';
			$html .= '          });';
			$html .= '        }';
			$html .= '        return { results: items };';
			$html .= '      }';
			$html .= '    },';
			$html .= '    formatNoMatches: function() { return "Sin resultados"; },';
			$html .= '    formatSearching: function() { return "Buscando..."; },';
			$html .= '    formatInputTooShort: function(input, min) {';
			$html .= '      return "Escriba " + (min - input.length) + " caracteres más";';
			$html .= '    }';
			$html .= '  });';
			$html .= '}';
			$html .= '</script>';

			return $html;

		} catch (Exception $ex) {
			error_log("Error crítico en Html::selectSearch() para $id / $clase: " . $ex->getMessage());
			return '<input type="text" id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '" class="textbox inputForm w230" placeholder="Error al cargar búsqueda" />';
		}
	}

	/**
	 * Versión echo de selectSearch
	 */
	public static function echoSelectSearch($id, $clase, $selected = null, $obligatorio = false, $config = array()) {
		echo self::selectSearch($id, $clase, $selected, $obligatorio, $config);
	}

	/**
	 * Método inteligente que decide automáticamente entre select() o selectSearch()
	 * según el tamaño del catálogo
	 * 
	 * @param string $id           ID del select
	 * @param string $clase        Clase del modelo
	 * @param string $where        Filtro SQL opcional
	 * @param string|null $selected Valor pre-seleccionado
	 * @param bool $obligatorio    Si true, no incluye opción vacía
	 * @param array $config        Configuración adicional
	 * @param int $umbral          Número de registros para cambiar a selectSearch (default: 30)
	 * @return string HTML del select apropiado
	 */
	public static function selectAuto($id, $clase, $where = '', $selected = null, $obligatorio = false, $config = array(), $umbral = 30) {
		try {
			// Si no se especifica WHERE, usar el default
			if (empty($where)) {
				$where = "anulado = 'N'";
			}

			// Contar registros
			$count = 0;
			try {
				$objs = Factory::getInstance()->getListObject($clase, $where);
				$count = is_array($objs) ? count($objs) : 0;
			} catch (Exception $ex) {
				error_log("Error contando registros en selectAuto para $clase: " . $ex->getMessage());
				$count = 0;
			}

			// Decidir qué método usar
			if ($count <= $umbral) {
				// Usar select nativo (rápido, simple)
				return self::select($id, $clase, $where, $selected, $obligatorio, $config);
			} else {
				// Usar selectSearch con AJAX (mejor UX para catálogos grandes)
				return self::selectSearch($id, $clase, $selected, $obligatorio, $config);
			}

		} catch (Exception $ex) {
			error_log("Error en selectAuto para $id / $clase: " . $ex->getMessage());
			// Fallback a select normal
			return self::select($id, $clase, $where, $selected, $obligatorio, $config);
		}
	}

	/**
	 * Versión echo de selectAuto
	 */
	public static function echoSelectAuto($id, $clase, $where = '', $selected = null, $obligatorio = false, $config = array(), $umbral = 30) {
		echo self::selectAuto($id, $clase, $where, $selected, $obligatorio, $config, $umbral);
	}

}