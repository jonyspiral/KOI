<?php

/**
 * Valores que se editan de la tabla:
 * 
 * La cantidad total de pares de la categoría			#spanCantidadPares_IDCAT
 * El precio total de la categoria						#spanTotal_IDCAT
 * 
 * La cantidad total de un articulo						#nombre_IDART
 * El id del artículo (saber en cuál hacen click)		.idArticulo (IDART)
 * La foto de un articulo								#foto_IDART
 * El color de un articulo								#color_IDART
 * El precio al público de un articulo					#precioPub_IDART
 * El precio de facturación de un articulo				#precioFac_IDART
 * El tipo de curva de un articulo						#curva_IDART).attr('tipo')
 * La fecha de disponibilidad de un articulo			#fechaDisp_IDART
 * Una cantidad de un talle de un articulo				#tablaPosiciones_IDART.posicion_NROPOSICION
 * La cantidad total de un articulo						#cantidad_IDART
 * El precio total de un articulo						#total_IDART
 * 
 * Todas las cantidades									.cantidadArt
 * Todos los totales									.totalArt
 * Todas las cantidades de Categoría					.spanCantidadPares
 * Todos los totales de Categoría						.spanTotal
 * 
 */


function generoFdp(){
	$formasDePago = '';
	foreach (Factory::getInstance()->getListObject('FormaDePago', 'anulado = \'N\'') as $fdp) {
		$formasDePago .= '<input id="rdFormaDePago_' . $fdp->id . '" class="textbox inputFormaDePago" ';
		$formasDePago .= 'type="radio" name="radioGroupFormaDePago" value="' . $fdp->id . '" rel="id" />';
		$formasDePago .= '<label for="rdFormaDePago_' . $fdp->id . '">' . $fdp->nombre . '</label>';
	}
	return $formasDePago;
}

$idBuscar = Funciones::get('id');

?>

<style>
	#divInicial {
		margin-top: 170px;
	}
	#divCampos1 {
		height: 440px;
	}
	#divCampos1.alterHeight {
		height: 140px;
	}
	.pad {
		padding: 0 3px;
	}
	.pad2 {
		padding: 5px 0;
	}
	#divAutorizaciones {
		height: 40px;
	}
	#divDatosNotaDePedido {
		height: 260px;
	}
	#tituloInfoNotaDePedido {
		height: 20px;
	}

	.agotado {
		position: relative;
		-webkit-transform: rotate(-30deg);
		font-size: 20px;
		font-weight: bold;
		background-color: red;
		color: white;
		z-index: 2;
	}
	.agotado.top {
		top: 25px;
	}

	.notop {
		position: relative;
		top: -10px;
	}

	/* PopUp Editar */
	#tablePopUpEditar {
		border-spacing: 1px;
	}
	#tablePopUpEditar th, #tablePopUpEditar .curva td {
		padding: 10px 20px;
	}
	#tablePopUpEditar .rowFinita td {
		padding: 2px 20px;
	}
</style>

<script type='text/javascript'>
	var notaDePedido = {},
		stock,
		objGlobal = [],
		listaAplicable = 'N';
	<?php if (Usuario::logueado()->esCliente()) { ?>
		var cliente = {	id: '<?php echo Usuario::logueado()->contacto->cliente->id; ?>',
						razonSocial: '<?php echo Usuario::logueado()->contacto->cliente->razonSocial; ?>'};
		listaAplicable = '<?php echo Usuario::logueado()->contacto->cliente->listaAplicable; ?>';
	<?php } ?>
	<?php if (Usuario::logueado()->esVendedor() || Usuario::logueado()->esCliente()) { ?>
		var vendedor = {id: '<?php echo Usuario::logueado()->getCodigoPersonal(); ?>',
						nombreApellido: '<?php echo Usuario::logueado()->nombreApellido; ?>'};
	<?php } ?>

	$(document).ready(function(){
		tituloPrograma = 'Nota de pedido';
		$('#botonAgregarGrande, #botonBuscarGrande').hover(function(){
			$(this).css('position', 'relative').animate({
				height: '+=25px',
				top: '-=25px'
			});
		}, function(){
			$(this).css('position', 'none').animate({
				height: '-=25px',
				top: '+=25px'
			});
		});
		$('#inputSucursal').focus(function(){
			$(this).attr('alt', 'idCliente=' + $('#inputCliente_selectedValue').val());
		});
		$('#inputCliente').blur(function(){funciones.delay('ponerVendedor();');});
		<?php if (!Usuario::logueado()->esCliente()) { ?>
			$('#btnBuscarAyudaArticulos').click(function(){funciones.delay('buscarAyudaArticulos();');});
		<?php } ?>
		cambiarModo('inicio');
		$.showLoading();
		$.get('/content/comercial/pedidos/nota_de_pedido/getTablaArticulos.php', function(html){
			$('#divCampos1').html(html);
			$.postJSON('/content/comercial/pedidos/nota_de_pedido/getStock.php', function(json){
				stock = json.data;
				$.hideLoading();
				<?php echo ($idBuscar ? 'buscar("' . $idBuscar . '");' : ''); ?>
			});
		});
	});

	function loadRestantes() {
		$('#divCampos1>div:first>div').each(function(){
			var div = $(this);
			if (!div.attr('loaded'))
				loadCategoria(div.attr('idcategoria'), true);
		}, function(){
			$.hideLoading();
		});
	}

	function loadMe(obj) {
		var idCategoria = obj.attr('idcategoria');
		loadCategoria(idCategoria);
	}

	function loadCategoria(idCategoria, fromRestantes) {
		var div = $('#divCategoria_' + idCategoria);
		if (div.attr('loaded'))
			return;
		if (!fromRestantes)
			$.showLoading();
		$.getJSON('/content/comercial/pedidos/nota_de_pedido/getTablaArticulos.php?idCategoria=' + idCategoria, function(json){
			div.html(json.data.html);
			$('#divCategoria_' + json.data.idCategoria + ' .tablaCategoria').each(function(){
				var trs = $('#' + this.id + '>tbody>tr');
				for (var i = 0; i < trs.length; i++){
					tr = trs[i];
					var idCombinado = escapeIdC($(tr).find('.idArticulo').text() + '_' + $(tr).find('.idColor').text());
					$('#stockDisp_' + idCombinado).text(getStockArticulo(idCombinado));
					crearObjetoGlobal(idCombinado);
				}
				div.attr('loaded', '1');
				if (!fromRestantes) $.hideLoading();
			});
		});
	}

	function escapeIdC(idC){
		return idC.replace(/\./g, '\\\.');
	}

	function crearObjetoGlobal(idC){
		var auxObj = {};
		auxObj.idArticulo = $('#articulo_' + idC).text();
		auxObj.idColor = $('#color_' + idC).text();
		auxObj.color = $('#color_' + idC).attr('title');
		auxObj.idCombinado = escapeIdC(auxObj.idArticulo + '_' + auxObj.idColor);
		auxObj.idCategoria = $('#tr_' + idC).parents('.tablaCategoria:first').attr('id').split('_')[1];
		auxObj.nombre = $('#nombre_' + idC).text();
		auxObj.precioPub = $('#precioPub_' + idC).text();
		auxObj.precioFac = function(){
			var precio = $('#precioFac_' + this.idCombinado + ' label').not('.hidden').text();
			return precio;
		};
		auxObj.curva = {};
		auxObj.curva.tipo = $('#curva_' + idC).attr('tipo');
		auxObj.curva.cantPos = funciones.toInt($('#curva_' + idC).attr('cantPos'));
		if (auxObj.curva.tipo == 'M') {
			var htmlCurvas = $('#curva_' + idC).attr('curvas');
			var curvas = [];
			if (htmlCurvas) {
				htmlCurvas = htmlCurvas.split('|');
				$(htmlCurvas).each(function(){
					var arrAux = this.split('_');
					var id = arrAux[0];
					var p = arrAux[1].split('-');
					curvas[id] = [];
					for (var j = 1; j <= auxObj.curva.cantPos; j++)
						curvas[id][j] = funciones.toInt(p[j - 1]);
				});
			}
			auxObj.curva.curvas = curvas;
		}
		auxObj.stockDisponible = $('#stockDisp_' + idC);
		auxObj.fechaDisponible = $('#fechaDisp_' + idC);
		auxObj.talles = [];
		$('#tablaPosiciones_' + idC + ' .talle').each(function(i){
			auxObj.talles[i + 1] = $(this).text();
		});
		auxObj.posiciones = [];
		$('#tablaPosiciones_' + idC + ' .posicion').each(function(i){
			auxObj.posiciones[i + 1] = $(this);
		});
		auxObj.cantidad = function(){
			var temp = 0;
			$(this.posiciones).each(function(){
				temp += funciones.toInt($(this).text());
			});
			$('#cantidad_' + idC).text(temp);
			return temp;
		};
		auxObj.total = $('#total_' + idC);
		auxObj.vaciar = function(){
			$('#tablaPosiciones_' + this.idCombinado + ' .posicion').text(0);
			this.cantidad();
		};
		objGlobal[idC] = auxObj;
	}

	function getStockArticulo(idCombinado, posicion) {
		var idArticulo = funciones.toInt(idCombinado.split('_')[0]);
		var idColor = idCombinado.split('_')[1];
		try {
			if (typeof posicion === 'undefined'){
				var temp = 0;
				var arr = stock[idArticulo][idColor];
				for (pos in arr) {
					if (funciones.toInt(arr[pos]) > 0)
						temp += funciones.toInt(arr[pos]);
				}
				return temp;
			}
			var valorReal = funciones.toInt(stock[idArticulo][idColor][posicion]);
			if (valorReal > 0)
				return valorReal;
			return 0;
		} catch (ex) {
			return 0;
		}	
	}

	function corregirPrecios() {
		if (listaAplicable == 'D'){
			$('.precioN').addClass('hidden');
			$('.precioD').removeClass('hidden');
		} else {
			$('.precioD').addClass('hidden');
			$('.precioN').removeClass('hidden');
		}
	}

	function buscarAyudaArticulos() {
		var idArt = $('#inputAyudaArticulo_selectedValue').val(),
			idColor = $('#inputAyudaColor_selectedValue').val();
		if (idArt != '' && idColor != '') {
			var idCombinado = idArt + '_' + idColor;
			if (objGlobal[idCombinado])
				editarFila(idArt + '_' + idColor);
			else
				$.error('El artículo-color que intentó buscar no es parte de la nota de pedido');
		} else
			$('#inputAyudaArticulo').focus();
	}

	function manejarClienteYVendedor(){
		<?php if (Usuario::logueado()->esCliente()) { ?>
			listaAplicable = '<?php echo Usuario::logueado()->contacto->cliente->listaAplicable; ?>';
			$('#inputCliente').autoComplete(cliente);
			$('#inputBuscarCliente').autoComplete(cliente);
			$('#inputCliente').disable();
			$('#inputBuscarCliente').disable();
		<?php } ?>
		<?php if (Usuario::logueado()->esVendedor() || Usuario::logueado()->esCliente()) { ?>
			$('#inputVendedor').autoComplete(vendedor);
			$('#inputVendedor').disable();
			$('#inputDescuento').disable();
			$('#inputRecargo').disable();
		<?php } ?>
	}

	function ponerVendedor(){
		$.postJSON('/content/comercial/pedidos/nota_de_pedido/getInfoCliente.php?idCliente=' + $('#inputCliente_selectedValue').val(), function(json){
			$('#inputVendedor').autoComplete(json.data.vendedor);
			listaAplicable = json.data.listaAplicable;
		});
	}

	function manejarCustomRadios(modo){
		switch(modo){
			case 'inicio':
				//$('#radioGroupFormaDePago').disableRadioGroup();
				break;
			case 'buscar':
				//$('#radioGroupFormaDePago').disableRadioGroup();
				break;
			case 'editar':
				//<?php echo (Usuario::logueado()->esVendedor() ? '' : '$("#radioGroupFormaDePago").enableRadioGroup();' ); ?>
				break;
			case 'agregar':
				//<?php echo (Usuario::logueado()->esVendedor() ? '' : '$("#radioGroupFormaDePago").enableRadioGroup();' ); ?>
				break;
		}
	}

	function mostrarSoloLoPedido(){
		$('.tablaCategoria>tbody>tr').not('.itemDelPedido').hide();
		$('.tablaCategoria>tbody').each(function(){
			if ($(this).find('tr:visible').length == 0)
				$(this).parent().find('thead>tr').hide();
		});
		$('.solapas').solapas({fixedHeight: 400, heightSolapas: 47, precall: loadMe, selectedItem: 0});
	}

	function mostrarTodo(){
		$('.solapas').solapas({fixedHeight: 400, heightSolapas: 47, precall: loadMe, selectedItem: 0});
		$('.tablaCategoria tr').show();
	}

	function ocultarDatos(click){
		if (typeof click === 'undefined')
			click = false;
		if ((!click) || ($('#inputCliente_selectedValue').val() != '' && $('#inputSucursal_selectedValue').val() != '' && $('#inputAlmacen_selectedValue').val() != '' && $('#inputTemporada_selectedValue').val() != '')) {
			$('#divDatosTitulo').show();
			$('#labelMostrarDatos').show();
			$('#labelOcultarDatos').hide();
			$('#divCampos1').removeClass('alterHeight');
			$('#divDatosNotaDePedido').slideUp();
			funciones.delay('ponerDatos();');
			if ((click) && (!$('#divCampos1').isVisible())) {
				$('#divCampos1').slideDown();
				<?php if (!Usuario::logueado()->esCliente()) { ?>
					$('.ayudaArticulos').show();
					$('#btnBuscar').show().focus();
				<?php } ?>
			}
			corregirPrecios();
		} else {
			$.error('Debe seleccionar un cliente, una sucursal, un almacén, y la temporada antes de continuar');
		}
	}

	function ponerDatos(){
		$('#labelDatosCliente').text($('#inputCliente_selectedName').val());
		$('#labelDatosSucursal').text($('#inputSucursal_selectedName').val());
		$('#labelDatosEstado').text($('#labelEstado').text());
		var desc = funciones.toFloat($('#inputDescuento').val());
		var rec = funciones.toFloat($('#inputRecargo').val());
		setTotalTotal();
		setTotalPares();
		$('#labelDatosTotal').text($('#labelTotal').text());
		$('#labelDatosDescuento').text(((desc > 0) ? ' (' + (desc) + '% descuento)' : '') + ((rec > 0) ? ' (' + (rec) + '% recargo)' : ''));
		$('.labelDatosSeparador').text(' | ');
	}

	function mostrarDatos(){
		if ($('#inputAlmacen_selectedValue').val() == '')
			$('#inputAlmacen').val('01').autoComplete();
		if ($('#inputTemporada_selectedValue').val() == '')
			$('#inputTemporada').val('9').autoComplete();
		$('#divDatosTitulo').hide();
		$('#labelMostrarDatos').hide();
		$('#labelOcultarDatos').show();
		$('#divCampos1').addClass('alterHeight');
		$('#divDatosNotaDePedido').slideDown();
		$('#divCampos1').slideUp();
		$('.ayudaArticulos').hide();
		$('#btnBuscar').hide();
	}

	function limpiarScreen(){
		notaDePedido = {};
		$('.labelForm').text('');
		$('#divAutorizaciones').html('');

		$('.posicion').text('0');
		$('.cantidadArt').text('0');
		$('.totalArt').text(funciones.formatearMoneda('0'));
		$('.spanCantidadPares').text('0');
		$('.spanTotal').text(funciones.formatearMoneda('0'));
	}

	function ampliarFoto(link){
		$.jPopUp.show($('<div class="w600 h300 vaBottom table-cell aCenter"><img src="' + link + '" height="300" />'), [{value: 'Cerrar', action: function(){$.jPopUp.close();}}]);
	}

	function getCantidadCat(idCategoria){
		var cantidad = 0;
		$('#tablaCategoria_' + idCategoria + ' .cantidadArt').each(function(){
			cantidad += funciones.toInt($(this).text());
		});
		return cantidad;
	}
	function setCantidadCat(idCategoria){
		var cantidad = getCantidadCat(idCategoria);
		return $('#spanCantidadPares_' + idCategoria).text(cantidad);
	}

	function getTotalCat(idCategoria){
		var total = 0;
		$('#tablaCategoria_' + idCategoria + ' .totalArt').each(function(){
			total += funciones.toFloat($(this).text());
		});
		return total;
	}
	function setTotalCat(idCategoria){
		var total = getTotalCat(idCategoria);
		return $('#spanTotal_' + idCategoria).text(funciones.formatearMoneda(total));
	}

	function setTotalPares(){
		var pares = 0;
		$('.spanCantidadPares').each(function(){
			pares += funciones.toInt($(this).text());
		});
		$('#labelDatosPares').text(pares);
	}

	function setTotalTotal(){
		var total = 0;
		$('.spanTotal').each(function(){
			total += funciones.toFloat($(this).text());
		});
		var newTotal = total;
		newTotal -= total * (funciones.toFloat($('#inputDescuento').val()) / 100);
		newTotal += total * (funciones.toFloat($('#inputRecargo').val()) / 100);
		$('#labelTotal').text(funciones.formatearMoneda(newTotal));
		$('#labelDatosTotal').text(funciones.formatearMoneda(newTotal));
	}

	function calculaTotales(categoria){
		var selector = '#tablaCategoria_' + categoria;
		if (typeof categoria === 'undefined')
			selector = '.tablaCategoria';
		$(selector).each(function(){
			var idCategoria = this.id.split('_')[1];
			var trs = $('#' + this.id + '>tbody>tr');
			for (var i = 0; i < trs.length; i++){
				tr = trs[i];
				var idCombinado = escapeIdC($(tr).find('.idArticulo').text() + '_' + $(tr).find('.idColor').text());
				objGlobal[idCombinado].cantidad(); //Recalcula la cantidad y la pone
				objGlobal[idCombinado].total.text(funciones.formatearMoneda(funciones.toInt(objGlobal[idCombinado].cantidad()) * funciones.toFloat(objGlobal[idCombinado].precioFac())));
			}
			setCantidadCat(idCategoria);
			setTotalCat(idCategoria);
		});
		setTotalPares();
		setTotalTotal();
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined') {
			$('#inputBuscar').val(idBuscar).autoComplete();
		}
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		loadRestantes();
		var url = '/content/comercial/pedidos/nota_de_pedido/buscar.php?idNotaDePedido=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'Ocurrió un error al buscar la nota de pedido "' + $('#inputBuscar_selectedName').val() + '"',
			cbSuccess = function(json){
				$('#tablaDatos, #tablaDatos2').loadJSON(json);
				$('#labelUsuario').text(funciones.acortarString(json.usuario.nombre + ' ' + json.usuario.apellido, 25, '...'));
				$('#inputDescuento').val(json.descuento);
				$('#inputRecargo').val(json.recargo);
				$('#labelTotal').text(funciones.formatearMoneda(json.importeTotal));
				listaAplicable = json.cliente.listaAplicable;
				corregirPrecios();
				<?php if (!Usuario::logueado()->esCliente()) { ?>
					funciones.generoDivAutorizaciones(json, '<?php echo Usuario::logueado()->id; ?>');
				<?php } ?>
				$('.itemDelPedido').removeClass('itemDelPedido');
				$(json.detalle).each(function(){
					var idCombinado = this.articulo.id + '_' + this.colorPorArticulo.id;
					$('#tr_' + idCombinado).addClass('itemDelPedido');
					//A la variable notaDePedido le voy asignando las cosas que tiene el pedido como si fuera LIBRE
					//Esto es porque no guardo la cantidad de cada curva que pidió porque pueden variar las curvas
					//Si no edita esa línea, se manda a guardar como libre
					var aux = '';
					var contador = 0;
					for (var i = 1; i <= 8; i++){
						try {
							objGlobal[idCombinado].posiciones[i].text(this.cantidad[i]);
							var cant = funciones.toInt(this.cantidad[i]);
							contador += cant;
							aux += cant + '-'; //Para la variable notaDePedido (libre) (ver dentro de 5 lineas)
						} catch (ex) {
						}
					}
					aux = aux.substr(0, aux.length - 1);
					notaDePedido[idCombinado] = {};
					if (contador > 0)
						notaDePedido[idCombinado]['L'] = aux;
				});
				calculaTotales();
				setTimeout(function(){ponerDatos();}, 500);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function vaciarFila(idCombinado){
		delete notaDePedido[idCombinado];
		objGlobal[idCombinado].vaciar();
		return calculaTotales(objGlobal[idCombinado].idCategoria);
	}

	function addCurva(idCurva){
		var cant = $('#c' + idCurva + '_cant');
		if (funciones.toInt(cant.text()) < 0){
			cant.text('0');
		}
		cant.text(funciones.toInt(cant.text()) + 1);
		recalcularTotales();
	}

	function removeCurva(idCurva){
		var cant = $('#c' + idCurva + '_cant');
		if (funciones.toInt(cant.text()) > 0){
			cant.text(funciones.toInt(cant.text()) - 1);
		} else {
			cant.text('0');
		}
		recalcularTotales();
	}

	function recalcularTotales(){
		var arrayPosiciones = [];
		$('.curva').each(function(){
			var curva = this;
			$(curva).find('.posicionDetalle').each(function(i){
				if (typeof arrayPosiciones[i + 1] === 'undefined')
					arrayPosiciones[i + 1] = 0;
				arrayPosiciones[i + 1] += funciones.toInt($(curva).find('.cantidadDetalle').text()) * funciones.toInt($(this).text());
			});
		});
		for (key in arrayPosiciones){
			$('#tot_p' + key).text(arrayPosiciones[key]);
		}
	}

	function editarFila(idCombinado){
		idCombinado = escapeIdC(idCombinado);
		var j;
		var div = '';
		var tipoComer = objGlobal[idCombinado].curva.tipo;
		if (tipoComer == 'A') {
			$.alert('No puede pedir el artículo porque está agotado');
			return;
		}
		var cantPos = objGlobal[idCombinado].curva.cantPos;
		div += '<div id="divPopUpEditar">';
		div += '<div>';
		div += '<div class="p10 aRight">';
		div += '<label class="bold">';
		div += objGlobal[idCombinado].nombre + ' - ' + objGlobal[idCombinado].color;
		div += '</label>';
		div += ' (' + objGlobal[idCombinado].idArticulo + ' - ' + objGlobal[idCombinado].idColor + ')';
		div += '</div>';
		div += '</div>';
		div += '<table id="tablePopUpEditar">';
		div += '<thead>';
		div += '<tr class="tableHeader">';
		div += '<th>' + (tipoComer == 'M' ? 'Curva' : '') + '</th>';
		for (j = 1; j <= cantPos; j++)
			div += '<th>' + objGlobal[idCombinado].talles[j] + '</th>';
		if (tipoComer == 'M') {
			div += '<th>Cant</th>';
			div += '<th>+</th>';
			div += '<th>-</th>';
		}
		div += '</tr>';
		div += '</thead>';
		div += '<tbody>';
		if (tipoComer == 'L' || tipoComer == 'T') {
			div += '<tr class="tableRow bWhite stock rowFinita">';
			div += '<td>Stock</td>';
			for (j = 1; j <= cantPos; j++)
				div += '<td id="stock_p' + j + '" class="aCenter">' + getStockArticulo(idCombinado, j) + '</td>';
			div += '</tr>';
			div += '<tr class="tableRow bGray curva">';
			div += '<td id="l_curva"></td>';
			for (j = 1; j <= cantPos; j++){
				div += '<td id="l_p' + j + '" class="aCenter">';
				div += '<input id="input_l_p' + j + '" class="textbox w25 aCenter" maxlength="3" type="text" onblur="ponerTotal(this, ' + j + ')" value="0" />';
				div += '</td>';
			}
			div += '</tr>';
		} else if (tipoComer == 'M') {
			div += '<tr class="tableRow bWhite stock rowFinita">';
			div += '<td>Stock</td>';
			for (j = 1; j <= cantPos; j++)
				div += '<td id="stock_p' + j + '" class="aCenter">' + getStockArticulo(idCombinado, j) + '</td>';
			div += '<td colspan="3"></td>';
			div += '</tr>';
			var curvas = objGlobal[idCombinado].curva.curvas;
			for (id in curvas) {
				div += '<tr class="tableRow bGray curva">';
				div += '<td id="c' + (id) + '_curva" class="idCurva">' + id + '</td>';
				for (j = 1; j <= cantPos; j++)
					div += '<td id="c' + (id) + '_p' + j + '" class="aCenter posicionDetalle">' + funciones.toInt(curvas[id][j]) + '</td>';
				div += '<td id="c' + (id) + '_cant" class="aCenter bSkin cantidadDetalle bold">0</td>';
				div += '<td id="c' + (id) + '_mas"><a href="#" onclick="addCurva(\'' + (id) + '\')" class="boton"><img src="/img/botones/25/agregar.gif" /></a></td>';
				div += '<td id="c' + (id) + '_menos"><a href="#" onclick="removeCurva(\'' + (id) + '\')" class="boton"><img src="/img/botones/25/menos.gif" /></a></td>';
				div += '</tr>';
			}
		}
		div += '</tbody>';
		div += '<tfoot>';
		div += '<tr class="tableRow bWhite rowFinita">';
		div += '<td id="tot_empty1 aCenter">Total</td>';
		for (j = 1; j <= cantPos; j++)
			div += '<td id="tot_p' + j + '" class="aCenter bold totalDetalle" posicion="' + j + '">0</td>';
		if (tipoComer == 'M') 
			div += '<td colspan="3"></td>';
		div += '</tr>';
		div += '</foot>';
		div += '</table>';
		div += '</div>';
		div = $(div);
		var botones = [{value: 'Guardar', action: function(){popUpGuardar(idCombinado);}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones, function(){vaciarAyuda();});
	}

	function vaciarAyuda(){
		$('#inputAyudaArticulo').limpiarAutoSuggestBox();
		$('#inputAyudaColor').limpiarAutoSuggestBox();
		$('#inputAyudaArticulo').focus();
	}

	function popUpGuardar(idCombinado){
		var tipoComer = objGlobal[idCombinado].curva.tipo;
		notaDePedido[idCombinado] = {};
		var aux = '';
		var contador = 0;
		if (tipoComer == 'M'){
			$('.curva').each(function(){
				var cant = funciones.toInt($(this).find('.cantidadDetalle').text());
				if (cant > 0)
					notaDePedido[idCombinado][funciones.toInt($(this).find('.idCurva').text())] = cant;
			});
			$('.totalDetalle').each(function(){
				objGlobal[idCombinado].posiciones[funciones.toInt($(this).attr('posicion'))].text($(this).text());
			});
		} else if (tipoComer == 'L') {
			$('.totalDetalle').each(function(){
				var cant = funciones.toInt($(this).text());
				contador += cant;
				aux += cant + '-';
			});
			aux = aux.substr(0, aux.length - 1);
			if (contador > 0)
				notaDePedido[idCombinado]['L'] = aux;
			$('.totalDetalle').each(function(){
				objGlobal[idCombinado].posiciones[funciones.toInt($(this).attr('posicion'))].text($(this).text());
			});
		} else if (tipoComer == 'T') {
			var cumple = true;
			$('.totalDetalle').each(function(){
				var cant = funciones.toInt($(this).text());
				var stock = funciones.toInt($('#stock_p' + $(this).attr('posicion')).text());
				if (cant > stock)
					cumple = false;
				contador += cant;
				aux += cant + '-';
			});
			if (!cumple) {
				$.jPopUp.close(function(){
					$.alert('Este artículo sólo puede ser pedido sobre stock disponible');
				});
				return;
			}
			aux = aux.substr(0, aux.length - 1);
			if (contador > 0)
				notaDePedido[idCombinado]['L'] = aux;
			$('.totalDetalle').each(function(){
				objGlobal[idCombinado].posiciones[funciones.toInt($(this).attr('posicion'))].text($(this).text());
			});
		}
		calculaTotales(objGlobal[idCombinado].idCategoria);
		$.jPopUp.close(function(){
			$('#tr_' + idCombinado).animate({opacity: '0.05'}, 300, function(){$(this).animate({opacity: '1'}, 300);});
		});
	}

	function ponerTotal(input, nroTotal) {
		if (!funciones.esNatural(input.value))
			input.value = '0';
		$('#tot_p' + nroTotal).text(input.value);
	}

	function xlsClick(){
		<?php if (Usuario::logueado()->esCliente()) { ?>
			goXls('<?php echo Usuario::logueado()->contacto->cliente->listaAplicable; ?>');
		<?php } else { ?>
			var botones = [{value: 'Precio de lista', action: function(){goXls('L'); $.jPopUp.close();}}, {value: 'Precio distribuidor', action: function(){goXls('D'); $.jPopUp.close();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
			var div = '<div class="jMsgBox-wrapper jMsgBox-confirm" style="min-width: 270px; min-height: 60px; zoom: 1; ">';
			div += '¿Qué precio quiere mostrar como precio mayorista?';
			div += '</div>';
			$.jPopUp.show(div, botones);
		<?php } ?>
	}

	function goXls(precio) {
		var finalUrl = urlToExport('xls') + precio;
		if (finalUrl)
			funciones.xlsClick(finalUrl);
	}

	function urlToExport(tipo, precio){
		var url = '/content/comercial/pedidos/nota_de_pedido/get' + (tipo == 'xls' ? 'Xls' : 'Pdf') + '.php?tipoPrecio=';
		return url;
	}

	function hayErrorGuardar(){
		if ($('#inputCliente_selectedValue').val() == '')
			return 'Debe seleccionar un cliente';
		if ($('#inputSucursal_selectedValue').val() == '')
			return 'Debe seleccionar una sucursal';
		if ($('#inputTemporada_selectedValue').val() == '')
			return 'Debe seleccionar la temporada del pedido';
		if (funciones.objectLength(notaDePedido) == 0)
			return 'Debe elegir algún artículo';
		return false;
	}

    function guardar(){
      var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
      var url = '/content/comercial/pedidos/nota_de_pedido/' + aux + '.php?';
      funciones.guardar(url, armoObjetoGuardar());
    }

    function facturarClick () {
      var error = hayErrorGuardar();
      if (error) {
        $.error(error);
      } else {
        $.confirm('¿Está seguro que desea facturar este pedido?', function (r) {
          if (r == funciones.si) {
            facturar();
          }
        });
      }
    }

    function facturar(){
      var url = '/content/comercial/pedidos/nota_de_pedido/facturar.php?';
      funciones.guardar(url, armoObjetoGuardar(), function () {
        var url = '/content/comercial/facturas/reimpresion/getPdf.php';
        url += '?puntoDeVenta=' + this.data.puntoDeVenta + '&letra=' + this.data.letra + '&numero=' + this.data.numero;
        funciones.pdfClick(url);
        setTimeout(funciones.reload, 100);
      });
    }

	function armoObjetoGuardar(){
		return {
			idNotaDePedido: $('#inputBuscar_selectedValue').val(),
			idCliente: $('#inputCliente_selectedValue').val(),
			idSucursal: $('#inputSucursal_selectedValue').val(),
			idAlmacen: $('#inputAlmacen_selectedValue').val(),
			observaciones: $('#inputObservaciones').val(),
			/*formaDePago: $('#radioGroupFormaDePago').radioVal(),*/
			idTemporada: $('#inputTemporada_selectedValue').val(),
			idVendedor: $('#inputVendedor_selectedValue').val(),
			descuento: funciones.toFloat($('#inputDescuento').val()),
			recargo: funciones.toFloat($('#inputRecargo').val()),
			notaDePedido: notaDePedido
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar la nota de pedido "' + $('#inputBuscar_selectedName').val() + '"?',
			url = '/content/comercial/pedidos/nota_de_pedido/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {idNotaDePedido: $('#inputBuscar_selectedValue').val()};
	}

	function autorizar(nro, bool) {
		var msg = '¿Está seguro que desea ' + (bool ? 'autorizar' : 'rechazar') + ' la nota de pedido?</br></br>Motivo:',
			url = '/content/comercial/pedidos/nota_de_pedido/autorizar.php?';
		funciones.autorizar(msg, url, armoObjetoAutorizar(nro, bool));
	}

	function armoObjetoAutorizar(nro, bool, motivo){
		return {
			idNotaDePedido: $('#inputBuscar_selectedValue').val(),
			numeroDeAutorizacion: nro,
			autoriza: (bool ? 'S' : 'N'),
			motivo: motivo
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#inputBuscarCliente').enable();
				$('.ayudaArticulos').hide();
				$('.solapas label[onclick]').hide();
				manejarCustomRadios(modo);
				manejarClienteYVendedor();
				$('#divInicial').slideDown();
				$('#divInfoNotaDePedido').slideUp();
				$('#divCampos1').slideUp();
				$('#btnXls').show();
                $('#btnFacturar').hide();
				mostrarTodo();
				ocultarDatos();
				$('.solapas').solapas({fixedHeight: 400, heightSolapas: 47, precall: loadMe}).restart();
				break;
			case 'buscar':
				$('#inputBuscarCliente').disable();
				$('.ayudaArticulos').hide();
				$('.solapas label[onclick]').hide();
				manejarCustomRadios(modo);
				manejarClienteYVendedor();
				$('#divInicial').slideUp();
				$('#divInfoNotaDePedido').slideDown();
				$('#divDatosEditarGuardar').hide();
				$('#divCampos1').slideDown();
				$('#btnXls').hide();
                $('#btnFacturar').hide();
				mostrarSoloLoPedido(); //Muestro sólo lo que pedí
				ocultarDatos();
				break;
			case 'editar':
				$('#btnBuscar').show();
				$('#inputBuscarCliente').disable();
				<?php if (!Usuario::logueado()->esCliente()) { ?>
					$('.ayudaArticulos').show();
				<?php } ?>
				$('.noEditable').disable();
				$('.solapas label[onclick]').show();
				manejarCustomRadios(modo);
				$('#divInfoNotaDePedido').slideDown();
				$('#divDatosEditarGuardar').show();
                $('#btnFacturar').hide();
				mostrarTodo();
				ocultarDatos();
				$('.noEnable').disable().disableRadioGroup();
				$('#inputAyudaArticulo').focus();
				break;
			case 'agregar':
				$('#inputBuscarCliente').disable();
				<?php if (!Usuario::logueado()->esCliente()) { ?>
					$('.ayudaArticulos').show();
				<?php } ?>
				$('.solapas label[onclick]').show();
				manejarCustomRadios(modo);
				manejarClienteYVendedor();
				$('#divInicial').slideUp();
				$('#divInfoNotaDePedido').slideDown();
				$('#divDatosEditarGuardar').show();
				$('#divCampos1').slideUp();
                $('#btnFacturar').show();
				mostrarTodo();
				mostrarDatos();
				$('#inputCliente').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divInicial' class='aCenter'>
		<a id='botonBuscarGrande' class='boton' href='#' title='Buscar' onclick='funciones.buscarClick()'><img class='p15' src='/img/botones/personales/buscar.jpg' /></a>
		<a id='botonAgregarGrande' class='boton' href='#' title='Agregar' onclick='funciones.agregarClick()'><img class='p15' src='/img/botones/personales/mas.jpg' /></a>
	</div>
	<div id='divInfoNotaDePedido' class='w100p mBottom10 corner10 bAllOrange bWhite hidden'>
		<div id='divDatosNotaDePedido' class='hidden'>
			<div id='divCampos21' class='fLeft w50p'>
				<?php
					$tabla = new HtmlTable(array('cantRows' => 7, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
					$tabla->getRowCellArray($rows, $cells);

					$cells[0][0]->content = '<label>Estado:</label>';
					$cells[0][0]->style->width = '165px';
					$cells[0][1]->content = '<label id="labelEstado" class="labelForm" rel="estado"></label>';
					$cells[0][1]->style->width = '260px';
					$cells[1][0]->content = '<label>Cliente:</label>';
					$cells[1][1]->content = '<input id="inputCliente" class="textbox autoSuggestBox obligatorio ' . (Usuario::logueado()->esCliente() ? '' : 'inputForm') . ' noEditable w230" name="ClienteTodos" rel="cliente" />';
					$cells[2][0]->content = '<label>Sucursal:</label>';
					$cells[2][1]->content = '<input id="inputSucursal" class="textbox autoSuggestBox obligatorio inputForm noEditable w230" name="Sucursal" linkedTo="inputCliente,Cliente" rel="sucursal" />';
					$cells[3][0]->content = '<label>Almacén:</label>';
					$cells[3][1]->content = '<input id="inputAlmacen" class="textbox autoSuggestBox obligatorio inputForm noEditable w230" name="Almacen" rel="almacen" />';
					$cells[4][0]->content = '<label>Total:</label>';
					$cells[4][1]->content = '<label id="labelTotal" class="labelForm"></label>';
					$cells[5][0]->content = '<label>Observaciones:</label>';
					$cells[5][1]->content = '<textarea id="inputObservaciones" onblur="$(\'#labelOcultarDatos\').parent().focus();" class="textbox inputForm w230" rel="observaciones"></textarea>';
					$cells[6][0]->content = '<label>Usuario alta:</label>';
					$cells[6][1]->content = '<label id="labelUsuario" class="labelForm"></label>';

					$tabla->create();
				?>
			</div>
			<div id='divCampos22' class='fRight w50p'>
				<?php
					$tabla = new HtmlTable(array('cantRows' => 6, 'cantCols' => 2, 'id' => 'tablaDatos2', 'cellSpacing' => 10));
					$tabla->getRowCellArray($rows, $cells);
					
					$cells[0][0]->content = '<label>Vendedor:</label>';
					$cells[0][0]->style->width = '135px';
					$cells[0][1]->content = '<input id="inputVendedor" class="textbox autoSuggestBox ' . (Usuario::logueado()->esVendedor() ? '' : 'inputForm') . ' w230" name="Vendedor" rel="vendedor" />';
					$cells[0][1]->style->width = '260px';
					$cells[1][0]->content = '<label>Temporada:</label>';
					$cells[1][1]->content = '<input id="inputTemporada" class="textbox autoSuggestBox obligatorio w230" name="Temporada" rel="temporada" />';
					$cells[2][0]->content = '<label>Descuento:</label>';
					$cells[2][1]->content = '<input id="inputDescuento" class="textbox ' . (Usuario::logueado()->esVendedor() ? '' : 'inputForm') . ' w230" validate="Porcentaje" />';
					$cells[3][0]->content = '<label>Recargo:</label>';
					$cells[3][1]->content = '<input id="inputRecargo" class="textbox ' . (Usuario::logueado()->esVendedor() ? '' : 'inputForm') . ' w230" validate="Porcentaje" />';
					$cells[4][0]->content = (!Usuario::logueado()->esCliente()) ? '<label>Autorizaciones</label>' : '';
					$cells[4][1]->content = ' ';
					$cells[5][0]->colspan = 2;
					$cells[5][0]->content = '<div id="divAutorizaciones" class="customScroll pRight10"></div>';

					$tabla->create();
				?>
			</div>
		</div>
		<div id='tituloInfoNotaDePedido' class='bold white bLightOrange corner10 p5'>
			<div id='divDatosTitulo' class='fLeft'>
				<label id='labelDatosCliente' title='Cliente'></label>
				<label class='labelDatosSeparador'></label>
				<label id='labelDatosSucursal' title='Sucursal'></label>
				<label class='labelDatosSeparador'></label>
				<label id='labelDatosEstado' title='Estado'></label>
				<label class='labelDatosSeparador'></label>
				<label id='labelDatosPares' title='Pares'></label><label> pares</label>
				<label class='labelDatosSeparador'></label>
				<label id='labelDatosTotal' title='Total'></label>
				<label id='labelDatosDescuento' title='Descuento'></label>
			</div>
			<div id='divDatosEditarGuardar' class='fRight pRight15'>
				<a href='#' onclick='mostrarDatos();'><label id='labelMostrarDatos' class='cPointer s19'>+</label></a>
				<a class='borderFocusDarkOrange' href='#' onclick='ocultarDatos(true);'><label id='labelOcultarDatos' class='cPointer hidden p5'>Siguiente</label></a>
			</div>
		</div>
	</div>
	<div id='divCampos1' class='fLeft w100p hidden solapas'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar w200' name='ClienteTodos' alt='' />
		</div>
		<div>
			<label class='filtroBuscar'>Nota de pedido:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Pedido' linkedTo='inputBuscarCliente,Cliente' alt='aprobado=N' />
		</div>
		<div>
			<label class='ayudaArticulos'>Artículo:</label>
			<input id='inputAyudaArticulo' class='textbox autoSuggestBox ayudaArticulos w200' name='Articulo' alt='' />
		</div>
		<div>
			<label class='ayudaArticulos'>Color:</label>
			<input id='inputAyudaColor' class='textbox autoSuggestBox ayudaArticulos w140' name='ColorPorArticulo' linkedTo='inputAyudaArticulo,Articulo' alt='' />
		</div>
		<div>
			<a id='btnBuscarAyudaArticulos' class='boton ayudaArticulos' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'comercial/pedidos/nota_de_pedido/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'comercial/pedidos/nota_de_pedido/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'comercial/pedidos/nota_de_pedido/borrar/')); ?>
        <?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
        <?php Html::echoBotonera(array('boton' => 'fac', 'accion' => 'facturarClick();', 'id' => 'btnFacturar', 'permiso' => 'comercial/pedidos/nota_de_pedido/facturar/')); ?>
        <?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
