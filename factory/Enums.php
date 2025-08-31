<?php

abstract class Modos {
	const select = 0;
    const insert = 1;
    const update = 2;
    const delete = 3;
    const id = 4;
}
abstract class TiposAutorizacion {
	const notaDePedido = 1;
	const altaCliente = 2;
	const altaProveedor = 3;
}
abstract class TiposPersonal {
	const personal = 'P';
    const operador = 'O';
    const vendedor = 'V';
}
abstract class TiposContacto {
	const cliente = 'C';
    const proveedor = 'R';
    const otro = 'X';
}
abstract class TiposUsuario {
	const contacto = 'C';
    const vendedor = 'V';
    const personal = 'P';
}
abstract class TiposDocumento {
	const recibo = 'REC';
    const factura = 'FAC';
    const notaDeCredito = 'NCR';
    const notaDeDebito = 'NDB';
}
abstract class TiposDocumento2 {
	const ncrAjuste = 'NCA';
	const ncrComercial = 'NCC';
	const ncrDevolucion = 'NCD';
	const ncrFinanciera = 'NCF';
	const ncrFactura = 'NCT';
	const ncrNotaDeDebito = 'NCB';
	const ndbAjuste = 'NDA';
	const ndbChequeRechazado = 'NDR';
	const ndbComercial = 'NDC';
	const ndbFinanciera = 'NDF';
	const ndbNotaDeCredito = 'NDT';

	public static function getTipoDocumento2($tipoDocumento2) {
		switch ($tipoDocumento2) {
			case self::ncrAjuste: return self::ncrAjuste;
			case self::ncrComercial: return self::ncrComercial;
			case self::ncrDevolucion: return self::ncrDevolucion;
			case self::ncrFinanciera: return self::ncrFinanciera;
			case self::ndbAjuste: return self::ndbAjuste;
			case self::ndbChequeRechazado: return self::ndbChequeRechazado;
			case self::ndbComercial: return self::ndbComercial;
			case self::ndbFinanciera: return self::ndbFinanciera;
			default: return false;
		}
	}
}
abstract class CodigosComprobante {
	public static function getCodigoComprobante($tipoCbte, $letra) {
		switch ($tipoCbte) {
			case 'FAC':
				switch ($letra) {
					case 'A': return '01';
					case 'B': return '06';
					case 'E': return '19';
				}
				break;
			case 'NDB':
				switch ($letra) {
					case 'A': return '02';
					case 'B': return '07';
					case 'E': return '20';
				}
				break;
			case 'NCR':
				switch ($letra) {
					case 'A': return '03';
					case 'B': return '08';
					case 'E': return '21';
				}
				break;
			case 'REC':
				switch ($letra) {
					case 'A': return '04';
					case 'B': return '09';
				}
				break;
			case 'REM':
				switch ($letra) {
					case 'R': return '91';
				}
				break;
		}
		return '01';
	}
}
abstract class TiposOperador {
	const fasonier = 'F';
    const personal = 'P';
    const vendedor = 'V';
}
abstract class TiposOperacionStock {
	const ajusteStock = 1;
	const confirmacionStock = 2;
	const remito = 3;
	const notaDeCredito = 4;
	const movimientoAlmacen = 5;
	const garantia = 6;
	const devolucionACliente = 7;
}
abstract class TiposOperacionStockMP {
	const ajusteStock = 1;
	const consumo = 2;
	const remito = 3;
	const movimientoAlmacen = 4;
}
abstract class TiposMovimientoStock {
	const inicial = 'INI';
	const positivo = 'POS';
	const negativo = 'NEG';
}
abstract class AreasEmpresa {
    const casaCentral = 15;
}
abstract class PermisosUsuarioPorCaja {
	const verCaja = 1;
	const crearSubcajas = 2;
	const cerrarCaja = 3;
	const editarUsuarios = 4;
	const recibo = 10;
	const ordenDePago = 11;
	const ajuste = 12;
	const transferenciaInterna = 13;
	const transferenciaBancariaOperacion = 14;
	const ingresoChequePropio = 15;
	const cobroChequesVentanilla = 16;
	const ventaCheques = 17;
	const depositoBancario = 18;
	const rechazoCheque = 19;
	const rendicionGastos = 20;
	const aporteSocio = 21;
	const acreditarCheque = 22;
	const debitarCheque = 23;
	const retiroSocio = 24;
	const prestamo = 25;
	const reingresoChequeCartera = 26;
}
abstract class TiposTransferenciaBase {
	const recibo = 10;
	const ordenDePago = 11;
	const ajuste = 12;
	const transferenciaInterna = 13;
	const transferenciaBancariaOperacion = 14;
	const ingresoChequePropio = 15;
	const cobroChequeVentanilla = 16;
	const ventaCheques = 17;
	const depositoBancario = 18;
	const rechazoCheque = 19;
	const rendicionGastos = 20;
	const aporteSocio = 21;
	const acreditarCheque = 22;
	const debitarCheque = 23;
	const retiroSocio = 24;
	const prestamo = 25;
	const reingresoChequeCartera = 26;

	public static function getConstants() {
		$rc = new ReflectionClass('TiposTransferenciaBase');
		return $rc->getConstants();
	}
}
abstract class DocumentosContables {
	const recibo = 10;
	const ordenDePago = 11;
	const ajuste = 12;
	const transferenciaInterna = 13;
	const transferenciaBancariaOperacion = 14;
	const ingresoChequePropio = 15;
	const cobroChequesVentanilla = 16;
	const ventaCheques = 17;
	const depositoBancario = 18;
	const rechazoCheque = 19;
	const rendicionGastos = 20;
	const aporteSocio = 21;
	const acreditarCheque = 22;
	const debitarCheque = 23;
	const retiroSocio = 24;
	const prestamo = 25;
	const reingresoChequeCartera = 26;
}
abstract class TiposImporte {
	const efectivo = 'E';
	const cheque = 'C';
	const transferenciaBancariaImporte = 'T';
	const retencionEfectuada = 'R';
	const retencionSufrida = 'S';
}
abstract class Cajas {
	const chequesRechazados = 2;
}
abstract class TipoImpuesto {
	const iva = 1;
	const iibb = 2;
	const ganancias = 3;
}
abstract class Motivos {
	const rechazoCheque = 1;
	const agregarGarantia = 2;
}
abstract class Impuestos {
	const iva21 = 1;
	const iva10 = 2;
	const iva27 = 3;
	const ivaPercepcion = 5;
}
abstract class ParametrosContabilidad {
	const deudoresPorVentas = 1;
	const ingresosPorVentas = 2;
	const ivaDebitoFiscal = 3;
	const valoresADepositar = 4;
	const descuentosComerciales = 5;
	const recargosComerciales = 6;
	const chequesRechazados = 7;
	const comisionesBancarias = 8;
	const prestamosBancarios = 9;
	const cuentaParticular = 10;
	const gastosARendir = 11;
	const proveedores = 12;
}
abstract class ParametrosCompras {
	const minimoParaConsiderarCumplido = 0.01;
}
abstract class TiposRutas {
	const imagenEtiquetaCania = 1;
	const imagenEtiquetaLengua = 2;
	const imagenKitBordado = 3;
	const imagenKitFrecuencia1 = 4;
	const imagenKitFrecuencia2 = 5;
	const imagenKitFrecuencia3 = 6;
	const imagenKitSerigrafia = 7;
	const imagenLengua = 8;
	const imagenPrincipal = 9;
	const imagenCania = 10;
	const imagenLadoInterno = 11;
	const imagenPuntera = 12;
	const imagenTalon = 13;
	const imagenMiniatura = 14;

	const imagenesClonables = '8,9,10,11,12,13';
	const categoriasEcommerceConTablaDeTalles = 'K,L,M,S,W';
}
abstract class ExtensionImagen {
	const png = '.png';
	const jpg = '.jpg';
}
abstract class ParametrosGenerales {
	const clienteDepositosPendientes = 707;
}

?>