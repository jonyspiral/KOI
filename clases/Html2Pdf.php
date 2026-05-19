<?php

class Html2Pdf extends KoiServices {
    const PDF_DOWNLOAD = 'D';
    const PDF_ASSTRING = 'S';
    const PDF_EMBEDDED = 'I';
    const PDF_SAVEFILE = 'F';
    const PDF_PORTRAIT = 'Portrait';
    const PDF_LANDSCAPE = 'Landscape';

    protected $service = 'HTML2PDF';
    private $created;
    private $localTmpFolder;
    private $localIncludeFolder;

    public $html;
    public $pdfPath;
    public $fileName;

    public $orientacion = 'Portrait';
    /*
    public $tamanio = 'A4';
    public $tablaDeContenido = false;
    public $copias = 1;
    public $escalaDeGrises = false;
    public $titulo = '';
    */

    public $tituloReporte;
    public $datosCabecera;
    public $llevaHeader;
    public $llevaFooter;
    public $marginTop;
    public $marginBottom;
    public $marginRight;
    public $marginLeft;

    public function __construct() {
        $runtimeBasePath = $this->getRuntimeBasePath();
        $this->htmlUrlBase = Config::urlBase . 'tmp/html2pdf/';
        $this->localTmpFolder = $runtimeBasePath . 'tmp/html2pdf/';
        $this->localIncludeFolder = $runtimeBasePath . 'includes/html2pdf/';
        $this->ensureDirectory($this->localTmpFolder);
        $this->created = false;
        $this->llevaHeader = true;
        $this->llevaFooter = true;
        $this->marginLeft = 2;
        $this->marginTop = 30;
        $this->marginRight = 0;
        $this->marginBottom = 15;
    }

    public function create() {
        try {
            $this->createHtml();
            $htmlPath = 'file://' . $this->localTmpFolder . $this->fileName . '.html';
            $this->pdfPath = $this->getOutputPdfPath();
            $header = $this->armoHeader();
            $footer = $this->armoFooter();
            $margins = '-L ' . $this->marginLeft . ' -T ' . $this->marginTop . ' -R ' . $this->marginRight . ' -B ' . $this->marginBottom;

            if ($this->shouldUseLocalBinary()) {
                $this->createWithLocalBinary($htmlPath, $header, $footer, $margins);
            } else {
                $response = $this->execute(trim(''
                    . ' ' . $header . ''
                    . ' ' . $footer . ''
                    . ' ' . $margins . ''
                    . ' --orientation ' . $this->orientacion
                    . ' ' . $htmlPath . ' '
                    . ' ' . $this->pdfPath . ' '
                ), ' ');
                if ($response !== 'SUCCESS') {
                    throw new Exception('Ocurrio un error al intentar crear el PDF. ' . $response);
                }
            }

            $this->deleteHtml();
            $this->created = true;
        } catch (Exception $ex) {
            $this->deleteFiles();
            throw $ex;
        }
    }

    public function download() {
        if (!$this->created) {
            $this->create();
        }
        header('Content-Description: File Transfer');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream', false);
        header('Content-Type: application/download', false);
        header('Content-Type: application/pdf', false);
        header('Content-Disposition: attachment; filename="' . $this->fileName . '.pdf";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($this->pdfPath));
        ob_clean();
        flush();
        readfile($this->pdfPath);
    }

    public function open($usoExistente = false) {
        $pdfPath = $this->localTmpFolder . $this->fileName . '.pdf';
        $exists = file_exists($pdfPath);
        if ((($exists && !$usoExistente) || (!$exists)) && (!$this->created)) {
            if ($exists) {
                $this->deleteFiles();
            }
            $this->create();
        }
        header('Content-Type: application/pdf');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
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

    protected function deleteHtml() {
        try {
            $fileName = $this->localTmpFolder . $this->fileName . '.html';
            $this->delete($fileName);
            $fileName = $this->localTmpFolder . $this->fileName . '_currentHeader.html';
            $this->delete($fileName);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    protected function deletePdf() {
        try {
            $fileName = $this->localTmpFolder . $this->fileName . '.pdf';
            $this->delete($fileName);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function deleteFiles() {
        try {
            $this->deleteHtml();
            $this->deletePdf();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    private function createHtml() {
        if (empty($this->fileName) || !isset($this->fileName)) {
            $this->fileName = $this->getRandomName();
        }
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
            $header = '--header-html ' . escapeshellarg('file://' . $this->localTmpFolder . $this->fileName . '_currentHeader.html');
        }
        return $header;
    }

    private function createHeader() {
        $headerHtml = $this->getHeaderHtml();
        $htmlPath = $this->localTmpFolder . $this->fileName . '_currentHeader.html';
        $fh = fopen($htmlPath, 'w');
        fwrite($fh, $headerHtml);
        fclose($fh);
        return $headerHtml;
    }

    private function armoFooter() {
        $footer = '';
        if ($this->llevaFooter) {
            $footer = '--footer-html ' . escapeshellarg('file://' . $this->localIncludeFolder . 'footer.html');
        }
        return $footer;
    }

    private function delete($fileName) {
        if (file_exists($fileName)) {
            unlink($fileName);
        }
    }
    private function getRuntimeBasePath() {
        $configBasePath = Config::pathBase;
        if (!empty($configBasePath) && is_dir($configBasePath)) {
            return rtrim($configBasePath, '/\\') . DIRECTORY_SEPARATOR;
        }
        return dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }

    private function ensureDirectory($path) {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function getOutputPdfPath() {
        if (DIRECTORY_SEPARATOR === '/') {
            return $this->localTmpFolder . $this->fileName . '.pdf';
        }
        return 'C:\\' . str_replace('/', '\\\\', $this->localTmpFolder . $this->fileName . '.pdf');
    }

    private function shouldUseLocalBinary() {
        return DIRECTORY_SEPARATOR === '/';
    }

    private function createWithLocalBinary($htmlPath, $header, $footer, $margins) {
        $commandParts = array();
        if (file_exists('/usr/bin/xvfb-run')) {
            $commandParts[] = '/usr/bin/xvfb-run';
            $commandParts[] = '-a';
        }

        $commandParts[] = '/usr/bin/wkhtmltopdf';
        $commandParts[] = '--enable-local-file-access';

        if ($header !== '') {
            $commandParts[] = $header;
        }
        if ($footer !== '') {
            $commandParts[] = $footer;
        }

        $commandParts[] = $margins;
        $commandParts[] = '--orientation ' . escapeshellarg($this->orientacion);
        $commandParts[] = escapeshellarg($htmlPath);
        $commandParts[] = escapeshellarg($this->pdfPath);

        $command = implode(' ', $commandParts);
        $output = array();
        $returnVar = 0;
        exec($command . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0 || !file_exists($this->pdfPath)) {
            $msg = trim(implode("\n", $output));
            if ($msg === '') {
                $msg = 'wkhtmltopdf no pudo generar el archivo';
            }
            throw new Exception('Ocurrio un error al intentar crear el PDF. ' . $msg);
        }
    }

    private function getRandomName() {
        return uniqid();
    }

    private function getHeaderHtml() {
        $htmlDatos = '';
        foreach ($this->datosCabecera as $dato => $valor) {
            $htmlDatos .= ($htmlDatos == '' ? '' : '</br>') . '<span>' . $dato . ': <strong>' . $valor . '</strong></span>';
        }
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
                            <img src="../../includes/html2pdf/logoHeader2.png" />
                        </td>
                        <td class="tdInfoSpiral">
                            <div class="infoSpiral">
                                <span>Chaco 2317 - Lanus</span>
                                <span>1822</span>
                                <span>Buenos Aires</span>
                                <span>1142186382</span>
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