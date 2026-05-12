<?php

require_once(Config::pathBase . 'includes/myPHPExcel/myPHPExcel.php');

/** @noinspection PhpInconsistentReturnPointsInspection */
class Html2Xls {
	public	$html;
	public	$fileName;
	public	$tituloReporte;
	public	$datosCabecera;
	public	$excel;
	private	$cantCols;
	private	$captions;
	private	$created;
	private	$lastRow;
	private	$localTmpFolder;

	public function __construct(){
		$this->cantCols = 0;
		$this->captions = array();
		$this->created = false;
		$this->excel = new myPHPExcel();
		$this->excel->setMargins(0, 0, 0, 0);
		$this->localTmpFolder = Config::pathBase . 'tmp/html2xls/';
		$this->lastRow = 0;
	}

	public function create(){
		try {
			$this->createXls();
			$this->created = true;
		} catch (Exception $ex) {
			$this->deleteFiles();
			throw $ex;
		}
	}

	public function download(){
		if (!$this->created)
			$this->create();
		$xlsPath = $this->localTmpFolder . $this->fileName . '.xls';
		header("Content-Length: " . filesize ($xlsPath));
		header("Content-type: application/xls");
		header("Content-disposition: attachment; filename=" . basename($xlsPath));
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		ob_clean();
		flush();
		readfile($xlsPath);
	}

	public function open(){
		if (!$this->created)
			$this->create();
		header('Content-type: application/xls');
		readfile($this->localTmpFolder . $this->fileName . '.xls');
	}

	public static function getHtmlFromPhp($url) {
		ob_start();
		include($url);
		$html = ob_get_clean();
		return $html;
	}

	private function deleteXls(){
		try {
			$fileName = $this->localTmpFolder . $this->fileName . '.xls';
			$this->delete($fileName);
		} catch (Exception $ex) {
			throw $ex;
		}
	}

	public function deleteFiles(){
		try {
			$this->deleteXls();
		} catch (Exception $ex) {
			throw $ex;
		}
	}

	private function createXls() {
		$DOM = new DOMDocument();
		$DOM->loadHTML($this->html);
		$tablas = $DOM->getElementsByTagName('table');
		foreach($tablas as $tabla) {
			$llevaCaption = false;
			switch($tabla->firstChild->nodeName) {
				case 'caption':
				case 'thead':
				case 'tbody':
				case 'tfoot':
					foreach($tabla->childNodes as $child) {
						if ($child->nodeName == 'caption') {
							$this->addCaptionTemp($child);
							$llevaCaption = true;
						} elseif ($child->nodeName == 'thead') {
							$this->addThead($child, $llevaCaption);
							$llevaCaption = false;
						} elseif ($child->nodeName == 'tbody') {
							$this->addTbody($child, $llevaCaption);
						} elseif ($child->nodeName == 'tfoot') {
							$this->addTfoot($child, $llevaCaption);
						} else {
							return false;
						}
					}
					break;
				case 'tr':
					$this->addTrs($tabla->childNodes, $llevaCaption);
					break;
				default:
					return false;
			}
		}

		$this->addTitulo();
		$this->addDatosCabecera();
		$this->addCaptions();
		
		if (empty($this->fileName) || !isset($this->fileName))
			$this->fileName = $this->getRandomName();
		$xlsPath = $this->localTmpFolder . $this->fileName . '.xls';
		$this->excel->guardar($xlsPath);
		return true;
	}

	private function delete($fileName) {
		if (file_exists($fileName))
			unlink($fileName);
	}

	private function getRandomName(){
		return uniqid();
	}


	//############### HTML 2 XLS ###############//
	
	private function addTitulo() {
		$this->excel->font('A1', 'Calibri', 16, 'FF000000', true);
		$this->excel->combinarCeldas('A1:' . $this->getLetra('A', $this->cantCols - 1) . '1');
		$this->excel->alignCenter('A1');
		$this->excel->texto('A1', $this->tituloReporte);
	}

	private function addDatosCabecera() {
		//$this->cantCols no puede ser menor a 4 porque no entran los encabezados
		$colsPerCol = Funciones::roundDown($this->cantCols / 2);
		$colsKey = Funciones::roundDown($colsPerCol / 2);
		$colsVal = $colsPerCol - $colsKey;
		$row = 3;
		$colKey1 = 'A';
		$colVal1 = $this->getLetra($colKey1, $colsKey);
		$colKey2 = $this->getLetra($colKey1, $colsKey + $colsVal);
		$colVal2 = $this->getLetra($colKey1, $colsKey + $colsVal + $colsKey);
		$i = 0;
		foreach($this->datosCabecera as $key => $val) {
			$colKey = $colKey1;
			$colVal = $colVal1;
			if ($i % 2) {
				$colKey = $colKey2;
				$colVal = $colVal2;
			}
			$cellKey = $colKey . $row;
			$cellVal = $colVal . $row;
			if ($colsKey > 1)
				$this->excel->combinarCeldas($cellKey . ':' . $this->getLetra($colKey, $colsKey - 1) . $row);
			if ($colsVal > 1)
				$this->excel->combinarCeldas($cellVal . ':' . $this->getLetra($colVal, $colsVal - 1) . $row);

			$this->excel->font($cellKey, 'Calibri', 12, 'FF000000', true);
			$this->excel->texto($cellKey, $key . ':');
			$this->excel->font($cellVal, 'Calibri', 12, 'FF000000');
			$this->excel->texto($cellVal, $val);

			if ($i % 2)
				$row++;
			$i++;
		}
	}

	private function addCaptions() {
		foreach($this->captions as $caption) {
			$cellInicio = 'A' . $caption['fila'];
			$cellFin = $this->getLetra('A', $this->cantCols - 1) . $caption['fila'];
			$this->excel->combinarCeldas($cellInicio . ':' . $cellFin);
			$this->excel->alignCenter($cellInicio);
			$this->excel->font($cellInicio, 'Calibri', 13, 'FF000000', true);
			$this->excel->texto($cellInicio, $caption['texto']);
		}
	}

	private function getLetra($letraInicial, $desplazamiento) {
		for ($i = 0; $i < $desplazamiento; $i++)
			++$letraInicial;
		return $letraInicial;
	}

	private function addCaptionTemp($caption) {
		$this->captions[] = array('fila' => 0, 'texto' => $caption->textContent);
	}

	private function addThead($thead, $llevaCaption) {
		if ($thead->nodeName == 'thead') {
			$cantRowsDatos = Funciones::roundUp(count($this->datosCabecera) / 2);
			if ($this->lastRow == 0)
				//Título + Espacio + DATOS + Espacio + Acá arranca [+ Caption]
				$this->lastRow = 1 + 1 + $cantRowsDatos + 1 + 1 + ($llevaCaption ? 1 : 0);
			else
				//Dos espacios antes que arranque la tabla nueva
				$this->lastRow += 2;
			if ($llevaCaption) {
				$this->captions[count($this->captions) - 1]['fila'] = $this->lastRow;
				$this->lastRow++;
			}
			$col = 'A';
			$ths = $thead->childNodes;
			if ($thead->firstChild->nodeName == 'tr')
				$ths = $thead->firstChild->childNodes;
			$tempCantCols = 0;
			foreach($ths as $th) {
				$colspan = $th->attributes->getNamedItem('colspan')->nodeValue;
				$this->aplicarEstilos($thead, $th, $col, $colspan, $th->textContent);
				$this->excel->texto($col . $this->lastRow, $th->textContent);
				++$col;
				$tempCantCols++;
			}
			if ($this->cantCols < $tempCantCols)
				$this->cantCols = $tempCantCols;
		}
	}

	private function addTbody($tbody, $llevaCaption) {
		if ($tbody->nodeName == 'tbody') {
			$this->addTrs($tbody->childNodes, $llevaCaption);
		}
	}

	private function addTfoot($tfoot, $llevaCaption) {
		if ($tfoot->nodeName == 'tfoot') {
			$this->addTrs($tfoot->childNodes, $llevaCaption);
		}
	}

	private function addTrs($trs, $llevaCaption) {
		$this->lastRow++;
		if ($this->lastRow == 1) {
			$cantRowsDatos = Funciones::roundUp(count($this->datosCabecera) / 2);
			//Título + Espacio + DATOS + Espacio [+ Caption] + Acá arranca
			$this->lastRow = 1 + 1 + $cantRowsDatos + 1 + 1;
		}
		if ($llevaCaption) {
			$this->captions[count($this->captions) - 1]['fila'] = $this->lastRow;
			$this->lastRow++;
		}
		foreach($trs as $tr) {
			$col = 'A';
			$tempCantCols = 0;
			foreach($tr->childNodes as $td) {
				$colspan = $td->attributes->getNamedItem('colspan')->nodeValue;
				$content = $td->textContent;
				$content = Funciones::reemplazar('###FILA###', $this->lastRow, $content);
				$this->aplicarEstilos($tr, $td, $col, $colspan, $content);
				$this->excel->texto($col . $this->lastRow, $content);
				$col = $this->getLetra($col, $colspan);

				$tempCantCols++;
				if ($this->cantCols < $tempCantCols)
					$this->cantCols = $tempCantCols;
			}
			$this->lastRow++;
		}
	}

	function aplicarEstilos($tr, $td, $col, $colspan, &$content) {
		$trClass = $tr->attributes->getNamedItem('class')->nodeValue;
		$tdClass = $td->attributes->getNamedItem('class')->nodeValue;
		if (strstr($trClass, 'bold') || strstr($trClass, 'tableHeader'))
			$this->excel->bold($col . $this->lastRow);
		if (strstr($tdClass, 'aCenter'))
			$this->excel->alignCenter($col . $this->lastRow);
		if (strstr($tdClass, 'white') || strstr($trClass, 'white') || strstr($trClass, 'tableHeader'))
			$this->excel->color($col . $this->lastRow, '00FFFFFF');
		if (strstr($tdClass, 'bDarkGray') || strstr($trClass, 'bDarkGray'))
			$this->excel->colorRelleno($col . $this->lastRow, '00B6B6BF');
		if (strstr($tdClass, 'bDarkGray') || strstr($trClass, 'bLightGray'))
			$this->excel->colorRelleno($col . $this->lastRow, '00EEEFFF');
		if (strstr($tdClass, 'bBlack') || strstr($trClass, 'bBlack'))
			$this->excel->colorRelleno($col . $this->lastRow, '00000000');
		if (strstr($tdClass, 'bLightRed') || strstr($trClass, 'bLightRed'))
			$this->excel->colorRelleno($col . $this->lastRow, '00F45949');
		if (strstr($tdClass, 'bLightGreen') || strstr($trClass, 'bLightGreen'))
			$this->excel->colorRelleno($col . $this->lastRow, '007CD57C');
		if (strstr($tdClass, 'bLightOrange') || strstr($trClass, 'bLightOrange') || strstr($trClass, 'tableHeader'))
			$this->excel->colorRelleno($col . $this->lastRow, '00EF942F');
		if (strstr($tdClass, 'bDarkOrange') || strstr($trClass, 'bDarkOrange'))
			$this->excel->colorRelleno($col . $this->lastRow, '00C55A29');
		if ($td->attributes->getNamedItem('colspan') && Funciones::toInt($td->attributes->getNamedItem('colspan')->nodeValue) > 1)
			$colspan = Funciones::toInt($td->attributes->getNamedItem('colspan')->nodeValue);
		if ($colspan > 1)
			$this->excel->combinarCeldas($col . $this->lastRow . ':' . $this->getLetra($col, $colspan - 1) . $this->lastRow);
		if (strpos($content, '$') !== false && count(explode(' ', trim($content, ' '))) == 2) {
			$this->excel->currency($col . $this->lastRow);
			$content = Funciones::formatearDecimales($content, 2); //Es para sacarle el signo pesos
		}
		if (Funciones::formatearDecimales($content, 2, ',', '', false) === $content) {
			$this->excel->numericFloat($col . $this->lastRow);
			$content = Funciones::toFloat($content, 2);
		}
		if (Funciones::formatearDecimales($content, 0, ',', '', false) === $content) {
			$this->excel->numeric($col . $this->lastRow);
			$content = Funciones::toInt($content);
		}
	}
	//############### HTML 2 XLS ###############//
}


?>