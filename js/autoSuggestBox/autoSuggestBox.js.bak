/*
 * Se usan los siguientes tags de INPUT:
 * name="Localidad" - Name para el tipo de dato que se busca
 * alt="" - Alt para filtrar la consulta
 * linkedTo="inputPais,Pais;inputProvincia,Provincia" - LinkedTo para que el campo ALT se genere solo (acIdInput,TipoDeDato)
 *
 */

//Variables Globales
var acListTotal   =  0,
	acListCurrent = -1,
	acDelay		  = 10,
	url = '/js/autoSuggestBox/autoSuggestBox.php?',
	idResultados = '',
	acResultados = '',
	acInput = '',
	acInputSelectedValue = '',
	acInputSelectedName = '',
	rowHeight = 19,
	rowWidth = 212,
	extraData = {};

$.fn.autoComplete = function(obj/*, callback*/) {
	if (typeof obj === 'undefined' || obj == null) {
		clearAutoComplete(this.attr('id')/*, callback*/); //No sirve el callback porque OBJ estaría en UNDEFINED y no entraría por acá 
		return;
	}
	var objectType = $(this).attr('name');
	var defVal = '', defName = '';
	if (objectType) {
		switch(objectType) {
			case 'Banco':
				defVal = obj.idBanco; defName = obj.nombre;
				break;
			case 'BancoPropio':
				defVal = obj.idSucursal; defName = obj.nombreSucursal;
				break;
			case 'Cliente':
			case 'ClienteTodos':
				defVal = obj.id; defName = obj.razonSocial;
				break;
			case 'NotaDePedido':
				defVal = obj.id; defName = obj.fechaAlta + ' (' + obj.estado + ')';
				break;
			case 'OrdenDePago':
				defVal = obj.numero; defName = obj.fecha;
				break;
			case 'Personal':
				defVal = obj.idPersonal; defName = obj.nombre;
				break;
			case 'Proveedor':
			case 'ProveedorTodos':
				defVal = obj.id; defName = obj.razonSocial;
				break;
			case 'Recibo':
				defVal = obj.numero; defName = obj.fecha;
				break;
			case 'Vendedor':
				defVal = obj.id; defName = obj.nombre + obj.apellido;
				break;
			case 'Usuario':
				defVal = obj.id; defName = obj.id;
				break;
			default:
				defVal = obj.id; defName = obj.nombre;
				break;
		}
	}
	acSetDefs($(this).attr('id'), defVal, defName);
	/*callback;*/
};

$.fn.autoSuggestBox = function(){
	return this.each(function(){
		if ($('#' + this.id +  '_selectedValue').length == 0 || $('#' + this.id).hasClass('autoSuggestBox_forceInit')){ //Si no existe el input selectedValue es porque no se lo hizo AutoSuggestBox todavía
			var defVal = ($('#' + this.id).attr('defVal') ? $('#' + this.id).attr('defVal') : '');
			var defName = ($('#' + this.id).attr('defName') ? $('#' + this.id).attr('defName') : '');

			//Creo el div de resultados (si es que no existe)
			if ($('#' + this.id +  '_divResultados').length == 0)
				$("body").append('<div id="' + this.id +  '_divResultados' + '" class="divResultados"></div>');

			//Creo el input selectedValue después del input y el selectedName después del selectedValue
            if (!$('#' + this.id).hasClass('autoSuggestBox_noAppend')) {
                $('#' + this.id).after('<input id="' + this.id + '_selectedValue" class="autoSuggestBox_selectedValue" style="display: none;" />');
                $('#' + this.id + '_selectedValue').after('<input id="' + this.id + '_selectedName" class="autoSuggestBox_selectedName" style="display: none;" />');
            }

			//Si el input tiene 'defVal' y 'defName', los pongo
			acSetDefs(this.id, defVal, defName);

			//Lleno las variables globales
			llenoVariables(this.id);
	
			//Bindeo el evento OnBlur. Tiene que ser con delay para que haga este y el blur que yo le ponga al campo al mismo tiempo.
			acInput.blur(function(){setTimeout('clearAutoComplete("' + this.id + '");', funciones.autoSuggestBoxDelay - 100);});
	
			//Bindeo el evento OnKeyUp
			acInput.keyup(aKeyUp);
	
			//Bindeo el evento Focus. Esto sirve para cuando se le pone un LinkedTo (que sirve para filtros combinados. Ej: pais->provincia)
			//En el input de provincia se pone linkedTo="inputPais,Pais"
			if (tieneLink($(this)))
				linkAutoSuggestBox(this.id);
				//$(this).focus(function(){setTimeout('linkAutoSuggestBox("' + this.id + '");', 1);});
		}
	});
};

function acSetDefs(id, defVal, defName){
	if (defVal && defName) {
		var text = defVal + ' - ' + defName;
		$('#' + id).val(text).attr('lastVal', text);
		$('#' + id + '_selectedValue').val(defVal);
		$('#' + id + '_selectedName').val(defName);
	}
}

function tieneLink(input){
	return (typeof input.attr('linkedTo') !== 'undefined' && input.attr('linkedTo') != '');
}

function llenoVariables(id){
	acIdInput = id;
	idResultados = acIdInput +  '_divResultados';
	acInput = $("#" + acIdInput);
	acResultados = $('#' + acIdInput +  '_divResultados');
	acInputSelectedValue = $('#' + acIdInput + '_selectedValue');
	acInputSelectedName = $('#' + acIdInput + '_selectedName');
}

function llenoVariablesArray(id){
	var array = [];
	array['acIdInput'] = id;
	array['idResultados'] = acIdInput +  '_divResultados';
	array['acInput'] = $("#" + acIdInput);
	array['acResultados'] = $('#' + acIdInput +  '_divResultados');
	array['acInputSelectedValue'] = $('#' + acIdInput + '_selectedValue');
	array['acInputSelectedName'] = $('#' + acIdInput + '_selectedName');
    return array;
}

function aKeyUp(e){
	llenoVariables(this.id);
	//reposicionoDivResultados();

	//window.event es para IE
	var keyCode = e.keyCode || window.event.keyCode;
	var lastVal = acInput.val();
	
	//Me fijo si apreto arriba o abajo
	if (updownArrow(keyCode)){
		return;
	}
	
	//Me fijo si apretó enter o escape
	if (keyCode == 13 || keyCode == 27){
		clearAutoComplete();
		return;
	}
	
	//Si escribió texto, hago el autoComplete.
	//Lo hago con TimeOut para que esta función termine y se concrete el evento aKeyUp.
	//Cuando termina este evento, el KEY finalmente se pone en el input, entonces obtengo acá el lastVal y adentro consulto denuevo el valor del input.
	setTimeout(function () {autoComplete(lastVal, keyCode);}, acDelay);

	//En el BG del input pongo un GIF de "loading"
	//acSearchField.css("background", "url(images/varios/loading-mini.gif) #FFFFFF no-repeat right");
}

function reposicionoDivResultados(array){
	if (typeof array === 'undefined')
		array = [];
	//Obtengo la posición del input
	var sf_pos    = acInput.offset();
	var sf_top    = sf_pos.top;
	var sf_left   = sf_pos.left;

	//Obtengo el tamaño del input
	var sf_height = acInput.height();
	var sf_width  = acInput.width();

	//Obtengo el alto de la pantalla
	var sf_screenHeight = document.documentElement.clientHeight;

	//Obtengo el alto del espacio entre el input y el fin de la página
	var sf_espacioInferior = sf_screenHeight - (sf_top - window.pageYOffset) - sf_height;

	//Calculo el alto del divResultado
	var cantRows = 0;
	for (var i = 0; i < array.length; i++){
		cantRows += funciones.roundUp(acResultados.children(':eq(' + i + ')').textSize().width / rowWidth);
	}
	var sf_divResultadoHeight = cantRows * rowHeight + 4;

	//apply the css styles - optimized for Firefox
	acResultados.css("position", "absolute");
	acResultados.css("top", ((cantRows > 0) && (sf_espacioInferior < sf_divResultadoHeight) ? (sf_top - sf_divResultadoHeight) : (sf_top + sf_height + 5)));
	acResultados.css("left", sf_left - 2);
	acResultados.css("width", sf_width + 18); //Ajusto el div al ancho del input
	//acResultsDiv.css("width", 400); //Le fuerzo el tamaño para que cuando el input es muy chiquito no se achique el div.
}

function updownArrow(keyCode) {
	if (acListTotal && (keyCode == 40 || keyCode == 38)){
		if (keyCode == 38){ //Arriba
			if (acListCurrent == 0 || acListCurrent == -1){
				acListCurrent = acListTotal - 1;
			} else {
				acListCurrent--;
			}
		} else { //Abajo
			if (acListCurrent == acListTotal - 1){
				acListCurrent = 0;
			} else {
				acListCurrent++;
			}
		}
		//Recorro el div y le pongo el estilo que corresponde a cada opción
		acResultados.children().each(function(i){
			if (i == acListCurrent){
				var value = this.childNodes[0].nodeValue;
				acInput.val(value);
				var id = value.split(' - ')[0];
				acInputSelectedValue.val(id); //Seteo selectedValue
				var name = (value.indexOf(' - ') == -1 ? id : value.substr(value.indexOf(' - ') + 3));
				acInputSelectedName.val(name); //Seteo selectedName
				this.className = "selected";
			} else {
				this.className = "unselected";
			}
		});
		return true;
	} else {
		//Reinicio los estilos
		acListCurrent = -1;
		return false;
	}
}

function goAjax(id, urlFull, lastValue, keyCode){
	var array = llenoVariablesArray(id);
	$.getJSON(urlFull, function(json){
		//get the total of results
		if (typeof json === 'undefined' || typeof json.data === 'undefined')
			acListTotal = 0;
		else
			try {acListTotal = json.data.length;} catch (e) {}
		var ansLength = acListTotal;
		var arrayResultados = [];
		var newData = '';

		if (ansLength) {
			for (var i = 0; i < ansLength; i++) {
				try {
					var id = json.data[i].id,
						contenido = json.data[i].nombre;
					var combinado = id + (contenido == '' ? '' : ' - ' + contenido);

					if (!(typeof json.data[i].data == 'undefined') && (typeof extraData[combinado] == 'undefined')) {
						extraData[combinado] = json.data[i].data;
					}
					arrayResultados[arrayResultados.length] = combinado;
					newData += '<div class="unselected">' + combinado + '</div>';
				} catch (e) {}
			}
		} else {
			newData += '<div class="unselectable">Sin registros</div>';
		}

		array['acResultados'].html(newData);
		array['acResultados'].css("display", "block");

		var divs = $('#' + array['idResultados'] + " > div").not('.unselectable');

		divs.mouseover(function(){
			divs.each(function(){this.className = "unselected";});
			this.className = "selected";
		});

		divs.click(function(){
			var value = this.childNodes[0].nodeValue;
			array['acInput'].val(value);
			var id = value.split(' - ')[0];
			array['acInputSelectedValue'].val(id); //Seteo selectedValue
			var name = (value.indexOf(' - ') == -1 ? id : value.substr(value.indexOf(' - ') + 3));
			array['acInputSelectedName'].val(name); //Seteo selectedName
			clearAutoComplete();
		});

		//Esto es para que cuando hace blur habiendo puesto sólo el ID o algo, busque si existe ese
		if (keyCode == 'sungutrule'){
			var found = false;
			array['acResultados'].children().each(function () {
				var value = this.childNodes[0].nodeValue;
				var id = value.split(' - ')[0];
				if (id == lastValue) {
					found = true;
					array['acInputSelectedValue'].val(id); //Seteo selectedValue
					var name = (value.indexOf(' - ') == -1 ? id : value.substr(value.indexOf(' - ') + 3));
					array['acInputSelectedName'].val(name); //Seteo selectedName
					array['acInput'].val(id + ' - ' + name); //Seteo el value
				}
			});
			//Si no encuentra el ID que ingresó el usuario, borro los 3 campos
			if (!found) {
				array['acInputSelectedValue'].val(''); //Seteo selectedValue
				array['acInputSelectedName'].val(''); //Seteo selectedName
				array['acInput'].val(''); //Seteo el value
			}
			clearAutoComplete(array);
		}
		reposicionoDivResultados(arrayResultados);
	});
}

function autoComplete(lastValue, keyCode){
	//Dejo de mostrar el "loading"
	//acInput.css("background", "");

	var part = acInput.val();

	//Si está vacío el input, limpio el DIV
	if (part == '' && keyCode != 113){ //113 es F2 (givemeall)
		clearAutoComplete();
		return;
	}

	//Si no cambió el valor, salgo.
	if (lastValue != part && keyCode != 'sungutrule'){
		return;
	}
	
	part = (part == '' ? 'givemeall' : part); //Si el input está vacío y llegó acá es porque apretó F2

	goAjax(acIdInput, url + 'key=' + part + '&name=' + acInput.attr("name") + '&' + acInput.attr("alt"), lastValue, keyCode);
}

function clearAutoComplete(id){
	//En id llega NADA, un ID o un ARRAY
	//Cuando llega el ID es porque se ingresó un ID sin elegir uno de la lista
	if (typeof id !== 'undefined' && !funciones.isArray(id)){
		if ($('#' + id).attr('lastVal') != $('#' + id).val()) {
			llenoVariables(id);
	
			//Si el tipo ingresa nada más q el ID, lo pongo en selectedValue y Name y busco.
			var input = acInput.val();
			if (input.split(' - ').length == 1){
				acInputSelectedValue.val(input);
				acInputSelectedName.val(input);
				if (input != ''){
					autoComplete(input, 'sungutrule');
					if (acInput.attr('linkerFrom'))
						cambiarAlt(acInput);
					return;
				}
			}
		}
	}
	//Cuando llega un ARRAY es porque se ejecuta la función AUTOCOMPLETE, para completar el nombre según el ID
	var aArray = [];
	if (funciones.isArray(id))
		aArray = id;
	else
		//Lleno un array con las mismas variables globales que ya existían, pero lo hago para no tener que copiar dos veces lo de abajo
		aArray = llenoVariablesArray(acIdInput);

	if ((aArray['acInput'].attr('lastVal') != aArray['acInput'].val()) && (aArray['acInput'].attr('linkerFrom'))) {
		cambiarAlt(aArray['acInput']);
	}
	aArray['acResultados'].html('');
	aArray['acResultados'].css("display", "none");
	aArray['acInput'].attr('lastVal', aArray['acInput'].val());
	if (aArray['acInput'].val() == "")
		aArray['acInput'].next().val("");
}

function cambiarAlt(i/*input*/){
	if (i.attr('linkerFrom')) {
		var linkeds = i.attr('linkerFrom').split(';');
		$(linkeds).each(function(){
			var acIdInput = this;
			var input = $('#' + acIdInput); //Es el input hijo
			var acIdInputLinked = getLinkPhpId(getLinkedClass(input, i.attr('id'))); //Es el input padre
			var alt = (typeof input.attr('alt') === 'undefined' ? '' : input.attr('alt'));
			var newAlt = '';
			//Me fijo si ya tiene el &asd=4
			// Hago un loop con el split de "&" y dsp slit de "="
			// Si alguno es igual a acIdInputLinked entonces no lo pongo en el newAlt, al resto sí
			//Una vez generado el nuevoAlt lo pongo en el input linked
			// Y borro el contenido de los hijos
			var arrAlt = alt.split('&');
			for (key in arrAlt) {
				var arrUnAlt = arrAlt[key].split('=');
				if (arrUnAlt.length == 2)
					if (arrUnAlt[0] != acIdInputLinked)
						newAlt += '&' + arrUnAlt[0] + '=' + arrUnAlt[1];
			}
			newAlt += '&' + acIdInputLinked + '=' + escape($('#' + i.attr('id') + '_selectedValue').val());
			input.attr('alt', newAlt);
			if (input.val() != '') {
				input.blur();
			} else {
				input.val('');
				$('#' + acIdInput + '_selectedValue, #' + acIdInput + '_selectedName').val('');
			}
		});
	}
}

function getLinkedClass(inputLinked, idLinker) {
	//Esta función loopea en los linkedTo del inputLinked y cuando encuentra
	// el idLinker le devuelve el valor que está al otro lado de la coma, que es la Clase
	var vReturn = '';
	if (inputLinked.attr('linkedTo')) {
		var linkedTos = inputLinked.attr('linkedTo').split(';');
		$(linkedTos).each(function(){
			var arr = this.split(',');
			if (arr.length == 2 && arr[0] == idLinker)
				vReturn = arr[1];
		});
	}
	return vReturn;
}

function linkAutoSuggestBox(id){
	var obj = $('#' + id);
	if (obj.attr('linkedTo')) {
		var links = obj.attr('linkedTo').split(';');
		$(links).each(function(){
			var input = $('#' + this.split(',')[0]),
				aux = '';
			if (input.attr('linkerFrom')) {
				aux = input.attr('linkerFrom');
				if (aux.length > 0 && aux.substr(aux.length - 1, aux.length) != ';')
					aux += ';';
			}
			aux += id;
			input.removeAttr('linkerFrom').attr('linkerFrom', aux);
			cambiarAlt(input);
		});
	}
}

function getLinkPhpId(id){
	//Para todos los casos va a ser id + Nombre ("idPais").
	//Para los casos raros, los meto en el switch
	switch (id){
		/*
		case 'Pais':
			return 'idPais';
			break;
		*/
		case 'sacaranga':
			return 'solocongo';
			break;
	}
	return 'id' + id;
}