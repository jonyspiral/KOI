/*
 * En los inputs que se quiere validar se usa el tag Validate por ejemplo " validate='Email' "
 * En el ready del master se pone lo siguiente:
 * $('.textbox[validate]').blur(function(){setTimeout('funciones.validate("' + this.id + '", "' + $(this).attr('validate') + '");', 1);});
 * Con esto attacha un evento blur en cada control que tenga el tag Validate.
 * Se llama a una función genérica de validar que a partir del tipo de validación y el ID del campo llama a una función que maneja la situación
 */


Funciones.prototype.executeFunctionByName = function(functionName /*, args */) {
	//Los argumentos de la función a ejecutar se mandan como parámetros opcionales a partir del segundo (splice(1))
	var context = window;
	var args = Array.prototype.slice.call(arguments).splice(1);
	var namespaces = functionName.split(".");
	var func = namespaces.pop();
	for(var i = 0; i < namespaces.length; i++) {
		context = context[namespaces[i]];
	}
	return context[func].apply(this, args);
};
Funciones.prototype.validate = function(inputId, tipo){
	//To_do el temita del LASTCHECK es por si hacemos un BLUR ANORMAL
	//Por ejemplo, si estando en foco en el input, hago un blur en otra ventana de windows o en otra página, cuando vuelva no puedo salir del error
	var input = $('#' + inputId);
	if ((typeof input.attr('lastCheck') === 'undefined') || (new Date().getTime() - input.attr('lastCheck') > 300)){
		try {
			funciones.executeFunctionByName('funciones.validate' + tipo, input);
		} catch (ex){}
	}
};
Funciones.prototype.validateEmail = function(input){
	var email = input.val();
	if (email == '')
		return;
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	if (!reg.test(email)){
		$.error('El email ingresado no es válido', function(){
			input.attr('lastCheck', new Date().getTime()).focus();
		});
	};
};
Funciones.prototype.validateTelefono = function(input){
	var telefono = input.val();
	if (telefono == '')
		return;
	var reg = /^[0-9]+$/;
	if (!reg.test(telefono)){
		$.error('El telefono ingresado no es válido', function(){
			input.attr('lastCheck', new Date().getTime()).focus();
		});
	};
};