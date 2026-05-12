<?php

class Html2Pdf extends KoiServices {
	const PDF_DOWNLOAD = 'D'; //Force the client to download PDF file when finish() is called.
	const PDF_ASSTRING = 'S'; //Returns the PDF file as a string when finish() is called.
	const PDF_EMBEDDED = 'I'; //When possible, force the client to embed PDF file when finish() is called.
	const PDF_SAVEFILE = 'F'; //PDF file is saved into the server space when finish() is called. The path is returned.
	const PDF_PORTRAIT = 'Portrait'; //PDF generated as landscape (vertical).
	const PDF_LANDSCAPE = 'Landscape'; //PDF generated as landscape (horizontal).

	protected	$service = 'HTML2PDF';
	private		$created;
	private		$localTmpFolder;
	private		$localIncludeFolder;

	public		$html;
	public		$pdfPath;
	public		$fileName;

	public		$orientacion = 'Portrait';
	/*
	public	$tamanio = 'A4';
	public	$tablaDeContenido = false;
	public	$copias = 1;
	public	$escalaDeGrises = false;
	public	$titulo = '';
	*/

	public	$tituloReporte;
	public	$datosCabecera;
	public	$llevaHeader;
	public	$llevaFooter;
	public	$marginTop;
	public	$marginBottom;
	public	$marginRight;
	public	$marginLeft;


	public function __construct(){
		$this->htmlUrlBase = Config::urlBase . 'tmp/html2pdf/';
		$this->localTmpFolder = Config::pathBase . 'tmp/html2pdf/';
		$this->localIncludeFolder = Config::pathBase . 'includes/html2pdf/';
		//$this->exePath = 'C:\\xampp\\php\\HTML2PDF\\wkhtmltopdf.exe';
		$this->created = false;
		$this->llevaHeader = true;
		$this->llevaFooter = true;
		$this->marginLeft = 2;
		$this->marginTop = 30;
		$this->marginRight = 0;
		$this->marginBottom = 15;
	}

	public function create(){
		try {
			$this->createHtml();
			$htmlPath = 'file://' . $this->localTmpFolder . $this->fileName . '.html';
			$this->pdfPath = 'C:\\' . (str_replace('/', '\\', $this->localTmpFolder . $this->fileName . '.pdf'));
			$header = $this->armoHeader();
			$footer = $this->armoFooter();
			$margins = '-L ' . $this->marginLeft . ' -T ' . $this->marginTop . ' -R ' . $this->marginRight . ' -B ' . $this->marginBottom;

			$response = $this->execute(trim(''
				. ' ' . $header . ''
				. ' ' . $footer . ''
				. ' ' . $margins . ''
				//. (($this->copias > 1) ? ' --copies ' . $this->copias : '')			// Número de copias. No anda me parece, pero porque la DLL no lo soporta
				. ' --orientation ' . $this->orientacion							// Orientación (Portrait)
				//. ' --page-size ' . $this->tamanio									// Tamaño de la página (A4)
				//. ($this->tablaDeContenido ? ' --toc' : '')							// Tabla de contenidos
				//. ($this->escalaDeGrises ? ' --grayscale' : '')						// Escala de grises
				//. (($this->titulo != '') ? ' --title "' . $this->titulo . '"' : '')	// Título
				//. ' ' . ($this->htmlUrlBase . $this->fileName . '.html') . ' '												// Archivo HTML
				. ' ' . $htmlPath . ' '												// Archivo HTML
				//. ' ' . "C:\\asd.pdf" . ' '										// Path final del PDF
				. ' ' . $this->pdfPath . ' '										// Path final del PDF
			), ' ');
			if ($response !== 'SUCCESS') {
				throw new Exception('Ocurrió un error al intentar crear el PDF. ' . $response);
			}
			$this->deleteHtml();
			$this->created = true;
		} catch (Exception $ex) {
			$this->deleteFiles();
			throw $ex;
		}

	}

	public function download(){
		if (!$this->created)
			$this->create();
		header('Content-Description: File Transfer');
		header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
		//header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		// Forzar descarga
		header('Content-Type: application/force-download');
		header('Content-Type: application/octet-stream', false);
		header('Content-Type: application/download', false);
		header('Content-Type: application/pdf', false);
		// Se usa Content-Disposition para ponerle un nombre por defecto
		header('Content-Disposition: attachment; filename="' . $this->fileName . '.pdf";');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($this->pdfPath));
		ob_clean();
		flush();
		readfile($this->pdfPath);
	}

	public function open($usoExistente = false){
		$pdfPath = $this->localTmpFolder . $this->fileName . '.pdf';
		$exists = file_exists($pdfPath);
		if ((($exists && !$usoExistente) || (!$exists)) && (!$this->created)){
			$exists && $this->deleteFiles();
			$this->create();
		}
		header('Content-Type: application/pdf');
		header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
		header('Pragma: public');
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s').' GMT');
		header('Content-Length: ' . filesize($pdfPath));
		header('Content-Disposition: inline; filename="' . $this->fileName . '.pdf";');
		readfile($pdfPath);
	}

	public static function getHtmlFromPhp($url) {
		ob_start();
		include($url);
		$html = ob_get_clean();
		return $html;
	}

	protected function deleteHtml(){
		try {
			$fileName = $this->localTmpFolder . $this->fileName . '.html';
			$this->delete($fileName);
			$fileName = $this->localTmpFolder . $this->fileName . '_currentHeader' . '.html';
			$this->delete($fileName);
		} catch (Exception $ex) {
			throw $ex;
		}
	}

	protected function deletePdf(){
		try {
			$fileName = $this->localTmpFolder . $this->fileName . '.pdf';
			$this->delete($fileName);
		} catch (Exception $ex) {
			throw $ex;
		}
	}

	public function deleteFiles(){
		try {
			$this->deleteHtml();
			$this->deletePdf();
		} catch (Exception $ex) {
			throw $ex;
		}
	}

	private function createHtml() {
		if (empty($this->fileName) || !isset($this->fileName))
			$this->fileName = $this->getRandomName();
		$htmlPath = $this->localTmpFolder . $this->fileName . '.html';
		$fh = fopen($htmlPath, 'w');
		$html = '
			<html>
			<head>
				<link href="../../css/styles.css" rel="stylesheet" type="text/css" />
			</head>
			<body>
				' . $this->html . '
			</body>
		';
		fwrite($fh, $html);
		fclose($fh);
		return $html;
	}

	private function armoHeader() {
		$header = '';
		if ($this->llevaHeader) {
			$this->createHeader();
			$header = '--header-html ' . 'file://' . $this->localTmpFolder . $this->fileName . '_currentHeader' . '.html';
			//$header = ' --header-html ' . 'http://desarrollo/tmp/html2pdf/notmp/header.php';
		}
		return $header;
	}

	private function createHeader() {
		//Obtengo el HTML del header
		$headerHtml = $this->getHeaderHtml();
		//Guardo el archivo del header
		$htmlPath = $this->localTmpFolder . $this->fileName . '_currentHeader' . '.html';
		$fh = fopen($htmlPath, 'w');
		fwrite($fh, $headerHtml);
		fclose($fh);
		return $headerHtml;
	}

	private function armoFooter() {
		$footer = '';
		if ($this->llevaFooter) {
			$footer = '--footer-html ' . 'file://' . $this->localIncludeFolder . 'footer' . '.html';
			//De footer uso el número de página
			//$footer = ' --footer-center "Página [page] de [toPage]"';
		}
		return $footer;
	}

	private function delete($fileName) {
		if (file_exists($fileName))
			unlink($fileName);
	}

	private function getRandomName(){
		return uniqid();
	}

	private function getHeaderHtml(){
		$htmlDatos = '';
		foreach($this->datosCabecera as $dato => $valor)
			$htmlDatos .= ($htmlDatos == '' ? '' : '</br>') . '<span>' . $dato . ': <strong>' . $valor . '</strong></span>';
		$html = '
			<html>
			<head>
			<style>
			body {
				margin: 0;
				padding: 0px 0px 20px 0px;
			}
			.tabla {
				width: 100%;
				border-bottom: 1px solid lightgray;
			}
			.tdLogo {
				width: 100px;
				vertical-align: top;
				text-align: center;
			}
			.infoSpiral {
				height: 110px;
				padding-left: 10px;
				border-left: 1px solid lightgray;
				line-height: 24px;
			}
			.infoSpiral>span {
				display: block;
			}
			.tdInfoSpiral {
				width: 200px;
			}
			.tdTitulo {
				width: 280px;
			}
			.tdDatosCabecera {
				width: 170px;
				line-height: 20px;
			}
			</style>
			</head>
			<body>
				<table class="tabla">
					<tr>
						<td class="tdLogo">
							<img src="../../includes/html2pdf/logoHeader.png" />
						</td>
						<td class="tdInfoSpiral">
							<div class="infoSpiral">
								<span>Chaco 2317 - Lanús</span>
								<span>1822</span>
								<span>Buenos Aires</span>
								<span>0810-362-SPIR (7747)</span>
							</div>
						</td>
						<td class="tdTitulo">
							<h1> ' . $this->tituloReporte . '</h1>
						</td>
						<td class="tdDatosCabecera">
							' . $htmlDatos . '
						</td>
					</tr>
				</table>
			</body>
			</html>';
		return $html;
	}
}

?>