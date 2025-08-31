<?php

/**
 * @property Array			$rutas
 */

class ManejadorDeImagenes {
	protected static	$_rutas;

	public static function getRutas() {
		if (!isset(self::$_rutas)) {
			$array = array();
			$rutas = Factory::getInstance()->getArrayFromView('ruta_imagenes');
			if (count($rutas) == 0) {
				throw new FactoryExceptionCustomException('No existen rutas de imágenes cargadas');
			}

			foreach ($rutas as $ruta) {
				$array[$ruta['cod_ruta_imagenes']] = $ruta;
			}

			self::$_rutas = $array;
		}
		return self::$_rutas;
	}

	public static function getImgOnErrorHtml() {
		return 'onerror="funciones.imgError(this)"';
	}

	public static function getRutaTablaTallesEshop(ColorPorArticulo $colorPorArticulo) {
		$codigosCategoria = explode(',', TiposRutas::categoriasEcommerceConTablaDeTalles);

		if (in_array($colorPorArticulo->ecommerceCategory->id, $codigosCategoria)) {
			$nombreCategoria = Factory::getInstance()->getCategoriaCalzadoUsuario($colorPorArticulo->ecommerceCategory->id)->nombre;
		} else {
			foreach ($codigosCategoria as $codigo) {
				$categoria = Factory::getInstance()->getCategoriaCalzadoUsuario($codigo);
				if (Funciones::contieneString($colorPorArticulo->ecommerceNombre, $categoria->nombre)) {
					$nombreCategoria = $categoria->nombre;
				}
			}
		}

		return 'http://www.spiralshoes.com/eshop/img/cms/' . $nombreCategoria . '.jpg';
	}
}

?>