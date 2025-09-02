<?php

/**
 * @property Articulo					$articulo
 * @property CategoriaCalzadoUsuario	$categoriaCalzadoUsuario
 * @property CategoriaCalzadoUsuario	$ecommerceCategory
 * @property Color						$color
 * @property CurvaPorArticulo[]			$curvas
 * @property string						$formaDeComercializacionNombre
 * @property string					 	$formaDeComercializacionNombreCorto
 * @property TipoProductoStock			$tipoProductoStock
 * @property int[]						$stock
 * @property int[]						$stockMenosPendiente
 */

class ColorPorArticulo extends Base {
	const		_primaryKey = '["idArticulo", "id"]';

	public		$idArticulo;
	protected	$_articulo;
	public		$id;
	public		$nombre;
	public		$idColor;
	protected	$_color;
	public		$idCategoriaCalzadoUsuario;
	protected	$_categoriaCalzadoUsuario;
	protected	$_curvas;
	public		$formaDeComercializacion;	//"M"odular o por "L"ibre. Si es modular tengo que ir a buscar la curva
	protected	$_formaDeComercializacionNombre;
	protected	$_formaDeComercializacionNombreCorto;
	public		$fotos;					//Array de 0 a 12
	public		$clasificacionComercial;
	public		$textoVarios;
	public		$textoPuntera;
	public		$textoTalon;
	public		$textoLengua;
	public		$textoLadoInterno;
	public		$textoCania;
	public		$precioDistribuidor;
	public		$precioDistribuidorMinorista;
	public		$precioListaDistribuidor;
	public		$precioMayoristaDolar;
	public		$precioMinoristaDolar;
	public		$precioRecargado;
	public		$idTipoProductoStock;
	protected	$_tipoProductoStock;
	protected	$_stock;
	protected	$_stockMenosPendiente;
	public		$vigente;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;
	protected	$_ultimoPatronVigente;
	protected	$_ultimoPatron;

	//Atributos de Ecommerce
	public		$ecommerceExiste;
	public		$ecommerceFechaUltimaSinc;
	public		$ecommerceNombre;
	public		$ecommerceInfo;
	public		$ecommerceForSale;
	public		$idEcommerceCategory;
	protected	$_ecommerceCategory;
	public		$ecommerceCondition;
	public		$ecommerceExclusive;
	public		$ecommerceFeatured;
	public		$ecommercePrice1;
	public		$ecommercePrice2;
	public		$ecommercePrice3;
	public		$ecommerceImage1;
	public      $referenciaWebMayorista;

	public function getAnterior() {
		$coloresPorArticulo = Factory::getInstance()->getListObject('ColorPorArticulo', 'cod_articulo = ' . Datos::objectToDB($this->idArticulo) . ' AND vigente = ' . Datos::objectToDB('S'));
		$i = 1;
		foreach ($coloresPorArticulo as $colorPorArticulo) {
			/** @var ColorPorArticulo $colorPorArticulo */
			if ($colorPorArticulo->id == $this->id) {
				if ($i == 1) {
					$posibleIdAnterior = $this->idArticulo - 1;
					$condicion = true;
					$j = 1;
					while ($condicion || $j < 2000) {
						$j++;
						try {
							$posibleArticuloSiguiente = Factory::getInstance()->getArticulo($posibleIdAnterior);
							if (!$posibleArticuloSiguiente->vigente()) {
								throw new FactoryExceptionRegistroNoExistente();
							}
							$coloresPorArticulo = Factory::getInstance()->getListObject('ColorPorArticulo', 'cod_articulo = ' . Datos::objectToDB($posibleArticuloSiguiente->id) . ' AND vigente = ' . Datos::objectToDB('S'));
							$colorReturn = $coloresPorArticulo[count($coloresPorArticulo) - 1];
							break;
						} catch (FactoryExceptionRegistroNoExistente $ex) {
							$posibleIdAnterior++;
						}
					}
				} else {
					$colorReturn = $coloresPorArticulo[$i - 2];
					break;
				}
			}
			$i++;
		}

		return array('idArticulo' => $colorReturn->idArticulo, 'idColor' => $colorReturn->id);
	}

	public function getSiguiente() {
		$coloresPorArticulo = Factory::getInstance()->getListObject('ColorPorArticulo', 'cod_articulo = ' . Datos::objectToDB($this->idArticulo) . ' AND vigente = ' . Datos::objectToDB('S'));
		$i = 1;
		foreach ($coloresPorArticulo as $colorPorArticulo) {
			/** @var ColorPorArticulo $colorPorArticulo */
			if ($colorPorArticulo->id == $this->id) {
				if ($i == count($coloresPorArticulo)) {
					$posibleIdSiguiente = $this->idArticulo + 1;
					$condicion = true;
					$j = 1;
					while ($condicion || $j < 2000) {
						$j++;
						try {
							$posibleArticuloSiguiente = Factory::getInstance()->getArticulo($posibleIdSiguiente);
							if (!$posibleArticuloSiguiente->vigente()) {
								throw new FactoryExceptionRegistroNoExistente();
							}
							$coloresPorArticulo = Factory::getInstance()->getListObject('ColorPorArticulo', 'cod_articulo = ' . Datos::objectToDB($posibleArticuloSiguiente->id) . ' AND vigente = ' . Datos::objectToDB('S'));
							$colorReturn = $coloresPorArticulo[0];
							break;
						} catch (FactoryExceptionRegistroNoExistente $ex) {
							$posibleIdSiguiente++;
						}
					}
				} else {
					$colorReturn = $coloresPorArticulo[$i];
					break;
				}
			}
			$i++;
		}

		return array('idArticulo' => $colorReturn->idArticulo, 'idColor' => $colorReturn->id);
	}

	public function getRutaImagen($tipoRuta, $idVersion = null, $extension = ExtensionImagen::png) {
		$rutas = ManejadorDeImagenes::getRutas();

		if (!$idVersion && $tipoRuta != TiposRutas::imagenMiniatura) {
			$idVersion = $this->getUltimoPatronVigente();
		}

		if ($tipoRuta == TiposRutas::imagenEtiquetaLengua) {
			$patronesItem = Factory::getInstance()->getListObject('PatronItem', 'cod_articulo = ' . Datos::objectToDB($this->idArticulo) . ' AND cod_color_articulo = ' . Datos::objectToDB($this->id) . ' AND version = ' .  Datos::objectToDB($idVersion) . ' AND conjunto = 51');
			if (count($patronesItem) > 0) {
				$patronItem = $patronesItem[0];
				$nombreImagen = $patronItem->material->id . $patronItem->idColorMateriaPrima;
			}
		} elseif ($tipoRuta == TiposRutas::imagenEtiquetaCania) {
			$patronesItem = Factory::getInstance()->getListObject('PatronItem', 'cod_articulo = ' . Datos::objectToDB($this->idArticulo) . ' AND cod_color_articulo = ' . Datos::objectToDB($this->id) . ' AND version = ' .  Datos::objectToDB($idVersion) . ' AND conjunto = 50');
			if (count($patronesItem) > 0) {
				$patronItem = $patronesItem[0];
				$nombreImagen = $patronItem->material->id . $patronItem->idColorMateriaPrima;
			}
		} elseif ($tipoRuta == TiposRutas::imagenMiniatura) {
			$nombreImagen = $this->idArticulo . $this->id . '_ch';
			$extension = ExtensionImagen::jpg;
		} else {
			$nombreImagen = $this->idArticulo . '_' . $this->id . '_' . $idVersion;
		}

		return $rutas[$tipoRuta]['ruta_ftp'] . $nombreImagen  . $extension;
	}

	public function getUltimoPatron() {
		if (!isset($this->_ultimoPatron)){
			$patrones = Factory::getInstance()->getListObject('Patron', 'cod_articulo = ' . Datos::objectToDB($this->idArticulo) . ' AND cod_color_articulo = ' . Datos::objectToDB($this->id) . ' ORDER BY version desc');
			$version = 0;

			if ($patrones[0]) {
				$version = $patrones[0]->version;
			}
			$this->_ultimoPatron = $version;
		}
		return $this->_ultimoPatron;
	}

	public function getUltimoPatronVigente() {
		if (!isset($this->_ultimoPatronVigente)){
			$patrones = Factory::getInstance()->getListObject('Patron', 'cod_articulo = ' . Datos::objectToDB($this->idArticulo) . ' AND cod_color_articulo = ' . Datos::objectToDB($this->id) . ' ORDER BY version desc');
			$version = 1;

			foreach ($patrones as $patron) {
				if ($patron->esVersionActual()) {
					$version = $patron->version;
					break;
				}
			}

			$this->_ultimoPatronVigente = $version;
		}
		return $this->_ultimoPatronVigente;
	}

	public function getStockAlmacen($idAlmacen) {
		if (empty($this->stock[$idAlmacen])) {
			return array(
				'1' => 0,
				'2' => 0,
				'3' => 0,
				'4' => 0,
				'5' => 0,
				'6' => 0,
				'7' => 0,
				'8' => 0,
				'9' => 0,
				'10' => 0,
			);
		} else {
			return $this->stock[$idAlmacen];
		}
	}

	public function getStockMenosPendienteAlmacen($idAlmacen) {
		return $this->stockMenosPendiente[$idAlmacen];
	}

	public function cumpleConAlgunaCurva($curvaStr){
		$auxCantidades = explode('-', $curvaStr);
		for ($i = 0; $i < count($auxCantidades); $i++)
			$cantidades[$i + 1] = Funciones::toInt($auxCantidades[$i]);
		$cantidad = count($cantidades);
		foreach ($this->getCurvas() as $curva){
			$cumple = true;
			$firstVal = -1;
			for ($i = 1; $i <= $cantidad; $i++) {
				if ($curva->cantidad[$i] == 0)
					if ($cantidad[$i] == 0)
						$newVal = 0;
					else {
						$cumple = false;
						break;
					}
				else
					$newVal = ($cantidad[$i] / $curva->cantidad[$i]);
				if ($firstVal == -1)
					$firstVal = $newVal;
				if (($firstVal != 0) && ($newVal != $firstVal)){
					$cumple = false;
					break;
				}
			}
			if ($cumple)
				return true;
		}
		return false;
	}

	public function getPrecioSegunCliente($cliente = null) {
		/** @var Cliente $cliente */
		return Funciones::iIsSet((!is_null($cliente) && $cliente->listaAplicable == 'D') ? $this->precioDistribuidor : $this->precioMayoristaDolar, 0);
	}

	// clases/ColorPorArticulo.php
public function getIdNombre($nameField = 'nombre', $idField = 'id') {
  return '['.$this->idArticulo.'-'.$this->$idField.'] '.$this->articulo->nombre.' - '.$this->$nameField;
}

	//GETS y SETS
	protected function getArticulo() {
		if (!isset($this->_articulo)){
			$this->_articulo = Factory::getInstance()->getArticulo($this->idArticulo);
		}
		return $this->_articulo;
	}
	protected function setArticulo($articulo) {
		$this->_articulo = $articulo;
		return $this;
	}
	protected function getCategoriaCalzadoUsuario() {
		if (!isset($this->_categoriaCalzadoUsuario)){
			$this->_categoriaCalzadoUsuario = Factory::getInstance()->getCategoriaCalzadoUsuario($this->idCategoriaCalzadoUsuario);
		}
		return $this->_categoriaCalzadoUsuario;
	}
	protected function setCategoriaCalzadoUsuario($categoriaCalzadoUsuario) {
		$this->_categoriaCalzadoUsuario = $categoriaCalzadoUsuario;
		return $this;
	}
	protected function getColor() {
		if (!isset($this->_color)){
			$this->_color = Factory::getInstance()->getColor($this->idColor);
		}
		return $this->_color;
	}
	protected function setColor($color) {
		$this->_color = $color;
		return $this;
	}
	protected function getCurvas() {
		if (!isset($this->_curvas) && isset($this->id)){
			$this->_curvas = Factory::getInstance()->getListObject('CurvaPorArticulo', 'cod_articulo = ' . Datos::objectToDB($this->idArticulo) . ' AND cod_color_articulo = ' . Datos::objectToDB($this->id));
		}
		return $this->_curvas;
	}
	protected function setCurvas($curvas) {
		$this->_curvas = $curvas;
		return $this;
	}
	protected function getEcommerceCategory() {
		if (!isset($this->_ecommerceCategory)){
			$this->_ecommerceCategory = Factory::getInstance()->getCategoriaCalzadoUsuario($this->idEcommerceCategory);
		}
		return $this->_ecommerceCategory;
	}
	protected function setEcommerceCategory($ecommerceCategory) {
		$this->_ecommerceCategory = $ecommerceCategory;
		return $this;
	}
	protected function getFormaDeComercializacionNombre() {
		if (!isset($this->_formaDeComercializacionNombre)){
			switch ($this->formaDeComercializacion) {
				case 'M':
					$this->_formaDeComercializacionNombre = 'Modular';
					break;
				case 'A':
					$this->_formaDeComercializacionNombre = 'Agotado';
					break;
				case 'T':
					$this->_formaDeComercializacionNombre = 'Limitado';
					break;
				case 'L':
				default:
					$this->_formaDeComercializacionNombre = 'Libre';
			}
		}
		return $this->_formaDeComercializacionNombre;
	}
	protected function setFormaDeComercializacionNombre($formaDeComercializacionNombre) {
		$this->_formaDeComercializacionNombre = $formaDeComercializacionNombre;
		return $this;
	}
	protected function getFormaDeComercializacionNombreCorto() {
		if (!isset($this->_formaDeComercializacionNombreCorto)){
			switch ($this->formaDeComercializacion) {
				case 'M':
					$this->_formaDeComercializacionNombreCorto = 'Mod.';
					break;
				case 'A':
					$this->_formaDeComercializacionNombreCorto = 'Agot.';
					break;
				case 'T':
					$this->_formaDeComercializacionNombreCorto = 'Limit.';
					break;
				case 'L':
				default:
					$this->_formaDeComercializacionNombreCorto = 'Libre';
			}
		}
		return $this->_formaDeComercializacionNombreCorto;
	}
	protected function setFormaDeComercializacionNombreCorto($formaDeComercializacionNombreCorto) {
		$this->_formaDeComercializacionNombreCorto = $formaDeComercializacionNombreCorto;
		return $this;
	}
	protected function getStock() {
		if (!isset($this->_stock)){
			$where = 'cod_articulo = ' . Datos::objectToDB($this->idArticulo) . ' AND ';
			$where .= 'cod_color_articulo = ' . Datos::objectToDB($this->id) . ' ';
			$stocks = Factory::getInstance()->getListObject('Stock', $where);
			if (count($stocks)) {
				$aux = array();
				foreach ($stocks as $stock) {
					/** @var Stock $stock */
					$aux[$stock->idAlmacen] = $stock->cantidad;
				}
				$this->_stock = $aux;
			}
		}
		return $this->_stock;
	}
	protected function setStock($stock) {
		$this->_stock = $stock;
		return $this;
	}
	protected function getStockMenosPendiente() {
		if (!isset($this->_stockMenosPendiente)){
			$where = 'cod_articulo = ' . Datos::objectToDB($this->idArticulo) . ' AND ';
			$where .= 'cod_color_articulo = ' . Datos::objectToDB($this->id) . ' ';
			$stocks = Factory::getInstance()->getArrayFromView('stock_menos_pendiente_vw', $where);
			if (count($stocks)) {
				$aux = array();
				for ($i = 0; $i < count($stocks); $i++) {
					$item = $stocks[$i];
					for ($j = 1; $j <= 10; $j++)
						$aux[$item['cod_almacen']][$j] = $item['S' . $j];
				}
				$this->_stockMenosPendiente = $aux;
			}
		}
		return $this->_stockMenosPendiente;
	}
	protected function setStockMenosPendiente($stockMenosPendiente) {
		$this->_stockMenosPendiente = $stockMenosPendiente;
		return $this;
	}
	protected function getTipoProductoStock() {
		if (!isset($this->_tipoProductoStock)){
			$this->_tipoProductoStock = Factory::getInstance()->getTipoProductoStock($this->idTipoProductoStock);
		}
		return $this->_tipoProductoStock;
	}
	protected function setTipoProductoStock($tipoProductoStock) {
		$this->_tipoProductoStock = $tipoProductoStock;
		return $this;
	}


	public function getreferenciaWebMayorista() {
	    return $this->referenciaWebMayorista;
	}
	public function obtenerPorId($id) {
        $conn = $this->getConnection(); // Método en Base para obtener la conexión desde config/login
        $query = "SELECT * FROM color_por_articulo WHERE id = '$id'";
        $result = mssql_query($query, $conn);
        if (!$result) {
            echo "Error en consulta de color_por_articulo: " . mssql_get_last_message() . "\n";
            return null;
        }
        $row = mssql_fetch_object($result);
        mssql_free_result($result);
        if ($row) {
            $color = new ColorPorArticulo();
            $color->id = $row->id;
            $color->idArticulo = $row->id_articulo;
            $color->referenciaWebMayorista = $row->referencia_web_mayorista ? $row->referencia_web_mayorista : 'SIN_REFERENCIA';
            return $color;
        }
        echo "No se encontró color por id: $id\n";
        return null;
    }
}

?>