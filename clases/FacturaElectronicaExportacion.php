<?php

/*
 * ESTE ARCHIVO NO VA NI SE USAAAAAAAAAAA
 * ESTÁ MAL HECHO, LE FALTA TERMINAR
 */


class FacturaElectronicaExportacion {
	const	CUIT_SPIRAL = '33710051459';

	public	$tipo_cbte;			//Es un número según el tipo de documento (ver método getTipoComprobante)
	public	$punto_vta;			//Siempre 1
	public	$id;				//Es un campo de identificación del documento. Se puede hacer 'ANTERIOR + 1'. Debe ser único, aunque no necesariamente creciente y continuo
	private $_lastId;
	public	$cbte_nro;			//Es el número de comprobante que se va a enviar. Se puede hacer 'ANTERIOR + 1'
	public	$fecha_cbte;		//Fecha del comprobante en formato 'Ymd' (no puede ser mayor o menor a 5 días)
	public	$imp_total;			//Importe total de la factura
	public	$tipo_expo;			//Es el tipo de exportación. Expo de bienes es 1 (FAC o NCR de artículos), de servicios 2 (NCRs o NDBs), otros 4
	public	$permiso_existente;	//'S' o 'N' (si se dispone o no del despacho  de exportación) o '' si Tipo_cbte es 20 (NDB) o 21 (NCR) o si Tipo_cbte es 19 (FAC) y Tipo_expo es 2 (sercivios) o 4 (otros)
	public	$dst_cmp;			//País de destino. Se usa getPaisDestino('TF') ||| TF = Tierra del Fuego, es el más común
	public	$cliente;			//Nombre del cliente, razón social, lo que sea (un string)
	public	$domicilio_cliente;	//Domicilio del cliente (un string)
	public	$id_impositivo;		//Es el CUIT del cliente
	public	$obs_comerciales;	//Observaciones comerciales
	public	$obs;				//Observaciones generales
	public	$forma_pago;		//Es un string diciendo la forma de pago (Efectivo, etc)
	public	$incoterms;			//FCA. Solamente es obligatorio si es una factura (Cbte_Tipo = 19) y los conceptos son bienes (Tipo_expo = 1). Se usa getIncoterms()
	public	$incoterms_ds;		//Información complementaria del incoterm (string, opcional)
	public	$idioma_cbte;		//Idioma del comprobante. Se usa getIdiomaCbt()
	
	//Hardcodeados
	public	$cuit_pais_cliente;	//Es el CUIT del país del cliente. No se usa, se usa sólo el id_impositivo (CUIT)
	public	$moneda_id;			//ID de la moneda que se usa en la factura. Se usa getMonedaId('PES')
	public	$moneda_ctz;		//Es la cotización de la moneda. Hardcodeo, usamos siempre 1 por ahora
	
	public	$items;				//Array asociativo con los items de la factura
	/*
		codigo = "PRO1"				//Código del artículo
		ds = "Descripción del ART"	//Descripción del artículo
		qty = 2						//Cantidad de artículos
		precio = "130.00"			//Precio unitario
		umed = 1					//Unidad de medida. Se usa getUnidadMedida
		imp_total = "250.00"		//Importe final del artículo (cant x precio)
		bonif = "10.00"				//Bonificación final por esa línea
	*/

	private	$WSAA;				//Objeto interface Web Service Autenticación y Autorización
	private	$WSFEX;				//Objeto interface Web Service de Factura Electrónica Exportacion
	private $pathCertificado;	//Certificado: certificado es el firmado por la AFIP
	private $pathKey;			//ClavePrivada: la clave privada usada para crear el certificado
	private	$errorBase;
	private	$cache;
	private $conectado;
	private $llenado;
	private	$cae;

	public function __construct() {
		/** @noinspection PhpUndefinedClassInspection */
		$this->WSAA = new COM('WSAA');
		/** @noinspection PhpUndefinedClassInspection */
		$this->WSFEX = new COM('WSFEXv1');
		$this->pathCertificado = Config::pathBase . '\\includes\\fe\\desarrollo.crt';
		$this->pathKey = Config::pathBase . '\\includes\\fe\\desarrollo.key';
		$this->errorBase = 'Ocurrió un error al conectar con el Web Service de Facturación Exportación';

		//Valores por defecto
		$this->conectado = false;
		$this->llenado = false;

		//Valores hardcodeados
		$this->cuit_pais_cliente = '';
		$this->moneda_id = $this->getMonedaId('PES');
		$this->moneda_ctz = 1;
	}

	public function llenar(Documento $documento) {
		if (!$this->conectado)
			$this->establecerConexion();

		$this->id = $this->getLastId();
		$this->punto_vta = 1;
		$this->tipo_cbte = $documento->getTipoComprobante($documento->tipoDocumento);
		$this->cbte_nro = $this->getLastCBT() + 1;
		$this->fecha_cbte = Funciones::hoy('Ymd');
		$this->imp_total = $documento->importeTotal;
		$this->tipo_expo = 1;
		$this->permiso_existente = 'N';
		//$this->dst_cmp = $this->getPaisDestino($documento->cliente->sucursalFisica->direccionProvincia);
		$this->cliente = $documento->cliente->razonSocial;
		$this->domicilio_cliente = $documento->cliente->direccion;
		$this->id_impositivo = $documento->cliente->cuit;
		$this->obs_comerciales = '';
		$this->obs = '';
		$this->forma_pago = 'Efectivo';
		$this->incoterms = $this->getIncoterms('FCA');
		$this->incoterms_ds = '';
		$this->idioma_cbte = $this->getIdiomaCbt('ESP');
		
		//Lleno los items
		
	}

	public function getCae() {
		if (!$this->llenado)
			$this->llenar();

		$this->crearFactura();
		$this->agregarItems();
		$this->agregarPermiso();
		$this->agregarCbtAsociado();
		
		
		//Llamo al WebService de Autorización para obtener el CAE
		$this->cae = $this->WSFEX->Authorize($this->id);
		
		//Verifico que no haya rechazo o dado una advertencia al generar el CAE
		if ($this->cae == '') {
			throw new Exception($this->errorBase . ': getCae()');
		} /** @noinspection PhpUndefinedFieldInspection */ elseif ($cae == 'NULL' || $this->WSFEX->Resultado != 'A') {
			throw new Exception('No se asignó CAE (Rechazado). Motivos: ' . $this->getMotivo());
		} /** @noinspection PhpUndefinedFieldInspection */ elseif ($this->WSFEX->Motivo != 'NULL' && $this->WSFEX->Motivo != '00') {
			throw new FactoryExceptionCustomException('Se asignó CAE pero con advertencias. Motivos: ' . $this->getMotivo());
		}

		return $this->cae;
	}

	public function getVencimientoAutorizacion() {
		//Devuelve la fecha de vencimiento de la autorización
		if (isset($this->cae))
			/** @noinspection PhpUndefinedFieldInspection */
			return $this->WSFEX->Vencimiento;
		return '';
	}

	private function getLastId() {
		//Recupera último número de secuencia ID
		if (!$this->conectado)
			$this->establecerConexion();
		if (!isset($this->_lastId))
			$this->_lastId = $this->WSFEX->GetLastID();
		return $this->_lastId;
	}

	public function getLastCBT() {
		//Recupero último número de comprobante para un tipo de comprobante y un punto de venta (opcional)
		if (!$conectado)
			$this->establecerConexion();
		if (!isset($this->punto_vta) || !isset($this->tipo_cbte))
			throw new Exception('Para obtener el último número de comprobante debe setear el punto de venta y el tipo de comprobante (getLastCBT)');
		if (!isset($this->_lastCBT))
			$this->_lastCBT = $this->WSFEX->RecuperaLastCMP($this->punto_vta, $this->tipo_cbte);
		return $this->_lastCBT;
	}

	private function establecerConexion() {
		if (!$conectado) {
			$this->autenticar();
			$this->conectar();
			$this->comprobarEstado();
		}
	}

	private function autenticar() {
		if (!$conectado) {
			//Genero un Ticket de Requerimiento de Acceso (TRA)
			$tra = $this->WSAA->CreateTRA('wsfex');
			//Genero el mensaje firmado (CMS)
			$cms = $this->WSAA->SignTRA($tra, $this->pathCertificado, $this->pathKey);
			//Llamo al web service para autenticar
			$this->WSAA->CallWSAA($cms); //homologación/desarrollo
			//$ta = $this->WSAA->CallWSAA($cms, "https://wsaa.afip.gov.ar/ws/services/LoginCms?wsdl") //producción
		}
	}

	private function conectar() {
		if (!$conectado) {
			//Seteo el token y sign de autorización (pasos previos) Y CUIT del emisor
			/** @noinspection PhpUndefinedFieldInspection */
			$this->WSFEX->Token = $this->WSAA->Token;
			/** @noinspection PhpUndefinedFieldInspection */
			$this->WSFEX->Sign = $this->WSAA->Sign;
			/** @noinspection PhpUndefinedFieldInspection */
			$this->WSFEX->Cuit = self::CUIT_SPIRAL;

			//Conecto con el Web Service de Facturación
			$ok = $this->WSFEX->Conectar($this->cache, 'https://wswhomo.afip.gov.ar/WSFEXv1/service.asmx?WSDL'); //desarrollo
			//$ok = $this->WSFEX->Conectar("https://wsw.afip.gov.ar/WSFEXv1/service.asmx?WSDL"); //producción
	
			//Si ocurrió un error en la conexión, tiro exception
			if (!$ok)
				throw new Exception($this->errorBase . ': WSFE->Conectar');
		}
	}

	private function comprobarEstado() {
		if (!$conectado) {
			//Llamo a un servicio nulo, para obtener el estado del servidor (opcional)
			//Si alguno está caido, devuelvo el error
			$this->WSFEX->Dummy();
			/** @noinspection PhpUndefinedFieldInspection */
			if ($this->WSFEX->AppServerStatus != 'OK')
				throw new Exception($this->errorBase . ': WSFE->AppServerStatus');
			/** @noinspection PhpUndefinedFieldInspection */
			if ($this->WSFEX->DbServerStatus != 'OK')
				throw new Exception($this->errorBase . ': WSFE->DbServerStatus');
			/** @noinspection PhpUndefinedFieldInspection */
			if ($this->WSFEX->AuthServerStatus != 'OK')
				throw new Exception($this->errorBase . ': WSFE->AuthServerStatus');
		}
	}

	private function crearFactura() {
		//Compruebo si los datos para crear la factura son correctos y suficientes
		$this->checkDatos();
		//Creo una factura (internamente, no se llama al WebService):
		$this->WSFEX->CrearFactura(
			$this->tipo_cbte, $this->punto_vta, $this->cbte_nro, $this->fecha_cbte,
			$this->imp_total, $this->tipo_expo, $this->permiso_existente, $this->dst_cmp,
			$this->cliente, $this->cuit_pais_cliente, $this->domicilio_cliente,
			$this->id_impositivo, $this->moneda_id, $this->moneda_ctz, $this->obs_comerciales,
			$this->obs, $this->forma_pago, $this->incoterms, $this->idioma_cbte, $this->incoterms_ds
		);
	}

	private function checkDatos() {
		//Acá compruebo que todos los datos necesarios estén asignados
		$array = array(
				'tipo_cbte', 'punto_vta', 'id', 'presta_serv', 'tipo_doc', 'nro_doc',
				'cbt_desde', 'cbt_hasta', 'imp_neto', 'impto_liq',
				'imp_total', 'imp_tot_conc', 'fecha_cbte', 'fecha_venc_pago'
			);
		foreach ($array as $attr) {
			if (!isset($this->$attr))
				throw new FactoryException('No están completos todos los datos para obtener el CAE (' . $attr . ')');
		}
	}

	private function agregarItems() {
		foreach($items as $item) {
			$this->WSFEX->AgregarItem(
				$item['codigo'],
				$item['ds'],
				$item['qty'],
				$item['umed'],
				$item['precio'],
				$item['imp_total'],
				$item['bonif']
			);
		}
	}

	private function agregarPermiso() {
		//?? Agrego un permiso (ver manual para el desarrollador)
		if (false) {
		//if ($this->permiso_existente == 'S') {
			$id = '99999AAXX999999A';
			$dst = 225; //País de destino de la mercaderia
			$this->WSFEX->AgregarPermiso($id, $dst);
		}
	}

	private function agregarCbtAsociado() {
		//?? Agrego un comprobante asociado (ver manual para el desarrollador)
		if (false) {
		//if ($this->tipo_cbte != $this->getTipoComprobante('FAC')) { //Así estaba en la documentación: $this->tipo_cbte != 19
			$tipo_cbte_asoc = 19;
			$punto_vta_asoc = 2;
			$cbte_nro_asoc = 1;
			$cuit_asoc = "20111111111"; //CUIT Asociado
			$this->WSFEX->AgregarCmpAsoc($tipo_cbte_asoc, $punto_vta_asoc, $cbte_nro_asoc, $cuit_asoc);
		}
	}

	private function getMotivo() {
		/** @noinspection PhpUndefinedFieldInspection */
		switch ($this->WSFEX->Motivo) {
			case '00': case 'NULL': return 'No hay error (solo como referencia)';
			case '01': return 'CUIT informada no es R.I.';
			case '02': return 'CUIT no autorizada a facturar electrónicamente';
			case '03': return 'CUIT registra inconvenientes con domicilio fiscal';
			case '04': return 'Punto de venta no se encuentra declarado';
			case '05': return 'Fecha del comprobante incorrecta';
			case '06': return 'CUIT no puede emitir comprobantes clase A';
			case '07': return 'Para comprobantes clase A debe indicar CUIT';
			case '08': return 'CUIT informada es inválida';
			case '09': return 'CUIT informada no existe en el padrón';
			case '10': return 'CUIT informada no corresponde a un R.I.';
			case '11': return 'El Nº de comprobante no es correlativo o la fecha no corresponde';
			case '12': return 'El rango informado se encuentra autorizado';
			case '13': return 'CUIT indicada se encuentra comprendida en el régimen establecido por la resolución general N° 2177 y/o en el título I de la resolución general N° 1361 art. 24 de la RG N° 2177-';
			default: return 'Hubo un warning que no se pudo parsear';
		}
	}

	public function getTipoComprobante($tipoDocum) {
		/* tipo_cbte
			19	Facturas de Exportación
			20	Nota de Débito por Operaciones con el Exterior
			21	Nota de Crédito por Operaciones con el Exterior
			88	Remito Electrónico (solo para comprobantes asociados, Nuevo WSFEXv1! )
			89	Resumen de Datos (solo para comprobantes asociados, Nuevo WSFEXv1! )
		*/
		switch ($tipoDocum) {
			case 'FAC': return 19;
			case 'NDB': return 20;
			case 'NCR': return 21;
			default: return 19;
		}
	}

	public function getTipoDNI($id) {
		/* tipo_doc
			80 - CUIT
			86 - CUIL
			87 - CDI
			89 - LE
			90 - LC
			91 - CI extranjera
			92 - en trámite
			93 - Acta nacimiento
			95 - CI Bs. As. RNP
			96 - DNI
			94 - Pasaporte
			00 - CI Policía Federal
			01 - CI Buenos Aires
			07 - CI Mendoza
			08 - CI La Rioja
			09 - CI Salta
			10 - CI San Juan
			11 - CI San Luis
			12 - CI Santa Fe
			13 - CI Santiago del Estero
			14 - CI Tucumán
			16 - CI Chaco
			17 - CI Chubut
			18 - CI Formosa
			19 - CI Misiones
			20 - CI Neuquén
		*/
		switch ($id) {
			case 'CUIT': return 80;
			case 'CUIL': return 86;
			case 'DNI': return 96;
			case 'PASAPORTE': return 94;
			default: return 80;
		}
	}

	public function getPaisDestino($provincia) {
		/*
			200	ARGENTINA
			202	BOLIVIA
			203	BRASIL
			205	COLOMBIA
			206	COSTA RICA
			208	CHILE
			210	ECUADOR
			212	ESTADOS UNIDOS
			218	MEXICO
			221	PARAGUAY
			222	PERU
			225	URUGUAY
			250	AAE Tierra del Fuego - ARGENTINA
		*/
		if ($provincia->idPais == 'AR' && $provincia->id != 'V') //Hardcodeo para tierra del fuego
			return 200;
		return 250;
		/*
		switch ($id) {
			case 'ARGENTINA': return 200;
			case 'TF': return 250;
			default: return 250;
		}
		*/
	}

	public function getMonedaId($id) {
		/*
			PES	Pesos Argentinos
			DOL	Dólar Estadounidense
			002	Dólar Libre EEUU
			010	Pesos Mejicanos
			011	Pesos Uruguayos
			021	Libra Esterlina
			023	Bolívar Venezolano
			029	Güaraní
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

	public function getIncoterms($id) {
		/*
			EXW	EXW
			FCA	FCA
			FAS	FAS
			FOB	FOB
			CFR	CFR
			CIF	CIF
			CPT	CPT
			CIP	CIP
			DAF	DAF
			DES	DES
			DEQ	DEQ
			DDU	DDU
			DDP	DDP
			DAP	DAP
			DAT	DAT
		*/
		switch ($id) {
			case 'FCA': return 'FCA';
			case 'EXW': return 'EXW';
			case 'FOB': return 'FOB';
			case 'CIF': return 'CIF';
			default: return 'FCA';
		}
	}

	public function getIdiomaCbt($id) {
		/*
			1	Español
			2	Inglés
			3	Portugués
		*/
		switch ($id) {
			case 'ESP': return 1;
			case 'ING': return 2;
			case 'POR': return 3;
			default: return 'ESP';
		}
	}

	//GETS y SETS
}

?>