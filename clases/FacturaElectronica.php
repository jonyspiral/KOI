<?php

class FacturaElectronica {
	//Nombre original del CRT: KoiFacturaElectronica__5f90f4cb0e48f6fa.crt
	public	$errorCae;

	private	$tipo_cbte;			//Es un número según el tipo de documento (ver método getTipoComprobante)
	private	$punto_vta;			//Para FE es el 2 ('0002')
	private	$concepto;			//Si es una factura de productos va 1, servicios 2, ambas 3
	private	$tipo_doc;			//El tipo de documento del cliente, si es DNI, CUIT, CUIL, etc. Para la mayoría será CUIT (factura 'A')
	private	$nro_doc;			//Es el número de documento del cliente (DNI, CUIT, CUIL, etc)
								//Para facturas B a consumidor final (menores a $1000) el campo nro_doc deberá ser cero (0) y el campo tipo_doc 99
	private	$cbt_desde;			//Es el número inicial de comprobante que se va a enviar. Se puede hacer 'ANTERIOR + 1'
	private	$cbt_hasta;			//Es el número final de comprobante que se va a enviar. Si no es por lote, es el mismo que el inicial
	private $_lastCBT;
	private	$imp_neto;			//Importe neto que luego será GRAVADO por el IVA
	private	$imp_iva;			//Importe TOTAL del IVA
	private	$imp_trib;			//Importe total de otros impuestos
	private	$imp_op_ex;			//Importe de operaciones exentas (?)
	private	$imp_total;			//Importe total de la factura
	private	$imp_tot_conc;		//Importe total de conceptos NO GRAVADOS por el IVA
	private	$moneda_id;			//Id de la moneda de la factura. Se usa getIdMoneda('PES')
	private	$moneda_ctz;		//Es la cotización de la moneda. Uso siempre 1
	private	$fecha_cbte;		//Fecha del comprobante en formato 'Ymd' (no puede ser mayor o menor a 5 días)
	private	$fecha_venc_pago;	//Fecha límite de vencimiento para el pago de la factura (Sólo si concepto es 2 o 3)
	private	$fecha_serv_desde;	//Fechas del período de servicios prestado (sólo si presta_serv = 1)
	private	$fecha_serv_hasta;	//Fechas del período de servicios prestado (sólo si presta_serv = 1)

	private	$WSAA;				//Objeto interface Web Service Autenticación y Autorización
	private	$WSFE;				//Objeto interface Web Service de Factura Electrónica
	private $pathCertificado;	//Certificado: certificado es el firmado por la AFIP
	private $pathKey;			//ClavePrivada: la clave privada usada para crear el certificado
	private $pathTa;			//Ticket de acceso: es el XML que guarda la info de acceso para ser reutilizada
	private	$token;				//Token de acceso para el WSN (reutilizable)
	private	$sign;				//Sign de acceso para el WSN (reutilizable)
	private $urlAutenticacion;	//Dirección del WebService de autenticación
	private $urlConexion;		//Dirección del WebService para conectar
	private	$errorBase;
	private $conectado;
	private $llenado;
	private $checkeado;
	private $creado;
	private $ivaAgregado;
	private	$cae;

	public function __construct() {
		/** @noinspection PhpUndefinedClassInspection */
		$this->WSAA = new COM('WSAA');
		/** @noinspection PhpUndefinedClassInspection */
		$this->WSFE = new COM('WSFEv1');
		if (Config::desarrollo()) {
			$this->pathCertificado = Config::pathBase . '\\includes\\fe\\desarrollo.crt';
			$this->pathKey = Config::pathBase . '\\includes\\fe\\desarrollo.key';
			$this->pathTa = Config::pathBase . '\\includes\\fe\\ta_desarrollo.xml';
			$this->urlAutenticacion = 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms';
			$this->urlConexion = 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL';
        } elseif (Config::encinitas()) {
            $this->pathCertificado = Config::pathBase . '\\includes\\fe\\KoiFacturaElectronicaNCNTS.crt';
            $this->pathKey = Config::pathBase . '\\includes\\fe\\KoiFacturaElectronicaNCNTS.key';
            $this->pathTa = Config::pathBase . '\\includes\\fe\\KoiTicketAccesoNCNTS.xml';
            $this->urlAutenticacion = 'https://wsaa.afip.gov.ar/ws/services/LoginCms';
            $this->urlConexion = 'https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL';
        } else {
			$this->pathCertificado = Config::pathBase . '\\includes\\fe\\KoiFacturaElectronica.crt';
			$this->pathKey = Config::pathBase . '\\includes\\fe\\KoiFacturaElectronica.key';
			$this->pathTa = Config::pathBase . '\\includes\\fe\\KoiTicketAcceso.xml';
			$this->urlAutenticacion = 'https://wsaa.afip.gov.ar/ws/services/LoginCms';
			$this->urlConexion = 'https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL';
		}
		$this->errorBase = 'Ocurrió un error al conectar con el Web Service de Facturación';
		$this->conectado = false;
		$this->llenado = false;
		$this->checkeado = false;
		$this->creado = false;
		$this->ivaAgregado = false;
	}

	public function llenar($documento) {
		$this->establecerConexion();
		if (!$this->llenado)
			$this->llenarDatos($documento);
		if (!$this->checkeado)
			$this->checkDatos();
		if (!$this->creado)
			$this->crearFactura();
		if (!$this->ivaAgregado)
			$this->agregarIva($documento);

		if ($documento->tipoDocumento != 'FAC') {

			$fechaDesde = Funciones::hoy('Y-m-d');
			$where = 'anulado = ' . Datos::objectToDB('N') . ' AND fecha_desde <= dbo.toDate(' . Datos::objectToDB($fechaDesde) . ') AND fecha_hasta >= dbo.toDate(' . Datos::objectToDB($fechaDesde) . ')';
			$ejercicios = Factory::getInstance()->getListObject('EjercicioContable', $where);
			$fechaDesde = '01/01/' . date('Y');
			if (count($ejercicios) > 0) {
				$fechaDesde = $ejercicios[0]->fechaDesde;
			}
			$fechaDesde = Funciones::getDate('Ymd', strtotime($fechaDesde));

			try {
				$this->WSFE->AgregarPeriodoComprobantesAsociados($fechaDesde, $this->fecha_cbte);
			} catch (\Exception $e) {
				throw new FactoryExceptionCustomException($e->getMessage());
			}
		}
	}

	public function getCae() {
		if (!isset($this->cae)) {
			if (!($this->conectado && $this->llenado && $this->checkeado && $this->creado && $this->ivaAgregado))
				throw new FactoryExceptionCustomException('Debe llenar la factura electrónica antes de pedir el CAE (->llenar($documento))');
			$this->obtenerCae();
			$this->errorCae = $this->verificarCae();
		}
		return $this->cae;
	}

	public function getVencimientoAutorizacion() {
		//Devuelve la fecha de vencimiento de la autorización
		if (!isset($this->cae))
			throw new FactoryExceptionCustomException('Debe obtenerse el CAE antes de pedir el vencimiento de la autorización');
		/** @noinspection PhpUndefinedFieldInspection */
		return $this->WSFE->Vencimiento;
	}

	public function getNumeroComprobante() {
		if (!isset($this->cae))
			throw new FactoryExceptionCustomException('Debe obtenerse el CAE antes de pedir el número de comprobante');
		return $this->cbt_desde;
	}

	/*
	 * Mátodos privados para el funcionamiento
	 */

	private function establecerConexion() {
		if (!$this->conectado) {
			$this->autenticar();
			$this->conectar();
			$this->comprobarEstado();
			$this->conectado = true;
		}
	}

	private function autenticar() {
		if (!$this->conectado) {
			if (file_exists($this->pathTa) && $ta = file_get_contents($this->pathTa)) {
				$ok = $this->WSAA->AnalizarXml($ta); //Esto parsea el $ta y lo mete en el WSAA. Si devuelve TRUE es porque está OK
				if (!$this->WSAA->Expirado()) {
					$this->token = $this->WSAA->ObtenerTagXml("token");
					$this->sign = $this->WSAA->ObtenerTagXml("sign");
				}
			}
			if (!$this->token || !$this->sign) {
				$tra = $this->WSAA->CreateTRA(); //Genero un Ticket de Requerimiento de Acceso (TRA)
				$crtContent = file_get_contents($this->pathCertificado);
				$keyContent = file_get_contents($this->pathKey);
				$cms = $this->WSAA->SignTRA($tra, $crtContent, $keyContent); //Genero el mensaje firmado (CMS)

				try {
					$ta = $this->WSAA->CallWSAA($cms, $this->urlAutenticacion); //Llamo al web service para autenticar
				} catch (Exception $ex) {
					// Estas 3 variables nos permiten ver el error, el request y el response
					$err = $this->WSAA->Excepcion;
					//$req = $this->WSAA->XmlRequest;
					//$res = $this->WSAA->XmlResponse;
                    throw new Exception('Error en la autenticación WSAA: ' . $err . ' - ' . $ex->getMessage());
				}
				if ($ta) {
					file_put_contents($this->pathTa, $ta);
				}

				/** @noinspection PhpUndefinedFieldInspection */
				$this->token = $this->WSAA->Token;
				/** @noinspection PhpUndefinedFieldInspection */
				$this->sign = $this->WSAA->Sign;
			}
		}
	}

	private function conectar() {
		if (!$this->conectado) {
			//Seteo el token y sign de autorización (pasos previos) Y CUIT del emisor
			/** @noinspection PhpUndefinedFieldInspection */
			$this->WSFE->Token = $this->token;
			/** @noinspection PhpUndefinedFieldInspection */
			$this->WSFE->Sign = $this->sign;
			/** @noinspection PhpUndefinedFieldInspection */
			$this->WSFE->Cuit = Config::encinitas() ? Config::CUIT_NCNTS : Config::CUIT_SPIRAL;

			//Conecto con el Web Service de Facturación
			if (!$this->WSFE->Conectar('', $this->urlConexion))
				throw new FactoryExceptionCustomException($this->errorBase . ': WSFE->Conectar');
		}
	}

	private function comprobarEstado() {
		if (!$this->conectado) {
			//Llamo a un servicio nulo, para obtener el estado del servidor (opcional)
			//Si alguno está caido, devuelvo el error
			$this->WSFE->Dummy();
			/** @noinspection PhpUndefinedFieldInspection */
			if ($this->WSFE->AppServerStatus != 'OK')
				throw new FactoryExceptionCustomException($this->errorBase . ': WSFE->AppServerStatus');
			/** @noinspection PhpUndefinedFieldInspection */
			if ($this->WSFE->DbServerStatus != 'OK')
				throw new FactoryExceptionCustomException($this->errorBase . ': WSFE->DbServerStatus');
			/** @noinspection PhpUndefinedFieldInspection */
			if ($this->WSFE->AuthServerStatus != 'OK')
				throw new FactoryExceptionCustomException($this->errorBase . ': WSFE->AuthServerStatus');
		}
	}

	private function llenarDatos(Documento $documento) {
		$this->punto_vta = $documento->puntoDeVenta;
		$this->tipo_cbte = $this->getTipoComprobante($documento->tipoDocumento, $documento->letra);
		if (false) { //ACÁ VA UNA CONDICIÓN PARA SABER CUÁNDO ES FACTURA DE SERVICIO Y CUÁNDO NO!
			$this->concepto = 2;
			//Fechas del peróodo del servicio facturado (solo si presta_serv = 1)
			$this->fecha_serv_desde = Funciones::hoy('Ymd');
			$this->fecha_serv_hasta = Funciones::hoy('Ymd');
			$this->fecha_venc_pago = Funciones::hoy('Ymd');
		} else {
			$this->concepto = 1;
		}
		$this->tipo_doc = $this->getTipoDNI(isset($documento->cliente->cuit) ? 'CUIT' : (isset($documento->cliente->dni) ? 'DNI' : 'CONSUMIDORFINAL'));
		$this->nro_doc = (isset($documento->cliente->cuit) ? $this->validarCuit($documento->cliente->cuit) : $documento->cliente->dni);
		$this->cbt_desde = $this->getLastCBT() + 1;
		$this->cbt_hasta = $this->cbt_desde;
		$this->fecha_cbte = Funciones::hoy('Ymd');
		//Es el importe neto (gravado, por eso le resto lo NoGravado) menos los descuentos (porque no tengo otro campo para mandar los desc)
		$nuevoImporteNeto = $documento->importeNeto - $documento->importeNoGravado - (($documento->importeNeto - $documento->importeNoGravado) * $documento->descuentoComercialPorc / 100) - $documento->descuentoDespachoImporte;
		$this->imp_neto = Funciones::toFloat($nuevoImporteNeto, 2);
		$this->imp_iva = Funciones::toFloat($documento->ivaImporte1 + $documento->ivaImporte2 + $documento->ivaImporte3, 2);
		$this->imp_trib = 0;
		$this->moneda_id = $this->getIdMoneda('PES');
		$this->moneda_ctz = 1;
		$this->imp_op_ex = 0; //No sé qué diferencia tiene con NO GRAVADOS
		$this->imp_tot_conc = Funciones::toFloat($documento->importeNoGravado - (($documento->importeNoGravado) * $documento->descuentoComercialPorc / 100), 2);
		$this->imp_total = Funciones::toFloat($documento->importeTotal, 2);

		//Algunos valores por defecto (hardcodeados)
		/** @noinspection PhpUndefinedFieldInspection */
		$this->impto_liq_rni = 0;
		$this->imp_op_ex = 0;

		$this->llenado = true;
	}

	private function checkDatos() {
		//Compruebo si los datos para crear la factura son correctos y suficientes
		$array = array(
			'tipo_cbte', 'punto_vta', 'concepto', 'tipo_doc'/*, 'nro_doc'*/, 'cbt_desde', 'cbt_hasta', 'imp_neto',
			'imp_iva', 'imp_trib', 'imp_op_ex', 'imp_total', 'imp_tot_conc', 'moneda_id', 'moneda_ctz', 'fecha_cbte'
		);
		foreach ($array as $attr) {
			if (!isset($this->$attr))
				throw new FactoryException('No están completos todos los datos para obtener el CAE (' . $attr . ')');
		}

		$this->checkeado = true;
	}

	private function crearFactura() {
		$this->WSFE->CrearFactura(
			$this->concepto, $this->tipo_doc, $this->nro_doc, $this->tipo_cbte, $this->punto_vta,
			$this->cbt_desde, $this->cbt_hasta, $this->imp_total, $this->imp_tot_conc, $this->imp_neto,
			$this->imp_iva, $this->imp_trib, $this->imp_op_ex, $this->fecha_cbte, $this->fecha_venc_pago,
			$this->fecha_serv_desde, $this->fecha_serv_hasta, $this->moneda_id, $this->moneda_ctz
		);

		$this->creado = true;
	}

	private function agregarIva($documento) {
		//Primero genero 3 objetos de IVA
		$ivas = array();
		$sumBaseImp = 0;
		for ($i = 1; $i <= 3; $i++) {
			$importe = 'ivaImporte' . $i;
			if (Funciones::toFloat($documento->$importe) > 0) {
				$porcentaje = 'ivaPorcentaje' . $i;
				$id = $this->getIdIva(Funciones::toString($documento->$porcentaje));
				$base_imp = Funciones::toFloat($documento->$importe / ($documento->$porcentaje / 100), 2);
				$sumBaseImp += $base_imp;
				$importe = Funciones::toFloat($documento->$importe, 2);
				$ivas[] = array('id' => $id, 'base_imp' => $base_imp, 'importe' => $importe, 'porcentaje' => $documento->$porcentaje);
			}
		}
		//Me fijo si hay algún importe de IVA
		if (count($ivas) > 0) {
			//CHequeo que los decimales están bien (la sumatoria de las base_imp debe ser igual al importe_neto)
			$diferencia  = $this->imp_neto - $sumBaseImp;
			if ($diferencia != 0) {
				$ivas[0]['base_imp'] += $diferencia;
				$ivas[0]['importe'] = Funciones::toFloat($ivas[0]['base_imp'] * ($ivas[0]['porcentaje'] / 100), 2);
			}
			//Agrego por separado las alícuotas de IVA
			foreach ($ivas as $iva) {
				$this->WSFE->AgregarIva($iva['id'], $iva['base_imp'], $iva['importe']);
			}
		}
		$this->ivaAgregado = true;
	}

	private function obtenerCae() {
		//Llamo al WebService y obtengo el CAE
		$this->cae = $this->WSFE->CAESolicitar();
	}

	private function verificarCae() {
		//Verifico que no haya rechazo o dado una advertencia al generar el CAE
		/** @noinspection PhpUndefinedFieldInspection */
		$msg = ($this->WSFE->ErrMsg == '' ? $this->WSFE->Obs : $this->WSFE->ErrMsg);
		if ($this->cae == '') {
			throw new FactoryExceptionCustomException('No se asignó CAE (rechazado). Motivos: ' . $msg);
		} elseif (($this->cae != '') && ($msg != '')) {
			return 'Se asignó CAE pero con advertencias. Motivos: ' . $msg;
		}
		return false;
	}

	private function getLastCBT() {
		//Recupero último número de comprobante para un tipo de comprobante y un punto de venta (opcional)
		if (!isset($this->_lastCBT)) {
			if (!isset($this->punto_vta) || !isset($this->tipo_cbte))
				throw new FactoryExceptionCustomException('Para obtener el último número de comprobante debe setear el punto de venta y el tipo de comprobante (getLastCBT)');
			$this->_lastCBT = $this->WSFE->CompUltimoAutorizado($this->tipo_cbte, $this->punto_vta);
		}
		return $this->_lastCBT;
	}

	private function validarCuit($cuit) {
		if (Funciones::toInt($cuit) <= 0 || strlen($cuit) != 11)
			throw new FactoryExceptionCustomException('No se puede obtener el CAE ya que el CUIT del cliente es incorrecto');
		return $cuit;
	}

	/** @noinspection PhpInconsistentReturnPointsInspection */
	private function getTipoComprobante($tipoDocum, $letra) {
		/* tipo_cbte
			1 Facturas A
			2 Notas de Débito A
			3 Notas de Crédito A
			4 Recibos A
			5 Notas de Venta al contado A
			6 Facturas B
			7 Notas de Débito B
			8 Notas de Crédito B
			9 Recibos B
			10 Notas de Venta al contado B
			39 Otros comprobantes A que cumplan con la R.G. Nº 3419
			40 Otros comprobantes B que cumplan con la R.G. Nº 3419
			60 Cuenta de Venta y Líquido producto A
			61 Cuenta de Venta y Líquido producto B
			63 Liquidación A
			64 Liquidación B
		*/
		switch ($tipoDocum) {
			case 'FAC':
				switch ($letra) {
					case 'A': return 1;
					case 'B': return 6;
				}
				break;
			case 'NDB':
				switch ($letra) {
					case 'A': return 2;
					case 'B': return 7;
				}
				break;
			case 'NCR':
				switch ($letra) {
					case 'A': return 3;
					case 'B': return 8;
				}
				break;
			default:
				return 1;
				break;
		}
	}

	private function getTipoDNI($id) {
		/* tipo_doc
			80 - CUIT
			96 - DNI
			86 - CUIL
			87 - CDI
			89 - LE
			90 - LC
			92 - en trámite
			94 - Pasaporte
			99 - Consumidor final (nro_doc = 0)
		*/
		switch ($id) {
			case 'CUIT': return 80;
			case 'DNI': return 96;
			case 'CONSUMIDORFINAL': return 99;
			case 'CUIL': return 86;
			case 'PASAPORTE': return 94;
			default: return 80;
		}
	}

	private function getIdIva($porc) {
		/*
			3	0%
			4	10.5%
			5	21%
			6	27%
		*/
		switch ($porc) {
			case '0': return 3;
			case '10.5': return 4;
			case '21': return 5;
			case '27': return 6;
			default: return 5;
		}
	}

	public function getIdMoneda($id) {
		/*
			PES	Pesos Argentinos
			DOL	Dólar Estadounidense
			002	Dólar Libre EEUU
			010	Pesos Mejicanos
			011	Pesos Uruguayos
			021	Libra Esterlina
			023	Bolívar Venezolano
			029	Guaraní
			031	Peso Boliviano
			032	Peso Colombiano
			033	Peso Chileno
			060	Euro
		*/
		switch ($id) {
			case 'PES': return 'PES';
			case 'DOL': return 'DOL';
			case 'EUR': return 060;
			default: return 'PES';
		}
	}

	//GETS y SETS
}

?>