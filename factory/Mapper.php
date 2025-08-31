<?php

class Mapper {
	public	function fillObject($dr, $obj) {
		try {
			$method = 'fill' . ucfirst(Funciones::getType($obj));
			if (!method_exists($this, $method)) {
				throw new Exception('No existe el método ' . $method . ' en la clase "Mapper".');
			}
			if (is_subclass_of($obj, 'Base'))
				$obj->modo = Modos::update;
			return $this->$method($dr, $obj);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function fillListObject($ds, $list, $clase){
		try {
			if ($list == null){
				$list = array();
			}
			for ($i = 0; $i < count($ds); $i++) {
				$dr = $ds[$i];
				$obj = new $clase();
				$obj->modo = Modos::insert;
				$obj = $this->fillObject($dr, $obj);
				$list[] = $obj;
			}
			return $list;
		} catch (Exception $ex){
			throw $ex;
		}
	}
	public	function fillArray($ds, $list){
		try {
			if ($list == null){
				$list = array();
			}
			for ($i = 0; $i < count($ds); $i++) {
				$dr = $ds[$i];
				$list[] = $dr;
			}
			return $list;
		} catch (Exception $ex){
			throw $ex;
		}
	}
	public	function getQueryInstancia($obj, $modo) {
		try {
			$method = 'mapperQuery' . ucfirst(Funciones::getType($obj));
			if (!method_exists($this, $method)) {
				throw new Exception('No existe el método ' . $method . ' en la clase "Mapper".');
			}
			return $this->$method($obj, $modo);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getQueryInstanciaWhere($obj, $modo, $clausulaWhere, $limit) {
		try {
			return $this->cambiarWhere($this->getQueryInstancia($obj, $modo), $clausulaWhere, $limit);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getQueryView($viewName, $clausulaWhere, $limit, $fields) {
		try {
			if (is_array($fields)) {
				$fields = implode(', ', $fields);
				rtrim(', ', $fields);
			}
			$query = 'SELECT ' . $fields . ' FROM ' . $viewName . ' WHERE 1 = 1';
			return $this->cambiarWhere($query, $clausulaWhere, $limit);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	public	function getQueryStoredProcedure($storedProcedureName, $parametros) {
		try {
			$query = 'EXEC ' . $storedProcedureName . ' ' . $parametros;
			return $query;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function cambiarWhere($sentencia, $clausulaWhere, $limit) {
		try {
			if ($limit != 0) {
				$iPos2 = strrpos($sentencia, 'SELECT');
				$sentencia = 'SELECT TOP ' . $limit . substr($sentencia, $iPos2 + 6);
			}
			$iPos1 = strrpos($sentencia, 'WHERE');
			if (trim($clausulaWhere) == '')
				return substr($sentencia, 0, $iPos1 + 6) . '1 = 1; ';
			else
				return substr($sentencia, 0, $iPos1 + 6) . $clausulaWhere;
		} catch (Exception $ex) {
			throw $ex;
		}
	}

	//FILLS

	private function fillAcreditarCheque($dr, AcreditarCheque $acreditarCheque) {
		try {
			return $this->fillAcreditarDebitarCheque($dr, $acreditarCheque);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillAcreditarChequeCabecera($dr, AcreditarChequeCabecera $acreditarChequeCabecera) {
		try {
			return $this->fillAcreditarDebitarChequeCabecera($dr, $acreditarChequeCabecera);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillAcreditarDebitarCheque($dr, AcreditarDebitarCheque $acreditarDebitarCheque) {
		try {
			$acreditarDebitarCheque->numero = $dr['cod_acreditar_debitar_cheque'];
			$acreditarDebitarCheque->empresa = $dr['empresa'];
			$acreditarDebitarCheque->idImportePorOperacion = $dr['cod_importe_operacion'];
			$acreditarDebitarCheque->importeTotal = $dr['importe_total'];
			$acreditarDebitarCheque->entradaSalida = $dr['entrada_salida'];
			return $acreditarDebitarCheque;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillAcreditarDebitarChequeCabecera($dr, AcreditarDebitarChequeCabecera $acreditarDebitarChequeCabecera) {
		try {
			$acreditarDebitarChequeCabecera->numero = $dr['cod_acreditar_debitar_cheque'];
			$acreditarDebitarChequeCabecera->empresa = $dr['empresa'];
			$acreditarDebitarChequeCabecera->tipo = $dr['tipo'];
			$acreditarDebitarChequeCabecera->observaciones = $dr['observaciones'];
			$acreditarDebitarChequeCabecera->idUsuario = $dr['cod_usuario'];
			$acreditarDebitarChequeCabecera->fecha = Funciones::formatearFecha($dr['fecha_documento'], 'd/m/Y');
			$acreditarDebitarChequeCabecera->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$acreditarDebitarChequeCabecera->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$acreditarDebitarChequeCabecera->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $acreditarDebitarChequeCabecera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillAjusteStock($dr, AjusteStock $ajusteStock) {
		try {
			$ajusteStock->id = $dr['id'];
			$ajusteStock->tipoMovimiento = $dr['tipo_movimiento'];
			$ajusteStock->idAlmacen = $dr['cod_almacen'];
			$ajusteStock->idArticulo = $dr['cod_articulo'];
			$ajusteStock->idColorPorArticulo = $dr['cod_color_articulo'];
			$ajusteStock->motivo = $dr['motivo'];
			for ($i = 1; $i <= 10; $i++)
				$ajusteStock->cantidad[$i] = $dr['cant_' . $i];
			$ajusteStock->idUsuario = $dr['cod_usuario'];
			$ajusteStock->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			return $ajusteStock;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillAjusteStockMP($dr, AjusteStockMP $ajusteStockMP) {
		try {
			$ajusteStockMP->id = $dr['id'];
			$ajusteStockMP->tipoMovimiento = $dr['tipo_movimiento'];
			$ajusteStockMP->idAlmacen = $dr['cod_almacen'];
			$ajusteStockMP->idMaterial = $dr['cod_material'];
			$ajusteStockMP->idColorMateriaPrima = $dr['cod_color'];
			$ajusteStockMP->motivo = $dr['motivo'];
			for ($i = 1; $i <= 10; $i++)
				$ajusteStockMP->cantidad[$i] = $dr['cant_' . $i];
			$ajusteStockMP->idUsuario = $dr['cod_usuario'];
			$ajusteStockMP->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			return $ajusteStockMP;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillAlmacen($dr, Almacen $almacen) {
		try {
			$almacen->id = $dr['cod_almacen'];
			$almacen->anulado = $dr['anulado'];
			$almacen->fechaAlta = Funciones::formatearFecha($dr['fecha_ultima_modificacion'], 'd/m/Y');
			$almacen->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$almacen->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_modificacion'], 'd/m/Y');
			$almacen->nombre = $dr['denom_almacen'];
			$almacen->nombreCorto = $dr['denom_abrev'];
			return $almacen;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
    private function fillAlmacenPorSeccion($dr, AlmacenPorSeccion $almacenPorSeccion) {
        try {
            $almacenPorSeccion->idSeccionProduccion = $dr['cod_seccion'];
            $almacenPorSeccion = $this->fillAlmacen($dr, $almacenPorSeccion);
            return $almacenPorSeccion;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
	private function fillAporteSocio($dr, AporteSocio $aporteSocio) {
		try {
			$aporteSocio->numero = $dr['nro_aporte_socio'];
			$aporteSocio->empresa = $dr['empresa'];
			$aporteSocio->idImportePorOperacion = $dr['cod_importe_operacion'];
			$aporteSocio->idSocio = $dr['cod_socio'];
			$aporteSocio->concepto = $dr['concepto'];
			$aporteSocio->importeTotal = $dr['importe_total'];
			$aporteSocio->idAsientoContable = $dr['cod_asiento_contable'];
			$aporteSocio->observaciones = $dr['observaciones'];
			$aporteSocio->idUsuario = $dr['cod_usuario'];
			$aporteSocio->idUsuarioBaja = $dr['cod_usuario_baja'];
			$aporteSocio->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$aporteSocio->anulado = $dr['anulado'];
			$aporteSocio->fecha = Funciones::formatearFecha($dr['fecha_documento'], 'd/m/Y');
			$aporteSocio->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$aporteSocio->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$aporteSocio->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $aporteSocio;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillAreaEmpresa($dr, AreaEmpresa $areaEmpresa) {
		try {
			$areaEmpresa->id = $dr['id'];
			$areaEmpresa->nombre = $dr['nombre'];
			$areaEmpresa->habilitadaTicket = $dr['habilitada_ticket'];
			$areaEmpresa->anulado = $dr['anulado'];
			$areaEmpresa->idUsuario = $dr['cod_usuario'];
			$areaEmpresa->idUsuarioBaja = $dr['cod_usuario_baja'];
			$areaEmpresa->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$areaEmpresa->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$areaEmpresa->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$areaEmpresa->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $areaEmpresa;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillArticulo($dr, Articulo $articulo) {
		try {
			$articulo->id = Funciones::toString($dr['cod_articulo']);
			$articulo->nombre = $dr['denom_articulo'];
			$articulo->idCliente = $dr['cod_cliente'];
			$articulo->idProveedor = $dr['cod_prov'];
			$articulo->idRangoTalle = $dr['cod_rango'];
			$articulo->idRubroIva = $dr['cod_rubro_iva'];
			$articulo->idRutaProduccion = $dr['cod_ruta'];
			$articulo->idTemporada = $dr['cod_tempo'];
			$articulo->idHorma = trim($dr['cod_horma']);
			$articulo->idLineaProducto = $dr['cod_linea'];
			$articulo->idFamiliaProducto = $dr['cod_familia_producto'];
			$articulo->idMarca = $dr['cod_marca'];
			$articulo->origen = $dr['origen'];
			$articulo->naturaleza = $dr['naturaleza'];
			/* Se usan los precios de ColorPorArticulo
			$articulo->precioDistribuidor = $dr['precio_distribuidor'];
			$articulo->precioListaDistribuidor = $dr['precio_lista_distribuidor'];
			$articulo->precioLista = $dr['precio_lista'];
			$articulo->precioListaOriginal = $dr['precio_lista'];
			$articulo->precioListaAumento = $dr['precio_lista_aumento'];
			$articulo->precioListaMayorista = $dr['precio_lista_mayor'];
			$articulo->precioRecargado = $dr['precio_recargado'];
*/
			$articulo->fechaDeLanzamiento = Funciones::formatearFecha($dr['fecha_lanzamiento'], 'd/m/Y');
			$articulo->fechaDePrecioActual = Funciones::formatearFecha($dr['fecha_precio'], 'd/m/Y');
			$articulo->vigente = $dr['vigente'];
			$articulo->fechaAlta = Funciones::formatearFecha($dr['fechaAlta'], 'd/m/Y');
			$articulo->fechaBaja = Funciones::formatearFecha($dr['fecha_de_baja'], 'd/m/Y');
			$articulo->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_modificacion'], 'd/m/Y');
			return $articulo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillAsientoContable($dr, AsientoContable $asientoContable) {
		try {
			$asientoContable->id = $dr['cod_asiento'];
			$asientoContable->empresa = $dr['empresa'];
			$asientoContable->nombre = $dr['nombre'];
			$asientoContable->idEjercicioContable = $dr['cod_ejercicio'];
			$asientoContable->fecha = Funciones::formatearFecha($dr['fecha_asiento'], 'd/m/Y');
			$asientoContable->importe = Funciones::formatearDecimales($dr['importe'], 2, '.');
			$asientoContable->anulado = $dr['anulado'];
			$asientoContable->idUsuario = $dr['cod_usuario'];
			$asientoContable->idUsuarioBaja = $dr['cod_usuario_baja'];
			$asientoContable->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$asientoContable->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$asientoContable->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$asientoContable->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $asientoContable;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillAsientoContableModelo($dr, AsientoContableModelo $asientoContableModelo) {
		try {
			$asientoContableModelo->id = $dr['cod_asiento_modelo'];
			$asientoContableModelo->nombre = $dr['nombre'];
			$asientoContableModelo->anulado = $dr['anulado'];
			$asientoContableModelo->idUsuario = $dr['cod_usuario'];
			$asientoContableModelo->idUsuarioBaja = $dr['cod_usuario_baja'];
			$asientoContableModelo->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$asientoContableModelo->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$asientoContableModelo->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$asientoContableModelo->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $asientoContableModelo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillAsientoContableModeloFila($dr, AsientoContableModeloFila $asientoContableModeloFila) {
		try {
			$asientoContableModeloFila->idAsientoContableModelo = $dr['cod_asiento_modelo'];
			$asientoContableModeloFila->numeroFila = $dr['numero_fila'];
			$asientoContableModeloFila->idImputacion = $dr['cod_imputacion'];
			$asientoContableModeloFila->observaciones = $dr['observaciones'];
			$asientoContableModeloFila->anulado = $dr['anulado'];
			$asientoContableModeloFila->idUsuario = $dr['cod_usuario'];
			$asientoContableModeloFila->idUsuarioBaja = $dr['cod_usuario_baja'];
			$asientoContableModeloFila->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$asientoContableModeloFila->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$asientoContableModeloFila->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$asientoContableModeloFila->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $asientoContableModeloFila;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillAutorizacion($dr, Autorizacion $autorizacion) {
		try {
			$autorizacion->idAutorizacionTipo = $dr['cod_tipo_autorizacion'];
			$autorizacion->autorizado = $dr['autorizado'];
			$autorizacion->idEspecifico = $dr['id_especifico'];
			$autorizacion->fecha = Funciones::formatearFecha($dr['fecha_autorizacion'], 'd/m/Y');
			$autorizacion->numero = $dr['numero_autorizacion'];
			$autorizacion->motivo = $dr['motivo'];
			$autorizacion->idUsuario = $dr['cod_usuario_autorizador'];
			return $autorizacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillAutorizacionPersona($dr, AutorizacionPersona $autorizacionPersona) {
		try {
			$autorizacionPersona->idAutorizacionTipo = $dr['cod_tipo_autorizacion'];
			$autorizacionPersona->numero = $dr['numero_autorizacion'];
			$autorizacionPersona->idUsuario = $dr['cod_usuario_autorizador'];
			return $autorizacionPersona;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillAutorizacionTipo($dr, AutorizacionTipo $autorizacionTipo) {
		try {
			$autorizacionTipo->id = $dr['cod_tipo_autorizacion'];
			$autorizacionTipo->cantidad = $dr['cant_autorizaciones_necesarias'];
			$autorizacionTipo->nombre = $dr['nombre_tipo_autorizacion'];
			$autorizacionTipo->nombreObjeto = $dr['nombre_objeto'];
			return $autorizacionTipo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillBanco($dr, Banco $banco) {
		try {
			$banco->idBanco = $dr['cod_banco'];
			$banco->nombre = $dr['nombre'];
			$banco->codigoBanco = $dr['numero_banco'];
			$banco->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$banco->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$banco->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $banco;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillBancoPropio($dr, BancoPropio $bancoPropio) {
		try {
			$bancoPropio->idBanco = $dr['cod_banco'];
			$bancoPropio->idSucursal = $dr['cod_sucursal'];
			$bancoPropio->nombreSucursal = $dr['nombre_sucursal'];
			$bancoPropio->direccion->fill($dr);
			$bancoPropio->telefono = $dr['telefono'];
			$bancoPropio->imputacionContable = $dr['imputacion_contable'];
			$bancoPropio->fechaInicioCuenta = Funciones::formatearFecha($dr['fecha_inicio_cuenta'], 'd/m/Y');
			$bancoPropio->observaciones = $dr['observaciones'];
			$bancoPropio->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$bancoPropio->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$bancoPropio->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $bancoPropio;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCaja($dr, Caja $caja) {
		try {
			$caja->id = $dr['cod_caja'];
			$caja->idResponsable = $dr['cod_duenio'];
			$caja->idCajaPadre = $dr['cod_caja_padre'];
			$caja->nombre = $dr['nombre'];
			$caja->idImputacion = $dr['cod_imputacion'];
			$caja->fechaLimite = Funciones::formatearFecha($dr['fecha_limite'], 'd/m/Y');
			$caja->diasCierre = $dr['dias_cierre'];
			$caja->importeDescubierto = $dr['importe_descubierto'];
			$caja->importeMaximo = $dr['importe_maximo'];
			$caja->importeEfectivo = $dr['importe_efectivo'];
			$caja->importeGastitos = $dr['importe_gastitos'];
			$caja->esCajaBanco = $dr['caja_banco'];
			$caja->anulado = $dr['anulado'];
			$caja->dispParaNegociar = $dr['disp_para_negociar'];
			$caja->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$caja->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$caja->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $caja;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCajaPosiblesTransferenciaInterna($dr, CajaPosiblesTransferenciaInterna $cajasPosiblesTransferenciaInterna) {
		try {
			$cajasPosiblesTransferenciaInterna->idCajaSalida = $dr['cod_caja_salida'];
			$cajasPosiblesTransferenciaInterna->idCajaEntrada = $dr['cod_caja_entrada'];

			return $cajasPosiblesTransferenciaInterna;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCambiosSituacionCliente($dr, CambiosSituacionCliente $cambiosSituacionCliente) {
		try {
			$cambiosSituacionCliente->id = $dr['cod_cambios_situacion_cliente'];
			$cambiosSituacionCliente->idCliente = $dr['cod_cliente'];
			$cambiosSituacionCliente->calificacionNueva = $dr['cod_calificacion_nuevo'];
			$cambiosSituacionCliente->calificacionAnterior = $dr['cod_calificacion_anterior'];
			$cambiosSituacionCliente->idUsuario = $dr['cod_usuario'];
			$cambiosSituacionCliente->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$cambiosSituacionCliente->hora = Funciones::formatearFecha($dr['fecha'], 'H:i');
			return $cambiosSituacionCliente;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCategoriaCalzadoUsuario($dr, CategoriaCalzadoUsuario $categoriaCalzadoUsuario) {
		try {
			$categoriaCalzadoUsuario->id = $dr['cod_categoria'];
			$categoriaCalzadoUsuario->anulado = $dr['anulado'];
			$categoriaCalzadoUsuario->fechaAlta = Funciones::formatearFecha($dr['fechaAlta'], 'd/m/Y');
			$categoriaCalzadoUsuario->fechaBaja = Funciones::formatearFecha($dr['fechaBaja'], 'd/m/Y');
			$categoriaCalzadoUsuario->fechaUltimaMod = Funciones::formatearFecha($dr['fechaUltimaMod'], 'd/m/Y');
			$categoriaCalzadoUsuario->nombre = $dr['denom_categoria'];
			return $categoriaCalzadoUsuario;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCausaNotaDeCredito($dr, CausaNotaDeCredito $causaNotaDeCredito) {
		try {
			$causaNotaDeCredito->id = $dr['clave_tabla'];
			$causaNotaDeCredito->nombre = $dr['causa_nota_credito'];
			return $causaNotaDeCredito;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCierrePeriodoFiscal($dr, CierrePeriodoFiscal $cierrePeriodoFiscal) {
		try {
			$cierrePeriodoFiscal->id = $dr['cod_cierre_periodo'];
			$cierrePeriodoFiscal->idTipoPeriodoFiscal = $dr['cod_tipo_periodo'];
			$cierrePeriodoFiscal->fechaDesde = Funciones::formatearFecha($dr['fecha_desde'], 'd/m/Y');
			$cierrePeriodoFiscal->fechaHasta = Funciones::formatearFecha($dr['fecha_hasta'], 'd/m/Y');
			$cierrePeriodoFiscal->anulado = $dr['anulado'];
			$cierrePeriodoFiscal->idUsuario = $dr['cod_usuario'];
			$cierrePeriodoFiscal->idUsuarioBaja = $dr['cod_usuario_baja'];
			$cierrePeriodoFiscal->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$cierrePeriodoFiscal->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$cierrePeriodoFiscal->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$cierrePeriodoFiscal->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $cierrePeriodoFiscal;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCheque($dr, Cheque $cheque) {
		try {
			$cheque->id = $dr['cod_cheque'];
			$cheque->empresa = $dr['empresa'];
			$cheque->idCliente = $dr['cod_cliente'];
			$cheque->idProveedor = $dr['cod_proveedor'];
			$cheque->idBanco = $dr['cod_banco'];
			$cheque->idCuentaBancaria = $dr['cod_cuenta_bancaria'];
			$cheque->idRechazoCheque = $dr['cod_rechazo_cheque'];
			$cheque->numero = $dr['numero'];
			$cheque->libradorNombre = $dr['librador_nombre'];
			$cheque->libradorCuit = $dr['librador_cuit'];
			$cheque->importe = Funciones::toFloat($dr['importe'], 2);
			$cheque->noALaOrden = $dr['no_a_la_orden'];
			$cheque->cruzado = $dr['cruzado'];
			$cheque->concluido = $dr['concluido'];
			$cheque->esperandoEnBanco = $dr['esperando_en_banco'];
			$cheque->idCajaActual = $dr['cod_caja_actual'];
			$cheque->diasVencimiento = $dr['dias_vencimiento'];
			$cheque->idUsuario = $dr['cod_usuario'];
			$cheque->idUsuarioBaja = $dr['cod_usuario_baja'];
			$cheque->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$cheque->anulado = $dr['anulado'];
			$cheque->fechaCreditoDebito = Funciones::formatearFecha($dr['fecha_credito_debito'], 'd/m/Y');
			$cheque->fechaEmision = Funciones::formatearFecha($dr['fecha_emision'], 'd/m/Y');
			$cheque->fechaVencimiento = Funciones::formatearFecha($dr['fecha_vencimiento'], 'd/m/Y');
			$cheque->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$cheque->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$cheque->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $cheque;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillChequera($dr, Chequera $chequera) {
		try {
			$chequera->id = $dr['cod_chequera'];
			$chequera->idCuentaBancaria = $dr['cod_cuenta_bancaria'];
			$chequera->idUsuario = $dr['cod_usuario'];
			$chequera->idUsuarioBaja = $dr['cod_usuario_baja'];
			$chequera->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$chequera->numeroInicio = $dr['numero_inicio'];
			$chequera->numeroFin = $dr['numero_fin'];
			$chequera->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$chequera->anulado = $dr['anulado'];
			$chequera->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$chequera->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$chequera->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $chequera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillChequeraItem($dr, ChequeraItem $chequeraItem) {
		try {
			$chequeraItem->id = $dr['cod_chequera_d'];
			$chequeraItem->idChequera = $dr['cod_chequera'];
			$chequeraItem->numero = $dr['numero'];
			$chequeraItem->utilizado = $dr['utilizado'];
			return $chequeraItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCliente($dr, Cliente $cliente) {
		try {
			$cliente->id = $dr['cod_cli'];
			$cliente->anulado = $dr['anulado'];
			$cliente->autorizado = $dr['autorizado'];
			$cliente->calificacion = Funciones::toInt($dr['cod_calificacion']);
			$cliente->calificacionOriginal = Funciones::toInt($dr['cod_calificacion']);
			$cliente->cobranzaEmail1 = $dr['email_1'];
			$cliente->cobranzaEmail2 = $dr['email_2'];
			$cliente->observacionesCobranza = $dr['observaciones_cobranza'];
			$cliente->observacionesGestionCobranza = $dr['observaciones_gestion_cobranza'];
			$cliente->observaciones = $dr['observaciones'];
			$cliente->marcasQueComercializa = $dr['marcas_comercializa'];
			$cliente->referenciasBancarias = $dr['ref_bancarias'];
			$cliente->referenciasComerciales = $dr['ref_comerciales'];
			$cliente->cobranzaTelefono1 = $dr['tel_cobranza_1'];
			$cliente->cobranzaTelefono2 = $dr['tel_cobranza_2'];
			$cliente->cobranzaTelefono3 = $dr['tel_cobranza_3'];
			$cliente->idCondicionIva = $dr['cod_cond_iva'];
			$cliente->creditoDescuentoEspecial = $dr['descuento_especial'];
			$cliente->idCreditoFormaDePago = $dr['forma_pago'];
			$cliente->creditoLimite = $dr['limite_credito'];
			$cliente->creditoPlazoMaximo = $dr['plazo_maximo'];
			$cliente->creditoPrimeraEntrega = $dr['primera_entrega'];
			$cliente->cuit = $dr['cuit'];
			$cliente->dni = $dr['dni'];
			$cliente->direccionCalle = $dr['calle'];
			$cliente->direccionCodigoPostal = $dr['cod_postal'];
			$cliente->direccionDepartamento = $dr['oficina_depto'];
			$cliente->idDireccionLocalidad = $dr['cod_localidad_nro'];
			$cliente->direccionNumero = $dr['numero'];
			$cliente->idDireccionPais = $dr['cod_pais'];
			$cliente->direccionPartidoDepartamento = $dr['partido_departamento'];
			$cliente->direccionPiso = $dr['piso'];
			$cliente->idDireccionProvincia = $dr['cod_provincia'];
			$cliente->email = $dr['email'];
			$cliente->fechaUltimaCalificacion = Funciones::formatearFecha($dr['fecha_calificacion'], 'd/m/Y');
			$cliente->idGrupoEmpresa = $dr['cod_grupo_empresa'];
			$cliente->listaAplicable = $dr['lista_aplicable'];
			$cliente->nombre = $dr['denom_fantasia'];
			$cliente->razonSocial = $dr['razon_social'];
			$cliente->idVendedor = $dr['cod_vendedor'];
			$cliente->idRubro = $dr['rubro'];
			$cliente->telefono1 = $dr['telefono_1'];
			$cliente->interno1 = $dr['interno_1'];
			$cliente->habilitadoCae = $dr['habilitado_cae'];
			$cliente->idSucursalCentral = $dr['cod_casa_central'];
			$cliente->idSucursalFiscal = $dr['cod_casa_fiscal'];
			$cliente->idSucursalCobranza = $dr['cod_casa_cobranza'];
			$cliente->idSucursalEntrega = $dr['cod_casa_entrega'];
			return $cliente;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillClienteTodos($dr, ClienteTodos $clienteTodos) {
		try {
			$clienteTodos = $this->fillCliente($dr, $clienteTodos);
			return $clienteTodos;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCobroChequeVentanilla($dr, CobroChequeVentanilla $cobroChequeVentanilla) {
		try {
			$cobroChequeVentanilla->numero = $dr['cod_cobro_cheque_ventanilla'];
			$cobroChequeVentanilla->empresa = $dr['empresa'];
			$cobroChequeVentanilla->idImportePorOperacion = $dr['cod_importe_operacion'];
			$cobroChequeVentanilla->importeTotal = $dr['importe_total'];
			$cobroChequeVentanilla->entradaSalida = $dr['entrada_salida'];
			return $cobroChequeVentanilla;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCobroChequeVentanillaCabecera($dr, CobroChequeVentanillaCabecera $cobroChequeVentanillaCabecera) {
		try {
			$cobroChequeVentanillaCabecera->numero = $dr['cod_cobro_cheque_ventanilla'];
			$cobroChequeVentanillaCabecera->empresa = $dr['empresa'];
			$cobroChequeVentanillaCabecera->idAsientoContable = $dr['cod_asiento_contable'];
			$cobroChequeVentanillaCabecera->observaciones = $dr['observaciones'];
			$cobroChequeVentanillaCabecera->fecha = Funciones::formatearFecha($dr['fecha_documento'], 'd/m/Y');
			$cobroChequeVentanillaCabecera->idResponsable = $dr['cod_responsable'];
			$cobroChequeVentanillaCabecera->idUsuario = $dr['cod_usuario'];
			$cobroChequeVentanillaCabecera->idUsuarioBaja = $dr['cod_usuario_baja'];
			$cobroChequeVentanillaCabecera->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$cobroChequeVentanillaCabecera->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$cobroChequeVentanillaCabecera->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$cobroChequeVentanillaCabecera->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $cobroChequeVentanillaCabecera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCobroChequeVentanillaTemporal($dr, CobroChequeVentanillaTemporal $cobroChequeVentanillaTemporal) {
		try {
			$cobroChequeVentanillaTemporal->id = $dr['cod_cobro_cheque_vent_temp'];
			$cobroChequeVentanillaTemporal->idCaja = $dr['cod_caja'];
			$cobroChequeVentanillaTemporal->idResponsable = $dr['cod_responsable'];
			$cobroChequeVentanillaTemporal->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$cobroChequeVentanillaTemporal->idCheques = $dr['cheques'];
			$cobroChequeVentanillaTemporal->idUsuario = $dr['cod_usuario'];
			$cobroChequeVentanillaTemporal->idUsuarioBaja = $dr['cod_usuario_baja'];
			$cobroChequeVentanillaTemporal->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$cobroChequeVentanillaTemporal->confirmado = $dr['confirmado'];
			$cobroChequeVentanillaTemporal->anulado = $dr['anulado'];
			$cobroChequeVentanillaTemporal->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$cobroChequeVentanillaTemporal->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$cobroChequeVentanillaTemporal->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $cobroChequeVentanillaTemporal;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillColor($dr, Color $color) {
		try {
			$color->id = $dr['cod_color'];
			$color->nombre = $dr['denom_color'];
			$color->anulado = $dr['anulado'];
			$color->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_modificacion'], 'd/m/Y');
			return $color;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillColorMateriaPrima($dr, ColorMateriaPrima $colorMateriaPrima) {
		try {
			$colorMateriaPrima->idMaterial = $dr['cod_material'];
			$colorMateriaPrima->idColor = $dr['cod_color'];
			$colorMateriaPrima->anulado = $dr['anulado'];
			$colorMateriaPrima->precioUnitario = Funciones::toFloat($dr['precio_unitario'], 4);
			$colorMateriaPrima->idUsuario = $dr['autor_ultima_modificacion'];
			$colorMateriaPrima->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_modificacion'], 'd/m/Y');
			$colorMateriaPrima->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$colorMateriaPrima->precioVentaUnitario = Funciones::toFloat($dr['precio_venta_unitario'], 4);
			$colorMateriaPrima->nombreColor = $dr['denom_color'];
			return $colorMateriaPrima;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillColorPorArticulo($dr, ColorPorArticulo $colorPorArticulo) {
		try {
			$colorPorArticulo->idArticulo = $dr['cod_articulo'];
			$colorPorArticulo->id = $dr['cod_color_articulo'];
			$colorPorArticulo->idColor = $dr['cod_color'];
			$colorPorArticulo->idCategoriaCalzadoUsuario = $dr['categoria_usuario'];
			$colorPorArticulo->nombre = $dr['denom_color'];
			$colorPorArticulo->vigente = $dr['vigente'];
			$colorPorArticulo->fechaAlta = Funciones::formatearFecha($dr['fechaAlta'], 'd/m/Y');
			$colorPorArticulo->fechaBaja = Funciones::formatearFecha($dr['fecha_de_baja'], 'd/m/Y');
			$colorPorArticulo->fechaUltimaMod = Funciones::formatearFecha($dr['fechaUltimaMod'], 'd/m/Y');
			$colorPorArticulo->formaDeComercializacion = $dr['comercializacion_libre'];
			$colorPorArticulo->precioMinoristaDolar = $dr['precio_minorista_usd'];
			$colorPorArticulo->precioMayoristaDolar = $dr['precio_mayorista_usd'];
			$colorPorArticulo->precioDistribuidor = $dr['precio_distrib'];
			$colorPorArticulo->precioDistribuidorMinorista = $dr['precio_distrib_minorista'];
			$colorPorArticulo->fotos[0] = $dr['fotografia'];
			for ($i = 1; $i < 9; $i++)
				$colorPorArticulo->fotos[$i] = $dr['fotografia' . $i];
			$colorPorArticulo->fotos[9] = $dr['zoom_lado_interno'];
			$colorPorArticulo->fotos[10]= $dr['zoom_puntera'];
			$colorPorArticulo->fotos[11] = $dr['zoom_caña'];
			$colorPorArticulo->fotos[12] = $dr['zoom_talon'];
			$colorPorArticulo->clasificacionComercial = $dr['clasificacion_comercial'];
			$colorPorArticulo->textoVarios = $dr['texto_varios'];
			$colorPorArticulo->textoPuntera = $dr['texto_puntera'];
			$colorPorArticulo->textoTalon = $dr['texto_talon'];
			//$colorPorArticulo->textoLengua = $dr['texto_lengua'];
			$colorPorArticulo->textoLadoInterno = $dr['texto_lado_interno'];
			$colorPorArticulo->textoCania = $dr['texto_caña'];
			$colorPorArticulo->precioRecargado = $dr['precio_recargado'];
			$colorPorArticulo->idTipoProductoStock = Funciones::toInt($dr['id_tipo_producto_stock']);
			$colorPorArticulo->ecommerceExiste = $dr['ecommerce_existe'];
			$colorPorArticulo->ecommerceFechaUltimaSinc = Funciones::formatearFecha($dr['ecommerce_fecha_ultima_sinc'], 'd/m/Y');
			$colorPorArticulo->ecommerceNombre = $dr['ecommerce_nombre'];
			$colorPorArticulo->ecommerceInfo = $dr['ecommerce_info'];
			$colorPorArticulo->ecommerceForSale = $dr['ecommerce_forsale'];
			$colorPorArticulo->ecommerceCondition = $dr['ecommerce_condition'];
			$colorPorArticulo->idEcommerceCategory = $dr['ecommerce_cod_category'];
			$colorPorArticulo->ecommerceExclusive = $dr['ecommerce_exclusive'];
			$colorPorArticulo->ecommerceFeatured = $dr['ecommerce_featured'];
			$colorPorArticulo->ecommercePrice1 = $dr['ecommerce_price1'];
			$colorPorArticulo->ecommercePrice2 = $dr['ecommerce_price2'];
			$colorPorArticulo->ecommercePrice3 = $dr['ecommerce_price3'];
			$colorPorArticulo->ecommerceImage1 = $dr['ecommerce_image1'];
			$colorPorArticulo->referenciaWebMayorista = $dr['referencia_web_mayorista'];
			return $colorPorArticulo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillConcepto($dr, Concepto $concepto) {
		try {
			$concepto->id = $dr['cod_concepto'];
			$concepto->nombre = $dr['nombre'];
			$concepto->descripcion = $dr['descripcion'];
			$concepto->idUsuario = $dr['cod_usuario'];
			$concepto->anulado = $dr['anulado'];
			$concepto->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$concepto->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$concepto->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $concepto;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillConceptoRetencionGanancias($dr, ConceptoRetencionGanancias $conceptoRetencionGanancias) {
		try {
			$conceptoRetencionGanancias->id = $dr['cod_concepto_reten_ganan'];
			$conceptoRetencionGanancias->concepto = $dr['concepto'];
			$conceptoRetencionGanancias->idUsuario = $dr['cod_usuario'];
			$conceptoRetencionGanancias->idUsuarioBaja = $dr['cod_usuario_baja'];
			$conceptoRetencionGanancias->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$conceptoRetencionGanancias->anulado = $dr['anulado'];
			$conceptoRetencionGanancias->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$conceptoRetencionGanancias->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$conceptoRetencionGanancias->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $conceptoRetencionGanancias;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCondicionIva($dr, CondicionIva $condicionIva) {
		try {
			$condicionIva->id = $dr['denom_cond_iva'];
			$condicionIva->anulado = $dr['anulado'];
			$condicionIva->nombre = $dr['denom_completa'];
			$condicionIva->letraFactura = $dr['letra_factura'];
			$condicionIva->letraFacturaProveedor = $dr['letra_factura_proveedor'];
			$condicionIva->tratamiento = $dr['tratamiento'];
			for ($i = 1; $i <= 5; $i++)
				$condicionIva->porcentajes[$i] = $dr['valor_impuesto_' . $i];
			return $condicionIva;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillConfirmacionStock($dr, ConfirmacionStock $confirmacionStock) {
		try {
			$confirmacionStock->id = $dr['id'];
			$confirmacionStock->idOrdenDeFabricacion = $dr['cod_orden_fabricacion'];
			$confirmacionStock->numeroTarea = $dr['numero_tarea'];
			$confirmacionStock->idSeccionProduccion = $dr['cod_seccion_produccion'];
			$confirmacionStock->cantidadTotal = $dr['cantidad'];
			for ($i = 1; $i <= 10; $i++)
				$confirmacionStock->cantidad[$i] = $dr['cant_' . $i];
			$confirmacionStock->anulado = $dr['anulado'];
			$confirmacionStock->idUsuario = $dr['cod_usuario'];
			$confirmacionStock->idUsuarioBaja = $dr['cod_usuario_baja'];
			$confirmacionStock->fechaAlta = $dr['fecha_alta'];
			$confirmacionStock->fechaBaja = $dr['fecha_baja'];
			return $confirmacionStock;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillContacto($dr, Contacto $contacto) {
		try {
			$contacto->id = $dr['cod_contacto'];
			$contacto->tipo = $dr['tipo'];
			if ($contacto->tipo == TiposContacto::cliente) {
				$contacto->idCliente = $dr['cod_cliente'];
				$contacto->idSucursal = $dr['cod_sucursal'];
			}
			if ($contacto->tipo == TiposContacto::proveedor)
				$contacto->idProveedor = $dr['cod_proveedor'];
			$contacto->anulado = $dr['anulado'];
			$contacto->idAreaEmpresa = $dr['cod_area_empresa'];
			$contacto->apellido = $dr['apellido'];
			$contacto->celular = $dr['celular'];
			$contacto->email1 = $dr['email1'];
			$contacto->email2 = $dr['email2'];
			$contacto->interno1 = $dr['interno1'];
			$contacto->interno2 = $dr['interno2'];
			$contacto->telefono1 = $dr['telefono1'];
			$contacto->telefono2 = $dr['telefono2'];
			$contacto->direccionCalle = $dr['calle'];
			$contacto->direccionNumero = $dr['numero'];
			$contacto->direccionPiso = $dr['piso'];
			$contacto->direccionDepartamento = $dr['departamento'];
			$contacto->direccionCodigoPostal = $dr['codigo_postal'];
			$contacto->idDireccionLocalidad = $dr['cod_localidad_nro'];
			$contacto->idDireccionProvincia = $dr['cod_provincia'];
			$contacto->idDireccionPais = $dr['cod_pais'];
			$contacto->nombre = $dr['nombre'];
			$contacto->referencia = $dr['referencia'];
			$contacto->observaciones = $dr['observaciones'];
			return $contacto;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillConjunto($dr, Conjunto $conjunto) {
		try {
			$conjunto->id = trim($dr['conjunto']);
			$conjunto->nombre = trim($dr['denom_conjunto']);
			$conjunto->conformacion = trim($dr['conformacion']);
			return $conjunto;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCurva($dr, Curva $curva) {
		try {
			$curva->id = $dr['cod_curva'];
			$curva->anulado = $dr['anulado'];
			for ($i = 1; $i < 16; $i++)
				$curva->cantidad[$i] = $dr['pos_' . $i];
			$curva->nombre = $dr['denom_curva'];
			$curva->tipoDeCurva = $dr['tipo_curva'];
			return $curva;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCurvaPorArticulo($dr, CurvaPorArticulo $curvaPorArticulo) {
		try {
			$curvaPorArticulo->idArticulo = $dr['cod_articulo'];
			$curvaPorArticulo->idColorPorArticulo = $dr['cod_color_articulo'];
			$curvaPorArticulo->idCurva = $dr['cod_curva'];
			return $curvaPorArticulo;
		} catch (Exception $ex) {
		throw $ex;
		}
	}
	private function fillCurvaProduccionPorArticulo($dr, CurvaProduccionPorArticulo $curvaProduccionPorArticulo) {
		try {
			$curvaProduccionPorArticulo->id = $dr['modulo_variante_nro'];
			$curvaProduccionPorArticulo->tipoDeCurva = $dr['tipo_modulo'];
			$curvaProduccionPorArticulo->idArticulo = $dr['cod_articulo'];
			$curvaProduccionPorArticulo->orden = $dr['cod_modulo'];
			$curvaProduccionPorArticulo->nombre = $dr['denom_modulo'];
			$curvaProduccionPorArticulo->activo = $dr['activo'];
			$curvaProduccionPorArticulo->cantidadTotal = $dr['total_modulo_pares'];
            for ($i = 1; $i < 10; $i++)
                $curvaProduccionPorArticulo->cantidad[$i] = $dr['pos_' . $i . '_cant'];
			return $curvaProduccionPorArticulo;
		} catch (Exception $ex) {
		throw $ex;
		}
	}
	private function fillCuentaBancaria($dr, CuentaBancaria $cuentaBancaria) {
		try {
			$cuentaBancaria->id = $dr['cod_cuenta_bancaria'];
			$cuentaBancaria->idBanco = $dr['cod_banco'];
			$cuentaBancaria->idSucursal = $dr['cod_sucursal_banco'];
			$cuentaBancaria->idCaja = $dr['cod_caja'];
			$cuentaBancaria->idProveedor = $dr['cod_proveedor'];
			$cuentaBancaria->idImputacion = $dr['cod_imputacion'];
			$cuentaBancaria->numeroCuenta = $dr['numero_cuenta'];
			$cuentaBancaria->nombreCuenta = $dr['nombre_cuenta'];
			$cuentaBancaria->anulado = $dr['anulado'];
			$cuentaBancaria->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$cuentaBancaria->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$cuentaBancaria->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $cuentaBancaria;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCuentaCorrienteHistorica(/** @noinspection PhpUnusedParameterInspection */$dr, CuentaCorrienteHistorica $cuentaCorrienteHistorica) {
		try {
			return $cuentaCorrienteHistorica;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCuentaCorrienteHistoricaProveedor(/** @noinspection PhpUnusedParameterInspection */$dr, CuentaCorrienteHistoricaProveedor $cuentaCorrienteHistoricaProveedor) {
		try {
			return $cuentaCorrienteHistoricaProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCuentaCorrienteHistoricaDocumento($dr, CuentaCorrienteHistoricaDocumento $cuentaCorrienteHistoricaDocumento) {
		try {
			$cuentaCorrienteHistoricaDocumento->empresa = $dr['empresa'];
			$cuentaCorrienteHistoricaDocumento->idCliente = $dr['cod_cliente'];
			$cuentaCorrienteHistoricaDocumento->tipoDocumento = $dr['tipo_docum'];
			$cuentaCorrienteHistoricaDocumento->numeroDocumento = $dr['numero'];
			$cuentaCorrienteHistoricaDocumento->numeroComprobante = $dr['nro_comprobante'];
			$cuentaCorrienteHistoricaDocumento->letra = $dr['letra'];
			$cuentaCorrienteHistoricaDocumento->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$cuentaCorrienteHistoricaDocumento->fechaVencimiento = Funciones::formatearFecha($dr['cae_vencimiento'], 'd/m/Y');
			$cuentaCorrienteHistoricaDocumento->detalle = $dr['observaciones'];
			$cuentaCorrienteHistoricaDocumento->importeTotal = $dr['importe_total'];
			$cuentaCorrienteHistoricaDocumento->diasPromedioPago = $dr['dias_promedio_pago'];
			return $cuentaCorrienteHistoricaDocumento;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillCuentaCorrienteHistoricaDocumentoProveedor($dr, CuentaCorrienteHistoricaDocumentoProveedor $cuentaCorrienteHistoricaDocumentoProveedor) {
		try {
			$cuentaCorrienteHistoricaDocumentoProveedor->idDocumento = $dr['cod_documento_proveedor'];
			$cuentaCorrienteHistoricaDocumentoProveedor->empresa = $dr['empresa'];
			$cuentaCorrienteHistoricaDocumentoProveedor->idProveedor = $dr['cod_proveedor'];
			$cuentaCorrienteHistoricaDocumentoProveedor->tipoDocumento = $dr['tipo_docum'];
			$cuentaCorrienteHistoricaDocumentoProveedor->numeroDocumento = $dr['nro_documento'];
			$cuentaCorrienteHistoricaDocumentoProveedor->numeroComprobante = $dr['nro_comprobante'];
			$cuentaCorrienteHistoricaDocumentoProveedor->letra = $dr['letra'];
			$cuentaCorrienteHistoricaDocumentoProveedor->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$cuentaCorrienteHistoricaDocumentoProveedor->detalle = $dr['observaciones'];
			$cuentaCorrienteHistoricaDocumentoProveedor->importeTotal = $dr['importe_total'];
			return $cuentaCorrienteHistoricaDocumentoProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDebitarCheque($dr, DebitarCheque $debitarCheque) {
		try {
			return $this->fillAcreditarDebitarCheque($dr, $debitarCheque);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDebitarChequeCabecera($dr, DebitarChequeCabecera $debitarChequeCabecera) {
		try {
			return $this->fillAcreditarDebitarChequeCabecera($dr, $debitarChequeCabecera);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDepositoBancario($dr, DepositoBancario $depositoBancario) {
		try {
			$depositoBancario->numero = $dr['cod_deposito_bancario'];
			$depositoBancario->empresa = $dr['empresa'];
			$depositoBancario->idImportePorOperacion = $dr['cod_importe_operacion'];
			$depositoBancario->importeTotal = $dr['importe_total'];
			$depositoBancario->entradaSalida = $dr['entrada_salida'];
			return $depositoBancario;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDepositoBancarioCabecera($dr, DepositoBancarioCabecera $depositoBancarioCabecera) {
		try {
			$depositoBancarioCabecera->numero = $dr['cod_deposito_bancario'];
			$depositoBancarioCabecera->empresa = $dr['empresa'];
			$depositoBancarioCabecera->numeroTransaccion = $dr['numero_transaccion'];
			$depositoBancarioCabecera->ventaCheque = $dr['venta_cheques'];
			$depositoBancarioCabecera->idAsientoContable = $dr['cod_asiento_contable'];
			$depositoBancarioCabecera->observaciones = $dr['observaciones'];
			$depositoBancarioCabecera->idUsuario = $dr['cod_usuario'];
			$depositoBancarioCabecera->idUsuarioBaja = $dr['cod_usuario_baja'];
			$depositoBancarioCabecera->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$depositoBancarioCabecera->fecha = Funciones::formatearFecha($dr['fecha_documento'], 'd/m/Y');
			$depositoBancarioCabecera->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$depositoBancarioCabecera->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$depositoBancarioCabecera->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $depositoBancarioCabecera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDepositoBancarioTemporal($dr, DepositoBancarioTemporal $depositoBancarioTemporal) {
		try {
			$depositoBancarioTemporal->id = $dr['cod_deposito_bancario_temporal'];
			$depositoBancarioTemporal->idCaja = $dr['cod_caja'];
			$depositoBancarioTemporal->idCuentaBancaria = $dr['cod_cuenta_bancaria'];
			$depositoBancarioTemporal->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$depositoBancarioTemporal->ventaCheque = $dr['venta_cheques'];
			$depositoBancarioTemporal->numeroBoleta = $dr['numero_boleta'];
			$depositoBancarioTemporal->efectivo = $dr['importe_efectivo'];
			$depositoBancarioTemporal->idCheques = $dr['cheques'];
			$depositoBancarioTemporal->idUsuario = $dr['cod_usuario'];
			$depositoBancarioTemporal->idUsuarioBaja = $dr['cod_usuario_baja'];
			$depositoBancarioTemporal->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$depositoBancarioTemporal->confirmado = $dr['confirmado'];
			$depositoBancarioTemporal->anulado = $dr['anulado'];
			$depositoBancarioTemporal->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$depositoBancarioTemporal->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$depositoBancarioTemporal->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $depositoBancarioTemporal;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDespacho($dr, Despacho $despacho) {
		try {
			$despacho->numero = $dr['nro_despacho'];
			$despacho->empresa = $dr['empresa'];
			$despacho->anulado = $dr['anulado'];
			$despacho->idCliente = $dr['cod_cliente'];
			$despacho->idSucursal = $dr['cod_sucursal'];
			$despacho->idUsuario = $dr['cod_usuario'];
			$despacho->idUsuarioBaja = $dr['cod_usuario_baja'];
			$despacho->cantidad = $dr['cantidad'];		//Es la cantidad, no un S/N
			$despacho->pendiente = $dr['pendiente'];	//Idem
			$despacho->idEcommerceOrder = $dr['cod_ecommerce_order'];
			$despacho->observaciones = $dr['observaciones'];
			$despacho->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$despacho->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$despacho->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $despacho;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDespachoItem($dr, DespachoItem $despachoItem) {
		try {
			$despachoItem->despachoNumero = $dr['nro_despacho'];
			$despachoItem->numeroDeItem = $dr['nro_item'];
			$despachoItem->empresa = $dr['empresa'];
			$despachoItem->anulado = $dr['anulado'];
			$despachoItem->pedidoNumero = $dr['nro_pedido'];
			$despachoItem->pedidoNumeroDeItem = $dr['nro_item_pedido'];
			$despachoItem->idCliente = $dr['cod_cliente'];
			$despachoItem->idSucursal = $dr['cod_sucursal'];
			$despachoItem->idAlmacen = $dr['cod_almacen'];
			$despachoItem->idArticulo = $dr['cod_articulo'];
			$despachoItem->idColorPorArticulo = $dr['cod_color_articulo'];
			$despachoItem->remitoNumero = $dr['nro_remito'];
			$despachoItem->remitoLetra = $dr['letra_remito'];
			$despachoItem->facturaPuntoDeVenta = $dr['punto_venta_factura'];
			$despachoItem->facturaTipoDocumento = $dr['tipo_docum_factura'];
			$despachoItem->facturaLetra = $dr['letra_factura'];
			$despachoItem->facturaNumero = $dr['nro_factura'];
			$despachoItem->precioAlFacturar = $dr['precio_al_facturar'];
			$despachoItem->descuentoPedido = $dr['descuento_pedido'];
			$despachoItem->recargoPedido = $dr['recargo_pedido'];
			$despachoItem->ivaPorcentaje = $dr['iva_porc'];
			$despachoItem->precioUnitario = $dr['precio_unitario'];
			$despachoItem->precioUnitarioFinal = $dr['precio_unitario_final'];
			$despachoItem->precioUnitarioFacturar = $dr['precio_unitario_facturar'];
			$despachoItem->precioUnitarioFacturarFinal = $dr['precio_unitario_facturar_final'];
			for ($i = 1; $i <= 10; $i++)
				$despachoItem->cantidad[$i] = $dr['cant_' . $i];
			$despachoItem->idUsuarioBaja = $dr['cod_usuario_baja'];
			$despachoItem->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$despachoItem->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$despachoItem->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $despachoItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDevolucionACliente($dr, DevolucionACliente $devolucionACliente) {
		try {
			$devolucionACliente->id = $dr['cod_devolucion'];
			$devolucionACliente->idCliente = $dr['cod_cliente'];
			$devolucionACliente->idSucursal = $dr['cod_sucursal'];
			$devolucionACliente->anulado = $dr['anulado'];
			$devolucionACliente->observaciones = $dr['observaciones'];
			$devolucionACliente->idUsuario = $dr['cod_usuario'];
			$devolucionACliente->idUsuarioBaja = $dr['cod_usuario_baja'];
			$devolucionACliente->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$devolucionACliente->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$devolucionACliente->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$devolucionACliente->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $devolucionACliente;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDevolucionAClienteItem($dr, DevolucionAClienteItem $devolucionAClienteItem) {
		try {
			$devolucionAClienteItem->id = $dr['id'];
			$devolucionAClienteItem->idDevolucionACliente = $dr['cod_devolucion'];
			$devolucionAClienteItem->idAlmacen = $dr['cod_almacen'];
			$devolucionAClienteItem->idArticulo = $dr['cod_articulo'];
			$devolucionAClienteItem->idColorPorArticulo = $dr['cod_color_articulo'];
			for ($i = 1; $i <= 10; $i++)
				$devolucionAClienteItem->cantidad[$i] = $dr['cant_' . $i];
			$devolucionAClienteItem->idUsuario = $dr['cod_usuario'];
			$devolucionAClienteItem->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			return $devolucionAClienteItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumento($dr, Documento $documento, $primeraVez = true) {
		try {
			if ($primeraVez) {
				$tipoDocum = (Funciones::iIsSet($documento->tipoDocumento, $dr['tipo_docum']));
				switch ($tipoDocum) {
					case 'FAC':
					case 'NDB':
						$documento = $this->fillDocumentoDebe($dr, Funciones::cast($documento, 'DocumentoDebe')); break;
					case 'REC':
					case 'NCR':
						$documento = $this->fillDocumentoHaber($dr, Funciones::cast($documento, 'DocumentoHaber')); break;
					default:
						$documento = $this->fillDocumento($dr, $documento, false); break;
				}
			} else {
				$documento->empresa = $dr['empresa'];
				$documento->puntoDeVenta = $dr['punto_venta'];
				$documento->tipoDocumento = $dr['tipo_docum'];
				$documento->tipoDocumento2 = $dr['tipo_docum_2'];
				$documento->numero = $dr['numero'];
				$documento->idNumeroComprobante = $dr['nro_comprobante'];
				$documento->letra = $dr['letra'];
				$documento->anulado = $dr['anulado'];
				$documento->idCliente = $dr['cod_cliente'];
				$documento->idSucursal = $dr['cod_sucursal'];
				$documento->idUsuario = Funciones::keyIsSet($dr, 'cod_usuario');
				$documento->idUsuarioBaja = Funciones::keyIsSet($dr, 'cod_usuario_baja');
				$documento->idUsuarioUltimaMod = Funciones::keyIsSet($dr, 'cod_usuario_ultima_mod');
				$documento->tieneDetalle = $dr['tiene_detalle'];
				$documento->importeNeto = $dr['importe_neto'];
				$documento->importeNoGravado = $dr['importe_no_gravado'];
				$documento->importeTotal = $dr['importe_total'];
				$documento->importePendiente = $dr['importe_pendiente'];
				$documento->descuentoComercialImporte = $dr['descuento_comercial_importe'];
				$documento->descuentoComercialPorc = $dr['descuento_comercial_porc'];
				$documento->descuentoDespachoImporte = $dr['descuento_despacho_importe'];
				$documento->ivaPorcentaje1 = $dr['iva_porc_1'];
				$documento->ivaImporte1 = $dr['iva_importe_1'];
				$documento->ivaPorcentaje2 = $dr['iva_porc_2'];
				$documento->ivaImporte2 = $dr['iva_importe_2'];
				$documento->ivaPorcentaje3 = $dr['iva_porc_3'];
				$documento->ivaImporte3 = $dr['iva_importe_3'];
				$documento->idFormaDePago = $dr['cod_forma_pago'];
				$documento->diasPromedioPago = $dr['dias_promedio_pago'];
				$documento->cae = $dr['cae'];
				$documento->caeFechaVencimiento = Funciones::formatearFecha($dr['cae_vencimiento'], 'd/m/Y');
				$documento->caeObtencionFecha = Funciones::formatearFecha($dr['cae_obtencion_fecha'], 'd/m/Y');
				$documento->caeObtencionObservaciones = $dr['cae_obtencion_observaciones'];
				$documento->idCaeObtencionUsuario = $dr['cae_obtencion_usuario'];
				$documento->mailEnviado = $dr['mail_enviado'];
				$documento->idAsientoContable = $dr['cod_asiento_contable'];
				$documento->observaciones = $dr['observaciones'];
				$documento->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
				$documento->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
				$documento->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
				$documento->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			}
			return $documento;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumentoAplicacion($dr, DocumentoAplicacion $documentoAplicacion) {
		try {
			$documentoAplicacion->empresa = $dr['empresa'];
			$documentoAplicacion->puntoDeVenta = $dr['punto_venta'];
			$documentoAplicacion->tipoDocumento = $dr['tipo_docum'];
			$documentoAplicacion->nroDocumento = $dr['nro_documento'];
			$documentoAplicacion->nroComprobante = $dr['nro_comprobante'];
			$documentoAplicacion->letra = $dr['letra'];
			$documentoAplicacion->idCliente = $dr['cod_cliente'];
			$documentoAplicacion->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$documentoAplicacion->importeTotal = Funciones::toFloat($dr['importe_total'], 2);
			$documentoAplicacion->importePendiente = Funciones::toFloat($dr['importe_pendiente'], 2);
			return $documentoAplicacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumentoAplicacionDebe($dr, DocumentoAplicacionDebe $documentoAplicacionDebe) {
		try {
			return $this->fillDocumentoAplicacion($dr, $documentoAplicacionDebe);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumentoAplicacionHaber($dr, DocumentoAplicacionHaber $documentoAplicacionHaber) {
		try {
			return $this->fillDocumentoAplicacion($dr, $documentoAplicacionHaber);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumentoDebe($dr, DocumentoDebe $documentoDebe, $primeraVez = true) {
		try {
			if ($primeraVez) {
				$tipoDocum = (Funciones::iIsSet($documentoDebe->tipoDocumento, $dr['tipo_docum']));
				switch ($tipoDocum) {
					case 'FAC':
						$documentoDebe = $this->fillFactura($dr, Funciones::cast($documentoDebe, 'Factura')); break;
					case 'NDB':
						$documentoDebe = $this->fillNotaDeDebito($dr, Funciones::cast($documentoDebe, 'NotaDeDebito')); break;
					default:
						$documentoDebe = $this->fillDocumentoDebe($dr, $documentoDebe, false); break;
				}
			} else {
				$documentoDebe = $this->fillDocumento($dr, $documentoDebe, false);
			}
			return $documentoDebe;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumentoGastoDatos($dr, DocumentoGastoDatos $documentoGastoDatos) {
		try {
			$documentoGastoDatos->id = $dr['cod_documento_gasto_datos'];
			$documentoGastoDatos->razonSocial = $dr['razon_social'];
			$documentoGastoDatos->idCondicionIva = $dr['condicion_iva'];
			$documentoGastoDatos->cuit = $dr['cuit'];
			$documentoGastoDatos->idImputacion = $dr['cod_imputacion'];
			$documentoGastoDatos->direccion->calle = $dr['calle'];
			$documentoGastoDatos->direccion->numero = $dr['numero'];
			$documentoGastoDatos->direccion->piso = $dr['piso'];
			$documentoGastoDatos->direccion->departamento = $dr['oficina_depto'];
			$documentoGastoDatos->direccion->codigoPostal = $dr['cod_postal'];
			$documentoGastoDatos->direccion->idPais = $dr['cod_pais'];
			$documentoGastoDatos->direccion->idLocalidad = $dr['cod_localidad'];
			$documentoGastoDatos->direccion->idProvincia = $dr['cod_provincia'];
			$documentoGastoDatos->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			return $documentoGastoDatos;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumentoHaber($dr, DocumentoHaber $documentoHaber, $primeraVez = true) {
		try {
			if ($primeraVez) {
				$tipoDocum = (Funciones::iIsSet($documentoHaber->tipoDocumento, $dr['tipo_docum']));
				switch ($tipoDocum) {
					case 'REC':
						$documentoHaber = $this->fillRecibo($dr, Funciones::cast($documentoHaber, 'Recibo')); break;
					case 'NCR':
						$documentoHaber = $this->fillNotaDeCredito($dr, Funciones::cast($documentoHaber, 'NotaDeCredito')); break;
					default:
						$documentoHaber = $this->fillDocumentoHaber($dr, $documentoHaber, false); break;
				}
			} else {
				$documentoHaber = $this->fillDocumento($dr, $documentoHaber, false);
			}
			return $documentoHaber;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumentoHija($dr, DocumentoHija $documentoHija) {
		try {
			$documentoHija->id = $dr['id'];
			$documentoHija->empresa = $dr['empresa'];
			$documentoHija->anulado = $dr['anulada'];
			$documentoHija->importe = $dr['importe'];
			$documentoHija->cancelPuntoDeVenta = $dr['cancel_punto_venta'];
			$documentoHija->cancelTipoDocumento = $dr['cancel_tipo_docum'];
			$documentoHija->cancelNumero = $dr['cancel_nro_documento'];
			$documentoHija->cancelLetra = $dr['cancel_letra'];
			$documentoHija->madrePuntoDeVenta = $dr['madre_punto_venta'];
			$documentoHija->madreTipoDocumento = $dr['madre_tipo_docum'];
			$documentoHija->madreNumero = $dr['madre_nro_documento'];
			$documentoHija->madreLetra = $dr['madre_letra'];
			$documentoHija->idUsuario = $dr['cod_usuario'];
			$documentoHija->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$documentoHija->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$documentoHija->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $documentoHija;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumentoItem($dr, DocumentoItem $documentoItem) {
		try {
			$documentoItem->empresa = $dr['empresa'];
			$documentoItem->puntoDeVenta = $dr['punto_venta'];
			$documentoItem->documentoTipoDocumento = $dr['tipo_docum'];
			$documentoItem->documentoNumero = $dr['nro_documento'];
			$documentoItem->documentoLetra = $dr['letra'];
			$documentoItem->numeroDeItem = $dr['nro_item'];
			$documentoItem->anulado = $dr['anulado'];
			$documentoItem->idCliente = $dr['cod_cliente'];
			$documentoItem->idAlmacen = $dr['cod_almacen'];
			$documentoItem->idArticulo = $dr['cod_articulo'];
			$documentoItem->idColorPorArticulo = $dr['cod_color_articulo'];
			for ($i = 1; $i <= 10; $i++)
				$documentoItem->cantidad[$i] = $dr['cant_' . $i];
			$documentoItem->precioUnitario = $dr['precio_unitario'];
			$documentoItem->descuentoPedido = $dr['descuento'];
			$documentoItem->recargoPedido = $dr['recargo'];
			$documentoItem->ivaPorcentaje = $dr['iva_porc'];
			$documentoItem->precioUnitario = $dr['precio_unitario'];
			$documentoItem->precioUnitarioFinal = $dr['precio_unitario_final'];
			$documentoItem->descripcionItem = $dr['descripcion_item'];
			$documentoItem->idImputacion = $dr['cod_imputacion'];
			return $documentoItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumentoProveedor($dr, DocumentoProveedor $documentoProveedor) {
		try {
			$documentoProveedor->id = $dr['cod_documento_proveedor'];
			$documentoProveedor->empresa = $dr['empresa'];
			$documentoProveedor->puntoVenta = $dr['punto_venta'];
			$documentoProveedor->idTipo = $dr['tipo_factura'];
			$documentoProveedor->tipoDocum = $dr['tipo_docum'];
			$documentoProveedor->nroDocumento = $dr['nro_documento'];
			$documentoProveedor->letra = $dr['letra'];
			$documentoProveedor->idProveedor = $dr['cod_proveedor'];
			$documentoProveedor->operacionTipo = $dr['operacion_tipo'];
			$documentoProveedor->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$documentoProveedor->netoGravado = Funciones::toFloat($dr['neto_gravado'], 2);
			$documentoProveedor->netoNoGravado = Funciones::toFloat($dr['neto_no_gravado'], 2);
			$documentoProveedor->importeTotal = Funciones::toFloat($dr['importe_total'], 2);
			$documentoProveedor->importePendiente = Funciones::toFloat($dr['importe_pendiente'], 2);
			$documentoProveedor->condicionPlazoPago = $dr['condicion_plazo_pago'];
			$documentoProveedor->fechaVencimiento = Funciones::formatearFecha($dr['fecha_vencimiento'], 'd/m/Y');
			$documentoProveedor->fechaPeriodoFiscal = Funciones::formatearFecha($dr['fecha_periodo_fiscal'], 'd/m/Y');
			$documentoProveedor->observaciones = $dr['observaciones'];
			$documentoProveedor->documentoEnConflicto = $dr['documento_en_conflicto'];
			$documentoProveedor->idAsientoContable = $dr['cod_asiento_contable'];
			$documentoProveedor->idUsuario = $dr['cod_usuario'];
			$documentoProveedor->idUsuarioBaja = $dr['cod_usuario_baja'];
			$documentoProveedor->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$documentoProveedor->anulado = $dr['anulado'];
			$documentoProveedor->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$documentoProveedor->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$documentoProveedor->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			$documentoProveedor->idDocumentoGastoDatos = $dr['cod_documento_gasto_datos'];
			$documentoProveedor->facturaGastos = $dr['factura_gastos'];
			return $documentoProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumentoProveedorAplicacion($dr, DocumentoProveedorAplicacion $documentoProveedorAplicacion) {
		try {
			$documentoProveedorAplicacion->id = $dr['id'];
			$documentoProveedorAplicacion->empresa = $dr['empresa'];
			$documentoProveedorAplicacion->puntoDeVenta = $dr['punto_venta'];
			$documentoProveedorAplicacion->tipoDocumento = $dr['tipo_docum'];
			$documentoProveedorAplicacion->nroDocumento = $dr['nro_documento'];
			$documentoProveedorAplicacion->letra = $dr['letra'];
			$documentoProveedorAplicacion->idProveedor = $dr['cod_proveedor'];
			$documentoProveedorAplicacion->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$documentoProveedorAplicacion->importeTotal = Funciones::toFloat($dr['importe_total'], 2);
			$documentoProveedorAplicacion->importePendiente = Funciones::toFloat($dr['importe_pendiente'], 2);
			return $documentoProveedorAplicacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumentoProveedorAplicacionDebe($dr, DocumentoProveedorAplicacionDebe $documentoProveedorAplicacionDebe) {
		try {
			return $this->fillDocumentoProveedorAplicacion($dr, $documentoProveedorAplicacionDebe);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumentoProveedorAplicacionHaber($dr, DocumentoProveedorAplicacionHaber $documentoProveedorAplicacionHaber) {
		try {
			return $this->fillDocumentoProveedorAplicacion($dr, $documentoProveedorAplicacionHaber);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumentoProveedorHija($dr, DocumentoProveedorHija $documentoProveedorHija) {
		try {
			$documentoProveedorHija->id = $dr['id'];
			$documentoProveedorHija->empresa = $dr['empresa'];
			$documentoProveedorHija->anulado = $dr['anulada'];
			$documentoProveedorHija->importe = $dr['importe'];
			$documentoProveedorHija->idDocumentoCancelatorio = $dr['cod_cancel'];
			$documentoProveedorHija->cancelTipoDocumento = $dr['tipo_docum_cancel'];
			$documentoProveedorHija->idMadre = $dr['cod_madre'];
			$documentoProveedorHija->idUsuario = $dr['cod_usuario'];
			$documentoProveedorHija->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$documentoProveedorHija->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$documentoProveedorHija->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $documentoProveedorHija;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillDocumentoProveedorItem($dr, DocumentoProveedorItem $documentoProveedorItem) {
		try {
			$documentoProveedorItem->idDocumentoProveedor = $dr['cod_documento_proveedor'];
			$documentoProveedorItem->nroItem = $dr['nro_item'];
			$documentoProveedorItem->descripcion = $dr['descripcion'];
			$documentoProveedorItem->cantidad = Funciones::toFloat($dr['cantidad'], 2);
			$documentoProveedorItem->precioUnitario = Funciones::toFloat($dr['precio_unitario'], 4);
			$documentoProveedorItem->importe = Funciones::toFloat($dr['importe'], 2);
			for($i = 1; $i < 16; $i++) {
				$documentoProveedorItem->cantidades[$i] = $dr['cant_' . $i];
				$documentoProveedorItem->precios[$i] = Funciones::toFloat($dr['pr_' . $i], 2);
			}
			$documentoProveedorItem->usaRango = $dr['usa_rango'];
			$documentoProveedorItem->idImputacion = $dr['cod_imputacion'];
			$documentoProveedorItem->gravado = $dr['gravado'];
			$documentoProveedorItem->origenDetalle = $dr['origen_detalle'];
			$documentoProveedorItem->idRemitoPorOrdenDeCompra = $dr['cod_remito_orden_de_compra'];
			$documentoProveedorItem->idUsuario = $dr['cod_usuario'];
			$documentoProveedorItem->idUsuarioBaja = $dr['cod_usuario_baja'];
			$documentoProveedorItem->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$documentoProveedorItem->anulado = $dr['anulado'];
			$documentoProveedorItem->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$documentoProveedorItem->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$documentoProveedorItem->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');

			$documentoProveedorItem->id = $documentoProveedorItem->remitoPorOrdenDeCompra->id;
			$documentoProveedorItem->idMaterialColor = $documentoProveedorItem->remitoPorOrdenDeCompra->remitoProveedorItem->material->id . ' - ' . $documentoProveedorItem->remitoPorOrdenDeCompra->remitoProveedorItem->colorMateriaPrima->idColor;
			$documentoProveedorItem->total = $documentoProveedorItem->importe;
			$documentoProveedorItem->talles = $documentoProveedorItem->remitoPorOrdenDeCompra->ordenDeCompraItem->material->rango->posicion;
			return $documentoProveedorItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_Coupon($dr, Ecommerce_Coupon $ecommerce_Coupon) {
		try {
			$ecommerce_Coupon->id = $dr['cod_coupon'];
			$ecommerce_Coupon->idEcommerce = $dr['cod_coupon_ecommerce'];
			$ecommerce_Coupon->idOrder = $dr['cod_order'];
			$ecommerce_Coupon->code = $dr['instrument_id'];
			$ecommerce_Coupon->amount = $dr['amount'];
			$ecommerce_Coupon->percentage = $dr['percentage'];
			$ecommerce_Coupon->maxAmount = $dr['max_amount'];
			$ecommerce_Coupon->appliedAmount = $dr['applied_amount'];
			$ecommerce_Coupon->anulado = $dr['anulado'];
			$ecommerce_Coupon->idUsuario = $dr['cod_usuario'];
			$ecommerce_Coupon->idUsuarioBaja = $dr['cod_usuario_baja'];
			$ecommerce_Coupon->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$ecommerce_Coupon->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$ecommerce_Coupon->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$ecommerce_Coupon->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $ecommerce_Coupon;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_Customer($dr, Ecommerce_Customer $commerce_Customer) {
		try {
			$commerce_Customer->id = $dr['cod_customer'];
			$commerce_Customer->idEcommerce = $dr['cod_customer_ecommerce'];
			$commerce_Customer->idUsergroup = $dr['cod_usergroup'];
			$commerce_Customer->email = $dr['email'];
			$commerce_Customer->title = $dr['title'];
			$commerce_Customer->firstname = $dr['firstname'];
			$commerce_Customer->lastname = $dr['lastname'];
			$commerce_Customer->birthday = $dr['birthday'];
			$commerce_Customer->newsletters = $dr['newsletters'];
			$commerce_Customer->offers = $dr['offers'];
			$commerce_Customer->anulado = $dr['anulado'];
			$commerce_Customer->idUsuario = $dr['cod_usuario'];
			$commerce_Customer->idUsuarioBaja = $dr['cod_usuario_baja'];
			$commerce_Customer->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$commerce_Customer->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$commerce_Customer->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$commerce_Customer->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $commerce_Customer;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_Delivery($dr, Ecommerce_Delivery $commerce_Delivery) {
		try {
			$commerce_Delivery->id = $dr['cod_delivery'];
			$commerce_Delivery->idOrder = $dr['cod_order'];
			$commerce_Delivery->street = $dr['street'];
			$commerce_Delivery->city = $dr['city'];
			$commerce_Delivery->province = $dr['province'];
			$commerce_Delivery->pbox = $dr['pbox'];
			$commerce_Delivery->country = $dr['country'];
			$commerce_Delivery->phone = $dr['phone'];
			$commerce_Delivery->receptorName = $dr['receptor_name'];
			$commerce_Delivery->expectedDate = $dr['expected_date'];
			$commerce_Delivery->timeFrame = $dr['time_frame'];
			$commerce_Delivery->anulado = $dr['anulado'];
			$commerce_Delivery->idUsuario = $dr['cod_usuario'];
			$commerce_Delivery->idUsuarioBaja = $dr['cod_usuario_baja'];
			$commerce_Delivery->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$commerce_Delivery->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$commerce_Delivery->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$commerce_Delivery->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $commerce_Delivery;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_Order($dr, Ecommerce_Order $ecommerce_Order) {
		try {
			$ecommerce_Order->id = $dr['cod_order'];
			$ecommerce_Order->idEcommerce = $dr['cod_order_ecommerce'];
			$ecommerce_Order->idStatus = $dr['cod_status'];
			$ecommerce_Order->idServicioAndreani = $dr['cod_servicio_andreani'];
			$ecommerce_Order->idCustomer = $dr['cod_customer'];
			$ecommerce_Order->totalDiscount = $dr['total_discount'];
			$ecommerce_Order->totalCoupon = $dr['total_coupon'];
			$ecommerce_Order->grandTotal = $dr['grand_total'];
			$ecommerce_Order->fechaPedido = Funciones::formatearFecha($dr['fecha_pedido'], 'd/m/Y H:i');
			$ecommerce_Order->idDependenciasCumplidas = $dr['cod_dependencias_cumplidas'];
			$ecommerce_Order->idCuponDeCambio = $dr['cod_cupon_cambio'];
			$ecommerce_Order->cuponDeCambioUtilizado = $dr['cupon_cambio_utilizado'];
			$ecommerce_Order->cuponDeCambioImporte = $dr['cupon_cambio_importe'];
			$ecommerce_Order->anulado = $dr['anulado'];
			$ecommerce_Order->idUsuario = $dr['cod_usuario'];
			$ecommerce_Order->idUsuarioBaja = $dr['cod_usuario_baja'];
			$ecommerce_Order->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$ecommerce_Order->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$ecommerce_Order->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$ecommerce_Order->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $ecommerce_Order;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_OrderDetail($dr, Ecommerce_OrderDetail $ecommerce_OrderDetail) {
		try {
			$ecommerce_OrderDetail->id = $dr['cod_order_detail'];
			$ecommerce_OrderDetail->idOrder = $dr['cod_order'];
			$ecommerce_OrderDetail->reference = $dr['reference'];
			$ecommerce_OrderDetail->description = $dr['description'];
			$ecommerce_OrderDetail->size = $dr['size'];
			$ecommerce_OrderDetail->quantity = $dr['quantity'];
			$ecommerce_OrderDetail->price = $dr['price'];
			$ecommerce_OrderDetail->subtotal = $dr['subtotal'];
			$ecommerce_OrderDetail->anulado = $dr['anulado'];
			$ecommerce_OrderDetail->idUsuario = $dr['cod_usuario'];
			$ecommerce_OrderDetail->idUsuarioBaja = $dr['cod_usuario_baja'];
			$ecommerce_OrderDetail->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$ecommerce_OrderDetail->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$ecommerce_OrderDetail->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$ecommerce_OrderDetail->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $ecommerce_OrderDetail;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_OrderStatus($dr, Ecommerce_OrderStatus $ecommerce_OrderStatus) {
		try {
			$ecommerce_OrderStatus->id = $dr['cod_status'];
			$ecommerce_OrderStatus->nombre = $dr['nombre'];
			$ecommerce_OrderStatus->mostrarEnPanel = $dr['mostrar_en_panel'];
			$ecommerce_OrderStatus->idStatusAnterior = $dr['cod_status_anterior'];
			$ecommerce_OrderStatus->idProximoStatus = $dr['cod_proximo_status'];
			$ecommerce_OrderStatus->idDependencias = $dr['cod_dependencias'];
			$ecommerce_OrderStatus->anulado = $dr['anulado'];
			$ecommerce_OrderStatus->idUsuario = $dr['cod_usuario'];
			$ecommerce_OrderStatus->idUsuarioBaja = $dr['cod_usuario_baja'];
			$ecommerce_OrderStatus->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$ecommerce_OrderStatus->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$ecommerce_OrderStatus->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$ecommerce_OrderStatus->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $ecommerce_OrderStatus;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_OrderStatus_Cobrado($dr, Ecommerce_OrderStatus_Cobrado $ecommerce_OrderStatus_Cobrado) {
		try {
			return $this->fillEcommerce_OrderStatus($dr, $ecommerce_OrderStatus_Cobrado);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_OrderStatus_Despachado($dr, Ecommerce_OrderStatus_Despachado $ecommerce_OrderStatus_Despachado) {
		try {
			return $this->fillEcommerce_OrderStatus($dr, $ecommerce_OrderStatus_Despachado);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_OrderStatus_EnTransito($dr, Ecommerce_OrderStatus_EnTransito $ecommerce_OrderStatus_EnTransito) {
		try {
			return $this->fillEcommerce_OrderStatus($dr, $ecommerce_OrderStatus_EnTransito);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_OrderStatus_Facturado($dr, Ecommerce_OrderStatus_Facturado $ecommerce_OrderStatus_Facturado) {
		try {
			return $this->fillEcommerce_OrderStatus($dr, $ecommerce_OrderStatus_Facturado);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_OrderStatus_FacturadoCae($dr, Ecommerce_OrderStatus_FacturadoCae $ecommerce_OrderStatus_FacturadoCae) {
		try {
			return $this->fillEcommerce_OrderStatus($dr, $ecommerce_OrderStatus_FacturadoCae);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_OrderStatus_Finalizado($dr, Ecommerce_OrderStatus_Finalizado $ecommerce_OrderStatus_Finalizado) {
		try {
			return $this->fillEcommerce_OrderStatus($dr, $ecommerce_OrderStatus_Finalizado);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_OrderStatus_Pedido($dr, Ecommerce_OrderStatus_Pedido $ecommerce_OrderStatus_Pedido) {
		try {
			return $this->fillEcommerce_OrderStatus($dr, $ecommerce_OrderStatus_Pedido);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_OrderStatus_PendienteDeCambio($dr, Ecommerce_OrderStatus_PendienteDeCambio $ecommerce_OrderStatus_PendienteDeCambio) {
		try {
			return $this->fillEcommerce_OrderStatus($dr, $ecommerce_OrderStatus_PendienteDeCambio);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_OrderStatus_PendienteDeDevolucion($dr, Ecommerce_OrderStatus_PendienteDeDevolucion $ecommerce_OrderStatus_PendienteDeDevolucion) {
		try {
			return $this->fillEcommerce_OrderStatus($dr, $ecommerce_OrderStatus_PendienteDeDevolucion);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_OrderStatus_Predespachado($dr, Ecommerce_OrderStatus_Predespachado $ecommerce_OrderStatus_Predespachado) {
		try {
			return $this->fillEcommerce_OrderStatus($dr, $ecommerce_OrderStatus_Predespachado);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_OrderStatus_Remitido($dr, Ecommerce_OrderStatus_Remitido $ecommerce_OrderStatus_Remitido) {
		try {
			return $this->fillEcommerce_OrderStatus($dr, $ecommerce_OrderStatus_Remitido);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_Payment($dr, Ecommerce_Payment $ecommerce_Payment) {
		try {
			$ecommerce_Payment->id = $dr['cod_payment'];
			$ecommerce_Payment->idEcommerce = $dr['cod_payment_ecommerce'];
			$ecommerce_Payment->idOrder = $dr['cod_order'];
			$ecommerce_Payment->idMethod = $dr['cod_method'];
			$ecommerce_Payment->instrumentId = $dr['instrument_id'];
			$ecommerce_Payment->amount = $dr['amount'];
			$ecommerce_Payment->authId = $dr['auth_id'];
			$ecommerce_Payment->info = $dr['info'];
			$ecommerce_Payment->anulado = $dr['anulado'];
			$ecommerce_Payment->idUsuario = $dr['cod_usuario'];
			$ecommerce_Payment->idUsuarioBaja = $dr['cod_usuario_baja'];
			$ecommerce_Payment->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$ecommerce_Payment->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$ecommerce_Payment->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$ecommerce_Payment->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $ecommerce_Payment;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_PaymentMethod($dr, Ecommerce_PaymentMethod $ecommerce_PaymentMethod) {
		try {
			$ecommerce_PaymentMethod->id = $dr['cod_method'];
			$ecommerce_PaymentMethod->nombre = $dr['nombre'];
			$ecommerce_PaymentMethod->anulado = $dr['anulado'];
			$ecommerce_PaymentMethod->idUsuario = $dr['cod_usuario'];
			$ecommerce_PaymentMethod->idUsuarioBaja = $dr['cod_usuario_baja'];
			$ecommerce_PaymentMethod->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$ecommerce_PaymentMethod->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$ecommerce_PaymentMethod->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$ecommerce_PaymentMethod->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $ecommerce_PaymentMethod;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_ServicioAndreani($dr, Ecommerce_ServicioAndreani $ecommerce_ServicioAndreani) {
		try {
			$ecommerce_ServicioAndreani->id = $dr['cod_servicio_andreani'];
			$ecommerce_ServicioAndreani->nombre = $dr['nombre'];
			$ecommerce_ServicioAndreani->numeroDeContrato = $dr['numero_contrato'];
			$ecommerce_ServicioAndreani->anulado = $dr['anulado'];
			$ecommerce_ServicioAndreani->idUsuario = $dr['cod_usuario'];
			$ecommerce_ServicioAndreani->idUsuarioBaja = $dr['cod_usuario_baja'];
			$ecommerce_ServicioAndreani->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$ecommerce_ServicioAndreani->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$ecommerce_ServicioAndreani->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$ecommerce_ServicioAndreani->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $ecommerce_ServicioAndreani;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEcommerce_Usergroup($dr, Ecommerce_Usergroup $ecommerce_Usergroup) {
		try {
			$ecommerce_Usergroup->id = $dr['cod_usergroup'];
			$ecommerce_Usergroup->nombre = $dr['nombre'];
			$ecommerce_Usergroup->empresa = $dr['empresa'];
			$ecommerce_Usergroup->anulado = $dr['anulado'];
			$ecommerce_Usergroup->idUsuario = $dr['cod_usuario'];
			$ecommerce_Usergroup->idUsuarioBaja = $dr['cod_usuario_baja'];
			$ecommerce_Usergroup->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$ecommerce_Usergroup->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$ecommerce_Usergroup->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$ecommerce_Usergroup->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $ecommerce_Usergroup;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEfectivo($dr, Efectivo $efectivo) {
		try {
			$efectivo->id = $dr['cod_efectivo'];
			$efectivo->empresa = $dr['empresa'];
			$efectivo->importe = $dr['importe'];
			return $efectivo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEgresoDeFondosItem($dr, EgresoDeFondosItem $egresoDeFondosItem) {
		try {
			$egresoDeFondosItem->numeroOrdenDePago = $dr['numero'];
			$egresoDeFondosItem->empresa = $dr['empresa'];
			$egresoDeFondosItem->proveedor = $dr['de_para'];
			$egresoDeFondosItem->imputacionGeneral = $dr['imputacion_general'];
			$egresoDeFondosItem->imputacionEspecifica = $dr['imputacion_especifica'];
			$egresoDeFondosItem->denomEspecifica = $dr['denom_especifica'];
			$egresoDeFondosItem->denomGeneral = $dr['denom_general'];
			$egresoDeFondosItem->efectivo = $dr['efectivo'];
			$egresoDeFondosItem->cheques = $dr['cheques'];
			$egresoDeFondosItem->chequesPropios = $dr['cheques_propios'];
			$egresoDeFondosItem->chequesTerceros = $dr['cheques_terceros'];
			$egresoDeFondosItem->transferencias = $dr['transferencias'];
			$egresoDeFondosItem->total = $dr['total'];
			$egresoDeFondosItem->idCaja = $dr['cod_caja'];
			$egresoDeFondosItem->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			return $egresoDeFondosItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEjercicioContable($dr, EjercicioContable $ejercicioContable) {
		try {
			$ejercicioContable->id = $dr['cod_ejercicio_contable'];
			$ejercicioContable->nombre = $dr['nombre'];
			$ejercicioContable->fechaDesde = Funciones::formatearFecha($dr['fecha_desde'], 'd/m/Y');
			$ejercicioContable->fechaHasta = Funciones::formatearFecha($dr['fecha_hasta'], 'd/m/Y');
			$ejercicioContable->anulado = $dr['anulado'];
			$ejercicioContable->idUsuario = $dr['cod_usuario'];
			$ejercicioContable->idUsuarioBaja = $dr['cod_usuario_baja'];
			$ejercicioContable->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$ejercicioContable->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$ejercicioContable->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$ejercicioContable->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $ejercicioContable;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillEmail($dr, Email $email) {
		try {
			$email->id = $dr['cod_email'];
			$email->de = $dr['de'];
			$email->para = Funciones::jsonDecode($dr['para']);
			$email->cc = Funciones::jsonDecode($dr['cc']);
			$email->cco = Funciones::jsonDecode($dr['cco']);
			$email->asunto = $dr['asunto'];
			$email->contenido = $dr['contenido'];
			$email->imagenes = Funciones::jsonDecode($dr['imagenes']);
			$email->adjuntos = Funciones::jsonDecode($dr['adjuntos']);
			$email->fechaProgramada = Funciones::formatearFecha($dr['fecha_programada'], 'd/m/Y H:i:s');
			$email->fechaEnviado = Funciones::formatearFecha($dr['fecha_enviado'], 'd/m/Y H:i:s');
			$email->anulado = $dr['anulado'];
			$email->idUsuario = $dr['cod_usuario'];
			$email->idUsuarioBaja = $dr['cod_usuario_baja'];
			$email->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$email->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$email->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $email;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillExplosionLoteTemp($dr, ExplosionLoteTemp $explosionLoteTemp) {
		try {
			$explosionLoteTemp->id = $dr['cod_explosion_lote_temp'];
			$explosionLoteTemp->pi = $dr['pi'];
			$explosionLoteTemp->rubro = $dr['rubro'];
			$explosionLoteTemp->idProveedor = $dr['cod_proveedor'];
			$explosionLoteTemp->idMaterial = $dr['codigo'];
			$explosionLoteTemp->idColor = $dr['color'];
			$explosionLoteTemp->consumo = $dr['Consumo'];
			$explosionLoteTemp->pendiente = $dr['pendiente'];
			$explosionLoteTemp->stockUms = $dr['STK_UMS'];
			$explosionLoteTemp->comprometido = $dr['comprometido'];
			$explosionLoteTemp->precioUnitario = $dr['PU'];
			$explosionLoteTemp->preferente = $dr['preferente'];
			$explosionLoteTemp->stockMinimo = $dr['stk_min'];
			$explosionLoteTemp->factorConversion = $dr['FC'];
			$explosionLoteTemp->necesidad = $dr['Necesidad'];
			$explosionLoteTemp->cantidadComprar = $dr['Acomprar'];
			for($i = 1; $i < 11; $i++)
				$explosionLoteTemp->cantidadesComprar[$i] = $dr['cant_' . $i];
			$explosionLoteTemp->importe = $dr['importe'];
			$explosionLoteTemp->unidadMedida = $dr['unidad_medida'];
			$explosionLoteTemp->unidadMedidaCompra = $dr['UMC'];
			$explosionLoteTemp->stockUmc = $dr['Stk_UMC'];
			$explosionLoteTemp->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$explosionLoteTemp->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $explosionLoteTemp;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillFactura($dr, Factura $factura) {
		try {
			$factura->cancelNumero = $dr['cancel_nro_documento'];
			$factura->idEcommerceOrder = $dr['cod_ecommerce_order'];
			$factura = $this->fillDocumentoDebe($dr, $factura, false);
			return $factura;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillFacturaProveedor($dr, FacturaProveedor $facturaProveedor) {
		try {
			return $this->fillDocumentoProveedor($dr, $facturaProveedor);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillFajaHoraria($dr, FajaHoraria $fajaHoraria) {
		try {
			$fajaHoraria->id = $dr['cod_faja_horaria'];
			$fajaHoraria->anulado = $dr['anulado'];
			$fajaHoraria->nombre = $dr['denominacion_horario'];
			$fajaHoraria->horarioEntrada = Funciones::formatearFecha($dr['horario_entrada'], 'H:i');
			$fajaHoraria->horarioSalida = Funciones::formatearFecha($dr['horario_salida'], 'H:i');
			return $fajaHoraria;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillFasonier($dr, Fasonier $fasonier) {
		try {
			$fasonier->id = $dr['cod_prov'];
			$fasonier->tipo = $dr['tipo_operador'];
			$fasonier = $this->fillProveedor($dr, $fasonier);
			return $fasonier;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillFichaje($dr, Fichaje $fichaje) {
		try {
			$fichaje->id = $dr['clave_tabla'];
			$fichaje->legajo = $dr['legajo_nro'];
			$fichaje->tipo = $dr['movimiento_tipo'];
			$fichaje->anomalias = $dr['con_anomalias'];
			$fichaje->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$fichaje->horaEntrada = Funciones::formatearFecha($dr['entrada_horario'], 'H:i:s');
			$fichaje->horaSalida = Funciones::formatearFecha($dr['salida_horario'], 'H:i:s');
			$fichaje->diferenciaEntrada = $dr['diferencia_entrada'];
			$fichaje->diferenciaSalida = $dr['diferencia_salida'];
			$fichaje->lugarEntrada = $dr['ubicacion_tipo'];
			$fichaje->lugarSalida = $dr['ubicacion_confirmada'];
			$fichaje->tipoCorreccion = $dr['cod_tipo_correccion'];
			$fichaje->idMotivoAusentismo = $dr['cod_motivo_ausentismo'];
			return $fichaje;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillFilaAsientoContable($dr, FilaAsientoContable $filaAsientoContable) {
		try {
			$filaAsientoContable->idAsientoContable = $dr['cod_asiento'];
			$filaAsientoContable->numeroFila = $dr['numero_fila'];
			$filaAsientoContable->idImputacion = $dr['cod_imputacion'];
			$filaAsientoContable->importeDebe = Funciones::formatearDecimales($dr['importe_debe'], 2, '.');
			$filaAsientoContable->importeHaber = Funciones::formatearDecimales($dr['importe_haber'], 2, '.');
			$filaAsientoContable->fechaVencimiento = Funciones::formatearFecha($dr['fecha_vencimiento'], 'd/m/Y');
			$filaAsientoContable->observaciones = $dr['observaciones'];
			$filaAsientoContable->anulado = $dr['anulado'];
			$filaAsientoContable->idUsuario = $dr['cod_usuario'];
			$filaAsientoContable->idUsuarioBaja = $dr['cod_usuario_baja'];
			$filaAsientoContable->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$filaAsientoContable->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$filaAsientoContable->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$filaAsientoContable->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $filaAsientoContable;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
    private function fillForecast($dr, Forecast $forecast) {
        try {
            $forecast->id = $dr['IdForecast'];
            $forecast->nombre = $dr['Denom_Forecast'];
            $forecast->fecha = $dr['Fecha'];
            $forecast->fechaInicio = $dr['Fecha_ingreso'];
            $forecast->fechaFin = $dr['Fecha_Fin'];
            $forecast->importado = $dr['aprobado'];
            $forecast->observaciones = $dr['Observaciones'];
            $forecast->anulado = $dr['anulado'];
            return $forecast;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    private function fillForecastItem($dr, ForecastItem $forecastItem) {
        try {
            $forecastItem->id = $dr['idDetForecast'];
            $forecastItem->idForecast = $dr['IdForecast'];
            $forecastItem->idArticulo = $dr['Cod_Articulo'];
            $forecastItem->idColorPorArticulo = $dr['Cod_Color_articulo'];
            $forecastItem->version = $dr['version'];
            $forecastItem->cantidadTotal = $dr['Cantidad'];
            for ($i = 1; $i <= 10; $i++)
                $forecastItem->cantidad[$i] = $dr['cant_' . $i];
            return $forecastItem;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
	private function fillFormaDePago($dr, FormaDePago $formaDePago) {
		try {
			$formaDePago->id = $dr['cod_forma_pago_num'];
			$formaDePago->dias = $dr['cod_forma_pago_num'];
			$formaDePago->anulado = $dr['anulado'];
			$formaDePago->nombre = $dr['denom_forma_pago'];
			return $formaDePago;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillFuncionalidadPorRol($dr, FuncionalidadPorRol $funcionalidadPorRol) {
		try {
			$funcionalidadPorRol->idFuncionalidad = $dr['cod_funcionalidad'];
			$funcionalidadPorRol->idRol = $dr['cod_rol'];
			return $funcionalidadPorRol;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillGastito($dr, Gastito $gastito) {
		try {
			$gastito->id = $dr['cod_gastito'];
			$gastito->empresa = $dr['empresa'];
			$gastito->importe = $dr['importe'];
			$gastito->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$gastito->idPersonaGasto = $dr['cod_persona_gasto'];
			$gastito->comprobante = $dr['comprobante'];
			$gastito->observaciones = $dr['observaciones'];
			$gastito->idRendicionGastos = $dr['cod_rendicion_gastos'];
			$gastito->idCaja = $dr['cod_caja'];
			$gastito->idUsuario = $dr['cod_usuario'];
			$gastito->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$gastito->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$gastito->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $gastito;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillGarantia($dr, Garantia $garantia) {
		try {
			$garantia->id = $dr['cod_garantia'];
			$garantia->clasificada = $dr['clasificada'];
			$garantia->devuelta = $dr['devuelta'];
			$garantia->solucionNcr = $dr['solucion_ncr'];
			$garantia->totalNcr = $dr['total_ncr'];
			$garantia->idCliente = $dr['cod_cliente'];
			$garantia->idOrder = $dr['cod_order'];
			$garantia->movimientos = Funciones::jsonDecode($dr['movimientos']);
			$garantia->derivada = $dr['derivada'];
			$garantia->idMotivo = $dr['cod_motivo'];
			$garantia->observaciones = $dr['observaciones'];
			$garantia->anulado = $dr['anulado'];
			$garantia->idUsuario = $dr['cod_usuario'];
			$garantia->idUsuarioBaja = $dr['cod_usuario_baja'];
			$garantia->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$garantia->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$garantia->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$garantia->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $garantia;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillGarantiaItem($dr, GarantiaItem $garantiaItem) {
		try {
			$garantiaItem->id = $dr['id'];
			$garantiaItem->idGarantia = $dr['cod_garantia'];
			$garantiaItem->idAlmacen = $dr['cod_almacen'];
			$garantiaItem->idArticulo = $dr['cod_articulo'];
			$garantiaItem->idColorPorArticulo = $dr['cod_color_articulo'];
			$garantiaItem->importeNcr = $dr['importe_ncr'];
			for ($i = 1; $i <= 10; $i++)
				$garantiaItem->cantidad[$i] = $dr['cant_' . $i];
			return $garantiaItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillSeguimientoCliente($dr, SeguimientoCliente $seguimientoCliente) {
		try {
			$seguimientoCliente->id = $dr['id'];
			$seguimientoCliente->idCliente = $dr['cod_cli'];
			$seguimientoCliente->fechaGestion = Funciones::formatearFecha($dr['fecha_gestion'], 'd/m/Y');
			$seguimientoCliente->observaciones = $dr['observaciones'];
			$seguimientoCliente->estado = $dr['estado'];
			$seguimientoCliente->anulado = $dr['anulado'];
			$seguimientoCliente->idUsuario = $dr['cod_usuario'];
			$seguimientoCliente->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$seguimientoCliente->idUsuarioBaja = $dr['cod_usuario_baja'];
			$seguimientoCliente->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$seguimientoCliente->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			$seguimientoCliente->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			return $seguimientoCliente;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillGrupoEmpresa($dr, GrupoEmpresa $grupoEmpresa) {
		try {
			$grupoEmpresa->id = $dr['cod_grupo_empresa'];
			$grupoEmpresa->anulado = $dr['anulado'];
			$grupoEmpresa->comisionPorVentas = $dr['Comision_ventas'];
			$grupoEmpresa->nombre = $dr['denominacion'];
			return $grupoEmpresa;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillKoiTicket($dr, KoiTicket $koiTicket) {
		try {
			$koiTicket->id = $dr['cod_koi_ticket'];
			$koiTicket->idAreaEmpresa = $dr['cod_area_empresa'];
			$koiTicket->descripcion = $dr['descripcion'];
			$koiTicket->respuesta = $dr['respuesta'];
			$koiTicket->prioridadExterna = $dr['prioridad_externa'];
			$koiTicket->prioridadInterna = $dr['prioridad_interna'];
			$koiTicket->prioridad = $dr['prioridad'];
			$koiTicket->idTicketOriginal = $dr['cod_ticket_original'];
			$koiTicket->idResponsable = $dr['cod_responsable'];
			$koiTicket->fechaEstimadaResolucion = Funciones::formatearFecha($dr['fecha_estimada_resolucion'], 'd/m/Y');
			$koiTicket->estado = $dr['estado'];
			$koiTicket->idUsuarioCierre = $dr['cod_usuario_cierre'];
			$koiTicket->fechaCierre = Funciones::formatearFecha($dr['fecha_cierre'], 'd/m/Y');
			$koiTicket->anulado = $dr['anulado'];
			$koiTicket->idUsuario = $dr['cod_usuario'];
			$koiTicket->idUsuarioBaja = $dr['cod_usuario_baja'];
			$koiTicket->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$koiTicket->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$koiTicket->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			$koiTicket->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			return $koiTicket;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillHorma($dr, Horma $horma) {
		try {
			$horma->id = trim($dr['cod_horma']);
			$horma->activa = $dr['activa'];
			$horma->colorExterno = $dr['color_externo'];
			$horma->diseniador = $dr['diseñador'];
			$horma->fabricante = $dr['fabricante'];
			$horma->fechaAlta = Funciones::formatearFecha($dr['incorporada_fecha'], 'd/m/Y');
			$horma->fechaBaja = Funciones::formatearFecha($dr['desactivada_fecha'], 'd/m/Y');
			$horma->nombre = $dr['denom_horma'];
			$horma->observaciones = $dr['observaciones'];
			$horma->punto = $dr['punto'];
			$horma->retiradaPor = $dr['decidio_retirar'];
			$horma->talleDesde = $dr['talles_desde'];
			$horma->talleHasta = $dr['talles_hasta'];
			return $horma;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillImportePorOperacion($dr, ImportePorOperacion $importePorOperacion) {
		try {
			$importePorOperacion->idImportePorOperacion = $dr['cod_importe_operacion'];
			$importePorOperacion->tipoOperacion = $dr['tipo_transferencia'];
			$importePorOperacion->idCaja = $dr['cod_caja'];
			$importePorOperacion->fechaCaja = Funciones::formatearFecha($dr['fecha_caja'], 'd/m/Y');
			$importePorOperacion->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			return $importePorOperacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillImportePorOperacionItem($dr, ImportePorOperacionItem $importePorOperacionItem) {
		try {
			$importePorOperacionItem->idImportePorOperacion = $dr['cod_importe_operacion'];
			$importePorOperacionItem->tipoOperacion = $dr['tipo_transferencia'];
			$importePorOperacionItem->idCaja = $dr['cod_caja'];
			$importePorOperacionItem->tipoImporte = $dr['tipo_importe'];
			$importePorOperacionItem->idImporte = $dr['cod_importe'];
			$importePorOperacionItem->anulado = $dr['anulado'];
			return $importePorOperacionItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillImputacion($dr, Imputacion $imputacion) {
		try {
			$imputacion->id = $dr['cuenta'];
			$imputacion->nombre = trim($dr['denominacion']);
			$imputacion->imputable = $dr['es_imputable'];
			$imputacion->idUsuario = $dr['cod_usuario'];
			$imputacion->idUsuarioBaja = $dr['cod_usuario_baja'];
			$imputacion->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$imputacion->anulado = $dr['anulado'];
			$imputacion->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$imputacion->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$imputacion->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $imputacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillImpuesto($dr, Impuesto $imputacion) {
		try {
			$imputacion->id = $dr['cod_impuesto'];
			$imputacion->nombre = $dr['nombre'];
			$imputacion->descripcion = $dr['descripcion'];
			$imputacion->idImputacion = $dr['cod_imputacion'];
			$imputacion->porcentaje = $dr['porcentaje'];
			$imputacion->esGravado = $dr['es_gravado'];
			$imputacion->tipo = $dr['tipo'];
			$imputacion->idUsuario = $dr['cod_usuario'];
			$imputacion->idUsuarioBaja = $dr['cod_usuario_baja'];
			$imputacion->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$imputacion->anulado = $dr['anulado'];
			$imputacion->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$imputacion->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$imputacion->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $imputacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillImpuestoPorDocumentoProveedor($dr, ImpuestoPorDocumentoProveedor $impuestoPorDocumentoProveedor) {
		try {
			$impuestoPorDocumentoProveedor->idDocumentoProveedor = $dr['cod_documento_proveedor'];
			$impuestoPorDocumentoProveedor->idImpuesto = $dr['cod_impuesto'];
			$impuestoPorDocumentoProveedor->porcentaje = Funciones::toFloat($dr['porcentaje'], 2);
			$impuestoPorDocumentoProveedor->importe = Funciones::toFloat($dr['importe'], 2);
			return $impuestoPorDocumentoProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillIndicador($dr, Indicador $indicador) {
		try {
			$indicador->id = $dr['cod_indicador'];
			$indicador->nombre = $dr['nombre'];
			$indicador->descripcion = $dr['descripcion'];
			$indicador->view = $dr['nombre_view'];
			$indicador->valor1 = $dr['valor_1'];
			$indicador->valor2 = $dr['valor_2'];
			$indicador->valor3 = $dr['valor_3'];
			$indicador->where = $dr['clausula_where'];
			$indicador->query = $dr['query'];
			$indicador->fields = $dr['fields'];
			$indicador->anulado = $dr['anulado'];
			$indicador->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$indicador->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$indicador->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $indicador;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillIndicadorPorRol($dr, IndicadorPorRol $indicadorPorRol) {
		try {
			$indicadorPorRol->idIndicador = $dr['cod_indicador'];
			$indicadorPorRol = $this->fillRol($dr, $indicadorPorRol);
			return $indicadorPorRol;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillIngresoChequePropio($dr, IngresoChequePropio $ingresoChequePropio) {
		try {
			$ingresoChequePropio->numero = $dr['cod_ingreso_cheque_propio'];
			$ingresoChequePropio->empresa = $dr['empresa'];
			$ingresoChequePropio->idImportePorOperacion = $dr['cod_importe_operacion'];
			$ingresoChequePropio->importeTotal = $dr['importe_total'];
			$ingresoChequePropio->observaciones = $dr['observaciones'];
			$ingresoChequePropio->idUsuario = $dr['cod_usuario'];
			$ingresoChequePropio->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			return $ingresoChequePropio;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillInstruccionArticulo($dr, InstruccionArticulo $instruccionArticulo) {
		try {
			$instruccionArticulo->idArticulo = $dr['cod_articulo'];
			$instruccionArticulo->idSeccion = $dr['cod_seccion'];
			$instruccionArticulo->interna = $dr['interna'];
			$instruccionArticulo->instruccion = $dr['instruccion'];
			$instruccionArticulo->anulado = $dr['anulado'];
			$instruccionArticulo->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_modificacion'], 'd/m/Y');
			return $instruccionArticulo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillLineaProducto($dr, LineaProducto $lineaProducto) {
		try {
			$lineaProducto->id = $dr['cod_linea_nro'];
			$lineaProducto->anulado = $dr['anulado'];
			$lineaProducto->fechaAlta = Funciones::formatearFecha($dr['fechaAlta'], 'd/m/Y');
			$lineaProducto->fechaBaja = Funciones::formatearFecha($dr['fecha_de_baja'], 'd/m/Y');
			$lineaProducto->fechaLanzamiento = Funciones::formatearFecha($dr['lanzamiento_inicial'], 'd/m/Y');
			$lineaProducto->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_modificacion'], 'd/m/Y');
			$lineaProducto->nombre = $dr['denom_linea'];
			$lineaProducto->tituloCatalogo = $dr['titulo_catalogo'];
			//$lineaProducto->tituloEcommerce = $dr['titulo_ecommerce'];
			$lineaProducto->origen = $dr['origen'];
			return $lineaProducto;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillLocalidad($dr, Localidad $localidad) {
		try {
			$localidad->id = $dr['cod_localidad_nro'];
			$localidad->idPais = $dr['cod_pais'];
			$localidad->idProvincia = $dr['cod_provincia'];
			$localidad->anulado = $dr['ANULADO'];
			$localidad->nombre = $dr['denom_localidad'];
			$localidad->codigoPostal = $dr['cod_postal'];
			$localidad->idZona = $dr['cod_zona_geo'];
			return $localidad;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillMarca($dr, Marca $marca) {
		try {
			$marca->id = $dr['cod_marca'];
			$marca->anulado = $dr['anulado'];
			$marca->fechaAlta = Funciones::formatearFecha($dr['fechaAlta'], 'd/m/Y');
			$marca->fechaBaja = Funciones::formatearFecha($dr['fechaBaja'], 'd/m/Y');
			$marca->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_modificacion'], 'd/m/Y');
			$marca->logo = $dr['logo'];
			$marca->nombre = $dr['denom_marca'];
			return $marca;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillMaterial($dr, Material $material) {
		try {
			$material->id = $dr['cod_material'];
			$material->nombre = $dr['denom_material'];
			$material->idRubro = $dr['cod_rubro'];
			$material->idSubrubro = $dr['cod_subrubro'];
			$material->usaRango = $dr['usa_rango'];
			$material->precioPorTalle = $dr['precio_por_talle'];
			$material->idRango = $dr['cod_rango'];
			$material->idUnidadMedida = $dr['unidad_medida'];
			$material->idUnidadMedidaCompra = $dr['unidad_medida_compra'];
			$material->factorConversion = (empty($dr['factor_conversion']) ? 1 : $dr['factor_conversion']);
			$material->loteMinimo = $dr['lote_minimo'];
			$material->loteMultiplo = $dr['lote_multiplo'];
			$material->anticipacionCompra = $dr['anticipacion_compra'];
			$material->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_modificacion'], 'd/m/Y');
			$material->produccionInterna = $dr['produccion_interna'];
			$material->fotografia = $dr['fotografia'];
			$material->packaging = $dr['packaging'];
			$material->espesor = $dr['espesor'];
			$material->textura = $dr['textura'];
			$material->soporte = $dr['soporte'];
			$material->materialPredomina = $dr['material_predomina'];
			$material->trazabilidadOblig = $dr['trazabilidad_oblig'];
			$material->muestraEnPlanificacion = $dr['muestra_en_planificacion'];
			$material->tieneCorrimiento = $dr['tiene_corrimiento'];
			$material->idArticulo = $dr['cod_articulo'];
			$material->naturaleza = $dr['naturaleza'];
			return $material;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillMotivo($dr, Motivo $motivoRechazoCheque) {
		try {
			$motivoRechazoCheque->id = $dr['cod_motivo'];
			$motivoRechazoCheque->nombre = $dr['nombre_motivo'];
			$motivoRechazoCheque->tipo = $dr['tipo_motivo'];
			$motivoRechazoCheque->descripcion = $dr['descripcion'];
			$motivoRechazoCheque->anulado = $dr['anulado'];
			$motivoRechazoCheque->fechaAlta = Funciones::formatearFecha($dr['fechaAlta'], 'd/m/Y');
			$motivoRechazoCheque->fechaBaja = Funciones::formatearFecha($dr['fechaBaja'], 'd/m/Y');
			$motivoRechazoCheque->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $motivoRechazoCheque;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillMotivoAusentismo($dr, MotivoAusentismo $motivoAusentismo) {
		try {
			$motivoAusentismo->id = $dr['id'];
			$motivoAusentismo->nombre = $dr['nombre_motivo'];
			$motivoAusentismo->anulado = $dr['anulado'];
			$motivoAusentismo->idUsuario = $dr['cod_usuario'];
			$motivoAusentismo->idUsuarioBaja = $dr['cod_usuario_baja'];
			$motivoAusentismo->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$motivoAusentismo->fechaAlta = Funciones::formatearFecha($dr['fechaAlta'], 'd/m/Y');
			$motivoAusentismo->fechaBaja = Funciones::formatearFecha($dr['fechaBaja'], 'd/m/Y');
			$motivoAusentismo->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $motivoAusentismo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillMovimientoAlmacen($dr, MovimientoAlmacen $movimientoAlmacen) {
		try {
			$movimientoAlmacen->id = $dr['id'];
			$movimientoAlmacen->idConfirmacion = $dr['cod_confirmacion'];
			$movimientoAlmacen->tipoMovimiento = $dr['tipo_movimiento'];
			$movimientoAlmacen->idAlmacen = $dr['cod_almacen'];
			$movimientoAlmacen->idArticulo = $dr['cod_articulo'];
			$movimientoAlmacen->idColorPorArticulo = $dr['cod_color_articulo'];
			$movimientoAlmacen->motivo = $dr['motivo'];
			for ($i = 1; $i <= 10; $i++)
				$movimientoAlmacen->cantidad[$i] = $dr['cant_' . $i];
			$movimientoAlmacen->idUsuario = $dr['cod_usuario'];
			$movimientoAlmacen->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			return $movimientoAlmacen;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillMovimientoAlmacenConfirmacion($dr, MovimientoAlmacenConfirmacion $movimientoAlmacenConfirmacion) {
		try {
			$movimientoAlmacenConfirmacion->id = $dr['cod_confirmacion'];
			$movimientoAlmacenConfirmacion->idAlmacenOrigen = $dr['cod_almacen_origen'];
			$movimientoAlmacenConfirmacion->idAlmacenDestino = $dr['cod_almacen_destino'];
			$movimientoAlmacenConfirmacion->idArticulo = $dr['cod_articulo'];
			$movimientoAlmacenConfirmacion->idColorPorArticulo = $dr['cod_color_articulo'];
			$movimientoAlmacenConfirmacion->motivo = $dr['motivo'];
			for ($i = 1; $i <= 10; $i++)
				$movimientoAlmacenConfirmacion->cantidad[$i] = $dr['cant_' . $i];
			$movimientoAlmacenConfirmacion->confirmado = $dr['confirmado'];
			$movimientoAlmacenConfirmacion->anulado = $dr['anulado'];
			$movimientoAlmacenConfirmacion->idUsuario = $dr['cod_usuario'];
			$movimientoAlmacenConfirmacion->idUsuarioBaja = $dr['cod_usuario_baja'];
			$movimientoAlmacenConfirmacion->idUsuarioConfirmacion = $dr['cod_usuario_confirmacion'];
			$movimientoAlmacenConfirmacion->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$movimientoAlmacenConfirmacion->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$movimientoAlmacenConfirmacion->fechaConfirmacion = Funciones::formatearFecha($dr['fecha_confirmacion'], 'd/m/Y');
			return $movimientoAlmacenConfirmacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillMovimientoAlmacenMP($dr, MovimientoAlmacenMP $movimientoAlmacenMP) {
		try {
			$movimientoAlmacenMP->id = $dr['id'];
			$movimientoAlmacenMP->idConfirmacion = $dr['cod_confirmacion'];
			$movimientoAlmacenMP->tipoMovimiento = $dr['tipo_movimiento'];
			$movimientoAlmacenMP->idAlmacen = $dr['cod_almacen'];
			$movimientoAlmacenMP->idMaterial = $dr['cod_material'];
			$movimientoAlmacenMP->idColorMateriaPrima = $dr['cod_color'];
			$movimientoAlmacenMP->motivo = $dr['motivo'];
			for ($i = 1; $i <= 10; $i++)
				$movimientoAlmacenMP->cantidad[$i] = $dr['cant_' . $i];
			$movimientoAlmacenMP->idUsuario = $dr['cod_usuario'];
			$movimientoAlmacenMP->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			return $movimientoAlmacenMP;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillMovimientoAlmacenConfirmacionMP($dr, MovimientoAlmacenConfirmacionMP $movimientoAlmacenConfirmacionMP) {
		try {
			$movimientoAlmacenConfirmacionMP->id = $dr['cod_confirmacion'];
			$movimientoAlmacenConfirmacionMP->idAlmacenOrigen = $dr['cod_almacen_origen'];
			$movimientoAlmacenConfirmacionMP->idAlmacenDestino = $dr['cod_almacen_destino'];
			$movimientoAlmacenConfirmacionMP->idMaterial = $dr['cod_material'];
			$movimientoAlmacenConfirmacionMP->idColorMateriaPrima = $dr['cod_color'];
			$movimientoAlmacenConfirmacionMP->motivo = $dr['motivo'];
			for ($i = 1; $i <= 10; $i++)
				$movimientoAlmacenConfirmacionMP->cantidad[$i] = $dr['cant_' . $i];
			$movimientoAlmacenConfirmacionMP->confirmado = $dr['confirmado'];
			$movimientoAlmacenConfirmacionMP->anulado = $dr['anulado'];
			$movimientoAlmacenConfirmacionMP->idUsuario = $dr['cod_usuario'];
			$movimientoAlmacenConfirmacionMP->idUsuarioBaja = $dr['cod_usuario_baja'];
			$movimientoAlmacenConfirmacionMP->idUsuarioConfirmacion = $dr['cod_usuario_confirmacion'];
			$movimientoAlmacenConfirmacionMP->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$movimientoAlmacenConfirmacionMP->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$movimientoAlmacenConfirmacionMP->fechaConfirmacion = Funciones::formatearFecha($dr['fecha_confirmacion'], 'd/m/Y');
			return $movimientoAlmacenConfirmacionMP;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillMovimientoStock($dr, MovimientoStock $movimientoStock) {
		try {
			$movimientoStock->id = $dr['id'];
			$movimientoStock->tipoMovimiento = $dr['tipo_movimiento'];
			$movimientoStock->tipoOperacion = $dr['tipo_operacion'];
			$movimientoStock->keyObjeto = $dr['key_objeto'];
			$movimientoStock->observaciones = $dr['observaciones'];
			$movimientoStock->idAlmacen = $dr['cod_almacen'];
			$movimientoStock->idArticulo = $dr['cod_articulo'];
			$movimientoStock->idColorPorArticulo = $dr['cod_color_articulo'];
			for ($i = 1; $i <= 10; $i++)
				$movimientoStock->cantidad[$i] = $dr['cant_' . $i];
			$movimientoStock->idUsuario = $dr['cod_usuario'];
			$movimientoStock->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			return $movimientoStock;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillMovimientoStockMP($dr, MovimientoStockMP $movimientoStockMP) {
		try {
			$movimientoStockMP->id = $dr['id'];
			$movimientoStockMP->tipoMovimiento = $dr['tipo_movimiento'];
			$movimientoStockMP->tipoOperacion = $dr['tipo_operacion'];
			$movimientoStockMP->keyObjeto = $dr['key_objeto'];
			$movimientoStockMP->observaciones = $dr['observaciones'];
			$movimientoStockMP->idAlmacen = $dr['cod_almacen'];
			$movimientoStockMP->idMaterial = $dr['cod_material'];
			$movimientoStockMP->idColorMateriaPrima = $dr['cod_color'];
			for ($i = 1; $i <= 10; $i++)
				$movimientoStockMP->cantidad[$i] = $dr['cant_' . $i];
			$movimientoStockMP->idUsuario = $dr['cod_usuario'];
			$movimientoStockMP->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			return $movimientoStockMP;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillNotaDeCredito($dr, NotaDeCredito $notaDeCredito) {
		try {
			$notaDeCredito->idCausa = $dr['agrupa_causa'];
			$notaDeCredito->cancelNumero = $dr['cancel_nro_documento'];
			$notaDeCredito = $this->fillDocumentoHaber($dr, $notaDeCredito, false);
			return $notaDeCredito;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillNotaDeCreditoProveedor($dr, NotaDeCreditoProveedor $notaDeCreditoProveedor) {
		try {
			$notaDeCreditoProveedor->idFacturaCancelatoria = $dr['cod_factura_cancelatoria'];
			return $this->fillDocumentoProveedor($dr, $notaDeCreditoProveedor);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillNotaDeDebito($dr, NotaDeDebito $notaDeDebito) {
		try {
			$notaDeDebito = $this->fillDocumentoDebe($dr, $notaDeDebito, false);
			return $notaDeDebito;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillNotaDeDebitoProveedor($dr, NotaDeDebitoProveedor $notaDeDebitoProveedor) {
		try {
			return $this->fillDocumentoProveedor($dr, $notaDeDebitoProveedor);
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillNotificacion($dr, Notificacion $notificacion) {
		try {
			$notificacion->id = $dr['cod_notificacion'];
			$notificacion->idTipoNotificacion = $dr['tipo_notificacion'];
			$notificacion->keyObjeto = $dr['key_objeto'];
			$notificacion->link = $dr['link'];
			$notificacion->detalle = $dr['detalle'];
			$notificacion->anulado = $dr['anulado'];
			$notificacion->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$notificacion->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$notificacion->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y H:i');
			return $notificacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillNotificacionPorUsuario($dr, NotificacionPorUsuario $notificacionPorUsuario) {
		try {
			$notificacionPorUsuario->anulado = $dr['anulado'];
			$notificacionPorUsuario->idNotificacion = $dr['cod_notificacion'];
			$notificacionPorUsuario->vista = $dr['vista'];
			$notificacionPorUsuario->eliminable = $dr['eliminable'];
			$notificacionPorUsuario = $this->fillUsuario($dr, $notificacionPorUsuario);
			$notificacionPorUsuario->fechaUltimaMod = date('d/m/Y H:i:s', strtotime($dr['fecha_ultima_mod'])); //Va despues para pisarlo. No tocar
			return $notificacionPorUsuario;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillOperador($dr, Operador $operador) {
		try {
			$operador->id = $dr['cod_operador'];
			$operador->tipo = $dr['tipo_operador'];
			$operador->porcComisionVtas = $dr['porc_comision_vtas'];
			$operador = $this->fillPersonal($dr, $operador);
			return $operador;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillOrdenDeCompra($dr, OrdenDeCompra $ordenDeCompra) {
		try {
			$ordenDeCompra->id = $dr['cod_orden_de_compra'];
			$ordenDeCompra->codSucursal = $dr['cod_sucursal'];
			$ordenDeCompra->numero = $dr['nro_orden_compra'];
			$ordenDeCompra->idProveedor = $dr['cod_proveedor'];
			$ordenDeCompra->fechaEmision = Funciones::formatearFecha($dr['fecha_emision'], 'd/m/Y');
			$ordenDeCompra->idAlmacen = $dr['cod_almacen_entrega'];
			$ordenDeCompra->idLoteDeProduccion = $dr['nro_lote'];
			$ordenDeCompra->usaRango = $dr['importe_pendiente'];
			$ordenDeCompra->observaciones = $dr['observaciones'];
			$ordenDeCompra->esHexagono = $dr['es_hexagono'];
			$ordenDeCompra->idUsuario = $dr['cod_usuario'];
			$ordenDeCompra->idUsuarioBaja = $dr['cod_usuario_baja'];
			$ordenDeCompra->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$ordenDeCompra->anulado = $dr['anulado'];
			$ordenDeCompra->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$ordenDeCompra->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$ordenDeCompra->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $ordenDeCompra;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillOrdenDeCompraItem($dr, OrdenDeCompraItem $ordenDeCompraItem) {
		try {
			$ordenDeCompraItem->idOrdenDeCompra = $dr['cod_orden_de_compra'];
			$ordenDeCompraItem->numeroDeItem = $dr['nro_item'];
			$ordenDeCompraItem->idMaterial = $dr['cod_material'];
			$ordenDeCompraItem->idColorMaterial = $dr['cod_color'];
			$ordenDeCompraItem->fechaEntrega = Funciones::formatearFecha($dr['fecha_entrega'], 'd/m/Y');
			$ordenDeCompraItem->precioUnitario = Funciones::formatearDecimales($dr['precio_unitario'], 4, '.');
			$ordenDeCompraItem->cantidad = Funciones::formatearDecimales($dr['cantidad'], 4, '.');
			$ordenDeCompraItem->cantidadPendiente = $dr['cantidad_pendiente'];
			$ordenDeCompraItem->importe = Funciones::formatearDecimales($dr['importe'], 2, '.');
			for($i = 1; $i < 16; $i++){
				$ordenDeCompraItem->cantidades[$i] = $dr['cant_' . $i];
				$ordenDeCompraItem->precios[$i] = Funciones::formatearDecimales($dr['pr_' . $i], 4, '.');
				$ordenDeCompraItem->cantidadesPendientes[$i] = $dr['cant_p_' . $i];
			}
			$ordenDeCompraItem->letraFactura = $dr['letra_factura'];
			$ordenDeCompraItem->sucursalFactura = $dr['factura_sucursal'];
			$ordenDeCompraItem->loteDeCompra = $dr['nro_lote_compra'];
			$ordenDeCompraItem->anulado = $dr['anulado'];
			$ordenDeCompraItem->idUsuario = $dr['cod_usuario'];
			$ordenDeCompraItem->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$ordenDeCompraItem->idUsuarioBaja = $dr['cod_usuario_baja'];
			$ordenDeCompraItem->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$ordenDeCompraItem->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$ordenDeCompraItem->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			$ordenDeCompraItem->idImpuesto = $dr['cod_impuesto'];
			$ordenDeCompraItem->importeImpuesto = $dr['importe_impuesto'];
			return $ordenDeCompraItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillOrdenDeFabricacion($dr, OrdenDeFabricacion $ordenDeFabricacion) {
		try {
			$ordenDeFabricacion->id = $dr['nro_orden_fabricacion'];
			$ordenDeFabricacion->idLoteDeProduccion = $dr['nro_plan'];
            $ordenDeFabricacion->tipoOrden = $dr['tipo_orden'];
			$ordenDeFabricacion->idArticulo = $dr['cod_articulo'];
			$ordenDeFabricacion->idColorPorArticulo = $dr['cod_color_articulo'];
			$ordenDeFabricacion->version = $dr['version'];
			$ordenDeFabricacion->confirmada = $dr['Confirmada'];
			$ordenDeFabricacion->idCurvaDeProduccion = $dr['cod_modulo'];
            $ordenDeFabricacion->cantidadOptimaProduccion = $dr['cantidad_optima_produccion'];
			$ordenDeFabricacion->cantidadTotal = $dr['cantidad'];
			for ($i = 1; $i < 10; $i++)
				$ordenDeFabricacion->cantidad[$i] = $dr['co_' . $i];
            $ordenDeFabricacion->anulado = $dr['anulado'];
			$ordenDeFabricacion->fechaInicio = Funciones::formatearFecha($dr['fecha_inicio'], 'd/m/Y');
			$ordenDeFabricacion->fechaFin = Funciones::formatearFecha($dr['fecha_fin_programada'], 'd/m/Y');
			return $ordenDeFabricacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillOrdenDePago($dr, OrdenDePago $ordenDePago) {
		try {
			$ordenDePago->numero = $dr['nro_orden_de_pago'];
			$ordenDePago->empresa = $dr['empresa'];
			$ordenDePago->idImportePorOperacion = $dr['cod_importe_operacion'];
			$ordenDePago->idProveedor = $dr['cod_proveedor'];
			$ordenDePago->tipoOperacion = $dr['operacion_tipo'];
			$ordenDePago->idImputacion = $dr['imputacion'];
			$ordenDePago->importePendiente = Funciones::formatearDecimales($dr['importe_pendiente'], 2, '.');
			$ordenDePago->importeTotal = Funciones::formatearDecimales($dr['importe_total'], 2, '.');
			$ordenDePago->importeSujetoRetencion = Funciones::formatearDecimales($dr['importe_sujeto_ret'], 2, '.');
			$ordenDePago->beneficiario = $dr['beneficiario'];
			$ordenDePago->mailEnviado = $dr['mail_enviado'];
			$ordenDePago->idAsientoContable = $dr['cod_asiento_contable'];
			$ordenDePago->retieneGanancias = $dr['retiene_ganancias'];
			$ordenDePago->observaciones = $dr['observaciones'];
			$ordenDePago->idUsuario = $dr['cod_usuario'];
			$ordenDePago->idUsuarioBaja = $dr['cod_usuario_baja'];
			$ordenDePago->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$ordenDePago->anulado = $dr['anulado'];
			$ordenDePago->fecha = Funciones::formatearFecha($dr['fecha_documento'], 'd/m/Y');
			$ordenDePago->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$ordenDePago->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$ordenDePago->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $ordenDePago;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPais($dr, Pais $pais) {
		try {
			$pais->id = $dr['COD_PAIS'];
			$pais->anulado = $dr['anulado'];
			$pais->nombre = $dr['DENOM_PAIS'];
			return $pais;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillParametro($dr, Parametro $parametro) {
		try {
			$parametro->id = $dr['id'];
			$parametro->valor = $dr['valor'];
			$parametro->descripcion = $dr['descripcion'];
			return $parametro;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillParametroContabilidad($dr, ParametroContabilidad $parametroContabilidad) {
		try {
			$parametroContabilidad->id = $dr['id'];
			$parametroContabilidad->idImputacion = $dr['cod_imputacion'];
			$parametroContabilidad->observaciones = $dr['observaciones'];
			$parametroContabilidad->anulado = $dr['anulado'];
			$parametroContabilidad->idUsuario = $dr['cod_usuario'];
			$parametroContabilidad->idUsuarioBaja = $dr['cod_usuario_baja'];
			$parametroContabilidad->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$parametroContabilidad->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$parametroContabilidad->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$parametroContabilidad->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $parametroContabilidad;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPatron($dr, Patron $patron) {
		try {
			$patron->idArticulo = $dr['cod_articulo'];
			$patron->idColorPorArticulo = $dr['cod_color_articulo'];
			$patron->version = $dr['version'];
			$patron->tipoPatron = $dr['tipo_patron'];
			$patron->fecha = $dr['fecha'];
			$patron->confirmado = $dr['confirmado'];
			$patron->versionActual = $dr['version_actual'];
			$patron->borrador = $dr['borrador'];
			$patron->idHorma = trim($dr['cod_horma']);
			$patron->disenio = Funciones::keyIsSet($dr, 'diseño');
			$patron->borradorViejo = $dr['borrador_viejo'];
			$patron->costo = Funciones::keyIsSet($dr, 'costo');
			return $patron;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPatronItem($dr, PatronItem $patronItem) {
		try {
			$patronItem->idArticulo = $dr['cod_articulo'];
			$patronItem->idColorPorArticulo = $dr['cod_color_articulo'];
			$patronItem->version = $dr['version'];
			$patronItem->numeroDeItem = $dr['nro_item'];
			$patronItem->idMaterial = $dr['cod_material'];
			$patronItem->idColorMateriaPrima = $dr['cod_color_material'];
			$patronItem->idSeccion = $dr['cod_seccion'];
			$patronItem->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$patronItem->itemNuevo = $dr['item_nuevo'];
			$patronItem->consumoPar = Funciones::toFloat(Funciones::formatearDecimales($dr['consumo_par'], 4));
			$patronItem->consumoBatch = Funciones::toFloat(Funciones::formatearDecimales($dr['consumo_batch'], 4));
			$patronItem->idConjunto = trim($dr['conjunto']);
			$patronItem->varia = $dr['varia'];
			$patronItem->escalado = $dr['escalado'];
			$patronItem->escalaDesplazada = $dr['escala_desplaza'];
			$patronItem->tipoPatron = $dr['tipo_patron'];
			$patronItem->trazable = $dr['trazable'];
			$patronItem->asignadoLote = $dr['asignado_lote'];
			$patronItem->cantEntregada = $dr['cant_entregada'];
			$patronItem->entregado = $dr['entregado'];
			return $patronItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPedido($dr, Pedido $pedido) {
		try {
			$pedido->empresa = $dr['empresa'];
			$pedido->numero = $dr['nro_pedido'];
			$pedido->anulado = $dr['anulado'];
			$pedido->aprobado = $dr['aprobado'];
			$pedido->idCliente = $dr['cod_cliente'];
			$pedido->idSucursal = $dr['cod_sucursal'];
			$pedido->idUsuario = $dr['cod_usuario'];
			$pedido->idVendedor = $dr['cod_vendedor'];
			$pedido->idAlmacen = $dr['cod_almacen'];
			$pedido->precioAlFacturar = $dr['precio_al_facturar'];
			$pedido->descuento = $dr['descuento_pedido'];
			$pedido->recargo = $dr['recargo_pedido'];
			$pedido->importeTotal = $dr['importe_total'];
			$pedido->idFormaDePago = $dr['cod_forma_pago'];
			$pedido->idTemporada = $dr['cod_temporada'];
			$pedido->idEcommerceOrder = $dr['cod_ecommerce_order'];
			$pedido->observaciones = $dr['observaciones'];
			$pedido->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$pedido->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$pedido->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $pedido;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPedidoItem($dr, PedidoItem $pedidoItem) {
		try {
			$pedidoItem->empresa = $dr['empresa'];
			$pedidoItem->numero = $dr['nro_pedido'];
			$pedidoItem->numeroDeItem = $dr['nro_item'];
			$pedidoItem->anulado = $dr['anulado'];
			$pedidoItem->idCliente = $dr['cod_cliente'];
			$pedidoItem->idVendedor = $dr['cod_vendedor'];
			$pedidoItem->idAlmacen = $dr['cod_almacen'];
			$pedidoItem->idArticulo = $dr['cod_articulo'];
			$pedidoItem->idColorPorArticulo = $dr['cod_color_articulo'];
			$pedidoItem->precioUnitario = $dr['precio_unitario'];
			for ($i = 1; $i <= 10; $i++) {
				if ($dr['anulado'] == 'N') {
					$pedidoItem->cantidad[$i] = $dr['cant_' . $i];
					$pedidoItem->pendiente[$i] = $dr['pend_' . $i];
					$pedidoItem->predespachados[$i] = $dr['pred_' . $i];
					$pedidoItem->tickeados[$i] = $dr['tick_' . $i];
				} else {
					$pedidoItem->cantidad[$i] = 0;
					$pedidoItem->pendiente[$i] = 0;
					$pedidoItem->predespachados[$i] = 0;
					$pedidoItem->tickeados[$i] = 0;
				}
			}
			$pedidoItem->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$pedidoItem->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$pedidoItem->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $pedidoItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPermisoPorCaja($dr, PermisoPorCaja $permisoPorCaja) {
		try {
			$permisoPorCaja->idCaja = $dr['cod_caja'];
			$permisoPorCaja->idPermiso = $dr['cod_permiso'];
			return $permisoPorCaja;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPermisoPorUsuarioPorCaja($dr, PermisoPorUsuarioPorCaja $permisoPorUsuarioPorCaja) {
		try {
			$permisoPorUsuarioPorCaja->idCaja = $dr['cod_caja'];
			$permisoPorUsuarioPorCaja->idUsuario = $dr['cod_usuario'];
			$permisoPorUsuarioPorCaja->idPermiso = $dr['cod_permiso'];
			return $permisoPorUsuarioPorCaja;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPersonaGasto($dr, PersonaGasto $personaGasto) {
		try {
			$personaGasto->id = $dr['id'];
			$personaGasto->nombre = $dr['nombre'];
			$personaGasto->idUsuario = $dr['cod_usuario'];
			$personaGasto->idUsuarioBaja = $dr['cod_usuario_baja'];
			$personaGasto->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$personaGasto->anulado = $dr['anulado'];
			$personaGasto->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$personaGasto->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$personaGasto->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $personaGasto;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPersonal($dr, Personal $personal) {
		try {
			$personal->idPersonal = $dr['cod_personal'];
			$personal->anulado = $dr['anulado'];
			$personal->apellido = $dr['apellido'];
			$personal->cuil = $dr['cuil'];
			$personal->direccionCalle = $dr['calle'];
			$personal->direccionCodigoPostal = $dr['cod_postal'];
			$personal->direccionDepartamento = $dr['departamento'];
			$personal->idDireccionLocalidad = $dr['cod_localidad_nro'];
			$personal->direccionNumero = $dr['numero'];
			$personal->idDireccionPais = $dr['cod_pais'];
			$personal->direccionPartidoDepartamento = $dr['partido_departamento'];
			$personal->direccionPiso = $dr['piso'];
			$personal->idDireccionProvincia = $dr['provincia'];
			$personal->dni = $dr['doc_identidad_nro'];
			$personal->email = $dr['e_mail'];
			$personal->idFajaHoraria = $dr['cod_faja_horaria'];
			$personal->fechaAntiguedadGremio = Funciones::formatearFecha($dr['fecha_antiguedad_gremio'], 'd/m/Y');
			$personal->fechaEgreso = Funciones::formatearFecha($dr['fecha_egreso'], 'd/m/Y');
			$personal->fechaIngreso = Funciones::formatearFecha($dr['fecha_ingreso'], 'd/m/Y');
			$personal->fechaNacimiento = Funciones::formatearFecha($dr['fecha_nacimiento'], 'd/m/Y');
			$personal->foto = $dr['fotografia'];
			$personal->legajo = $dr['legajo_nro'];
			$personal->modalidadRetribucion = $dr['retribucion_modalidad'];
			$personal->nombre = $dr['nombres'];
			//$personal->objetivo1 = $dr['objetivo_1'];
			//$personal->objetivo2 = $dr['objetivo_2'];
			//$personal->objetivo3 = $dr['objetivo_3'];
			//$personal->obraSocial = $dr['obra_social'];
			//$personal->premio1 = $dr['premio_1'];
			//$personal->premio2 = $dr['premio_2'];
			//$personal->premio3 = $dr['premio_3'];
			$personal->idSeccionProduccion = $dr['seccion'];
			//$personal->situacion = $dr['situacion'];
			$personal->telefono = $dr['tel_domicilio'];
			$personal->celular = $dr['tel_celular'];
			$personal->valorHora = $dr['valor_hora'];
			//$personal->valorHora1 = $dr['valor_hora_1'];
			//$personal->valorHoraMerienda = $dr['valor_hora_merienda'];
			$personal->valorMes = $dr['valor_mes'];
			//$personal->valorMes1 = $dr['valor_mes_1'];
			//$personal->valorPares = $dr['valor_pares'];
			$personal->valorQuincena = $dr['valor_quincena'];
			$personal->ficha = $dr['ficha'];
			return $personal;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPersonalOperador($dr, PersonalOperador $personalOperador) {
		try {
			$personalOperador->id = $dr['cod_operador'];
			$personalOperador = $this->fillOperador($dr, $personalOperador);
			return $personalOperador;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillLoteDeProduccion($dr, LoteDeProduccion $loteDeProduccion) {
		try {
			$loteDeProduccion->id = $dr['nro_plan'];
			$loteDeProduccion->nombre = $dr['denom_plan'];
			$loteDeProduccion->idForecast = $dr['id_forecast'];
			$loteDeProduccion->anulado = $dr['anulado'];
			$loteDeProduccion->fechaAlta = Funciones::formatearFecha($dr['fecha_carga'], 'd/m/Y');
			$loteDeProduccion->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			return $loteDeProduccion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPredespacho($dr, Predespacho $predespacho) {
		try {
			$predespacho->empresa = $dr['empresa'];
			$predespacho->pedidoNumero = $dr['nro_pedido'];
			$predespacho->pedidoNumeroDeItem = $dr['nro_item'];
			$predespacho->idCliente = $dr['cod_cliente'];
			$predespacho->idAlmacen = $dr['cod_almacen'];
			$predespacho->idArticulo = $dr['cod_articulo'];
			$predespacho->idColorPorArticulo = $dr['cod_color_articulo'];
			for ($i = 1; $i <= 10; $i++)
				$predespacho->predespachados[$i] = $dr['pred_' . $i];
			for ($i = 1; $i <= 10; $i++)
				$predespacho->tickeados[$i] = $dr['tick_' . $i];
			$predespacho->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$predespacho->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');;
			return $predespacho;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPrestamo($dr, Prestamo $prestamo) {
		try {
			$prestamo->numero = $dr['nro_prestamo'];
			$prestamo->empresa = $dr['empresa'];
			$prestamo->idImportePorOperacion = $dr['cod_importe_operacion'];
			$prestamo->idCuentaBancaria = $dr['cod_cuenta_bancaria'];
			$prestamo->importeTotal = Funciones::formatearDecimales($dr['importe_total'], 2, '.');
			$prestamo->importePendiente = Funciones::formatearDecimales($dr['importe_pendiente'], 2, '.');
			$prestamo->idAsientoContable = $dr['cod_asiento_contable'];
			$prestamo->observaciones = $dr['observaciones'];
			$prestamo->idUsuario = $dr['cod_usuario'];
			$prestamo->idUsuarioBaja = $dr['cod_usuario_baja'];
			$prestamo->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$prestamo->anulado = $dr['anulado'];
			$prestamo->fecha = Funciones::formatearFecha($dr['fecha_documento'], 'd/m/Y');
			$prestamo->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$prestamo->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$prestamo->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $prestamo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPresupuesto($dr, Presupuesto $presupuesto) {
		try {
			$presupuesto->id = $dr['cod_presupuesto'];
			$presupuesto->idProveedor = $dr['cod_proveedor'];
			$presupuesto->productivo = $dr['productiva'];
			$presupuesto->modalidadCreacion = $dr['modalidad_creacion'];
			$presupuesto->idLoteDeProduccion = $dr['nro_lote'];
			$presupuesto->observaciones = $dr['observaciones'];
			$presupuesto->anulado = $dr['anulado'];
			$presupuesto->idUsuario = $dr['cod_usuario'];
			$presupuesto->idUsuarioBaja = $dr['cod_usuario_baja'];
			$presupuesto->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$presupuesto->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$presupuesto->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$presupuesto->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $presupuesto;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPresupuestoItem($dr, PresupuestoItem $presupuestoItem) {
		try {
			$presupuestoItem->idPresupuesto = $dr['cod_presupuesto'];
			$presupuestoItem->numeroDeItem = $dr['nro_item'];
			$presupuestoItem->idMaterial = $dr['cod_material'];
			$presupuestoItem->idColorMaterial = $dr['cod_color'];
			$presupuestoItem->fechaEntrega = Funciones::formatearFecha($dr['fecha_entrega'], 'd/m/Y');
			$presupuestoItem->cantidad = $dr['cantidad'];
			for($i = 1; $i < 11; $i++){
				$presupuestoItem->cantidades[$i] = $dr['cant_' . $i];
			}
			$presupuestoItem->saciado = $dr['saciado'];
			$presupuestoItem->anulado = $dr['anulado'];
			$presupuestoItem->idUsuario = $dr['cod_usuario'];
			$presupuestoItem->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$presupuestoItem->idUsuarioBaja = $dr['cod_usuario_baja'];
			$presupuestoItem->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$presupuestoItem->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$presupuestoItem->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $presupuestoItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillPresupuestoOrdenCompra($dr, PresupuestoOrdenCompra $presupuestoOrdenCompra) {
		try {
			$presupuestoOrdenCompra->id = $dr['cod_presupuesto_orden_compra'];
			$presupuestoOrdenCompra->idPresupuesto = $dr['cod_presupuesto'];
			$presupuestoOrdenCompra->numeroDeItemPresupuesto = $dr['nro_item_presupuesto'];
			$presupuestoOrdenCompra->idOrdenDeCompra = $dr['cod_orden_de_compra'];
			$presupuestoOrdenCompra->numeroDeItemOrdenDeCompra = $dr['nro_item_orden_de_compra'];

			return $presupuestoOrdenCompra;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillProveedor($dr, Proveedor $proveedor) {
		try {
			$proveedor->id = $dr['cod_prov'];
			$proveedor->anulado = $dr['anulado'];
			$proveedor->autorizado = $dr['autorizado'];
			$proveedor->conceptoRetenGanancias = $dr['concepto_reten_ganancias'];
			$proveedor->idCondicionIva = $dr['condicion_iva'];
			$proveedor->cuentaAcumuladora = $dr['cuenta_acumuladora'];
			$proveedor->cuit = $dr['cuit'];
			$proveedor->nombre = $dr['denom_fantasia'];
			$proveedor->denominacionCuentaAcumuladora = $dr['denominacion_cta_acum'];
			$proveedor->direccionCalle = $dr['calle'];
			$proveedor->direccionCodigoPostal = $dr['cod_postal'];
			$proveedor->direccionDepartamento = $dr['oficina_depto'];
			$proveedor->idDireccionLocalidad = $dr['cod_localidad_nro'];
			$proveedor->direccionNumero = $dr['numero'];
			$proveedor->idDireccionPais = $dr['pais'];
			$proveedor->direccionPartidoDepartamento = $dr['partido_departamento'];
			$proveedor->direccionPiso = $dr['piso'];
			$proveedor->idDireccionProvincia = $dr['provincia'];
			$proveedor->email = $dr['e_mail'];
			$proveedor->fax = $dr['fax'];
			$proveedor->horariosAtencion = $dr['horarios_atencion'];
			$proveedor->importeAcumuladoMes = $dr['importe_acumulado_mes'];
			$proveedor->importeRetenidoMes = $dr['importe_retenido_mes'];
			$proveedor->imputacionEnCompra = $dr['imputacion_en_compra'];
			$proveedor->idImputacionGeneral = $dr['imputacion_general'];
			$proveedor->idImputacionEspecifica = $dr['imputacion_especifica'];
			$proveedor->idImputacionHaber = $dr['cod_imputacion_haber'];
			$proveedor->jurisdiccion1IngresosBrutos = $dr['jurisd_1_ingr_brutos'];
			$proveedor->jurisdiccion2IngresosBrutos = $dr['jurisd_2_ingr_brutos'];
			$proveedor->limiteCredito = $dr['limite_credito'];
			$proveedor->observaciones = $dr['observaciones'];
			$proveedor->paginaWeb = $dr['pagina_web'];
			$proveedor->plazoPago = $dr['plazo_pago'];
			$proveedor->plazoPagoPrimeraEntrega = $dr['plazo_pago_primera_entrega'];
			$proveedor->razonSocial = $dr['razon_social'];
			$proveedor->retencionEspecial = $dr['retencion_especial'];
			$proveedor->retenerImpuestoGanancias = $dr['retener_imp_ganancias'];
			$proveedor->retenerIngresosBrutos = $dr['retener_ingr_brutos'];
			$proveedor->retenerIva = $dr['retener_iva'];
			$proveedor->rubroPalabra = $dr['rubro'];
			$proveedor->telefono1 = $dr['telefono_1'];
			$proveedor->telefono2 = $dr['telefono_2'];
			$proveedor->idTipoProveedor = $dr['tipo_proveedor'];
			$proveedor->idTransporte = $dr['cod_transporte'];
			$proveedor->vencimiento = $dr['primera_entrega'];
			$proveedor->saldo = $dr['saldo'];
			$proveedor->saldo1 = $dr['saldo_1'];
			$proveedor->saldo2 = $dr['saldo_2'];
			return $proveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillProveedorMateriaPrima($dr, ProveedorMateriaPrima $proveedorMateriaPrima) {
		try {
			$proveedorMateriaPrima->idProveedor = $dr['cod_proveedor'];
			$proveedorMateriaPrima->idMaterial = $dr['cod_material'];
			$proveedorMateriaPrima->idColor = $dr['cod_color'];
			$proveedorMateriaPrima->preferente = $dr['preferente'];
			$proveedorMateriaPrima->precioCompra = Funciones::toFloat($dr['precio_compra'], 4);
			$proveedorMateriaPrima->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$proveedorMateriaPrima->codigoInterno = $dr['codigo_interno_proveedor'];
			$proveedorMateriaPrima->anulado = $dr['anulado'];
			$proveedorMateriaPrima->fechaBaja = Funciones::formatearFecha($dr['fecha_anulacion'], 'd/m/Y');
			return $proveedorMateriaPrima;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillProveedorTodos($dr, ProveedorTodos $proveedorTodos) {
		try {
			$proveedorTodos = $this->fillProveedor($dr, $proveedorTodos);
			return $proveedorTodos;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillProvincia($dr, Provincia $provincia) {
		try {
			$provincia->id = $dr['cod_provincia'];
			$provincia->idPais = $dr['COD_PAIS'];
			$provincia->anulado = $dr['anulado'];
			$provincia->nombre = $dr['DENOM_provincia'];
			return $provincia;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRangoTalle($dr, RangoTalle $rangoTalle) {
		try {
			$rangoTalle->id = $dr['cod_rango_nro'];
			$rangoTalle->anulado = $dr['anulado'];
			$rangoTalle->fechaAlta = Funciones::formatearFecha($dr['fechaAlta'], 'd/m/Y');
			$rangoTalle->fechaBaja = Funciones::formatearFecha($dr['fechaBaja'], 'd/m/Y');
			$rangoTalle->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_modificacion'], 'd/m/Y');
			$rangoTalle->nombre = $dr['denom_rango'];
			for ($i = 1; $i <= 10; $i++)
				$rangoTalle->posicion[$i] = $dr['posic_' . $i];
			$rangoTalle->punto = $dr['punto'];
			return $rangoTalle;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRechazoCheque($dr, RechazoCheque $rechazoCheque) {
		try {
			$rechazoCheque->numero = $dr['cod_rechazo_cheque'];
			$rechazoCheque->empresa = $dr['empresa'];
			$rechazoCheque->idImportePorOperacion = $dr['cod_importe_operacion'];
			$rechazoCheque->importeTotal = $dr['importe_total'];
			$rechazoCheque->entradaSalida = $dr['entrada_salida'];
			return $rechazoCheque;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRechazoChequeCabecera($dr, RechazoChequeCabecera $rechazoChequeCabecera) {
		try {
			$rechazoChequeCabecera->numero = $dr['cod_rechazo_cheque'];
			$rechazoChequeCabecera->empresa = $dr['empresa'];
			$rechazoChequeCabecera->observaciones = $dr['observaciones'];
			$rechazoChequeCabecera->motivo = $dr['motivo'];
			$rechazoChequeCabecera->idUsuario = $dr['cod_usuario'];
			$rechazoChequeCabecera->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			return $rechazoChequeCabecera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRecibo($dr, Recibo $recibo) {
		try {
			$recibo->numero = $dr['nro_recibo'];
			$recibo->empresa = $dr['empresa'];
			$recibo->idImportePorOperacion = $dr['cod_importe_operacion'];
			$recibo->idCliente = $dr['cod_cliente'];
			$recibo->tipoOperacion = $dr['operacion_tipo'];
			$recibo->idImputacion = $dr['imputacion'];
			$recibo->importeTotal = Funciones::formatearDecimales($dr['importe_total'], 2, '.');
			$recibo->importePendiente = Funciones::formatearDecimales($dr['importe_pendiente'], 2, '.');
			$recibo->recibidoDe = $dr['recibido_de'];
			$recibo->mailEnviado = $dr['mail_enviado'];
			$recibo->idAsientoContable = $dr['cod_asiento_contable'];
			$recibo->fechaPonderadaPago = Funciones::formatearFecha($dr['fecha_ponderada_pago'], 'd/m/Y');
			$recibo->idEcommerceOrder = $dr['cod_ecommerce_order'];
			$recibo->numeroReciboProvisorio = $dr['numero_recibo_provisorio'];
			$recibo->observaciones = $dr['observaciones'];
			$recibo->idUsuario = $dr['cod_usuario'];
			$recibo->idUsuarioBaja = $dr['cod_usuario_baja'];
			$recibo->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$recibo->anulado = $dr['anulado'];
			$recibo->fecha = Funciones::formatearFecha($dr['fecha_documento'], 'd/m/Y');
			$recibo->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$recibo->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$recibo->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $recibo;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillReingresoChequeCartera($dr, ReingresoChequeCartera $reingresoChequeCartera) {
		try {
			$reingresoChequeCartera->numero = $dr['nro_recibo'];
			$reingresoChequeCartera->empresa = $dr['empresa'];
			$reingresoChequeCartera->idImportePorOperacion = $dr['cod_importe_operacion'];
			$reingresoChequeCartera->importeTotal = Funciones::formatearDecimales($dr['importe_total'], 2, '.');
			$reingresoChequeCartera->idAsientoContable = $dr['cod_asiento_contable'];
			$reingresoChequeCartera->observaciones = $dr['observaciones'];
			$reingresoChequeCartera->idUsuario = $dr['cod_usuario'];
			$reingresoChequeCartera->idUsuarioBaja = $dr['cod_usuario_baja'];
			$reingresoChequeCartera->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$reingresoChequeCartera->anulado = $dr['anulado'];
			$reingresoChequeCartera->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$reingresoChequeCartera->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$reingresoChequeCartera->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $reingresoChequeCartera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRemito($dr, Remito $remito) {
		try {
			$remito->empresa = $dr['empresa'];
			$remito->numero = $dr['nro_remito'];
			$remito->letra = $dr['letra'];
			$remito->anulado = $dr['anulado'];
			$remito->idCliente = $dr['cod_cliente'];
			$remito->idSucursal = $dr['cod_sucursal'];
			$remito->idUsuario = $dr['cod_usuario'];
			$remito->idUsuarioBaja = $dr['cod_usuario_baja'];
			$remito->cantidadBultos = $dr['cantidad_bultos'];
			//$remito->cantidadPares = $dr['cantidad_pares']; Lo hago solo por getter ahora
			$remito->fecha = Funciones::formatearFecha($dr['fecha_remito'], 'd/m/Y');
			$remito->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$remito->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$remito->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			$remito->facturaPuntoDeVenta = $dr['punto_venta_factura'];
			$remito->facturaTipoDocumento = $dr['tipo_docum_factura'];
			$remito->facturaLetra = $dr['letra_factura'];
			$remito->facturaNumero = $dr['nro_factura'];
			$remito->importe = $dr['importe_total'];
			$remito->idEcommerceOrder = $dr['cod_ecommerce_order'];
			$remito->observaciones = $dr['observaciones'];
			return $remito;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRemitoPorOrdenDeCompra($dr, RemitoPorOrdenDeCompra $remitoPorOrdenDeCompra) {
		try {
			$remitoPorOrdenDeCompra->id = $dr['cod_remito_orden_de_compra'];
			$remitoPorOrdenDeCompra->idRemitoProveedor = $dr['cod_remito_proveedor'];
			$remitoPorOrdenDeCompra->numeroDeItemRemitoProveedor = $dr['nro_item_remito_proveedor'];
			$remitoPorOrdenDeCompra->idOrdenDeCompra = $dr['cod_orden_de_compra'];
			$remitoPorOrdenDeCompra->numeroDeItemOrdenDeCompra = $dr['nro_item_orden_de_compra'];
			$remitoPorOrdenDeCompra->cantidadOc = Funciones::formatearDecimales($dr['cantidad_oc'], 4, '.');
			$remitoPorOrdenDeCompra->cantidad = Funciones::formatearDecimales($dr['cantidad'], 4, '.');
			$remitoPorOrdenDeCompra->cantidadPendiente = Funciones::formatearDecimales($dr['cantidad_pendiente'], 4, '.');
			for($i = 1; $i < 16; $i++)
				$remitoPorOrdenDeCompra->cantidadesOc[$i] = $dr['cant_oc_' . $i];
			for($i = 1; $i < 16; $i++)
				$remitoPorOrdenDeCompra->cantidades[$i] = $dr['cant_' . $i];
			for($i = 1; $i < 16; $i++)
				$remitoPorOrdenDeCompra->cantidadesPendientes[$i] = $dr['cant_p_' . $i];
			return $remitoPorOrdenDeCompra;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRemitoProveedor($dr, RemitoProveedor $remitoProveedor) {
		try {
			$remitoProveedor->id = $dr['cod_remito_proveedor'];
			$remitoProveedor->idFacturaProveedor = $dr['cod_documento_proveedor'];
			$remitoProveedor->idProveedor = $dr['cod_proveedor'];
			$remitoProveedor->numero = $dr['nro_remito'];
			$remitoProveedor->sucursal = $dr['sucursal'];
			$remitoProveedor->letra = $dr['letra'];
			$remitoProveedor->fechaRecepcion = Funciones::formatearFecha($dr['fecha_recepcion'], 'd/m/Y');
			$remitoProveedor->idAlmacen = $dr['cod_almacen_recepcion'];
			$remitoProveedor->conOrdenDeCompra = $dr['con_orden_compra'];
			$remitoProveedor->esHexagono = $dr['es_hexagono'];
			return $remitoProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRemitoProveedorItem($dr, RemitoProveedorItem $remitoProveedorItem) {
		try {
			$remitoProveedorItem->idRemitoProveedor = $dr['cod_remito_proveedor'];
			$remitoProveedorItem->idMaterial = $dr['cod_material'];
			$remitoProveedorItem->numeroDeItem = $dr['nro_item'];
			$remitoProveedorItem->idColorMaterial = $dr['cod_color'];
			$remitoProveedorItem->cantidad = $dr['cantidad'];
			for($i = 1; $i < 16; $i++)
				$remitoProveedorItem->cantidades[$i] = $dr['cant_' . $i];
			$remitoProveedorItem->fueraDeOrden = $dr['fuera_de_orden'];
			$remitoProveedorItem->embalaje = $dr['embalaje'];
			return $remitoProveedorItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRendicionGastos($dr, RendicionGastos $rendicionGastos) {
		try {
			$rendicionGastos->numero = $dr['cod_rendicion_gastos'];
			$rendicionGastos->empresa = $dr['empresa'];
			$rendicionGastos->idImportePorOperacion = $dr['cod_importe_operacion'];
			$rendicionGastos->importeTotal = Funciones::formatearDecimales($dr['importe_total'], 2, '.');
			$rendicionGastos->importePendiente = Funciones::formatearDecimales($dr['importe_pendiente'], 2, '.');
			$rendicionGastos->idAsientoContable = $dr['cod_asiento_contable'];
			$rendicionGastos->observaciones = $dr['observaciones'];
			$rendicionGastos->idUsuario = $dr['cod_usuario'];
			$rendicionGastos->idUsuarioBaja = $dr['cod_usuario_baja'];
			$rendicionGastos->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$rendicionGastos->anulado = $dr['anulado'];
			$rendicionGastos->fecha = Funciones::formatearFecha($dr['fecha_documento'], 'd/m/Y');
			$rendicionGastos->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$rendicionGastos->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$rendicionGastos->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $rendicionGastos;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRetencion($dr, Retencion $retencion) {
		try {
			$retencion->id = $dr['cod_retencion'];
			$retencion->empresa = $dr['empresa'];
			$retencion->idTipoRetencion = $dr['cod_tipo_retencion'];
			$retencion->nombre = $dr['nombre'];
			$retencion->numeroCertificado = $dr['numero_certificado'];
			$retencion->cuit = $dr['cuit'];
			$retencion->importe = $dr['importe'];
			$retencion->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$retencion->declarada = $dr['declarada'];
			$retencion->anulado = $dr['anulado'];
			$retencion->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$retencion->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$retencion->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $retencion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRetencionEfectuada($dr, RetencionEfectuada $retencionEfectuada) {
		try {
			$retencionEfectuada->idProveedor = $dr['cod_proveedor'];
			$retencionEfectuada->importeNeto = $dr['importe_neto'];
			$retencionEfectuada = $this->fillRetencion($dr, $retencionEfectuada);
			return $retencionEfectuada;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRetencionEscala($dr, RetencionEscala $retencionEscala) {
		try {
			$retencionEscala->ano = $dr['ano'];
			$retencionEscala->mes = $dr['mes_num'];
			$retencionEscala->item = $dr['tramo_escala'];
			$retencionEscala->comienzo = $dr['comienzo_escala'];
			$retencionEscala->final = $dr['final_escala'];
			$retencionEscala->fijo = $dr['fijo'];
			$retencionEscala->masPorcentaje = $dr['mas_porcentaje'];
			$retencionEscala->sobreExcedente = $dr['sobre_excedente_escala'];
			return $retencionEscala;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRetencionSufrida($dr, RetencionSufrida $retencionSufrida) {
		try {
			$retencionSufrida->idCliente = $dr['cod_cliente'];
			$retencionSufrida = $this->fillRetencion($dr, $retencionSufrida);
			return $retencionSufrida;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRetencionTabla($dr, RetencionTabla $retencionTabla) {
		try {
			$retencionTabla->ano = $dr['ano'];
			$retencionTabla->mes = $dr['mes_num'];
			$retencionTabla->item = $dr['item_concepto'];
			$retencionTabla->concepto = $dr['concepto'];
			$retencionTabla->escalaDirecto = $dr['escala_o_directo'];
			$retencionTabla->baseImponible = $dr['monto_no_sujeto'];
			$retencionTabla->inscriptoAlicuota = $dr['inscripto_alicuota'];
			$retencionTabla->noInscriptoAlicuota = $dr['no_inscripto_alicuota'];
			$retencionTabla->noCorrespondeMenor = $dr['no_corresponde_menor'];
			return $retencionTabla;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRetiroSocio($dr, RetiroSocio $retiroSocio) {
		try {
			$retiroSocio->numero = $dr['nro_retiro_socio'];
			$retiroSocio->empresa = $dr['empresa'];
			$retiroSocio->idImportePorOperacion = $dr['cod_importe_operacion'];
			$retiroSocio->idSocio = $dr['cod_socio'];
			$retiroSocio->concepto = $dr['concepto'];
			$retiroSocio->importeTotal = $dr['importe_total'];
			$retiroSocio->idAsientoContable = $dr['cod_asiento_contable'];
			$retiroSocio->observaciones = $dr['observaciones'];
			$retiroSocio->idUsuario = $dr['cod_usuario'];
			$retiroSocio->idUsuarioBaja = $dr['cod_usuario_baja'];
			$retiroSocio->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$retiroSocio->anulado = $dr['anulado'];
			$retiroSocio->fecha = Funciones::formatearFecha($dr['fecha_documento'], 'd/m/Y');
			$retiroSocio->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$retiroSocio->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$retiroSocio->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $retiroSocio;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRol($dr, Rol $rol) {
		try {
			$rol->id = $dr['cod_rol'];
			$rol->anulado = $dr['anulado'];
			$rol->nombre = $dr['nombre'];
			$rol->tipo = $dr['tipo'];
			return $rol;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRolPorTipoNotificacion($dr, RolPorTipoNotificacion $rolPorTipoNotificacion) {
		try {
			$rolPorTipoNotificacion->eliminable = $dr['eliminable'];
			$rolPorTipoNotificacion->idTipoNotificacion = $dr['cod_tipo_notificacion'];
			$rolPorTipoNotificacion = $this->fillRol($dr, $rolPorTipoNotificacion);
			return $rolPorTipoNotificacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRolPorUsuario($dr, RolPorUsuario $rolPorUsuario) {
		try {
			$rolPorUsuario->idUsuario = $dr['cod_usuario'];
			$rolPorUsuario = $this->fillRol($dr, $rolPorUsuario);
			return $rolPorUsuario;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRubro($dr, Rubro $rubro) {
		try {
			$rubro->id = $dr['cod_grupo'];
			$rubro->nombre = $dr['denom_grupo'];
			return $rubro;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRubroIva($dr, RubroIva $rubroIva) {
		try {
			$rubroIva->id = $dr['cod_rubro_iva'];
			$rubroIva->nombre = $dr['nombre'];
			$rubroIva->anulado = $dr['anulado'];
			$rubroIva->columnaIva = $dr['columna_iva'];
			return $rubroIva;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRutaProduccion($dr, RutaProduccion $rutaProduccion) {
		try {
			$rutaProduccion->id = $dr['cod_ruta'];
			$rutaProduccion->anulado = $dr['anulado'];
			$rutaProduccion->fechaAlta = Funciones::formatearFecha($dr['fechaAlta'], 'd/m/Y');
			$rutaProduccion->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$rutaProduccion->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_modificacion'], 'd/m/Y');
			$rutaProduccion->nombre = $dr['denom_ruta'];
			return $rutaProduccion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillRutaProduccionPaso($dr, RutaProduccionPaso $rutaProduccionPaso) {
		try {
			$rutaProduccionPaso->idRutaProduccion = $dr['cod_ruta'];
			$rutaProduccionPaso->nroPaso = $dr['cod_paso'];
			$rutaProduccionPaso->nroSubPaso = $dr['sub_paso'];
			$rutaProduccionPaso->anulado = $dr['anulado'];
			$rutaProduccionPaso->imprimirOrdenF2 = $dr['imprimir_orden_f2'];
			$rutaProduccionPaso->jerarquiaSeccion = $dr['jerarquia_seccion'];
			$rutaProduccionPaso->ejecucion = $dr['ejecucion'];
			$rutaProduccionPaso->duracion = $dr['duracion'];
			$rutaProduccionPaso->puntoProgramacion = $dr['punto_programacion'];
			$rutaProduccionPaso->fechaAlta = Funciones::formatearFecha($dr['fechaAlta'], 'd/m/Y');
			$rutaProduccionPaso->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$rutaProduccionPaso->fechaUltimaMod = Funciones::formatearFecha($dr['fechaUltimaMod'], 'd/m/Y');
			$rutaProduccionPaso->idSeccionProduccion = $dr['cod_seccion'];
			$rutaProduccionPaso->tieneSubordinadas = $dr['tiene_subordinadas'];
			return $rutaProduccionPaso;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillSeccionProduccion($dr, SeccionProduccion $seccionProduccion) {
		try {
			$seccionProduccion->id = $dr['cod_seccion'];
			$seccionProduccion->anulado = $dr['anulado'];
			$seccionProduccion->color = $dr['color'];
			$seccionProduccion->fechaAlta = Funciones::formatearFecha($dr['fechaAlta'], 'd/m/Y');
			$seccionProduccion->fechaBaja = Funciones::formatearFecha($dr['fechaBaja'], 'd/m/Y');
			$seccionProduccion->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_modificacion'], 'd/m/Y');
			$seccionProduccion->imprimeStickers = $dr['impresion_stickers'];
			$seccionProduccion->ingresaAlStock = $dr['ingresa_al_stock'];
			$seccionProduccion->idAlmacenDefault = $dr['cod_almacen_default'];
			$seccionProduccion->interrumpible = $dr['interrumpible'];
			$seccionProduccion->jerarquiaSeccion = $dr['jerarquia_seccion'];
			$seccionProduccion->nombre = $dr['denom_seccion'];
			$seccionProduccion->nombreCorto = $dr['denom_corta'];
			$seccionProduccion->idSeccionSuperior = $dr['subordinada_de_seccion'];
			$seccionProduccion->idUnidadDeMedida = $dr['unid_med_cap_prod'];
			return $seccionProduccion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillSocio($dr, Socio $socio) {
		try {
			$socio->id = $dr['cod_socio'];
			$socio->observaciones = $dr['observaciones'];
			$socio->cuil = $dr['cuil'];
			$socio->dni = $dr['dni'];
			$socio->direccion->fill($dr);
			$socio->email = $dr['email'];
			$socio->nombre = $dr['nombre'];
			$socio->telefono = $dr['telefono'];
			$socio->celular = $dr['celular'];
			$socio->anulado = $dr['anulado'];
			$socio->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$socio->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$socio->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $socio;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillSolicitudDeFondos($dr, SolicitudDeFondos $solicitudDeFondos) {
		try {
			$solicitudDeFondos->id = $dr['cod_solicitud_de_fondos'];
			$solicitudDeFondos->idCajaSolicitante = $dr['cod_caja_solicitante'];
			$solicitudDeFondos->idCajaSolicitado = $dr['cod_caja_solicitado'];
			$solicitudDeFondos->cerrada = $dr['cerrada'];
			$solicitudDeFondos->aprobada = $dr['aprobada'];
			return $solicitudDeFondos;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillSolicitudDeFondosItem($dr, SolicitudDeFondosItem $solicitudDeFondosItem) {
		try {
			$solicitudDeFondosItem->id = $dr['cod_solicitud_de_fondos'];
			$solicitudDeFondosItem->orden = $dr['orden'];
			$solicitudDeFondosItem->idUsuario = $dr['cod_usuario'];
			$solicitudDeFondosItem->importe = $dr['importe'];
			$solicitudDeFondosItem->motivo = $dr['motivo'];
			$solicitudDeFondosItem->observaciones = $dr['observaciones'];
			$solicitudDeFondosItem->fechaSugerida = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			return $solicitudDeFondosItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillSubdiarioDeIngresosItem($dr, SubdiarioDeIngresosItem $subdiarioDeIngresosItem) {
		try {
			$subdiarioDeIngresosItem->numeroRecibo = $dr['numero'];
			$subdiarioDeIngresosItem->empresa = $dr['empresa'];
			$subdiarioDeIngresosItem->cliente = $dr['de_para'];
			$subdiarioDeIngresosItem->imputacion = $dr['imputacion'];
			$subdiarioDeIngresosItem->efectivo = $dr['efectivo'];
			$subdiarioDeIngresosItem->cheques = $dr['cheques'];
			$subdiarioDeIngresosItem->transferencias = $dr['transferencias'];
			$subdiarioDeIngresosItem->total = $dr['total'];
			$subdiarioDeIngresosItem->idCaja = $dr['cod_caja'];
			$subdiarioDeIngresosItem->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			return $subdiarioDeIngresosItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillStock($dr, Stock $stock) {
		try {
			$stock->idAlmacen = $dr['cod_almacen'];
			$stock->idArticulo = $dr['cod_articulo'];
			$stock->idColorPorArticulo = $dr['cod_color_articulo'];
			for ($i = 1; $i <= 10; $i++)
				$stock->cantidad[$i] = $dr['cant_' . $i];
			return $stock;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillStockMP($dr, StockMP $stockMP) {
		try {
			$stockMP->idAlmacen = $dr['cod_almacen'];
			$stockMP->idMaterial = $dr['cod_material'];
			$stockMP->idColorMateriaPrima = $dr['cod_color'];
			for ($i = 1; $i <= 10; $i++)
				$stockMP->cantidad[$i] = $dr['cant_' . $i];
			return $stockMP;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillSucursal($dr, Sucursal $sucursal) {
		try {
			$sucursal->id = $dr['cod_suc'];
			$sucursal->idCliente = $dr['cod_cli'];
			$sucursal->activo = $dr['activo'];
			$sucursal->anulado = $dr['anulado'];
			$sucursal->celular = $dr['NumCelular'];
			$sucursal->idContacto = $dr['cod_contacto'];
			$sucursal->direccionCalle = $dr['calle'];
			$sucursal->direccionCodigoPostal = $dr['cod_postal'];
			$sucursal->direccionDepartamento = $dr['oficina_depto'];
			$sucursal->idDireccionLocalidad = $dr['cod_localidad_nro'];
			$sucursal->direccionNumero = $dr['numero'];
			$sucursal->idDireccionPais = $dr['cod_pais'];
			$sucursal->direccionPartidoDepartamento = $dr['partido_departamento'];
			$sucursal->direccionPiso = $dr['piso'];
			$sucursal->idDireccionProvincia = $dr['cod_provincia'];
			$sucursal->email = $dr['email'];
			$sucursal->idSucursalEntrega = $dr['cod_sucursal_entrega'];
			$sucursal->esCasaCentral = $dr['casa_central'];
			$sucursal->esPuntoDeVenta = $dr['punto_venta'];
			$sucursal->fax = $dr['fax'];
			$sucursal->horarioAtencion = $dr['horario_atencion'];
			$sucursal->nombre = $dr['denom_sucursal'];
			$sucursal->observaciones = $dr['observaciones'];
			$sucursal->reparto = $dr['reparto'];
			$sucursal->telefono1 = $dr['telefono_1'];
			$sucursal->telefono2 = $dr['telefono_2'];
			$sucursal->idTransporte = $dr['cod_transporte'];
			$sucursal->idVendedor = $dr['cod_vendedor'];
			$sucursal->idZonaTransporte = $dr['cod_zona'];
			$sucursal->direccionLatitud = $dr['latitud'];
			$sucursal->direccionLongitud = $dr['longitud'];
			$sucursal->direccionFormateada = $dr['direccion_formateada'];
			//$sucursal->horarioEntrega1 = $dr['horario_entrega_1'];
			//$sucursal->horarioEntrega2 = $dr['horario_entrega_2'];
			return $sucursal;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillTareaProduccion($dr, TareaProduccion $tareaProduccion) {
		try {
			$tareaProduccion->idOrdenDeFabricacion = $dr['nro_orden_fabricacion'];
			$tareaProduccion->numero = $dr['nro_tarea'];
            $tareaProduccion->situacion = $dr['situacion'];
            $tareaProduccion->tipoTarea = $dr['tipo_tarea'];
			$tareaProduccion->idTareaDeriva = $dr['tarea_deriva'];
			$tareaProduccion->idTareaOriginal = $dr['tarea_original'];
			$tareaProduccion->pasoDeriva = $dr['paso_deriva'];
			$tareaProduccion->ultimoPasoCumplido = $dr['ultimo_paso_cumplido'];
			$tareaProduccion->cantidadModulos = $dr['cantidad_modulos'];
			$tareaProduccion->impresa = $dr['impresa'];
			//$tareaProduccion->paraStock = $dr['para_stock'];
            //$tareaProduccion->cantidadTotal = $dr['cantidad'];
            for ($i = 1; $i < 10; $i++)
                $tareaProduccion->cantidad[$i] = $dr['pos_' . $i . '_cant'];
			$tareaProduccion->observaciones = $dr['observacion'];
			$tareaProduccion->idOperadorEntregado = $dr['operador_entregado'];
			//$tareaProduccion->fallada = $dr['fallada'];
            $tareaProduccion->anulado = $dr['anulado'];
            $tareaProduccion->fechaProgramacion = Funciones::formatearFecha($dr['fecha_programacion'], 'd/m/Y');
			$tareaProduccion->fechaCorte = Funciones::formatearFecha($dr['fecha_corte'], 'd/m/Y');
			$tareaProduccion->fechaAparado = Funciones::formatearFecha($dr['fecha_aparado'], 'd/m/Y');
			$tareaProduccion->fechaArmado = Funciones::formatearFecha($dr['fecha_armado'], 'd/m/Y');
			return $tareaProduccion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillTareaProduccionItem($dr, TareaProduccionItem $tareaProduccionItem) {
		try {
			$tareaProduccionItem->idOrdenDeFabricacion = $dr['nro_orden_fabricacion'];
			$tareaProduccionItem->numeroTarea = $dr['nro_tarea'];
			$tareaProduccionItem->idSeccionProduccion = $dr['cod_seccion'];
			$tareaProduccionItem->idAlmacen = $dr['cod_almacen'];
			$tareaProduccionItem->idArticulo = $dr['cod_articulo'];
			$tareaProduccionItem->idColorPorArticulo = $dr['cod_color_articulo'];
			$tareaProduccionItem->idUnidadProduccion = $dr['cod_unidad_produccion'];
			$tareaProduccionItem->ejecucion = $dr['ejecucion'];
			$tareaProduccionItem->numeroPaso = $dr['nro_paso'];
			$tareaProduccionItem->subPaso = $dr['sub_paso'];
			$tareaProduccionItem->cantidadEntrada = $dr['cantidad_entrada'];
			$tareaProduccionItem->cantidadSalida = $dr['cantidad_salida'];
			$tareaProduccionItem->fechaEntradaProgramada = Funciones::formatearFecha($dr['fecha_entrada_programada'], 'd/m/Y');
			$tareaProduccionItem->fechaEntradaReal = Funciones::formatearFecha($dr['fecha_entrada_real'], 'd/m/Y');
			$tareaProduccionItem->horaEntradaReal = Funciones::formatearFecha($dr['fecha_entrada_real'], 'H:i');
			$tareaProduccionItem->fechaSalidaReal = Funciones::formatearFecha($dr['fecha_salida_real'], 'd/m/Y');
			$tareaProduccionItem->horaSalidaReal = Funciones::formatearFecha($dr['fecha_salida_real'], 'H:i');
			$tareaProduccionItem->idOperador = $dr['cod_operador'];
			$tareaProduccionItem->duracionPaso = $dr['duracion_paso'];
			$tareaProduccionItem->cumplidoPaso = $dr['cumplido_paso'];
			for ($i = 1; $i < 10; $i++)
				$tareaProduccionItem->cantidad[$i] = $dr['cant_' . $i];
			$tareaProduccionItem->entradaConfirmada = $dr['entrada_confirmada'];
			//$tareaProduccionItem->ordenFcGenerada = $dr['orden_fc_generada'];
			$tareaProduccionItem->rendido = $dr['rendido'];
			//$tareaProduccionItem->rendidoMo = $dr['rendido_mo'];
			$tareaProduccionItem->valorAplicable = $dr['valor_aplicable'];
			$tareaProduccionItem->liquidado = $dr['liquidado'];
			$tareaProduccionItem->liquidacionNumero = $dr['liquidacion_nro'];
			$tareaProduccionItem->liquidacionFecha = $dr['liquidacion_fecha'];
			$tareaProduccionItem->pendienteTotal = $dr['pendiente'];
			for ($i = 1; $i < 10; $i++)
				$tareaProduccionItem->pendiente[$i] = $dr['pend_' . $i];
			return $tareaProduccionItem;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillTemporada($dr, Temporada $temporada) {
		try {
			$temporada->id = $dr['cod_tempo'];
			$temporada->nombre = $dr['denom_tempo'];
			$temporada->tipo = $dr['tipo_tempo'];
			return $temporada;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillTipoFactura($dr, TipoFactura $tipoFactura) {
		try {
			$tipoFactura->id = $dr['cod_tipo_factura'];
			$tipoFactura->nombre = $dr['nombre'];
			$tipoFactura->descripcion = $dr['descripcion'];
			$tipoFactura->idUsuario = $dr['cod_usuario'];
			$tipoFactura->idUsuarioBaja = $dr['cod_usuario_baja'];
			$tipoFactura->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$tipoFactura->anulado = $dr['anulado'];
			$tipoFactura->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$tipoFactura->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$tipoFactura->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $tipoFactura;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillTipoNotificacion($dr, TipoNotificacion $tipoNotificacion) {
		try {
			$tipoNotificacion->id = $dr['cod_tipo_notificacion'];
			$tipoNotificacion->accionAnular = $dr['accion_anular'];
			$tipoNotificacion->accionCumplido = $dr['accion_cumplido'];
			$tipoNotificacion->accionNotificacion = $dr['accion_notificacion'];
			$tipoNotificacion->anularAlCumplir = $dr['anular_al_cumplir'];
			$tipoNotificacion->nombre = $dr['nombre'];
			$tipoNotificacion->link = $dr['link'];
			$tipoNotificacion->detalle = $dr['detalle'];
			$tipoNotificacion->imagen = $dr['imagen'];
			$tipoNotificacion->anulado = $dr['anulado'];
			$tipoNotificacion->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$tipoNotificacion->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$tipoNotificacion->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $tipoNotificacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillTipoPeriodoFiscal($dr, TipoPeriodoFiscal $tipoPeriodoFiscal) {
		try {
			$tipoPeriodoFiscal->id = $dr['cod_tipo_periodo'];
			$tipoPeriodoFiscal->nombre = $dr['nombre'];
			$tipoPeriodoFiscal->anulado = $dr['anulado'];
			$tipoPeriodoFiscal->idUsuario = $dr['cod_usuario'];
			$tipoPeriodoFiscal->idUsuarioBaja = $dr['cod_usuario_baja'];
			$tipoPeriodoFiscal->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$tipoPeriodoFiscal->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$tipoPeriodoFiscal->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$tipoPeriodoFiscal->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $tipoPeriodoFiscal;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillTipoProductoStock($dr, TipoProductoStock $tipoProductoStock) {
		try {
			$tipoProductoStock->id = $dr['id_tipo_producto_stock_nro'];
			$tipoProductoStock->nombre = $dr['denom_tipo_producto'];
			$tipoProductoStock->nombreCatalogo = $dr['nombre_catalogo'];
            $tipoProductoStock->mostrarEnCatalogo = $dr['mostrar_en_catalogo'];
            $tipoProductoStock->exclusivoCatalogo = $dr['exclusivo_catalogo'];
            $tipoProductoStock->descuentoPorc = $dr['descuento_porc'];
			return $tipoProductoStock;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillTipoProveedor($dr, TipoProveedor $tipoProveedor) {
		try {
			$tipoProveedor->id = $dr['cod_tipo_proveedor'];
			$tipoProveedor->nombre = $dr['denom_tipo_proveedor'];
			return $tipoProveedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillTipoRetencion($dr, TipoRetencion $tipoRetencion) {
		try {
			$tipoRetencion->id = $dr['cod_tipo_retencion'];
			$tipoRetencion->nombre = $dr['nombre'];
			$tipoRetencion->idImputacion = $dr['cod_imputacion'];
			return $tipoRetencion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillTransporte($dr, Transporte $transporte) {
		try {
			$transporte->id = $dr['cod_transporte_nro'];
			$transporte->anulado = $dr['anulado'];
			$transporte->cuit = $dr['cuit'];
			$transporte->direccionCalle = $dr['direccion'];
			//$transporte->direccionCodigoPostal = $dr[''];
			//$transporte->direccionDepartamento = $dr[''];
			$transporte->idDireccionLocalidad = $dr['cod_localidad_nro'];
			//$transporte->direccionNumero = $dr[''];
			$transporte->idDireccionPais = $dr['cod_pais'];
			//$transporte->direccionPartidoDepartamento = $dr[''];
			//$transporte->direccionPiso = $dr[''];
			$transporte->idDireccionProvincia = $dr['cod_provincia'];
			$transporte->horario = $dr['horario'];
			$transporte->email = $dr['mail'];
			$transporte->nombre = $dr['denom_transporte'];
			$transporte->telefono = $dr['telefono'];
			return $transporte;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillTransferenciaBancariaImporte($dr, TransferenciaBancariaImporte $transfBancariaImporte) {
		try {
			$transfBancariaImporte->id = $dr['cod_transferencia_ban'];
			$transfBancariaImporte->empresa = $dr['empresa'];
			$transfBancariaImporte->importe = $dr['importe'];
			$transfBancariaImporte->numeroTransferenciaBancariaOperacion = $dr['cod_transferencia_ban_op'];
			return $transfBancariaImporte;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillTransferenciaBancariaOperacion($dr, TransferenciaBancariaOperacion $transfBancariaOperacion) {
		try {
			$transfBancariaOperacion->numero = $dr['cod_transferencia_ban'];
			$transfBancariaOperacion->empresa = $dr['empresa'];
			$transfBancariaOperacion->idImportePorOperacion = $dr['cod_importe_operacion'];
			$transfBancariaOperacion->fechaTransferencia = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$transfBancariaOperacion->idCuentaBancaria = $dr['cod_cuenta_bancaria'];
			$transfBancariaOperacion->entradaSalida = $dr['entrada_salida'];
			$transfBancariaOperacion->importeTotal = $dr['importe_total'];
			$transfBancariaOperacion->numeroTransferencia = $dr['numero_transferencia'];
			$transfBancariaOperacion->observaciones = $dr['observaciones'];
			$transfBancariaOperacion->haciaDesde = $dr['hacia_desde'];
			$transfBancariaOperacion->idUsuario = $dr['cod_usuario'];
			$transfBancariaOperacion->idUsuarioBaja = $dr['cod_usuario_baja'];
			$transfBancariaOperacion->anulado = $dr['anulado'];
			$transfBancariaOperacion->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$transfBancariaOperacion->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$transfBancariaOperacion->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $transfBancariaOperacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillTransferenciaInterna($dr, TransferenciaInterna $transferenciaInterna) {
		try {
			$transferenciaInterna->numero = $dr['cod_transferencia_int'];
			$transferenciaInterna->empresa = $dr['empresa'];
			$transferenciaInterna->idImportePorOperacion = $dr['cod_importe_operacion'];
			$transferenciaInterna->importeTotal = $dr['importe_total'];
			$transferenciaInterna->entradaSalida = $dr['entrada_salida'];
			return $transferenciaInterna;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillTransferenciaInternaCabecera($dr, TransferenciaInternaCabecera $transferenciaInternaCabecera) {
		try {
			$transferenciaInternaCabecera->numero = $dr['cod_transferencia_int'];
			$transferenciaInternaCabecera->empresa = $dr['empresa'];
			$transferenciaInternaCabecera->idAsientoContable = $dr['cod_asiento_contable'];
			$transferenciaInternaCabecera->fechaDocumento = Funciones::formatearFecha($dr['fechaDocumento'], 'd/m/Y');
			$transferenciaInternaCabecera->observaciones = $dr['observaciones'];
			$transferenciaInternaCabecera->idUsuario = $dr['cod_usuario'];
			$transferenciaInternaCabecera->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			return $transferenciaInternaCabecera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillUnidadDeMedida($dr, UnidadDeMedida $unidadDeMedida) {
		try {
			$unidadDeMedida->id = $dr['cod_unidad'];
			$unidadDeMedida->nombre = $dr['denom_unidad'];
			return $unidadDeMedida;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillUsuario($dr, Usuario $usuario) {
		try {
			$usuario->id = $dr['cod_usuario'];
			$usuario->tipoPersona = $dr['tipo'];
			$usuario->idPersonal = $dr['cod_personal'];
			$usuario->idContacto = Funciones::keyIsSet($dr, 'cod_contacto');
			$usuario->mensajeHome = Funciones::keyIsSet($dr, 'mensaje_home');
			$usuario->idUsuarioAlta = Funciones::keyIsSet($dr, 'cod_usuario_alta');
			$usuario->idUsuarioBaja = Funciones::keyIsSet($dr, 'cod_usuario_baja');
			$usuario->idUsuarioUltimaMod = Funciones::keyIsSet($dr, 'cod_usuario_ultima_mod');
			$usuario->fechaAlta = Funciones::formatearFecha($dr['fechaAlta'], 'd/m/Y');
			$usuario->fechaBaja = Funciones::formatearFecha($dr['fechaBaja'], 'd/m/Y');
			$usuario->fechaUltimaAct = Funciones::formatearFecha($dr['fechaUltimaAct'], 'd/m/Y');
			$usuario->fechaUltimaMod = Funciones::formatearFecha($dr['fechaUltimaMod'], 'd/m/Y');
			$usuario->anulado = Funciones::keyIsSet($dr, 'anulado');
			return $usuario;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillUsuarioCalzado($dr, UsuarioCalzado $usuarioCalzado) {
		try {
			$usuarioCalzado->id = $dr['cod_usuarios'];
			$usuarioCalzado->nombre = $dr['denom_usuarios'];
			return $usuarioCalzado;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillUsuarioLogin($dr, UsuarioLogin $usuarioLogin) {
		try {
			$usuarioLogin->password = $dr['password'];
			$usuarioLogin = $this->fillUsuario($dr, $usuarioLogin);
			return $usuarioLogin;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillUsuarioPorAlmacen($dr, UsuarioPorAlmacen $usuarioPorAlmacen) {
		try {
			$usuarioPorAlmacen->idAlmacen = $dr['cod_almacen'];
			$usuarioPorAlmacen = $this->fillUsuario($dr, $usuarioPorAlmacen);
			return $usuarioPorAlmacen;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillUsuarioPorAreaEmpresa($dr, UsuarioPorAreaEmpresa $usuarioPorAreaEmpresa) {
		try {
			$usuarioPorAreaEmpresa->idAreaEmpresa = $dr['id_area_empresa'];
			$usuarioPorAreaEmpresa = $this->fillUsuario($dr, $usuarioPorAreaEmpresa);
			return $usuarioPorAreaEmpresa;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillUsuarioPorCaja($dr, UsuarioPorCaja $usuarioPorCaja) {
		try {
			$usuarioPorCaja->idCaja = $dr['cod_caja'];
			$usuarioPorCaja->idUsuario = $dr['cod_usuario'];
			$usuarioPorCaja = $this->fillUsuario($dr, $usuarioPorCaja);
			return $usuarioPorCaja;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
    private function fillUsuarioPorSeccionProduccion($dr, UsuarioPorSeccionProduccion $usuarioPorSeccionProduccion) {
        try {
            $usuarioPorSeccionProduccion->idSeccionProduccion = $dr['cod_seccion'];
            $usuarioPorSeccionProduccion = $this->fillUsuario($dr, $usuarioPorSeccionProduccion);
            return $usuarioPorSeccionProduccion;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
	private function fillUsuarioPorTipoNotificacion($dr, UsuarioPorTipoNotificacion $usuarioPorTipoNotificacion) {
		try {
			$usuarioPorTipoNotificacion->eliminable = $dr['eliminable'];
			$usuarioPorTipoNotificacion->idTipoNotificacion = $dr['cod_tipo_notificacion'];
			$usuarioPorTipoNotificacion = $this->fillUsuario($dr, $usuarioPorTipoNotificacion);
			return $usuarioPorTipoNotificacion;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillVendedor($dr, Vendedor $vendedor) {
		try {
			$vendedor->id = $dr['cod_operador'];
			$vendedor = $this->fillOperador($dr, $vendedor);
			return $vendedor;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillVentaCheques($dr, VentaCheques $ventaCheques) {
		try {
			$ventaCheques->numero = $dr['cod_venta_cheques'];
			$ventaCheques->empresa = $dr['empresa'];
			$ventaCheques->idImportePorOperacion = $dr['cod_importe_operacion'];
			$ventaCheques->importeTotal = $dr['importe_total'];
			$ventaCheques->entradaSalida = $dr['entrada_salida'];
			return $ventaCheques;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillVentaChequesCabecera($dr, VentaChequesCabecera $ventaChequesCabecera) {
		try {
			$ventaChequesCabecera->numero = $dr['cod_venta_cheques'];
			$ventaChequesCabecera->empresa = $dr['empresa'];
			$ventaChequesCabecera->idAsientoContable = $dr['cod_asiento_contable'];
			$ventaChequesCabecera->observaciones = $dr['observaciones'];
			$ventaChequesCabecera->idUsuario = $dr['cod_usuario'];
			$ventaChequesCabecera->idUsuarioBaja = $dr['cod_usuario_baja'];
			$ventaChequesCabecera->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$ventaChequesCabecera->fecha = Funciones::formatearFecha($dr['fecha_documento'], 'd/m/Y');
			$ventaChequesCabecera->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$ventaChequesCabecera->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$ventaChequesCabecera->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			return $ventaChequesCabecera;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillVentaChequesTemporal($dr, VentaChequesTemporal $ventaChequesTemporal) {
		try {
			$ventaChequesTemporal->id = $dr['cod_venta_cheques_temporal'];
			$ventaChequesTemporal->idCaja = $dr['cod_caja'];
			$ventaChequesTemporal->idCuentaBancaria = $dr['cod_cuenta_bancaria'];
			$ventaChequesTemporal->fecha = Funciones::formatearFecha($dr['fecha'], 'd/m/Y');
			$ventaChequesTemporal->idCheques = $dr['cheques'];
			$ventaChequesTemporal->idUsuario = $dr['cod_usuario'];
			$ventaChequesTemporal->idUsuarioBaja = $dr['cod_usuario_baja'];
			$ventaChequesTemporal->idUsuarioUltimaMod = $dr['cod_usuario_ultima_mod'];
			$ventaChequesTemporal->confirmado = $dr['confirmado'];
			$ventaChequesTemporal->anulado = $dr['anulado'];
			$ventaChequesTemporal->fechaAlta = Funciones::formatearFecha($dr['fecha_alta'], 'd/m/Y');
			$ventaChequesTemporal->fechaBaja = Funciones::formatearFecha($dr['fecha_baja'], 'd/m/Y');
			$ventaChequesTemporal->fechaUltimaMod = Funciones::formatearFecha($dr['fecha_ultima_mod'], 'd/m/Y');
			$ventaChequesTemporal->empresa = $dr['empresa'];
			return $ventaChequesTemporal;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillZona($dr, Zona $zona) {
		try {
			$zona->id = $dr['cod_zona'];
			$zona->anulado = $dr['anulado'];
			$zona->nombre = $dr['nombre'];
			$zona->descripcion = $dr['descripcion'];
			return $zona;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function fillZonaTransporte($dr, ZonaTransporte $zonaTransporte) {
		try {
			$zonaTransporte->id = $dr['cod_zona_nro'];
			$zonaTransporte->anulado = $dr['anulado'];
			$zonaTransporte->descripcion = $dr['descripcion'];
			$zonaTransporte->nombre = $dr['denom_zona'];
			return $zonaTransporte;
		} catch (Exception $ex) {
			throw $ex;
		}
	}

	//MAPPERS DE CLASES
	private function mapperQueryAcreditarCheque(AcreditarCheque $acreditarCheque, $modo){
		return $this->mapperQueryAcreditarDebitarCheque($acreditarCheque, $modo);
	}
	private function mapperQueryAcreditarChequeCabecera(AcreditarChequeCabecera $acreditarChequeCabecera, $modo){
		return $this->mapperQueryAcreditarDebitarChequeCabecera($acreditarChequeCabecera, $modo);
	}
	private function mapperQueryAcreditarDebitarCheque(AcreditarDebitarCheque $acreditarDebitarCheque, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM acreditar_debitar_cheque_d ';
				$sql .= 'WHERE cod_acreditar_debitar_cheque = ' . Datos::objectToDB($acreditarDebitarCheque->numero) . ' ';
				$sql .= 'AND entrada_salida = ' . Datos::objectToDB($acreditarDebitarCheque->entradaSalida) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($acreditarDebitarCheque->empresa) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO acreditar_debitar_cheque_d (';
				$sql .= 'cod_acreditar_debitar_cheque, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'importe_total, ';
				$sql .= 'entrada_salida';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($acreditarDebitarCheque->numero) . ', ';
				$sql .= Datos::objectToDB($acreditarDebitarCheque->empresa) . ', ';
				$sql .= Datos::objectToDB($acreditarDebitarCheque->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($acreditarDebitarCheque->importeTotal) . ', ';
				$sql .= Datos::objectToDB($acreditarDebitarCheque->entradaSalida);
				$sql .= '); ';
				//} elseif ($modo == Modos::update) {
				//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_acreditar_debitar_cheque), 0) + 1 FROM acreditar_debitar_cheque_d ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($acreditarDebitarCheque->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryAcreditarDebitarChequeCabecera(AcreditarDebitarChequeCabecera $acreditarDebitarChequeCabecera, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM acreditar_debitar_cheque_c ';
				$sql .= 'WHERE cod_acreditar_debitar_cheque = ' . Datos::objectToDB($acreditarDebitarChequeCabecera->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($acreditarDebitarChequeCabecera->empresa) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO acreditar_debitar_cheque_c (';
				$sql .= 'cod_acreditar_debitar_cheque, ';
				$sql .= 'empresa, ';
				$sql .= 'tipo, ';
				$sql .= 'fecha, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_documento, ';
				$sql .= 'fecha_alta';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($acreditarDebitarChequeCabecera->numero) . ', ';
				$sql .= Datos::objectToDB($acreditarDebitarChequeCabecera->empresa) . ', ';
				$sql .= Datos::objectToDB($acreditarDebitarChequeCabecera->tipo) . ', ';
				$sql .= Datos::objectToDB($acreditarDebitarChequeCabecera->fecha) . ', ';
				$sql .= Datos::objectToDB($acreditarDebitarChequeCabecera->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
				//} elseif ($modo == Modos::update) {
				//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_acreditar_debitar_cheque), 0) + 1 FROM acreditar_debitar_cheque_c ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($acreditarDebitarChequeCabecera->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryAjusteStock(AjusteStock $ajusteStock, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ajustes_stock ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($ajusteStock->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ajustes_stock (';
				$sql .= 'id, ';
				$sql .= 'tipo_movimiento, ';
				$sql .= 'cod_almacen, ';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				$sql .= 'motivo, ';
				$sql .= 'cantidad, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ajusteStock->id) . ', ';
				$sql .= Datos::objectToDB($ajusteStock->tipoMovimiento) . ', ';
				$sql .= Datos::objectToDB($ajusteStock->almacen->id) . ', ';
				$sql .= Datos::objectToDB($ajusteStock->articulo->id) . ', ';
				$sql .= Datos::objectToDB($ajusteStock->colorPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($ajusteStock->motivo) . ', ';
				$sql .= Datos::objectToDB($ajusteStock->cantidadTotal) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($ajusteStock->cantidad[$i]) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(id), 0) + 1 FROM ajustes_stock; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryAjusteStockMP(AjusteStockMP $ajusteStockMP, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ajustes_stock_mp ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($ajusteStockMP->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ajustes_stock_mp (';
				$sql .= 'id, ';
				$sql .= 'tipo_movimiento, ';
				$sql .= 'cod_almacen, ';
				$sql .= 'cod_material, ';
				$sql .= 'cod_color, ';
				$sql .= 'motivo, ';
				$sql .= 'cantidad, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ajusteStockMP->id) . ', ';
				$sql .= Datos::objectToDB($ajusteStockMP->tipoMovimiento) . ', ';
				$sql .= Datos::objectToDB($ajusteStockMP->almacen->id) . ', ';
				$sql .= Datos::objectToDB($ajusteStockMP->material->id) . ', ';
				$sql .= Datos::objectToDB($ajusteStockMP->colorMateriaPrima->idColor) . ', ';
				$sql .= Datos::objectToDB($ajusteStockMP->motivo) . ', ';
				$sql .= Datos::objectToDB($ajusteStockMP->cantidadTotal) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($ajusteStockMP->cantidad[$i]) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(id), 0) + 1 FROM ajustes_stock_mp; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryAlmacen(Almacen $almacen, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM almacenes ';
				$sql .= 'WHERE cod_almacen = ' . Datos::objectToDB($almacen->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO almacenes (';
				$sql .= 'cod_almacen, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_ultima_modificacion, ';
				$sql .= 'denom_almacen, ';
				$sql .= 'denom_abrev ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($almacen->id) . ', ';
				$sql .=  Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= Datos::objectToDB($almacen->nombre) . ', ';
				$sql .= Datos::objectToDB($almacen->nombreCorto) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE almacenes SET ';
				$sql .= 'denom_almacen = ' . Datos::objectToDB($almacen->nombre) . ', ';
				$sql .= 'denom_abrev = ' . Datos::objectToDB($almacen->nombreCorto) . ', ';
				$sql .= 'fecha_ultima_modificacion = GETDATE() ';
				$sql .= 'WHERE cod_almacen = ' . Datos::objectToDB($almacen->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE almacenes SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('N') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_almacen = ' . Datos::objectToDB($almacen->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
    private function mapperQueryAlmacenPorSeccion(AlmacenPorSeccion $almacenPorSeccion, $modo){
        $sql = '';
        try {
            if ($modo == Modos::select){
                $sql .= 'SELECT * FROM almacenes_por_seccion_v ';
                $sql .= 'WHERE cod_almacen = ' . Datos::objectToDB($almacenPorSeccion->id) . ' ';
                $sql .= 'AND cod_seccion = ' . Datos::objectToDB($almacenPorSeccion->idSeccionProduccion) . '; ';
            } elseif (($modo == Modos::insert)) {
                $sql .= 'INSERT INTO almacenes_por_seccion (';
                $sql .= 'cod_almacen, ';
                $sql .= 'cod_seccion ';
                $sql .= ') VALUES (';
                $sql .= Datos::objectToDB($almacenPorSeccion->id) . ', ';
                $sql .= Datos::objectToDB($almacenPorSeccion->seccionProduccion->id) . ' ';
                $sql .= '); ';
            } elseif ($modo == Modos::delete) {
                $sql .= 'DELETE FROM almacenes_por_seccion ';
                $sql .= 'WHERE cod_almacen = ' . Datos::objectToDB($almacenPorSeccion->id) . ' ';
                $sql .= 'AND cod_seccion = ' . Datos::objectToDB($almacenPorSeccion->idSeccionProduccion) . '; ';
            } elseif ($modo == Modos::update) {
            } else {
                throw new FactoryException('Modo incorrecto');
            }
            return $sql;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
	private function mapperQueryAporteSocio(AporteSocio $aporteSocio, $modo) {
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM aporte_socio ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($aporteSocio->empresa) . ' ';
				$sql .= 'AND nro_aporte_socio = ' . Datos::objectToDB($aporteSocio->numero) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO aporte_socio (';
				$sql .= 'nro_aporte_socio, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'cod_socio, ';
				$sql .= 'concepto, ';
				$sql .= 'importe_total, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_documento, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($aporteSocio->numero) . ', ';
				$sql .= Datos::objectToDB($aporteSocio->empresa) . ', ';
				$sql .= Datos::objectToDB($aporteSocio->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($aporteSocio->socio->id) . ', ';
				$sql .= Datos::objectToDB($aporteSocio->concepto) . ', ';
				$sql .= Datos::objectToDB($aporteSocio->importeTotal) . ', ';
				$sql .= Datos::objectToDB($aporteSocio->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB($aporteSocio->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() , ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE aporte_socio SET ';
				$sql .= 'concepto = ' . Datos::objectToDB($aporteSocio->concepto) . ', ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($aporteSocio->asientoContable->id) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($aporteSocio->observaciones) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE nro_aporte_socio = ' . Datos::objectToDB($aporteSocio->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($aporteSocio->empresa) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE aporte_socio SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE nro_aporte_socio = ' . Datos::objectToDB($aporteSocio->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($aporteSocio->empresa) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_aporte_socio), 0) + 1 FROM aporte_socio ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($aporteSocio->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryAreaEmpresa(AreaEmpresa $areaEmpresa, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM areas_empresa ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($areaEmpresa->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO areas_empresa (';
				$sql .= 'id, ';
				$sql .= 'nombre, ';
				$sql .= 'habilitada_ticket, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($areaEmpresa->id) . ', ';
				$sql .= Datos::objectToDB($areaEmpresa->nombre) . ', ';
				$sql .= Datos::objectToDB($areaEmpresa->habilitadaTicket) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'DELETE FROM usuarios_por_area_empresa ';
				$sql .= 'WHERE id_area_empresa = ' . Datos::objectToDB($areaEmpresa->id) . '; ';
				$sql .= 'UPDATE areas_empresa SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($areaEmpresa->nombre) . ', ';
				$sql .= 'habilitada_ticket = ' . Datos::objectToDB($areaEmpresa->habilitadaTicket) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = ' . 'GETDATE()' . ' ' ;
				$sql .= 'WHERE id = ' . Datos::objectToDB($areaEmpresa->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE areas_empresa SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($areaEmpresa->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(id), 0) + 1 FROM areas_empresa; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryArticulo(Articulo $articulo, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM articulos ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB(Funciones::toString($articulo->id)) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO articulos (';
				$sql .= 'cod_articulo, ';
				$sql .= 'denom_articulo, ';
				$sql .= 'origen, ';
				$sql .= 'cod_ruta, ';
				$sql .= 'cod_linea, ';
				$sql .= 'cod_familia_producto, ';
				$sql .= 'cod_marca, ';
				$sql .= 'cod_rango, ';
				$sql .= 'cod_cliente, ';
				$sql .= 'cod_prov, ';
				$sql .= 'cod_tempo, ';
				$sql .= 'cod_horma, ';
				$sql .= 'aprob_disenio, ';
				$sql .= 'aprob_produccion, ';
				$sql .= 'naturaleza, ';
				$sql .= 'fechaAlta, ';
				//$sql .= 'cod_rubro_iva, ';
				/* Se usan los precios de ColorPorArticulo
				$sql .= 'precio_distribuidor, ';
				$sql .= 'precio_lista_distribuidor, ';
				$sql .= 'precio_lista, ';
				$sql .= 'precio_lista_mayor, ';
				$sql .= 'precio_recargado, ';
				*/
				$sql .= 'vigente ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($articulo->id) . ', ';
				$sql .= Datos::objectToDB($articulo->nombre) . ', ';
				$sql .= Datos::objectToDB($articulo->origen) . ', ';
				$sql .= Datos::objectToDB($articulo->rutaProduccion->id) . ', ';
				$sql .= Datos::objectToDB($articulo->lineaProducto->id) . ', ';
				$sql .= Datos::objectToDB($articulo->familiaProducto->id) . ', ';
				$sql .= Datos::objectToDB($articulo->marca->id) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($articulo->rangoTalle->id, 2)) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($articulo->cliente->id, 2)) . ', ';
				$sql .= Datos::objectToDB($articulo->proveedor->id) . ', ';
				$sql .= Datos::objectToDB($articulo->temporada->id) . ', ';
				$sql .= Datos::objectToDB($articulo->horma->id) . ', ';
				$sql .= Datos::objectToDB('S') . ', ';
				$sql .= Datos::objectToDB('S') . ', ';
				$sql .= Datos::objectToDB($articulo->naturaleza) . ', ';
				$sql .= 'GETDATE(), ';
				//$sql .= Datos::objectToDB($articulo->rubroIva->id) . ', ';
				/* Se usan los precios de ColorPorArticulo
				$sql .= Datos::objectToDB($articulo->precioDistribuidor) . ', ';
				$sql .= Datos::objectToDB($articulo->precioListaDistribuidor) . ', ';
				$sql .= Datos::objectToDB($articulo->precioLista) . ', ';
				$sql .= Datos::objectToDB($articulo->precioListaMayorista) . ', ';
				$sql .= Datos::objectToDB($articulo->precioRecargado) . ', ';
				*/
				$sql .= Datos::objectToDB('S') . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE articulos SET ';
				/* Solapa de ALTA */
				$sql .= 'denom_articulo = ' . Datos::objectToDB($articulo->nombre) . ', ';
				$sql .= 'origen = ' . Datos::objectToDB($articulo->origen) . ', ';
				$sql .= 'cod_ruta = ' . Datos::objectToDB($articulo->rutaProduccion->id) . ', ';
				$sql .= 'cod_linea = ' . Datos::objectToDB($articulo->lineaProducto->id) . ', ';
				$sql .= 'cod_familia_producto = ' . Datos::objectToDB($articulo->familiaProducto->id) . ', ';
				$sql .= 'cod_marca = ' . Datos::objectToDB($articulo->marca->id) . ', ';
				$sql .= 'cod_rango = ' . Datos::objectToDB(Funciones::padLeft($articulo->rangoTalle->id, 2)) . ', ';
				$sql .= 'cod_cliente = ' . Datos::objectToDB(Funciones::padLeft($articulo->cliente->id, 2)) . ', ';
				$sql .= 'cod_prov = ' . Datos::objectToDB($articulo->proveedor->id) . ', ';
				$sql .= 'cod_tempo = ' . Datos::objectToDB($articulo->temporada->id) . ', ';
				$sql .= 'cod_horma = ' . Datos::objectToDB($articulo->horma->id) . ', ';
				$sql .= 'fecha_lanzamiento = ' . Datos::objectToDB($articulo->fechaDeLanzamiento) . ', ';
				$sql .= 'naturaleza = ' . Datos::objectToDB($articulo->naturaleza) . ', ';
				/* Se usan los precios de ColorPorArticulo
				$sql .= 'precio_distribuidor = ' . Datos::objectToDB($articulo->precioDistribuidor) . ', ';
				$sql .= 'precio_lista_distribuidor = ' . Datos::objectToDB($articulo->precioListaDistribuidor) . ', ';
				$sql .= 'precio_lista = ' . Datos::objectToDB($articulo->precioLista) . ', ';
				$sql .= 'precio_lista_aumento = ' . Datos::objectToDB($articulo->precioListaAumento) . ', ';
				$sql .= 'precio_lista_mayor = ' . Datos::objectToDB($articulo->precioListaMayorista) . ', ';
				$sql .= 'precio_recargado = ' . Datos::objectToDB($articulo->precioRecargado) . ', ';
				*/
				/* Solapa Comercial */
				$sql .= 'cod_rubro_iva = ' . Datos::objectToDB($articulo->rubroIva->id) . ', ';
				$sql .= 'fecha_ultima_modificacion = GETDATE() ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB($articulo->id) . '; ';
				//Cuando se modifica un artï¿½culo, tambiï¿½n pongo la fecha de ï¿½ltima modificaciï¿½n en sus colores (para sincronizar luego con ecommerce)
				$sql .= 'UPDATE colores_por_articulo SET ';
				$sql .= 'fechaUltimaMod = GETDATE() ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB($articulo->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE articulos SET ';
				$sql .= 'vigente = ' . Datos::objectToDB('N') . ', ';
				$sql .= 'fecha_de_baja = GETDATE() ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB($articulo->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(CONVERT(INT, cod_articulo)), 0) + 1 FROM articulos WHERE ISNUMERIC(cod_articulo) = 1; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryAsientoContable(AsientoContable $asientoContable, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM asientos_contables ';
				$sql .= 'WHERE cod_asiento = ' . Datos::objectToDB($asientoContable->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO asientos_contables (';
				$sql .= 'cod_asiento, ';
				$sql .= 'empresa, ';
				$sql .= 'nombre, ';
				$sql .= 'cod_ejercicio, ';
				$sql .= 'fecha_asiento, ';
				$sql .= 'importe, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($asientoContable->id) . ', ';
				$sql .= Datos::objectToDB($asientoContable->empresa) . ', ';
				$sql .= Datos::objectToDB($asientoContable->nombre) . ', ';
				$sql .= Datos::objectToDB($asientoContable->ejercicioContable->id) . ', ';
				$sql .= Datos::objectToDB($asientoContable->fecha) . ', ';
				$sql .= Datos::objectToDB($asientoContable->importe) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'DELETE FROM filas_asientos_contables ';
				$sql .= 'WHERE cod_asiento = ' . Datos::objectToDB($asientoContable->id) . '; ';
				$sql .= 'UPDATE asientos_contables SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($asientoContable->nombre) . ', ';
				$sql .= 'cod_ejercicio = ' . Datos::objectToDB($asientoContable->ejercicioContable->id) . ', ';
				$sql .= 'fecha_asiento = ' . Datos::objectToDB($asientoContable->fecha) . ', ';
				$sql .= 'importe = ' . Datos::objectToDB($asientoContable->importe) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = ' . 'GETDATE()' . ' ' ;
				$sql .= 'WHERE cod_asiento = ' . Datos::objectToDB($asientoContable->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE asientos_contables SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_asiento = ' . Datos::objectToDB($asientoContable->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_asiento), 0) + 1 FROM asientos_contables; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryAsientoContableModelo(AsientoContableModelo $asientoContableModelo, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM asientos_modelo_c ';
				$sql .= 'WHERE cod_asiento_modelo = ' . Datos::objectToDB($asientoContableModelo->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO asientos_modelo_c (';
				$sql .= 'cod_asiento_modelo, ';
				$sql .= 'nombre, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($asientoContableModelo->id) . ', ';
				$sql .= Datos::objectToDB($asientoContableModelo->nombre) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'DELETE FROM asientos_modelo_d ';
				$sql .= 'WHERE cod_asiento_modelo = ' . Datos::objectToDB($asientoContableModelo->id) . '; ';
				$sql .= 'UPDATE asientos_modelo_c SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($asientoContableModelo->nombre) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = ' . 'GETDATE()' . ' ' ;
				$sql .= 'WHERE cod_asiento_modelo = ' . Datos::objectToDB($asientoContableModelo->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE asientos_modelo_c SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_asiento_modelo = ' . Datos::objectToDB($asientoContableModelo->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_asiento_modelo), 0) + 1 FROM asientos_modelo_c; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryAsientoContableModeloFila(AsientoContableModeloFila $asientoContableModeloFila, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM asientos_modelo_d ';
				$sql .= 'WHERE cod_asiento_modelo = ' . Datos::objectToDB($asientoContableModeloFila->idAsientoContableModelo) . ' ';
				$sql .= 'AND numero_fila = ' . Datos::objectToDB($asientoContableModeloFila->numeroFila) . '; ';
			} elseif ($modo == Modos::insert || $modo == Modos::update) {
				$sql .= 'INSERT INTO asientos_modelo_d (';
				$sql .= 'cod_asiento_modelo, ';
				$sql .= 'numero_fila, ';
				$sql .= 'cod_imputacion, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($asientoContableModeloFila->idAsientoContableModelo) . ', ';
				$sql .= Datos::objectToDB($asientoContableModeloFila->numeroFila) . ', ';
				$sql .= Datos::objectToDB($asientoContableModeloFila->imputacion->id) . ', ';
				$sql .= Datos::objectToDB($asientoContableModeloFila->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE asientos_modelo_d SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_asiento_modelo = ' . Datos::objectToDB($asientoContableModeloFila->idAsientoContableModelo) . ' ';
				$sql .= 'AND numero_fila = ' . Datos::objectToDB($asientoContableModeloFila->numeroFila) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryAutorizacion(Autorizacion $autorizacion, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM autorizaciones ';
				$sql .= 'WHERE cod_tipo_autorizacion = ' . Datos::objectToDB($autorizacion->idAutorizacionTipo) . ' ';
				$sql .= 'AND numero_autorizacion = ' . Datos::objectToDB($autorizacion->numero) . ' ';
				$sql .= 'AND id_especifico = ' . Datos::objectToDB($autorizacion->idEspecifico) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO autorizaciones (';
				$sql .= 'cod_tipo_autorizacion, ';
				$sql .= 'autorizado, ';
				$sql .= 'numero_autorizacion, ';
				$sql .= 'motivo, ';
				$sql .= 'id_especifico, ';
				$sql .= 'cod_usuario_autorizador, ';
				$sql .= 'fecha_autorizacion ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($autorizacion->autorizacionTipo->id) . ', ';
				$sql .= Datos::objectToDB($autorizacion->autorizado) . ', ';
				$sql .= Datos::objectToDB($autorizacion->numero) . ', ';
				$sql .= Datos::objectToDB($autorizacion->motivo) . ', ';
				$sql .= Datos::objectToDB($autorizacion->idEspecifico) . ', ';
				$sql .= Datos::objectToDB($autorizacion->usuario->id) . ', ';
				$sql .= Datos::objectToDB($autorizacion->fecha) . ' ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) { No se usa.
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM autorizaciones ';
				$sql .= 'WHERE cod_tipo_autorizacion = ' . Datos::objectToDB($autorizacion->autorizacionTipo->id) . ' ';
				$sql .= 'AND numero_autorizacion = ' . Datos::objectToDB($autorizacion->numero) . ' ';
				$sql .= 'AND id_especifico = ' . Datos::objectToDB($autorizacion->idEspecifico) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryAutorizacionPersona(AutorizacionPersona $autorizacionPersona, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM autorizaciones_personas ';
				$sql .= 'WHERE cod_tipo_autorizacion = ' . Datos::objectToDB($autorizacionPersona->idAutorizacionTipo) . ' ';
				$sql .= 'AND numero_autorizacion = ' . Datos::objectToDB($autorizacionPersona->numero) . ' ';
				$sql .= 'AND cod_usuario_autorizador = ' . Datos::objectToDB($autorizacionPersona->idUsuario) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO autorizaciones_personas (';
				$sql .= 'cod_tipo_autorizacion, ';
				$sql .= 'numero_autorizacion, ';
				$sql .= 'cod_usuario_autorizador ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($autorizacionPersona->autorizacionTipo->id) . ', ';
				$sql .= Datos::objectToDB($autorizacionPersona->numero) . ', ';
				$sql .= Datos::objectToDB($autorizacionPersona->usuario->id) . ' ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) { No se usa.
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM autorizaciones_personas ';
				$sql .= 'WHERE cod_tipo_autorizacion = ' . Datos::objectToDB($autorizacionPersona->autorizacionTipo->id) . ' ';
				$sql .= 'AND numero_autorizacion = ' . Datos::objectToDB($autorizacionPersona->numero) . ' ';
				$sql .= 'AND cod_usuario_autorizador = ' . Datos::objectToDB($autorizacionPersona->usuario->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryAutorizacionTipo(AutorizacionTipo $autorizacionTipo, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM autorizaciones_tipos ';
				$sql .= 'WHERE cod_tipo_autorizacion = ' . Datos::objectToDB($autorizacionTipo->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO autorizaciones_tipos (';
				$sql .= 'cant_autorizaciones_necesarias, ';
				$sql .= 'nombre_tipo_autorizacion, ';
				$sql .= 'nombre_objeto ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($autorizacionTipo->cantidad) . ', ';
				$sql .= Datos::objectToDB($autorizacionTipo->nombre) . ', ';
				$sql .= Datos::objectToDB($autorizacionTipo->nombreObjeto) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE autorizaciones_tipos SET ';
				$sql .= 'cant_autorizaciones_necesarias = ' . Datos::objectToDB($autorizacionTipo->cantidad) . ', ';
				$sql .= 'nombre_tipo_autorizacion = ' . Datos::objectToDB($autorizacionTipo->nombre) . ', ';
				$sql .= 'nombre_objeto = ' . Datos::objectToDB($autorizacionTipo->nombreObjeto) . ' ';
				$sql .= 'WHERE cod_tipo_autorizacion = ' . Datos::objectToDB($autorizacionTipo->id) . '; ';
			//} elseif ($modo == Modos::delete) { No se usa.
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT IDENT_CURRENT(\'autorizaciones_tipos\') + IDENT_INCR(\'autorizaciones_tipos\');';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryBanco(Banco $banco, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM banco ';
				$sql .= 'WHERE cod_banco = ' . Datos::objectToDB($banco->idBanco) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO banco (';
				$sql .= 'cod_banco, ';
				$sql .= 'nombre, ';
				$sql .= 'numero_banco, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($banco->idBanco) . ', ';
				$sql .= Datos::objectToDB($banco->nombre) . ', ';
				$sql .= Datos::objectToDB($banco->codigoBanco) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE banco SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($banco->nombre) . ', ';
				$sql .= 'numero_banco = ' . Datos::objectToDB($banco->codigoBanco) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_banco = ' . Datos::objectToDB($banco->idBanco) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE banco SET ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_banco = ' . Datos::objectToDB($banco->idBanco) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_banco), 0) + 1 FROM banco;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryBancoPropio(BancoPropio $bancoPropio, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM banco_propio_v ';
				$sql .= 'WHERE cod_banco = ' . Datos::objectToDB($bancoPropio->idBanco) . ' ';
				$sql .= 'AND cod_sucursal = ' . Datos::objectToDB($bancoPropio->idSucursal) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO banco_propio (';
				$sql .= 'cod_banco, ';
				$sql .= 'cod_sucursal, ';
				$sql .= 'nombre_sucursal, ';
				$sql .= 'observaciones, ';
				$sql .= 'telefono, ';
				$sql .= 'direccion_calle, ';
				$sql .= 'direccion_codigo_postal, ';
				$sql .= 'direccion_departamento, ';
				$sql .= 'direccion_cod_localidad, ';
				$sql .= 'direccion_numero, ';
				$sql .= 'direccion_cod_pais, ';
				$sql .= 'direccion_piso, ';
				$sql .= 'direccion_cod_provincia, ';
				$sql .= 'imputacion_contable, ';
				$sql .= 'fecha_inicio_cuenta, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($bancoPropio->banco->idBanco) . ', ';
				$sql .= Datos::objectToDB($bancoPropio->idSucursal) . ', ';
				$sql .= Datos::objectToDB($bancoPropio->nombreSucursal) . ', ';
				$sql .= Datos::objectToDB($bancoPropio->observaciones) . ', ';
				$sql .= Datos::objectToDB($bancoPropio->telefono) . ', ';
				$sql .= Datos::objectToDB($bancoPropio->direccion->calle) . ', ';
				$sql .= Datos::objectToDB($bancoPropio->direccion->codigoPostal) . ', ';
				$sql .= Datos::objectToDB($bancoPropio->direccion->departamento) . ', ';
				$sql .= Datos::objectToDB($bancoPropio->direccion->localidad->id) . ', ';
				$sql .= Datos::objectToDB($bancoPropio->direccion->numero) . ', ';
				$sql .= Datos::objectToDB($bancoPropio->direccion->pais->id) . ', ';
				$sql .= Datos::objectToDB($bancoPropio->direccion->piso) . ', ';
				$sql .= Datos::objectToDB($bancoPropio->direccion->provincia->id) . ', ';
				$sql .= Datos::objectToDB($bancoPropio->imputacionContable) . ', ';
				$sql .= Datos::objectToDB($bancoPropio->fechaInicioCuenta) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE banco_propio SET ';
				$sql .= 'nombre_sucursal = ' . Datos::objectToDB($bancoPropio->nombreSucursal) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($bancoPropio->observaciones) . ', ';
				$sql .= 'telefono = ' . Datos::objectToDB($bancoPropio->telefono) . ', ';
				$sql .= 'direccion_calle = ' . Datos::objectToDB($bancoPropio->direccion->calle) . ', ';
				$sql .= 'direccion_codigo_postal = ' . Datos::objectToDB($bancoPropio->direccion->codigoPostal) . ', ';
				$sql .= 'direccion_departamento = ' . Datos::objectToDB($bancoPropio->direccion->departamento) . ', ';
				$sql .= 'direccion_cod_localidad = ' . Datos::objectToDB($bancoPropio->direccion->localidad->id) . ', ';
				$sql .= 'direccion_numero = ' . Datos::objectToDB($bancoPropio->direccion->numero) . ', ';
				$sql .= 'direccion_cod_pais = ' . Datos::objectToDB($bancoPropio->direccion->pais->id) . ', ';
				$sql .= 'direccion_piso = ' . Datos::objectToDB($bancoPropio->direccion->piso) . ', ';
				$sql .= 'direccion_cod_provincia = ' . Datos::objectToDB($bancoPropio->direccion->provincia->id) . ', ';
				$sql .= 'imputacion_contable = ' . Datos::objectToDB($bancoPropio->imputacionContable) . ', ';
				$sql .= 'fecha_inicio_cuenta = ' . Datos::objectToDB($bancoPropio->fechaInicioCuenta) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_banco = ' . Datos::objectToDB($bancoPropio->banco->idBanco) . ' AND ';
				$sql .= 'cod_sucursal = ' . Datos::objectToDB($bancoPropio->idSucursal) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE banco_propio SET ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_banco = ' . Datos::objectToDB($bancoPropio->idBanco) . ' AND ';
				$sql .= 'cod_sucursal = ' . Datos::objectToDB($bancoPropio->idSucursal) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_sucursal), 0) + 1 FROM banco_propio WHERE cod_banco = ' . Datos::objectToDB($bancoPropio->banco->idBanco) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCaja(Caja $caja, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM caja_v ';
				$sql .= 'WHERE cod_caja = ' . Datos::objectToDB($caja->id);
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO caja (';
				$sql .= 'cod_caja, ';
				$sql .= 'cod_duenio, ';
				$sql .= 'cod_caja_padre, ';
				$sql .= 'cod_imputacion, ';
				$sql .= 'nombre, ';
				$sql .= 'fecha_limite, ';
				$sql .= 'dias_cierre, ';
				$sql .= 'importe_efectivo, ';
				$sql .= 'importe_descubierto, ';
				$sql .= 'importe_maximo, ';
				$sql .= 'caja_banco, ';
				$sql .= 'disp_para_negociar, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($caja->id) . ', ';
				$sql .= Datos::objectToDB($caja->responsable->id) . ', ';
				$sql .= Datos::objectToDB($caja->cajaPadre->id) . ', ';
				$sql .= Datos::objectToDB($caja->imputacion->id) . ', ';
				$sql .= Datos::objectToDB($caja->nombre) . ', ';
				$sql .= Datos::objectToDB($caja->fechaLimite) . ', ';
				$sql .= Datos::objectToDB($caja->diasCierre) . ', ';
				$sql .= Datos::objectToDB(Funciones::toFloat($caja->importeEfectivo)) . ', ';
				$sql .= Datos::objectToDB($caja->importeDescubierto) . ', ';
				$sql .= Datos::objectToDB(Funciones::toFloat($caja->importeMaximo)) . ', ';
				$sql .= Datos::objectToDB($caja->esCajaBanco) . ', ';
				$sql .= Datos::objectToDB(Funciones::toFloat($caja->dispParaNegociar)) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE caja SET ';
				$sql .= 'fecha_limite = ' . Datos::objectToDB($caja->fechaLimite) . ', ';
				$sql .= 'dias_cierre = ' . Datos::objectToDB($caja->diasCierre) . ', ';
				$sql .= 'cod_imputacion = ' . Datos::objectToDB($caja->imputacion->id) . ', ';
				$sql .= 'nombre = ' . Datos::objectToDB($caja->nombre) . ', ';
				$sql .= 'importe_efectivo = ' . Datos::objectToDB($caja->importeEfectivo) . ', ';
				$sql .= 'importe_descubierto = ' . Datos::objectToDB($caja->importeDescubierto) . ', ';
				$sql .= 'importe_maximo = ' . Datos::objectToDB(Funciones::toFloat($caja->importeMaximo)) . ', ';
				$sql .= 'caja_banco = ' . Datos::objectToDB($caja->esCajaBanco) . ', ';
				$sql .= 'disp_para_negociar = ' . Datos::objectToDB(Funciones::toFloat($caja->dispParaNegociar)) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_caja = ' . Datos::objectToDB($caja->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE caja SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_caja = ' . Datos::objectToDB($caja->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_caja), 0) + 1 FROM caja;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCajaPosiblesTransferenciaInterna(CajaPosiblesTransferenciaInterna $cajaPosiblesTransferenciaInterna, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM cajas_posibles_transferencia_interna_v ';
				$sql .= 'WHERE cod_caja_salida = ' . Datos::objectToDB($cajaPosiblesTransferenciaInterna->idCajaSalida) . ' AND ';
				$sql .= 'cod_caja_entrada = ' . Datos::objectToDB($cajaPosiblesTransferenciaInterna->idCajaEntrada) . '; ';
			} elseif (($modo == Modos::insert) || ($modo == Modos::update)) {
				$sql .= 'INSERT INTO cajas_posibles_transferencia_interna (';
				$sql .= 'cod_caja_salida, ';
				$sql .= 'cod_caja_entrada ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($cajaPosiblesTransferenciaInterna->idCajaSalida) . ', ';
				$sql .= Datos::objectToDB($cajaPosiblesTransferenciaInterna->idCajaEntrada) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::delete) {
				// Se borran todos los registros de una caja al mismo tiempo, y se vuelven a insertar todos on transaction (desde el abm de cajas)
				$sql .= 'DELETE FROM cajas_posibles_transferencia_interna ';
				$sql .= 'WHERE cod_caja_salida = ' . Datos::objectToDB($cajaPosiblesTransferenciaInterna->cajaSalida->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCambiosSituacionCliente(CambiosSituacionCliente $cambiosSituacionCliente, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM cambios_situacion_cliente ';
				$sql .= 'WHERE cod_cambios_situacion_cliente = ' . Datos::objectToDB($cambiosSituacionCliente->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO cambios_situacion_cliente (';
				$sql .= 'cod_cambios_situacion_cliente, ';
				$sql .= 'cod_cliente, ';
				$sql .= 'cod_calificacion_nuevo, ';
				$sql .= 'cod_calificacion_anterior, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($cambiosSituacionCliente->id) . ', ';
				$sql .= Datos::objectToDB($cambiosSituacionCliente->cliente->id) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($cambiosSituacionCliente->calificacionNueva, 2, '0')) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($cambiosSituacionCliente->calificacionAnterior, 2, '0')) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_cambios_situacion_cliente), 0) + 1 FROM cambios_situacion_cliente' . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCategoriaCalzadoUsuario(CategoriaCalzadoUsuario $categoriaCalzadoUsuario, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM categorias_calzado_usuarios ';
				$sql .= 'WHERE cod_categoria = ' . Datos::objectToDB($categoriaCalzadoUsuario->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO categorias_calzado_usuarios (';
				$sql .= 'cod_categoria, ';
				$sql .= 'denom_categoria, ';
				$sql .= 'anulado, ';
				$sql .= 'fechaAlta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($categoriaCalzadoUsuario->id) . ', ';
				$sql .= Datos::objectToDB($categoriaCalzadoUsuario->nombre) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE categorias_calzado_usuarios SET ';
				$sql .= 'denom_categoria = ' . Datos::objectToDB($categoriaCalzadoUsuario->nombre) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB($categoriaCalzadoUsuario->anulado) . ', ';
				if ($categoriaCalzadoUsuario->anulado == 'S')
					$sql .= 'fechaBaja = GETDATE(), ';
				$sql .= 'fechaUltimaMod = GETDATE(), ';
				$sql .= 'WHERE cod_categoria = ' . Datos::objectToDB($categoriaCalzadoUsuario->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE categorias_calzado_usuarios SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fechaBaja = GETDATE() ';
				$sql .= 'WHERE cod_categoria = ' . Datos::objectToDB($categoriaCalzadoUsuario->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCausaNotaDeCredito(CausaNotaDeCredito $causaNotaDeCredito, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM notas_credito_causalidades ';
				$sql .= 'WHERE clave_tabla = ' . Datos::objectToDB($causaNotaDeCredito->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO notas_credito_causalidades (';
				$sql .= 'causa_nota_credito ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($causaNotaDeCredito->nombre) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE notas_credito_causalidades SET ';
				$sql .= 'causa_nota_credito = ' . Datos::objectToDB($causaNotaDeCredito->nombre) . ' ';
				$sql .= 'WHERE clave_tabla = ' . Datos::objectToDB($causaNotaDeCredito->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM notas_credito_causalidades ';
				$sql .= 'WHERE clave_tabla = ' . Datos::objectToDB($causaNotaDeCredito->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT IDENT_CURRENT(\'notas_credito_causalidades\') + IDENT_INCR(\'notas_credito_causalidades\');';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCierrePeriodoFiscal(CierrePeriodoFiscal $cierrePeriodoFiscal, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM periodos_fiscales_cierres ';
				$sql .= 'WHERE cod_cierre_periodo = ' . Datos::objectToDB($cierrePeriodoFiscal->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO periodos_fiscales_cierres (';
				$sql .= 'cod_cierre_periodo, ';
				$sql .= 'cod_tipo_periodo, ';
				$sql .= 'fecha_desde, ';
				$sql .= 'fecha_hasta, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($cierrePeriodoFiscal->id) . ', ';
				$sql .= Datos::objectToDB($cierrePeriodoFiscal->tipoPeriodoFiscal->id) . ', ';
				$sql .= Datos::objectToDB($cierrePeriodoFiscal->fechaDesde) . ', ';
				$sql .= Datos::objectToDB($cierrePeriodoFiscal->fechaHasta) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE periodos_fiscales_cierres SET ';
				$sql .= 'cod_tipo_periodo = ' . Datos::objectToDB($cierrePeriodoFiscal->tipoPeriodoFiscal->id) . ', ';
				$sql .= 'fecha_desde = ' . Datos::objectToDB($cierrePeriodoFiscal->fechaDesde) . ', ';
				$sql .= 'fecha_hasta = ' . Datos::objectToDB($cierrePeriodoFiscal->fechaHasta) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_cierre_periodo = ' . Datos::objectToDB($cierrePeriodoFiscal->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE periodos_fiscales_cierres SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_cierre_periodo = ' . Datos::objectToDB($cierrePeriodoFiscal->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_cierre_periodo), 0) + 1 FROM periodos_fiscales_cierres;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCheque(Cheque $cheque, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM cheque_v ';
				$sql .= 'WHERE cod_cheque = ' . Datos::objectToDB($cheque->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO cheque (';
				$sql .= 'cod_cheque, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_cliente, ';
				$sql .= 'cod_proveedor, ';
				$sql .= 'cod_banco, ';
				$sql .= 'cod_cuenta_bancaria, ';
				$sql .= 'cod_rechazo_cheque, ';
				$sql .= 'numero, ';
				$sql .= 'librador_nombre, ';
				$sql .= 'librador_cuit, ';
				$sql .= 'importe, ';
				$sql .= 'no_a_la_orden, ';
				$sql .= 'cruzado, ';
				$sql .= 'concluido, ';
				$sql .= 'cod_caja_actual, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_emision, ';
				$sql .= 'fecha_vencimiento, ';
				$sql .= 'fecha_alta';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($cheque->id) . ', ';
				$sql .= Datos::objectToDB($cheque->empresa) . ', ';
				$sql .= Datos::objectToDB($cheque->cliente->id) . ', ';
				$sql .= Datos::objectToDB($cheque->proveedor->id) . ', ';
				$sql .= Datos::objectToDB($cheque->banco->idBanco) . ', ';
				$sql .= Datos::objectToDB($cheque->cuentaBancaria->id) . ', ';
				$sql .= Datos::objectToDB($cheque->rechazoCheque->numero) . ', ';
				$sql .= Datos::objectToDB($cheque->numero) . ', ';
				$sql .= Datos::objectToDB($cheque->libradorNombre) . ', ';
				$sql .= Datos::objectToDB($cheque->libradorCuit) . ', ';
				$sql .= Datos::objectToDB($cheque->importe) . ', ';
				$sql .= Datos::objectToDB($cheque->noALaOrden) . ', ';
				$sql .= Datos::objectToDB($cheque->cruzado) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($cheque->cajaActual->id) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($cheque->fechaEmision) . ', ';
				$sql .= Datos::objectToDB($cheque->fechaVencimiento) . ', ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE cheque SET ';
				$sql .= 'cod_cliente = ' . Datos::objectToDB($cheque->cliente->id) . ', ';
				$sql .= 'cod_proveedor = ' . Datos::objectToDB($cheque->proveedor->id) . ', ';
				$sql .= 'cod_banco = ' . Datos::objectToDB($cheque->banco->idBanco) . ', ';
				$sql .= 'cod_cuenta_bancaria = ' . Datos::objectToDB($cheque->cuentaBancaria->id) . ', ';
				$sql .= 'cod_rechazo_cheque = ' . Datos::objectToDB($cheque->rechazoCheque->numero) . ', ';
				$sql .= 'numero = ' . Datos::objectToDB($cheque->numero) . ', ';
				$sql .= 'librador_nombre = ' . Datos::objectToDB($cheque->libradorNombre) . ', ';
				$sql .= 'librador_cuit = ' . Datos::objectToDB($cheque->libradorCuit) . ', ';
				$sql .= 'no_a_la_orden = ' . Datos::objectToDB($cheque->noALaOrden) . ', ';
				$sql .= 'cruzado = ' . Datos::objectToDB($cheque->cruzado) . ', ';
				$sql .= 'concluido = ' . Datos::objectToDB($cheque->concluido) . ', ';
				$sql .= 'esperando_en_banco = ' . Datos::objectToDB($cheque->esperandoEnBanco) . ', ';
				$sql .= 'cod_caja_actual = ' . Datos::objectToDB($cheque->cajaActual->id) . ', ';
				$sql .= 'fecha_credito_debito = ' . Datos::objectToDB($cheque->fechaCreditoDebito) . ', ';
				$sql .= 'fecha_vencimiento = ' . Datos::objectToDB($cheque->fechaVencimiento) . ', ';
				$sql .= 'fecha_emision= ' . Datos::objectToDB($cheque->fechaEmision) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = ' . 'GETDATE()' . ' ' ;
				$sql .= 'WHERE cod_cheque = ' . Datos::objectToDB($cheque->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE cheque SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_cheque = ' . Datos::objectToDB($cheque->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_cheque), 0) + 1 FROM cheque; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryChequera(Chequera $chequera, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM chequera_c ';
				$sql .= 'WHERE cod_chequera= ' . Datos::objectToDB($chequera->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO chequera_c (';
				$sql .= 'cod_chequera, ';
				$sql .= 'cod_cuenta_bancaria, ';
				$sql .= 'fecha, ';
				$sql .= 'numero_inicio, ';
				$sql .= 'numero_fin, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($chequera->id) . ', ';
				$sql .= Datos::objectToDB($chequera->cuentaBancaria->id) . ', ';
				$sql .= Datos::objectToDB($chequera->fecha) . ', ';
				$sql .= Datos::objectToDB($chequera->numeroInicio) . ', ';
				$sql .= Datos::objectToDB($chequera->numeroFin) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE chequera_c SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'WHERE cod_chequera= ' . Datos::objectToDB($chequera->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_chequera), 0) + 1 FROM chequera_c; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryChequeraItem(ChequeraItem $chequeraItem, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM chequera_v ';
				$sql .= 'WHERE cod_chequera_d = ' . Datos::objectToDB($chequeraItem->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO chequera_d (';
				$sql .= 'cod_chequera_d, ';
				$sql .= 'cod_chequera, ';
				$sql .= 'numero ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($chequeraItem->id) . ', ';
				$sql .= Datos::objectToDB($chequeraItem->chequera->id) . ', ';
				$sql .= Datos::objectToDB($chequeraItem->numero) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE chequera_d SET ';
				$sql .= 'utilizado = ' . Datos::objectToDB($chequeraItem->utilizado) . ' ';
				$sql .= 'WHERE cod_chequera_d = ' . Datos::objectToDB($chequeraItem->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM chequera_d ';
				$sql .= 'WHERE cod_chequera_d = ' . Datos::objectToDB($chequeraItem->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_chequera_d), 0) + 1 FROM chequera_d' . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCliente(Cliente $cliente, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Clientes ';
				$sql .= 'WHERE cod_cli = ' . Datos::objectToDB($cliente->id) . ' AND ';
				$sql .= 'autorizado = ' . Datos::objectToDB('S') . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Clientes (';
				$sql .= 'cod_cliente, ';
				$sql .= 'cod_cli, ';
				$sql .= 'anulado, ';
				$sql .= 'activo, ';
				$sql .= 'autorizado, ';
				$sql .= 'cod_casa_central, ';
				$sql .= 'cod_casa_fiscal, ';
				$sql .= 'cod_casa_cobranza, ';
				$sql .= 'cod_calificacion, ';
				$sql .= 'email_1, ';
				$sql .= 'email_2, ';
				$sql .= 'observaciones_cobranza, ';
				$sql .= 'observaciones, ';
				$sql .= 'marcas_comercializa, ';
				$sql .= 'ref_bancarias, ';
				$sql .= 'ref_comerciales, ';
				$sql .= 'tel_cobranza_1, ';
				$sql .= 'tel_cobranza_2, ';
				$sql .= 'tel_cobranza_3, ';
				$sql .= 'cod_cond_iva, ';
				$sql .= 'descuento_especial, ';
				if ($cliente->calificacion != $cliente->calificacionOriginal)
					$sql .= 'fecha_calificacion, ';
				$sql .= 'forma_pago, ';
				$sql .= 'limite_credito, ';
				$sql .= 'plazo_maximo, ';
				$sql .= 'primera_entrega, ';
				$sql .= 'cuit, ';
				$sql .= 'dni, ';
				$sql .= 'denom_fantasia, ';
				$sql .= 'calle, ';
				$sql .= 'cod_postal, ';
				$sql .= 'oficina_depto, ';
				$sql .= 'cod_localidad, ';
				$sql .= 'cod_localidad_nro, ';
				$sql .= 'numero, ';
				$sql .= 'cod_pais, ';
				$sql .= 'partido_departamento, ';
				$sql .= 'piso, ';
				$sql .= 'cod_provincia, ';
				$sql .= 'email, ';
				$sql .= 'cod_grupo_empresa, ';
				$sql .= 'lista_aplicable, ';
				$sql .= 'razon_social, ';
				$sql .= 'rubro, ';
				$sql .= 'telefono_1, ';
				$sql .= 'interno_1, ';
				$sql .= 'habilitado_cae, ';
				$sql .= 'cod_vendedor, ';
				$sql .= 'fechaAlta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB(Funciones::padLeft(Datos::objectToDB($cliente->id), 6, '0')) . ', ';
				$sql .= Datos::objectToDB($cliente->id) . ', ';
				$sql .= Datos::objectToDB('S') . ', ';
				$sql .= Datos::objectToDB('S') . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(1) . ', ';
				$sql .= Datos::objectToDB(1) . ', ';
				$sql .= Datos::objectToDB(1) . ', ';
				if ($cliente->calificacion == 0)
					$cliente->calificacion = null;
				$sql .= Datos::objectToDB(Funciones::padLeft($cliente->calificacion, 2, '0')) . ', ';
				$sql .= Datos::objectToDB($cliente->cobranzaEmail1) . ', ';
				$sql .= Datos::objectToDB($cliente->cobranzaEmail2) . ', ';
				$sql .= Datos::objectToDB($cliente->observacionesCobranza) . ', ';
				$sql .= Datos::objectToDB($cliente->observaciones) . ', ';
				$sql .= Datos::objectToDB($cliente->marcasQueComercializa) . ', ';
				$sql .= Datos::objectToDB($cliente->referenciasBancarias) . ', ';
				$sql .= Datos::objectToDB($cliente->referenciasComerciales) . ', ';
				$sql .= Datos::objectToDB($cliente->cobranzaTelefono1) . ', ';
				$sql .= Datos::objectToDB($cliente->cobranzaTelefono2) . ', ';
				$sql .= Datos::objectToDB($cliente->cobranzaTelefono3) . ', ';
				$sql .= Datos::objectToDB($cliente->condicionIva->id) . ', ';
				$sql .= Datos::objectToDB($cliente->creditoDescuentoEspecial) . ', ';
				if ($cliente->calificacion != $cliente->calificacionOriginal)
					$sql .= 'GETDATE(), ';
				$sql .= Datos::objectToDB($cliente->creditoFormaDePago->id) . ', ';
				$sql .= Datos::objectToDB($cliente->creditoLimite) . ', ';
				$sql .= Datos::objectToDB($cliente->creditoPlazoMaximo) . ', ';
				$sql .= Datos::objectToDB($cliente->creditoPrimeraEntrega) . ', ';
				$sql .= Datos::objectToDB($cliente->cuit) . ', ';
				$sql .= Datos::objectToDB($cliente->dni) . ', ';
				$sql .= Datos::objectToDB($cliente->nombre) . ', ';
				$sql .= Datos::objectToDB($cliente->direccionCalle) . ', ';
				$sql .= Datos::objectToDB($cliente->direccionCodigoPostal) . ', ';
				$sql .= Datos::objectToDB($cliente->direccionDepartamento) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($cliente->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= Datos::objectToDB($cliente->direccionLocalidad->id) . ', ';
				$sql .= Datos::objectToDB($cliente->direccionNumero) . ', ';
				$sql .= Datos::objectToDB($cliente->direccionPais->id) . ', ';
				$sql .= Datos::objectToDB($cliente->direccionPartidoDepartamento) . ', ';
				$sql .= Datos::objectToDB($cliente->direccionPiso) . ', ';
				$sql .= Datos::objectToDB($cliente->direccionProvincia->id) . ', ';
				$sql .= Datos::objectToDB($cliente->email) . ', ';
				$sql .= Datos::objectToDB($cliente->grupoEmpresa->id) . ', ';
				$sql .= Datos::objectToDB($cliente->listaAplicable) . ', ';
				$sql .= Datos::objectToDB($cliente->razonSocial) . ', ';
				$sql .= Datos::objectToDB($cliente->rubro->id) . ', ';
				$sql .= Datos::objectToDB($cliente->telefono1) . ', ';
				$sql .= Datos::objectToDB($cliente->interno1) . ', ';
				$sql .= Datos::objectToDB($cliente->habilitadoCae) . ', ';
				$sql .= Datos::objectToDB($cliente->vendedor->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Clientes SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($cliente->anulado) . ', ';
				$sql .= 'autorizado = ' . Datos::objectToDB($cliente->autorizado) . ', ';
				$sql .= 'cod_casa_central = ' . Datos::objectToDB($cliente->sucursalCentral->id) . ', ';
				$sql .= 'cod_casa_fiscal = ' . Datos::objectToDB($cliente->sucursalFiscal->id) . ', ';
				$sql .= 'cod_casa_cobranza = ' . Datos::objectToDB($cliente->sucursalCobranza->id) . ', ';
				$sql .= 'cod_casa_entrega = ' . Datos::objectToDB($cliente->sucursalEntrega->id) . ', ';
				if ($cliente->calificacion == 0)
					$cliente->calificacion = null;
				$sql .= 'cod_calificacion = ' . Datos::objectToDB(Funciones::padLeft($cliente->calificacion, 2, '0')) . ', ';
				$sql .= 'email_1 = ' . Datos::objectToDB($cliente->cobranzaEmail1) . ', ';
				$sql .= 'email_2 = ' . Datos::objectToDB($cliente->cobranzaEmail2) . ', ';
				$sql .= 'observaciones_cobranza = ' . Datos::objectToDB($cliente->observacionesCobranza) . ', ';
				$sql .= 'observaciones_gestion_cobranza = ' . Datos::objectToDB($cliente->observacionesGestionCobranza) . ', ';
				$sql .= 'observaciones_vendedor = ' . Datos::objectToDB($cliente->observacionesVendedor) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($cliente->observaciones) . ', ';
				$sql .= 'marcas_comercializa = ' . Datos::objectToDB($cliente->marcasQueComercializa) . ', ';
				$sql .= 'ref_bancarias = ' . Datos::objectToDB($cliente->referenciasBancarias) . ', ';
				$sql .= 'ref_comerciales = ' . Datos::objectToDB($cliente->referenciasComerciales) . ', ';
				$sql .= 'tel_cobranza_1 = ' . Datos::objectToDB($cliente->cobranzaTelefono1) . ', ';
				$sql .= 'tel_cobranza_2 = ' . Datos::objectToDB($cliente->cobranzaTelefono2) . ', ';
				$sql .= 'tel_cobranza_3 = ' . Datos::objectToDB($cliente->cobranzaTelefono3) . ', ';
				$sql .= 'cod_cond_iva = ' . Datos::objectToDB($cliente->condicionIva->id) . ', ';
				$sql .= 'descuento_especial = ' . Datos::objectToDB($cliente->creditoDescuentoEspecial) . ', ';
				if ($cliente->calificacion != $cliente->calificacionOriginal)
					$sql .= 'fecha_calificacion = GETDATE(), ';
				$sql .= 'forma_pago = ' . Datos::objectToDB($cliente->creditoFormaDePago->id) . ', ';
				$sql .= 'limite_credito = ' . Datos::objectToDB($cliente->creditoLimite) . ', ';
				$sql .= 'plazo_maximo = ' . Datos::objectToDB($cliente->creditoPlazoMaximo) . ', ';
				$sql .= 'primera_entrega = ' . Datos::objectToDB($cliente->creditoPrimeraEntrega) . ', ';
				$sql .= 'cuit = ' . Datos::objectToDB($cliente->cuit) . ', ';
				$sql .= 'dni = ' . Datos::objectToDB($cliente->dni) . ', ';
				$sql .= 'denom_fantasia = ' . Datos::objectToDB($cliente->nombre) . ', ';
				$sql .= 'calle = ' . Datos::objectToDB($cliente->direccionCalle) . ', ';
				$sql .= 'cod_postal = ' . Datos::objectToDB($cliente->direccionCodigoPostal) . ', ';
				$sql .= 'oficina_depto = ' . Datos::objectToDB($cliente->direccionDepartamento) . ', ';
				$sql .= 'cod_localidad_nro = ' . Datos::objectToDB($cliente->direccionLocalidad->id) . ', ';
				$sql .= 'cod_localidad = ' . Datos::objectToDB(Funciones::padLeft($cliente->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= 'numero = ' . Datos::objectToDB($cliente->direccionNumero) . ', ';
				$sql .= 'cod_pais = ' . Datos::objectToDB($cliente->direccionPais->id) . ', ';
				$sql .= 'partido_departamento = ' . Datos::objectToDB($cliente->direccionPartidoDepartamento) . ', ';
				$sql .= 'piso = ' . Datos::objectToDB($cliente->direccionPiso) . ', ';
				$sql .= 'cod_provincia = ' . Datos::objectToDB($cliente->direccionProvincia->id) . ', ';
				$sql .= 'email = ' . Datos::objectToDB($cliente->email) . ', ';
				$sql .= 'cod_grupo_empresa = ' . Datos::objectToDB($cliente->grupoEmpresa->id) . ', ';
				$sql .= 'lista_aplicable = ' . Datos::objectToDB($cliente->listaAplicable) . ', ';
				$sql .= 'razon_social = ' . Datos::objectToDB($cliente->razonSocial) . ', ';
				$sql .= 'rubro = ' . Datos::objectToDB($cliente->rubro->id) . ', ';
				$sql .= 'telefono_1 = ' . Datos::objectToDB($cliente->telefono1) . ', ';
				$sql .= 'interno_1 = ' . Datos::objectToDB($cliente->interno1) . ', ';
				$sql .= 'habilitado_cae = ' . Datos::objectToDB($cliente->habilitadoCae) . ', ';
				$sql .= 'cod_vendedor = ' . Datos::objectToDB($cliente->vendedor->id) . ', ';
				$sql .= 'fecha_ultima_modificacion = GETDATE() ';
				$sql .= 'WHERE cod_cli = ' . Datos::objectToDB($cliente->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Clientes SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'autorizado = ' . Datos::objectToDB('S') . ', '; //Esto es para que no aparezca como "POR AUTORIZAR"
				$sql .= 'fecha_ultima_modificacion = GETDATE(), ';
				$sql .= 'fechaBaja = GETDATE() ';
				$sql .= 'WHERE cod_cli = ' . Datos::objectToDB($cliente->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_cli), 0) + 1 FROM clientes;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryClienteTodos(ClienteTodos $clienteTodos, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM clientes ';
				$sql .= 'WHERE cod_cli = ' . Datos::objectToDB($clienteTodos->id) . '; ';
			} else {
				$sql .= $this->mapperQueryCliente($clienteTodos, $modo);
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCobroChequeVentanilla(CobroChequeVentanilla $cobroChequeVentanilla, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM cobro_cheque_ventanilla_d ';
				$sql .= 'WHERE cod_cobro_cheque_ventanilla = ' . Datos::objectToDB($cobroChequeVentanilla->numero) . ' ';
				$sql .= 'AND entrada_salida = ' . Datos::objectToDB($cobroChequeVentanilla->entradaSalida) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($cobroChequeVentanilla->empresa) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO cobro_cheque_ventanilla_d (';
				$sql .= 'cod_cobro_cheque_ventanilla, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'importe_total, ';
				$sql .= 'entrada_salida';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($cobroChequeVentanilla->numero) . ', ';
				$sql .= Datos::objectToDB($cobroChequeVentanilla->empresa) . ', ';
				$sql .= Datos::objectToDB($cobroChequeVentanilla->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($cobroChequeVentanilla->importeTotal) . ', ';
				$sql .= Datos::objectToDB($cobroChequeVentanilla->entradaSalida);
				$sql .= '); ';
				//} elseif ($modo == Modos::update) {
				//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_cobro_cheque_ventanilla), 0) + 1 FROM cobro_cheque_ventanilla_d ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($cobroChequeVentanilla->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCobroChequeVentanillaCabecera(CobroChequeVentanillaCabecera $cobroChequeVentanillaCabecera, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM cobro_cheque_ventanilla_c ';
				$sql .= 'WHERE cod_cobro_cheque_ventanilla = ' . Datos::objectToDB($cobroChequeVentanillaCabecera->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($cobroChequeVentanillaCabecera->empresa) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO cobro_cheque_ventanilla_c (';
				$sql .= 'cod_cobro_cheque_ventanilla, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'cod_responsable, ';
				$sql .= 'fecha_documento, ';
				$sql .= 'fecha_alta';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($cobroChequeVentanillaCabecera->numero) . ', ';
				$sql .= Datos::objectToDB($cobroChequeVentanillaCabecera->empresa) . ', ';
				$sql .= Datos::objectToDB($cobroChequeVentanillaCabecera->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB($cobroChequeVentanillaCabecera->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB($cobroChequeVentanillaCabecera->responsable->idPersonal) . ', ';
				$sql .= Datos::objectToDB($cobroChequeVentanillaCabecera->fecha) . ', ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE cobro_cheque_ventanilla_c SET ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($cobroChequeVentanillaCabecera->asientoContable->id) . ', ';
				$sql .= 'observaciones = ' .Datos::objectToDB($cobroChequeVentanillaCabecera->observaciones) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_cobro_cheque_ventanilla = ' . Datos::objectToDB($cobroChequeVentanillaCabecera->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($cobroChequeVentanillaCabecera->empresa) . '; ';
				//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_cobro_cheque_ventanilla), 0) + 1 FROM cobro_cheque_ventanilla_c ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($cobroChequeVentanillaCabecera->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCobroChequeVentanillaTemporal(CobroChequeVentanillaTemporal $cobroChequeVentanillaTemporal, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM cobro_cheque_ventanilla_temporal ';
				$sql .= 'WHERE cod_cobro_cheque_vent_temp = ' . Datos::objectToDB($cobroChequeVentanillaTemporal->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO cobro_cheque_ventanilla_temporal (';
				$sql .= 'cod_cobro_cheque_vent_temp, ';
				$sql .= 'cod_caja, ';
				$sql .= 'cod_responsable, ';
				$sql .= 'fecha, ';
				$sql .= 'cheques, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'confirmado, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($cobroChequeVentanillaTemporal->id) . ', ';
				$sql .= Datos::objectToDB($cobroChequeVentanillaTemporal->caja->id) . ', ';
				$sql .= Datos::objectToDB($cobroChequeVentanillaTemporal->responsable->idPersonal) . ', ';
				$sql .= Datos::objectToDB($cobroChequeVentanillaTemporal->fecha) . ', ';
				$sql .= Datos::objectToDB($cobroChequeVentanillaTemporal->idCheques) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE cobro_cheque_ventanilla_temporal SET ';
				$sql .= 'cod_responsable = ' . Datos::objectToDB($cobroChequeVentanillaTemporal->responsable->idPersonal) . ', ';
				$sql .= 'cheques = ' . Datos::objectToDB($cobroChequeVentanillaTemporal->idCheques) . ', ';
				$sql .= 'confirmado = ' .Datos::objectToDB($cobroChequeVentanillaTemporal->confirmado) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_cobro_cheque_vent_temp = ' . Datos::objectToDB($cobroChequeVentanillaTemporal->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE cobro_cheque_ventanilla_temporal SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' .Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_cobro_cheque_vent_temp = ' . Datos::objectToDB($cobroChequeVentanillaTemporal->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_cobro_cheque_vent_temp), 0) + 1 FROM cobro_cheque_ventanilla_temporal;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryColor(Color $color, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Colores_materias_primas ';
				$sql .= 'WHERE cod_color = ' . Datos::objectToDB($color->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Colores_materias_primas (';
				$sql .= 'cod_color, ';
				$sql .= 'denom_color, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_ultima_modificacion ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($color->id) . ', ';
				$sql .= Datos::objectToDB($color->nombre) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Colores_materias_primas SET ';
				$sql .= 'denom_color = ' . Datos::objectToDB($color->nombre) . ', ';
				$sql .= 'fecha_ultima_modificacion = GETDATE() ';
				$sql .= 'WHERE cod_color = ' . Datos::objectToDB($color->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Colores_materias_primas SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_ultima_modificacion = GETDATE() ';
				$sql .= 'WHERE cod_color = ' . Datos::objectToDB($color->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryColorMateriaPrima(ColorMateriaPrima $colorMateriaPrima, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM materias_primas_v ';
				$sql .= 'WHERE cod_color = ' . Datos::objectToDB($colorMateriaPrima->idColor) . ' ';
				$sql .= 'AND cod_material = ' . Datos::objectToDB(Funciones::padLeft($colorMateriaPrima->idMaterial, 4)) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Materias_primas (';
				$sql .= 'cod_material, ';
				$sql .= 'cod_color, ';
				$sql .= 'abrev_color, ';
				$sql .= 'anulado, ';
				$sql .= 'precio_unitario, ';
				$sql .= 'precio_venta_unitario, ';
				$sql .= 'autor_ultima_modificacion ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($colorMateriaPrima->material->id) . ', ';
				$sql .= Datos::objectToDB($colorMateriaPrima->idColor) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($colorMateriaPrima->nombreColor) . ', ';
				$sql .= Datos::objectToDB($colorMateriaPrima->precioUnitario) . ', ';
				$sql .= Datos::objectToDB($colorMateriaPrima->precioVentaUnitario) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Materias_primas SET ';
				$sql .= 'fecha_ultima_modificacion = GETDATE(), ';
				$sql .= 'precio_unitario = ' . Datos::objectToDB($colorMateriaPrima->precioUnitario) . ', ';
				$sql .= 'precio_venta_unitario = ' . Datos::objectToDB($colorMateriaPrima->precioVentaUnitario) . ', ';
				$sql .= 'autor_ultima_modificacion = ' . Datos::objectToDB(Usuario::logueado()->id) . ' ';
				$sql .= 'WHERE cod_color = ' . Datos::objectToDB($colorMateriaPrima->idColor) . ' ';
				$sql .= 'AND cod_material = ' . Datos::objectToDB(Funciones::padLeft($colorMateriaPrima->idMaterial, 4)) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Materias_primas SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_color = ' . Datos::objectToDB($colorMateriaPrima->idColor) . ' ';
				$sql .= 'AND cod_material = ' . Datos::objectToDB(Funciones::padLeft($colorMateriaPrima->idMaterial, 4)) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryColorPorArticulo(ColorPorArticulo $colorPorArticulo, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM colores_por_articulo_v ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB(Funciones::toString($colorPorArticulo->idArticulo)) . ' ';
				$sql .= 'AND cod_color_articulo = ' . Datos::objectToDB($colorPorArticulo->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO colores_por_articulo (';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				$sql .= 'cod_color, ';
				$sql .= 'aprob_disenio, ';
				$sql .= 'aprob_produccion, ';
				$sql .= 'denom_color, ';
				$sql .= 'denom_color_abreviada, ';
				$sql .= 'vigente, ';
				$sql .= 'fechaAlta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($colorPorArticulo->articulo->id) . ', ';
				$sql .= Datos::objectToDB($colorPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($colorPorArticulo->color->id) . ', ';
				$sql .= Datos::objectToDB('S') . ', ';
				$sql .= Datos::objectToDB('S') . ', ';
				$sql .= Datos::objectToDB($colorPorArticulo->color->nombre) . ', ';
				$sql .= Datos::objectToDB($colorPorArticulo->color->id) . ', ';
				$sql .= Datos::objectToDB('S') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'DELETE FROM curvas_por_articulo ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB($colorPorArticulo->articulo->id) . ' ';
				$sql .= 'AND cod_color_articulo = ' . Datos::objectToDB($colorPorArticulo->id) . '; ';
				$sql .= 'UPDATE colores_por_articulo SET ';
				$sql .= 'categoria_usuario = ' . Datos::objectToDB($colorPorArticulo->categoriaCalzadoUsuario->id) . ', ';
				$sql .= 'id_tipo_producto_stock = ' . Datos::objectToDB(Funciones::padLeft($colorPorArticulo->tipoProductoStock->id, 2, '0')) . ', ';
				$sql .= 'comercializacion_libre = ' . Datos::objectToDB($colorPorArticulo->formaDeComercializacion) . ', ';
				$sql .= 'denom_color = ' . Datos::objectToDB($colorPorArticulo->nombre) . ', ';
				$sql .= 'precio_distrib = ' . Datos::objectToDB(Funciones::toFloat($colorPorArticulo->precioDistribuidor)) . ', ';
				$sql .= 'precio_distrib_minorista = ' . Datos::objectToDB(Funciones::toFloat($colorPorArticulo->precioDistribuidorMinorista)) . ', ';
				$sql .= 'precio_mayorista_usd = ' . Datos::objectToDB(Funciones::toFloat($colorPorArticulo->precioMayoristaDolar)) . ', ';
				$sql .= 'precio_minorista_usd = ' . Datos::objectToDB(Funciones::toFloat($colorPorArticulo->precioMinoristaDolar)) . ', ';
				$sql .= 'precio_recargado = ' . Datos::objectToDB(Funciones::toFloat($colorPorArticulo->precioRecargado)) . ', ';
				$sql .= 'fotografia = ' . Datos::objectToDB($colorPorArticulo->fotos[0]) . ', ';
				for ($i = 1; $i < 9; $i++)
					$sql .= 'fotografia' . $i . ' = ' . Datos::objectToDB($colorPorArticulo->fotos[$i]) . ', ';
				$sql .= 'zoom_lado_interno = ' . Datos::objectToDB($colorPorArticulo->fotos[9]) . ', ';
				$sql .= 'zoom_puntera = ' . Datos::objectToDB($colorPorArticulo->fotos[10]) . ', ';
				$sql .= 'zoom_caña = ' . Datos::objectToDB($colorPorArticulo->fotos[11]) . ', ';
				$sql .= 'zoom_talon = ' . Datos::objectToDB($colorPorArticulo->fotos[12]) . ', ';
				$sql .= 'ecommerce_existe = ' . Datos::objectToDB($colorPorArticulo->ecommerceExiste) . ', ';
				$sql .= 'ecommerce_fecha_ultima_sinc = ' . Datos::objectToDB($colorPorArticulo->ecommerceFechaUltimaSinc) . ', ';
				$sql .= 'ecommerce_nombre = ' . Datos::objectToDB($colorPorArticulo->ecommerceNombre) . ', ';
				$sql .= 'ecommerce_info = ' . Datos::objectToDB($colorPorArticulo->ecommerceInfo) . ', ';
				$sql .= 'ecommerce_forsale = ' . Datos::objectToDB($colorPorArticulo->ecommerceForSale) . ', ';
				$sql .= 'ecommerce_condition = ' . Datos::objectToDB($colorPorArticulo->ecommerceCondition) . ', ';
				$sql .= 'ecommerce_cod_category = ' . Datos::objectToDB($colorPorArticulo->ecommerceCategory->id) . ', ';
				$sql .= 'ecommerce_exclusive = ' . Datos::objectToDB($colorPorArticulo->ecommerceExclusive) . ', ';
				$sql .= 'ecommerce_featured = ' . Datos::objectToDB($colorPorArticulo->ecommerceFeatured) . ', ';
				$sql .= 'ecommerce_price1 = ' . Datos::objectToDB($colorPorArticulo->ecommercePrice1) . ', ';
				$sql .= 'ecommerce_price2 = ' . Datos::objectToDB($colorPorArticulo->ecommercePrice2) . ', ';
				$sql .= 'ecommerce_price3 = ' . Datos::objectToDB($colorPorArticulo->ecommercePrice3) . ', ';
				$sql .= 'ecommerce_image1 = ' . Datos::objectToDB($colorPorArticulo->ecommerceImage1) . ', ';
				$sql .= 'fechaUltimaMod = GETDATE() ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB($colorPorArticulo->articulo->id) . ' ';
				$sql .= 'AND cod_color_articulo = ' . Datos::objectToDB($colorPorArticulo->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE colores_por_articulo SET ';
				$sql .= 'fecha_de_baja = GETDATE(), ';
				$sql .= 'vigente = ' . Datos::objectToDB('N') . ' ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB($colorPorArticulo->articulo->id) . ' ';
				$sql .= 'AND cod_color_articulo = ' . Datos::objectToDB($colorPorArticulo->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryConcepto(Concepto $concepto, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM concepto ';
				$sql .= 'WHERE cod_concepto = ' . Datos::objectToDB($concepto->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO concepto (';
				$sql .= 'cod_concepto, ';
				$sql .= 'nombre, ';
				$sql .= 'descripcion, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($concepto->id) . ', ';
				$sql .= Datos::objectToDB($concepto->nombre) . ', ';
				$sql .= Datos::objectToDB($concepto->descripcion) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE concepto SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($concepto->nombre) . ', ';
				$sql .= 'descripcion = ' . Datos::objectToDB($concepto->descripcion) . ' ';
				$sql .= 'WHERE cod_concepto = ' . Datos::objectToDB($concepto->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE concepto SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_concepto = ' . Datos::objectToDB($concepto->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_concepto), 0) + 1 FROM concepto;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryConceptoRetencionGanancias(ConceptoRetencionGanancias $conceptoRetencionGanancias, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM concepto_retencion_ganancias ';
				$sql .= 'WHERE cod_concepto_reten_ganan = ' . Datos::objectToDB($conceptoRetencionGanancias->id) . '; ';
			//} elseif ($modo == Modos::insert) {
			//} elseif ($modo == Modos::update) {
			//} elseif ($modo == Modos::delete) {
			//} elseif ($modo == Modos::id) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCondicionIva(CondicionIva $condicionIva, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM condiciones_iva ';
				$sql .= 'WHERE denom_cond_iva = ' . Datos::objectToDB($condicionIva->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO condiciones_iva (';
				$sql .= 'cod_cond_iva, ';
				$sql .= 'denom_cond_iva, ';
				$sql .= 'anulado, ';
				$sql .= 'denom_completa, ';
				$sql .= 'letra_factura, ';
				$sql .= 'letra_factura_proveedor, ';
				for ($i = 1; $i <= 5; $i++)
					$sql .= 'valor_impuesto_' . $i . ', ';
				$sql .= 'tratamiento ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($condicionIva->id) . ', ';
				$sql .= Datos::objectToDB($condicionIva->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($condicionIva->nombre) . ', ';
				$sql .= Datos::objectToDB($condicionIva->letraFactura) . ', ';
				$sql .= Datos::objectToDB($condicionIva->letraFacturaProveedor) . ', ';
				for ($i = 1; $i <= 5; $i++)
					$sql .= Datos::objectToDB($condicionIva->porcentajes[$i]) . ', ';
				$sql .= Datos::objectToDB($condicionIva->tratamiento) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE condiciones_iva SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($condicionIva->anulado) . ', ';
				$sql .= 'denom_completa = ' . Datos::objectToDB($condicionIva->nombre) . ', ';
				$sql .= 'letra_factura = ' . Datos::objectToDB($condicionIva->letraFactura) . ', ';
				$sql .= 'letra_factura_proveedor = ' . Datos::objectToDB($condicionIva->letraFacturaProveedor) . ', ';
				for ($i = 1; $i <= 5; $i++)
					$sql .= 'valor_impuesto_' . $i . ' = ' . Datos::objectToDB($condicionIva->porcentajes[$i]) . ', ';
				$sql .= 'tratamiento = ' . Datos::objectToDB($condicionIva->tratamiento) . ' ';
				$sql .= 'WHERE denom_cond_iva = ' . Datos::objectToDB($condicionIva->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE condiciones_iva SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE denom_cond_iva = ' . Datos::objectToDB($condicionIva->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryConfirmacionStock(ConfirmacionStock $confirmacionStock, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM confirmaciones_stock ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($confirmacionStock->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO confirmaciones_stock (';
				$sql .= 'id, ';
				$sql .= 'cod_orden_fabricacion, ';
				$sql .= 'numero_tarea, ';
				$sql .= 'cod_seccion_produccion, ';
				$sql .= 'cantidad, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($confirmacionStock->id) . ', ';
				$sql .= Datos::objectToDB($confirmacionStock->tareaProduccionItem->idOrdenDeFabricacion) . ', ';
				$sql .= Datos::objectToDB($confirmacionStock->tareaProduccionItem->numeroTarea) . ', ';
				$sql .= Datos::objectToDB($confirmacionStock->tareaProduccionItem->idSeccionProduccion) . ', ';
				$sql .= Datos::objectToDB($confirmacionStock->cantidadTotal) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($confirmacionStock->cantidad[$i]) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE confirmaciones_stock SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' .Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($confirmacionStock->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(id), 0) + 1 FROM confirmaciones_stock;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryContacto(Contacto $contacto, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Contactos ';
				$sql .= 'WHERE cod_contacto = ' . Datos::objectToDB($contacto->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Contactos (';
				$sql .= 'cod_cliente, ';
				$sql .= 'cod_sucursal, ';
				$sql .= 'cod_proveedor, ';
				$sql .= 'tipo, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_area_empresa, ';
				$sql .= 'nombre, ';
				$sql .= 'apellido, ';
				$sql .= 'celular, ';
				$sql .= 'telefono1, ';
				$sql .= 'interno1, ';
				$sql .= 'telefono2, ';
				$sql .= 'interno2, ';
				$sql .= 'email1, ';
				$sql .= 'email2, ';
				$sql .= 'calle, ';
				$sql .= 'numero, ';
				$sql .= 'piso, ';
				$sql .= 'departamento, ';
				$sql .= 'codigo_postal, ';
				$sql .= 'cod_localidad, ';
				$sql .= 'cod_localidad_nro, ';
				$sql .= 'cod_provincia, ';
				$sql .= 'cod_pais, ';
				$sql .= 'referencia, ';
				$sql .= 'observaciones ';
				$sql .= ') VALUES (';
				if ($contacto->tipo == 'C') {
					$sql .= Datos::objectToDB($contacto->cliente->id) . ', ';
					$sql .= Datos::objectToDB($contacto->sucursal->id) . ', ';
				} else {
					$sql .= 'NULL' . ', ';
					$sql .= 'NULL' . ', ';
				}
				if ($contacto->tipo == 'P')
					$sql .= Datos::objectToDB($contacto->proveedor->id) . ', ';
				else
					$sql .= 'NULL' . ', ';
				$sql .= Datos::objectToDB($contacto->tipo) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($contacto->idAreaEmpresa) . ', ';
				$sql .= Datos::objectToDB($contacto->nombre) . ', ';
				$sql .= Datos::objectToDB($contacto->apellido) . ', ';
				$sql .= Datos::objectToDB($contacto->celular) . ', ';
				$sql .= Datos::objectToDB($contacto->telefono1) . ', ';
				$sql .= Datos::objectToDB($contacto->interno1) . ', ';
				$sql .= Datos::objectToDB($contacto->telefono2) . ', ';
				$sql .= Datos::objectToDB($contacto->interno2) . ', ';
				$sql .= Datos::objectToDB($contacto->email1) . ', ';
				$sql .= Datos::objectToDB($contacto->email2) . ', ';
				$sql .= Datos::objectToDB($contacto->direccionCalle) . ', ';
				$sql .= Datos::objectToDB($contacto->direccionNumero) . ', ';
				$sql .= Datos::objectToDB($contacto->direccionPiso) . ', ';
				$sql .= Datos::objectToDB($contacto->direccionDepartamento) . ', ';
				$sql .= Datos::objectToDB($contacto->direccionCodigoPostal) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($contacto->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= Datos::objectToDB($contacto->direccionLocalidad->id) . ', ';
				$sql .= Datos::objectToDB($contacto->direccionProvincia->id) . ', ';
				$sql .= Datos::objectToDB($contacto->direccionPais->id) . ', ';
				$sql .= Datos::objectToDB($contacto->referencia) . ', ';
				$sql .= Datos::objectToDB($contacto->observaciones) . ' ';
				$sql .= '); ';
				$sql .= 'SELECT @@IDENTITY; ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Contactos SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($contacto->anulado) . ', ';
				$sql .= 'cod_area_empresa = ' . Datos::objectToDB($contacto->idAreaEmpresa) . ', ';
				$sql .= 'nombre = ' . Datos::objectToDB($contacto->nombre) . ', ';
				$sql .= 'apellido = ' . Datos::objectToDB($contacto->apellido) . ', ';
				$sql .= 'celular = ' .Datos::objectToDB($contacto->celular) . ', ';
				$sql .= 'telefono1 = ' . Datos::objectToDB($contacto->telefono1) . ', ';
				$sql .= 'interno1 = ' . Datos::objectToDB($contacto->interno1) . ', ';
				$sql .= 'telefono2 = ' . Datos::objectToDB($contacto->telefono2) . ', ';
				$sql .= 'interno2 = ' . Datos::objectToDB($contacto->interno2) . ', ';
				$sql .= 'email1 = ' . Datos::objectToDB($contacto->email1) . ', ';
				$sql .= 'email2 = ' . Datos::objectToDB($contacto->email2) . ', ';
				$sql .= 'calle = ' . Datos::objectToDB($contacto->direccionCalle) . ', ';
				$sql .= 'numero = ' . Datos::objectToDB($contacto->direccionNumero) . ', ';
				$sql .= 'piso = ' . Datos::objectToDB($contacto->direccionPiso) . ', ';
				$sql .= 'departamento = ' . Datos::objectToDB($contacto->direccionDepartamento) . ', ';
				$sql .= 'codigo_postal = ' . Datos::objectToDB($contacto->direccionCodigoPostal) . ', ';
				$sql .= 'cod_localidad_nro = ' . Datos::objectToDB($contacto->direccionLocalidad->id) . ', ';
				$sql .= 'cod_localidad = ' . Datos::objectToDB(Funciones::padLeft($contacto->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= 'cod_provincia = ' . Datos::objectToDB($contacto->direccionProvincia->id) . ', ';
				$sql .= 'cod_pais = ' . Datos::objectToDB($contacto->direccionPais->id) . ', ';
				$sql .= 'referencia = ' . Datos::objectToDB($contacto->referencia) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($contacto->observaciones) . ' ';
				$sql .= 'WHERE cod_contacto = ' . Datos::objectToDB($contacto->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Contactos SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_contacto = ' . Datos::objectToDB($contacto->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT IDENT_CURRENT(\'contactos\') + IDENT_INCR(\'contactos\');';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryConjunto(Conjunto $conjunto, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM conjuntos ';
				$sql .= 'WHERE conjunto = ' . Datos::objectToDB($conjunto->id) . '; ';
			// } elseif ($modo == Modos::insert) {
			// } elseif ($modo == Modos::update) {
			// } elseif ($modo == Modos::delete) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
    private function mapperQueryConsumoStockMP(ConsumoStockMP $consumoStockMP, $modo){
        $sql = '';
        try {
            if ($modo == Modos::select){
                $sql .= 'SELECT * ';
                $sql .= 'FROM consumos_stock_mp ';
                $sql .= 'WHERE id = ' . Datos::objectToDB($consumoStockMP->id) . '; ';
            } elseif ($modo == Modos::insert) {
                $sql .= 'INSERT INTO consumos_stock_mp (';
                $sql .= 'id, ';
                $sql .= 'cod_almacen, ';
                $sql .= 'cod_material, ';
                $sql .= 'cod_color, ';
                $sql .= 'cantidad, ';
                for ($i = 1; $i <= 10; $i++)
                    $sql .= 'cant_' . $i . ', ';
                $sql .= 'cod_usuario, ';
                $sql .= 'fecha_alta ';
                $sql .= ') VALUES (';
                $sql .= Datos::objectToDB($consumoStockMP->id) . ', ';
                $sql .= Datos::objectToDB($consumoStockMP->almacen->id) . ', ';
                $sql .= Datos::objectToDB($consumoStockMP->material->id) . ', ';
                $sql .= Datos::objectToDB($consumoStockMP->colorMateriaPrima->idColor) . ', ';
                $sql .= Datos::objectToDB($consumoStockMP->cantidadTotal) . ', ';
                for ($i = 1; $i <= 10; $i++)
                    $sql .= Datos::objectToDB($consumoStockMP->cantidad[$i]) . ', ';
                $sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
                $sql .= 'GETDATE() ';
                $sql .= '); ';
                //} elseif ($modo == Modos::update) {
                //} elseif ($modo == Modos::delete) {
            } elseif ($modo == Modos::id) {
                $sql .= 'SELECT ISNULL(MAX(id), 0) + 1 FROM consumos_stock_mp; ';
            } else {
                throw new FactoryException('Modo incorrecto');
            }
            return $sql;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
	private function mapperQueryCuentaBancaria(CuentaBancaria $cuentaBancaria, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM cuenta_bancaria ';
				$sql .= 'WHERE cod_cuenta_bancaria = ' . Datos::objectToDB($cuentaBancaria->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO cuenta_bancaria (';
				$sql .= 'cod_cuenta_bancaria, ';
				$sql .= 'cod_banco, ';
				$sql .= 'cod_sucursal_banco, ';
				$sql .= 'cod_caja, ';
				$sql .= 'cod_proveedor, ';
				$sql .= 'cod_imputacion, ';
				$sql .= 'numero_cuenta, ';
				$sql .= 'nombre_cuenta, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($cuentaBancaria->id) . ', ';
				$sql .= Datos::objectToDB($cuentaBancaria->banco->idBanco) . ', ';
				$sql .= Datos::objectToDB($cuentaBancaria->sucursal->idSucursal) . ', ';
				$sql .= Datos::objectToDB($cuentaBancaria->caja->id) . ', ';
				$sql .= Datos::objectToDB($cuentaBancaria->proveedor->id) . ', ';
				$sql .= Datos::objectToDB($cuentaBancaria->imputacion->id) . ', ';
				$sql .= Datos::objectToDB($cuentaBancaria->numeroCuenta) . ', ';
				$sql .= Datos::objectToDB($cuentaBancaria->nombreCuenta) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE cuenta_bancaria SET ';
				$sql .= 'numero_cuenta = ' .Datos::objectToDB($cuentaBancaria->numeroCuenta) . ', ';
				$sql .= 'nombre_cuenta = ' . Datos::objectToDB($cuentaBancaria->nombreCuenta) . ', ';
				$sql .= 'cod_caja = ' . Datos::objectToDB($cuentaBancaria->caja->id) . ', ';
				$sql .= 'cod_proveedor = ' . Datos::objectToDB($cuentaBancaria->proveedor->id) . ', ';
				$sql .= 'cod_imputacion = ' . Datos::objectToDB($cuentaBancaria->imputacion->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_cuenta_bancaria = ' . Datos::objectToDB($cuentaBancaria->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE cuenta_bancaria SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_cuenta_bancaria = ' . Datos::objectToDB($cuentaBancaria->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_cuenta_bancaria), 0) + 1 FROM cuenta_bancaria;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCuentaCorrienteHistorica(/** @noinspection PhpUnusedParameterInspection */ CuentaCorrienteHistorica $cuentaCorrienteHistorica, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT 1;';
			//} elseif ($modo == Modos::insert) {
			//} elseif ($modo == Modos::update) {
			//} elseif ($modo == Modos::delete) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCuentaCorrienteHistoricaProveedor(/** @noinspection PhpUnusedParameterInspection */ CuentaCorrienteHistoricaProveedor $cuentaCorrienteHistoricaProveedor, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT 1;';
				//} elseif ($modo == Modos::insert) {
				//} elseif ($modo == Modos::update) {
				//} elseif ($modo == Modos::delete) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCuentaCorrienteHistoricaDocumento( $cuentaCorrienteHistoricaDocumento, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM cuenta_corriente_historica ';
				$sql .= 'WHERE cod_cliente = ' . Datos::objectToDB($cuentaCorrienteHistoricaDocumento->idCliente) . ' ';
				if ($cuentaCorrienteHistoricaDocumento->empresa == 1 || $cuentaCorrienteHistoricaDocumento->empresa == 2)
					$sql .= 'AND empresa = ' . Datos::objectToDB($cuentaCorrienteHistoricaDocumento->empresa) . ' ';
				$sql .= '; ';
				//} elseif ($modo == Modos::insert) {
				//} elseif ($modo == Modos::update) {
				//} elseif ($modo == Modos::delete) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCuentaCorrienteHistoricaDocumentoProveedor(CuentaCorrienteHistoricaDocumentoProveedor $cuentaCorrienteHistoricaDocumentoProveedor, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM cuenta_corriente_historica_proveedor_v ';
				$sql .= 'WHERE cod_proveedor = ' . Datos::objectToDB($cuentaCorrienteHistoricaDocumentoProveedor->idProveedor) . ' ';
				if ($cuentaCorrienteHistoricaDocumentoProveedor->empresa == 1 || $cuentaCorrienteHistoricaDocumentoProveedor->empresa == 2)
					$sql .= 'AND empresa = ' . Datos::objectToDB($cuentaCorrienteHistoricaDocumentoProveedor->empresa) . ' ';
				$sql .= '; ';
				//} elseif ($modo == Modos::insert) {
				//} elseif ($modo == Modos::update) {
				//} elseif ($modo == Modos::delete) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCurva(Curva $curva, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM curvas ';
				$sql .= 'WHERE cod_curva = ' . Datos::objectToDB($curva->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO curvas (';
				$sql .= 'tipo_curva, ';
				$sql .= 'anulado, ';
				for ($i = 1; $i < 16; $i++)
					$sql .= 'pos_' . $i . ', ';
				$sql .= 'denom_curva ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($curva->tipoDeCurva) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				for ($i = 1; $i < 16; $i++)
					$sql .= Datos::objectToDB($curva->cantidad[$i]) . ', ';
				$sql .= Datos::objectToDB($curva->nombre) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE modulos_basicos SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($curva->anulado) . ', ';
				for ($i = 1; $i < 16; $i++)
					$sql .= 'pos_' . $i . ' = ' . Datos::objectToDB($curva->cantidad[$i]) . ', ';
				$sql .= 'denom_curva = ' . Datos::objectToDB($curva->nombre) . ' ';
				$sql .= 'WHERE cod_curva = ' . Datos::objectToDB($curva->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE curvas SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_curva = ' . Datos::objectToDB($curva->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT IDENT_CURRENT(\'curvas\') + IDENT_INCR(\'curvas\');';
				
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCurvaPorArticulo(CurvaPorArticulo $curvaPorArticulo, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM curvas_por_articulo ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB(Funciones::toString($curvaPorArticulo->idArticulo)) . ' ';
				$sql .= 'AND cod_color_articulo = ' . Datos::objectToDB($curvaPorArticulo->idColorPorArticulo) . ' ';
				$sql .= 'AND cod_curva = ' . Datos::objectToDB($curvaPorArticulo->idCurva) . '; ';
			} elseif (($modo == Modos::insert) || ($modo == Modos::update)) {
				$sql .= 'INSERT INTO curvas_por_articulo (';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				$sql .= 'cod_curva ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($curvaPorArticulo->articulo->id) . ', ';
				$sql .= Datos::objectToDB($curvaPorArticulo->colorPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($curvaPorArticulo->curva->id) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM curvas_por_articulo ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB($curvaPorArticulo->articulo->id) . ' ';
				$sql .= 'AND cod_color_articulo = ' . Datos::objectToDB($curvaPorArticulo->colorPorArticulo->id) . ' ';
				$sql .= 'AND cod_curva = ' . Datos::objectToDB($curvaPorArticulo->curva->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryCurvaProduccionPorArticulo(CurvaProduccionPorArticulo $curvaProduccionPorArticulo, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM modulos_basicos ';
				$sql .= 'WHERE modulo_variante_nro = ' . Datos::objectToDB(Funciones::toString($curvaProduccionPorArticulo->id)) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO modulos_basicos (';
				$sql .= 'tipo_modulo, ';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_modulo, ';
				$sql .= 'denom_modulo, ';
                $sql .= 'total_modulo_pares, ';
                for ($i = 1; $i < 16; $i++)
                    $sql .= 'pos_' . $i . '_cant, ';
				$sql .= 'activo ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($curvaProduccionPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($curvaProduccionPorArticulo->articulo->id) . ', ';
				$sql .= Datos::objectToDB($curvaProduccionPorArticulo->orden) . ', ';
				$sql .= Datos::objectToDB($curvaProduccionPorArticulo->nombre) . ', ';
				$sql .= Datos::objectToDB($curvaProduccionPorArticulo->cantidadTotal) . ', ';
                for ($i = 1; $i < 10; $i++)
                    $sql .= Datos::objectToDB($curvaProduccionPorArticulo->cantidad[$i]) . ', ';
				$sql .= Datos::objectToDB('S') . ' ';
				$sql .= '); ';
            } elseif ($modo == Modos::update) {
                $sql .= 'UPDATE modulos_basicos SET ';
                $sql .= 'total_modulo_pares = ' . Datos::objectToDB($curvaProduccionPorArticulo->cantidadTotal) . ', ';
                for ($i = 1; $i < 10; $i++)
                    $sql .= 'pos_' . $i . '_cant = ' . Datos::objectToDB($curvaProduccionPorArticulo->cantidad[$i]) . ', ';
                $sql .= 'denom_modulo = ' . Datos::objectToDB($curvaProduccionPorArticulo->nombre) . ' ';
                $sql .= 'WHERE modulo_variante_nro = ' . Datos::objectToDB($curvaProduccionPorArticulo->id) . '; ';
			} elseif ($modo == Modos::delete) {
                $sql .= 'UPDATE modulos_basicos SET ';
                $sql .= 'activo = ' . Datos::objectToDB('N') . ' ';
                $sql .= 'WHERE modulo_variante_nro = ' . Datos::objectToDB($curvaProduccionPorArticulo->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
    private function mapperQueryDebitarCheque(DebitarCheque $debitarCheque, $modo){
        return $this->mapperQueryAcreditarDebitarCheque($debitarCheque, $modo);
    }
    private function mapperQueryDebitarChequeCabecera(DebitarChequeCabecera $debitarChequeCabecera, $modo){
        return $this->mapperQueryAcreditarDebitarChequeCabecera($debitarChequeCabecera, $modo);
    }
    private function mapperQueryDepositoBancario(DepositoBancario $depositoCheque, $modo){
        $sql = '';
        try {
            if ($modo == Modos::select){
                $sql .= 'SELECT * ';
                $sql .= 'FROM deposito_bancario_d ';
                $sql .= 'WHERE cod_deposito_bancario = ' . Datos::objectToDB($depositoCheque->numero) . ' ';
                $sql .= 'AND entrada_salida = ' . Datos::objectToDB($depositoCheque->entradaSalida) . ' ';
                $sql .= 'AND empresa = ' . Datos::objectToDB($depositoCheque->empresa) . '; ';
            } elseif ($modo == Modos::insert) {
                $sql .= 'INSERT INTO deposito_bancario_d (';
                $sql .= 'cod_deposito_bancario, ';
                $sql .= 'empresa, ';
                $sql .= 'cod_importe_operacion, ';
                $sql .= 'importe_total, ';
                $sql .= 'entrada_salida';
                $sql .= ') VALUES (';
                $sql .= Datos::objectToDB($depositoCheque->numero) . ', ';
                $sql .= Datos::objectToDB($depositoCheque->empresa) . ', ';
                $sql .= Datos::objectToDB($depositoCheque->importePorOperacion->idImportePorOperacion) . ', ';
                $sql .= Datos::objectToDB($depositoCheque->importeTotal) . ', ';
                $sql .= Datos::objectToDB($depositoCheque->entradaSalida);
                $sql .= '); ';
                //} elseif ($modo == Modos::update) {
                //} elseif ($modo == Modos::delete) {
            } elseif ($modo == Modos::id) {
                $sql .= 'SELECT ISNULL(MAX(cod_deposito_bancario), 0) + 1 FROM deposito_bancario_d ';
                $sql .= 'WHERE empresa = ' . Datos::objectToDB($depositoCheque->empresa) . ';';
            } else {
                throw new FactoryException('Modo incorrecto');
            }
            return $sql;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    private function mapperQueryDepositoBancarioCabecera(DepositoBancarioCabecera $depositoChequeCabecera, $modo){
        $sql = '';
        try {
            if ($modo == Modos::select){
                $sql .= 'SELECT * ';
                $sql .= 'FROM deposito_bancario_c ';
                $sql .= 'WHERE cod_deposito_bancario = ' . Datos::objectToDB($depositoChequeCabecera->numero) . ' ';
                $sql .= 'AND empresa = ' . Datos::objectToDB($depositoChequeCabecera->empresa) . '; ';
            } elseif ($modo == Modos::insert) {
                $sql .= 'INSERT INTO deposito_bancario_c (';
                $sql .= 'cod_deposito_bancario, ';
                $sql .= 'empresa, ';
                $sql .= 'numero_transaccion, ';
                $sql .= 'venta_cheques, ';
                $sql .= 'cod_asiento_contable, ';
                $sql .= 'observaciones, ';
                $sql .= 'cod_usuario, ';
                $sql .= 'fecha_documento, ';
                $sql .= 'fecha_alta';
                $sql .= ') VALUES (';
                $sql .= Datos::objectToDB($depositoChequeCabecera->numero) . ', ';
                $sql .= Datos::objectToDB($depositoChequeCabecera->empresa) . ', ';
                $sql .= Datos::objectToDB($depositoChequeCabecera->numeroTransaccion) . ', ';
                $sql .= Datos::objectToDB($depositoChequeCabecera->ventaCheque) . ', ';
                $sql .= Datos::objectToDB($depositoChequeCabecera->asientoContable->id) . ', ';
                $sql .= Datos::objectToDB($depositoChequeCabecera->observaciones) . ', ';
                $sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
                $sql .= Datos::objectToDB($depositoChequeCabecera->fecha) . ', ';
                $sql .= 'GETDATE()';
                $sql .= '); ';
            } elseif ($modo == Modos::update) {
                $sql .= 'UPDATE deposito_bancario_c SET ';
                $sql .= 'cod_asiento_contable = ' . Datos::objectToDB($depositoChequeCabecera->asientoContable->id) . ', ';
                $sql .= 'observaciones = ' .Datos::objectToDB($depositoChequeCabecera->observaciones) . ', ';
                $sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
                $sql .= 'fecha_ultima_mod = GETDATE() ';
                $sql .= 'WHERE cod_deposito_bancario = ' . Datos::objectToDB($depositoChequeCabecera->numero) . ' ';
                $sql .= 'AND empresa = ' . Datos::objectToDB($depositoChequeCabecera->empresa) . '; ';
                //} elseif ($modo == Modos::delete) {
            } elseif ($modo == Modos::id) {
                $sql .= 'SELECT ISNULL(MAX(cod_deposito_bancario), 0) + 1 FROM deposito_bancario_c ';
                $sql .= 'WHERE empresa = ' . Datos::objectToDB($depositoChequeCabecera->empresa) . ';';
            } else {
                throw new FactoryException('Modo incorrecto');
            }
            return $sql;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    private function mapperQueryDepositoBancarioTemporal(DepositoBancarioTemporal $depositoBancarioTemporal, $modo){
        $sql = '';
        try {
            if ($modo == Modos::select){
                $sql .= 'SELECT * ';
                $sql .= 'FROM deposito_bancario_temporal ';
                $sql .= 'WHERE cod_deposito_bancario_temporal = ' . Datos::objectToDB($depositoBancarioTemporal->id) . '; ';
            } elseif ($modo == Modos::insert) {
                $sql .= 'INSERT INTO deposito_bancario_temporal (';
                $sql .= 'cod_deposito_bancario_temporal, ';
                $sql .= 'cod_caja, ';
                $sql .= 'cod_cuenta_bancaria, ';
                $sql .= 'fecha, ';
                $sql .= 'venta_cheques, ';
                $sql .= 'numero_boleta, ';
                $sql .= 'importe_efectivo, ';
                $sql .= 'cheques, ';
                $sql .= 'cod_usuario, ';
                $sql .= 'confirmado, ';
                $sql .= 'anulado, ';
                $sql .= 'fecha_alta';
                $sql .= ') VALUES (';
                $sql .= Datos::objectToDB($depositoBancarioTemporal->id) . ', ';
                $sql .= Datos::objectToDB($depositoBancarioTemporal->caja->id) . ', ';
                $sql .= Datos::objectToDB($depositoBancarioTemporal->cuentaBancaria->id) . ', ';
                $sql .= Datos::objectToDB($depositoBancarioTemporal->fecha) . ', ';
                $sql .= Datos::objectToDB($depositoBancarioTemporal->ventaCheque) . ', ';
                $sql .= Datos::objectToDB($depositoBancarioTemporal->numeroBoleta) . ', ';
                $sql .= Datos::objectToDB($depositoBancarioTemporal->efectivo) . ', ';
                $sql .= Datos::objectToDB($depositoBancarioTemporal->idCheques) . ', ';
                $sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
                $sql .= Datos::objectToDB('N') . ', ';
                $sql .= Datos::objectToDB('N') . ', ';
                $sql .= 'GETDATE() ';
                $sql .= '); ';
            } elseif ($modo == Modos::update) {
                $sql .= 'UPDATE deposito_bancario_temporal SET ';
                $sql .= 'cod_cuenta_bancaria = ' . Datos::objectToDB($depositoBancarioTemporal->cuentaBancaria->id) . ', ';
                $sql .= 'fecha = ' . Datos::objectToDB($depositoBancarioTemporal->fecha) . ', ';
                $sql .= 'venta_cheques = ' . Datos::objectToDB($depositoBancarioTemporal->ventaCheque) . ', ';
                $sql .= 'numero_boleta = ' . Datos::objectToDB($depositoBancarioTemporal->numeroBoleta) . ', ';
                $sql .= 'importe_efectivo = ' . Datos::objectToDB($depositoBancarioTemporal->efectivo) . ', ';
                $sql .= 'cheques = ' . Datos::objectToDB($depositoBancarioTemporal->idCheques) . ', ';
                $sql .= 'confirmado = ' .Datos::objectToDB($depositoBancarioTemporal->confirmado) . ', ';
                $sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
                $sql .= 'fecha_ultima_mod = GETDATE() ';
                $sql .= 'WHERE cod_deposito_bancario_temporal = ' . Datos::objectToDB($depositoBancarioTemporal->id) . '; ';
            } elseif ($modo == Modos::delete) {
                $sql .= 'UPDATE deposito_bancario_temporal SET ';
                $sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
                $sql .= 'anulado = ' .Datos::objectToDB('S') . ', ';
                $sql .= 'fecha_baja = GETDATE() ';
                $sql .= 'WHERE cod_deposito_bancario_temporal = ' . Datos::objectToDB($depositoBancarioTemporal->id) . '; ';
            } elseif ($modo == Modos::id) {
                $sql .= 'SELECT ISNULL(MAX(cod_deposito_bancario_temporal), 0) + 1 FROM deposito_bancario_temporal;';
            } else {
                throw new FactoryException('Modo incorrecto');
            }
            return $sql;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
	private function mapperQueryDespacho(Despacho $despacho, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM despachos_c ';
				$sql .= 'WHERE nro_despacho = ' . Datos::objectToDB($despacho->numero) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO despachos_c (';
				$sql .= 'nro_despacho, ';
				$sql .= 'empresa, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_cliente, ';
				$sql .= 'cod_sucursal, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'cantidad, ';
				$sql .= 'pendiente, ';
				$sql .= 'cod_ecommerce_order, ';
				$sql .= 'observaciones, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($despacho->numero) . ', ';
				$sql .= Datos::objectToDB($despacho->empresa) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($despacho->cliente->id) . ', ';
				$sql .= Datos::objectToDB($despacho->sucursal->id) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB($despacho->cantidad) . ', ';
				$sql .= Datos::objectToDB($despacho->pendiente) . ', ';
				$sql .= Datos::objectToDB($despacho->ecommerceOrder->id) . ', ';
				$sql .= Datos::objectToDB($despacho->observaciones) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			} elseif ($modo == Modos::delete) {
				foreach ($despacho->detalle as $despachoItem) {
					/** @var $despachoItem DespachoItem */
					if ($despachoItem->remitido()) {
						//Si tiene remito no se puede borrar. Primero debe eliminarse el remito
						throw new FactoryException('No se puede borrar el despacho ya que uno de sus items pertenece a un remito. Debe borrar el remito número ' . $despachoItem->remitoNumero . ' primero');
					}
					$sql .= 'UPDATE despachos_d SET ';
					$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
					$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
					$sql .= 'fecha_baja = GETDATE() ';
					$sql .= 'WHERE nro_despacho = ' . Datos::objectToDB($despachoItem->despachoNumero) . ' ';
					$sql .= 'AND nro_item = ' . Datos::objectToDB($despachoItem->numeroDeItem) . '; ';
				}
				$sql .= 'UPDATE despachos_c SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE nro_despacho = ' . Datos::objectToDB($despacho->numero) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_despacho), 0) + 1 FROM despachos_c;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDespachoItem(DespachoItem $despachoItem, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM despachos_d_v ';
				$sql .= 'WHERE nro_despacho = ' . Datos::objectToDB($despachoItem->despachoNumero) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($despachoItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO despachos_d (';
				$sql .= 'nro_despacho, ';
				$sql .= 'nro_item, ';
				$sql .= 'empresa, ';
				$sql .= 'anulado, ';
				$sql .= 'nro_pedido, ';
				$sql .= 'nro_item_pedido, ';
				$sql .= 'cod_almacen, ';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				$sql .= 'precio_al_facturar, ';
				$sql .= 'descuento_pedido, ';
				$sql .= 'recargo_pedido, ';
				$sql .= 'iva_porc, ';
				$sql .= 'precio_unitario, ';
				$sql .= 'precio_unitario_final, ';
				$sql .= 'cantidad, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'fecha_alta, ';
				$sql .= 'fecha_ultima_mod ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($despachoItem->despachoNumero) . ', ';
				$sql .= Datos::objectToDB($despachoItem->numeroDeItem) . ', ';
				$sql .= Datos::objectToDB($despachoItem->empresa) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($despachoItem->pedido->numero) . ', ';
				$sql .= Datos::objectToDB($despachoItem->pedidoItem->numeroDeItem) . ', ';
				$sql .= Datos::objectToDB($despachoItem->almacen->id) . ', ';
				$sql .= Datos::objectToDB($despachoItem->articulo->id) . ', ';
				$sql .= Datos::objectToDB($despachoItem->colorPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($despachoItem->precioAlFacturar) . ', ';
				$sql .= Datos::objectToDB($despachoItem->descuentoPedido) . ', ';
				$sql .= Datos::objectToDB($despachoItem->recargoPedido) . ', ';
				$sql .= Datos::objectToDB($despachoItem->ivaPorcentaje) . ', ';
				$sql .= Datos::objectToDB($despachoItem->precioUnitario) . ', ';
				$sql .= Datos::objectToDB($despachoItem->precioUnitarioFinal) . ', ';
				$sql .= Datos::objectToDB(Funciones::sumaArray($despachoItem->cantidad)) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($despachoItem->cantidad[$i]) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE despachos_d SET ';
				$sql .= 'precio_unitario_facturar = ' . Datos::objectToDB($despachoItem->precioUnitarioFacturar) . ', ';
				$sql .= 'precio_unitario_facturar_final = ' . Datos::objectToDB($despachoItem->precioUnitarioFacturarFinal) . ', ';
				$sql .= 'nro_remito = ' . Datos::objectToDB($despachoItem->remito->numero) . ', ';
				$sql .= 'letra_remito = ' . Datos::objectToDB($despachoItem->remito->letra) . ' ';
				$sql .= 'WHERE nro_despacho = ' . Datos::objectToDB($despachoItem->despachoNumero) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($despachoItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::delete) {
				if ($despachoItem->remitido())
					throw new FactoryException('No se puede borrar el despacho porque pertenece a un remito');
				//Es necesario controlar que no estï¿½ anulado para que no sigan aumentï¿½ndose los predespachados
				//La otra opcion es bajar las cantidades del despacho_d a 0
				if ($despachoItem->anulado == 'S')
					throw new FactoryException('No se puede borrar el despacho porque ya está anulado');
				//Aumento la cantidad de predespachados del pedido
				$sql .= 'UPDATE predespachos SET ';
				$sql .= 'predespachados = predespachados + ' . Datos::objectToDB($despachoItem->cantidadTotal) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'pred_' . $i . ' = pred_' . $i . ' + ' . Datos::objectToDB($despachoItem->cantidad[$i]) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE nro_pedido = ' . Datos::objectToDB($despachoItem->pedidoNumero) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($despachoItem->pedidoNumeroDeItem) . '; ';
				//Anulo el despachoItem
				$sql .= 'UPDATE despachos_d SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE nro_despacho = ' . Datos::objectToDB($despachoItem->despachoNumero) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($despachoItem->numeroDeItem) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
    private function mapperQueryDevolucionACliente(DevolucionACliente $devolucionACliente, $modo){
        $sql = '';
        try {
            if ($modo == Modos::select){
                $sql .= 'SELECT * ';
                $sql .= 'FROM devoluciones_a_cliente_c ';
                $sql .= 'WHERE cod_devolucion = ' . Datos::objectToDB($devolucionACliente->id) . '; ';
            } elseif ($modo == Modos::insert) {
                $sql .= 'INSERT INTO devoluciones_a_cliente_c (';
                $sql .= 'cod_devolucion, ';
                $sql .= 'cod_cliente, ';
                $sql .= 'cod_sucursal, ';
                $sql .= 'cantidad, ';
                $sql .= 'anulado, ';
                $sql .= 'observaciones, ';
                $sql .= 'cod_usuario, ';
                $sql .= 'fecha_alta ';
                $sql .= ') VALUES (';
                $sql .= Datos::objectToDB($devolucionACliente->id) . ', ';
                $sql .= Datos::objectToDB($devolucionACliente->cliente->id) . ', ';
                $sql .= Datos::objectToDB($devolucionACliente->sucursal->id) . ', ';
                $sql .= Datos::objectToDB($devolucionACliente->cantidadPares) . ', ';
                $sql .= Datos::objectToDB('N') . ', ';
                $sql .= Datos::objectToDB($devolucionACliente->observaciones) . ', ';
                $sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
                $sql .= 'GETDATE() ';
                $sql .= '); ';
                //} elseif ($modo == Modos::update) {
            } elseif ($modo == Modos::delete) {
                $sql .= 'UPDATE devoluciones_a_cliente_c SET ';
                $sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
                $sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
                $sql .= 'fecha_baja = GETDATE() ';
                $sql .= 'WHERE cod_devolucion = ' . Datos::objectToDB($devolucionACliente->id) . '; ';
            } elseif ($modo == Modos::id) {
                $sql .= 'SELECT ISNULL(MAX(cod_devolucion), 0) + 1 FROM devoluciones_a_cliente_c;';
            } else {
                throw new FactoryException('Modo incorrecto');
            }
            return $sql;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    private function mapperQueryDevolucionAClienteItem(DevolucionAClienteItem $devolucionAClienteItem, $modo){
        $sql = '';
        try {
            if ($modo == Modos::select){
                $sql .= 'SELECT * ';
                $sql .= 'FROM devoluciones_a_cliente_d ';
                $sql .= 'WHERE id = ' . Datos::objectToDB($devolucionAClienteItem->id) . '; ';
            } elseif ($modo == Modos::insert) {
                $sql .= 'INSERT INTO devoluciones_a_cliente_d (';
                $sql .= 'cod_devolucion, ';
                $sql .= 'cod_almacen, ';
                $sql .= 'cod_articulo, ';
                $sql .= 'cod_color_articulo, ';
                $sql .= 'cantidad, ';
                for ($i = 1; $i <= 10; $i++)
                    $sql .= 'cant_' . $i . ', ';
                $sql .= 'cod_usuario, ';
                $sql .= 'fecha_alta ';
                $sql .= ') VALUES (';
                $sql .= Datos::objectToDB($devolucionAClienteItem->devolucionACliente->id) . ', ';
                $sql .= Datos::objectToDB($devolucionAClienteItem->almacen->id) . ', ';
                $sql .= Datos::objectToDB($devolucionAClienteItem->articulo->id) . ', ';
                $sql .= Datos::objectToDB($devolucionAClienteItem->colorPorArticulo->id) . ', ';
                $sql .= Datos::objectToDB($devolucionAClienteItem->cantidadTotal) . ', ';
                for ($i = 1; $i <= 10; $i++)
                    $sql .= Datos::objectToDB($devolucionAClienteItem->cantidad[$i]) . ', ';
                $sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
                $sql .= 'GETDATE() ';
                $sql .= '); ';
                //} elseif ($modo == Modos::update) {
                //} elseif ($modo == Modos::delete) {
            } elseif ($modo == Modos::id) {
                $sql .= 'SELECT IDENT_CURRENT(\'devoluciones_a_cliente_d\') + IDENT_INCR(\'devoluciones_a_cliente_d\');';
            } else {
                throw new FactoryException('Modo incorrecto');
            }
            return $sql;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
	private function mapperQueryDocumento(Documento $documento, $modo, $primeraVez = true){
		$sql = '';
		try {
			if ($primeraVez) {
				switch ($documento->tipoDocumento) {
					case 'FAC':
					case 'NDB':
						$sql = $this->mapperQueryDocumentoDebe($documento, $modo); break;
					case 'REC':
					case 'NCR':
						$sql = $this->mapperQueryDocumentoHaber($documento, $modo); break;
					default:
						$sql = $this->mapperQueryDocumento($documento, $modo, false); break;
				}
			} else {
				if ($modo == Modos::select) {
					$sql .= 'SELECT * ';
					$sql .= 'FROM documentos ';
					$sql .= 'WHERE empresa = ' . Datos::objectToDB($documento->empresa) . ' ';
					$sql .= 'AND punto_venta = ' . Datos::objectToDB($documento->puntoDeVenta) . ' ';
					$sql .= 'AND tipo_docum = ' . Datos::objectToDB($documento->tipoDocumento) . ' ';
					$sql .= 'AND numero = ' . Datos::objectToDB($documento->numero) . ' ';
					$sql .= 'AND letra = ' . Datos::objectToDB($documento->letra) . '; ';
				//} elseif ($modo == Modos::insert) {
				//} elseif ($modo == Modos::update) {
				//} elseif ($modo == Modos::delete) {
				} else {
					throw new FactoryException('Modo incorrecto');
				}
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDocumentoAplicacion(DocumentoAplicacion $documentoAplicacion, $modo, $primeraVez = true){
		$sql = '';
		try {
			if ($modo == Modos::select) {
				$sql .= 'SELECT * ';
				$sql .= 'FROM documentos_aplicacion_v ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($documentoAplicacion->empresa) . ' ';
				$sql .= 'AND punto_venta = ' . Datos::objectToDB($documentoAplicacion->puntoDeVenta) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($documentoAplicacion->tipoDocumento) . ' ';
				$sql .= 'AND nro_documento = ' . Datos::objectToDB($documentoAplicacion->nroDocumento) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($documentoAplicacion->letra) . '; ';
			//} elseif ($modo == Modos::insert) {
			} elseif ($modo == Modos::update) {
				if ($primeraVez) {
					switch ($documentoAplicacion->tipoDocumento) {
						case 'REC':
							$sql = $this->mapperQueryDocumentoAplicacionHaber($documentoAplicacion, $modo); break;
						case 'FAC':
						case 'NDB':
						case 'NCR':
						default:
							$sql = $this->mapperQueryDocumentoAplicacion($documentoAplicacion, $modo, false); break;
					}
				} else {
					/** @var DocumentoAplicacionDebe $documentoAplicacion */
					$sql .= 'UPDATE documentos_c SET ';
					$sql .= 'importe_pendiente = ' . Datos::objectToDB($documentoAplicacion->importePendiente) . ', ';
					$sql .= 'dias_promedio_pago = ' . Datos::objectToDB($documentoAplicacion->diasPromedioPago) . ' ';
					$sql .= 'WHERE empresa = ' . Datos::objectToDB($documentoAplicacion->empresa) . ' ';
					$sql .= 'AND punto_venta = ' . Datos::objectToDB($documentoAplicacion->puntoDeVenta) . ' ';
					$sql .= 'AND tipo_docum = ' . Datos::objectToDB($documentoAplicacion->tipoDocumento) . ' ';
					$sql .= 'AND nro_documento = ' . Datos::objectToDB($documentoAplicacion->nroDocumento) . ' ';
					$sql .= 'AND letra = ' . Datos::objectToDB($documentoAplicacion->letra) . '; ';
				}
			//} elseif ($modo == Modos::delete) {
			//} elseif ($modo == Modos::id) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDocumentoAplicacionDebe(DocumentoAplicacion $documentoAplicacionDebe, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM documentos_aplicacion_debe_v ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($documentoAplicacionDebe->empresa) . ' ';
				$sql .= 'AND punto_venta = ' . Datos::objectToDB($documentoAplicacionDebe->puntoDeVenta) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($documentoAplicacionDebe->tipoDocumento) . ' ';
				$sql .= 'AND nro_documento = ' . Datos::objectToDB($documentoAplicacionDebe->nroDocumento) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($documentoAplicacionDebe->letra) . '; ';
			//} elseif ($modo == Modos::insert) {
			} elseif ($modo == Modos::update) {
				$sql .= $this->mapperQueryDocumentoAplicacion($documentoAplicacionDebe, $modo, false);
			//} elseif ($modo == Modos::delete) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDocumentoAplicacionHaber(DocumentoAplicacion $documentoAplicacionHaber, $modo, $primeraVez = true){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM documentos_aplicacion_haber_v ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($documentoAplicacionHaber->empresa) . ' ';
				$sql .= 'AND punto_venta = ' . Datos::objectToDB($documentoAplicacionHaber->puntoDeVenta) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($documentoAplicacionHaber->tipoDocumento) . ' ';
				$sql .= 'AND nro_documento = ' . Datos::objectToDB($documentoAplicacionHaber->nroDocumento) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($documentoAplicacionHaber->letra) . '; ';
			//} elseif ($modo == Modos::insert) {
			} elseif ($modo == Modos::update) {
				if ($primeraVez) {
					switch ($documentoAplicacionHaber->tipoDocumento) {
						case 'REC':
							$sql = $this->mapperQueryDocumentoAplicacionHaber($documentoAplicacionHaber, $modo, false); break;
						case 'NCR':
						default:
							$sql = $this->mapperQueryDocumentoAplicacion($documentoAplicacionHaber, $modo, false); break;
					}
				} else {
					$sql .= 'UPDATE recibo SET ';
					$sql .= 'importe_pendiente = ' . Datos::objectToDB($documentoAplicacionHaber->importePendiente) . ' ';
					$sql .= 'WHERE nro_recibo = ' . Datos::objectToDB($documentoAplicacionHaber->nroDocumento) . ' ';
					$sql .= 'AND empresa = ' . Datos::objectToDB($documentoAplicacionHaber->empresa) . '; ';
				}
			//} elseif ($modo == Modos::delete) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDocumentoDebe(Documento/*No ponerle Debe*/ $documentoDebe, $modo, $primeraVez = true){
		$sql = '';
		try {
			if ($primeraVez) {
				switch ($documentoDebe->tipoDocumento) {
					case 'FAC':
						$sql = $this->mapperQueryFactura($documentoDebe, $modo); break;
					case 'NDB':
						$sql = $this->mapperQueryNotaDeDebito($documentoDebe, $modo); break;
					default:
						$sql = $this->mapperQueryDocumentoDebe($documentoDebe, $modo, false); break;
				}
			} else {
				if ($modo == Modos::select){
					$sql .= $this->mapperQueryDocumento($documentoDebe, $modo, false);
				//} elseif ($modo == Modos::insert) {
				//} elseif ($modo == Modos::update) {
				//} elseif ($modo == Modos::delete) {
				} else {
					throw new FactoryException('Modo incorrecto');
				}
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDocumentoGastoDatos(DocumentoGastoDatos $documentoGastoDatos, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM documento_gasto_datos ';
				$sql .= 'WHERE cod_documento_gasto_datos = ' . Datos::objectToDB($documentoGastoDatos->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO documento_gasto_datos (';
				$sql .= 'cod_documento_gasto_datos, ';
				$sql .= 'cuit, ';
				$sql .= 'cod_imputacion, ';
				$sql .= 'razon_social, ';
				$sql .= 'condicion_iva, ';
				$sql .= 'calle, ';
				$sql .= 'numero, ';
				$sql .= 'piso, ';
				$sql .= 'oficina_depto, ';
				$sql .= 'cod_postal, ';
				$sql .= 'cod_pais, ';
				$sql .= 'cod_localidad, ';
				$sql .= 'cod_provincia, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($documentoGastoDatos->id) . ', ';
				$sql .= Datos::objectToDB($documentoGastoDatos->cuit) . ', ';
				$sql .= Datos::objectToDB($documentoGastoDatos->imputacion->id) . ', ';
				$sql .= Datos::objectToDB($documentoGastoDatos->razonSocial) . ', ';
				$sql .= Datos::objectToDB($documentoGastoDatos->condicionIva->id) . ', ';
				$sql .= Datos::objectToDB($documentoGastoDatos->direccion->calle) . ', ';
				$sql .= Datos::objectToDB($documentoGastoDatos->direccion->numero) . ', ';
				$sql .= Datos::objectToDB($documentoGastoDatos->direccion->piso) . ', ';
				$sql .= Datos::objectToDB($documentoGastoDatos->direccion->departamento) . ', ';
				$sql .= Datos::objectToDB($documentoGastoDatos->direccion->codigoPostal) . ', ';
				$sql .= Datos::objectToDB($documentoGastoDatos->direccion->pais->id) . ', ';
				$sql .= Datos::objectToDB($documentoGastoDatos->direccion->localidad->id) . ', ';
				$sql .= Datos::objectToDB($documentoGastoDatos->direccion->provincia->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE documento_gasto_datos SET ';
				$sql .= 'cuit = ' . Datos::objectToDB($documentoGastoDatos->cuit) . ', ';
				$sql .= 'cod_imputacion = ' . Datos::objectToDB($documentoGastoDatos->imputacion->id) . ', ';
				$sql .= 'razon_social = ' . Datos::objectToDB($documentoGastoDatos->razonSocial) . ', ';
				$sql .= 'condicion_iva = ' . Datos::objectToDB($documentoGastoDatos->condicionIva->id) . ', ';
				$sql .= 'calle = ' . Datos::objectToDB($documentoGastoDatos->direccion->calle) . ', ';
				$sql .= 'numero = ' . Datos::objectToDB($documentoGastoDatos->direccion->numero) . ', ';
				$sql .= 'piso = ' . Datos::objectToDB($documentoGastoDatos->direccion->piso) . ', ';
				$sql .= 'oficina_depto = ' . Datos::objectToDB($documentoGastoDatos->direccion->departamento) . ', ';
				$sql .= 'cod_postal = ' . Datos::objectToDB($documentoGastoDatos->direccion->codigoPostal) . ', ';
				$sql .= 'cod_pais = ' . Datos::objectToDB($documentoGastoDatos->direccion->pais->id) . ', ';
				$sql .= 'cod_localidad = ' . Datos::objectToDB($documentoGastoDatos->direccion->localidad->id) . ', ';
				$sql .= 'cod_provincia = ' . Datos::objectToDB($documentoGastoDatos->direccion->provincia->id) . ' ';
				$sql .= 'WHERE cod_documento_gasto_datos = ' . Datos::objectToDB($documentoGastoDatos->id) . '; ';
			//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_documento_gasto_datos), 0) + 1 FROM documento_gasto_datos;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDocumentoHaber(Documento/*No ponerle Haber*/ $documentoHaber, $modo, $primeraVez = true){
		$sql = '';
		try {
			if ($primeraVez) {
				switch ($documentoHaber->tipoDocumento) {
					case 'REC':
						$sql = $this->mapperQueryRecibo($documentoHaber, $modo); break;
					case 'NCR':
						$sql = $this->mapperQueryNotaDeCredito($documentoHaber, $modo); break;
					default:
						$sql = $this->mapperQueryDocumentoHaber($documentoHaber, $modo, false); break;
				}
			} else {
				if ($modo == Modos::select){
					$sql .= $this->mapperQueryDocumento($documentoHaber, $modo, false);
				//} elseif ($modo == Modos::insert) {
				//} elseif ($modo == Modos::update) {
				//} elseif ($modo == Modos::delete) {
				} else {
					throw new FactoryException('Modo incorrecto');
				}
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDocumentoHija(DocumentoHija $documentoHija, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM documentos_h_v ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($documentoHija->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO documentos_h (';
				$sql .= 'empresa, ';
				$sql .= 'madre_punto_venta, ';
				$sql .= 'madre_tipo_docum, ';
				$sql .= 'madre_nro_documento, ';
				$sql .= 'madre_letra, ';
				$sql .= 'cancel_punto_venta, ';
				$sql .= 'cancel_tipo_docum, ';
				$sql .= 'cancel_nro_documento, ';
				$sql .= 'cancel_letra, ';
				$sql .= 'importe, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($documentoHija->empresa) . ', ';
				$sql .= Datos::objectToDB($documentoHija->madre->puntoDeVenta) . ', ';
				$sql .= Datos::objectToDB($documentoHija->madre->tipoDocumento) . ', ';
				$sql .= Datos::objectToDB($documentoHija->madre->nroDocumento) . ', ';
				$sql .= Datos::objectToDB($documentoHija->madre->letra) . ', ';
				$sql .= Datos::objectToDB($documentoHija->documentoCancelatorio->puntoDeVenta) . ', ';
				$sql .= Datos::objectToDB($documentoHija->documentoCancelatorio->tipoDocumento) . ', ';
				$sql .= Datos::objectToDB($documentoHija->documentoCancelatorio->nroDocumento) . ', ';
				$sql .= Datos::objectToDB($documentoHija->documentoCancelatorio->letra) . ', ';
				$sql .= Datos::objectToDB($documentoHija->importe) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM documentos_h ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($documentoHija->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT IDENT_CURRENT(\'documentos_h\') + IDENT_INCR(\'documentos_h\');';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDocumentoItem(DocumentoItem $documentoItem, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM documentos_d_v ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($documentoItem->empresa) . ' ';
				$sql .= 'AND punto_venta = ' . Datos::objectToDB($documentoItem->puntoDeVenta) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($documentoItem->documentoTipoDocumento) . ' ';
				$sql .= 'AND nro_documento = ' . Datos::objectToDB($documentoItem->documentoNumero) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($documentoItem->documentoLetra) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($documentoItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO documentos_d (';
				$sql .= 'empresa, ';
				$sql .= 'punto_venta, ';
				$sql .= 'tipo_docum, ';
				$sql .= 'nro_documento, ';
				$sql .= 'letra, ';
				$sql .= 'nro_item, ';
				$sql .= 'cod_almacen, ';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				$sql .= 'descuento, ';
				$sql .= 'recargo, ';
				$sql .= 'iva_porc, ';
				$sql .= 'precio_unitario, ';
				$sql .= 'precio_unitario_final, ';
				$sql .= 'cantidad, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'descripcion_item, ';
				$sql .= 'cod_imputacion, ';
				$sql .= 'fecha_documento ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($documentoItem->empresa) . ', ';
				$sql .= Datos::objectToDB($documentoItem->puntoDeVenta) . ', ';
				$sql .= Datos::objectToDB($documentoItem->documentoTipoDocumento) . ', ';
				$sql .= Datos::objectToDB($documentoItem->documentoNumero) . ', ';
				$sql .= Datos::objectToDB($documentoItem->documentoLetra) . ', ';
				$sql .= Datos::objectToDB($documentoItem->numeroDeItem) . ', ';
				$sql .= Datos::objectToDB($documentoItem->almacen->id) . ', ';
				$sql .= Datos::objectToDB($documentoItem->articulo->id) . ', ';
				$sql .= Datos::objectToDB($documentoItem->colorPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($documentoItem->descuentoPedido) . ', ';
				$sql .= Datos::objectToDB($documentoItem->recargoPedido) . ', ';
				$sql .= Datos::objectToDB($documentoItem->ivaPorcentaje) . ', ';
				$sql .= Datos::objectToDB($documentoItem->precioUnitario) . ', ';
				$sql .= Datos::objectToDB($documentoItem->precioUnitarioFinal) . ', ';
				$sql .= Datos::objectToDB($documentoItem->cantidadTotal) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($documentoItem->cantidad[$i]) . ', ';
				$sql .= Datos::objectToDB($documentoItem->descripcionItem) . ', ';
				$sql .= Datos::objectToDB($documentoItem->imputacion->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) { //Lo uso para cuando borro el documento (no borro el detalle)
			} elseif ($modo == Modos::delete) {
				/* ï¿½Se necesita borrar el detalle?
				$sql .= 'DELETE FROM documentos_d ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($documentoItem->empresa) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($documentoItem->documentoTipoDocumento) . ' ';
				$sql .= 'AND nro_documento = ' . Datos::objectToDB($documentoItem->documentoNumero) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($documentoItem->documentoLetra) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($documentoItem->numeroDeItem) . '; ';
				*/
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDocumentoProveedor(DocumentoProveedor $documentoProveedor, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM documento_proveedor_c ';
				$sql .= 'WHERE cod_documento_proveedor = ' . Datos::objectToDB($documentoProveedor->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO documento_proveedor_c (';
				$sql .= 'cod_documento_proveedor, ';
				$sql .= 'empresa, ';
				$sql .= 'punto_venta, ';
				$sql .= 'tipo_docum, ';
				$sql .= 'tipo_factura, ';
				$sql .= 'nro_documento, ';
				$sql .= 'letra, ';
				$sql .= 'cod_proveedor, ';
				$sql .= 'operacion_tipo, ';
				$sql .= 'fecha, ';
				$sql .= 'neto_gravado, ';
				$sql .= 'neto_no_gravado, ';
				$sql .= 'importe_total, ';
				$sql .= 'importe_pendiente, ';
				$sql .= 'condicion_plazo_pago, ';
				$sql .= 'fecha_vencimiento, ';
				$sql .= 'fecha_periodo_fiscal, ';
				$sql .= 'observaciones, ';
				$sql .= 'documento_en_conflicto, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta, ';
				$sql .= 'cod_viejo, ';
				$sql .= 'factura_gastos, ';
				$sql .= 'cod_documento_gasto_datos ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($documentoProveedor->id) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->empresa) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->puntoVenta) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->tipoDocum) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->tipo->id) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->nroDocumento) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->letra) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->proveedor->id) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->operacionTipo) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->fecha) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->netoGravado) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->netoNoGravado) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->importeTotal) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->importeTotal) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->condicionPlazoPago) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->fechaVencimiento) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->fechaPeriodoFiscal) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->observaciones) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->documentoEnConflicto) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ,';
				$sql .= Datos::objectToDB($documentoProveedor->idViejo) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->facturaGastos) . ', ';
				$sql .= Datos::objectToDB($documentoProveedor->documentoGastoDatos->id) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE documento_proveedor_c SET ';
				$sql .= 'punto_venta = ' . Datos::objectToDB($documentoProveedor->puntoVenta) . ', ';
				$sql .= 'nro_documento = ' . Datos::objectToDB($documentoProveedor->nroDocumento) . ', ';
				$sql .= 'letra = ' . Datos::objectToDB($documentoProveedor->letra) . ', ';
				$sql .= 'cod_proveedor = ' . Datos::objectToDB($documentoProveedor->proveedor->id) . ', ';
				$sql .= 'operacion_tipo = ' . Datos::objectToDB($documentoProveedor->operacionTipo) . ', ';
				$sql .= 'fecha = ' . Datos::objectToDB($documentoProveedor->fecha) . ', ';
				$sql .= 'neto_gravado = ' . Datos::objectToDB($documentoProveedor->netoGravado) . ', ';
				$sql .= 'neto_no_gravado = ' . Datos::objectToDB($documentoProveedor->netoNoGravado) . ', ';
				$sql .= 'importe_total = ' . Datos::objectToDB($documentoProveedor->importeTotal) . ', ';
				$sql .= 'importe_pendiente = ' . Datos::objectToDB($documentoProveedor->importeTotal) . ', ';
				$sql .= 'condicion_plazo_pago = ' . Datos::objectToDB($documentoProveedor->condicionPlazoPago) . ', ';
				$sql .= 'fecha_vencimiento = ' . Datos::objectToDB($documentoProveedor->fechaVencimiento) . ', ';
				$sql .= 'fecha_periodo_fiscal = ' . Datos::objectToDB($documentoProveedor->fechaPeriodoFiscal) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($documentoProveedor->observaciones) . ', ';
				$sql .= 'documento_en_conflicto = ' . Datos::objectToDB($documentoProveedor->documentoEnConflicto) . ', ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($documentoProveedor->asientoContable->id) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_documento_proveedor = ' . Datos::objectToDB($documentoProveedor->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE documento_proveedor_c SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_documento_proveedor = ' . Datos::objectToDB($documentoProveedor->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_documento_proveedor), 0) + 1 FROM documento_proveedor_c;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDocumentoProveedorAplicacion(DocumentoProveedorAplicacion $documentoProveedorAplicacion, $modo, $primeraVez = true){
		$sql = '';
		try {
			if ($modo == Modos::select) {
				$sql .= 'SELECT * ';
				$sql .= 'FROM documento_proveedor_aplicacion_v ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($documentoProveedorAplicacion->id) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($documentoProveedorAplicacion->empresa) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($documentoProveedorAplicacion->tipoDocumento) . ' ';
				// if ($documentoProveedorAplicacion->esHaber() && $documentoProveedorAplicacion->tipoDocumento) { // Si no buscamos también por tipo_docum, puede ocurrir que haya más de un registro que cumple con los mismos requerimientos (me pasó que había una FAC y una OP)
                //     $sql .= 'AND tipo_docum = ' . Datos::objectToDB($documentoProveedorAplicacion->tipoDocumento) . ' '; // Entonces eso hacía que detecte como que no existe un único registro, y fallaba
                // }
				$sql .= '; ';
			//} elseif ($modo == Modos::insert) {
			} elseif ($modo == Modos::update) {
				if ($primeraVez) {
					switch ($documentoProveedorAplicacion->tipoDocumento) {
						case 'OP':
						case 'REN':
							$sql = $this->mapperQueryDocumentoProveedorAplicacionHaber($documentoProveedorAplicacion, $modo); break;
						case 'FAC':
						case 'NDB':
						case 'NCR':
						default:
							$sql = $this->mapperQueryDocumentoProveedorAplicacion($documentoProveedorAplicacion, $modo, false); break;
					}
				} else {
					$sql .= 'UPDATE documento_proveedor_c SET ';
					$sql .= 'importe_pendiente = ' . Datos::objectToDB($documentoProveedorAplicacion->importePendiente) . ' ';
					$sql .= 'WHERE cod_documento_proveedor = ' . Datos::objectToDB($documentoProveedorAplicacion->id) . ' ';
					$sql .= 'AND empresa = ' . Datos::objectToDB($documentoProveedorAplicacion->empresa) . '; ';
				}
			//} elseif ($modo == Modos::delete) {
			//} elseif ($modo == Modos::id) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDocumentoProveedorAplicacionDebe(DocumentoProveedorAplicacion $documentoProveedorAplicacionDebe, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM documento_proveedor_aplicacion_debe_v ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($documentoProveedorAplicacionDebe->id) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($documentoProveedorAplicacionDebe->empresa) . '; ';
			//} elseif ($modo == Modos::insert) {
			} elseif ($modo == Modos::update) {
				$sql .= $this->mapperQueryDocumentoProveedorAplicacion($documentoProveedorAplicacionDebe, $modo, false);
			//} elseif ($modo == Modos::delete) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDocumentoProveedorAplicacionHaber(DocumentoProveedorAplicacion $documentoProveedorAplicacionHaber, $modo, $primeraVez = true){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM documento_proveedor_aplicacion_haber_v ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($documentoProveedorAplicacionHaber->empresa) . ' ';
				$sql .= 'AND id = ' . Datos::objectToDB($documentoProveedorAplicacionHaber->id) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($documentoProveedorAplicacionHaber->tipoDocumento) . '; ';
			//} elseif ($modo == Modos::insert) {
			} elseif ($modo == Modos::update) {
				if ($primeraVez) {
					switch ($documentoProveedorAplicacionHaber->tipoDocumento) {
						case 'OP':
						case 'REN':
							$sql = $this->mapperQueryDocumentoProveedorAplicacionHaber($documentoProveedorAplicacionHaber, $modo, false); break;
						case 'NCR':
						default:
							$sql = $this->mapperQueryDocumentoProveedorAplicacion($documentoProveedorAplicacionHaber, $modo, false); break;
					}
				} else {
					switch ($documentoProveedorAplicacionHaber->tipoDocumento) {
						case 'REN':
							$sql .= 'UPDATE rendicion_de_gastos SET ';
							$sql .= 'importe_pendiente = ' . Datos::objectToDB($documentoProveedorAplicacionHaber->importePendiente) . ' ';
							$sql .= 'WHERE cod_rendicion_gastos = ' . Datos::objectToDB($documentoProveedorAplicacionHaber->id) . ' ';
							$sql .= 'AND empresa = ' . Datos::objectToDB($documentoProveedorAplicacionHaber->empresa) . '; ';
							break;
						case 'OP':
							$sql .= 'UPDATE orden_de_pago SET ';
							$sql .= 'importe_pendiente = ' . Datos::objectToDB($documentoProveedorAplicacionHaber->importePendiente) . ' ';
							$sql .= 'WHERE nro_orden_de_pago = ' . Datos::objectToDB($documentoProveedorAplicacionHaber->id) . ' ';
							$sql .= 'AND empresa = ' . Datos::objectToDB($documentoProveedorAplicacionHaber->empresa) . '; ';
							break;
						default:
							throw new Exception('No se reconoce el tipo de documento al intentar aplicar. Debería ser "OP" o "REN"'); break;
					}
				}
			//} elseif ($modo == Modos::delete) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDocumentoProveedorHija(DocumentoProveedorHija $documentoProveedorHija, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM documento_proveedor_h_v ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($documentoProveedorHija->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO documento_proveedor_h (';
				$sql .= 'empresa, ';
				$sql .= 'cod_madre, ';
				$sql .= 'cod_cancel, ';
				$sql .= 'tipo_docum_cancel, ';
				$sql .= 'importe, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($documentoProveedorHija->empresa) . ', ';
				$sql .= Datos::objectToDB($documentoProveedorHija->madre->id) . ', ';
				$sql .= Datos::objectToDB($documentoProveedorHija->documentoCancelatorio->id) . ', ';
				$sql .= Datos::objectToDB($documentoProveedorHija->documentoCancelatorio->tipoDocumento) . ', ';
				$sql .= Datos::objectToDB($documentoProveedorHija->importe) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM documento_proveedor_h ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($documentoProveedorHija->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT IDENT_CURRENT(\'documento_proveedor_h\') + IDENT_INCR(\'documento_proveedor_h\');';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryDocumentoProveedorItem(DocumentoProveedorItem $documentoProveedorItem, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM documento_proveedor_d ';
				$sql .= 'WHERE cod_documento_proveedor = ' . Datos::objectToDB($documentoProveedorItem->documentoProveedor->id) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($documentoProveedorItem->nroItem) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO documento_proveedor_d (';
				$sql .= 'cod_documento_proveedor, ';
				$sql .= 'nro_item, ';
				$sql .= 'descripcion, ';
				$sql .= 'cantidad, ';
				for($i = 1; $i < 16; $i++) {
					$sql .= 'cant_' . $i . ', ';
					$sql .= 'pr_' . $i . ', ';
				}
				$sql .= 'precio_unitario, ';
				$sql .= 'importe, ';
				$sql .= 'usa_rango, ';
				$sql .= 'cod_imputacion, ';
				$sql .= 'gravado, ';
				$sql .= 'origen_detalle, ';
				$sql .= 'cod_remito_orden_de_compra, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($documentoProveedorItem->idDocumentoProveedor) . ', ';
				$sql .= Datos::objectToDB($documentoProveedorItem->nroItem) . ', ';
				$sql .= Datos::objectToDB($documentoProveedorItem->descripcion) . ', ';
				$sql .= Datos::objectToDB($documentoProveedorItem->cantidad) . ', ';
				for($i = 1; $i < 16; $i++){
					$sql .= Datos::objectToDB($documentoProveedorItem->cantidades[$i]) . ', ';
					$sql .= Datos::objectToDB($documentoProveedorItem->precios[$i]) . ', ';
				}
				$sql .= Datos::objectToDB($documentoProveedorItem->precioUnitario) . ', ';
				$sql .= Datos::objectToDB($documentoProveedorItem->importe) . ', ';
				$sql .= Datos::objectToDB($documentoProveedorItem->usaRango) . ', ';
				$sql .= Datos::objectToDB($documentoProveedorItem->imputacion->id) . ', ';
				$sql .= Datos::objectToDB($documentoProveedorItem->gravado) . ', ';
				$sql .= Datos::objectToDB($documentoProveedorItem->origenDetalle) . ', ';
				$sql .= Datos::objectToDB($documentoProveedorItem->remitoPorOrdenDeCompra->id) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE documento_proveedor_d SET ';
				$sql .= 'descripcion = ' . Datos::objectToDB($documentoProveedorItem->descripcion) . ', ';
				$sql .= 'cantidad = ' . Datos::objectToDB($documentoProveedorItem->cantidad) . ', ';
				$sql .= 'precio_unitario = ' . Datos::objectToDB($documentoProveedorItem->precioUnitario) . ', ';
				$sql .= 'importe = ' . Datos::objectToDB($documentoProveedorItem->importe) . ', ';
				$sql .= 'cod_imputacion = ' . Datos::objectToDB($documentoProveedorItem->imputacion->id) . ', ';
				$sql .= 'gravado = ' . Datos::objectToDB($documentoProveedorItem->gravado) . ', ';
				$sql .= 'cod_remito_orden_de_compra = ' . Datos::objectToDB($documentoProveedorItem->remitoPorOrdenDeCompra->id) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_documento_proveedor = ' . Datos::objectToDB($documentoProveedorItem->documentoProveedor->id) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($documentoProveedorItem->nroItem) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE documento_proveedor_d SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_documento_proveedor = ' . Datos::objectToDB($documentoProveedorItem->documentoProveedor->id) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($documentoProveedorItem->nroItem) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_item), 0) + 1 FROM documento_proveedor_d ';
				$sql .= 'WHERE cod_documento_proveedor = ' . Datos::objectToDB($documentoProveedorItem->idDocumentoProveedor) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryEcommerce_Coupon(Ecommerce_Coupon $ecommerce_Coupon, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ecommerce_coupons ';
				$sql .= 'WHERE cod_coupon = ' . Datos::objectToDB($ecommerce_Coupon->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ecommerce_coupons (';
				$sql .= 'cod_coupon, ';
				$sql .= 'cod_coupon_ecommerce, ';
				$sql .= 'code, ';
				$sql .= 'cod_order, ';
				$sql .= 'amount, ';
				$sql .= 'percentage, ';
				$sql .= 'max_amount, ';
				$sql .= 'applied_amount, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ecommerce_Coupon->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Coupon->idEcommerce) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Coupon->code) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Coupon->order->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Coupon->amount) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Coupon->percentage) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Coupon->maxAmount) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Coupon->appliedAmount) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE ecommerce_coupons SET ';
				$sql .= 'amount = ' . Datos::objectToDB($ecommerce_Coupon->amount) . ', ';
				$sql .= 'percentage = ' . Datos::objectToDB($ecommerce_Coupon->percentage) . ', ';
				$sql .= 'max_amount = ' . Datos::objectToDB($ecommerce_Coupon->maxAmount) . ', ';
				$sql .= 'applied_amount = ' . Datos::objectToDB($ecommerce_Coupon->appliedAmount) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_coupon = ' . Datos::objectToDB($ecommerce_Coupon->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE ecommerce_coupons SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_coupon = ' . Datos::objectToDB($ecommerce_Coupon->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_coupon), 0) + 1 FROM ecommerce_coupons; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryEcommerce_Customer(Ecommerce_Customer $ecommerce_Customer, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ecommerce_customers ';
				$sql .= 'WHERE cod_customer = ' . Datos::objectToDB($ecommerce_Customer->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ecommerce_customers (';
				$sql .= 'cod_customer, ';
				$sql .= 'cod_customer_ecommerce, ';
				$sql .= 'cod_usergroup, ';
				$sql .= 'email, ';
				$sql .= 'title, ';
				$sql .= 'firstname, ';
				$sql .= 'lastname, ';
				$sql .= 'birthday, ';
				$sql .= 'newsletters, ';
				$sql .= 'offers, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ecommerce_Customer->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Customer->idEcommerce) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Customer->usergroup->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Customer->email) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Customer->title) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Customer->firstname) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Customer->lastname) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Customer->birthday) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Customer->newsletters) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Customer->offers) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE ecommerce_customers SET ';
				$sql .= 'cod_usergroup = ' . Datos::objectToDB($ecommerce_Customer->usergroup->id) . ', ';
				$sql .= 'email = ' . Datos::objectToDB($ecommerce_Customer->email) . ', ';
				$sql .= 'title = ' . Datos::objectToDB($ecommerce_Customer->title) . ', ';
				$sql .= 'firstname = ' . Datos::objectToDB($ecommerce_Customer->firstname) . ', ';
				$sql .= 'lastname = ' . Datos::objectToDB($ecommerce_Customer->lastname) . ', ';
				$sql .= 'birthday = ' . Datos::objectToDB($ecommerce_Customer->birthday) . ', ';
				$sql .= 'newsletters = ' . Datos::objectToDB($ecommerce_Customer->newsletters) . ', ';
				$sql .= 'offers = ' . Datos::objectToDB($ecommerce_Customer->offers) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_customer = ' . Datos::objectToDB($ecommerce_Customer->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE ecommerce_customers SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_customer = ' . Datos::objectToDB($ecommerce_Customer->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_customer), 0) + 1 FROM ecommerce_customers; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryEcommerce_Delivery(Ecommerce_Delivery $ecommerce_Delivery, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ecommerce_deliverys ';
				$sql .= 'WHERE cod_delivery = ' . Datos::objectToDB($ecommerce_Delivery->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ecommerce_deliverys (';
				$sql .= 'cod_delivery, ';
				$sql .= 'cod_order, ';
				$sql .= 'street, ';
				$sql .= 'city, ';
				$sql .= 'province, ';
				$sql .= 'pbox, ';
				$sql .= 'country, ';
				$sql .= 'phone, ';
				$sql .= 'receptor_name, ';
				$sql .= 'expected_date, ';
				$sql .= 'time_frame, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ecommerce_Delivery->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Delivery->order->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Delivery->street) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Delivery->city) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Delivery->province) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Delivery->pbox) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Delivery->country) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Delivery->phone) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Delivery->receptorName) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Delivery->expectedDate) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Delivery->timeFrame) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE ecommerce_deliverys SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_delivery = ' . Datos::objectToDB($ecommerce_Delivery->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_delivery), 0) + 1 FROM ecommerce_deliverys; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryEcommerce_Order(Ecommerce_Order $ecommerce_Order, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ecommerce_orders_v ';
				$sql .= 'WHERE cod_order = ' . Datos::objectToDB($ecommerce_Order->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ecommerce_orders (';
				$sql .= 'cod_order, ';
				$sql .= 'cod_order_ecommerce, ';
				$sql .= 'cod_status, ';
				$sql .= 'cod_servicio_andreani, ';
				$sql .= 'cod_customer, ';
				$sql .= 'total_discount, ';
				$sql .= 'total_coupon, ';
				$sql .= 'grand_total, ';
				$sql .= 'fecha_pedido, ';
				$sql .= 'cod_dependencias_cumplidas, ';
				$sql .= 'cupon_cambio_utilizado, ';
				$sql .= 'cupon_cambio_importe, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ecommerce_Order->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Order->idEcommerce) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Order->status->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Order->servicioAndreani->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Order->customer->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Order->totalDiscount) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Order->totalCoupon) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Order->grandTotal) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Order->fechaPedido) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Order->idDependenciasCumplidas) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(0) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE ecommerce_orders SET ';
				$sql .= 'cod_status = ' . Datos::objectToDB($ecommerce_Order->status->id) . ', ';
				$sql .= 'cod_servicio_andreani = ' . Datos::objectToDB($ecommerce_Order->servicioAndreani->id) . ', ';
				$sql .= 'total_discount = ' . Datos::objectToDB($ecommerce_Order->totalDiscount) . ', ';
				$sql .= 'total_coupon = ' . Datos::objectToDB($ecommerce_Order->totalCoupon) . ', ';
				$sql .= 'grand_total = ' . Datos::objectToDB($ecommerce_Order->grandTotal) . ', ';
				$sql .= 'cod_dependencias_cumplidas = ' . Datos::objectToDB($ecommerce_Order->idDependenciasCumplidas) . ', ';
				$sql .= 'cod_cupon_cambio = ' . Datos::objectToDB($ecommerce_Order->idCuponDeCambio) . ', ';
				$sql .= 'cupon_cambio_utilizado = ' . Datos::objectToDB($ecommerce_Order->cuponDeCambioUtilizado) . ', ';
				$sql .= 'cupon_cambio_importe = ' . Datos::objectToDB($ecommerce_Order->cuponDeCambioImporte) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_order = ' . Datos::objectToDB($ecommerce_Order->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE ecommerce_orders SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_order = ' . Datos::objectToDB($ecommerce_Order->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_order), 0) + 1 FROM ecommerce_orders; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryEcommerce_OrderDetail(Ecommerce_OrderDetail $ecommerce_OrderDetail, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ecommerce_order_details ';
				$sql .= 'WHERE cod_order_detail = ' . Datos::objectToDB($ecommerce_OrderDetail->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ecommerce_order_details (';
				$sql .= 'cod_order_detail, ';
				$sql .= 'cod_order, ';
				$sql .= 'reference, ';
				$sql .= 'description, ';
				$sql .= 'size, ';
				$sql .= 'quantity, ';
				$sql .= 'price, ';
				$sql .= 'subtotal, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ecommerce_OrderDetail->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_OrderDetail->order->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_OrderDetail->reference) . ', ';
				$sql .= Datos::objectToDB($ecommerce_OrderDetail->description) . ', ';
				$sql .= Datos::objectToDB($ecommerce_OrderDetail->size) . ', ';
				$sql .= Datos::objectToDB($ecommerce_OrderDetail->quantity) . ', ';
				$sql .= Datos::objectToDB($ecommerce_OrderDetail->price) . ', ';
				$sql .= Datos::objectToDB($ecommerce_OrderDetail->subtotal) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			} elseif ($modo == Modos::delete) { //Tal vez esto deberï¿½a ser DELETE
				$sql .= 'UPDATE ecommerce_order_details SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_order_detail = ' . Datos::objectToDB($ecommerce_OrderDetail->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_order_detail), 0) + 1 FROM ecommerce_order_details; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryEcommerce_OrderStatus(Ecommerce_OrderStatus $ecommerce_OrderStatus, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ecommerce_order_status ';
				$sql .= 'WHERE cod_status = ' . Datos::objectToDB($ecommerce_OrderStatus->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ecommerce_order_status (';
				$sql .= 'cod_status, ';
				$sql .= 'nombre, ';
				$sql .= 'mostrar_en_panel, ';
				$sql .= 'cod_status_anterior, ';
				$sql .= 'cod_proximo_status, ';
				$sql .= 'cod_dependencias, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ecommerce_OrderStatus->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_OrderStatus->nombre) . ', ';
				$sql .= Datos::objectToDB($ecommerce_OrderStatus->mostrarEnPanel) . ', ';
				$sql .= Datos::objectToDB($ecommerce_OrderStatus->statusAnterior->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_OrderStatus->proximoStatus->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_OrderStatus->idDependencias) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE ecommerce_order_status SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($ecommerce_OrderStatus->nombre) . ', ';
				$sql .= 'mostrar_en_panel = ' . Datos::objectToDB($ecommerce_OrderStatus->mostrarEnPanel) . ', ';
				$sql .= 'cod_status_anterior = ' . Datos::objectToDB($ecommerce_OrderStatus->statusAnterior->id) . ', ';
				$sql .= 'cod_proximo_status = ' . Datos::objectToDB($ecommerce_OrderStatus->proximoStatus->id) . ', ';
				$sql .= 'cod_dependencias = ' . Datos::objectToDB($ecommerce_OrderStatus->idDependencias) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_status = ' . Datos::objectToDB($ecommerce_OrderStatus->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE ecommerce_order_status SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_status = ' . Datos::objectToDB($ecommerce_OrderStatus->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_status), 0) + 1 FROM ecommerce_order_status; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryEcommerce_OrderStatus_Cobrado(Ecommerce_OrderStatus_Cobrado $ecommerce_OrderStatus_Cobrado, $modo){
		return $this->mapperQueryEcommerce_OrderStatus($ecommerce_OrderStatus_Cobrado, $modo);
	}
	private function mapperQueryEcommerce_OrderStatus_Despachado(Ecommerce_OrderStatus_Despachado $ecommerce_OrderStatus_Despachado, $modo){
		return $this->mapperQueryEcommerce_OrderStatus($ecommerce_OrderStatus_Despachado, $modo);
	}
	private function mapperQueryEcommerce_OrderStatus_EnTransito(Ecommerce_OrderStatus_EnTransito $ecommerce_OrderStatus_EnTransito, $modo){
		return $this->mapperQueryEcommerce_OrderStatus($ecommerce_OrderStatus_EnTransito, $modo);
	}
	private function mapperQueryEcommerce_OrderStatus_Facturado(Ecommerce_OrderStatus_Facturado $ecommerce_OrderStatus_Facturado, $modo){
		return $this->mapperQueryEcommerce_OrderStatus($ecommerce_OrderStatus_Facturado, $modo);
	}
	private function mapperQueryEcommerce_OrderStatus_FacturadoCae(Ecommerce_OrderStatus_FacturadoCae $ecommerce_OrderStatus_FacturadoCae, $modo){
		return $this->mapperQueryEcommerce_OrderStatus($ecommerce_OrderStatus_FacturadoCae, $modo);
	}
	private function mapperQueryEcommerce_OrderStatus_Finalizado(Ecommerce_OrderStatus_Finalizado $ecommerce_OrderStatus_Finalizado, $modo){
		return $this->mapperQueryEcommerce_OrderStatus($ecommerce_OrderStatus_Finalizado, $modo);
	}
	private function mapperQueryEcommerce_OrderStatus_Pedido(Ecommerce_OrderStatus_Pedido $ecommerce_OrderStatus_Pedido, $modo){
		return $this->mapperQueryEcommerce_OrderStatus($ecommerce_OrderStatus_Pedido, $modo);
	}
	private function mapperQueryEcommerce_OrderStatus_PendienteDeCambio(Ecommerce_OrderStatus_PendienteDeCambio $ecommerce_OrderStatus_PendienteDeCambio, $modo){
		return $this->mapperQueryEcommerce_OrderStatus($ecommerce_OrderStatus_PendienteDeCambio, $modo);
	}
	private function mapperQueryEcommerce_OrderStatus_PendienteDeDevolucion(Ecommerce_OrderStatus_PendienteDeDevolucion $ecommerce_OrderStatus_PendienteDeDevolucion, $modo){
		return $this->mapperQueryEcommerce_OrderStatus($ecommerce_OrderStatus_PendienteDeDevolucion, $modo);
	}
	private function mapperQueryEcommerce_OrderStatus_Predespachado(Ecommerce_OrderStatus_Predespachado $ecommerce_OrderStatus_Predespachado, $modo){
		return $this->mapperQueryEcommerce_OrderStatus($ecommerce_OrderStatus_Predespachado, $modo);
	}
	private function mapperQueryEcommerce_OrderStatus_Remitido(Ecommerce_OrderStatus_Remitido $ecommerce_OrderStatus_Remitido, $modo){
		return $this->mapperQueryEcommerce_OrderStatus($ecommerce_OrderStatus_Remitido, $modo);
	}
	private function mapperQueryEcommerce_Payment(Ecommerce_Payment $ecommerce_Payment, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ecommerce_payments ';
				$sql .= 'WHERE cod_payment = ' . Datos::objectToDB($ecommerce_Payment->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ecommerce_payments (';
				$sql .= 'cod_payment, ';
				$sql .= 'cod_payment_ecommerce, ';
				$sql .= 'cod_order, ';
				$sql .= 'cod_method, ';
				$sql .= 'instrument_id, ';
				$sql .= 'amount, ';
				$sql .= 'auth_id, ';
				$sql .= 'info, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ecommerce_Payment->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Payment->idEcommerce) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Payment->order->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Payment->method->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Payment->instrumentId) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Payment->amount) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Payment->authId) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Payment->info) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE ecommerce_payments SET ';
				$sql .= 'info = ' . Datos::objectToDB($ecommerce_Payment->info) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_payment = ' . Datos::objectToDB($ecommerce_Payment->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE ecommerce_payments SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_payment = ' . Datos::objectToDB($ecommerce_Payment->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_payment), 0) + 1 FROM ecommerce_payments; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryEcommerce_PaymentMethod(Ecommerce_PaymentMethod $ecommerce_PaymentMethod, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ecommerce_payment_methods ';
				$sql .= 'WHERE cod_method = ' . Datos::objectToDB($ecommerce_PaymentMethod->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ecommerce_payment_methods (';
				$sql .= 'cod_method, ';
				$sql .= 'nombre, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ecommerce_PaymentMethod->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_PaymentMethod->nombre) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE ecommerce_payment_methods SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($ecommerce_PaymentMethod->nombre) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_method = ' . Datos::objectToDB($ecommerce_PaymentMethod->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE ecommerce_payment_methods SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_method = ' . Datos::objectToDB($ecommerce_PaymentMethod->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_method), 0) + 1 FROM ecommerce_payment_methods; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryEcommerce_ServicioAndreani(Ecommerce_ServicioAndreani $ecommerce_ServicioAndreani, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ecommerce_servicios_andreani ';
				$sql .= 'WHERE cod_servicio_andreani = ' . Datos::objectToDB($ecommerce_ServicioAndreani->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ecommerce_servicios_andreani (';
				$sql .= 'cod_servicio_andreani, ';
				$sql .= 'nombre, ';
				$sql .= 'numero_contrato, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ecommerce_ServicioAndreani->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_ServicioAndreani->nombre) . ', ';
				$sql .= Datos::objectToDB($ecommerce_ServicioAndreani->numeroDeContrato) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE ecommerce_servicios_andreani SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($ecommerce_ServicioAndreani->nombre) . ', ';
				$sql .= 'numero_contrato = ' . Datos::objectToDB($ecommerce_ServicioAndreani->numeroDeContrato) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_servicio_andreani = ' . Datos::objectToDB($ecommerce_ServicioAndreani->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE ecommerce_servicios_andreani SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_servicio_andreani = ' . Datos::objectToDB($ecommerce_ServicioAndreani->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_servicio_andreani), 0) + 1 FROM ecommerce_servicios_andreani; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryEcommerce_Usergroup(Ecommerce_Usergroup $ecommerce_Usergroup, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ecommerce_usergroups ';
				$sql .= 'WHERE cod_usergroup = ' . Datos::objectToDB($ecommerce_Usergroup->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ecommerce_usergroups (';
				$sql .= 'cod_usergroup, ';
				$sql .= 'nombre, ';
				$sql .= 'empresa, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ecommerce_Usergroup->id) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Usergroup->nombre) . ', ';
				$sql .= Datos::objectToDB($ecommerce_Usergroup->empresa) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE ecommerce_usergroups SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($ecommerce_Usergroup->nombre) . ', ';
				$sql .= 'empresa = ' . Datos::objectToDB($ecommerce_Usergroup->empresa) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_usergroup = ' . Datos::objectToDB($ecommerce_Usergroup->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE ecommerce_usergroups SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_usergroup = ' . Datos::objectToDB($ecommerce_Usergroup->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_usergroup), 0) + 1 FROM ecommerce_usergroups; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryEfectivo(Efectivo $efectivo, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM efectivo ';
				$sql .= 'WHERE cod_efectivo = ' . Datos::objectToDB($efectivo->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO efectivo (';
				$sql .= 'cod_efectivo, ';
				$sql .= 'empresa, ';
				$sql .= 'importe ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($efectivo->id) . ', ';
				$sql .= Datos::objectToDB($efectivo->empresa) . ', ';
				$sql .= Datos::objectToDB($efectivo->importe);
				$sql .= '); ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_efectivo), 0) + 1 FROM efectivo; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryEgresoDeFondosItem(/** @noinspection PhpUnusedParameterInspection */ EgresoDeFondosItem $egresoDeFondosItem, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM egreso_de_fondos_v ';
				$sql .= 'WHERE 1 = 1; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryEjercicioContable(EjercicioContable $ejercicioContable, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ejercicios_contables ';
				$sql .= 'WHERE cod_ejercicio_contable = ' . Datos::objectToDB($ejercicioContable->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ejercicios_contables (';
				$sql .= 'cod_ejercicio_contable, ';
				$sql .= 'nombre, ';
				$sql .= 'fecha_desde, ';
				$sql .= 'fecha_hasta, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ejercicioContable->id) . ', ';
				$sql .= Datos::objectToDB($ejercicioContable->nombre) . ', ';
				$sql .= Datos::objectToDB($ejercicioContable->fechaDesde) . ', ';
				$sql .= Datos::objectToDB($ejercicioContable->fechaHasta) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE ejercicios_contables SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($ejercicioContable->nombre) . ', ';
				$sql .= 'fecha_desde = ' . Datos::objectToDB($ejercicioContable->fechaDesde) . ', ';
				$sql .= 'fecha_hasta = ' . Datos::objectToDB($ejercicioContable->fechaHasta) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = ' . 'GETDATE()' . ' ' ;
				$sql .= 'WHERE cod_ejercicio_contable = ' . Datos::objectToDB($ejercicioContable->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE ejercicios_contables SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_ejercicio_contable = ' . Datos::objectToDB($ejercicioContable->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_ejercicio_contable), 0) + 1 FROM ejercicios_contables; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryEmail(Email $email, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM emails ';
				$sql .= 'WHERE cod_email = ' . Datos::objectToDB($email->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO emails (';
				$sql .= 'cod_email, ';
				$sql .= 'de, ';
				$sql .= 'para, ';
				$sql .= 'cc, ';
				$sql .= 'cco, ';
				$sql .= 'asunto, ';
				$sql .= 'contenido, ';
				$sql .= 'imagenes, ';
				$sql .= 'adjuntos, ';
				$sql .= 'fecha_programada, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($email->id) . ', ';
				$sql .= Datos::objectToDB($email->de) . ', ';
				$sql .= Datos::objectToDB($email->para) . ', ';
				$sql .= Datos::objectToDB($email->cc) . ', ';
				$sql .= Datos::objectToDB($email->cco) . ', ';
				$sql .= Datos::objectToDB($email->asunto) . ', ';
				$sql .= Datos::objectToDB($email->contenido) . ', ';
				$sql .= Datos::objectToDB($email->imagenes) . ', ';
				$sql .= Datos::objectToDB($email->adjuntos) . ', ';
				$sql .= Datos::objectToDB($email->fechaProgramada) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE emails SET ';
				$sql .= 'fecha_enviado = ' . Datos::objectToDB($email->fechaEnviado) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = ' . 'GETDATE()' . ' ' ;
				$sql .= 'WHERE cod_email = ' . Datos::objectToDB($email->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE emails SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_email = ' . Datos::objectToDB($email->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_email), 0) + 1 FROM emails; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryExplosionLoteTemp(ExplosionLoteTemp $explosionLoteTemp, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM explosion_lote_temp ';
				$sql .= 'WHERE cod_explosion_lote_temp = ' . Datos::objectToDB($explosionLoteTemp->id) . '; ';
			//} elseif ($modo == Modos::insert) {
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE explosion_lote_temp SET ';
				for($i = 1; $i < 11; $i++)
					$sql .= 'cant_' . $i . ' = ' . Datos::objectToDB($explosionLoteTemp->cantidadesComprar[$i]) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = ' . 'GETDATE()' . ' ' ;
				$sql .= 'WHERE cod_explosion_lote_temp = ' . Datos::objectToDB($explosionLoteTemp->id) . '; ';
			//} elseif ($modo == Modos::delete) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryFactura(Factura $factura, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= $this->mapperQueryDocumentoDebe($factura, $modo, false);
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO documentos_c (';
				$sql .= 'empresa, ';
				$sql .= 'punto_venta, ';
				$sql .= 'tipo_docum, ';
				$sql .= 'nro_documento, ';
				$sql .= 'letra, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_cliente, ';
				$sql .= 'cod_sucursal, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'tiene_detalle, ';
				$sql .= 'iva_porc_1, ';
				$sql .= 'iva_importe_1, ';
				$sql .= 'iva_porc_2, ';
				$sql .= 'iva_importe_2, ';
				$sql .= 'iva_porc_3, ';
				$sql .= 'iva_importe_3, ';
				$sql .= 'importe_neto, ';
				$sql .= 'importe_no_gravado, ';
				$sql .= 'importe_total, ';
				$sql .= 'importe_pendiente, ';
				$sql .= 'descuento_comercial_importe, ';
				$sql .= 'descuento_comercial_porc, ';
				$sql .= 'descuento_despacho_importe, ';
				$sql .= 'cod_forma_pago, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'cod_ecommerce_order, ';
				$sql .= 'observaciones, ';
				$sql .= 'fecha_documento, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($factura->empresa) . ', ';
				$sql .= Datos::objectToDB($factura->puntoDeVenta) . ', ';
				$sql .= Datos::objectToDB($factura->tipoDocumento) . ', ';
				$sql .= Datos::objectToDB($factura->numero) . ', ';
				$sql .= Datos::objectToDB($factura->letra) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($factura->cliente->id) . ', ';
				$sql .= Datos::objectToDB($factura->sucursal->id) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB($factura->tieneDetalle) . ', ';
				$sql .= Datos::objectToDB($factura->ivaPorcentaje1) . ', ';
				$sql .= Datos::objectToDB($factura->ivaImporte1) . ', ';
				$sql .= Datos::objectToDB($factura->ivaPorcentaje2) . ', ';
				$sql .= Datos::objectToDB($factura->ivaImporte2) . ', ';
				$sql .= Datos::objectToDB($factura->ivaPorcentaje3) . ', ';
				$sql .= Datos::objectToDB($factura->ivaImporte3) . ', ';
				$sql .= Datos::objectToDB($factura->importeNeto) . ', ';
				$sql .= Datos::objectToDB($factura->importeNoGravado) . ', ';
				$sql .= Datos::objectToDB($factura->importeTotal) . ', ';
				$sql .= Datos::objectToDB($factura->importePendiente) . ', ';
				$sql .= Datos::objectToDB($factura->descuentoComercialImporte) . ', ';
				$sql .= Datos::objectToDB($factura->descuentoComercialPorc) . ', ';
				$sql .= Datos::objectToDB($factura->descuentoDespachoImporte) . ', ';
				$sql .= Datos::objectToDB($factura->formaDePago->id) . ', ';
				$sql .= Datos::objectToDB($factura->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB($factura->ecommerceOrder->id) . ', ';
				$sql .= Datos::objectToDB($factura->observaciones) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
				if (!$factura->tieneDetalle()) {
					//Si tieneDetalle == 'S' es porque no tiene articulos en despachos_d, sino en documentos_d. No es el camino comï¿½n
					//Para los casos de tieneDetalle == 'S' se manejan desde el Factory
					foreach ($factura->detalle as $remito) {
						$sql .= 'UPDATE remitos_c SET ';
						$sql .= 'nro_factura = ' . Datos::objectToDB($factura->numero) . ', ';
						$sql .= 'punto_venta_factura = ' . Datos::objectToDB($factura->puntoDeVenta) . ', ';
						$sql .= 'tipo_docum_factura = ' . Datos::objectToDB($factura->tipoDocumento) . ', ';
						$sql .= 'letra_factura = ' . Datos::objectToDB($factura->letra) . ' ';
						$sql .= 'WHERE empresa = ' . Datos::objectToDB($remito->empresa) . ' ';
						$sql .= 'AND nro_remito = ' . Datos::objectToDB($remito->numero) . ' ';
						$sql .= 'AND letra = ' . Datos::objectToDB($remito->letra) . '; ';
					}
				}
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE documentos_c SET ';
				$sql .= 'nro_comprobante = ' . Datos::objectToDB($factura->numeroComprobante) . ', ';
				$sql .= 'cae = ' . Datos::objectToDB($factura->cae) . ', ';
				$sql .= 'cae_vencimiento = dbo.toDate(' . Datos::objectToDB($factura->caeFechaVencimiento) . '), ';
				$sql .= 'cae_obtencion_fecha = dbo.toDate(' . Datos::objectToDB($factura->caeObtencionFecha) . '), ';
				$sql .= 'cae_obtencion_observaciones = ' . Datos::objectToDB($factura->caeObtencionObservaciones) . ', ';
				$sql .= 'cae_obtencion_usuario = ' . Datos::objectToDB($factura->caeObtencionUsuario->id) . ', ';
				$sql .= 'mail_enviado = ' . Datos::objectToDB($factura->mailEnviado) . ', ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($factura->asientoContable->id) . ', ';
				$sql .= 'cod_ecommerce_order = ' . Datos::objectToDB($factura->ecommerceOrder->id) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($factura->empresa) . ' ';
				$sql .= 'AND punto_venta = ' . Datos::objectToDB($factura->puntoDeVenta) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($factura->tipoDocumento) . ' ';
				$sql .= 'AND nro_documento = ' . Datos::objectToDB($factura->numero) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($factura->letra) . '; ';
			} elseif ($modo == Modos::delete) {
				//Si no tiene CAE, puedo volver to_do atrï¿½s! Si tiene CAE, no
				if (isset($factura->cae))
					throw new FactoryException('No se puede borrar la factura ya que tiene CAE. Debe hacer una nota de crédito');
				if (!$factura->tieneDetalle()) {
					foreach ($factura->detalle as $remito) {
						$sql .= 'UPDATE remitos_c SET ';
						$sql .= 'nro_factura = NULL, ';
						$sql .= 'punto_venta_factura = NULL, ';
						$sql .= 'tipo_docum_factura = NULL, ';
						$sql .= 'letra_factura = NULL ';
						$sql .= 'WHERE empresa = ' . Datos::objectToDB($remito->empresa) . ' ';
						$sql .= 'AND nro_remito = ' . Datos::objectToDB($remito->numero) . ' ';
						$sql .= 'AND letra = ' . Datos::objectToDB($remito->letra) . '; ';
					}
				}
				$sql .= 'UPDATE documentos_c SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($factura->empresa) . ' ';
				$sql .= 'AND punto_venta = ' . Datos::objectToDB($factura->puntoDeVenta) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($factura->tipoDocumento) . ' ';
				$sql .= 'AND nro_documento = ' . Datos::objectToDB($factura->numero) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($factura->letra) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_documento), 0) + 1 FROM documentos_c ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($factura->empresa) . ' ';
				$sql .= 'AND punto_venta = ' . Datos::objectToDB($factura->puntoDeVenta) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($factura->tipoDocumento) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($factura->letra) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryFacturaProveedor(FacturaProveedor $facturaProveedor, $modo){
		return $this->mapperQueryDocumentoProveedor($facturaProveedor, $modo);
	}
	private function mapperQueryFajaHoraria(FajaHoraria $fajaHoraria, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM horarios_por_secciones ';
				$sql .= 'WHERE cod_faja_horaria = ' . Datos::objectToDB($fajaHoraria->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO horarios_por_secciones (';
				$sql .= 'cod_faja_horaria, ';
				$sql .= 'anulado, ';
				$sql .= 'denominacion_horario, ';
				$sql .= 'horario_entrada, ';
				$sql .= 'horario_salida ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($fajaHoraria->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($fajaHoraria->nombre) . ', ';
				$sql .= 'dbo.toDate(' . Datos::objectToDB('01/01/1990 ' . $fajaHoraria->horarioEntrada) . '), ';
				$sql .= 'dbo.toDate(' . Datos::objectToDB('01/01/1990 ' . $fajaHoraria->horarioSalida) . ') ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE horarios_por_secciones SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($fajaHoraria->anulado) . ', ';
				$sql .= 'denominacion_horario = ' . Datos::objectToDB($fajaHoraria->nombre) . ', ';
				$sql .= 'horario_entrada = dbo.toDate(' . Datos::objectToDB('01/01/1990 ' . $fajaHoraria->horarioEntrada) . '), ';
				$sql .= 'horario_salida = dbo.toDate(' . Datos::objectToDB('01/01/1990 ' . $fajaHoraria->horarioSalida) . ') ';
				$sql .= 'WHERE cod_faja_horaria = ' . Datos::objectToDB($fajaHoraria->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE horarios_por_secciones SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_faja_horaria = ' . Datos::objectToDB($fajaHoraria->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_faja_horaria), 0) + 1 FROM horarios_por_secciones;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryFasonier(Fasonier $fasonier, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM fasonier_v ';
				$sql .= 'WHERE cod_operador = ' . Datos::objectToDB($fasonier->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO proveedores_datos (';
				$sql .= 'cod_prov, ';
				$sql .= 'anulado, ';
				$sql .= 'concepto_reten_ganancias, ';
				$sql .= 'condicion_iva, ';
				$sql .= 'cuenta_acumuladora, ';
				$sql .= 'cuit, ';
				$sql .= 'denom_fantasia, ';
				$sql .= 'denominacion_cta_acum, ';
				$sql .= 'calle, ';
				$sql .= 'cod_postal, ';
				$sql .= 'oficina_depto, ';
				$sql .= 'cod_localidad, ';
				$sql .= 'cod_localidad_nro, ';
				$sql .= 'numero, ';
				$sql .= 'pais, ';
				$sql .= 'partido_departamento, ';
				$sql .= 'piso, ';
				$sql .= 'provincia, ';
				$sql .= 'e_mail, ';
				$sql .= 'fax, ';
				$sql .= 'horarios_atencion, ';
				$sql .= 'imputacion_en_compra, ';
				$sql .= 'jurisd_1_ingr_brutos, ';
				$sql .= 'jurisd_2_ingr_brutos, ';
				$sql .= 'limite_credito, ';
				$sql .= 'observaciones, ';
				$sql .= 'pagina_web, ';
				$sql .= 'plazo_pago, ';
				$sql .= 'plazo_pago_primera_entrega, ';
				$sql .= 'razon_social, ';
				$sql .= 'retencion_especial, ';
				$sql .= 'retener_imp_ganancias, ';
				$sql .= 'retener_ingr_brutos, ';
				$sql .= 'retener_iva, ';
				$sql .= 'rubro, ';
				$sql .= 'telefono_1, ';
				$sql .= 'telefono_2, ';
				$sql .= 'tipo_proveedor, ';
				$sql .= 'cod_transporte, ';
				$sql .= 'primera_entrega ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($fasonier->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($fasonier->conceptoRetenGanancias) . ', ';
				$sql .= Datos::objectToDB($fasonier->condicionIva->id) . ', ';
				$sql .= Datos::objectToDB($fasonier->cuentaAcumuladora) . ', ';
				$sql .= Datos::objectToDB($fasonier->cuit) . ', ';
				$sql .= Datos::objectToDB($fasonier->nombre) . ', ';
				$sql .= Datos::objectToDB($fasonier->denominacionCuentaAcumuladora) . ', ';
				$sql .= Datos::objectToDB($fasonier->direccionCalle) . ', ';
				$sql .= Datos::objectToDB($fasonier->direccionCodigoPostal) . ', ';
				$sql .= Datos::objectToDB($fasonier->direccionDepartamento) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($fasonier->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= Datos::objectToDB($fasonier->direccionLocalidad->id) . ', ';
				$sql .= Datos::objectToDB($fasonier->direccionNumero) . ', ';
				$sql .= Datos::objectToDB($fasonier->direccionPais->id) . ', ';
				$sql .= Datos::objectToDB($fasonier->direccionPartidoDepartamento) . ', ';
				$sql .= Datos::objectToDB($fasonier->direccionPiso) . ', ';
				$sql .= Datos::objectToDB($fasonier->direccionProvincia->id) . ', ';
				$sql .= Datos::objectToDB($fasonier->email) . ', ';
				$sql .= Datos::objectToDB($fasonier->fax) . ', ';
				$sql .= Datos::objectToDB($fasonier->horariosAtencion) . ', ';
				$sql .= Datos::objectToDB($fasonier->imputacionEnCompra) . ', ';
				$sql .= Datos::objectToDB($fasonier->jurisdiccion1IngresosBrutos) . ', ';
				$sql .= Datos::objectToDB($fasonier->jurisdiccion2IngresosBrutos) . ', ';
				$sql .= Datos::objectToDB($fasonier->limiteCredito) . ', ';
				$sql .= Datos::objectToDB($fasonier->observaciones) . ', ';
				$sql .= Datos::objectToDB($fasonier->paginaWeb) . ', ';
				$sql .= Datos::objectToDB($fasonier->plazoPago) . ', ';
				$sql .= Datos::objectToDB($fasonier->plazoPagoPrimeraEntrega) . ', ';
				$sql .= Datos::objectToDB($fasonier->razonSocial) . ', ';
				$sql .= Datos::objectToDB($fasonier->retencionEspecial) . ', ';
				$sql .= Datos::objectToDB($fasonier->retenerImpuestoGanancias) . ', ';
				$sql .= Datos::objectToDB($fasonier->retenerIngresosBrutos) . ', ';
				$sql .= Datos::objectToDB($fasonier->retenerIva) . ', ';
				$sql .= Datos::objectToDB($fasonier->rubroPalabra) . ', ';
				$sql .= Datos::objectToDB($fasonier->telefono1) . ', ';
				$sql .= Datos::objectToDB($fasonier->telefono2) . ', ';
				$sql .= Datos::objectToDB($fasonier->tipoProveedor->id) . ', ';
				$sql .= Datos::objectToDB($fasonier->transporte->id) . ', ';
				$sql .= Datos::objectToDB($fasonier->vencimiento) . ' ';
				$sql .= '); ';
				$sql .= 'INSERT INTO Operadores (';
				$sql .= 'cod_operador, ';
				$sql .= 'cod_proveedor, ';
				$sql .= 'anulado, ';
				$sql .= 'tipo_operador ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($fasonier->tipoOperador . Funciones::padLeft($fasonier->id, 5, '0')) . ', ';
				$sql .= Datos::objectToDB($fasonier->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($fasonier->tipoOperador) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE proveedores_datos SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($fasonier->anulado) . ', ';
				$sql .= 'concepto_reten_ganancias = ' . Datos::objectToDB($fasonier->conceptoRetenGanancias) . ', ';
				$sql .= 'condicion_iva = ' . Datos::objectToDB($fasonier->condicionIva->id) . ', ';
				$sql .= 'cuenta_acumuladora = ' . Datos::objectToDB($fasonier->cuentaAcumuladora) . ', ';
				$sql .= 'cuit = ' . Datos::objectToDB($fasonier->cuit) . ', ';
				$sql .= 'denom_fantasia = ' . Datos::objectToDB($fasonier->nombre) . ', ';
				$sql .= 'denominacion_cta_acum = ' . Datos::objectToDB($fasonier->denominacionCuentaAcumuladora) . ', ';
				$sql .= 'calle = ' . Datos::objectToDB($fasonier->direccionCalle) . ', ';
				$sql .= 'cod_postal = ' . Datos::objectToDB($fasonier->direccionCodigoPostal) . ', ';
				$sql .= 'oficina_depto = ' . Datos::objectToDB($fasonier->direccionDepartamento) . ', ';
				$sql .= 'cod_localidad_nro = ' . Datos::objectToDB($fasonier->direccionLocalidad->id) . ', ';
				$sql .= 'cod_localidad = ' . Datos::objectToDB(Funciones::padLeft($fasonier->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= 'numero = ' . Datos::objectToDB($fasonier->direccionNumero) . ', ';
				$sql .= 'pais = ' . Datos::objectToDB($fasonier->direccionPais->id) . ', ';
				$sql .= 'partido_departamento = ' . Datos::objectToDB($fasonier->direccionPartidoDepartamento) . ', ';
				$sql .= 'piso = ' . Datos::objectToDB($fasonier->direccionPiso) . ', ';
				$sql .= 'provincia = ' . Datos::objectToDB($fasonier->direccionProvincia->id) . ', ';
				$sql .= 'e_mail = ' . Datos::objectToDB($fasonier->email) . ', ';
				$sql .= 'fax = ' . Datos::objectToDB($fasonier->fax) . ', ';
				$sql .= 'horarios_atencion = ' . Datos::objectToDB($fasonier->horariosAtencion) . ', ';
				$sql .= 'imputacion_en_compra = ' . Datos::objectToDB($fasonier->imputacionEnCompra) . ', ';
				$sql .= 'jurisd_1_ingr_brutos = ' . Datos::objectToDB($fasonier->jurisdiccion1IngresosBrutos) . ', ';
				$sql .= 'jurisd_2_ingr_brutos = ' . Datos::objectToDB($fasonier->jurisdiccion2IngresosBrutos) . ', ';
				$sql .= 'limite_credito = ' . Datos::objectToDB($fasonier->limiteCredito) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($fasonier->observaciones) . ', ';
				$sql .= 'pagina_web = ' . Datos::objectToDB($fasonier->paginaWeb) . ', ';
				$sql .= 'plazo_pago = ' . Datos::objectToDB($fasonier->plazoPago) . ', ';
				$sql .= 'plazo_pago_primera_entrega = ' . Datos::objectToDB($fasonier->plazoPagoPrimeraEntrega) . ', ';
				$sql .= 'razon_social = ' . Datos::objectToDB($fasonier->razonSocial) . ', ';
				$sql .= 'retencion_especial = ' . Datos::objectToDB($fasonier->retencionEspecial) . ', ';
				$sql .= 'retener_imp_ganancias = ' . Datos::objectToDB($fasonier->retenerImpuestoGanancias) . ', ';
				$sql .= 'retener_ingr_brutos = ' . Datos::objectToDB($fasonier->retenerIngresosBrutos) . ', ';
				$sql .= 'retener_iva = ' . Datos::objectToDB($fasonier->retenerIva) . ', ';
				$sql .= 'rubro = ' . Datos::objectToDB($fasonier->rubroPalabra) . ', ';
				$sql .= 'telefono_1 = ' . Datos::objectToDB($fasonier->telefono1) . ', ';
				$sql .= 'telefono_2 = ' . Datos::objectToDB($fasonier->telefono2) . ', ';
				$sql .= 'tipo_proveedor = ' . Datos::objectToDB($fasonier->tipoProveedor->id) . ', ';
				$sql .= 'cod_transporte = ' . Datos::objectToDB($fasonier->transporte->id) . ', ';
				$sql .= 'primera_entrega = ' . Datos::objectToDB($fasonier->vencimiento) . ' ';
				$sql .= 'WHERE cod_prov = ' . Datos::objectToDB($fasonier->id) . '; ';
				$sql .= 'UPDATE Operadores SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($fasonier->anulado) . ' ';
				$sql .= 'WHERE cod_operador = ' . Datos::objectToDB($fasonier->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE proveedores_datos SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_prov = ' . Datos::objectToDB($fasonier->id) . '; ';
				$sql .= 'UPDATE Operadores SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_operador = ' . Datos::objectToDB($fasonier->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_prov), 0) + 1 FROM proveedores_datos;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryFichaje(Fichaje $fichaje, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM fichajes_v ';
				$sql .= 'WHERE clave_tabla = ' . Datos::objectToDB($fichaje->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO registro_entradas_salidas (';
				$sql .= 'clave_tabla, ';
				$sql .= 'legajo_nro, ';
				$sql .= 'movimiento_tipo, ';
				$sql .= 'con_anomalias, ';
				$sql .= 'fecha, ';
				$sql .= 'entrada_horario, ';
				$sql .= 'diferencia_entrada, ';
				$sql .= 'ubicacion_tipo ';			//Dï¿½nde fichï¿½ entrada
				//$sql .= 'ubicacion_confirmada ';	//Dï¿½nde fichï¿½ salida
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($fichaje->id) . ', ';
				$sql .= Datos::objectToDB($fichaje->legajo) . ', ';
				$sql .= Datos::objectToDB($fichaje->tipo) . ', ';
				$sql .= Datos::objectToDB($fichaje->anomalias) . ', ';
				$sql .= 'dbo.toDate(' . Datos::objectToDB($fichaje->fecha) . '), ';
				$sql .= 'dbo.toDate(' . Datos::objectToDB($fichaje->fecha . ' ' . $fichaje->horaEntrada) . '), ';
				$sql .= Datos::objectToDB($fichaje->diferenciaEntrada) . ', ';
				$sql .= Datos::objectToDB($fichaje->lugarEntrada) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE registro_entradas_salidas SET ';
				//Saco por ahora el update de entrada_horario porque me borra los segundos... una cagada :S igual no es tan grave
				//$sql .= 'entrada_horario = dbo.toDate(' . Datos::objectToDB($fichaje->fecha . ' ' . $fichaje->horaEntrada) . '), ';
				$sql .= 'diferencia_entrada = ' . Datos::objectToDB($fichaje->diferenciaEntrada) . ', ';
				$sql .= 'salida_horario = dbo.toDate(' . Datos::objectToDB($fichaje->fecha . ' ' . $fichaje->horaSalida) . '), ';
				$sql .= 'diferencia_salida = ' . Datos::objectToDB($fichaje->diferenciaSalida) . ', ';
				$sql .= 'con_anomalias = ' . Datos::objectToDB($fichaje->anomalias) . ', ';
				//$sql .= 'ubicacion_tipo = ' . Datos::objectToDB($fichaje->lugarEntrada) . ', ';
				$sql .= 'ubicacion_confirmada = ' . Datos::objectToDB($fichaje->lugarSalida) . ' ';
				$sql .= 'WHERE clave_tabla = ' . Datos::objectToDB($fichaje->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM registro_entradas_salidas ';
				$sql .= 'WHERE clave_tabla = ' . Datos::objectToDB($fichaje->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(clave_tabla), 0) + 1 FROM registro_entradas_salidas;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryFilaAsientoContable(FilaAsientoContable $filaAsientoContable, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM filas_asientos_contables ';
				$sql .= 'WHERE cod_asiento = ' . Datos::objectToDB($filaAsientoContable->idAsientoContable) . ' ';
				$sql .= 'AND numero_fila = ' . Datos::objectToDB($filaAsientoContable->numeroFila) . '; ';
			} elseif ($modo == Modos::insert || $modo == Modos::update) {
				$sql .= 'INSERT INTO filas_asientos_contables (';
				$sql .= 'cod_asiento, ';
				$sql .= 'numero_fila, ';
				$sql .= 'cod_imputacion, ';
				$sql .= 'importe_debe, ';
				$sql .= 'importe_haber, ';
				$sql .= 'fecha_vencimiento, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($filaAsientoContable->idAsientoContable) . ', ';
				$sql .= Datos::objectToDB($filaAsientoContable->numeroFila) . ', ';
				$sql .= Datos::objectToDB($filaAsientoContable->imputacion->id) . ', ';
				$sql .= Datos::objectToDB($filaAsientoContable->importeDebe) . ', ';
				$sql .= Datos::objectToDB($filaAsientoContable->importeHaber) . ', ';
				$sql .= Datos::objectToDB($filaAsientoContable->fechaVencimiento) . ', ';
				$sql .= Datos::objectToDB($filaAsientoContable->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE filas_asientos_contables SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_asiento = ' . Datos::objectToDB($filaAsientoContable->idAsientoContable) . ' ';
				$sql .= 'AND numero_fila = ' . Datos::objectToDB($filaAsientoContable->numeroFila) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
    private function mapperQueryForecast(Forecast $forecast, $modo){
        $sql = '';
        try {
            if ($modo == Modos::select){
                $sql .= 'SELECT * ';
                $sql .= 'FROM Forecast_encabezado ';
                $sql .= 'WHERE IdForecast = ' . Datos::objectToDB($forecast->id) . '; ';
            } elseif ($modo == Modos::insert) {
                $sql .= 'INSERT INTO Forecast_encabezado (';
                $sql .= 'Denom_Forecast, ';
                $sql .= 'Fecha, ';
                $sql .= 'Fecha_ingreso, ';
                $sql .= 'Fecha_Fin, ';
                $sql .= 'Observaciones, ';
                $sql .= 'aprobado, ';
                $sql .= 'anulado ';
                $sql .= ') VALUES (';
                $sql .= Datos::objectToDB($forecast->nombre) . ', ';
                $sql .= 'GETDATE(), ';
                $sql .= Datos::objectToDB($forecast->fechaInicio) . ', ';
                $sql .= Datos::objectToDB($forecast->fechaFin) . ', ';
                $sql .= Datos::objectToDB($forecast->observaciones) . ', ';
                $sql .= Datos::objectToDB('N') . ', ';
                $sql .= Datos::objectToDB('N') . ' ';
                $sql .= '); ';
            } elseif ($modo == Modos::update) {
                $sql .= 'DELETE FROM Forecast_detalle ';
                $sql .= 'WHERE IdForecast = ' . Datos::objectToDB($forecast->id) . '; ';

                $sql .= 'UPDATE Forecast_encabezado SET ';
                $sql .= 'Denom_Forecast = ' . Datos::objectToDB($forecast->nombre) . ', ';
                $sql .= 'Fecha = ' . Datos::objectToDB($forecast->fecha) . ', ';
                $sql .= 'Fecha_ingreso = ' . Datos::objectToDB($forecast->fechaInicio) . ', ';
                $sql .= 'Fecha_Fin = ' . Datos::objectToDB($forecast->fechaFin) . ', ';
                $sql .= 'Observaciones = ' . Datos::objectToDB($forecast->observaciones) . ', ';
                $sql .= 'aprobado = ' . Datos::objectToDB($forecast->importado) . ' ';
                $sql .= 'WHERE IdForecast = ' . Datos::objectToDB($forecast->id) . '; ';
            } elseif ($modo == Modos::delete) {
                $sql .= 'UPDATE Forecast_encabezado SET ';
                $sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
                $sql .= 'WHERE IdForecast = ' . Datos::objectToDB($forecast->id) . '; ';
            } elseif ($modo == Modos::id) {
                $sql .= 'SELECT IDENT_CURRENT(\'Forecast_encabezado\') + IDENT_INCR(\'Forecast_encabezado\');';
            } else {
                throw new FactoryException('Modo incorrecto');
            }
            return $sql;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    private function mapperQueryForecastItem(ForecastItem $forecastItem, $modo){
        $sql = '';
        try {
            if ($modo == Modos::select){
                $sql .= 'SELECT * ';
                $sql .= 'FROM Forecast_detalle ';
                $sql .= 'WHERE id = ' . Datos::objectToDB($forecastItem->id) . '; ';
            } elseif ($modo == Modos::insert) {
                $sql .= 'INSERT INTO Forecast_detalle (';
                $sql .= 'IdForecast, ';
                $sql .= 'cod_articulo, ';
                $sql .= 'cod_color_articulo, ';
                $sql .= 'version, ';
                for ($i = 1; $i <= 10; $i++)
                    $sql .= 'cant_' . $i . ', ';
                $sql .= 'cantidad ';
                $sql .= ') VALUES (';
                $sql .= Datos::objectToDB($forecastItem->forecast->id) . ', ';
                $sql .= Datos::objectToDB($forecastItem->articulo->id) . ', ';
                $sql .= Datos::objectToDB($forecastItem->colorPorArticulo->id) . ', ';
                $sql .= Datos::objectToDB($forecastItem->patron->version) . ', ';
                for ($i = 1; $i <= 10; $i++)
                    $sql .= Datos::objectToDB($forecastItem->cantidad[$i]) . ', ';
                $sql .= Datos::objectToDB(Funciones::sumaArray($forecastItem->cantidad)) . ' ';
                $sql .= '); ';
                //} elseif ($modo == Modos::update) {
                //} elseif ($modo == Modos::delete) {
            } elseif ($modo == Modos::id) {
                $sql .= 'SELECT IDENT_CURRENT(\'Forecast_detalle\') + IDENT_INCR(\'Forecast_detalle\');';
            } else {
                throw new FactoryException('Modo incorrecto');
            }
            return $sql;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
	private function mapperQueryFormaDePago(FormaDePago $formaDePago, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Formas_pago ';
				$sql .= 'WHERE cod_forma_pago_num = ' . Datos::objectToDB($formaDePago->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Formas_pago (';
				$sql .= 'cod_forma_pago_num, ';
				$sql .= 'cod_forma_pago, ';
				$sql .= 'anulado, ';
				$sql .= 'denom_forma_pago ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($formaDePago->id) . ', ';
				$sql .= 'dbo.PadLeft(' . Datos::objectToDB($formaDePago->id) . ', \'0\', 2), ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($formaDePago->nombre) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Formas_pago SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($formaDePago->anulado) . ', ';
				$sql .= 'denom_forma_pago = ' . Datos::objectToDB($formaDePago->nombre) . ' ';
				$sql .= 'WHERE cod_forma_pago_num = ' . Datos::objectToDB($formaDePago->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Formas_pago SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_forma_pago_num = ' . Datos::objectToDB($formaDePago->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryFormulario(/** @noinspection PhpUnusedParameterInspection */ Formulario $formulario, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				/*
				$sql .= 'SELECT * ';
				$sql .= 'FROM Formularios ';
				$sql .= 'WHERE cod_formulario = ' . Datos::objectToDB($formulario->id) . '; ';
				*/
			} elseif ($modo == Modos::insert) {
				/*
				$sql .= 'INSERT INTO Formularios (';
				$sql .= 'anulado, ';
				$sql .= 'nombre, ';
				$sql .= 'empresa, ';
				$sql .= 'tipo_docum, ';
				$sql .= 'letra, ';
				$sql .= 'html ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($formulario->nombre) . ', ';
				$sql .= Datos::objectToDB($formulario->empresa) . ', ';
				$sql .= Datos::objectToDB($formulario->tipoDocumento) . ', ';
				$sql .= Datos::objectToDB($formulario->letra) . ', ';
				$sql .= Datos::objectToDB($formulario->html) . ' ';
				$sql .= '); ';
				*/
			} elseif ($modo == Modos::update) {
				/*
				$sql .= 'UPDATE Formularios SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($formulario->anulado) . ', ';
				$sql .= 'nombre = ' . Datos::objectToDB($formulario->nombre) . ', ';
				$sql .= 'empresa = ' . Datos::objectToDB($formulario->empresa) . ', ';
				$sql .= 'tipo_docum = ' . Datos::objectToDB($formulario->tipoDocumento) . ', ';
				$sql .= 'letra = ' . Datos::objectToDB($formulario->letra) . ', ';
				$sql .= 'html = ' . Datos::objectToDB($formulario->html) . ' ';
				$sql .= 'WHERE cod_formulario = ' . Datos::objectToDB($formulario->id) . '; ';
				*/
			} elseif ($modo == Modos::delete) {
				/*
				$sql .= 'UPDATE Formularios SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_formulario = ' . Datos::objectToDB($formulario->id) . '; ';
				*/
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryFuncionalidadPorRol(FuncionalidadPorRol $funcionalidadPorRol, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM funcionalidades_por_rol ';
				$sql .= 'WHERE cod_rol = ' . Datos::objectToDB($funcionalidadPorRol->idRol) . ' ';
				$sql .= 'AND cod_funcionalidad = ' . Datos::objectToDB($funcionalidadPorRol->idFuncionalidad) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO funcionalidades_por_rol (';
				$sql .= 'cod_rol, ';
				$sql .= 'cod_funcionalidad ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($funcionalidadPorRol->idRol) . ', ';
				$sql .= Datos::objectToDB($funcionalidadPorRol->idFuncionalidad) . ' ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM funcionalidades_por_rol WHERE ';
				$sql .= 'cod_rol = ' . Datos::objectToDB($funcionalidadPorRol->idRol) . ' AND ';
				$sql .= 'cod_funcionalidad = ' . Datos::objectToDB($funcionalidadPorRol->idFuncionalidad) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryGastito(Gastito $gastito, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM gastito ';
				$sql .= 'WHERE cod_gastito = ' . Datos::objectToDB($gastito->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO gastito (';
				$sql .= 'cod_gastito, ';
				$sql .= 'empresa, ';
				$sql .= 'importe, ';
				$sql .= 'fecha, ';
				$sql .= 'cod_persona_gasto, ';
				$sql .= 'comprobante, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_caja, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($gastito->id) . ', ';
				$sql .= Datos::objectToDB($gastito->empresa) . ', ';
				$sql .= Datos::objectToDB($gastito->importe) . ', ';
				$sql .= Datos::objectToDB($gastito->fecha) . ', ';
				$sql .= Datos::objectToDB($gastito->personaGasto->id) . ', ';
				$sql .= Datos::objectToDB($gastito->comprobante) . ', ';
				$sql .= Datos::objectToDB($gastito->observaciones) . ', ';
				$sql .= Datos::objectToDB($gastito->caja->id) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE gastito SET ';
				$sql .= 'importe = ' . Datos::objectToDB($gastito->importe) . ', ';
				$sql .= 'fecha = ' . Datos::objectToDB($gastito->fecha) . ', ';
				$sql .= 'cod_persona_gasto = ' . Datos::objectToDB($gastito->personaGasto->id) . ', ';
				$sql .= 'comprobante = ' . Datos::objectToDB($gastito->comprobante) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($gastito->observaciones) . ', ';
				$sql .= 'cod_rendicion_gastos = ' . Datos::objectToDB($gastito->rendicionGastos->numero) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_gastito = ' . Datos::objectToDB($gastito->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM gastito ';
				$sql .= 'WHERE cod_gastito = ' . Datos::objectToDB($gastito->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_gastito), 0) + 1 FROM gastito;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryGarantia(Garantia $garantia, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM garantias_c ';
				$sql .= 'WHERE cod_garantia = ' . Datos::objectToDB($garantia->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO garantias_c (';
				$sql .= 'cod_cliente, ';
				$sql .= 'cod_order, ';
				$sql .= 'total_ncr, ';
				$sql .= 'derivada, ';
				$sql .= 'cod_motivo, ';
				$sql .= 'observaciones, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($garantia->cliente->id) . ', ';
				$sql .= Datos::objectToDB($garantia->order->id) . ', ';
				$sql .= Datos::objectToDB($garantia->totalNcr) . ', ';
				$sql .= Datos::objectToDB($garantia->derivada) . ', ';
				$sql .= Datos::objectToDB($garantia->motivo->id) . ', ';
				$sql .= Datos::objectToDB($garantia->observaciones) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE garantias_c SET ';
				$sql .= 'total_ncr = ' . Datos::objectToDB($garantia->totalNcr) . ', ';
				$sql .= 'clasificada = ' . Datos::objectToDB($garantia->clasificada) . ', ';
				$sql .= 'devuelta = ' . Datos::objectToDB($garantia->devuelta) . ', ';
				$sql .= 'solucion_ncr = ' . Datos::objectToDB($garantia->solucionNcr) . ', ';
				$sql .= 'movimientos = ' . Datos::objectToDB($garantia->movimientos) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_garantia = ' . Datos::objectToDB($garantia->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE garantias_c SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_garantia = ' . Datos::objectToDB($garantia->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT IDENT_CURRENT(\'garantias_c\') + IDENT_INCR(\'garantias_c\');';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryGarantiaItem(GarantiaItem $garantiaItem, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM garantias_d ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($garantiaItem->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO garantias_d (';
				$sql .= 'id, ';
				$sql .= 'cod_garantia, ';
				$sql .= 'cod_almacen, ';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				$sql .= 'importe_ncr, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'cantidad ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($garantiaItem->id) . ', ';
				$sql .= Datos::objectToDB($garantiaItem->idGarantia) . ', ';
				$sql .= Datos::objectToDB($garantiaItem->almacen->id) . ', ';
				$sql .= Datos::objectToDB($garantiaItem->articulo->id) . ', ';
				$sql .= Datos::objectToDB($garantiaItem->colorPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($garantiaItem->importeNcr) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($garantiaItem->cantidad[$i]) . ', ';
				$sql .= Datos::objectToDB($garantiaItem->cantidadTotal) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE garantias_d SET ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ' = ' . Datos::objectToDB($garantiaItem->cantidad[$i]) .  ', ';
				$sql .= 'cantidad = ' . Datos::objectToDB($garantiaItem->cantidadTotal) .  ', ';
				$sql .= 'importe_ncr = ' . Datos::objectToDB($garantiaItem->importeNcr) .  ' ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($garantiaItem->id) . '; ';
			//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(id), 0) + 1 FROM garantias_d;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQuerySeguimientoCliente(SeguimientoCliente $seguimientoCliente, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM gestiones_clientes_cobranza ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($seguimientoCliente->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO gestiones_clientes_cobranza (';
				$sql .= 'id, ';
				$sql .= 'cod_cli, ';
				$sql .= 'fecha_gestion, ';
				$sql .= 'observaciones, ';
				$sql .= 'estado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($seguimientoCliente->id) . ', ';
				$sql .= Datos::objectToDB($seguimientoCliente->cliente->id) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= Datos::objectToDB($seguimientoCliente->observaciones) . ', ';
				$sql .= Datos::objectToDB($seguimientoCliente->estado) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE gestiones_clientes_cobranza SET ';
				$sql .= 'observaciones = ' . Datos::objectToDB($seguimientoCliente->observaciones) . ', ';
				$sql .= 'estado = ' . Datos::objectToDB($seguimientoCliente->estado) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($seguimientoCliente->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE gestiones_clientes_cobranza SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($seguimientoCliente->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(id), 0) + 1 FROM gestiones_clientes_cobranza;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryGrupoEmpresa(GrupoEmpresa $grupoEmpresa, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM grupo_empresa ';
				$sql .= 'WHERE cod_grupo_empresa = ' . Datos::objectToDB($grupoEmpresa->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO grupo_empresa (';
				$sql .= 'cod_grupo_empresa, ';
				$sql .= 'denominacion, ';
				$sql .= 'Comision_ventas, ';
				$sql .= 'anulado ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($grupoEmpresa->id) . ', ';
				$sql .= Datos::objectToDB($grupoEmpresa->nombre) . ', ';
				$sql .= Datos::objectToDB($grupoEmpresa->comisionPorVentas) . ', ';
				$sql .= Datos::objectToDB('N') . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE grupo_empresa SET ';
				$sql .= 'denominacion = ' . Datos::objectToDB($grupoEmpresa->nombre) . ', ';
				$sql .= 'Comision_ventas = ' . Datos::objectToDB($grupoEmpresa->comisionPorVentas) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB($grupoEmpresa->anulado) . ' ';
				$sql .= 'WHERE cod_grupo_empresa = ' . Datos::objectToDB($grupoEmpresa->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE grupo_empresa SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_grupo_empresa = ' . Datos::objectToDB($grupoEmpresa->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryHeartbeat(Heartbeat $heartbeat, $modo){
		$sql = '';
		try {
			if ($modo == Modos::insert) {
				$sql .= 'UPDATE users SET ';
				$sql .= 'fechaUltimaAct = GETDATE() ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($heartbeat->idUsuario) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryKoiTicket(KoiTicket $koiTicket, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM koi_ticket_v ';
				$sql .= 'WHERE cod_koi_ticket = ' . Datos::objectToDB($koiTicket->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO koi_ticket (';
				$sql .= 'cod_koi_ticket, ';
				$sql .= 'cod_area_empresa, ';
				$sql .= 'descripcion, ';
				$sql .= 'prioridad_externa, ';
				$sql .= 'prioridad, ';
				$sql .= 'cod_ticket_original, ';
				$sql .= 'estado, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($koiTicket->id) . ', ';
				$sql .= Datos::objectToDB($koiTicket->areaEmpresa->id) . ', ';
				$sql .= Datos::objectToDB($koiTicket->descripcion) . ', ';
				$sql .= Datos::objectToDB($koiTicket->prioridadExterna) . ', ';
				$sql .= Datos::objectToDB($koiTicket->prioridad) . ', ';
				$sql .= Datos::objectToDB($koiTicket->ticketOriginal->id) . ', ';
				$sql .= Datos::objectToDB($koiTicket->estado) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($koiTicket->usuario->id) . ', '; //Estï¿½ asï¿½ para los casos en los que viene de un ticket delegado
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE koi_ticket SET ';
				$sql .= 'descripcion = ' . Datos::objectToDB($koiTicket->descripcion) . ', ';
				$sql .= 'respuesta = ' . Datos::objectToDB($koiTicket->respuesta) . ', ';
				$sql .= 'prioridad_externa = ' . Datos::objectToDB($koiTicket->prioridadExterna) . ', ';
				$sql .= 'prioridad_interna = ' . Datos::objectToDB($koiTicket->prioridadInterna) . ', ';
				$sql .= 'prioridad = ' . Datos::objectToDB($koiTicket->prioridad) . ', ';
				$sql .= 'cod_responsable = ' . Datos::objectToDB($koiTicket->responsable->id) . ', ';
				$sql .= 'fecha_estimada_resolucion = ' . Datos::objectToDB($koiTicket->fechaEstimadaResolucion) . ', ';
				$sql .= 'estado = ' . Datos::objectToDB($koiTicket->estado) . ', ';
				$sql .= 'cod_usuario_cierre = ' . Datos::objectToDB($koiTicket->usuarioCierre->id) . ', ';
				$sql .= 'fecha_cierre = ' . Datos::objectToDB($koiTicket->fechaCierre) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_koi_ticket = ' . Datos::objectToDB($koiTicket->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE koi_ticket SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_koi_ticket = ' . Datos::objectToDB($koiTicket->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_koi_ticket), 0) + 1 FROM koi_ticket;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryHorma(Horma $horma, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM hormas ';
				$sql .= 'WHERE cod_horma = ' . Datos::objectToDB($horma->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO hormas (';
				$sql .= 'cod_horma, ';
				$sql .= 'activa, ';
				$sql .= 'color_externo, ';
				$sql .= 'decidio_retirar, ';
				$sql .= 'denom_horma, ';
				$sql .= 'diseñador, ';
				$sql .= 'fabricante, ';
				$sql .= 'observaciones, ';
				$sql .= 'punto, ';
				$sql .= 'talles_desde, ';
				$sql .= 'talles_hasta, ';
				$sql .= 'incorporada_fecha ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($horma->id) . ', ';
				$sql .= Datos::objectToDB('S') . ', ';
				$sql .= Datos::objectToDB($horma->colorExterno) . ', ';
				$sql .= Datos::objectToDB($horma->retiradaPor) . ', ';
				$sql .= Datos::objectToDB($horma->nombre) . ', ';
				$sql .= Datos::objectToDB($horma->diseniador) . ', ';
				$sql .= Datos::objectToDB($horma->fabricante) . ', ';
				$sql .= Datos::objectToDB($horma->observaciones) . ', ';
				$sql .= Datos::objectToDB($horma->punto) . ', ';
				$sql .= Datos::objectToDB($horma->talleDesde) . ', ';
				$sql .= Datos::objectToDB($horma->talleHasta) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE hormas SET ';
				$sql .= 'activa = ' . Datos::objectToDB($horma->activa) . ', ';
				if ($horma->activa == 'N')
					$sql .= 'desactivada_fecha = GETDATE(), ';
				$sql .= 'color_externo = ' . Datos::objectToDB($horma->colorExterno) . ', ';
				$sql .= 'decidio_retirar = ' . Datos::objectToDB($horma->retiradaPor) . ', ';
				$sql .= 'denom_horma = ' . Datos::objectToDB($horma->nombre) . ', ';
				$sql .= 'diseñador = ' . Datos::objectToDB($horma->diseniador) . ', ';
				$sql .= 'fabricante = ' . Datos::objectToDB($horma->fabricante) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($horma->observaciones) . ', ';
				$sql .= 'punto = ' . Datos::objectToDB($horma->punto) . ', ';
				$sql .= 'talles_desde = ' . Datos::objectToDB($horma->talleDesde) . ', ';
				$sql .= 'talles_hasta = ' . Datos::objectToDB($horma->talleHasta) . ' ';
				$sql .= 'WHERE cod_horma = ' . Datos::objectToDB($horma->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE hormas SET ';
				$sql .= 'activa = ' . Datos::objectToDB('N') . ', ';
				$sql .= 'desactivada_fecha = GETDATE() ';
				$sql .= 'WHERE cod_horma = ' . Datos::objectToDB($horma->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryImportePorOperacion(ImportePorOperacion $ImportePorOperacion, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM importe_por_operacion_c ';
				$sql .= 'WHERE cod_importe_operacion = ' . Datos::objectToDB($ImportePorOperacion->idImportePorOperacion) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO importe_por_operacion_c (';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'tipo_transferencia, ';
				$sql .= 'cod_caja, ';
				$sql .= 'fecha_caja, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ImportePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($ImportePorOperacion->tipoOperacion) . ', ';
				$sql .= Datos::objectToDB($ImportePorOperacion->caja->id) . ', ';
				$sql .= Datos::objectToDB($ImportePorOperacion->fechaCaja) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_importe_operacion), 0) + 1 FROM importe_por_operacion_c;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryImportePorOperacionItem(ImportePorOperacionItem $ImportePorOperacionItem, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM importe_por_operacion_d_v ';
				$sql .= 'WHERE cod_importe_operacion = ' . Datos::objectToDB($ImportePorOperacionItem->idImportePorOperacion) . ' AND ';
				$sql .= 'tipo_importe = ' . Datos::objectToDB($ImportePorOperacionItem->tipoImporte) . ' AND ';
				$sql .= 'cod_importe = ' . Datos::objectToDB($ImportePorOperacionItem->idImporte) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO importe_por_operacion_d (';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'tipo_importe, ';
				$sql .= 'cod_importe, ';
				$sql .= 'anulado ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ImportePorOperacionItem->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($ImportePorOperacionItem->tipoImporte) . ', ';
				$sql .= Datos::objectToDB($ImportePorOperacionItem->idImporte) . ', ';
				$sql .= Datos::objectToDB('N') . ' ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE importe_por_operacion_d SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_importe_operacion = ' . Datos::objectToDB($ImportePorOperacionItem->idImportePorOperacion) . ' AND ';
				$sql .= 'tipo_importe = ' . Datos::objectToDB($ImportePorOperacionItem->tipoImporte) . ' AND ';
				$sql .= 'cod_importe = ' . Datos::objectToDB($ImportePorOperacionItem->idImporte) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryImputacion(Imputacion $imputacion, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM plan_cuentas ';
				$sql .= 'WHERE cuenta = ' . Datos::objectToDB($imputacion->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO plan_cuentas (';
				$sql .= 'cuenta, ';
				$sql .= 'denominacion, ';
				$sql .= 'es_imputable, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($imputacion->id) . ', ';
				$sql .= Datos::objectToDB($imputacion->nombre) . ', ';
				$sql .= Datos::objectToDB($imputacion->imputable) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE plan_cuentas SET ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE(), ';
				$sql .= 'cuenta = ' . Datos::objectToDB($imputacion->idNuevo) . ', ';
				$sql .= 'denominacion = ' . Datos::objectToDB($imputacion->nombre) . ', ';
				$sql .= 'es_imputable = ' . Datos::objectToDB($imputacion->imputable) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB($imputacion->anulado) . ' ';
				$sql .= 'WHERE cuenta = ' . Datos::objectToDB($imputacion->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE plan_cuentas SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cuenta = ' . Datos::objectToDB($imputacion->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryImpuesto(Impuesto $impuesto, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM impuesto ';
				$sql .= 'WHERE cod_impuesto = ' . Datos::objectToDB($impuesto->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO impuesto (';
				$sql .= 'cod_impuesto, ';
				$sql .= 'nombre, ';
				$sql .= 'descripcion, ';
				$sql .= 'cod_imputacion, ';
				$sql .= 'porcentaje, ';
				$sql .= 'es_gravado, ';
				$sql .= 'tipo, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($impuesto->id) . ', ';
				$sql .= Datos::objectToDB($impuesto->nombre) . ', ';
				$sql .= Datos::objectToDB($impuesto->descripcion) . ', ';
				$sql .= Datos::objectToDB($impuesto->imputacion->id) . ', ';
				$sql .= Datos::objectToDB($impuesto->porcentaje) . ', ';
				$sql .= Datos::objectToDB($impuesto->esGravado) . ', ';
				$sql .= Datos::objectToDB($impuesto->tipo) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE impuesto SET ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE(), ';
				$sql .= 'nombre = ' . Datos::objectToDB($impuesto->nombre) . ', ';
				$sql .= 'descripcion = ' . Datos::objectToDB($impuesto->descripcion) . ', ';
				$sql .= 'cod_imputacion = ' . Datos::objectToDB($impuesto->imputacion->id) . ', ';
				$sql .= 'porcentaje = ' . Datos::objectToDB($impuesto->porcentaje) . ', ';
				$sql .= 'es_gravado = ' . Datos::objectToDB($impuesto->esGravado) . ', ';
				$sql .= 'tipo = ' . Datos::objectToDB($impuesto->tipo) . ' ';
				$sql .= 'WHERE cod_impuesto = ' . Datos::objectToDB($impuesto->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE impuesto SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_impuesto = ' . Datos::objectToDB($impuesto->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_impuesto), 0) + 1 FROM impuesto;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryImpuestoPorDocumentoProveedor(ImpuestoPorDocumentoProveedor $impuestoPorDocumentoProveedor, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM impuesto_por_documento_proveedor ';
				$sql .= 'WHERE cod_documento_proveedor = ' . Datos::objectToDB($impuestoPorDocumentoProveedor->idDocumentoProveedor) . ' ';
				$sql .= 'AND cod_impuesto = ' . Datos::objectToDB($impuestoPorDocumentoProveedor->idImpuesto) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO impuesto_por_documento_proveedor (';
				$sql .= 'cod_documento_proveedor, ';
				$sql .= 'cod_impuesto, ';
				$sql .= 'porcentaje, ';
				$sql .= 'importe ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($impuestoPorDocumentoProveedor->idDocumentoProveedor) . ', ';
				$sql .= Datos::objectToDB($impuestoPorDocumentoProveedor->impuesto->id) . ', ';
				$sql .= Datos::objectToDB($impuestoPorDocumentoProveedor->porcentaje) . ', ';
				$sql .= Datos::objectToDB($impuestoPorDocumentoProveedor->importe) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE impuesto_por_documento_proveedor SET ';
				$sql .= 'cod_impuesto = ' . Datos::objectToDB($impuestoPorDocumentoProveedor->impuesto->id) . ', ';
				$sql .= 'porcentaje = ' . Datos::objectToDB($impuestoPorDocumentoProveedor->porcentaje) . ', ';
				$sql .= 'importe = ' . Datos::objectToDB($impuestoPorDocumentoProveedor->importe) . ' ';
				$sql .= 'WHERE cod_documento_proveedor = ' . Datos::objectToDB($impuestoPorDocumentoProveedor->idDocumentoProveedor) . ' ';
				$sql .= 'AND cod_impuesto = ' . Datos::objectToDB($impuestoPorDocumentoProveedor->idImpuesto) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM impuesto_por_documento_proveedor ';
				$sql .= 'WHERE cod_documento_proveedor = ' . Datos::objectToDB($impuestoPorDocumentoProveedor->idDocumentoProveedor) . ' ';
				$sql .= 'AND cod_impuesto = ' . Datos::objectToDB($impuestoPorDocumentoProveedor->idImpuesto) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryIndicador(Indicador $indicador, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM indicadores ';
				$sql .= 'WHERE cod_indicador = ' . Datos::objectToDB($indicador->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO indicadores (';
				$sql .= 'cod_indicador, ';
				$sql .= 'nombre, ';
				$sql .= 'descripcion, ';
				$sql .= 'nombre_view, ';
				$sql .= 'valor_1, ';
				$sql .= 'valor_2, ';
				$sql .= 'valor_3, ';
				$sql .= 'fields, ';
				$sql .= 'clausula_where, ';
				$sql .= 'query, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($indicador->id) . ', ';
				$sql .= Datos::objectToDB($indicador->nombre) . ', ';
				$sql .= Datos::objectToDB($indicador->descripcion) . ', ';
				$sql .= Datos::objectToDB($indicador->view) . ', ';
				$sql .= Datos::objectToDB($indicador->valor1) . ', ';
				$sql .= Datos::objectToDB($indicador->valor2) . ', ';
				$sql .= Datos::objectToDB($indicador->valor3) . ', ';
				$sql .= Datos::objectToDB($indicador->fields) . ', ';
				$sql .= Datos::objectToDB($indicador->where) . ', ';
				$sql .= Datos::objectToDB($indicador->query) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'DELETE FROM indicadores_por_rol ';
				$sql .= 'WHERE cod_indicador = ' . Datos::objectToDB($indicador->id) . '; ';
				$sql .= 'UPDATE indicadores SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($indicador->nombre) . ', ';
				$sql .= 'descripcion = ' . Datos::objectToDB($indicador->descripcion) . ', ';
				$sql .= 'nombre_view = ' . Datos::objectToDB($indicador->view) . ', ';
				$sql .= 'valor_1 = ' . Datos::objectToDB($indicador->valor1) . ', ';
				$sql .= 'valor_2 = ' . Datos::objectToDB($indicador->valor2) . ', ';
				$sql .= 'valor_3 = ' . Datos::objectToDB($indicador->valor3) . ', ';
				$sql .= 'fields = ' . Datos::objectToDB($indicador->fields) . ', ';
				$sql .= 'clausula_where = ' . Datos::objectToDB($indicador->where) . ', ';
				$sql .= 'query = ' . Datos::objectToDB($indicador->query) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_indicador = ' . Datos::objectToDB($indicador->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE indicadores SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_indicador = ' . Datos::objectToDB($indicador->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_indicador), 0) + 1 FROM indicadores;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryIndicadorPorRol(IndicadorPorRol $indicadorPorRol, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM indicadores_por_rol_v ';
				$sql .= 'WHERE cod_indicador = ' . Datos::objectToDB($indicadorPorRol->idIndicador) . ' ';
				$sql .= 'AND cod_rol = ' . Datos::objectToDB($indicadorPorRol->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO indicadores_por_rol (';
				$sql .= 'cod_indicador, ';
				$sql .= 'cod_rol ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($indicadorPorRol->idIndicador) . ', ';
				$sql .= Datos::objectToDB($indicadorPorRol->id) . ' ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			//} elseif ($modo == Modos::delete) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryIngresoChequePropio(IngresoChequePropio $ingresoChequePropio, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ingreso_cheque_propio ';
				$sql .= 'WHERE cod_ingreso_cheque_propio = ' . Datos::objectToDB($ingresoChequePropio->numero) . ' AND ';
				$sql .= 'empresa = ' . Datos::objectToDB($ingresoChequePropio->empresa) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ingreso_cheque_propio (';
				$sql .= 'cod_ingreso_cheque_propio, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'importe_total, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ingresoChequePropio->numero) . ', ';
				$sql .= Datos::objectToDB($ingresoChequePropio->empresa) . ', ';
				$sql .= Datos::objectToDB($ingresoChequePropio->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($ingresoChequePropio->importeTotal) . ', ';
				$sql .= Datos::objectToDB($ingresoChequePropio->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_ingreso_cheque_propio), 0) + 1 FROM ingreso_cheque_propio;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryInstruccionArticulo(InstruccionArticulo $instruccionArticulo, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM instrucciones_articulo ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB(Funciones::toString($instruccionArticulo->idArticulo)) . ' ';
				$sql .= 'AND cod_seccion = ' . Datos::objectToDB($instruccionArticulo->idSeccion) . ' ';
				$sql .= 'AND interna = ' . Datos::objectToDB($instruccionArticulo->interna) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO instrucciones_articulo (';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_seccion, ';
				$sql .= 'interna, ';
				$sql .= 'instruccion, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_ultima_modificacion ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($instruccionArticulo->articulo->id) . ', ';
				$sql .= Datos::objectToDB($instruccionArticulo->seccion->id) . ', ';
				$sql .= Datos::objectToDB($instruccionArticulo->interna) . ', ';
				$sql .= Datos::objectToDB($instruccionArticulo->instruccion) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE instrucciones_articulo SET ';
				$sql .= 'instruccion = ' . Datos::objectToDB($instruccionArticulo->instruccion) . ', ';
				$sql .= 'fecha_ultima_modificacion = GETDATE() ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB(Funciones::toString($instruccionArticulo->idArticulo)) . ' ';
				$sql .= 'AND cod_seccion = ' . Datos::objectToDB($instruccionArticulo->idSeccion) . ' ';
				$sql .= 'AND interna = ' . Datos::objectToDB($instruccionArticulo->interna) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE instrucciones_articulo SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_ultima_modificacion = GETDATE() ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB(Funciones::toString($instruccionArticulo->idArticulo)) . ' ';
				$sql .= 'AND cod_seccion = ' . Datos::objectToDB($instruccionArticulo->idSeccion) . ' ';
				$sql .= 'AND interna = ' . Datos::objectToDB($instruccionArticulo->interna) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryLineaProducto(LineaProducto $lineaProducto, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM lineas_productos ';
				$sql .= 'WHERE cod_linea_nro = ' . Datos::objectToDB($lineaProducto->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO lineas_productos (';
				$sql .= 'cod_linea_nro, ';
				$sql .= 'cod_linea, ';
				$sql .= 'anulado, ';
				$sql .= 'denom_linea, ';
				$sql .= 'titulo_catalogo, ';
				$sql .= 'fechaAlta, ';
				$sql .= 'lanzamiento_inicial, ';
				$sql .= 'origen ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($lineaProducto->id) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($lineaProducto->id, 2, '0')) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($lineaProducto->nombre) . ', ';
				$sql .= Datos::objectToDB($lineaProducto->tituloCatalogo) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= Datos::objectToDB($lineaProducto->fechaLanzamiento) . ', ';
				$sql .= Datos::objectToDB($lineaProducto->origen) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE lineas_productos SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($lineaProducto->anulado) . ', ';
				if ($lineaProducto->anulado == 'S')
					$sql .= 'fecha_de_baja = GETDATE(), ';
				$sql .= 'fecha_ultima_modificacion = GETDATE(), ';
				$sql .= 'denom_linea = ' . Datos::objectToDB($lineaProducto->nombre) . ', ';
				$sql .= 'titulo_catalogo = ' . Datos::objectToDB($lineaProducto->tituloCatalogo) . ', ';
				$sql .= 'lanzamiento_inicial = ' . Datos::objectToDB($lineaProducto->fechaLanzamiento) . ', ';
				$sql .= 'origen = ' . Datos::objectToDB($lineaProducto->origen) . ' ';
				$sql .= 'WHERE cod_linea_nro = ' . Datos::objectToDB($lineaProducto->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE lineas_productos SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_de_baja = GETDATE() ';
				$sql .= 'WHERE cod_linea_nro = ' . Datos::objectToDB($lineaProducto->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_linea_nro), 0) + 1 FROM lineas_productos;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryLocalidad(Localidad $localidad, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM localidades ';
				$sql .= 'WHERE cod_pais = ' . Datos::objectToDB($localidad->idPais) . ' AND ';
				$sql .= 'cod_provincia = ' . Datos::objectToDB($localidad->idProvincia) . ' AND ';
				$sql .= 'cod_localidad_nro = ' . Datos::objectToDB($localidad->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO localidades (';
				$sql .= 'cod_localidad_nro, ';
				$sql .= 'cod_localidad, ';
				$sql .= 'cod_pais, ';
				$sql .= 'cod_provincia, ';
				$sql .= 'denom_localidad, ';
				$sql .= 'cod_postal, ';
				$sql .= 'cod_zona_geo, ';
				$sql .= 'anulado ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($localidad->id) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($localidad->id, 3, '0')) . ', ';
				$sql .= Datos::objectToDB($localidad->pais->id) . ', ';
				$sql .= Datos::objectToDB($localidad->provincia->id) . ', ';
				$sql .= Datos::objectToDB($localidad->nombre) . ', ';
				$sql .= Datos::objectToDB($localidad->codigoPostal) . ', ';
				$sql .= Datos::objectToDB($localidad->zona->id) . ', ';
				$sql .= Datos::objectToDB('N') . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE localidades SET ';
				$sql .= 'denom_localidad = ' . Datos::objectToDB($localidad->nombre) . ', ';
				$sql .= 'cod_zona_geo = ' . Datos::objectToDB($localidad->zona->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB($localidad->anulado) . ' ';
				$sql .= 'WHERE cod_pais = ' . Datos::objectToDB($localidad->pais->id) . ' AND ';
				$sql .= 'cod_provincia = ' . Datos::objectToDB($localidad->provincia->id) . ' AND ';
				$sql .= 'cod_localidad_nro = ' . Datos::objectToDB($localidad->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE localidades SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_pais = ' . Datos::objectToDB($localidad->pais->id) . ' AND ';
				$sql .= 'cod_provincia = ' . Datos::objectToDB($localidad->provincia->id) . ' AND ';
				$sql .= 'cod_localidad_nro = ' . Datos::objectToDB($localidad->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_localidad_nro), 0) + 1 FROM localidades;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryMarca(Marca $marca, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Marcas ';
				$sql .= 'WHERE cod_marca = ' . Datos::objectToDB($marca->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Marcas (';
				$sql .= 'cod_marca, ';
				$sql .= 'anulado, ';
				$sql .= 'denom_marca, ';
				$sql .= 'fechaAlta, ';
				$sql .= 'logo ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($marca->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($marca->nombre) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= Datos::objectToDB($marca->logo) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Marcas SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($marca->anulado) . ', ';
				if ($marca->anulado == 'S')
					$sql .= 'fechaBaja = GETDATE(), ';
				$sql .= 'fecha_ultima_modificacion = GETDATE(), ';
				$sql .= 'denom_marca = ' . Datos::objectToDB($marca->nombre) . ', ';
				$sql .= 'logo = ' . Datos::objectToDB($marca->logo) . ' ';
				$sql .= 'WHERE cod_marca = ' . Datos::objectToDB($marca->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Marcas SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fechaBaja = GETDATE() ';
				$sql .= 'WHERE cod_marca = ' . Datos::objectToDB($marca->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryMaterial(Material $material, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM materiales_v ';
				$sql .= 'WHERE cod_material = ' . Datos::objectToDB(Funciones::padLeft($material->id, 4)) . '; ';
			//} elseif ($modo == Modos::insert) {
			//} elseif ($modo == Modos::update) {
			//} elseif ($modo == Modos::delete) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryMotivo(Motivo $motivo, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM motivo ';
				$sql .= 'WHERE cod_motivo = ' . Datos::objectToDB($motivo->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO motivo (';
				$sql .= 'cod_motivo, ';
				$sql .= 'nombre_motivo, ';
				$sql .= 'tipo_motivo, ';
				$sql .= 'descripcion, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($motivo->id) . ', ';
				$sql .= Datos::objectToDB($motivo->nombre) . ', ';
				$sql .= Datos::objectToDB($motivo->tipo) . ', ';
				$sql .= Datos::objectToDB($motivo->descripcion) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE motivo SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fechaBaja = GETDATE() ';
				$sql .= 'WHERE cod_motivo = ' . Datos::objectToDB($motivo->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_motivo), 0) + 1 FROM motivo; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryMotivoAusentismo(MotivoAusentismo $motivoAusentismo, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM motivos_ausentismo ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($motivoAusentismo->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO motivos_ausentismo (';
				$sql .= 'id, ';
				$sql .= 'nombre, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($motivoAusentismo->id) . ', ';
				$sql .= Datos::objectToDB($motivoAusentismo->nombre) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE motivos_ausentismo SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($motivoAusentismo->nombre) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($motivoAusentismo->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE motivos_ausentismo SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($motivoAusentismo->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(id), 0) + 1 FROM motivos_ausentismo; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryMovimientoAlmacen(MovimientoAlmacen $movimientoAlmacen, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM movimientos_almacen ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($movimientoAlmacen->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO movimientos_almacen (';
				$sql .= 'id, ';
				$sql .= 'cod_confirmacion, ';
				$sql .= 'tipo_movimiento, ';
				$sql .= 'cod_almacen, ';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				$sql .= 'motivo, ';
				$sql .= 'cantidad, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($movimientoAlmacen->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacen->confirmacion->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacen->tipoMovimiento) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacen->almacen->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacen->articulo->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacen->colorPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacen->motivo) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacen->cantidadTotal) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($movimientoAlmacen->cantidad[$i]) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(id), 0) + 1 FROM movimientos_almacen; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryMovimientoAlmacenConfirmacion(MovimientoAlmacenConfirmacion $movimientoAlmacenConfirmacion, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM movimientos_almacen_confirmacion ';
				$sql .= 'WHERE cod_confirmacion = ' . Datos::objectToDB($movimientoAlmacenConfirmacion->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO movimientos_almacen_confirmacion (';
				$sql .= 'cod_confirmacion, ';
				$sql .= 'cod_almacen_origen, ';
				$sql .= 'cod_almacen_destino, ';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				$sql .= 'motivo, ';
				$sql .= 'cantidad, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'confirmado, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacion->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacion->almacenOrigen->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacion->almacenDestino->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacion->articulo->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacion->colorPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacion->motivo) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacion->cantidadTotal) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($movimientoAlmacenConfirmacion->cantidad[$i]) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacion->usuario->id ? $movimientoAlmacenConfirmacion->usuario->id : Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE movimientos_almacen_confirmacion SET ';
				$sql .= 'confirmado = ' . Datos::objectToDB($movimientoAlmacenConfirmacion->confirmado) . ', ';
				$sql .= 'cod_usuario_confirmacion = ' . Datos::objectToDB($movimientoAlmacenConfirmacion->usuarioConfirmacion->id) . ', ';
				$sql .= 'fecha_confirmacion = dbo.toDate(' . Datos::objectToDB($movimientoAlmacenConfirmacion->fechaConfirmacion) . ') ';
				$sql .= 'WHERE cod_confirmacion = ' . Datos::objectToDB($movimientoAlmacenConfirmacion->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE movimientos_almacen_confirmacion SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_confirmacion = ' . Datos::objectToDB($movimientoAlmacenConfirmacion->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_confirmacion), 0) + 1 FROM movimientos_almacen_confirmacion; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryMovimientoAlmacenMP(MovimientoAlmacenMP $movimientoAlmacenMP, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM movimientos_almacen_mp ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($movimientoAlmacenMP->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO movimientos_almacen_mp (';
				$sql .= 'id, ';
				$sql .= 'cod_confirmacion, ';
				$sql .= 'tipo_movimiento, ';
				$sql .= 'cod_almacen, ';
				$sql .= 'cod_material, ';
				$sql .= 'cod_color, ';
				$sql .= 'motivo, ';
				$sql .= 'cantidad, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($movimientoAlmacenMP->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenMP->confirmacion->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenMP->tipoMovimiento) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenMP->almacen->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenMP->material->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenMP->colorMateriaPrima->idColor) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenMP->motivo) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenMP->cantidadTotal) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($movimientoAlmacenMP->cantidad[$i]) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(id), 0) + 1 FROM movimientos_almacen_mp; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryMovimientoAlmacenConfirmacionMP(MovimientoAlmacenConfirmacionMP $movimientoAlmacenConfirmacionMP, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM movimientos_almacen_confirmacion_mp ';
				$sql .= 'WHERE cod_confirmacion = ' . Datos::objectToDB($movimientoAlmacenConfirmacionMP->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO movimientos_almacen_confirmacion_mp (';
				$sql .= 'cod_confirmacion, ';
				$sql .= 'cod_almacen_origen, ';
				$sql .= 'cod_almacen_destino, ';
				$sql .= 'cod_material, ';
				$sql .= 'cod_color, ';
				$sql .= 'motivo, ';
				$sql .= 'cantidad, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'confirmado, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacionMP->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacionMP->almacenOrigen->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacionMP->almacenDestino->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacionMP->material->id) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacionMP->colorMateriaPrima->idColor) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacionMP->motivo) . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacionMP->cantidadTotal) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($movimientoAlmacenConfirmacionMP->cantidad[$i]) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($movimientoAlmacenConfirmacionMP->usuario->id ? $movimientoAlmacenConfirmacionMP->usuario->id : Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE movimientos_almacen_confirmacion_mp SET ';
				$sql .= 'confirmado = ' . Datos::objectToDB($movimientoAlmacenConfirmacionMP->confirmado) . ', ';
				$sql .= 'cod_usuario_confirmacion = ' . Datos::objectToDB($movimientoAlmacenConfirmacionMP->usuarioConfirmacion->id) . ', ';
				$sql .= 'fecha_confirmacion = dbo.toDate(' . Datos::objectToDB($movimientoAlmacenConfirmacionMP->fechaConfirmacion) . ') ';
				$sql .= 'WHERE cod_confirmacion = ' . Datos::objectToDB($movimientoAlmacenConfirmacionMP->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE movimientos_almacen_confirmacion_mp SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_confirmacion = ' . Datos::objectToDB($movimientoAlmacenConfirmacionMP->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_confirmacion), 0) + 1 FROM movimientos_almacen_confirmacion_mp; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryMovimientoStock(MovimientoStock $movimientoStock, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM movimientos_stock ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($movimientoStock->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO movimientos_stock (';
				$sql .= 'id, ';
				$sql .= 'tipo_movimiento, ';
				$sql .= 'tipo_operacion, ';
				$sql .= 'key_objeto, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_almacen, ';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				$sql .= 'cantidad, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($movimientoStock->id) . ', ';
				$sql .= Datos::objectToDB($movimientoStock->tipoMovimiento) . ', ';
				$sql .= Datos::objectToDB($movimientoStock->tipoOperacion) . ', ';
				$sql .= Datos::objectToDB($movimientoStock->keyObjeto) . ', ';
				$sql .= Datos::objectToDB($movimientoStock->observaciones) . ', ';
				$sql .= Datos::objectToDB($movimientoStock->almacen->id) . ', ';
				$sql .= Datos::objectToDB($movimientoStock->articulo->id) . ', ';
				$sql .= Datos::objectToDB($movimientoStock->colorPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($movimientoStock->cantidadTotal) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB(Funciones::toInt($movimientoStock->cantidad[$i])) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(id), 0) + 1 FROM movimientos_stock; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryMovimientoStockMP(MovimientoStockMP $movimientoStockMP, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM movimientos_stock_mp ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($movimientoStockMP->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO movimientos_stock_mp (';
				$sql .= 'id, ';
				$sql .= 'tipo_movimiento, ';
				$sql .= 'tipo_operacion, ';
				$sql .= 'key_objeto, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_almacen, ';
				$sql .= 'cod_material, ';
				$sql .= 'cod_color, ';
				$sql .= 'cantidad, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($movimientoStockMP->id) . ', ';
				$sql .= Datos::objectToDB($movimientoStockMP->tipoMovimiento) . ', ';
				$sql .= Datos::objectToDB($movimientoStockMP->tipoOperacion) . ', ';
				$sql .= Datos::objectToDB($movimientoStockMP->keyObjeto) . ', ';
				$sql .= Datos::objectToDB($movimientoStockMP->observaciones) . ', ';
				$sql .= Datos::objectToDB($movimientoStockMP->almacen->id) . ', ';
				$sql .= Datos::objectToDB($movimientoStockMP->material->id) . ', ';
				$sql .= Datos::objectToDB($movimientoStockMP->colorMateriaPrima->idColor) . ', ';
				$sql .= Datos::objectToDB($movimientoStockMP->cantidadTotal) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB(Funciones::toFloat($movimientoStockMP->cantidad[$i])) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(id), 0) + 1 FROM movimientos_stock_mp; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryNotaDeCredito(NotaDeCredito $notaDeCredito, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= $this->mapperQueryDocumentoHaber($notaDeCredito, $modo, false);
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO documentos_c (';
				$sql .= 'empresa, ';
				$sql .= 'punto_venta, ';
				$sql .= 'tipo_docum, ';
				$sql .= 'tipo_docum_2, ';
				$sql .= 'nro_documento, ';
				$sql .= 'letra, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_cliente, ';
				$sql .= 'cod_sucursal, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'tiene_detalle, ';
				$sql .= 'iva_porc_1, ';
				$sql .= 'iva_importe_1, ';
				$sql .= 'iva_porc_2, ';
				$sql .= 'iva_importe_2, ';
				$sql .= 'iva_porc_3, ';
				$sql .= 'iva_importe_3, ';
				$sql .= 'importe_neto, ';
				$sql .= 'importe_no_gravado, ';
				$sql .= 'importe_total, ';
				$sql .= 'importe_pendiente, ';
				$sql .= 'descuento_comercial_importe, ';
				$sql .= 'descuento_comercial_porc, ';
				$sql .= 'descuento_despacho_importe, ';
				$sql .= 'cod_forma_pago, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'cancel_nro_documento, ';
				$sql .= 'causa, ';
				$sql .= 'observaciones, ';
				$sql .= 'fecha_documento, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($notaDeCredito->empresa) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->puntoDeVenta) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->tipoDocumento) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->tipoDocumento2) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->numero) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->letra) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->cliente->id) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->sucursal->id) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->tieneDetalle) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->ivaPorcentaje1) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->ivaImporte1) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->ivaPorcentaje2) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->ivaImporte2) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->ivaPorcentaje3) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->ivaImporte3) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->importeNeto) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->importeNoGravado) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->importeTotal) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->importePendiente) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->descuentoComercialImporte) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->descuentoComercialPorc) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->descuentoDespachoImporte) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->formaDePago->id) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->documentoCancelatorio->numero) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->causa->id) . ', ';
				$sql .= Datos::objectToDB($notaDeCredito->observaciones) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
				if (isset($notaDeCredito->documentoCancelatorio->numero)) { //Si es una NCR de una factura, actualizo la factura
					$sql .= 'UPDATE documentos_c SET ';
					$sql .= 'cancel_nro_documento = ' . Datos::objectToDB($notaDeCredito->numero) . ' ';
					$sql .= 'WHERE empresa = ' . Datos::objectToDB($notaDeCredito->empresa) . ' ';
					$sql .= 'AND punto_venta = ' . Datos::objectToDB($notaDeCredito->puntoDeVenta) . ' ';
					$sql .= 'AND tipo_docum = \'FAC\' ';
					$sql .= 'AND nro_documento = ' . Datos::objectToDB($notaDeCredito->documentoCancelatorio->numero) . ' ';
					$sql .= 'AND letra = ' . Datos::objectToDB($notaDeCredito->letra) . '; ';
				}
				//Si tieneDetalle == 'S' o == 'N' da igual, siempre se maneja desde el Factory mandando a guardar cada DocumentoItem
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE documentos_c SET ';
				$sql .= 'nro_comprobante = ' . Datos::objectToDB($notaDeCredito->numeroComprobante) . ', ';
				$sql .= 'cae = ' . Datos::objectToDB($notaDeCredito->cae) . ', ';
				$sql .= 'cae_vencimiento = dbo.toDate(' . Datos::objectToDB($notaDeCredito->caeFechaVencimiento) . '), ';
				$sql .= 'cae_obtencion_fecha = dbo.toDate(' . Datos::objectToDB($notaDeCredito->caeObtencionFecha) . '), ';
				$sql .= 'cae_obtencion_observaciones = ' . Datos::objectToDB($notaDeCredito->caeObtencionObservaciones) . ', ';
				$sql .= 'cae_obtencion_usuario = ' . Datos::objectToDB($notaDeCredito->caeObtencionUsuario->id) . ', ';
				$sql .= 'mail_enviado = ' . Datos::objectToDB($notaDeCredito->mailEnviado) . ', ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($notaDeCredito->asientoContable->id) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($notaDeCredito->empresa) . ' ';
				$sql .= 'AND punto_venta = ' . Datos::objectToDB($notaDeCredito->puntoDeVenta) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($notaDeCredito->tipoDocumento) . ' ';
				$sql .= 'AND nro_documento = ' . Datos::objectToDB($notaDeCredito->numero) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($notaDeCredito->letra) . '; ';
			} elseif ($modo == Modos::delete) {
				//Si no tiene CAE, puedo volver to_do atrï¿½s! Si tiene CAE, no
				if (isset($notaDeCredito->cae))
					throw new FactoryException('No se puede borrar la nota de crédito ya que tiene CAE. Debe hacer una nota de débito');
				$sql .= 'UPDATE documentos_c SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($notaDeCredito->empresa) . ' ';
				$sql .= 'AND punto_venta = ' . Datos::objectToDB($notaDeCredito->puntoDeVenta) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($notaDeCredito->tipoDocumento) . ' ';
				$sql .= 'AND nro_documento = ' . Datos::objectToDB($notaDeCredito->numero) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($notaDeCredito->letra) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_documento), 0) + 1 FROM documentos_c ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($notaDeCredito->empresa) . ' ';
				$sql .= 'AND punto_venta = ' . Datos::objectToDB($notaDeCredito->puntoDeVenta) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($notaDeCredito->tipoDocumento) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($notaDeCredito->letra) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryNotaDeCreditoProveedor(NotaDeCreditoProveedor $notaDeCreditoProveedor, $modo){
		return $this->mapperQueryDocumentoProveedor($notaDeCreditoProveedor, $modo);
	}
	private function mapperQueryNotaDeDebito(NotaDeDebito $notaDeDebito, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= $this->mapperQueryDocumentoDebe($notaDeDebito, $modo, false);
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO documentos_c (';
				$sql .= 'empresa, ';
				$sql .= 'punto_venta, ';
				$sql .= 'tipo_docum, ';
				$sql .= 'tipo_docum_2, ';
				$sql .= 'nro_documento, ';
				$sql .= 'letra, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_cliente, ';
				$sql .= 'cod_sucursal, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'tiene_detalle, ';
				$sql .= 'iva_porc_1, ';
				$sql .= 'iva_importe_1, ';
				$sql .= 'iva_porc_2, ';
				$sql .= 'iva_importe_2, ';
				$sql .= 'iva_porc_3, ';
				$sql .= 'iva_importe_3, ';
				$sql .= 'importe_neto, ';
				$sql .= 'importe_no_gravado, ';
				$sql .= 'importe_total, ';
				$sql .= 'importe_pendiente, ';
				$sql .= 'descuento_comercial_importe, ';
				$sql .= 'descuento_comercial_porc, ';
				$sql .= 'descuento_despacho_importe, ';
				$sql .= 'cod_forma_pago, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'observaciones, ';
				$sql .= 'fecha_documento, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($notaDeDebito->empresa) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->puntoDeVenta) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->tipoDocumento) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->tipoDocumento2) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->numero) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->letra) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->cliente->id) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->sucursal->id) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->tieneDetalle) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->ivaPorcentaje1) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->ivaImporte1) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->ivaPorcentaje2) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->ivaImporte2) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->ivaPorcentaje3) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->ivaImporte3) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->importeNeto) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->importeNoGravado) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->importeTotal) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->importePendiente) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->descuentoComercialImporte) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->descuentoComercialPorc) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->descuentoDespachoImporte) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->formaDePago->id) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB($notaDeDebito->observaciones) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE documentos_c SET ';
				$sql .= 'nro_comprobante = ' . Datos::objectToDB($notaDeDebito->numeroComprobante) . ', ';
				$sql .= 'cae = ' . Datos::objectToDB($notaDeDebito->cae) . ', ';
				$sql .= 'cae_vencimiento = dbo.toDate(' . Datos::objectToDB($notaDeDebito->caeFechaVencimiento) . '), ';
				$sql .= 'cae_obtencion_fecha = dbo.toDate(' . Datos::objectToDB($notaDeDebito->caeObtencionFecha) . '), ';
				$sql .= 'cae_obtencion_observaciones = ' . Datos::objectToDB($notaDeDebito->caeObtencionObservaciones) . ', ';
				$sql .= 'cae_obtencion_usuario = ' . Datos::objectToDB($notaDeDebito->caeObtencionUsuario->id) . ', ';
				$sql .= 'mail_enviado = ' . Datos::objectToDB($notaDeDebito->mailEnviado) . ', ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($notaDeDebito->asientoContable->id) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($notaDeDebito->empresa) . ' ';
				$sql .= 'AND punto_venta = ' . Datos::objectToDB($notaDeDebito->puntoDeVenta) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($notaDeDebito->tipoDocumento) . ' ';
				$sql .= 'AND nro_documento = ' . Datos::objectToDB($notaDeDebito->numero) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($notaDeDebito->letra) . '; ';
			} elseif ($modo == Modos::delete) {
				//Si no tiene CAE, puedo volver to_do atrï¿½s! Si tiene CAE, no
				if (isset($notaDeDebito->cae))
					throw new FactoryException('No se puede borrar la nota de débito ya que tiene CAE. Debe hacer una nota de crédito');
				$sql .= 'UPDATE documentos_c SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($notaDeDebito->empresa) . ' ';
				$sql .= 'AND punto_venta = ' . Datos::objectToDB($notaDeDebito->puntoDeVenta) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($notaDeDebito->tipoDocumento) . ' ';
				$sql .= 'AND nro_documento = ' . Datos::objectToDB($notaDeDebito->numero) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($notaDeDebito->letra) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_documento), 0) + 1 FROM documentos_c ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($notaDeDebito->empresa) . ' ';
				$sql .= 'AND punto_venta = ' . Datos::objectToDB($notaDeDebito->puntoDeVenta) . ' ';
				$sql .= 'AND tipo_docum = ' . Datos::objectToDB($notaDeDebito->tipoDocumento) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($notaDeDebito->letra) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryNotaDeDebitoProveedor(NotaDeDebitoProveedor $notaDeDebitoProveedor, $modo){
		return $this->mapperQueryDocumentoProveedor($notaDeDebitoProveedor, $modo);
	}
	private function mapperQueryNotificacion(Notificacion $notificacion, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM notificaciones ';
				$sql .= 'WHERE cod_notificacion = ' . Datos::objectToDB($notificacion->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO notificaciones (';
				$sql .= 'cod_notificacion, ';
				$sql .= 'tipo_notificacion, ';
				$sql .= 'key_objeto, ';
				$sql .= 'link, ';
				$sql .= 'detalle, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($notificacion->id) . ', ';
				$sql .= Datos::objectToDB($notificacion->tipoNotificacion->id) . ', ';
				$sql .= Datos::objectToDB($notificacion->keyObjeto) . ', ';
				$sql .= Datos::objectToDB($notificacion->link) . ', ';
				$sql .= Datos::objectToDB($notificacion->detalle) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE notificaciones SET ';
				$sql .= 'detalle = ' . Datos::objectToDB($notificacion->detalle) . ', ';
				$sql .= 'link = ' . Datos::objectToDB($notificacion->link) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_notificacion = ' . Datos::objectToDB($notificacion->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE notificaciones SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_notificacion = ' . Datos::objectToDB($notificacion->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_notificacion), 0) + 1 FROM notificaciones;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryNotificacionPorUsuario(NotificacionPorUsuario $notificacionPorUsuario, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM notificaciones_por_usuario_v ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($notificacionPorUsuario->id) . ' ';
				$sql .= 'AND cod_notificacion = ' . Datos::objectToDB($notificacionPorUsuario->idNotificacion) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO notificaciones_por_usuario (';
				$sql .= 'cod_notificacion, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'vista, ';
				$sql .= 'anulado, ';
				$sql .= 'eliminable, ';
				$sql .= 'fecha_ultima_mod ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($notificacionPorUsuario->idNotificacion) . ', ';
				$sql .= Datos::objectToDB($notificacionPorUsuario->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($notificacionPorUsuario->eliminable) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				//Corresponde al UPDATE de NOTIFICACION, donde ya tengo el idNotificacion
				$sql .= 'UPDATE notificaciones_por_usuario SET ';
				$sql .= 'vista = ' . Datos::objectToDB($notificacionPorUsuario->vista) . ', ';
				$sql .= 'eliminable = ' . Datos::objectToDB($notificacionPorUsuario->eliminable) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($notificacionPorUsuario->id) . ' ';
				$sql .= 'AND cod_notificacion = ' . Datos::objectToDB($notificacionPorUsuario->idNotificacion) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE notificaciones_por_usuario SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($notificacionPorUsuario->id) . ' ';
				$sql .= 'AND cod_notificacion = ' . Datos::objectToDB($notificacionPorUsuario->idNotificacion) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryOperador(Operador $operador, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM operadores_v ';
				$sql .= 'WHERE cod_operador = ' . Datos::objectToDB($operador->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO personal (';
				$sql .= 'cod_personal, ';
				$sql .= 'anulado, ';
				$sql .= 'apellido, ';
				$sql .= 'tel_celular, ';
				$sql .= 'cuil, ';
				$sql .= 'calle, ';
				$sql .= 'cod_postal, ';
				$sql .= 'departamento, ';
				$sql .= 'cod_localidad, ';
				$sql .= 'cod_localidad_nro, ';
				$sql .= 'numero, ';
				$sql .= 'cod_pais, ';
				$sql .= 'partido_departamento, ';
				$sql .= 'piso, ';
				$sql .= 'provincia, ';
				$sql .= 'doc_identidad_nro, ';
				$sql .= 'e_mail, ';
				$sql .= 'cod_faja_horaria, ';
				$sql .= 'fecha_antiguedad_gremio, ';
				$sql .= 'fecha_egreso, ';
				$sql .= 'fecha_ingreso, ';
				$sql .= 'fecha_nacimiento, ';
				$sql .= 'fotografia, ';
				$sql .= 'legajo_nro, ';
				$sql .= 'retribucion_modalidad, ';
				$sql .= 'nombres, ';
				//$sql .= 'objetivo_1, ';
				//$sql .= 'objetivo_2, ';
				//$sql .= 'objetivo_3, ';
				//$sql .= 'obra_social, ';
				//$sql .= 'premio_1, ';
				//$sql .= 'premio_2, ';
				//$sql .= 'premio_3, ';
				//$sql .= 'seccion, ';
				//$sql .= 'situacion, ';
				$sql .= 'tel_domicilio, ';
				$sql .= 'valor_hora, ';
				//$sql .= 'valor_hora_1, ';
				//$sql .= 'valor_hora_merienda, ';
				//$sql .= 'valor_mes, ';
				//$sql .= 'valor_mes_1, ';
				//$sql .= 'valor_pares, ';
				$sql .= 'valor_quincena ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($operador->idPersonal) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($operador->apellido) . ', ';
				$sql .= Datos::objectToDB($operador->celular) . ', ';
				$sql .= Datos::objectToDB($operador->cuil) . ', ';
				$sql .= Datos::objectToDB($operador->direccionCalle) . ', ';
				$sql .= Datos::objectToDB($operador->direccionCodigoPostal) . ', ';
				$sql .= Datos::objectToDB($operador->direccionDepartamento) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($operador->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= Datos::objectToDB($operador->direccionLocalidad->id) . ', ';
				$sql .= Datos::objectToDB($operador->direccionNumero) . ', ';
				$sql .= Datos::objectToDB($operador->direccionPais->id) . ', ';
				$sql .= Datos::objectToDB($operador->direccionPartidoDepartamento) . ', ';
				$sql .= Datos::objectToDB($operador->direccionPiso) . ', ';
				$sql .= Datos::objectToDB($operador->direccionProvincia->id) . ', ';
				$sql .= Datos::objectToDB($operador->dni) . ', ';
				$sql .= Datos::objectToDB($operador->email) . ', ';
				$sql .= Datos::objectToDB($operador->fajaHoraria->id) . ', ';
				$sql .= Datos::objectToDB($operador->fechaAntiguedadGremio) . ', ';
				$sql .= Datos::objectToDB($operador->fechaEgreso) . ', ';
				$sql .= Datos::objectToDB($operador->fechaIngreso) . ', ';
				$sql .= Datos::objectToDB($operador->fechaNacimiento) . ', ';
				$sql .= Datos::objectToDB($operador->foto) . ', ';
				$sql .= Datos::objectToDB($operador->legajo) . ', ';
				$sql .= Datos::objectToDB($operador->modalidadRetribucion) . ', ';
				$sql .= Datos::objectToDB($operador->nombre) . ', ';
				//$sql .= Datos::objectToDB($operador->objetivo1) . ', ';
				//$sql .= Datos::objectToDB($operador->objetivo2) . ', ';
				//$sql .= Datos::objectToDB($operador->objetivo3) . ', ';
				//$sql .= Datos::objectToDB($operador->obraSocial) . ', ';
				//$sql .= Datos::objectToDB($operador->premio1) . ', ';
				//$sql .= Datos::objectToDB($operador->premio2) . ', ';
				//$sql .= Datos::objectToDB($operador->premio3) . ', ';
				//$sql .= Datos::objectToDB($operador->seccion) . ', ';
				//$sql .= Datos::objectToDB($operador->situacion) . ', ';
				$sql .= Datos::objectToDB($operador->telefono) . ', ';
				$sql .= Datos::objectToDB($operador->valorHora) . ', ';
				//$sql .= Datos::objectToDB($operador->valorHora1) . ', ';
				//$sql .= Datos::objectToDB($operador->valorHoraMerienda) . ', ';
				//$sql .= Datos::objectToDB($operador->valorMes) . ', ';
				//$sql .= Datos::objectToDB($operador->valorMes1) . ', ';
				//$sql .= Datos::objectToDB($operador->valorPares) . ', ';
				$sql .= Datos::objectToDB($operador->valorQuincena) . ' ';
				$sql .= '); ';
				$sql .= 'INSERT INTO Operadores (';
				$sql .= 'cod_operador, ';
				$sql .= 'cod_personal, ';
				$sql .= 'porc_comision_vtas, ';
				$sql .= 'anulado, ';
				$sql .= 'tipo_operador ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($operador->tipo . Funciones::padLeft($operador->idPersonal, 5, '0')) . ', ';
				$sql .= Datos::objectToDB($operador->idPersonal) . ', ';
				$sql .= Datos::objectToDB($operador->porcComisionVtas) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($operador->tipo) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE personal SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($operador->anulado) . ', ';
				$sql .= 'apellido = ' . Datos::objectToDB($operador->apellido) . ', ';
				$sql .= 'tel_celular = ' . Datos::objectToDB($operador->celular) . ', ';
				$sql .= 'cuil = ' . Datos::objectToDB($operador->cuil) . ', ';
				$sql .= 'calle = ' . Datos::objectToDB($operador->direccionCalle) . ', ';
				$sql .= 'cod_postal = ' . Datos::objectToDB($operador->direccionCodigoPostal) . ', ';
				$sql .= 'departamento = ' . Datos::objectToDB($operador->direccionDepartamento) . ', ';
				$sql .= 'cod_localidad = ' . Datos::objectToDB(Funciones::padLeft($operador->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= 'cod_localidad_nro = ' . Datos::objectToDB($operador->direccionLocalidad->id) . ', ';
				$sql .= 'numero = ' . Datos::objectToDB($operador->direccionNumero) . ', ';
				$sql .= 'cod_pais = ' . Datos::objectToDB($operador->direccionPais->id) . ', ';
				$sql .= 'partido_departamento = ' . Datos::objectToDB($operador->direccionPartidoDepartamento) . ', ';
				$sql .= 'piso = ' . Datos::objectToDB($operador->direccionPiso) . ', ';
				$sql .= 'provincia = ' . Datos::objectToDB($operador->direccionProvincia->id) . ', ';
				$sql .= 'doc_identidad_nro = ' . Datos::objectToDB($operador->dni) . ', ';
				$sql .= 'e_mail = ' . Datos::objectToDB($operador->email) . ', ';
				$sql .= 'cod_faja_horaria = ' . Datos::objectToDB($operador->fajaHoraria->id) . ', ';
				$sql .= 'fecha_antiguedad_gremio = ' . Datos::objectToDB($operador->fechaAntiguedadGremio) . ', ';
				$sql .= 'fecha_egreso = ' . Datos::objectToDB($operador->fechaEgreso) . ', ';
				$sql .= 'fecha_ingreso = ' . Datos::objectToDB($operador->fechaIngreso) . ', ';
				$sql .= 'fecha_nacimiento = ' . Datos::objectToDB($operador->fechaNacimiento) . ', ';
				$sql .= 'fotografia = ' . Datos::objectToDB($operador->foto) . ', ';
				$sql .= 'legajo_nro = ' . Datos::objectToDB($operador->legajo) . ', ';
				$sql .= 'retribucion_modalidad = ' . Datos::objectToDB($operador->modalidadRetribucion) . ', ';
				$sql .= 'nombres = ' . Datos::objectToDB($operador->nombre) . ', ';
				//$sql .= 'objetivo_1 = ' . Datos::objectToDB($operador->objetivo1) . ', ';
				//$sql .= 'objetivo_2 = ' . Datos::objectToDB($operador->objetivo2) . ', ';
				//$sql .= 'objetivo_3 = ' . Datos::objectToDB($operador->objetivo3) . ', ';
				//$sql .= 'obra_social = ' . Datos::objectToDB($operador->obraSocial) . ', ';
				//$sql .= 'premio_1 = ' . Datos::objectToDB($operador->premio1) . ', ';
				//$sql .= 'premio_2 = ' . Datos::objectToDB($operador->premio2) . ', ';
				//$sql .= 'premio_3 = ' . Datos::objectToDB($operador->premio3) . ', ';
				//$sql .= 'seccion = ' . Datos::objectToDB($operador->seccion) . ', ';
				//$sql .= 'situacion = ' . Datos::objectToDB($operador->situacion) . ', ';
				$sql .= 'tel_domicilio = ' . Datos::objectToDB($operador->telefono) . ', ';
				$sql .= 'valor_hora = ' . Datos::objectToDB($operador->valorHora) . ', ';
				//$sql .= 'valor_hora_1 = ' . Datos::objectToDB($operador->valorHora1) . ', ';
				//$sql .= 'valor_hora_merienda = ' . Datos::objectToDB($operador->valorHoraMerienda) . ', ';
				$sql .= 'valor_mes = ' . Datos::objectToDB($operador->valorMes) . ', ';
				//$sql .= 'valor_mes_1 = ' . Datos::objectToDB($operador->valorMes1) . ', ';
				//$sql .= 'valor_pares = ' . Datos::objectToDB($operador->valorPares) . ', ';
				$sql .= 'valor_quincena = ' . Datos::objectToDB($operador->valorQuincena) . ' ';
				$sql .= 'WHERE cod_personal = ' . Datos::objectToDB($operador->idPersonal) . '; ';
				$sql .= 'UPDATE Operadores SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($operador->anulado) . ', ';
				$sql .= 'porc_comision_vtas = ' . Datos::objectToDB($operador->porcComisionVtas) . ' ';
				$sql .= 'WHERE cod_operador = ' . Datos::objectToDB($operador->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE personal SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_personal = ' . Datos::objectToDB($operador->idPersonal) . '; ';
				$sql .= 'UPDATE Operadores SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_operador = ' . Datos::objectToDB($operador->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_personal), 0) + 1 FROM personal;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryOrdenDeCompra(OrdenDeCompra $ordenDeCompra, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM ordenes_compra_cabecera_v ';
				$sql .= 'WHERE cod_orden_de_compra = ' . Datos::objectToDB($ordenDeCompra->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO ordenes_compra_cabecera (';
				$sql .= 'cod_orden_de_compra, ';
				$sql .= 'cod_sucursal, ';
				$sql .= 'nro_orden_compra, ';
				$sql .= 'cod_proveedor, ';
				$sql .= 'nro_lote, ';
				$sql .= 'fecha_emision, ';
				$sql .= 'cod_almacen_entrega, ';
				$sql .= 'usa_rango, ';
				$sql .= 'observaciones, ';
				$sql .= 'es_hexagono, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ordenDeCompra->id) . ', ';
				$sql .= Datos::objectToDB('01') . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($ordenDeCompra->id, 6, 0)) . ', ';
				$sql .= Datos::objectToDB($ordenDeCompra->proveedor->id) . ', ';
				$sql .= Datos::objectToDB($ordenDeCompra->loteDeProduccion->id) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= Datos::objectToDB($ordenDeCompra->almacen->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($ordenDeCompra->observaciones) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE ordenes_compra_cabecera SET ';
				$sql .= 'nro_lote = ' . Datos::objectToDB($ordenDeCompra->loteDeProduccion->id) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($ordenDeCompra->observaciones) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_orden_de_compra = ' . Datos::objectToDB($ordenDePago->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE ordenes_compra_detalle SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_orden_de_compra = ' . Datos::objectToDB($ordenDeCompra->id) . '; ';
				$sql .= 'UPDATE ordenes_compra_cabecera SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_orden_de_compra = ' . Datos::objectToDB($ordenDeCompra->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_orden_de_compra), 0) + 1 FROM ordenes_compra_cabecera; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryOrdenDeCompraItem(OrdenDeCompraItem $ordenDeCompraItem, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Ordenes_compra_detalle_v ';
				$sql .= 'WHERE cod_orden_de_compra = ' . Datos::objectToDB($ordenDeCompraItem->ordenDeCompra->id) . ' AND ';
				$sql .= 'nro_item = ' . Datos::objectToDB($ordenDeCompraItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Ordenes_compra_detalle (';
				$sql .= 'cod_orden_de_compra, ';
				$sql .= 'nro_item, ';
				$sql .= 'cod_sucursal, ';
				$sql .= 'nro_orden_compra, ';
				$sql .= 'cod_material, ';
				$sql .= 'cod_color, ';
				$sql .= 'fecha_entrega, ';
				$sql .= 'precio_unitario, ';
				$sql .= 'cantidad, ';
				$sql .= 'cantidad_pendiente, ';
				$sql .= 'importe, ';
				$sql .= 'cod_impuesto, ';
				$sql .= 'importe_impuesto, ';

				for($i = 1; $i < 16; $i++){
					$sql .= 'cant_' . $i . ', ';
					$sql .= 'pr_' . $i . ', ';
					$sql .= 'cant_p_' . $i . ', ';
				}

				$sql .= 'nro_lote_compra, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ordenDeCompraItem->ordenDeCompra->id) . ', ';
				$sql .= Datos::objectToDB($ordenDeCompraItem->numeroDeItem) . ', ';
				$sql .= Datos::objectToDB('01') . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($ordenDeCompraItem->ordenDeCompra->id, 6, 0)) . ', ';
				$sql .= Datos::objectToDB($ordenDeCompraItem->colorMateriaPrima->idMaterial) . ', ';
				$sql .= Datos::objectToDB($ordenDeCompraItem->colorMateriaPrima->idColor) . ', ';
				$sql .= Datos::objectToDB($ordenDeCompraItem->fechaEntrega) . ', ';
				$sql .= Datos::objectToDB($ordenDeCompraItem->precioUnitario) . ', ';
				$sql .= Datos::objectToDB($ordenDeCompraItem->cantidad) . ', ';
				$sql .= Datos::objectToDB($ordenDeCompraItem->cantidad) . ', ';
				$sql .= Datos::objectToDB($ordenDeCompraItem->importe) . ', ';
				$sql .= Datos::objectToDB($ordenDeCompraItem->impuesto->id) . ', ';
				$sql .= Datos::objectToDB($ordenDeCompraItem->importeImpuesto) . ', ';
				for($i = 1; $i < 16; $i++){
					$sql .= Datos::objectToDB($ordenDeCompraItem->cantidades[$i]) . ', ';
					$sql .= Datos::objectToDB($ordenDeCompraItem->precios[$i]) . ', ';
					$sql .= Datos::objectToDB($ordenDeCompraItem->cantidades[$i]) . ', ';
				}
				$sql .= Datos::objectToDB($ordenDeCompraItem->loteDeCompra) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Ordenes_compra_detalle SET ';

				for($i = 1; $i < 16; $i++)
					$sql .= 'cant_p_' . $i . ' = ' . Datos::objectToDB($ordenDeCompraItem->cantidadesPendientes[$i]) . ', ';

				$sql .= 'cantidad_pendiente = ' . Datos::objectToDB($ordenDeCompraItem->cantidadPendiente) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_entrega = ' . Datos::objectToDB($ordenDeCompraItem->fechaEntrega) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_orden_de_compra = ' . Datos::objectToDB($ordenDeCompraItem->ordenDeCompra->id) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($ordenDeCompraItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Ordenes_compra_detalle SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_orden_de_compra = ' . Datos::objectToDB($ordenDeCompraItem->ordenDeCompra->id) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($ordenDeCompraItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_item), 0) + 1 FROM Ordenes_compra_detalle ';
				$sql .= 'WHERE cod_orden_de_compra = ' . Datos::objectToDB($ordenDeCompraItem->ordenDeCompra->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryOrdenDeFabricacion(OrdenDeFabricacion $ordenDeFabricacion, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Orden_fabricacion ';
				$sql .= 'WHERE nro_orden_fabricacion = ' . Datos::objectToDB($ordenDeFabricacion->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Orden_fabricacion (';
				$sql .= 'nro_orden_fabricacion, ';
				$sql .= 'nro_plan, ';
                $sql .= 'tipo_orden, ';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				$sql .= 'version, ';
				$sql .= 'Confirmada, ';
				$sql .= 'cod_cliente, ';
				$sql .= 'Terminada, ';
				$sql .= 'dividir_en_tareas, ';
				$sql .= 'manual, ';
				$sql .= 'impresa, ';
				$sql .= 'cod_modulo, ';
                $sql .= 'cantidad_optima_produccion, ';
				$sql .= 'cantidad, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'co_' . $i . ', ';
                $sql .= 'anulado, ';
				$sql .= 'fecha_inicio, ';
				$sql .= 'fecha_fin_programada ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ordenDeFabricacion->id) . ', ';
				$sql .= Datos::objectToDB($ordenDeFabricacion->loteDeProduccion->id) . ', ';
                $sql .= Datos::objectToDB($ordenDeFabricacion->tipoOrden) . ', ';
				$sql .= Datos::objectToDB($ordenDeFabricacion->articulo->id) . ', ';
				$sql .= Datos::objectToDB($ordenDeFabricacion->colorPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($ordenDeFabricacion->patron->version) . ', ';
				$sql .= Datos::objectToDB($ordenDeFabricacion->confirmada) . ', ';
				$sql .= Datos::objectToDB($ordenDeFabricacion->loteDeProduccion->id ? '000001' : null) . ', ';
                $sql .= Datos::objectToDB('N') . ', ';
                $sql .= Datos::objectToDB('S') . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
                $sql .= Datos::objectToDB($ordenDeFabricacion->curvaDeProduccion->id) . ', ';
                $sql .= Datos::objectToDB($ordenDeFabricacion->cantidadOptimaProduccion) . ', ';
				$sql .= Datos::objectToDB($ordenDeFabricacion->cantidadTotal) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($ordenDeFabricacion->cantidad[$i]) . ', ';
                $sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'dbo.toDate(' . Datos::objectToDB($ordenDeFabricacion->fechaInicio) . '), ';
				$sql .= 'dbo.toDate(' . Datos::objectToDB($ordenDeFabricacion->fechaFin) . ') ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Orden_fabricacion SET ';
				$sql .= 'Confirmada = ' . Datos::objectToDB($ordenDeFabricacion->confirmada) . ', ';
                $sql .= 'cod_modulo = ' . Datos::objectToDB($ordenDeFabricacion->curvaDeProduccion->id) . ', ';
                $sql .= 'cantidad_optima_produccion = ' . Datos::objectToDB($ordenDeFabricacion->cantidadOptimaProduccion) . ', ';
				$sql .= 'cantidad = ' . Datos::objectToDB($ordenDeFabricacion->cantidadTotal) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'co_' . $i . ' = ' . Datos::objectToDB($ordenDeFabricacion->cantidad[$i]) . ', ';
				$sql .= 'fecha_inicio = dbo.toDate(' . Datos::objectToDB($ordenDeFabricacion->fechaInicio) . '), ';
				$sql .= 'fecha_fin_programada = dbo.toDate(' . Datos::objectToDB($ordenDeFabricacion->fechaFin) . ') ';
				$sql .= 'WHERE nro_orden_fabricacion = ' . Datos::objectToDB($ordenDeFabricacion->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Orden_fabricacion SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE nro_orden_fabricacion = ' . Datos::objectToDB($ordenDeFabricacion->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_orden_fabricacion), 0) + 1 FROM Orden_fabricacion;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryOrdenDePago(OrdenDePago $ordenDePago, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM orden_de_pago ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($ordenDePago->empresa) . ' ';
				$sql .= 'AND nro_orden_de_pago = ' . Datos::objectToDB($ordenDePago->numero) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO orden_de_pago (';
				$sql .= 'nro_orden_de_pago, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'cod_proveedor, ';
				$sql .= 'operacion_tipo, ';
				$sql .= 'imputacion, ';
				$sql .= 'importe_total, ';
				$sql .= 'importe_sujeto_ret, ';
				$sql .= 'importe_pendiente, ';
				$sql .= 'beneficiario, ';
				$sql .= 'mail_enviado, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'retiene_ganancias, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_documento, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ordenDePago->numero) . ', ';
				$sql .= Datos::objectToDB($ordenDePago->empresa) . ', ';
				$sql .= Datos::objectToDB($ordenDePago->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($ordenDePago->proveedor->id) . ', ';
				$sql .= Datos::objectToDB($ordenDePago->tipoOperacion) . ', ';
				$sql .= Datos::objectToDB($ordenDePago->imputacion->id) . ', ';
				$sql .= Datos::objectToDB($ordenDePago->importeTotal) . ', ';
				$sql .= Datos::objectToDB($ordenDePago->importeSujetoRetencion) . ', ';
				$sql .= Datos::objectToDB($ordenDePago->importePendiente) . ', ';
				$sql .= Datos::objectToDB($ordenDePago->beneficiario) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($ordenDePago->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB($ordenDePago->retieneGanancias) . ', ';
				$sql .= Datos::objectToDB($ordenDePago->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($ordenDePago->fecha) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE orden_de_pago SET ';
				$sql .= 'imputacion = ' . Datos::objectToDB($ordenDePago->imputacion->id) . ', ';
				$sql .= 'beneficiario = ' . Datos::objectToDB($ordenDePago->beneficiario) . ', ';
				$sql .= 'mail_enviado = ' . Datos::objectToDB($ordenDePago->mailEnviado) . ', ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($ordenDePago->asientoContable->id) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($ordenDePago->observaciones) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE nro_orden_de_pago = ' . Datos::objectToDB($ordenDePago->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($ordenDePago->empresa) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE orden_de_pago SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE nro_orden_de_pago = ' . Datos::objectToDB($ordenDePago->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($ordenDePago->empresa) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_orden_de_pago), 0) + 1 FROM orden_de_pago ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($ordenDePago->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPais(Pais $pais, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Paises ';
				$sql .= 'WHERE cod_pais = ' . Datos::objectToDB($pais->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Paises (';
				$sql .= 'cod_pais, ';
				$sql .= 'denom_pais, ';
				$sql .= 'anulado ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($pais->id) . ', ';
				$sql .= Datos::objectToDB($pais->nombre) . ', ';
				$sql .= Datos::objectToDB('N') . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Paises SET ';
				$sql .= 'cod_pais = ' . Datos::objectToDB($pais->id) . ', ';
				$sql .= 'denom_pais = ' . Datos::objectToDB($pais->nombre) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB($pais->anulado) . ' ';
				$sql .= 'WHERE cod_pais = ' . Datos::objectToDB($pais->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Paises SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_pais = ' . Datos::objectToDB($pais->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryParametro(Parametro $parametro, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM parametro ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($parametro->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO parametro (';
				$sql .= 'id, ';
				$sql .= 'valor, ';
				$sql .= 'descripcion ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($parametro->id) . ', ';
				$sql .= Datos::objectToDB($parametro->valor) . ', ';
				$sql .= Datos::objectToDB($parametro->descripcion) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE parametro SET ';
				$sql .= 'id = ' . Datos::objectToDB($parametro->id) . ', ';
				$sql .= 'valor = ' . Datos::objectToDB($parametro->valor) . ', ';
				$sql .= 'descripcion = ' . Datos::objectToDB($parametro->descripcion) . ' ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($parametro->id) . '; ';
			} elseif ($modo == Modos::delete) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryParametroContabilidad(ParametroContabilidad $parametroContabilidad, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM parametros_contabilidad ';
				$sql .= 'WHERE cod_parametro = ' . Datos::objectToDB($parametroContabilidad->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO parametros_contabilidad (';
				$sql .= 'cod_parametro, ';
				$sql .= 'cod_imputacion, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($parametroContabilidad->id) . ', ';
				$sql .= Datos::objectToDB($parametroContabilidad->imputacion->id) . ', ';
				$sql .= Datos::objectToDB($parametroContabilidad->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE parametros_contabilidad SET ';
				$sql .= 'cod_imputacion = ' . Datos::objectToDB($parametroContabilidad->imputacion->id) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($parametroContabilidad->observaciones) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_parametro = ' . Datos::objectToDB($parametroContabilidad->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE parametros_contabilidad SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_parametro = ' . Datos::objectToDB($parametroContabilidad->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPatron(Patron $patron, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Patrones_mp_cabecera ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB($patron->idArticulo) . ' ';
				$sql .= 'AND cod_color_articulo = ' . Datos::objectToDB($patron->idColorPorArticulo) . ' ';
				$sql .= 'AND version = ' . Datos::objectToDB($patron->version) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO patrones_mp_cabecera (';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				$sql .= 'version, ';
				$sql .= 'tipo_patron, ';
				$sql .= 'fecha, ';
				$sql .= 'confirmado, ';
				$sql .= 'version_actual, ';
				$sql .= 'borrador, ';
				$sql .= 'cod_horma, ';
				$sql .= 'diseño, ';
				$sql .= 'borrador_viejo ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($patron->articulo->id) . ', ';
				$sql .= Datos::objectToDB($patron->colorPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($patron->version) . ', ';
				$sql .= Datos::objectToDB($patron->tipoPatron) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= Datos::objectToDB($patron->confirmado) . ', ';
				$sql .= Datos::objectToDB($patron->versionActual) . ', ';
				$sql .= Datos::objectToDB($patron->borrador) . ', ';
				$sql .= Datos::objectToDB($patron->horma->id) . ', ';
				$sql .= Datos::objectToDB($patron->disenio) . ', ';
				$sql .= Datos::objectToDB($patron->borradorViejo) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'DELETE FROM Patrones_mp_detalle ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB($patron->idArticulo) . ' ';
				$sql .= 'AND cod_color_articulo = ' . Datos::objectToDB($patron->idColorPorArticulo) . ' ';
				$sql .= 'AND version = ' . Datos::objectToDB($patron->version) . '; ';

				$sql .= 'UPDATE patrones_mp_cabecera SET ';
				$sql .= 'tipo_patron = ' . Datos::objectToDB($patron->tipoPatron) . ', ';
				$sql .= 'fecha = GETDATE() , ';
				$sql .= 'confirmado = ' . Datos::objectToDB($patron->confirmado) . ', ';
				$sql .= 'version_actual = ' . Datos::objectToDB($patron->versionActual) . ', ';
				$sql .= 'borrador = ' . Datos::objectToDB($patron->borrador) . ', ';
				$sql .= 'cod_horma = ' . Datos::objectToDB($patron->horma->id) . ', ';
				$sql .= 'diseño = ' . Datos::objectToDB($patron->disenio) . ', ';
				$sql .= 'borrador_viejo = ' . Datos::objectToDB($patron->borradorViejo) . ' ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB($patron->idArticulo) . ' ';
				$sql .= 'AND cod_color_articulo = ' . Datos::objectToDB($patron->idColorPorArticulo) . ' ';
				$sql .= 'AND version = ' . Datos::objectToDB($patron->version) . '; ';
			//} elseif ($modo == Modos::delete) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPatronItem(PatronItem $patronItem, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Patrones_mp_detalle ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB($patronItem->idArticulo) . ' ';
				$sql .= 'AND cod_color_articulo = ' . Datos::objectToDB($patronItem->idColorPorArticulo) . ' ';
				$sql .= 'AND version = ' . Datos::objectToDB($patronItem->version) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($patronItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO patrones_mp_detalle (';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				$sql .= 'version, ';
				$sql .= 'nro_item, ';
				$sql .= 'cod_material, ';
				$sql .= 'cod_color_material, ';
				$sql .= 'cod_seccion, ';
				$sql .= 'fecha_alta, ';
				$sql .= 'item_nuevo, ';
				$sql .= 'consumo_par, ';
				$sql .= 'consumo_batch, ';
				$sql .= 'conjunto, ';
				$sql .= 'varia, ';
				$sql .= 'escalado, ';
				$sql .= 'escala_desplazada, ';
				$sql .= 'tipo_patron, ';
				$sql .= 'trazable, ';
				$sql .= 'asignado_lote, ';
				$sql .= 'cant_entregada, ';
				$sql .= 'entregado ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($patronItem->patron->articulo->id) . ', ';
				$sql .= Datos::objectToDB($patronItem->patron->colorPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($patronItem->patron->version) . ', ';
				$sql .= Datos::objectToDB($patronItem->numeroDeItem) . ', ';
				$sql .= Datos::objectToDB($patronItem->material->id) . ', ';
				$sql .= Datos::objectToDB($patronItem->colorMateriaPrima->idColor) . ', ';
				$sql .= Datos::objectToDB($patronItem->seccion->id) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= Datos::objectToDB($patronItem->itemNuevo) . ', ';
				$sql .= Datos::objectToDB($patronItem->consumoPar) . ', ';
				$sql .= Datos::objectToDB($patronItem->consumoBatch) . ', ';
				$sql .= Datos::objectToDB($patronItem->conjunto->id) . ', ';
				$sql .= Datos::objectToDB($patronItem->varia) . ', ';
				$sql .= Datos::objectToDB($patronItem->escalado) . ', ';
				$sql .= Datos::objectToDB($patronItem->escalaDesplazada) . ', ';
				$sql .= Datos::objectToDB($patronItem->tipoPatron) . ', ';
				$sql .= Datos::objectToDB($patronItem->trazable) . ', ';
				$sql .= Datos::objectToDB($patronItem->asignadoLote) . ', ';
				$sql .= Datos::objectToDB($patronItem->cantEntregada) . ', ';
				$sql .= Datos::objectToDB($patronItem->entregado) . ' ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_item), 0) + 1 FROM Patrones_mp_detalle ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB($patronItem->patron->articulo->id) . ' ';
				$sql .= 'AND cod_color_articulo = ' . Datos::objectToDB($patronItem->patron->colorPorArticulo->id) . ' ';
				$sql .= 'AND version = ' . Datos::objectToDB($patronItem->patron->version) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPedido(Pedido $pedido, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM pedidos_c ';
				$sql .= 'WHERE nro_pedido = ' . Datos::objectToDB($pedido->numero) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO pedidos_c (';
				$sql .= 'empresa, ';
				$sql .= 'nro_pedido, ';
				$sql .= 'anulado, ';
				$sql .= 'aprobado, ';
				$sql .= 'cod_cliente, ';
				$sql .= 'cod_sucursal, ';
				$sql .= 'cod_vendedor, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'cod_almacen, ';
				$sql .= 'precio_al_facturar, ';
				$sql .= 'descuento_pedido, ';
				$sql .= 'recargo_pedido, ';
				$sql .= 'importe_total, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_forma_pago, ';
				$sql .= 'cod_temporada, ';
				$sql .= 'cod_ecommerce_order, ';
				$sql .= 'fecha_alta, ';
				$sql .= 'fecha_ultima_mod ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($pedido->empresa) . ', ';
				$sql .= Datos::objectToDB($pedido->numero) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($pedido->aprobado) . ', ';
				$sql .= Datos::objectToDB($pedido->cliente->id) . ', ';
				$sql .= Datos::objectToDB($pedido->sucursal->id) . ', ';
				$sql .= Datos::objectToDB($pedido->vendedor->id) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB($pedido->almacen->id) . ', ';
				$sql .= Datos::objectToDB($pedido->precioAlFacturar) . ', ';
				$sql .= Datos::objectToDB($pedido->descuento) . ', ';
				$sql .= Datos::objectToDB($pedido->recargo) . ', ';
				$sql .= Datos::objectToDB($pedido->importeTotal) . ', ';
				$sql .= Datos::objectToDB($pedido->observaciones) . ', ';
				$sql .= Datos::objectToDB($pedido->formaDePago->id) . ', ';
				$sql .= Datos::objectToDB($pedido->temporada->id) . ', ';
				$sql .= Datos::objectToDB($pedido->ecommerceOrder->id) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'DELETE FROM pedidos_d ';
				$sql .= 'WHERE nro_pedido = ' . Datos::objectToDB($pedido->numero) . '; ';
				$sql .= 'UPDATE pedidos_c SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($pedido->anulado) . ', ';
				$sql .= 'aprobado = ' . Datos::objectToDB($pedido->aprobado) . ', ';
				$sql .= 'cod_vendedor = ' . Datos::objectToDB($pedido->vendedor->id) . ', ';
				$sql .= 'precio_al_facturar = ' . Datos::objectToDB($pedido->precioAlFacturar) . ', ';
				$sql .= 'descuento_pedido = ' . Datos::objectToDB($pedido->descuento) . ', ';
				$sql .= 'recargo_pedido = ' . Datos::objectToDB($pedido->recargo) . ', ';
				$sql .= 'importe_total = ' . Datos::objectToDB($pedido->importeTotal) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($pedido->observaciones) . ', ';
				$sql .= 'cod_forma_pago = ' . Datos::objectToDB($pedido->formaDePago->id) . ', ';
				$sql .= 'cod_temporada = ' . Datos::objectToDB($pedido->temporada->id) . ', ';
				$sql .= 'cod_ecommerce_order = ' . Datos::objectToDB($pedido->ecommerceOrder->id) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE nro_pedido = ' . Datos::objectToDB($pedido->numero) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE pedidos_c SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE nro_pedido = ' . Datos::objectToDB($pedido->numero) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_pedido), 0) + 1 FROM pedidos_c;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPedidoItem(PedidoItem $pedidoItem, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM pedidos_d_v ';
				$sql .= 'WHERE nro_pedido = ' . Datos::objectToDB($pedidoItem->numero) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($pedidoItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO pedidos_d (';
				$sql .= 'empresa, ';
				$sql .= 'nro_pedido, ';
				$sql .= 'nro_item, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_almacen, ';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				$sql .= 'precio_unitario, ';
				$sql .= 'cantidad, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'pendiente, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'pend_' . $i . ', ';
				$sql .= 'fecha_alta, ';
				$sql .= 'fecha_ultima_mod ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($pedidoItem->empresa) . ', ';
				$sql .= Datos::objectToDB($pedidoItem->numero) . ', ';
				$sql .= Datos::objectToDB($pedidoItem->numeroDeItem) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($pedidoItem->almacen->id) . ', ';
				$sql .= Datos::objectToDB($pedidoItem->articulo->id) . ', ';
				$sql .= Datos::objectToDB($pedidoItem->colorPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($pedidoItem->precioUnitario) . ', ';
				$sql .= Datos::objectToDB(Funciones::sumaArray($pedidoItem->cantidad)) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($pedidoItem->cantidad[$i]) . ', ';
				$sql .= Datos::objectToDB(Funciones::sumaArray($pedidoItem->cantidad)) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($pedidoItem->cantidad[$i]) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE pedidos_d SET ';
				$sql .= 'pendiente = ' . Datos::objectToDB($pedidoItem->getTotalPendiente()) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'pend_' . $i . ' = ' . Datos::objectToDB($pedidoItem->pendiente[$i]) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE nro_pedido = ' . Datos::objectToDB($pedidoItem->numero) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($pedidoItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE pedidos_d SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE nro_pedido = ' . Datos::objectToDB($pedidoItem->numero) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($pedidoItem->numeroDeItem) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPermisoPorCaja(PermisoPorCaja $permisoPorCaja, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM permisos_por_caja ';
				$sql .= 'WHERE cod_caja = ' . Datos::objectToDB($permisoPorCaja->idCaja) . ' AND ';
				$sql .= 'cod_permiso = ' . Datos::objectToDB($permisoPorCaja->idPermiso) . '; ';
			} elseif (($modo == Modos::insert) || ($modo == Modos::update)) {
				$sql .= 'INSERT INTO permisos_por_caja (';
				$sql .= 'cod_caja, ';
				$sql .= 'cod_permiso ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($permisoPorCaja->idCaja) . ', ';
				$sql .= Datos::objectToDB($permisoPorCaja->idPermiso) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM permisos_por_caja ';
				$sql .= 'WHERE cod_caja = ' . Datos::objectToDB($permisoPorCaja->caja->id);
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPermisoPorUsuarioPorCaja(PermisoPorUsuarioPorCaja $permisoPorUsuarioPorCaja, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM permisos_por_usuarios_por_caja_v ';
				$sql .= 'WHERE cod_caja = ' . Datos::objectToDB($permisoPorUsuarioPorCaja->idCaja) . ' AND ';
				$sql .= 'cod_usuario = ' . Datos::objectToDB($permisoPorUsuarioPorCaja->idUsuario) . ' AND ';
				$sql .= 'cod_permiso = ' . Datos::objectToDB($permisoPorUsuarioPorCaja->idPermiso) . '; ';
			} elseif (($modo == Modos::insert) || ($modo == Modos::update)) {
				$sql .= 'INSERT INTO permisos_por_usuarios_por_caja (';
				$sql .= 'cod_caja, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'cod_permiso ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($permisoPorUsuarioPorCaja->idCaja) . ', ';
				$sql .= Datos::objectToDB($permisoPorUsuarioPorCaja->usuario->id) . ', ';
				$sql .= Datos::objectToDB($permisoPorUsuarioPorCaja->idPermiso) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::delete) {
				// Se borran todos los permisos de una caja al mismo tiempo, y se vuelven a insertar todos on transaction (desde el abm de cajas)
				$sql .= 'DELETE FROM permisos_por_usuarios_por_caja ';
				$sql .= 'WHERE cod_caja = ' . Datos::objectToDB($permisoPorUsuarioPorCaja->idCaja) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPersonaGasto(PersonaGasto $personaGasto, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM persona_gasto ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($personaGasto->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO persona_gasto (';
				$sql .= 'id, ';
				$sql .= 'nombre, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($personaGasto->id) . ', ';
				$sql .= Datos::objectToDB($personaGasto->nombre) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE persona_gasto SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($personaGasto->nombre) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($personaGasto->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE persona_gasto SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE id = ' . Datos::objectToDB($personaGasto->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(id), 0) + 1 FROM persona_gasto;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPersonal(Personal $personal, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM personal ';
				$sql .= 'WHERE cod_personal = ' . Datos::objectToDB($personal->idPersonal) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO personal (';
				$sql .= 'cod_personal, ';
				$sql .= 'anulado, ';
				$sql .= 'apellido, ';
				$sql .= 'cuil, ';
				$sql .= 'calle, ';
				$sql .= 'cod_postal, ';
				$sql .= 'departamento, ';
				//$sql .= 'cod_localidad, ';
				$sql .= 'cod_localidad_nro, ';
				$sql .= 'numero, ';
				$sql .= 'cod_pais, ';
				$sql .= 'partido_departamento, ';
				$sql .= 'piso, ';
				$sql .= 'provincia, ';
				$sql .= 'doc_identidad_nro, ';
				$sql .= 'e_mail, ';
				$sql .= 'cod_faja_horaria, ';
				$sql .= 'fecha_antiguedad_gremio, ';
				$sql .= 'fecha_egreso, ';
				$sql .= 'fecha_ingreso, ';
				$sql .= 'fecha_nacimiento, ';
				$sql .= 'fotografia, ';
				$sql .= 'legajo_nro, ';
				$sql .= 'retribucion_modalidad, ';
				$sql .= 'nombres, ';
				//$sql .= 'objetivo_1, ';
				//$sql .= 'objetivo_2, ';
				//$sql .= 'objetivo_3, ';
				//$sql .= 'obra_social, ';
				//$sql .= 'premio_1, ';
				//$sql .= 'premio_2, ';
				//$sql .= 'premio_3, ';
				$sql .= 'seccion, ';
				//$sql .= 'situacion, ';
				$sql .= 'tel_domicilio, ';
				$sql .= 'tel_celular, ';
				$sql .= 'valor_hora, ';
				//$sql .= 'valor_hora_1, ';
				//$sql .= 'valor_hora_merienda, ';
				$sql .= 'valor_mes, ';
				//$sql .= 'valor_mes_1, ';
				//$sql .= 'valor_pares, ';
				$sql .= 'valor_quincena, ';
				$sql .= 'ficha ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($personal->idPersonal) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($personal->apellido) . ', ';
				$sql .= Datos::objectToDB($personal->cuil) . ', ';
				$sql .= Datos::objectToDB($personal->direccionCalle) . ', ';
				$sql .= Datos::objectToDB($personal->direccionCodigoPostal) . ', ';
				$sql .= Datos::objectToDB($personal->direccionDepartamento) . ', ';
				//$sql .= Datos::objectToDB(Funciones::padLeft($personal->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= Datos::objectToDB($personal->direccionLocalidad->id) . ', ';
				$sql .= Datos::objectToDB($personal->direccionNumero) . ', ';
				$sql .= Datos::objectToDB($personal->direccionPais->id) . ', ';
				$sql .= Datos::objectToDB($personal->direccionPartidoDepartamento) . ', ';
				$sql .= Datos::objectToDB($personal->direccionPiso) . ', ';
				$sql .= Datos::objectToDB($personal->direccionProvincia->id) . ', ';
				$sql .= Datos::objectToDB($personal->dni) . ', ';
				$sql .= Datos::objectToDB($personal->email) . ', ';
				$sql .= Datos::objectToDB($personal->fajaHoraria->id) . ', ';
				$sql .= Datos::objectToDB($personal->fechaAntiguedadGremio) . ', ';
				$sql .= Datos::objectToDB($personal->fechaEgreso) . ', ';
				$sql .= Datos::objectToDB($personal->fechaIngreso) . ', ';
				$sql .= Datos::objectToDB($personal->fechaNacimiento) . ', ';
				$sql .= Datos::objectToDB($personal->foto) . ', ';
				$sql .= Datos::objectToDB($personal->legajo) . ', '; //Legajo
				$sql .= Datos::objectToDB($personal->modalidadRetribucion) . ', ';
				$sql .= Datos::objectToDB($personal->nombre) . ', ';
				//$sql .= Datos::objectToDB($personal->objetivo1) . ', ';
				//$sql .= Datos::objectToDB($personal->objetivo2) . ', ';
				//$sql .= Datos::objectToDB($personal->objetivo3) . ', ';
				//$sql .= Datos::objectToDB($personal->obraSocial) . ', ';
				//$sql .= Datos::objectToDB($personal->premio1) . ', ';
				//$sql .= Datos::objectToDB($personal->premio2) . ', ';
				//$sql .= Datos::objectToDB($personal->premio3) . ', ';
				$sql .= Datos::objectToDB($personal->seccionProduccion->id) . ', ';
				//$sql .= Datos::objectToDB($personal->situacion) . ', ';
				$sql .= Datos::objectToDB($personal->telefono) . ', ';
				$sql .= Datos::objectToDB($personal->celular) . ', ';
				$sql .= Datos::objectToDB($personal->valorHora) . ', ';
				//$sql .= Datos::objectToDB($personal->valorHora1) . ', ';
				//$sql .= Datos::objectToDB($personal->valorHoraMerienda) . ', ';
				$sql .= Datos::objectToDB($personal->valorMes) . ', ';
				//$sql .= Datos::objectToDB($personal->valorMes1) . ', ';
				//$sql .= Datos::objectToDB($personal->valorPares) . ', ';
				$sql .= Datos::objectToDB($personal->valorQuincena) . ', ';
				$sql .= Datos::objectToDB($personal->ficha) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE personal SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($personal->anulado) . ', ';
				$sql .= 'apellido = ' . Datos::objectToDB($personal->apellido) . ', ';
				$sql .= 'cuil = ' . Datos::objectToDB($personal->cuil) . ', ';
				$sql .= 'calle = ' . Datos::objectToDB($personal->direccionCalle) . ', ';
				$sql .= 'cod_postal = ' . Datos::objectToDB($personal->direccionCodigoPostal) . ', ';
				$sql .= 'departamento = ' . Datos::objectToDB($personal->direccionDepartamento) . ', ';
				//$sql .= 'cod_localidad = ' . Datos::objectToDB(Funciones::padLeft($personal->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= 'cod_localidad_nro = ' . Datos::objectToDB($personal->direccionLocalidad->id) . ', ';
				$sql .= 'numero = ' . Datos::objectToDB($personal->direccionNumero) . ', ';
				$sql .= 'cod_pais = ' . Datos::objectToDB($personal->direccionPais->id) . ', ';
				$sql .= 'partido_departamento = ' . Datos::objectToDB($personal->direccionPartidoDepartamento) . ', ';
				$sql .= 'piso = ' . Datos::objectToDB($personal->direccionPiso) . ', ';
				$sql .= 'provincia = ' . Datos::objectToDB($personal->direccionProvincia->id) . ', ';
				$sql .= 'doc_identidad_nro = ' . Datos::objectToDB($personal->dni) . ', ';
				$sql .= 'e_mail = ' . Datos::objectToDB($personal->email) . ', ';
				$sql .= 'cod_faja_horaria = ' . Datos::objectToDB($personal->fajaHoraria->id) . ', ';
				$sql .= 'fecha_antiguedad_gremio = ' . Datos::objectToDB($personal->fechaAntiguedadGremio) . ', ';
				$sql .= 'fecha_egreso = ' . Datos::objectToDB($personal->fechaEgreso) . ', ';
				$sql .= 'fecha_ingreso = ' . Datos::objectToDB($personal->fechaIngreso) . ', ';
				$sql .= 'fecha_nacimiento = ' . Datos::objectToDB($personal->fechaNacimiento) . ', ';
				$sql .= 'fotografia = ' . Datos::objectToDB($personal->foto) . ', ';
				//$sql .= 'legajo_nro = ' . Datos::objectToDB($personal->legajo) . ', '; Cambiar el legajo implica perder todos los fichajes! Ver con Ariel o Lea
				$sql .= 'retribucion_modalidad = ' . Datos::objectToDB($personal->modalidadRetribucion) . ', ';
				$sql .= 'nombres = ' . Datos::objectToDB($personal->nombre) . ', ';
				//$sql .= 'objetivo_1 = ' . Datos::objectToDB($personal->objetivo1) . ', ';
				//$sql .= 'objetivo_2 = ' . Datos::objectToDB($personal->objetivo2) . ', ';
				//$sql .= 'objetivo_3 = ' . Datos::objectToDB($personal->objetivo3) . ', ';
				//$sql .= 'obra_social = ' . Datos::objectToDB($personal->obraSocial) . ', ';
				//$sql .= 'premio_1 = ' . Datos::objectToDB($personal->premio1) . ', ';
				//$sql .= 'premio_2 = ' . Datos::objectToDB($personal->premio2) . ', ';
				//$sql .= 'premio_3 = ' . Datos::objectToDB($personal->premio3) . ', ';
				$sql .= 'seccion = ' . Datos::objectToDB($personal->seccionProduccion->id) . ', ';
				//$sql .= 'situacion = ' . Datos::objectToDB($personal->situacion) . ', ';
				$sql .= 'tel_domicilio = ' . Datos::objectToDB($personal->telefono) . ', ';
				$sql .= 'tel_celular = ' . Datos::objectToDB($personal->celular) . ', ';
				$sql .= 'valor_hora = ' . Datos::objectToDB($personal->valorHora) . ', ';
				//$sql .= 'valor_hora_1 = ' . Datos::objectToDB($personal->valorHora1) . ', ';
				//$sql .= 'valor_hora_merienda = ' . Datos::objectToDB($personal->valorHoraMerienda) . ', ';
				$sql .= 'valor_mes = ' . Datos::objectToDB($personal->valorMes) . ', ';
				//$sql .= 'valor_mes_1 = ' . Datos::objectToDB($personal->valorMes1) . ', ';
				//$sql .= 'valor_pares = ' . Datos::objectToDB($personal->valorPares) . ', ';
				$sql .= 'valor_quincena = ' . Datos::objectToDB($personal->valorQuincena) . ', ';
				$sql .= 'ficha = ' . Datos::objectToDB($personal->ficha) . ' ';
				$sql .= 'WHERE cod_personal = ' . Datos::objectToDB($personal->idPersonal) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE personal SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_personal = ' . Datos::objectToDB($personal->idPersonal) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_personal), 0) + 1 FROM personal;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPersonalOperador(PersonalOperador $personalOperador, $modo){
		$sql = '';
		try {
			$sql .= $this->mapperQueryOperador($personalOperador, $modo);
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryLoteDeProduccion(LoteDeProduccion $loteDeProduccion, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Planes_produccion ';
				$sql .= 'WHERE nro_plan = ' . Datos::objectToDB($loteDeProduccion->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Planes_produccion (';
				$sql .= 'nro_plan, ';
				$sql .= 'denom_plan, ';
				$sql .= 'id_forecast, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_carga ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($loteDeProduccion->id) . ', ';
				$sql .= Datos::objectToDB($loteDeProduccion->nombre) . ', ';
				$sql .= Datos::objectToDB($loteDeProduccion->forecast->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Planes_produccion SET ';
				$sql .= 'denom_plan = ' . Datos::objectToDB($loteDeProduccion->nombre) . ' ';
				$sql .= 'WHERE nro_plan = ' . Datos::objectToDB($loteDeProduccion->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Orden_fabricacion SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
                $sql .= 'fecha_ultima_modificacion = GETDATE() ';
				$sql .= 'WHERE nro_plan = ' . Datos::objectToDB($loteDeProduccion->id) . '; ';
                $sql .= 'UPDATE Planes_produccion SET ';
                $sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
                $sql .= 'fecha_baja = GETDATE() ';
                $sql .= 'WHERE nro_plan = ' . Datos::objectToDB($loteDeProduccion->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_plan), 0) + 1 FROM Planes_produccion;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPredespacho(Predespacho $predespacho, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM predespachos_v ';
				$sql .= 'WHERE nro_pedido = ' . Datos::objectToDB($predespacho->pedidoNumero) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($predespacho->pedidoNumeroDeItem) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO predespachos (';
				$sql .= 'empresa, ';
				$sql .= 'nro_pedido, ';
				$sql .= 'nro_item, ';
				$sql .= 'cod_almacen, ';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				$sql .= 'predespachados, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'pred_' . $i . ', ';
				$sql .= 'tickeados, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'tick_' . $i . ', ';
				$sql .= 'fecha_alta, ';
				$sql .= 'fecha_ultima_mod ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($predespacho->pedido->empresa) . ', ';
				$sql .= Datos::objectToDB($predespacho->pedido->numero) . ', ';
				$sql .= Datos::objectToDB($predespacho->pedidoItem->numeroDeItem) . ', ';
				$sql .= Datos::objectToDB($predespacho->almacen->id) . ', ';
				$sql .= Datos::objectToDB($predespacho->articulo->id) . ', ';
				$sql .= Datos::objectToDB($predespacho->colorPorArticulo->id) . ', ';
				$sql .= Datos::objectToDB($predespacho->getTotalPredespachados()) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($predespacho->predespachados[$i]) . ', ';
				$sql .= Datos::objectToDB($predespacho->getTotalTickeados()) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($predespacho->tickeados[$i]) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE predespachos SET ';
				$sql .= 'predespachados = ' . Datos::objectToDB($predespacho->getTotalPredespachados()) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'pred_' . $i . ' = ' . Datos::objectToDB($predespacho->predespachados[$i]) . ', ';
				$sql .= 'tickeados = ' . Datos::objectToDB($predespacho->getTotalTickeados()) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'tick_' . $i . ' = ' . Datos::objectToDB($predespacho->tickeados[$i]) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE nro_pedido = ' . Datos::objectToDB($predespacho->pedidoNumero) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($predespacho->pedidoNumeroDeItem) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM predespachos ';
				$sql .= 'WHERE nro_pedido = ' . Datos::objectToDB($predespacho->pedidoNumero) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($predespacho->pedidoNumeroDeItem) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPrestamo(Prestamo $prestamo, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM prestamo ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($prestamo->empresa) . ' ';
				$sql .= 'AND nro_prestamo = ' . Datos::objectToDB($prestamo->numero) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO prestamo (';
				$sql .= 'nro_prestamo, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'cod_cuenta_bancaria, ';
				$sql .= 'importe_total, ';
				$sql .= 'importe_pendiente, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_documento, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($prestamo->numero) . ', ';
				$sql .= Datos::objectToDB($prestamo->empresa) . ', ';
				$sql .= Datos::objectToDB($prestamo->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($prestamo->cuentaBancaria->id) . ', ';
				$sql .= Datos::objectToDB($prestamo->importeTotal) . ', ';
				$sql .= Datos::objectToDB($prestamo->importePendiente) . ', ';
				$sql .= Datos::objectToDB($prestamo->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB($prestamo->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= is_null($prestamo->fecha) ? 'GETDATE(), ' : Datos::objectToDB($prestamo->fecha) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE prestamo SET ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($prestamo->asientoContable->id) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($prestamo->observaciones) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE nro_prestamo = ' . Datos::objectToDB($prestamo->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($prestamo->empresa) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE prestamo SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE nro_prestamo = ' . Datos::objectToDB($prestamo->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($prestamo->empresa) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_prestamo), 0) + 1 FROM prestamo ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($prestamo->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPresupuesto(Presupuesto $presupuesto, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM presupuesto_c ';
				$sql .= 'WHERE cod_presupuesto = ' . Datos::objectToDB($presupuesto->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO presupuesto_c (';
				$sql .= 'cod_presupuesto, ';
				$sql .= 'cod_proveedor, ';
				$sql .= 'nro_lote, ';
				$sql .= 'productiva, ';
				$sql .= 'modalidad_creacion, ';
				$sql .= 'observaciones, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($presupuesto->id) . ', ';
				$sql .= Datos::objectToDB($presupuesto->proveedor->id) . ', ';
				$sql .= Datos::objectToDB($presupuesto->loteDeProduccion->id) . ', ';
				$sql .= Datos::objectToDB($presupuesto->productivo) . ', ';
				$sql .= Datos::objectToDB($presupuesto->modalidadCreacion) . ', ';
				$sql .= Datos::objectToDB($presupuesto->observaciones) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'DELETE FROM presupuesto_d ';
				$sql .= 'WHERE cod_presupuesto = ' . Datos::objectToDB($presupuesto->id) . '; ';
				$sql .= 'UPDATE presupuesto_c SET ';
				$sql .= 'productiva = ' . Datos::objectToDB($presupuesto->productivo) . ', ';
				$sql .= 'nro_lote = ' . Datos::objectToDB($presupuesto->loteDeProduccion->id) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($presupuesto->observaciones) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_presupuesto = ' . Datos::objectToDB($presupuesto->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE presupuesto_c SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_presupuesto = ' . Datos::objectToDB($presupuesto->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_presupuesto), 0) + 1 FROM presupuesto_c;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPresupuestoItem(PresupuestoItem $presupuestoItem, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM presupuesto_d ';
				$sql .= 'WHERE cod_presupuesto = ' . Datos::objectToDB($presupuestoItem->presupuesto->id) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($presupuestoItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO presupuesto_d (';
				$sql .= 'cod_presupuesto, ';
				$sql .= 'nro_item, ';
				$sql .= 'cod_material, ';
				$sql .= 'cod_color, ';
				$sql .= 'fecha_entrega, ';
				$sql .= 'cantidad, ';

				for($i = 1; $i < 11; $i++){
					$sql .= 'cant_' . $i . ', ';
				}

				$sql .= 'saciado, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($presupuestoItem->presupuesto->id) . ', ';
				$sql .= Datos::objectToDB($presupuestoItem->numeroDeItem) . ', ';
				$sql .= Datos::objectToDB($presupuestoItem->colorMateriaPrima->material->id) . ', ';
				$sql .= Datos::objectToDB($presupuestoItem->colorMateriaPrima->idColor) . ', ';
				$sql .= Datos::objectToDB($presupuestoItem->fechaEntrega) . ', ';
				$sql .= Datos::objectToDB($presupuestoItem->cantidad) . ', ';

				for($i = 1; $i < 11; $i++){
					$sql .= Datos::objectToDB($presupuestoItem->cantidades[$i]) . ', ';
				}

				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE presupuesto_d SET ';
				$sql .= 'cod_material = ' . Datos::objectToDB($presupuestoItem->colorMateriaPrima->material->id) . ', ';
				$sql .= 'cod_color = ' . Datos::objectToDB($presupuestoItem->colorMateriaPrima->idColor) . ', ';
				$sql .= 'fecha_entrega = ' . Datos::objectToDB($presupuestoItem->fechaEntrega) . ', ';

				for($i = 1; $i < 11; $i++){
					$sql .= 'cant_' . $i . ' = ' . Datos::objectToDB($presupuestoItem->cantidades[$i]) . ', ';
				}

				$sql .= 'saciado = ' . Datos::objectToDB($presupuestoItem->saciado) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_presupuesto = ' . Datos::objectToDB($presupuestoItem->presupuesto->id) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($presupuestoItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE presupuesto_d SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_presupuesto = ' . Datos::objectToDB($presupuestoItem->presupuesto->id) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($presupuestoItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_item), 0) + 1 FROM presupuesto_d ';
				$sql .= 'WHERE cod_presupuesto = ' . Datos::objectToDB($presupuestoItem->presupuesto->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryPresupuestoOrdenCompra(PresupuestoOrdenCompra $presupuestoOrdenDeCompra, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM presupuesto_orden_compra ';
				$sql .= 'WHERE cod_presupuesto_orden_compra = ' . Datos::objectToDB($presupuestoOrdenDeCompra->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO presupuesto_orden_compra (';
				$sql .= 'cod_presupuesto_orden_compra, ';
				$sql .= 'cod_presupuesto, ';
				$sql .= 'nro_item_presupuesto, ';
				$sql .= 'cod_orden_de_compra, ';
				$sql .= 'nro_item_orden_de_compra ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($presupuestoOrdenDeCompra->id) . ', ';
				$sql .= Datos::objectToDB($presupuestoOrdenDeCompra->presupuestoItem->presupuesto->id) . ', ';
				$sql .= Datos::objectToDB($presupuestoOrdenDeCompra->presupuestoItem->numeroDeItem) . ', ';
				$sql .= Datos::objectToDB($presupuestoOrdenDeCompra->ordenDeCompraItem->ordenDeCompra->id) . ', ';
				$sql .= Datos::objectToDB($presupuestoOrdenDeCompra->ordenDeCompraItem->numeroDeItem) . ' ';
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM presupuesto_orden_compra ';
				$sql .= 'WHERE cod_presupuesto_orden_compra = ' . Datos::objectToDB($presupuestoOrdenDeCompra->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_presupuesto_orden_compra), 0) + 1 FROM presupuesto_orden_compra;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryProveedor(Proveedor $proveedor, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM proveedores_v ';
				$sql .= 'WHERE cod_prov = ' . Datos::objectToDB($proveedor->id) . ' AND ';
				$sql .= 'autorizado = ' . Datos::objectToDB('S') . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO proveedores_datos (';
				$sql .= 'cod_prov, ';
				$sql .= 'anulado, ';
				$sql .= 'autorizado, ';
				$sql .= 'concepto_reten_ganancias, ';
				$sql .= 'condicion_iva, ';
				$sql .= 'cuenta_acumuladora, ';
				$sql .= 'cuit, ';
				$sql .= 'denom_fantasia, ';
				$sql .= 'denominacion_cta_acum, ';
				$sql .= 'calle, ';
				$sql .= 'cod_postal, ';
				$sql .= 'oficina_depto, ';
				$sql .= 'cod_localidad, ';
				$sql .= 'cod_localidad_nro, ';
				$sql .= 'numero, ';
				$sql .= 'pais, ';
				$sql .= 'partido_departamento, ';
				$sql .= 'piso, ';
				$sql .= 'provincia, ';
				$sql .= 'e_mail, ';
				$sql .= 'fax, ';
				$sql .= 'horarios_atencion, ';
				$sql .= 'imputacion_en_compra, ';
				$sql .= 'imputacion_general, ';
				$sql .= 'imputacion_especifica, ';
				$sql .= 'cod_imputacion_haber, ';
				$sql .= 'jurisd_1_ingr_brutos, ';
				$sql .= 'jurisd_2_ingr_brutos, ';
				$sql .= 'limite_credito, ';
				$sql .= 'observaciones, ';
				$sql .= 'pagina_web, ';
				$sql .= 'plazo_pago, ';
				$sql .= 'plazo_pago_primera_entrega, ';
				$sql .= 'razon_social, ';
				$sql .= 'retencion_especial, ';
				$sql .= 'retener_imp_ganancias, ';
				$sql .= 'retener_ingr_brutos, ';
				$sql .= 'retener_iva, ';
				$sql .= 'rubro, ';
				$sql .= 'telefono_1, ';
				$sql .= 'telefono_2, ';
				$sql .= 'tipo_proveedor, ';
				$sql .= 'cod_transporte, ';
				$sql .= 'primera_entrega ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($proveedor->id) . ', ';
				$sql .= Datos::objectToDB('S') . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($proveedor->conceptoRetenGanancias) . ', ';
				$sql .= Datos::objectToDB($proveedor->condicionIva->id) . ', ';
				$sql .= Datos::objectToDB($proveedor->cuentaAcumuladora) . ', ';
				$sql .= Datos::objectToDB($proveedor->cuit) . ', ';
				$sql .= Datos::objectToDB($proveedor->nombre) . ', ';
				$sql .= Datos::objectToDB($proveedor->denominacionCuentaAcumuladora) . ', ';
				$sql .= Datos::objectToDB($proveedor->direccionCalle) . ', ';
				$sql .= Datos::objectToDB($proveedor->direccionCodigoPostal) . ', ';
				$sql .= Datos::objectToDB($proveedor->direccionDepartamento) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($proveedor->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= Datos::objectToDB($proveedor->direccionLocalidad->id) . ', ';
				$sql .= Datos::objectToDB($proveedor->direccionNumero) . ', ';
				$sql .= Datos::objectToDB($proveedor->direccionPais->id) . ', ';
				$sql .= Datos::objectToDB($proveedor->direccionPartidoDepartamento) . ', ';
				$sql .= Datos::objectToDB($proveedor->direccionPiso) . ', ';
				$sql .= Datos::objectToDB($proveedor->direccionProvincia->id) . ', ';
				$sql .= Datos::objectToDB($proveedor->email) . ', ';
				$sql .= Datos::objectToDB($proveedor->fax) . ', ';
				$sql .= Datos::objectToDB($proveedor->horariosAtencion) . ', ';
				$sql .= Datos::objectToDB($proveedor->imputacionEnCompra) . ', ';
				$sql .= Datos::objectToDB($proveedor->imputacionGeneral->id) . ', ';
				$sql .= Datos::objectToDB($proveedor->imputacionEspecifica->id) . ', ';
				$sql .= Datos::objectToDB($proveedor->imputacionHaber->id) . ', ';
				$sql .= Datos::objectToDB($proveedor->jurisdiccion1IngresosBrutos) . ', ';
				$sql .= Datos::objectToDB($proveedor->jurisdiccion2IngresosBrutos) . ', ';
				$sql .= Datos::objectToDB($proveedor->limiteCredito) . ', ';
				$sql .= Datos::objectToDB($proveedor->observaciones) . ', ';
				$sql .= Datos::objectToDB($proveedor->paginaWeb) . ', ';
				$sql .= Datos::objectToDB($proveedor->plazoPago) . ', ';
				$sql .= Datos::objectToDB($proveedor->plazoPagoPrimeraEntrega) . ', ';
				$sql .= Datos::objectToDB($proveedor->razonSocial) . ', ';
				$sql .= Datos::objectToDB($proveedor->retencionEspecial) . ', ';
				$sql .= Datos::objectToDB($proveedor->retenerImpuestoGanancias) . ', ';
				$sql .= Datos::objectToDB($proveedor->retenerIngresosBrutos) . ', ';
				$sql .= Datos::objectToDB($proveedor->retenerIva) . ', ';
				$sql .= Datos::objectToDB($proveedor->rubroPalabra) . ', ';
				$sql .= Datos::objectToDB($proveedor->telefono1) . ', ';
				$sql .= Datos::objectToDB($proveedor->telefono2) . ', ';
				$sql .= Datos::objectToDB($proveedor->tipoProveedor->id) . ', ';
				$sql .= Datos::objectToDB($proveedor->transporte->id) . ', ';
				$sql .= Datos::objectToDB($proveedor->vencimiento) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE proveedores_datos SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($proveedor->anulado) . ', ';
				$sql .= 'autorizado = ' . Datos::objectToDB($proveedor->autorizado) . ', ';
				$sql .= 'concepto_reten_ganancias = ' . Datos::objectToDB($proveedor->conceptoRetenGanancias) . ', ';
				$sql .= 'condicion_iva = ' . Datos::objectToDB($proveedor->condicionIva->id) . ', ';
				$sql .= 'cuenta_acumuladora = ' . Datos::objectToDB($proveedor->cuentaAcumuladora) . ', ';
				$sql .= 'cuit = ' . Datos::objectToDB($proveedor->cuit) . ', ';
				$sql .= 'denom_fantasia = ' . Datos::objectToDB($proveedor->nombre) . ', ';
				$sql .= 'denominacion_cta_acum = ' . Datos::objectToDB($proveedor->denominacionCuentaAcumuladora) . ', ';
				$sql .= 'calle = ' . Datos::objectToDB($proveedor->direccionCalle) . ', ';
				$sql .= 'cod_postal = ' . Datos::objectToDB($proveedor->direccionCodigoPostal) . ', ';
				$sql .= 'oficina_depto = ' . Datos::objectToDB($proveedor->direccionDepartamento) . ', ';
				$sql .= 'cod_localidad = ' . Datos::objectToDB(Funciones::padLeft($proveedor->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= 'cod_localidad_nro = ' . Datos::objectToDB($proveedor->direccionLocalidad->id) . ', ';
				$sql .= 'numero = ' . Datos::objectToDB($proveedor->direccionNumero) . ', ';
				$sql .= 'pais = ' . Datos::objectToDB($proveedor->direccionPais->id) . ', ';
				$sql .= 'partido_departamento = ' . Datos::objectToDB($proveedor->direccionPartidoDepartamento) . ', ';
				$sql .= 'piso = ' . Datos::objectToDB($proveedor->direccionPiso) . ', ';
				$sql .= 'provincia = ' . Datos::objectToDB($proveedor->direccionProvincia->id) . ', ';
				$sql .= 'e_mail = ' . Datos::objectToDB($proveedor->email) . ', ';
				$sql .= 'fax = ' . Datos::objectToDB($proveedor->fax) . ', ';
				$sql .= 'horarios_atencion = ' . Datos::objectToDB($proveedor->horariosAtencion) . ', ';
				$sql .= 'imputacion_en_compra = ' . Datos::objectToDB($proveedor->imputacionEnCompra) . ', ';
				$sql .= 'imputacion_general = ' . Datos::objectToDB($proveedor->imputacionGeneral->id) . ', ';
				$sql .= 'imputacion_especifica = ' . Datos::objectToDB($proveedor->imputacionEspecifica->id) . ', ';
				$sql .= 'cod_imputacion_haber = ' . Datos::objectToDB($proveedor->imputacionHaber->id) . ', ';
				$sql .= 'jurisd_1_ingr_brutos = ' . Datos::objectToDB($proveedor->jurisdiccion1IngresosBrutos) . ', ';
				$sql .= 'jurisd_2_ingr_brutos = ' . Datos::objectToDB($proveedor->jurisdiccion2IngresosBrutos) . ', ';
				$sql .= 'limite_credito = ' . Datos::objectToDB($proveedor->limiteCredito) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($proveedor->observaciones) . ', ';
				$sql .= 'observaciones_gestion = ' . Datos::objectToDB($proveedor->observacionesGestion) . ', ';
				$sql .= 'pagina_web = ' . Datos::objectToDB($proveedor->paginaWeb) . ', ';
				$sql .= 'plazo_pago = ' . Datos::objectToDB($proveedor->plazoPago) . ', ';
				$sql .= 'plazo_pago_primera_entrega = ' . Datos::objectToDB($proveedor->plazoPagoPrimeraEntrega) . ', ';
				$sql .= 'razon_social = ' . Datos::objectToDB($proveedor->razonSocial) . ', ';
				$sql .= 'retencion_especial = ' . Datos::objectToDB($proveedor->retencionEspecial) . ', ';
				$sql .= 'retener_imp_ganancias = ' . Datos::objectToDB($proveedor->retenerImpuestoGanancias) . ', ';
				$sql .= 'retener_ingr_brutos = ' . Datos::objectToDB($proveedor->retenerIngresosBrutos) . ', ';
				$sql .= 'retener_iva = ' . Datos::objectToDB($proveedor->retenerIva) . ', ';
				$sql .= 'rubro = ' . Datos::objectToDB($proveedor->rubroPalabra) . ', ';
				$sql .= 'telefono_1 = ' . Datos::objectToDB($proveedor->telefono1) . ', ';
				$sql .= 'telefono_2 = ' . Datos::objectToDB($proveedor->telefono2) . ', ';
				$sql .= 'tipo_proveedor = ' . Datos::objectToDB($proveedor->tipoProveedor->id) . ', ';
				$sql .= 'cod_transporte = ' . Datos::objectToDB($proveedor->transporte->id) . ', ';
				$sql .= 'primera_entrega = ' . Datos::objectToDB($proveedor->vencimiento) . ' ';
				$sql .= 'WHERE cod_prov = ' . Datos::objectToDB($proveedor->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE proveedores_datos SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'autorizado = ' . Datos::objectToDB('S') . ' '; //Esto es para que no aparezca como "POR AUTORIZAR"
				$sql .= 'WHERE cod_prov = ' . Datos::objectToDB($proveedor->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_prov), 0) + 1 FROM proveedores_datos;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryProveedorMateriaPrima(ProveedorMateriaPrima $proveedorMateriaPrima, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Proveedores_materias_primas_v ';
				$sql .= 'WHERE cod_proveedor = ' . Datos::objectToDB($proveedorMateriaPrima->proveedor->id) . ' ';
				$sql .= 'AND cod_material = ' . Datos::objectToDB($proveedorMateriaPrima->material->id) . ' ';
				$sql .= 'AND cod_color = ' . Datos::objectToDB($proveedorMateriaPrima->idColor) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Proveedores_materias_primas (';
				$sql .= 'cod_proveedor, ';
				$sql .= 'cod_material, ';
				$sql .= 'cod_color, ';
				$sql .= 'preferente, ';
				$sql .= 'precio_compra, ';
				$sql .= 'fecha, ';
				$sql .= 'codigo_interno, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_anulacion ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($proveedorMateriaPrima->proveedor->id) . ', ';
				$sql .= Datos::objectToDB($proveedorMateriaPrima->material->id) . ', ';
				$sql .= Datos::objectToDB($proveedorMateriaPrima->idColor) . ', ';
				$sql .= Datos::objectToDB($proveedorMateriaPrima->preferente) . ', ';
				$sql .= Datos::objectToDB($proveedorMateriaPrima->precioCompra) . ', ';
				$sql .= Datos::objectToDB($proveedorMateriaPrima->fecha) . ', ';
				$sql .= Datos::objectToDB($proveedorMateriaPrima->codigoInterno) . ', ';
				$sql .= Datos::objectToDB($proveedorMateriaPrima->anulado) . ', ';
				$sql .= Datos::objectToDB($proveedorMateriaPrima->fechaBaja) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Proveedores_materias_primas SET ';
				$sql .= 'preferente = ' . Datos::objectToDB($proveedorMateriaPrima->preferente) . ', ';
				$sql .= 'precio_compra = ' . Datos::objectToDB($proveedorMateriaPrima->precioCompra) . ' ';
				$sql .= 'WHERE cod_proveedor = ' . Datos::objectToDB($proveedorMateriaPrima->proveedor->id) . ' ';
				$sql .= 'AND cod_material = ' . Datos::objectToDB($proveedorMateriaPrima->material->id) . ' ';
				$sql .= 'AND cod_color = ' . Datos::objectToDB($proveedorMateriaPrima->idColor) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Proveedores_materias_primas SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_proveedor = ' . Datos::objectToDB($proveedorMateriaPrima->proveedor->id) . ' ';
				$sql .= 'AND cod_material = ' . Datos::objectToDB($proveedorMateriaPrima->material->id) . ' ';
				$sql .= 'AND cod_color = ' . Datos::objectToDB($proveedorMateriaPrima->idColor) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryProveedorTodos(ProveedorTodos $proveedorTodos, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM proveedores_v ';
				$sql .= 'WHERE cod_prov = ' . Datos::objectToDB($proveedorTodos->id) . '; ';
			} else {
				$sql .= $this->mapperQueryProveedor($proveedorTodos, $modo);
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryProvincia(Provincia $provincia, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Provincias ';
				$sql .= 'WHERE cod_pais = ' . Datos::objectToDB($provincia->idPais) . ' AND ';
				$sql .= 'cod_provincia = ' . Datos::objectToDB($provincia->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Provincias (';
				$sql .= 'cod_provincia, ';
				$sql .= 'denom_provincia, ';
				$sql .= 'cod_pais, ';
				$sql .= 'anulado ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($provincia->id) . ', ';
				$sql .= Datos::objectToDB($provincia->nombre) . ', ';
				$sql .= Datos::objectToDB($provincia->pais->id) . ', ';
				$sql .= Datos::objectToDB('N') . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Provincias SET ';
				$sql .= 'denom_provincia = ' . Datos::objectToDB($provincia->nombre) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB($provincia->anulado) . ' ';
				$sql .= 'WHERE cod_pais = ' . Datos::objectToDB($provincia->pais->id) . ' AND ';
				$sql .= 'cod_provincia = ' . Datos::objectToDB($provincia->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Provincias SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_pais = ' . Datos::objectToDB($provincia->pais->id) . ' AND ';
				$sql .= 'cod_provincia = ' . Datos::objectToDB($provincia->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRangoTalle(RangoTalle $rangoTalle, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM rango_talles ';
				$sql .= 'WHERE cod_rango_nro = ' . Datos::objectToDB($rangoTalle->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO rango_talles (';
				$sql .= 'cod_rango_nro, ';
				$sql .= 'cod_rango, ';
				$sql .= 'anulado, ';
				$sql .= 'denom_rango, ';
				$sql .= 'fechaAlta, ';
				for ($i = 1; $i < 21; $i++)
					$sql .= 'posic_1, ';
				$sql .= 'punto ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($rangoTalle->id) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($rangoTalle->id, 2, '0')) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($rangoTalle->nombre) . ', ';
				$sql .= 'GETDATE(), ';
				for ($i = 1; $i < 21; $i++)
					$sql .= Datos::objectToDB($rangoTalle->posicion[$i]) . ', ';
				$sql .= Datos::objectToDB($rangoTalle->punto) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE rango_talles SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($rangoTalle->anulado) . ', ';
				if ($rangoTalle->anulado == 'S')
					$sql .= 'fechaBaja = GETDATE(), ';
				$sql .= 'fecha_ultima_modificacion = GETDATE(), ';
				$sql .= 'denom_rango = ' . Datos::objectToDB($rangoTalle->nombre) . ', ';
				for ($i = 1; $i < 21; $i++)
					$sql .= 'posic_' . $i . ' = ' . Datos::objectToDB($rangoTalle->posicion[$i]) . ', ';
				$sql .= 'punto = ' . Datos::objectToDB($rangoTalle->punto) . ' ';
				$sql .= 'WHERE cod_rango_nro = ' . Datos::objectToDB($rangoTalle->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE rango_talles SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fechaBaja = GETDATE() ';
				$sql .= 'WHERE cod_rango_nro = ' . Datos::objectToDB($rangoTalle->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_rango_nro), 0) + 1 FROM rango_talles;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRechazoCheque(RechazoCheque $rechazoCheque, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM rechazo_de_cheque_d ';
				$sql .= 'WHERE cod_rechazo_cheque = ' . Datos::objectToDB($rechazoCheque->numero) . ' ';
				$sql .= 'AND entrada_salida = ' . Datos::objectToDB($rechazoCheque->entradaSalida) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($rechazoCheque->empresa) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO rechazo_de_cheque_d (';
				$sql .= 'cod_rechazo_cheque, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'importe_total, ';
				$sql .= 'entrada_salida';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($rechazoCheque->numero) . ', ';
				$sql .= Datos::objectToDB($rechazoCheque->empresa) . ', ';
				$sql .= Datos::objectToDB($rechazoCheque->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($rechazoCheque->importeTotal) . ', ';
				$sql .= Datos::objectToDB($rechazoCheque->entradaSalida);
				$sql .= '); ';
				//} elseif ($modo == Modos::update) {
				//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_rechazo_cheque), 0) + 1 FROM rechazo_de_cheque_d ';
				$sql .= 'WHERE entrada_salida = ' . Datos::objectToDB($rechazoCheque->entradaSalida) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($rechazoCheque->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRechazoChequeCabecera(RechazoChequeCabecera $rechazoChequeCabecera, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM rechazo_de_cheque_c ';
				$sql .= 'WHERE cod_rechazo_cheque = ' . Datos::objectToDB($rechazoChequeCabecera->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($rechazoChequeCabecera->empresa) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO rechazo_de_cheque_c (';
				$sql .= 'cod_rechazo_cheque, ';
				$sql .= 'empresa, ';
				$sql .= 'observaciones, ';
				$sql .= 'motivo, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_documento, ';
				$sql .= 'fecha_alta';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($rechazoChequeCabecera->numero) . ', ';
				$sql .= Datos::objectToDB($rechazoChequeCabecera->empresa) . ', ';
				$sql .= Datos::objectToDB($rechazoChequeCabecera->observaciones) . ', ';
				$sql .= Datos::objectToDB($rechazoChequeCabecera->motivo->id) . ', ';
				$sql .= Datos::objectToDB($rechazoChequeCabecera->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE rechazo_de_cheque_c SET ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($rechazoChequeCabecera->asientoContable->id) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_rechazo_cheque = ' . Datos::objectToDB($rechazoChequeCabecera->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($rechazoChequeCabecera->empresa) . '; ';
				//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_rechazo_cheque), 0) + 1 FROM rechazo_de_cheque_c ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($rechazoChequeCabecera->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRecibo(Recibo $recibo, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM recibo ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($recibo->empresa) . ' ';
				$sql .= 'AND nro_recibo = ' . Datos::objectToDB($recibo->numero) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO recibo (';
				$sql .= 'nro_recibo, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'cod_cliente, ';
				$sql .= 'operacion_tipo, ';
				$sql .= 'imputacion, ';
				$sql .= 'importe_total, ';
				$sql .= 'importe_pendiente, ';
				$sql .= 'recibido_de, ';
				$sql .= 'mail_enviado, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'cod_ecommerce_order, ';
				$sql .= 'fecha_ponderada_pago, ';
				$sql .= 'numero_recibo_provisorio, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_documento, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($recibo->numero) . ', ';
				$sql .= Datos::objectToDB($recibo->empresa) . ', ';
				$sql .= Datos::objectToDB($recibo->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($recibo->cliente->id) . ', ';
				$sql .= Datos::objectToDB($recibo->tipoOperacion) . ', ';
				$sql .= Datos::objectToDB($recibo->imputacion->id) . ', ';
				$sql .= Datos::objectToDB($recibo->importeTotal) . ', ';
				$sql .= Datos::objectToDB($recibo->importePendiente) . ', ';
				$sql .= Datos::objectToDB($recibo->recibidoDe) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($recibo->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB($recibo->ecommerceOrder->id) . ', ';
				$sql .= Datos::objectToDB($recibo->fechaPonderadaPago) . ', ';
				$sql .= Datos::objectToDB($recibo->numeroReciboProvisorio) . ', ';
				$sql .= Datos::objectToDB($recibo->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= is_null($recibo->fecha) ? 'GETDATE(), ' : Datos::objectToDB($recibo->fecha) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE recibo SET ';
				$sql .= 'fecha_ponderada_pago = ' . Datos::objectToDB($recibo->fechaPonderadaPago) . ', ';
				$sql .= 'imputacion = ' . Datos::objectToDB($recibo->imputacion->id) . ', ';
				$sql .= 'cod_cliente = ' . Datos::objectToDB($recibo->cliente->id) . ', ';
				$sql .= 'recibido_de = ' . Datos::objectToDB($recibo->recibidoDe) . ', ';
				$sql .= 'mail_enviado = ' . Datos::objectToDB($recibo->mailEnviado) . ', ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($recibo->asientoContable->id) . ', ';
				$sql .= 'cod_ecommerce_order = ' . Datos::objectToDB($recibo->ecommerceOrder->id) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($recibo->observaciones) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE nro_recibo = ' . Datos::objectToDB($recibo->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($recibo->empresa) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE recibo SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE nro_recibo = ' . Datos::objectToDB($recibo->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($recibo->empresa) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_recibo), 0) + 1 FROM recibo ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($recibo->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryReingresoChequeCartera(ReingresoChequeCartera $reingresoChequeCartera, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select) {
				$sql .= 'SELECT * ';
				$sql .= 'FROM reingreso_cheque_cartera ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($reingresoChequeCartera->empresa) . ' ';
				$sql .= 'AND cod_reingreso_cheques_cartera = ' . Datos::objectToDB($reingresoChequeCartera->numero) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO reingreso_cheque_cartera (';
				$sql .= 'cod_reingreso_cheques_cartera, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'cod_proveedor, ';
				$sql .= 'importe_total, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($reingresoChequeCartera->numero) . ', ';
				$sql .= Datos::objectToDB($reingresoChequeCartera->empresa) . ', ';
				$sql .= Datos::objectToDB($reingresoChequeCartera->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($reingresoChequeCartera->proveedor->id) . ', ';
				$sql .= Datos::objectToDB($reingresoChequeCartera->importeTotal) . ', ';
				$sql .= Datos::objectToDB($reingresoChequeCartera->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB($reingresoChequeCartera->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE reingreso_cheque_cartera SET ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($reingresoChequeCartera->asientoContable->id) . ' ';
				$sql .= 'WHERE cod_reingreso_cheques_cartera = ' . Datos::objectToDB($reingresoChequeCartera->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($reingresoChequeCartera->empresa) . '; ';
			//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_reingreso_cheques_cartera), 0) + 1 FROM reingreso_cheque_cartera ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($reingresoChequeCartera->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRemito(Remito $remito, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM remitos_c_v ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($remito->empresa) . ' ';
				$sql .= 'AND nro_remito = ' . Datos::objectToDB($remito->numero) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($remito->letra) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO remitos_c (';
				$sql .= 'empresa, ';
				$sql .= 'nro_remito, ';
				$sql .= 'letra, ';
				$sql .= 'anulado, ';
				$sql .= 'cod_cliente, ';
				$sql .= 'cod_sucursal, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'importe_total, ';
				$sql .= 'cantidad_bultos, ';
				$sql .= 'cantidad_pares, ';
				$sql .= 'cod_ecommerce_order, ';
				$sql .= 'observaciones, ';
				$sql .= 'fecha_remito, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($remito->empresa) . ', ';
				$sql .= Datos::objectToDB($remito->numero) . ', ';
				$sql .= Datos::objectToDB($remito->letra) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($remito->cliente->id) . ', ';
				$sql .= Datos::objectToDB($remito->sucursal->id) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB($remito->importe) . ', ';
				$sql .= Datos::objectToDB($remito->cantidadBultos) . ', ';
				$sql .= Datos::objectToDB($remito->cantidadPares) . ', ';
				$sql .= Datos::objectToDB($remito->ecommerceOrder->id) . ', ';
				$sql .= Datos::objectToDB($remito->observaciones) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
				foreach ($remito->detalle as $despachoItem) {
					$sql .= 'UPDATE despachos_d SET ';
					$sql .= 'nro_remito = ' . Datos::objectToDB($remito->numero) . ', ';
					$sql .= 'letra_remito = ' . Datos::objectToDB($remito->letra) . ' ';
					$sql .= 'WHERE nro_despacho = ' . Datos::objectToDB($despachoItem->despachoNumero) . ' ';
					$sql .= 'AND nro_item = ' . Datos::objectToDB($despachoItem->numeroDeItem) . '; ';
				}
			//} elseif ($modo == Modos::update) {
			} elseif ($modo == Modos::delete) {
				if ($remito->facturado())
					throw new FactoryException('No se puede borrar el remito porque pertenece a una factura');
				foreach ($remito->detalle as $despachoItem) {
					$sql .= 'UPDATE despachos_d SET ';
					$sql .= 'nro_remito = NULL, ';					
					$sql .= 'letra_remito = NULL ';
					$sql .= 'WHERE nro_despacho = ' . Datos::objectToDB($despachoItem->despachoNumero) . ' ';
					$sql .= 'AND nro_item = ' . Datos::objectToDB($despachoItem->numeroDeItem) . '; ';
				}
				$sql .= 'UPDATE remitos_c SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($remito->empresa) . ' ';
				$sql .= 'AND nro_remito = ' . Datos::objectToDB($remito->numero) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($remito->letra) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_remito), 0) + 1 FROM remitos_c ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($remito->empresa) . ' ';
				$sql .= 'AND letra = ' . Datos::objectToDB($remito->letra) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRemitoPorOrdenDeCompra(RemitoPorOrdenDeCompra $remitoPorOrdenDeCompra, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM remito_orden_de_compra ';
				$sql .= 'WHERE cod_remito_orden_de_compra = ' . Datos::objectToDB($remitoPorOrdenDeCompra->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO remito_orden_de_compra (';
				$sql .= 'cod_remito_orden_de_compra, ';
				$sql .= 'cantidad_oc, ';
				$sql .= 'cantidad, ';
				$sql .= 'cantidad_pendiente, ';
				for($i = 1; $i < 16; $i++) {
					$sql .= 'cant_oc_' . Funciones::toInt($i) . ', ';
					$sql .= 'cant_' . Funciones::toInt($i) . ', ';
					$sql .= 'cant_p_' . Funciones::toInt($i) . ', ';
				}
				$sql .= 'cod_remito_proveedor, ';
				$sql .= 'nro_item_remito_proveedor, ';
				$sql .= 'cod_orden_de_compra, ';
				$sql .= 'nro_item_orden_de_compra ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($remitoPorOrdenDeCompra->id) . ', ';
				$sql .= Datos::objectToDB($remitoPorOrdenDeCompra->cantidadOc) . ', ';
				$sql .= Datos::objectToDB($remitoPorOrdenDeCompra->cantidad) . ', ';
				$sql .= Datos::objectToDB($remitoPorOrdenDeCompra->cantidad) . ', ';
				for($i = 1; $i < 16; $i++) {
					$sql .= Datos::objectToDB($remitoPorOrdenDeCompra->cantidadesOc[$i]) . ', ';
					$sql .= Datos::objectToDB($remitoPorOrdenDeCompra->cantidades[$i]) . ', ';
					$sql .= Datos::objectToDB($remitoPorOrdenDeCompra->cantidades[$i]) . ', ';
				}
				$sql .= Datos::objectToDB($remitoPorOrdenDeCompra->remitoProveedor->id) . ', ';
				$sql .= Datos::objectToDB($remitoPorOrdenDeCompra->numeroDeItemRemitoProveedor) . ', ';
				$sql .= Datos::objectToDB($remitoPorOrdenDeCompra->ordenDeCompra->id) . ', ';
				$sql .= Datos::objectToDB($remitoPorOrdenDeCompra->numeroDeItemOrdenDeCompra) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE remito_orden_de_compra SET ';
				for($i = 1; $i < 16; $i++)
					$sql .= 'cant_p_' . $i . ' = ' . Datos::objectToDB($remitoPorOrdenDeCompra->cantidadesPendientes[$i]) . ', ';
				$sql .= 'cantidad_pendiente = ' . Datos::objectToDB($remitoPorOrdenDeCompra->cantidadPendiente) . ' ';
				$sql .= 'WHERE cod_remito_orden_de_compra = ' . Datos::objectToDB($remitoPorOrdenDeCompra->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM remito_orden_de_compra ';
				$sql .= 'WHERE cod_remito_orden_de_compra = ' . Datos::objectToDB($remitoPorOrdenDeCompra->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_remito_orden_de_compra), 0) + 1 FROM remito_orden_de_compra;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRemitoProveedor(RemitoProveedor $remitoProveedor, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM remitos_proveedor_cabecera ';
				$sql .= 'WHERE cod_remito_proveedor = ' . Datos::objectToDB($remitoProveedor->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO remitos_proveedor_cabecera (';
				$sql .= 'cod_remito_proveedor, ';
				$sql .= 'cod_proveedor, ';
				$sql .= 'nro_compuesto_remito, ';
				$sql .= 'nro_remito, ';
				$sql .= 'letra, ';
				$sql .= 'sucursal, ';
				$sql .= 'fecha_recepcion, ';
				$sql .= 'cod_almacen_recepcion, ';
				$sql .= 'con_orden_compra, ';
				$sql .= 'es_hexagono, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($remitoProveedor->id) . ', ';
				$sql .= Datos::objectToDB($remitoProveedor->proveedor->id) . ', ';
				$sql .= Datos::objectToDB('R' . Funciones::padLeft($remitoProveedor->sucursal, 4, 0) . Funciones::padLeft($remitoProveedor->numero, 8, 0)) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($remitoProveedor->numero, 8, 0)) . ', ';
				$sql .= Datos::objectToDB('R') . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($remitoProveedor->sucursal, 4, 0)) . ', ';
				$sql .= Datos::objectToDB($remitoProveedor->fechaRecepcion) . ', ';
				$sql .= Datos::objectToDB('01') . ', ';
				$sql .= Datos::objectToDB('S') . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE remitos_proveedor_cabecera SET ';
				$sql .= 'cod_documento_proveedor = ' . Datos::objectToDB($remitoProveedor->facturaProveedor->id) . ' ';
				$sql .= 'WHERE cod_remito_proveedor = ' . Datos::objectToDB($remitoProveedor->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM remito_orden_de_compra ';
				$sql .= 'WHERE cod_remito_proveedor = ' . Datos::objectToDB($remitoProveedor->id) . '; ';
				$sql .= 'DELETE FROM remitos_proveedor_detalle ';
				$sql .= 'WHERE cod_remito_proveedor = ' . Datos::objectToDB($remitoProveedor->id) . '; ';
				$sql .= 'DELETE FROM remitos_proveedor_cabecera ';
				$sql .= 'WHERE cod_remito_proveedor = ' . Datos::objectToDB($remitoProveedor->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_remito_proveedor), 0) + 1 FROM remitos_proveedor_cabecera;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRemitoProveedorItem(RemitoProveedorItem $remitoProveedorItem, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM remitos_proveedor_detalle ';
				$sql .= 'WHERE cod_remito_proveedor = ' . Datos::objectToDB($remitoProveedorItem->idRemitoProveedor) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($remitoProveedorItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO remitos_proveedor_detalle (';
				$sql .= 'cod_remito_proveedor, ';
				$sql .= 'cod_proveedor, ';
				$sql .= 'nro_compuesto_remito, ';
				$sql .= 'nro_item, ';
				$sql .= 'cod_material, ';
				$sql .= 'cod_color, ';
				$sql .= 'cantidad, ';
				for($i = 1; $i < 16; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'fuera_de_orden, ';
				$sql .= 'embalaje, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($remitoProveedorItem->remitoProveedor->id) . ', ';
				$sql .= Datos::objectToDB($remitoProveedorItem->remitoProveedor->proveedor->id) . ', ';
				$sql .= Datos::objectToDB('R' . Funciones::padLeft($remitoProveedorItem->remitoProveedor->sucursal, 4, 0) . Funciones::padLeft($remitoProveedorItem->remitoProveedor->numero, 8, 0)) . ', ';
				$sql .= Datos::objectToDB($remitoProveedorItem->numeroDeItem) . ', ';
				$sql .= Datos::objectToDB($remitoProveedorItem->colorMateriaPrima->idMaterial) . ', ';
				$sql .= Datos::objectToDB($remitoProveedorItem->colorMateriaPrima->idColor) . ', ';
				$sql .= Datos::objectToDB($remitoProveedorItem->cantidad) . ', ';
				for($i = 1; $i < 16; $i++)
					$sql .= Datos::objectToDB($remitoProveedorItem->cantidades[$i]) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($remitoProveedorItem->embalaje) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE remitos_proveedor_detalle SET ';
				$sql .= 'cantidad = ' . Datos::objectToDB($remitoProveedorItem->cantidad) . ', ';
				for($i = 1; $i < 11; $i++)
					$sql .= 'cant_' . $i . ' = ' . Datos::objectToDB($remitoProveedorItem->cantidades[$i]) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_remito_proveedor = ' . Datos::objectToDB($remitoProveedorItem->remitoProveedor->id) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($remitoProveedorItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM remitos_proveedor_detalle ';
				$sql .= 'WHERE cod_remito_proveedor = ' . Datos::objectToDB($remitoProveedorItem->remitoProveedor->id) . ' ';
				$sql .= 'AND nro_item = ' . Datos::objectToDB($remitoProveedorItem->numeroDeItem) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_item), 0) + 1 FROM remitos_proveedor_detalle ';
				$sql .= 'WHERE cod_remito_proveedor = ' . Datos::objectToDB($remitoProveedorItem->remitoProveedor->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRendicionGastos(RendicionGastos $rendicionGastos, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM rendicion_de_gastos ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($rendicionGastos->empresa) . ' AND ';
				$sql .= 'cod_rendicion_gastos = ' . Datos::objectToDB($rendicionGastos->numero) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO rendicion_de_gastos (';
				$sql .= 'cod_rendicion_gastos, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'importe_total, ';
				$sql .= 'importe_pendiente, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_documento, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($rendicionGastos->numero) . ', ';
				$sql .= Datos::objectToDB($rendicionGastos->empresa) . ', ';
				$sql .= Datos::objectToDB($rendicionGastos->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($rendicionGastos->importeTotal) . ', ';
				$sql .= Datos::objectToDB($rendicionGastos->importePendiente) . ', ';
				$sql .= Datos::objectToDB($rendicionGastos->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB($rendicionGastos->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($rendicionGastos->fecha) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE rendicion_de_gastos SET ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($rendicionGastos->asientoContable->id) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($rendicionGastos->observaciones) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_rendicion_gastos = ' . Datos::objectToDB($rendicionGastos->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($rendicionGastos->empresa) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE rendicion_de_gastos SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_rendicion_gastos = ' . Datos::objectToDB($rendicionGastos->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($rendicionGastos->empresa) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_rendicion_gastos), 0) + 1 FROM rendicion_de_gastos ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($rendicionGastos->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRetencionEfectuada(RetencionEfectuada $retencionEfectuada, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM retencion_efectuada ';
				$sql .= 'WHERE cod_retencion = ' . Datos::objectToDB($retencionEfectuada->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO retencion_efectuada (';
				$sql .= 'cod_retencion, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_tipo_retencion, ';
				$sql .= 'cod_proveedor, ';
				$sql .= 'nombre, ';
				$sql .= 'numero_certificado, ';
				$sql .= 'cuit, ';
				$sql .= 'importe_neto, ';
				$sql .= 'importe, ';
				$sql .= 'fecha, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($retencionEfectuada->id) . ', ';
				$sql .= Datos::objectToDB($retencionEfectuada->empresa) . ', ';
				$sql .= Datos::objectToDB($retencionEfectuada->tipoRetencion->id) . ', ';
				$sql .= Datos::objectToDB($retencionEfectuada->proveedor->id) . ', ';
				$sql .= Datos::objectToDB($retencionEfectuada->nombre) . ', ';
				$sql .= Datos::objectToDB($retencionEfectuada->numeroCertificado) . ', ';
				$sql .= Datos::objectToDB($retencionEfectuada->cuit) . ', ';
				$sql .= Datos::objectToDB($retencionEfectuada->importeNeto) . ', ';
				$sql .= Datos::objectToDB($retencionEfectuada->importe) . ', ';
				$sql .= Datos::objectToDB($retencionEfectuada->fecha) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE retencion_efectuada SET ';
				$sql .= 'cod_tipo_retencion = ' . Datos::objectToDB($retencionEfectuada->tipoRetencion->id) . ', ';
				$sql .= 'numero_certificado = ' . Datos::objectToDB($retencionEfectuada->numeroCertificado) . ', ';
				$sql .= 'importe_neto = ' . Datos::objectToDB($retencionEfectuada->importeNeto) . ', ';
				$sql .= 'importe = ' . Datos::objectToDB($retencionEfectuada->importe) . ', ';
				$sql .= 'fecha = ' . Datos::objectToDB($retencionEfectuada->fecha) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_retencion = ' . Datos::objectToDB($retencionEfectuada->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE retencion_efectuada SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_retencion = ' . Datos::objectToDB($retencionEfectuada->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_retencion), 0) + 1 FROM retencion_efectuada; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRetencionEscala(RetencionEscala $retencionEscala, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM retenciones_ganancias_honorarios ';
				$sql .= 'WHERE ano = ' . Datos::objectToDB($retencionEscala->ano) . ' ';
				$sql .= 'AND mes_num = ' . Datos::objectToDB($retencionEscala->mes) . ' ';
				$sql .= 'AND tramo_escala = ' . Datos::objectToDB($retencionEscala->item) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO retenciones_ganancias_honorarios (';
				$sql .= 'mes, ';
				$sql .= 'mes_num, ';
				$sql .= 'ano, ';
				$sql .= 'tramo_escala, ';
				$sql .= 'comienzo_escala, ';
				$sql .= 'final_escala, ';
				$sql .= 'fijo, ';
				$sql .= 'mas_porcentaje, ';
				$sql .= 'sobre_excedente_escala ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($retencionEscala->mes) . ', ';
				$sql .= Datos::objectToDB($retencionEscala->mes) . ', ';
				$sql .= Datos::objectToDB($retencionEscala->ano) . ', ';
				$sql .= Datos::objectToDB($retencionEscala->item) . ', ';
				$sql .= Datos::objectToDB($retencionEscala->comienzo) . ', ';
				$sql .= Datos::objectToDB($retencionEscala->final) . ', ';
				$sql .= Datos::objectToDB($retencionEscala->fijo) . ', ';
				$sql .= Datos::objectToDB($retencionEscala->masPorcentaje) . ', ';
				$sql .= Datos::objectToDB($retencionEscala->sobreExcedente) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE retenciones_ganancias_honorarios SET ';
				$sql .= 'comienzo_escala = ' . Datos::objectToDB($retencionEscala->comienzo) . ', ';
				$sql .= 'final_escala = ' . Datos::objectToDB($retencionEscala->final) . ', ';
				$sql .= 'fijo = ' . Datos::objectToDB($retencionEscala->fijo) . ', ';
				$sql .= 'mas_porcentaje = ' . Datos::objectToDB($retencionEscala->masPorcentaje) . ', ';
				$sql .= 'sobre_excedente_escala = ' . Datos::objectToDB($retencionEscala->sobreExcedente) . ' ';
				$sql .= 'WHERE ano = ' . Datos::objectToDB($retencionEscala->ano) . ' ';
				$sql .= 'AND mes_num = ' . Datos::objectToDB($retencionEscala->mes) . ' ';
				$sql .= 'AND tramo_escala = ' . Datos::objectToDB($retencionEscala->item) . '; ';
			//} elseif ($modo == Modos::delete) {
			//} elseif ($modo == Modos::id) {
			//	$sql .= 'SELECT ISNULL(MAX(cod_retencion), 0) + 1 FROM retenciones_ganancias_honorarios; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRetencionSufrida(RetencionSufrida $retencionSufrida, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM retencion_sufrida ';
				$sql .= 'WHERE cod_retencion = ' . Datos::objectToDB($retencionSufrida->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO retencion_sufrida (';
				$sql .= 'cod_retencion, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_tipo_retencion, ';
				$sql .= 'cod_cliente, ';
				$sql .= 'nombre, ';
				$sql .= 'numero_certificado, ';
				$sql .= 'cuit, ';
				$sql .= 'importe, ';
				$sql .= 'fecha, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($retencionSufrida->id) . ', ';
				$sql .= Datos::objectToDB($retencionSufrida->empresa) . ', ';
				$sql .= Datos::objectToDB($retencionSufrida->tipoRetencion->id) . ', ';
				$sql .= Datos::objectToDB($retencionSufrida->cliente->id) . ', ';
				$sql .= Datos::objectToDB($retencionSufrida->nombre) . ', ';
				$sql .= Datos::objectToDB($retencionSufrida->numeroCertificado) . ', ';
				$sql .= Datos::objectToDB($retencionSufrida->cuit) . ', ';
				$sql .= Datos::objectToDB($retencionSufrida->importe) . ', ';
				$sql .= Datos::objectToDB($retencionSufrida->fecha) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE retencion_sufrida SET ';
				$sql .= 'cod_tipo_retencion = ' . Datos::objectToDB($retencionSufrida->tipoRetencion->id) . ', ';
				$sql .= 'numero_certificado = ' . Datos::objectToDB($retencionSufrida->numeroCertificado) . ', ';
				$sql .= 'importe = ' . Datos::objectToDB($retencionSufrida->importe) . ', ';
				$sql .= 'fecha = ' . Datos::objectToDB($retencionSufrida->fecha) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_retencion = ' . Datos::objectToDB($retencionSufrida->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE retencion_sufrida SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_retencion = ' . Datos::objectToDB($retencionSufrida->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_retencion), 0) + 1 FROM retencion_sufrida; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRetencionTabla(RetencionTabla $retencionTabla, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM retencion_ganancias_tabla ';
				$sql .= 'WHERE ano = ' . Datos::objectToDB($retencionTabla->ano) . ' ';
				$sql .= 'AND mes_num = ' . Datos::objectToDB($retencionTabla->mes) . ' ';
				$sql .= 'AND item_concepto = ' . Datos::objectToDB($retencionTabla->item) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO retencion_ganancias_tabla (';
				$sql .= 'mes, ';
				$sql .= 'mes_num, ';
				$sql .= 'ano, ';
				$sql .= 'item_concepto, ';
				$sql .= 'concepto, ';
				$sql .= 'escala_o_directo, ';
				$sql .= 'monto_no_sujeto, ';
				$sql .= 'inscripto_alicuota, ';
				$sql .= 'no_inscripto_alicuota, ';
				$sql .= 'no_corresponde_menor ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($retencionTabla->mes) . ', ';
				$sql .= Datos::objectToDB($retencionTabla->mes) . ', ';
				$sql .= Datos::objectToDB($retencionTabla->ano) . ', ';
				$sql .= Datos::objectToDB($retencionTabla->item) . ', ';
				$sql .= Datos::objectToDB($retencionTabla->concepto) . ', ';
				$sql .= Datos::objectToDB($retencionTabla->escalaDirecto) . ', ';
				$sql .= Datos::objectToDB($retencionTabla->baseImponible) . ', ';
				$sql .= Datos::objectToDB($retencionTabla->inscriptoAlicuota) . ', ';
				$sql .= Datos::objectToDB($retencionTabla->noInscriptoAlicuota) . ', ';
				$sql .= Datos::objectToDB($retencionTabla->noCorrespondeMenor) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE retencion_ganancias_tabla SET ';
				$sql .= 'concepto = ' . Datos::objectToDB($retencionTabla->concepto) . ', ';
				$sql .= 'escala_o_directo = ' . Datos::objectToDB($retencionTabla->escalaDirecto) . ', ';
				$sql .= 'monto_no_sujeto = ' . Datos::objectToDB($retencionTabla->baseImponible) . ', ';
				$sql .= 'inscripto_alicuota = ' . Datos::objectToDB($retencionTabla->inscriptoAlicuota) . ', ';
				$sql .= 'no_inscripto_alicuota = ' . Datos::objectToDB($retencionTabla->noInscriptoAlicuota) . ', ';
				$sql .= 'no_corresponde_menor = ' . Datos::objectToDB($retencionTabla->noCorrespondeMenor) . ' ';
				$sql .= 'WHERE ano = ' . Datos::objectToDB($retencionTabla->ano) . ' ';
				$sql .= 'AND mes_num = ' . Datos::objectToDB($retencionTabla->mes) . ' ';
				$sql .= 'AND item_concepto = ' . Datos::objectToDB($retencionTabla->item) . '; ';
			//} elseif ($modo == Modos::delete) {
			//} elseif ($modo == Modos::id) {
			//	$sql .= 'SELECT ISNULL(MAX(cod_retencion), 0) + 1 FROM retencion_ganancias_tabla; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRetiroSocio(RetiroSocio $retiroSocio, $modo) {
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM retiro_socio ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($retiroSocio->empresa) . ' ';
				$sql .= 'AND nro_retiro_socio = ' . Datos::objectToDB($retiroSocio->numero) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO retiro_socio (';
				$sql .= 'nro_retiro_socio, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'cod_socio, ';
				$sql .= 'concepto, ';
				$sql .= 'importe_total, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_documento, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($retiroSocio->numero) . ', ';
				$sql .= Datos::objectToDB($retiroSocio->empresa) . ', ';
				$sql .= Datos::objectToDB($retiroSocio->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($retiroSocio->socio->id) . ', ';
				$sql .= Datos::objectToDB($retiroSocio->concepto) . ', ';
				$sql .= Datos::objectToDB($retiroSocio->importeTotal) . ', ';
				$sql .= Datos::objectToDB($retiroSocio->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB($retiroSocio->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() , ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE retiro_socio SET ';
				$sql .= 'concepto = ' . Datos::objectToDB($retiroSocio->concepto) . ', ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($retiroSocio->asientoContable->id) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($retiroSocio->observaciones) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE nro_retiro_socio = ' . Datos::objectToDB($retiroSocio->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($retiroSocio->empresa) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE retiro_socio SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE nro_retiro_socio = ' . Datos::objectToDB($retiroSocio->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($retiroSocio->empresa) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(nro_retiro_socio), 0) + 1 FROM retiro_socio ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($retiroSocio->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRol(Rol $rol, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM roles ';
				$sql .= 'WHERE cod_rol = ' . Datos::objectToDB($rol->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO roles (';
				$sql .= 'nombre, ';
				$sql .= 'anulado, ';
				$sql .= 'tipo, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($rol->nombre) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($rol->tipo) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				//Le borro las funcionalidades pq despuï¿½s se van a guardar en el transaction
				$sql .= 'DELETE FROM funcionalidades_por_rol ';
				$sql .= 'WHERE cod_rol = ' . Datos::objectToDB($rol->id) . '; ';
				$sql .= 'UPDATE roles SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($rol->nombre) . ' ';
				$sql .= 'WHERE cod_rol = ' . Datos::objectToDB($rol->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE roles SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_rol = ' . Datos::objectToDB($rol->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT IDENT_CURRENT(\'roles\') + IDENT_INCR(\'roles\');';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRolPorTipoNotificacion(RolPorTipoNotificacion $rolPorTipoNotificacion, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM roles_por_tipo_notificacion_v ';
				$sql .= 'WHERE cod_rol = ' . Datos::objectToDB($rolPorTipoNotificacion->id) . ' AND ';
				$sql .= 'cod_tipo_notificacion = ' . Datos::objectToDB($rolPorTipoNotificacion->idTipoNotificacion) . '; ';
			} elseif (($modo == Modos::insert) || ($modo == Modos::update)) {
				$sql .= 'INSERT INTO roles_por_tipo_notificacion (';
				$sql .= 'cod_rol, ';
				$sql .= 'cod_tipo_notificacion, ';
				$sql .= 'eliminable ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($rolPorTipoNotificacion->id) . ', ';
				$sql .= Datos::objectToDB($rolPorTipoNotificacion->tipoNotificacion->id) . ', ';
				$sql .= Datos::objectToDB($rolPorTipoNotificacion->eliminable) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM roles_por_tipo_notificacion ';
				$sql .= 'WHERE cod_rol = ' . Datos::objectToDB($rolPorTipoNotificacion->id) . ' AND ';
				$sql .= 'cod_tipo_notificacion = ' . Datos::objectToDB($rolPorTipoNotificacion->idTipoNotificacion) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRolPorUsuario(RolPorUsuario $rolPorUsuario, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				//Le pido los campos uno por uno para asï¿½ ponerles ALIAS y cuando hago
				//algï¿½n getListObject, no me tire problemas en el WHERE (puedo no poner la tabla en el where)
				$sql .= 'SELECT * FROM roles_por_usuario_v ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($rolPorUsuario->usuario->id) . ' AND ';
				$sql .= 'cod_rol = ' . Datos::objectToDB($rolPorUsuario->id) . '; ';
			} elseif (($modo == Modos::insert) || ($modo == Modos::update)) {
				$sql .= 'INSERT INTO roles_por_usuario (';
				$sql .= 'cod_usuario, ';
				$sql .= 'cod_rol ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($rolPorUsuario->idUsuario) . ', ';
				$sql .= Datos::objectToDB($rolPorUsuario->id) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM roles_por_usuario ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($rolPorUsuario->usuario->id) . ' AND ';
				$sql .= 'cod_rol = ' . Datos::objectToDB($rolPorUsuario->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRubro(Rubro $rubro, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Grupos_clientes ';
				$sql .= 'WHERE cod_grupo = ' . Datos::objectToDB($rubro->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Grupos_clientes (';
				$sql .= 'cod_grupo, ';
				$sql .= 'denom_grupo ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($rubro->id) . ', ';
				$sql .= Datos::objectToDB($rubro->nombre) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Grupos_clientes SET ';
				$sql .= 'denom_grupo = ' . Datos::objectToDB($rubro->nombre) . ' ';
				$sql .= 'WHERE cod_grupo = ' . Datos::objectToDB($rubro->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM Grupos_clientes ';
				$sql .= 'WHERE cod_grupo = ' . Datos::objectToDB($rubro->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRubroIva(RubroIva $rubroIva, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM rubros_iva ';
				$sql .= 'WHERE cod_rubro_iva = ' . Datos::objectToDB($rubroIva->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO rubros_iva (';
				$sql .= 'nombre, ';
				$sql .= 'anulado, ';
				$sql .= 'columna_iva ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($rubroIva->nombre) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($rubroIva->columnaIva) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE rubros_iva SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($rubroIva->nombre) . ', ';
				$sql .= 'columna_iva = ' . Datos::objectToDB($rubroIva->columnaIva) . ' ';
				$sql .= 'WHERE cod_rubro_iva = ' . Datos::objectToDB($rubroIva->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE rubros_iva SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_rubro_iva = ' . Datos::objectToDB($rubroIva->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT IDENT_CURRENT(\'rubros_iva\') + IDENT_INCR(\'rubros_iva\');';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRutaProduccion(RutaProduccion $rutaProduccion, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM rutas_produccion ';
				$sql .= 'WHERE cod_ruta = ' . Datos::objectToDB($rutaProduccion->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO rutas_produccion (';
				$sql .= 'cod_ruta, ';
				$sql .= 'denom_ruta, ';
				$sql .= 'fechaAlta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($rutaProduccion->id) . ', ';
				$sql .= Datos::objectToDB($rutaProduccion->nombre) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE rutas_produccion SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($rutaProduccion->anulado) . ', ';
				if ($rutaProduccion->anulado == 'S')
					$sql .= 'fecha_baja = GETDATE(), ';
				$sql .= 'denom_ruta = ' . Datos::objectToDB($rutaProduccion->nombre) . ', ';
				$sql .= 'fecha_ultima_modificacion = GETDATE() ';
				$sql .= 'WHERE cod_ruta = ' . Datos::objectToDB($rutaProduccion->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Rutas_produccion SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_ruta = ' . Datos::objectToDB($rutaProduccion->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_ruta), 0) + 1 FROM rutas_produccion;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryRutaProduccionPaso(RutaProduccionPaso $rutaProduccionPaso, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Pasos_rutas_produccion ';
				$sql .= 'WHERE cod_ruta = ' . Datos::objectToDB($rutaProduccionPaso->idRutaProduccion) . ' ';
				$sql .= 'AND cod_paso = ' . Datos::objectToDB($rutaProduccionPaso->nroPaso) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'DECLARE @idRutaProduccionPaso AS INT; ';
				$sql .= 'SET @idRutaProduccionPaso = (SELECT ISNULL(MAX(cod_paso), 0) FROM Rutas_produccion WHERE cod_ruta = ' . Datos::objectToDB($rutaProduccionPaso->rutaProduccion->id) . ') + 1; ';
				$sql .= 'INSERT INTO Pasos_rutas_produccion (';
				$sql .= 'cod_ruta, ';
				$sql .= 'cod_paso, ';
				$sql .= 'sub_paso, ';
				$sql .= 'cod_seccion, ';
				$sql .= 'ejecucion, ';
				$sql .= 'duracion, ';
				$sql .= 'punto_programacion, ';
				$sql .= 'anulado, ';
				$sql .= 'fechaAlta, ';
				$sql .= 'tiene_subordinadas, ';
				$sql .= 'jerarquia_seccion, ';
				$sql .= 'imprimir_orden_f2 ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($rutaProduccionPaso->rutaProduccion->id) . ', ';
				$sql .= '@idRutaProduccionPaso, ';
				$sql .= Datos::objectToDB($rutaProduccionPaso->nroSubPaso) . ', ';
				$sql .= Datos::objectToDB($rutaProduccionPaso->ejecucion) . ', ';
				$sql .= Datos::objectToDB($rutaProduccionPaso->duracion) . ', ';
				$sql .= Datos::objectToDB($rutaProduccionPaso->puntoProgramacion) . ', ';
				$sql .= Datos::objectToDB($rutaProduccionPaso->seccionProduccion->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= Datos::objectToDB($rutaProduccionPaso->tieneSubordinadas) . ', ';
				$sql .= Datos::objectToDB($rutaProduccionPaso->jerarquiaSeccion) . ', ';
				$sql .= Datos::objectToDB('N') . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Pasos_rutas_produccion SET ';
				$sql .= 'sub_paso = ' . Datos::objectToDB($rutaProduccionPaso->nroSubPaso) . ', ';
				$sql .= 'cod_seccion = ' . Datos::objectToDB($rutaProduccionPaso->seccionProduccion->id) . ', ';
				$sql .= 'ejecucion = ' . Datos::objectToDB($rutaProduccionPaso->ejecucion) . ', ';
				$sql .= 'duracion = ' . Datos::objectToDB($rutaProduccionPaso->duracion) . ', ';
				$sql .= 'punto_programacion = ' . Datos::objectToDB($rutaProduccionPaso->puntoProgramacion) . ', ';
				$sql .= 'fechaUltimaMod = GETDATE(), ';
				$sql .= 'tiene_subordinadas = ' . Datos::objectToDB($rutaProduccionPaso->tieneSubordinadas) . ', ';
				$sql .= 'jerarquia_seccion = ' . Datos::objectToDB($rutaProduccionPaso->jerarquiaSeccion) . ' ';
				$sql .= 'WHERE cod_ruta = ' . Datos::objectToDB($rutaProduccionPaso->rutaProduccion->id) . ' ';
				$sql .= 'AND cod_paso = ' . Datos::objectToDB($rutaProduccionPaso->nroPaso) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Pasos_rutas_produccion SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_ruta = ' . Datos::objectToDB($rutaProduccionPaso->rutaProduccion->id) . ' ';
				$sql .= 'AND cod_paso = ' . Datos::objectToDB($rutaProduccionPaso->nroPaso) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQuerySeccionProduccion(SeccionProduccion $seccionProduccion, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM secciones_produccion ';
				$sql .= 'WHERE cod_seccion = ' . Datos::objectToDB($seccionProduccion->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO secciones_produccion (';
				$sql .= 'cod_seccion, ';
				$sql .= 'anulado, ';
				$sql .= 'color, ';
				$sql .= 'denom_corta, ';
				$sql .= 'denom_seccion, ';
				$sql .= 'fechaAlta, ';
				$sql .= 'impresion_stickers, ';
				$sql .= 'ingresa_al_stock, ';
				$sql .= 'cod_almacen_default, ';
				$sql .= 'interrumpible, ';
				$sql .= 'jerarquia_seccion, ';
				$sql .= 'subordinada_de_seccion, ';
				$sql .= 'unid_med_cap_prod ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($seccionProduccion->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($seccionProduccion->color) . ', ';
				$sql .= Datos::objectToDB($seccionProduccion->nombreCorto) . ', ';
				$sql .= Datos::objectToDB($seccionProduccion->nombre) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= Datos::objectToDB($seccionProduccion->imprimeStickers) . ', ';
                $sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($seccionProduccion->almacenDefault->id) . ', ';
				$sql .= Datos::objectToDB($seccionProduccion->interrumpible) . ', ';
				$sql .= Datos::objectToDB($seccionProduccion->jerarquiaSeccion) . ', ';
				$sql .= Datos::objectToDB($seccionProduccion->seccionSuperior->id) . ', ';
				$sql .= Datos::objectToDB($seccionProduccion->unidadDeMedida->id) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE secciones_produccion SET ';
				$sql .= 'fecha_ultima_modificacion = GETDATE(), ';
				$sql .= 'color = ' . Datos::objectToDB($seccionProduccion->color) . ', ';
				$sql .= 'denom_corta = ' . Datos::objectToDB($seccionProduccion->nombreCorto) . ', ';
				$sql .= 'denom_seccion = ' . Datos::objectToDB($seccionProduccion->nombre) . ', ';
				$sql .= 'impresion_stickers = ' . Datos::objectToDB($seccionProduccion->imprimeStickers) . ', ';
				$sql .= 'ingresa_al_stock = ' . Datos::objectToDB($seccionProduccion->ingresaAlStock) . ', ';
				$sql .= 'cod_almacen_default = ' . Datos::objectToDB($seccionProduccion->almacenDefault->id) . ', ';
				$sql .= 'interrumpible = ' . Datos::objectToDB($seccionProduccion->interrumpible) . ', ';
				$sql .= 'jerarquia_seccion = ' . Datos::objectToDB($seccionProduccion->jerarquiaSeccion) . ', ';
				$sql .= 'subordinada_de_seccion = ' . Datos::objectToDB($seccionProduccion->seccionSuperior->id) . ', ';
				$sql .= 'unid_med_cap_prod = ' . Datos::objectToDB($seccionProduccion->unidadDeMedida->id) . ' ';
				$sql .= 'WHERE cod_seccion = ' . Datos::objectToDB($seccionProduccion->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE secciones_produccion SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fechaBaja = GETDATE() ';
				$sql .= 'WHERE cod_seccion = ' . Datos::objectToDB($seccionProduccion->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_seccion), 0) + 1 FROM secciones_produccion;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQuerySocio(Socio $socio, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM socio ';
				$sql .= 'WHERE cod_socio = ' . Datos::objectToDB($socio->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO socio (';
				$sql .= 'cod_socio, ';
				$sql .= 'anulado, ';
				$sql .= 'observaciones, ';
				$sql .= 'cuil, ';
				$sql .= 'dni, ';
				$sql .= 'direccion_calle, ';
				$sql .= 'direccion_codigo_postal, ';
				$sql .= 'direccion_departamento, ';
				$sql .= 'direccion_localidad, ';
				$sql .= 'direccion_numero, ';
				$sql .= 'direccion_cod_pais, ';
				$sql .= 'direccion_piso, ';
				$sql .= 'direccion_cod_provincia, ';
				$sql .= 'email, ';
				$sql .= 'nombre, ';
				$sql .= 'telefono, ';
				$sql .= 'celular, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($socio->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($socio->observaciones) . ', ';
				$sql .= Datos::objectToDB($socio->cuil) . ', ';
				$sql .= Datos::objectToDB($socio->dni) . ', ';
				$sql .= Datos::objectToDB($socio->direccion->calle) . ', ';
				$sql .= Datos::objectToDB($socio->direccion->codigoPostal) . ', ';
				$sql .= Datos::objectToDB($socio->direccion->departamento) . ', ';
				$sql .= Datos::objectToDB($socio->direccion->localidad->id) . ', ';
				$sql .= Datos::objectToDB($socio->direccion->numero) . ', ';
				$sql .= Datos::objectToDB($socio->direccion->pais->id) . ', ';
				$sql .= Datos::objectToDB($socio->direccion->piso) . ', ';
				$sql .= Datos::objectToDB($socio->direccion->provincia->id) . ', ';
				$sql .= Datos::objectToDB($socio->email) . ', ';
				$sql .= Datos::objectToDB($socio->nombre) . ', ';
				$sql .= Datos::objectToDB($socio->telefono) . ', ';
				$sql .= Datos::objectToDB($socio->celular) . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE socio SET ';
				$sql .= 'observaciones = ' . Datos::objectToDB($socio->observaciones) . ', ';
				$sql .= 'cuil = ' . Datos::objectToDB($socio->cuil) . ', ';
				$sql .= 'dni = ' . Datos::objectToDB($socio->dni) . ', ';
				$sql .= 'direccion_calle = ' . Datos::objectToDB($socio->direccion->calle) . ', ';
				$sql .= 'direccion_codigo_postal = ' . Datos::objectToDB($socio->direccion->codigoPostal) . ', ';
				$sql .= 'direccion_departamento = ' . Datos::objectToDB($socio->direccion->departamento) . ', ';
				$sql .= 'direccion_localidad = ' . Datos::objectToDB($socio->direccion->localidad->id) . ', ';
				$sql .= 'direccion_numero = ' . Datos::objectToDB($socio->direccion->numero) . ', ';
				$sql .= 'direccion_cod_pais = ' . Datos::objectToDB($socio->direccion->pais->id) . ', ';
				$sql .= 'direccion_piso = ' . Datos::objectToDB($socio->direccion->piso) . ', ';
				$sql .= 'direccion_cod_provincia = ' . Datos::objectToDB($socio->direccion->provincia->id) . ', ';
				$sql .= 'email = ' . Datos::objectToDB($socio->email) . ', ';
				$sql .= 'nombre = ' . Datos::objectToDB($socio->nombre) . ', ';
				$sql .= 'telefono = ' . Datos::objectToDB($socio->telefono) . ', ';
				$sql .= 'celular = ' . Datos::objectToDB($socio->celular) . ', ';
				$sql .= 'fecha_ultima_modificacion = GETDATE() ';
				$sql .= 'WHERE cod_socio = ' . Datos::objectToDB($socio->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE socio SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_socio = ' . Datos::objectToDB($socio->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_socio), 0) + 1 FROM socio;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQuerySolicitudDeFondos(SolicitudDeFondos $solicitudDeFondos, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM solicitud_de_fondos_c ';
				$sql .= 'WHERE cod_solicitud_de_fondos = ' . Datos::objectToDB($solicitudDeFondos->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO solicitud_de_fondos_c (';
				$sql .= 'cod_solicitud_de_fondos, ';
				$sql .= 'cod_caja_solicitante, ';
				$sql .= 'cod_caja_solicitado, ';
				$sql .= 'cerrada, ';
				$sql .= 'aprobada ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($solicitudDeFondos->id) . ', ';
				$sql .= Datos::objectToDB($solicitudDeFondos->idCajaSolicitante) . ', ';
				$sql .= Datos::objectToDB($solicitudDeFondos->idCajaSolicitado) . ', ';
				$sql .= Datos::objectToDB($solicitudDeFondos->cerrada) . ', ';
				$sql .= Datos::objectToDB($solicitudDeFondos->aprobada) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE solicitud_de_fondos_c SET ';
				$sql .= 'cerrada = ' . Datos::objectToDB($solicitudDeFondos->cerrada) . ', ';
				$sql .= 'aprobada = ' . Datos::objectToDB($solicitudDeFondos->aprobada) . ' ';
				$sql .= 'WHERE cod_solicitud_de_fondos = ' . Datos::objectToDB($solicitudDeFondos->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_solicitud_de_fondos), 0) + 1 FROM solicitud_de_fondos_c;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQuerySolicitudDeFondosItem(SolicitudDeFondosItem $solicitudDeFondosItem, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM solicitud_de_fondos_d ';
				$sql .= 'WHERE cod_solicitud_de_fondos = ' . Datos::objectToDB($solicitudDeFondosItem->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO solicitud_de_fondos_d (';
				$sql .= 'orden, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'importe, ';
				$sql .= 'motivo, ';
				$sql .= 'observaciones, ';
				$sql .= 'fecha ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($solicitudDeFondosItem->id) . ', ';
				$sql .= Datos::objectToDB($solicitudDeFondosItem->idUsuario) . ', ';
				$sql .= Datos::objectToDB($solicitudDeFondosItem->importe) . ', ';
				$sql .= Datos::objectToDB($solicitudDeFondosItem->motivo) . ', ';
				$sql .= Datos::objectToDB($solicitudDeFondosItem->observaciones) . ', ';
				$sql .= Datos::objectToDB($solicitudDeFondosItem->fechaSugerida) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_seccion), 0) + 1 FROM solicitud_de_fondos_d;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryStock(Stock $stock, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM stock ';
				$sql .= 'WHERE cod_almacen = ' . Datos::objectToDB($stock->idAlmacen) . ' ';
				$sql .= 'AND cod_articulo = ' . Datos::objectToDB($stock->idArticulo) . ' ';
				$sql .= 'AND cod_color_articulo = ' . Datos::objectToDB($stock->idColorPorArticulo) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO stock (';
				$sql .= 'cod_almacen, ';
				$sql .= 'cod_articulo, ';
				$sql .= 'cod_color_articulo, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'cantidad ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($stock->almacen->id) . ', ';
				$sql .= Datos::objectToDB($stock->articulo->id) . ', ';
				$sql .= Datos::objectToDB($stock->colorPorArticulo->id) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB(Funciones::toInt($stock->cantidad[$i])) . ', ';
				$sql .= Datos::objectToDB($stock->cantidadTotal) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE stock SET ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ' = ' . Datos::objectToDB($stock->cantidad[$i]) . ', ';
				$sql .= 'cantidad = ' . Datos::objectToDB($stock->cantidadTotal) . ' ';
				$sql .= 'WHERE cod_almacen = ' . Datos::objectToDB($stock->idAlmacen) . ' ';
				$sql .= 'AND cod_articulo = ' . Datos::objectToDB($stock->idArticulo) . ' ';
				$sql .= 'AND cod_color_articulo = ' . Datos::objectToDB($stock->idColorPorArticulo) . '; ';
				//Cuando se modifica un stock, tambiï¿½n pongo la fecha de ï¿½ltima modificaciï¿½n en sus colores (para sincronizar luego con ecommerce)
				$sql .= 'UPDATE colores_por_articulo SET ';
				$sql .= 'fechaUltimaMod = GETDATE() ';
				$sql .= 'WHERE cod_articulo = ' . Datos::objectToDB($stock->idArticulo) . ' ';
				$sql .= 'AND cod_color_articulo = ' . Datos::objectToDB($stock->idColorPorArticulo) . '; ';

			//} elseif ($modo == Modos::delete) {
			//} elseif ($modo == Modos::id) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryStockMP(StockMP $stockMP, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM stock_mp_tabla ';
				$sql .= 'WHERE cod_almacen = ' . Datos::objectToDB($stockMP->idAlmacen) . ' ';
				$sql .= 'AND cod_material = ' . Datos::objectToDB($stockMP->idMaterial) . ' ';
				$sql .= 'AND cod_color = ' . Datos::objectToDB($stockMP->idColorMateriaPrima) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO stock_mp_tabla (';
				$sql .= 'cod_almacen, ';
				$sql .= 'cod_material, ';
				$sql .= 'cod_color, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'cantidad ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($stockMP->almacen->id) . ', ';
				$sql .= Datos::objectToDB($stockMP->material->id) . ', ';
				$sql .= Datos::objectToDB($stockMP->colorMateriaPrima->idColor) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB(Funciones::toInt($stockMP->cantidad[$i])) . ', ';
				$sql .= Datos::objectToDB($stockMP->cantidadTotal) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE stock_mp_tabla SET ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ' = ' . Datos::objectToDB($stockMP->cantidad[$i]) . ', ';
				$sql .= 'cantidad = ' . Datos::objectToDB($stockMP->cantidadTotal) . ' ';
				$sql .= 'WHERE cod_almacen = ' . Datos::objectToDB($stockMP->idAlmacen) . ' ';
				$sql .= 'AND cod_material = ' . Datos::objectToDB($stockMP->idMaterial) . ' ';
				$sql .= 'AND cod_color = ' . Datos::objectToDB($stockMP->idColorMateriaPrima) . '; ';
			//} elseif ($modo == Modos::delete) {
			//} elseif ($modo == Modos::id) {
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQuerySubdiarioDeIngresosItem(/** @noinspection PhpUnusedParameterInspection */ SubdiarioDeIngresosItem $subdiarioDeIngresos, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM subdiario_de_ingresos_v ';
				$sql .= 'WHERE 1 = 1; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQuerySucursal(Sucursal $sucursal, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM sucursales_v ';
				$sql .= 'WHERE cod_cli = ' . Datos::objectToDB($sucursal->idCliente) . ' AND ';
				$sql .= 'cod_suc = ' . Datos::objectToDB($sucursal->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO sucursales_clientes (';
				$sql .= 'cod_sucursal, ';
				$sql .= 'cod_suc, ';
				$sql .= 'cod_cliente, ';
				$sql .= 'cod_cli, ';
				$sql .= 'activo, ';
				$sql .= 'anulado, ';
				$sql .= 'NumCelular, ';
				$sql .= 'cod_contacto, ';
				$sql .= 'calle, ';
				$sql .= 'cod_postal, ';
				$sql .= 'oficina_depto, ';
				$sql .= 'cod_localidad, ';
				$sql .= 'cod_localidad_nro, ';
				$sql .= 'numero, ';
				$sql .= 'cod_pais, ';
				$sql .= 'partido_departamento, ';
				$sql .= 'piso, ';
				$sql .= 'cod_provincia, ';
				$sql .= 'email, ';
				$sql .= 'cod_sucursal_entrega, ';
				$sql .= 'calle_entrega, ';
				$sql .= 'cod_postal_entrega, ';
				$sql .= 'oficina_depto_entrega, ';
				$sql .= 'cod_localidad_entrega, ';
				$sql .= 'cod_localidad_entrega_nro, ';
				$sql .= 'numero_entrega, ';
				$sql .= 'cod_pais_entrega, ';
				$sql .= 'partido_departamento_entrega, ';
				$sql .= 'piso_entrega, ';
				$sql .= 'cod_provincia_entrega, ';
				$sql .= 'casa_central, ';
				$sql .= 'punto_venta, ';
				$sql .= 'fax, ';
				$sql .= 'horario_atencion, ';
				$sql .= 'denom_sucursal, ';
				$sql .= 'observaciones, ';
				$sql .= 'reparto, ';
				$sql .= 'telefono_1, ';
				$sql .= 'telefono_2, ';
				$sql .= 'cod_transporte, ';
				$sql .= 'cod_vendedor, ';
				$sql .= 'cod_zona, ';
				$sql .= 'latitud, ';
				$sql .= 'longitud, ';
				$sql .= 'direccion_formateada, ';
				$sql .= 'horario_entrega_1, ';
				$sql .= 'horario_entrega_2 ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB(Funciones::padLeft($sucursal->id, 3, '0')) . ', ';
				$sql .= Datos::objectToDB($sucursal->id) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($sucursal->cliente->id, 6, '0')) . ', ';
				$sql .= Datos::objectToDB($sucursal->cliente->id) . ', ';
				$sql .= Datos::objectToDB('S') . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($sucursal->celular) . ', ';
				$sql .= Datos::objectToDB($sucursal->contacto->id) . ', ';
				$sql .= Datos::objectToDB($sucursal->direccionCalle) . ', ';
				$sql .= Datos::objectToDB($sucursal->direccionCodigoPostal) . ', ';
				$sql .= Datos::objectToDB($sucursal->direccionDepartamento) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($sucursal->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= Datos::objectToDB($sucursal->direccionLocalidad->id) . ', ';
				$sql .= Datos::objectToDB($sucursal->direccionNumero) . ', ';
				$sql .= Datos::objectToDB($sucursal->direccionPais->id) . ', ';
				$sql .= Datos::objectToDB($sucursal->direccionPartidoDepartamento) . ', ';
				$sql .= Datos::objectToDB($sucursal->direccionPiso) . ', ';
				$sql .= Datos::objectToDB($sucursal->direccionProvincia->id) . ', ';
				$sql .= Datos::objectToDB($sucursal->email) . ', ';
				if (!isset($sucursal->sucursalEntrega->id)) {
					$sql .= Datos::objectToDB($sucursal->id) . ', ';
					$sucursal->sucursalEntrega = $sucursal;
				} else {
					$sql .= Datos::objectToDB($sucursal->sucursalEntrega->id) . ', ';
				}
				$sql .= Datos::objectToDB($sucursal->sucursalEntrega->direccionCalle) . ', ';
				$sql .= Datos::objectToDB($sucursal->sucursalEntrega->direccionCodigoPostal) . ', ';
				$sql .= Datos::objectToDB($sucursal->sucursalEntrega->direccionDepartamento) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($sucursal->sucursalEntrega->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= Datos::objectToDB($sucursal->sucursalEntrega->direccionLocalidad->id) . ', ';
				$sql .= Datos::objectToDB($sucursal->sucursalEntrega->direccionNumero) . ', ';
				$sql .= Datos::objectToDB($sucursal->sucursalEntrega->direccionPais->id) . ', ';
				$sql .= Datos::objectToDB($sucursal->sucursalEntrega->direccionPartidoDepartamento) . ', ';
				$sql .= Datos::objectToDB($sucursal->sucursalEntrega->direccionPiso) . ', ';
				$sql .= Datos::objectToDB($sucursal->sucursalEntrega->direccionProvincia->id) . ', ';
				$sql .= Datos::objectToDB($sucursal->esCasaCentral) . ', ';
				$sql .= Datos::objectToDB($sucursal->esPuntoDeVenta) . ', ';
				$sql .= Datos::objectToDB($sucursal->fax) . ', ';
				$sql .= Datos::objectToDB($sucursal->horarioAtencion) . ', ';
				$sql .= Datos::objectToDB($sucursal->nombre) . ', ';
				$sql .= Datos::objectToDB($sucursal->observaciones) . ', ';
				$sql .= Datos::objectToDB($sucursal->reparto) . ', ';
				$sql .= Datos::objectToDB($sucursal->telefono1) . ', ';
				$sql .= Datos::objectToDB($sucursal->telefono2) . ', ';
				$sql .= Datos::objectToDB($sucursal->transporte->id) . ', ';
				$sql .= Datos::objectToDB($sucursal->vendedor->id) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($sucursal->zonaTransporte->id, 2, '0')) . ', ';
				$sql .= Datos::objectToDB($sucursal->direccionLatitud) . ', ';
				$sql .= Datos::objectToDB($sucursal->direccionLongitud) . ', ';
				$sql .= Datos::objectToDB($sucursal->direccionFormateada) . ', ';
				$sql .= Datos::objectToDB($sucursal->horarioEntrega1) . ', ';
				$sql .= Datos::objectToDB($sucursal->horarioEntrega2) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE sucursales_clientes SET ';
				$sql .= 'activo = ' . Datos::objectToDB($sucursal->activo) . ', ';
				$sql .= 'NumCelular = ' . Datos::objectToDB($sucursal->celular) . ', ';
				$sql .= 'cod_contacto = ' . Datos::objectToDB($sucursal->contacto->id) . ', ';
				$sql .= 'calle = ' . Datos::objectToDB($sucursal->direccionCalle) . ', ';
				$sql .= 'cod_postal = ' . Datos::objectToDB($sucursal->direccionCodigoPostal) . ', ';
				$sql .= 'oficina_depto = ' . Datos::objectToDB($sucursal->direccionDepartamento) . ', ';
				$sql .= 'cod_localidad = ' . Datos::objectToDB(Funciones::padLeft($sucursal->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= 'cod_localidad_nro = ' . Datos::objectToDB($sucursal->direccionLocalidad->id) . ', ';
				$sql .= 'numero = ' . Datos::objectToDB($sucursal->direccionNumero) . ', ';
				$sql .= 'cod_pais = ' . Datos::objectToDB($sucursal->direccionPais->id) . ', ';
				$sql .= 'partido_departamento = ' . Datos::objectToDB($sucursal->direccionPartidoDepartamento) . ', ';
				$sql .= 'piso = ' . Datos::objectToDB($sucursal->direccionPiso) . ', ';
				$sql .= 'cod_provincia = ' . Datos::objectToDB($sucursal->direccionProvincia->id) . ', ';
				$sql .= 'email = ' . Datos::objectToDB($sucursal->email) . ', ';
				$sql .= 'cod_sucursal_entrega = ' . Datos::objectToDB($sucursal->sucursalEntrega->id) . ', ';
				$sql .= 'calle_entrega = ' . Datos::objectToDB($sucursal->sucursalEntrega->direccionCalle) . ', ';
				$sql .= 'cod_postal_entrega = ' . Datos::objectToDB($sucursal->sucursalEntrega->direccionCodigoPostal) . ', ';
				$sql .= 'oficina_depto_entrega = ' . Datos::objectToDB($sucursal->sucursalEntrega->direccionDepartamento) . ', ';
				$sql .= 'cod_localidad_entrega = ' . Datos::objectToDB(Funciones::padLeft($sucursal->sucursalEntrega->direccionLocalidad->id, 3, '0')) . ', ';
				$sql .= 'cod_localidad_entrega_nro = ' . Datos::objectToDB($sucursal->sucursalEntrega->direccionLocalidad->id) . ', ';
				$sql .= 'numero_entrega = ' . Datos::objectToDB($sucursal->sucursalEntrega->direccionNumero) . ', ';
				$sql .= 'cod_pais_entrega = ' . Datos::objectToDB($sucursal->sucursalEntrega->direccionPais->id) . ', ';
				$sql .= 'partido_departamento_entrega = ' . Datos::objectToDB($sucursal->sucursalEntrega->direccionPartidoDepartamento) . ', ';
				$sql .= 'piso_entrega = ' . Datos::objectToDB($sucursal->sucursalEntrega->direccionPiso) . ', ';
				$sql .= 'cod_provincia_entrega = ' . Datos::objectToDB($sucursal->sucursalEntrega->direccionProvincia->id) . ', ';
				$sql .= 'casa_central = ' . Datos::objectToDB($sucursal->esCasaCentral) . ', ';
				$sql .= 'punto_venta = ' . Datos::objectToDB($sucursal->esPuntoDeVenta) . ', ';
				$sql .= 'fax = ' . Datos::objectToDB($sucursal->fax) . ', ';
				$sql .= 'horario_atencion = ' . Datos::objectToDB($sucursal->horarioAtencion) . ', ';
				$sql .= 'denom_sucursal = ' . Datos::objectToDB($sucursal->nombre) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($sucursal->observaciones) . ', ';
				$sql .= 'reparto = ' . Datos::objectToDB($sucursal->reparto) . ', ';
				$sql .= 'telefono_1 = ' . Datos::objectToDB($sucursal->telefono1) . ', ';
				$sql .= 'telefono_2 = ' . Datos::objectToDB($sucursal->telefono2) . ', ';
				$sql .= 'cod_transporte = ' . Datos::objectToDB($sucursal->transporte->id) . ', ';
				$sql .= 'cod_vendedor = ' . Datos::objectToDB($sucursal->vendedor->id) . ', ';
				$sql .= 'cod_zona = ' . Datos::objectToDB(Funciones::padLeft($sucursal->zonaTransporte->id, 2, '0')) . ', ';
				$sql .= 'latitud = ' . Datos::objectToDB($sucursal->direccionLatitud) . ', ';
				$sql .= 'longitud = ' . Datos::objectToDB($sucursal->direccionLongitud) . ', ';
				$sql .= 'direccion_formateada = ' . Datos::objectToDB($sucursal->direccionFormateada) . ', ';
				$sql .= 'horario_entrega_1 = ' . Datos::objectToDB($sucursal->horarioEntrega1) . ', ';
				$sql .= 'horario_entrega_2 = ' . Datos::objectToDB($sucursal->horarioEntrega2) . ' ';
				$sql .= 'WHERE cod_cli = ' . Datos::objectToDB($sucursal->idCliente) . ' AND ';
				$sql .= 'cod_suc = ' . Datos::objectToDB($sucursal->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE sucursales_clientes SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_cli = ' . Datos::objectToDB($sucursal->idCliente) . ' AND ';
				$sql .= 'cod_suc = ' . Datos::objectToDB($sucursal->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_suc), 0) + 1 FROM sucursales_clientes ';
				$sql .= 'WHERE cod_cli = ' . Datos::objectToDB($sucursal->cliente->id) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryTareaProduccion(TareaProduccion $tareaProduccion, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM tareas_cabecera_v ';
				$sql .= 'WHERE nro_orden_fabricacion = ' . Datos::objectToDB($tareaProduccion->idOrdenDeFabricacion) . ' ';
				$sql .= 'AND nro_tarea = ' . Datos::objectToDB($tareaProduccion->numero) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Tareas_cabecera (';
				$sql .= 'nro_orden_fabricacion, ';
				$sql .= 'nro_tarea, ';
				$sql .= 'situacion, ';
				$sql .= 'tipo_tarea, ';
				$sql .= 'tarea_deriva, ';
				$sql .= 'tarea_original, ';
				$sql .= 'paso_deriva, ';
				$sql .= 'ultimo_paso_cumplido, ';
				$sql .= 'cantidad_modulos, ';
				$sql .= 'impresa, ';
				$sql .= 'para_stock, ';
                $sql .= 'cantidad, ';
                for ($i = 1; $i <= 10; $i++)
                    $sql .= 'pos_' . $i . '_cant, ';
				$sql .= 'observacion, ';
				$sql .= 'operador_entregado, ';
				$sql .= 'fallada, ';
                $sql .= 'anulado, ';
                $sql .= 'fecha_programacion, ';
				$sql .= 'fecha_corte, ';
				$sql .= 'fecha_aparado, ';
				$sql .= 'fecha_armado ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($tareaProduccion->ordenDeFabricacion->id) . ', ';
				$sql .= Datos::objectToDB($tareaProduccion->numero) . ', ';
				$sql .= Datos::objectToDB($tareaProduccion->situacion) . ', ';
				$sql .= Datos::objectToDB($tareaProduccion->tipoTarea) . ', ';
				$sql .= Datos::objectToDB($tareaProduccion->idTareaDeriva) . ', ';
				$sql .= Datos::objectToDB($tareaProduccion->idTareaOriginal) . ', ';
				$sql .= Datos::objectToDB($tareaProduccion->pasoDeriva) . ', ';
				$sql .= Datos::objectToDB($tareaProduccion->ultimoPasoCumplido) . ', ';
				$sql .= Datos::objectToDB($tareaProduccion->cantidadModulos) . ', ';
				$sql .= Datos::objectToDB($tareaProduccion->impresa) . ', ';
				$sql .= Datos::objectToDB('S') . ', ';
                $sql .= Datos::objectToDB($tareaProduccion->cantidadTotal) . ', ';
                for ($i = 1; $i <= 10; $i++)
                    $sql .= Datos::objectToDB($tareaProduccion->cantidad[$i]) . ', ';
				$sql .= Datos::objectToDB($tareaProduccion->observaciones) . ', ';
				$sql .= Datos::objectToDB($tareaProduccion->idOperadorEntregado) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
                $sql .= Datos::objectToDB($tareaProduccion->fechaProgramacion) . ', ';
				$sql .= Datos::objectToDB($tareaProduccion->fechaCorte) . ', ';
				$sql .= Datos::objectToDB($tareaProduccion->fechaAparado) . ', ';
				$sql .= Datos::objectToDB($tareaProduccion->fechaArmado) . ' ';
				$sql .= '); ';
			/*} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Tareas_cabecera SET ';
				$sql .= 'denom_tempo = ' . Datos::objectToDB($tareaProduccion->nombre) . ', ';
				$sql .= 'tipo_tempo = ' . Datos::objectToDB($tareaProduccion->tipo) . ' ';
				$sql .= 'WHERE nro_orden_fabricacion = ' . Datos::objectToDB($tareaProduccion->idOrdenDeFabricacion) . ' ';
				$sql .= 'AND nro_tarea = ' . Datos::objectToDB($tareaProduccion->numero) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'WHERE nro_orden_fabricacion = ' . Datos::objectToDB($tareaProduccion->idOrdenDeFabricacion) . ' ';
				$sql .= 'AND nro_tarea = ' . Datos::objectToDB($tareaProduccion->numero) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_tempo), 0) + 1 FROM Tareas_cabecera;';
			*/} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryTareaProduccionItem(TareaProduccionItem $tareaProduccionItem, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM tareas_detalle_v ';
				$sql .= 'WHERE nro_orden_fabricacion = ' . Datos::objectToDB($tareaProduccionItem->idOrdenDeFabricacion) . ' ';
				$sql .= 'AND nro_tarea = ' . Datos::objectToDB($tareaProduccionItem->numeroTarea) . ' ';
				$sql .= 'AND cod_seccion = ' . Datos::objectToDB($tareaProduccionItem->idSeccionProduccion) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Tareas_detalle (';
				$sql .= 'nro_orden_fabricacion, ';
				$sql .= 'nro_tarea, ';
				$sql .= 'cod_seccion, ';
				$sql .= 'cod_unidad_produccion, ';
				$sql .= 'ejecucion, ';
				$sql .= 'nro_paso, ';
				$sql .= 'sub_paso, ';
				$sql .= 'cantidad_entrada, ';
				$sql .= 'cantidad_salida, ';
				$sql .= 'fecha_entrada_programada, ';
				$sql .= 'fecha_entrada_real, ';
				$sql .= 'hora_entrada_real, ';
				$sql .= 'fecha_salida_real, ';
				$sql .= 'hora_salida_real, ';
				$sql .= 'cod_operador, ';
				$sql .= 'duracion_paso, ';
				$sql .= 'cumplido_paso, ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'cant_' . $i . ', ';
				$sql .= 'entrada_confirmada, ';
				$sql .= 'orden_fc_generada, ';
				$sql .= 'rendido, ';
				$sql .= 'rendido_mo, ';
				$sql .= 'valor_aplicable, ';
				$sql .= 'liquidado, ';
				$sql .= 'liquidacion_nro, ';
				$sql .= 'liquidacion_fecha ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($tareaProduccionItem->ordenDeFabricacion->id) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->numeroTarea) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->seccionProduccion->id) . ', ';
				$sql .= Datos::objectToDB('01') . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->ejecucion) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->numeroPaso) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->subPaso) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->cantidadEntrada) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->cantidadSalida) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->fechaEntradaProgramada) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->fechaEntradaReal) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->horaEntradaReal) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->fechaSalidaReal) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->horaSalidaReal) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->operador->id) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->duracionPaso) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->cumplidoPaso) . ', ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= Datos::objectToDB($tareaProduccionItem->cantidad[$i]) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->entradaConfirmada) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->rendido) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->valorAplicable) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->liquidado) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->liquidacionNumero) . ', ';
				$sql .= Datos::objectToDB($tareaProduccionItem->liquidacionFecha) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Tareas_detalle SET ';
				for ($i = 1; $i <= 10; $i++)
					$sql .= 'pend_' . $i . ' = ' . Datos::objectToDB($tareaProduccionItem->pendiente[$i]) . ', ';
				$sql .= 'pendiente = ' . Datos::objectToDB($tareaProduccionItem->pendienteTotal) . ' ';
				$sql .= 'WHERE nro_orden_fabricacion = ' . Datos::objectToDB($tareaProduccionItem->idOrdenDeFabricacion) . ' ';
				$sql .= 'AND nro_tarea = ' . Datos::objectToDB($tareaProduccionItem->numeroTarea) . ' ';
				$sql .= 'AND cod_seccion = ' . Datos::objectToDB($tareaProduccionItem->idSeccionProduccion) . '; ';
			/*} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_tempo), 0) + 1 FROM Tareas_cabecera;';
			*/} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryTemporada(Temporada $temporada, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM temporadas ';
				$sql .= 'WHERE cod_tempo = ' . Datos::objectToDB($temporada->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO temporadas (';
				$sql .= 'cod_tempo, ';
				$sql .= 'denom_tempo, ';
				$sql .= 'tipo_tempo ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($temporada->id) . ', ';
				$sql .= Datos::objectToDB($temporada->nombre) . ', ';
				$sql .= Datos::objectToDB($temporada->tipo) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE temporadas SET ';
				$sql .= 'denom_tempo = ' . Datos::objectToDB($temporada->nombre) . ', ';
				$sql .= 'tipo_tempo = ' . Datos::objectToDB($temporada->tipo) . ' ';
				$sql .= 'WHERE cod_tempo = ' . Datos::objectToDB($temporada->id) . '; ';
			//} elseif ($modo == Modos::delete) { No se usa.
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_tempo), 0) + 1 FROM temporadas;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryTipoFactura(TipoFactura $tipoFactura, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM tipo_factura ';
				$sql .= 'WHERE cod_tipo_factura = ' . Datos::objectToDB($tipoFactura->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO tipo_factura (';
				$sql .= 'cod_tipo_factura, ';
				$sql .= 'nombre, ';
				$sql .= 'descripcion, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($tipoFactura->id) . ', ';
				$sql .= Datos::objectToDB($tipoFactura->nombre) . ', ';
				$sql .= Datos::objectToDB($tipoFactura->descripcion) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE tipo_factura SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($tipoFactura->nombre) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE(), ';
				$sql .= 'descripcion = ' . Datos::objectToDB($tipoFactura->descripcion) . ' ';
				$sql .= 'WHERE cod_tipo_factura = ' . Datos::objectToDB($tipoFactura->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE tipo_factura SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE(), ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_tipo_factura = ' . Datos::objectToDB($tipoFactura->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_tipo_factura), 0) + 1 FROM tipo_factura;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryTipoNotificacion(TipoNotificacion $tipoNotificacion, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM tipos_notificacion ';
				$sql .= 'WHERE cod_tipo_notificacion = ' . Datos::objectToDB($tipoNotificacion->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO tipos_notificacion (';
				$sql .= 'cod_tipo_notificacion, ';
				$sql .= 'accion_notificacion, ';
				$sql .= 'accion_cumplido, ';
				$sql .= 'accion_anular, ';
				$sql .= 'anular_al_cumplir, ';
				$sql .= 'nombre, ';
				$sql .= 'link, ';
				$sql .= 'detalle, ';
				$sql .= 'imagen, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($tipoNotificacion->id) . ', ';
				$sql .= Datos::objectToDB($tipoNotificacion->accionNotificacion) . ', ';
				$sql .= Datos::objectToDB($tipoNotificacion->accionCumplido) . ', ';
				$sql .= Datos::objectToDB($tipoNotificacion->accionAnular) . ', ';
				$sql .= Datos::objectToDB($tipoNotificacion->anularAlCumplir) . ', ';
				$sql .= Datos::objectToDB($tipoNotificacion->nombre) . ', ';
				$sql .= Datos::objectToDB($tipoNotificacion->link) . ', ';
				$sql .= Datos::objectToDB($tipoNotificacion->detalle) . ', ';
				$sql .= Datos::objectToDB($tipoNotificacion->imagen) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE tipos_notificacion SET ';
				$sql .= 'accion_notificacion = ' . Datos::objectToDB($tipoNotificacion->accionNotificacion) . ', ';
				$sql .= 'accion_cumplido = ' . Datos::objectToDB($tipoNotificacion->accionCumplido) . ', ';
				$sql .= 'accion_anular = ' . Datos::objectToDB($tipoNotificacion->accionAnular) . ', ';
				$sql .= 'anular_al_cumplir = ' . Datos::objectToDB($tipoNotificacion->anularAlCumplir) . ', ';
				$sql .= 'nombre = ' . Datos::objectToDB($tipoNotificacion->nombre) . ', ';
				$sql .= 'link = ' . Datos::objectToDB($tipoNotificacion->link) . ', ';
				$sql .= 'detalle = ' . Datos::objectToDB($tipoNotificacion->detalle) . ', ';
				$sql .= 'imagen = ' . Datos::objectToDB($tipoNotificacion->imagen) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_tipo_notificacion = ' . Datos::objectToDB($tipoNotificacion->id) . '; ';
				$sql .= 'DELETE FROM usuarios_por_tipo_notificacion ';
				$sql .= 'WHERE cod_tipo_notificacion = ' . Datos::objectToDB($tipoNotificacion->id) . '; ';
				$sql .= 'DELETE FROM roles_por_tipo_notificacion ';
				$sql .= 'WHERE cod_tipo_notificacion = ' . Datos::objectToDB($tipoNotificacion->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE tipos_notificacion SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_tipo_notificacion = ' . Datos::objectToDB($tipoNotificacion->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_tipo_notificacion), 0) + 1 FROM tipos_notificacion;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryTipoPeriodoFiscal(TipoPeriodoFiscal $tipoPeriodoFiscal, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM periodos_fiscales_tipos ';
				$sql .= 'WHERE cod_tipo_periodo = ' . Datos::objectToDB($tipoPeriodoFiscal->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO periodos_fiscales_tipos (';
				$sql .= 'cod_tipo_periodo, ';
				$sql .= 'nombre, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($tipoPeriodoFiscal->id) . ', ';
				$sql .= Datos::objectToDB($tipoPeriodoFiscal->nombre) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE periodos_fiscales_tipos SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($tipoPeriodoFiscal->nombre) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_tipo_periodo = ' . Datos::objectToDB($tipoPeriodoFiscal->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE periodos_fiscales_tipos SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_tipo_periodo = ' . Datos::objectToDB($tipoPeriodoFiscal->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_tipo_periodo), 0) + 1 FROM periodos_fiscales_tipos;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryTipoProductoStock(TipoProductoStock $tipoProductoStock, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM tipo_producto_Stock ';
				$sql .= 'WHERE id_tipo_producto_stock_nro = ' . Datos::objectToDB($tipoProductoStock->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO tipo_producto_Stock (';
				$sql .= 'id_tipo_producto_stock_nro, ';
				$sql .= 'id_tipo_producto_stock, ';
                $sql .= 'denom_tipo_producto, ';
				$sql .= 'nombre_catalogo, ';
				$sql .= 'mostrar_en_catalogo ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($tipoProductoStock->id) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($tipoProductoStock->id, 2, '0')) . ', ';
                $sql .= Datos::objectToDB($tipoProductoStock->nombre) . ', ';
				$sql .= Datos::objectToDB($tipoProductoStock->nombreCatalogo) . ', ';
				$sql .= Datos::objectToDB($tipoProductoStock->mostrarEnCatalogo) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE tipo_producto_Stock SET ';
                $sql .= 'denom_tipo_producto = ' . Datos::objectToDB($tipoProductoStock->nombre) . ', ';
				$sql .= 'nombre_catalogo = ' . Datos::objectToDB($tipoProductoStock->nombreCatalogo) . ', ';
				$sql .= 'mostrar_en_catalogo = ' . Datos::objectToDB($tipoProductoStock->mostrarEnCatalogo) . ' ';
				$sql .= 'WHERE id_tipo_producto_stock_nro = ' . Datos::objectToDB($tipoProductoStock->id) . '; ';
			//} elseif ($modo == Modos::delete) { No se usa.
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(id_tipo_producto_stock_nro), 0) + 1 FROM tipo_producto_Stock;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryTipoProveedor(TipoProveedor $tipoProveedor, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Tipos_proveedor ';
				$sql .= 'WHERE cod_tipo_proveedor = ' . Datos::objectToDB($tipoProveedor->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Tipos_proveedor (';
				$sql .= 'cod_tipo_proveedor, ';
				$sql .= 'denom_tipo_proveedor ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($tipoProveedor->id) . ', ';
				$sql .= Datos::objectToDB($tipoProveedor->nombre) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Tipos_proveedor SET ';
				$sql .= 'denom_tipo_proveedor = ' . Datos::objectToDB($tipoProveedor->nombre) . ' ';
				$sql .= 'WHERE cod_tipo_proveedor = ' . Datos::objectToDB($tipoProveedor->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM Tipos_proveedor ';
				$sql .= 'WHERE cod_tipo_proveedor = ' . Datos::objectToDB($tipoProveedor->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryTipoRetencion(TipoRetencion $tipoRetencion, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM tipo_retencion ';
				$sql .= 'WHERE cod_tipo_retencion = ' . Datos::objectToDB($tipoRetencion->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO tipo_retencion (';
				$sql .= 'cod_tipo_retencion, ';
				$sql .= 'nombre, ';
				$sql .= 'cod_imputacion ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($tipoRetencion->id) . ', ';
				$sql .= Datos::objectToDB($tipoRetencion->nombre) . ', ';
				$sql .= Datos::objectToDB($tipoRetencion->imputacion->id) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE tipo_retencion SET ';
				$sql .= 'nombre = ' . Datos::objectToDB($tipoRetencion->nombre) . ', ';
				$sql .= 'cod_imputacion = ' . Datos::objectToDB($tipoRetencion->imputacion->id) . ' ';
				$sql .= 'WHERE cod_tipo_retencion = ' . Datos::objectToDB($tipoRetencion->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM tipo_retencion ';
				$sql .= 'WHERE cod_tipo_retencion = ' . Datos::objectToDB($tipoRetencion->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_tipo_retencion), 0) + 1 FROM tipo_retencion;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryTransporte(Transporte $transporte, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM transportes ';
				$sql .= 'WHERE cod_transporte_nro = ' . Datos::objectToDB($transporte->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO transportes (';
				$sql .= 'cod_transporte_nro, ';
				$sql .= 'cod_transporte, ';
				$sql .= 'anulado, ';
				$sql .= 'cuit, ';
				$sql .= 'direccion, ';
				$sql .= 'cod_localidad_nro, ';
				$sql .= 'cod_pais, ';
				$sql .= 'cod_provincia, ';
				$sql .= 'horario, ';
				$sql .= 'mail, ';
				$sql .= 'denom_transporte, ';
				$sql .= 'telefono ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($transporte->id) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($transporte->id, 3, '0')) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($transporte->cuit) . ', ';
				$sql .= Datos::objectToDB($transporte->direccionCalle) . ', ';
				$sql .= Datos::objectToDB($transporte->direccionLocalidad->id) . ', ';
				$sql .= Datos::objectToDB($transporte->direccionPais->id) . ', ';
				$sql .= Datos::objectToDB($transporte->direccionProvincia->id) . ', ';
				$sql .= Datos::objectToDB($transporte->horario) . ', ';
				$sql .= Datos::objectToDB($transporte->email) . ', ';
				$sql .= Datos::objectToDB($transporte->nombre) . ', ';
				$sql .= Datos::objectToDB($transporte->telefono) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE transportes SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($transporte->anulado) . ', ';
				$sql .= 'cuit = ' . Datos::objectToDB($transporte->cuit) . ', ';
				$sql .= 'direccion = ' . Datos::objectToDB($transporte->direccionCalle) . ', ';
				$sql .= 'cod_localidad_nro = ' . Datos::objectToDB($transporte->direccionLocalidad->id) . ', ';
				$sql .= 'cod_pais = ' . Datos::objectToDB($transporte->direccionPais->id) . ', ';
				$sql .= 'cod_provincia = ' . Datos::objectToDB($transporte->direccionProvincia->id) . ', ';
				$sql .= 'horario = ' . Datos::objectToDB($transporte->horario) . ', ';
				$sql .= 'mail = ' . Datos::objectToDB($transporte->email) . ', ';
				$sql .= 'denom_transporte = ' . Datos::objectToDB($transporte->nombre) . ', ';
				$sql .= 'telefono = ' . Datos::objectToDB($transporte->telefono) . ' ';
				$sql .= 'WHERE cod_transporte_nro = ' . Datos::objectToDB($transporte->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE Transportes SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_transporte_nro = ' . Datos::objectToDB($transporte->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_transporte_nro), 0) + 1 FROM transportes;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryTransferenciaBancariaOperacion(TransferenciaBancariaOperacion $transfBancariaOp, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM transferencia_bancaria_operacion ';
				$sql .= 'WHERE cod_transferencia_ban = ' . Datos::objectToDB($transfBancariaOp->numero) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO transferencia_bancaria_operacion (';
				$sql .= 'cod_transferencia_ban, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'fecha, ';
				$sql .= 'cod_cuenta_bancaria, ';
				$sql .= 'entrada_salida, ';
				$sql .= 'importe_total, ';
				$sql .= 'numero_transferencia, ';
				$sql .= 'observaciones, ';
				$sql .= 'hacia_desde, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($transfBancariaOp->numero) . ', ';
				$sql .= Datos::objectToDB($transfBancariaOp->empresa) . ', ';
				$sql .= Datos::objectToDB($transfBancariaOp->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($transfBancariaOp->fechaTransferencia) . ', ';
				$sql .= Datos::objectToDB($transfBancariaOp->cuentaBancaria->id) . ', ';
				$sql .= Datos::objectToDB($transfBancariaOp->entradaSalida) . ', ';
				$sql .= Datos::objectToDB($transfBancariaOp->importeTotal) . ', ';
				$sql .= Datos::objectToDB($transfBancariaOp->numeroTransferencia) . ', ';
				$sql .= Datos::objectToDB($transfBancariaOp->observaciones) . ', ';
				$sql .= Datos::objectToDB($transfBancariaOp->haciaDesde) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE() ';
				$sql .= '); ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE transferencia_bancaria_operacion SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_transferencia_ban = ' . Datos::objectToDB($transfBancariaOp->numero) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_transferencia_ban), 0) + 1 FROM transferencia_bancaria_operacion;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryTransferenciaBancariaImporte(TransferenciaBancariaImporte $transfBancariaImp, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM transferencia_bancaria_importe ';
				$sql .= 'WHERE cod_transferencia_ban = ' . Datos::objectToDB($transfBancariaImp->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO transferencia_bancaria_importe (';
				$sql .= 'cod_transferencia_ban, ';
				$sql .= 'empresa, ';
				$sql .= 'importe, ';
				$sql .= 'cod_transferencia_ban_op ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($transfBancariaImp->id) . ', ';
				$sql .= Datos::objectToDB($transfBancariaImp->empresa) . ', ';
				$sql .= Datos::objectToDB($transfBancariaImp->importe) . ', ';
				$sql .= Datos::objectToDB($transfBancariaImp->numeroTransferenciaBancariaOperacion) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_transferencia_ban), 0) + 1 FROM transferencia_bancaria_importe;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryTransferenciaInterna(TransferenciaInterna $transferenciaInterna, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM transferencia_interna_d ';
				$sql .= 'WHERE cod_transferencia_int = ' . Datos::objectToDB($transferenciaInterna->numero) . ' ';
				$sql .= 'AND entrada_salida = ' . Datos::objectToDB($transferenciaInterna->entradaSalida) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($transferenciaInterna->empresa) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO transferencia_interna_d (';
				$sql .= 'cod_transferencia_int, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'importe_total, ';
				$sql .= 'entrada_salida';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($transferenciaInterna->numero) . ', ';
				$sql .= Datos::objectToDB($transferenciaInterna->empresa) . ', ';
				$sql .= Datos::objectToDB($transferenciaInterna->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($transferenciaInterna->importeTotal) . ', ';
				$sql .= Datos::objectToDB($transferenciaInterna->entradaSalida);
				$sql .= '); ';
			//} elseif ($modo == Modos::update) {
			//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_transferencia_int), 0) + 1 FROM transferencia_interna_d ';
				$sql .= 'WHERE entrada_salida = ' . Datos::objectToDB($transferenciaInterna->entradaSalida) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($transferenciaInterna->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryTransferenciaInternaCabecera(TransferenciaInternaCabecera $transferenciaInternaCabecera, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM transferencia_interna_c ';
				$sql .= 'WHERE cod_transferencia_int = ' . Datos::objectToDB($transferenciaInternaCabecera->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($transferenciaInternaCabecera->empresa) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO transferencia_interna_c (';
				$sql .= 'cod_transferencia_int, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'fecha_documento, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_alta';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($transferenciaInternaCabecera->numero) . ', ';
				$sql .= Datos::objectToDB($transferenciaInternaCabecera->empresa) . ', ';
				$sql .= Datos::objectToDB($transferenciaInternaCabecera->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB($transferenciaInternaCabecera->fechaDocumento) . ', ';
				$sql .= Datos::objectToDB($transferenciaInternaCabecera->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE transferencia_interna_c SET ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($transferenciaInternaCabecera->asientoContable->id) . ', ';
				$sql .= 'observaciones = ' . Datos::objectToDB($transferenciaInternaCabecera->observaciones) . ' ';
				$sql .= 'WHERE cod_transferencia_int = ' . Datos::objectToDB($transferenciaInternaCabecera->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($transferenciaInternaCabecera->empresa) . '; ';
			//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_transferencia_int), 0) + 1 FROM transferencia_interna_c ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($transferenciaInternaCabecera->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryUnidadDeMedida(UnidadDeMedida $unidadDeMedida, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM Unidades_medida ';
				$sql .= 'WHERE cod_unidad = ' . Datos::objectToDB($unidadDeMedida->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO Unidades_medida (';
				$sql .= 'cod_unidad, ';
				$sql .= 'denom_unidad ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($unidadDeMedida->id) . ', ';
				$sql .= Datos::objectToDB($unidadDeMedida->nombre) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE Unidades_medida SET ';
				$sql .= 'denom_unidad = ' . Datos::objectToDB($unidadDeMedida->nombre) . ' ';
				$sql .= 'WHERE cod_unidad = ' . Datos::objectToDB($unidadDeMedida->id) . '; ';
			//} elseif ($modo == Modos::delete) { No se usa.
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryUsuario(Usuario $usuario, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM users ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($usuario->id) . '; ';
			//} elseif ($modo == Modos::insert) { Se hace INSERT sï¿½lo de UsuarioLogin
			} elseif ($modo == Modos::update) {
				$sql .= 'DELETE FROM roles_por_usuario ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($usuario->id) . '; ';
				$sql .= 'UPDATE users SET ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fechaUltimaMod = GETDATE() ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($usuario->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE users SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ', ';
				$sql .= 'fechaBaja = GETDATE() ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($usuario->id) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryUsuarioCalzado(UsuarioCalzado $usuarioCalzado, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM usuarios ';
				$sql .= 'WHERE cod_usuarios = ' . Datos::objectToDB($usuarioCalzado->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO usuarios (';
				$sql .= 'cod_usuarios, ';
				$sql .= 'denom_usuarios ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($usuarioCalzado->id) . ', ';
				$sql .= Datos::objectToDB($usuarioCalzado->nombre) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE usuarios SET ';
				$sql .= 'denom_usuarios = ' . Datos::objectToDB($usuarioCalzado->nombre) . ' ';
				$sql .= 'WHERE cod_usuarios = ' . Datos::objectToDB($usuarioCalzado->id) . '; ';
			//} elseif ($modo == Modos::delete) { No se usa.
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_usuarios), 0) + 1 FROM usuarios;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryUsuarioLogin(UsuarioLogin $usuarioLogin, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= $this->mapperQueryUsuario($usuarioLogin, $modo);
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO users (';
				$sql .= 'cod_usuario, ';
				$sql .= 'password, ';
				$sql .= 'tipo, ';
				$sql .= 'cod_personal, ';
				$sql .= 'cod_contacto, ';
				$sql .= 'cod_usuario_alta, ';
				$sql .= 'fechaAlta, ';
				$sql .= 'anulado ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($usuarioLogin->id) . ', ';
				$sql .= Datos::objectToDB($usuarioLogin->password) . ', ';
				$sql .= Datos::objectToDB($usuarioLogin->tipoPersona) . ', ';
				if ($usuarioLogin->tipoUsuario == 'P')
					$sql .= Datos::objectToDB($usuarioLogin->idPersonal) . ', ';
				else
					$sql .= 'NULL, ';
				if ($usuarioLogin->tipoUsuario == 'C')
					$sql .= Datos::objectToDB($usuarioLogin->contacto->id) . ', ';
				else
					$sql .= 'NULL, ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= Datos::objectToDB('N') . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'DELETE FROM roles_por_usuario ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($usuarioLogin->id) . '; ';
				$sql .= 'UPDATE users SET ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fechaUltimaMod = GETDATE(), ';
				$sql .= 'password = ' . Datos::objectToDB($usuarioLogin->password) . ' ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($usuarioLogin->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= $this->mapperQueryUsuario($usuarioLogin, $modo);
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryUsuarioPorAlmacen(UsuarioPorAlmacen $usuarioPorAlmacen, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * FROM usuarios_por_almacen_v ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($usuarioPorAlmacen->id) . ' ';
				$sql .= 'AND cod_almacen = ' . Datos::objectToDB($usuarioPorAlmacen->idAlmacen) . '; ';
			} elseif (($modo == Modos::insert) || ($modo == Modos::update)) {
				$sql .= 'INSERT INTO usuarios_por_almacen (';
				$sql .= 'cod_usuario, ';
				$sql .= 'cod_almacen ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($usuarioPorAlmacen->id) . ', ';
				$sql .= Datos::objectToDB($usuarioPorAlmacen->almacen->id) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM usuarios_por_almacen ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($usuarioPorAlmacen->id) . ' ';
				$sql .= 'AND cod_almacen = ' . Datos::objectToDB($usuarioPorAlmacen->idAlmacen) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryUsuarioPorAreaEmpresa(UsuarioPorAreaEmpresa $usuarioPorAreaEmpresa, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * FROM usuarios_por_area_empresa_v ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($usuarioPorAreaEmpresa->id) . ' ';
				$sql .= 'AND id_area_empresa = ' . Datos::objectToDB($usuarioPorAreaEmpresa->idAreaEmpresa) . '; ';
			} elseif (($modo == Modos::insert) || ($modo == Modos::update)) {
				$sql .= 'INSERT INTO usuarios_por_area_empresa (';
				$sql .= 'cod_usuario, ';
				$sql .= 'id_area_empresa ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($usuarioPorAreaEmpresa->id) . ', ';
				$sql .= Datos::objectToDB($usuarioPorAreaEmpresa->areaEmpresa->id) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM usuarios_por_area_empresa ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($usuarioPorAreaEmpresa->id) . ' ';
				$sql .= 'AND id_area_empresa = ' . Datos::objectToDB($usuarioPorAreaEmpresa->idAreaEmpresa) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryUsuarioPorCaja(UsuarioPorCaja $usuarioPorCaja, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM usuarios_por_caja_v ';
				$sql .= 'WHERE cod_caja = ' . Datos::objectToDB($usuarioPorCaja->idCaja) . ' AND ';
				$sql .= 'cod_usuario = ' . Datos::objectToDB($usuarioPorCaja->idUsuario) . '; ';
			} elseif (($modo == Modos::insert) || ($modo == Modos::update)) {
				$sql .= 'INSERT INTO usuarios_por_caja (';
				$sql .= 'cod_caja, ';
				$sql .= 'cod_usuario ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($usuarioPorCaja->idCaja) . ', ';
				$sql .= Datos::objectToDB($usuarioPorCaja->idUsuario) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM usuarios_por_caja ';
				$sql .= 'WHERE cod_caja = ' . Datos::objectToDB($usuarioPorCaja->caja->id);
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
    private function mapperQueryUsuarioPorSeccionProduccion(UsuarioPorSeccionProduccion $usuarioPorSeccionProduccion, $modo){
        $sql = '';
        try {
            if ($modo == Modos::select){
                $sql .= 'SELECT * FROM usuarios_por_seccion_v ';
                $sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($usuarioPorSeccionProduccion->id) . ' ';
                $sql .= 'AND cod_seccion = ' . Datos::objectToDB($usuarioPorSeccionProduccion->idSeccionProduccion) . '; ';
            } elseif (($modo == Modos::insert) || ($modo == Modos::update)) {
                $sql .= 'INSERT INTO usuarios_por_seccion (';
                $sql .= 'cod_usuario, ';
                $sql .= 'cod_seccion ';
                $sql .= ') VALUES (';
                $sql .= Datos::objectToDB($usuarioPorSeccionProduccion->id) . ', ';
                $sql .= Datos::objectToDB($usuarioPorSeccionProduccion->seccionProduccion->id) . ' ';
                $sql .= '); ';
            } elseif ($modo == Modos::delete) {
                $sql .= 'DELETE FROM usuarios_por_seccion ';
                $sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($usuarioPorSeccionProduccion->id) . ' ';
                $sql .= 'AND cod_seccion = ' . Datos::objectToDB($usuarioPorSeccionProduccion->idSeccionProduccion) . '; ';
            } else {
                throw new FactoryException('Modo incorrecto');
            }
            return $sql;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
	private function mapperQueryUsuarioPorTipoNotificacion(UsuarioPorTipoNotificacion $usuarioPorTipoNotificacion, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM usuarios_por_tipo_notificacion_v ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($usuarioPorTipoNotificacion->id) . ' AND ';
				$sql .= 'cod_tipo_notificacion = ' . Datos::objectToDB($usuarioPorTipoNotificacion->idTipoNotificacion) . '; ';
			} elseif (($modo == Modos::insert) || ($modo == Modos::update)) {
				$sql .= 'INSERT INTO usuarios_por_tipo_notificacion (';
				$sql .= 'cod_usuario, ';
				$sql .= 'cod_tipo_notificacion, ';
				$sql .= 'eliminable ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($usuarioPorTipoNotificacion->id) . ', ';
				$sql .= Datos::objectToDB($usuarioPorTipoNotificacion->tipoNotificacion->id) . ', ';
				$sql .= Datos::objectToDB($usuarioPorTipoNotificacion->eliminable) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'DELETE FROM usuarios_por_tipo_notificacion ';
				$sql .= 'WHERE cod_usuario = ' . Datos::objectToDB($usuarioPorTipoNotificacion->id) . ' AND ';
				$sql .= 'cod_tipo_notificacion = ' . Datos::objectToDB($usuarioPorTipoNotificacion->idTipoNotificacion) . '; ';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryVendedor(Vendedor $vendedor, $modo){
		$sql = '';
		try {
			$sql .= $this->mapperQueryOperador($vendedor, $modo);
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryVentaCheques(VentaCheques $ventaCheques, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM venta_cheques_d ';
				$sql .= 'WHERE cod_venta_cheques = ' . Datos::objectToDB($ventaCheques->numero) . ' ';
				$sql .= 'AND entrada_salida = ' . Datos::objectToDB($ventaCheques->entradaSalida) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($ventaCheques->empresa) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO venta_cheques_d (';
				$sql .= 'cod_venta_cheques, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_importe_operacion, ';
				$sql .= 'importe_total, ';
				$sql .= 'entrada_salida';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ventaCheques->numero) . ', ';
				$sql .= Datos::objectToDB($ventaCheques->empresa) . ', ';
				$sql .= Datos::objectToDB($ventaCheques->importePorOperacion->idImportePorOperacion) . ', ';
				$sql .= Datos::objectToDB($ventaCheques->importeTotal) . ', ';
				$sql .= Datos::objectToDB($ventaCheques->entradaSalida);
				$sql .= '); ';
				//} elseif ($modo == Modos::update) {
				//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_venta_cheques), 0) + 1 FROM venta_cheques_d ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($ventaCheques->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryVentaChequesCabecera(VentaChequesCabecera $ventaChequesCabecera, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM venta_cheques_c ';
				$sql .= 'WHERE cod_venta_cheques = ' . Datos::objectToDB($ventaChequesCabecera->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($ventaChequesCabecera->empresa) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO venta_cheques_c (';
				$sql .= 'cod_venta_cheques, ';
				$sql .= 'empresa, ';
				$sql .= 'cod_asiento_contable, ';
				$sql .= 'observaciones, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'fecha_documento, ';
				$sql .= 'fecha_alta';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ventaChequesCabecera->numero) . ', ';
				$sql .= Datos::objectToDB($ventaChequesCabecera->empresa) . ', ';
				$sql .= Datos::objectToDB($depositoChequeCabecera->asientoContable->id) . ', ';
				$sql .= Datos::objectToDB($ventaChequesCabecera->observaciones) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB($ventaChequesCabecera->fecha) . ', ';
				$sql .= 'GETDATE()';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE venta_cheques_c SET ';
				$sql .= 'cod_asiento_contable = ' . Datos::objectToDB($ventaChequesCabecera->asientoContable->id) . ', ';
				$sql .= 'observaciones = ' .Datos::objectToDB($ventaChequesCabecera->observaciones) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_venta_cheques = ' . Datos::objectToDB($ventaChequesCabecera->numero) . ' ';
				$sql .= 'AND empresa = ' . Datos::objectToDB($ventaChequesCabecera->empresa) . '; ';
				//} elseif ($modo == Modos::delete) {
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_venta_cheques), 0) + 1 FROM venta_cheques_c ';
				$sql .= 'WHERE empresa = ' . Datos::objectToDB($ventaChequesCabecera->empresa) . ';';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryVentaChequesTemporal(VentaChequesTemporal $ventaChequesTemporal, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM venta_cheques_temporal ';
				$sql .= 'WHERE cod_venta_cheques_temporal = ' . Datos::objectToDB($ventaChequesTemporal->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO venta_cheques_temporal (';
				$sql .= 'cod_venta_cheques_temporal, ';
				$sql .= 'cod_caja, ';
				$sql .= 'cod_cuenta_bancaria, ';
				$sql .= 'fecha, ';
				$sql .= 'cheques, ';
				$sql .= 'cod_usuario, ';
				$sql .= 'confirmado, ';
				$sql .= 'anulado, ';
				$sql .= 'fecha_alta, ';
				$sql .= 'empresa ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($ventaChequesTemporal->id) . ', ';
				$sql .= Datos::objectToDB($ventaChequesTemporal->caja->id) . ', ';
				$sql .= Datos::objectToDB($ventaChequesTemporal->cuentaBancaria->id) . ', ';
				$sql .= Datos::objectToDB($ventaChequesTemporal->fecha) . ', ';
				$sql .= Datos::objectToDB($ventaChequesTemporal->idCheques) . ', ';
				$sql .= Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= 'GETDATE(), ';
				$sql .= Datos::objectToDB($ventaChequesTemporal->empresa) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE venta_cheques_temporal SET ';
				$sql .= 'cod_cuenta_bancaria = ' . Datos::objectToDB($ventaChequesTemporal->cuentaBancaria->id) . ', ';
				$sql .= 'cheques = ' . Datos::objectToDB($ventaChequesTemporal->idCheques) . ', ';
				$sql .= 'confirmado = ' .Datos::objectToDB($ventaChequesTemporal->confirmado) . ', ';
				$sql .= 'cod_usuario_ultima_mod = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'fecha_ultima_mod = GETDATE() ';
				$sql .= 'WHERE cod_venta_cheques_temporal = ' . Datos::objectToDB($ventaChequesTemporal->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE venta_cheques_temporal SET ';
				$sql .= 'cod_usuario_baja = ' . Datos::objectToDB(Usuario::logueado()->id) . ', ';
				$sql .= 'anulado = ' .Datos::objectToDB('S') . ', ';
				$sql .= 'fecha_baja = GETDATE() ';
				$sql .= 'WHERE cod_venta_cheques_temporal = ' . Datos::objectToDB($ventaChequesTemporal->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_venta_cheques_temporal), 0) + 1 FROM venta_cheques_temporal;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryZona(Zona $zona, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM zonas_geo ';
				$sql .= 'WHERE cod_zona = ' . Datos::objectToDB($zona->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO zonas_geo (';
				$sql .= 'anulado, ';
				$sql .= 'nombre, ';
				$sql .= 'descripcion ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($zona->nombre) . ', ';
				$sql .= Datos::objectToDB($zona->descripcion) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE zonas_geo SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($zona->anulado) . ', ';
				$sql .= 'nombre = ' . Datos::objectToDB($zona->nombre) . ', ';
				$sql .= 'descripcion = ' . Datos::objectToDB($zona->descripcion) . ' ';
				$sql .= 'WHERE cod_zona = ' . Datos::objectToDB($zona->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE zonas_geo SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_zona = ' . Datos::objectToDB($zona->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT IDENT_CURRENT(\'zonas_geo\') + IDENT_INCR(\'zonas_geo\');';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
	private function mapperQueryZonaTransporte(ZonaTransporte $zonaTransporte, $modo){
		$sql = '';
		try {
			if ($modo == Modos::select){
				$sql .= 'SELECT * ';
				$sql .= 'FROM zonas ';
				$sql .= 'WHERE cod_zona_nro = ' . Datos::objectToDB($zonaTransporte->id) . '; ';
			} elseif ($modo == Modos::insert) {
				$sql .= 'INSERT INTO zonas (';
				$sql .= 'cod_zona_nro, ';
				$sql .= 'cod_zona, ';
				$sql .= 'anulado, ';
				$sql .= 'descripcion, ';
				$sql .= 'denom_zona ';
				$sql .= ') VALUES (';
				$sql .= Datos::objectToDB($zonaTransporte->id) . ', ';
				$sql .= Datos::objectToDB(Funciones::padLeft($zonaTransporte->id, 2, '0')) . ', ';
				$sql .= Datos::objectToDB('N') . ', ';
				$sql .= Datos::objectToDB($zonaTransporte->descripcion) . ', ';
				$sql .= Datos::objectToDB($zonaTransporte->nombre) . ' ';
				$sql .= '); ';
			} elseif ($modo == Modos::update) {
				$sql .= 'UPDATE zonas SET ';
				$sql .= 'anulado = ' . Datos::objectToDB($zonaTransporte->anulado) . ', ';
				$sql .= 'descripcion = ' . Datos::objectToDB($zonaTransporte->descripcion) . ', ';
				$sql .= 'denom_zona = ' . Datos::objectToDB($zonaTransporte->nombre) . ' ';
				$sql .= 'WHERE cod_zona_nro = ' . Datos::objectToDB($zonaTransporte->id) . '; ';
			} elseif ($modo == Modos::delete) {
				$sql .= 'UPDATE zonas SET ';
				$sql .= 'anulado = ' . Datos::objectToDB('S') . ' ';
				$sql .= 'WHERE cod_zona_nro = ' . Datos::objectToDB($zonaTransporte->id) . '; ';
			} elseif ($modo == Modos::id) {
				$sql .= 'SELECT ISNULL(MAX(cod_zona_nro), 0) + 1 FROM zonas;';
			} else {
				throw new FactoryException('Modo incorrecto');
			}
			return $sql;
		} catch (Exception $ex) {
			throw $ex;
		}
	}
}

