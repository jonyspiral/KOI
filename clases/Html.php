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
		/* $array estará compuesto de la siguiente manera:
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
		//Esta función recorre objetos según sus atributos y los va pasando a un JSON.
		//También lista los atributos protected (para lazy loading).
		//Como parámetro se le puede indicar el nivel máximo de profundidad a recorrer, siendo el más superficial el CERO.

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
					if (count($val) != 0 && ($nivelMaximo < 0 || ($nivel + 1) <= $nivelMaximo)) //Si el nivelMaximo es negativo o el nivel actual + 1 es menor q el máximo
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
	static function jsonSuccess($msg = 'Completado con éxito', $obj = null){
		echo Html::jsonEncode($msg, $obj, JSONResponse::JSON_SUCCESS);
	}
	static function jsonError($msg = 'Ocurrió un error', $obj = null){
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
	static function jsonInfo($msg = 'Completado con información adicional', $obj = null){
        Logger::addInfo($msg, $obj);
		echo Html::jsonEncode($msg, $obj, JSONResponse::JSON_INFO);
	}
	static function escapeUrl($str){
		return urlencode($str);
	}
	static function unescapeUrl($str){
		return urldecode($str);
	}
	static function utfDecode($obj){
        if (!isset($obj))
			return $obj;
		if (is_scalar($obj))
			return utf8_decode((get_magic_quotes_gpc() ? stripslashes($obj) : $obj));
		else {
			foreach($obj as $id => $val){
				if (is_scalar($val))
					$val = utf8_decode((get_magic_quotes_gpc() ? stripslashes($val) : $val));
				else {
					if (count($val) != 0)
						$val = Html::utfDecode($val);
				}
				$obj[$id] = $val;
			}
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
}


?>