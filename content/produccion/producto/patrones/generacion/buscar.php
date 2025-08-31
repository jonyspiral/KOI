<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/producto/patrones/generacion/buscar/')) { ?>
<?php

$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColor');
$idVersion = Funciones::get('idVersion');
$clonar = Funciones::get('clonar') == 'S';
$idArticuloClonar = Funciones::get('idArticuloClonar');
$idColorClonar = Funciones::get('idColorClonar');
$copiarImagenes = Funciones::get('copiarImagenes') == 'S';
$confirmar = (Funciones::get('confirm') == '1');

try {
	Factory::getInstance()->beginTransaction();

	$patronOriginal = Factory::getInstance()->getPatron($idArticulo, $idColor, $idVersion);

	if ($clonar) {
		$colorPorArticulo = Factory::getInstance()->getColorPorArticulo($idArticuloClonar, $idColorClonar);

		if (count($patronOriginal->detalle) == 0) {
			throw new FactoryExceptionCustomException('No se puede clonar un patrón que no posee detalles');
		}

		$patronOriginal->idArticulo = null;
		$patronOriginal->idColorPorArticulo = null;
		$patronOriginal->articulo = $colorPorArticulo->articulo;
		$patronOriginal->colorPorArticulo = $colorPorArticulo;
		$ultimoPatron = $colorPorArticulo->getUltimoPatron();
		$patronOriginal->version = $ultimoPatron + 1;
		$patronOriginal->tipoPatron = 'D';
		$patronOriginal->confirmado = 'N';
		$patronOriginal->versionActual = 'N';

		Factory::getInstance()->marcarParaInsertar($patronOriginal);
		for ($i = 0; $i < count($patronOriginal->detalle); $i++) {
			$patronOriginal->detalle[$i]->idArticulo = null;
			$patronOriginal->detalle[$i]->idColorPorArticulo = null;

			$patronOriginal->detalle[$i]->articulo = $patronOriginal->articulo;
			$patronOriginal->detalle[$i]->colorPorArticulo = $patronOriginal->colorPorArticulo;
			$patronOriginal->detalle[$i]->version = $patronOriginal->version;

			Factory::getInstance()->marcarParaInsertar($patronOriginal->detalle[$i]);
		}
		$patronOriginal->guardar();

		$verificarPatron = Factory::getInstance()->getPatron($patronOriginal->colorPorArticulo->articulo->id, $patronOriginal->colorPorArticulo->id, $patronOriginal->version);
		if (count($verificarPatron->detalle) == 0) {
			throw new FactoryExceptionCustomException('Ocurrió un error al intentar copiar los detalles del patrón');
		}

		if ($copiarImagenes) {
			$manejadorFtp = Factory::getInstance()->getManejadorFtp('ftp.spiralshoes.com', 'developement@spiralshoes.com', 'KoiDevelopement201');
			$rutas = ManejadorDeImagenes::getRutas();
			$codigosRutas = explode(',', TiposRutas::imagenesClonables);
			$arrayRutaImagenesBorrar = array();
			$imagenesConError = array();

			foreach ($codigosRutas as $codigoRuta) {
				$idArticuloIdColor = $idArticuloClonar . '_' . $idColorClonar . '_';
				$nombreArchivoDescargar = $idArticuloIdColor . $colorPorArticulo->getUltimoPatronVigente() . ExtensionImagen::png;
				$nuevoNombreArchivo = $idArticuloIdColor . ($colorPorArticulo->getUltimoPatron() + 1) . ExtensionImagen::png;

				$manejadorFtp->changeDir($rutas[$codigoRuta]['ruta_ftp_write']);
				$archivoExistente = $manejadorFtp->downloadFile($nombreArchivoDescargar, $nuevoNombreArchivo);
				$archivoSubido = $manejadorFtp->uploadFile($nuevoNombreArchivo, $nuevoNombreArchivo, true);

				if (!$archivoExistente && !$archivoSubido) {
					$hayQueConfirmar = true;
					$imagenesConError[] = $rutas[$codigoRuta]['nombre'];
				} else {
					$arrayRutaImagenesBorrar[] = $rutas[$codigoRuta]['ruta_ftp_write'];
				}
			}

			if (!$confirmar && $hayQueConfirmar) {
				//Rollbackeo
				foreach ($arrayRutaImagenesBorrar as $item) {
					$manejadorFtp->changeDir($item);
					$manejadorFtp->delete($nuevoNombreArchivo);
				}

				$strImagenesConError = '';
				foreach ($imagenesConError as $imagenConError) {
					$strImagenesConError .= $imagenConError . ', ';
				}
				$strImagenesConError = trim($strImagenesConError, ', ');

				Html::jsonConfirm('No se pudieron copiar las siguientes vistas de imagen: ' . $strImagenesConError . '. ¿Deseea realizar la clonación del patrón de todos modos?');
			}
		}
	}

	if ($confirmar || !$hayQueConfirmar) {
		$return = array(
			'idArticulo' => $patronOriginal->articulo->id,
			'idColor' => $patronOriginal->colorPorArticulo->id,
			'idVersion' => $patronOriginal->version
		);

		$detalle = array();
		foreach ($patronOriginal->detalle as $item) {
			/** @var $item PatronItem */
			$itemDetalle = array(
				'idSeccion' => array('id' => $item->seccion->id, 'nombre' => $item->seccion->nombre),
				'idConjunto' => array('id' => $item->conjunto->id, 'nombre' => $item->conjunto->nombre),
				'idMaterial' => array('id' => $item->material->id, 'nombre' => $item->material->nombre),
				'unidadDeMedida' => $item->material->unidadDeMedida->nombre,
				'idColor' => array('id' => $item->colorMateriaPrima->idColor, 'nombre' => $item->colorMateriaPrima->nombreColor),
				'consumoPar' => $item->consumoPar
			);
			$detalle[] = $itemDetalle;
		}

		$return['detalle'] = $detalle;

		Factory::getInstance()->commitTransaction();

		Html::jsonEncode('', $return);
	}

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>