<?php
//Sólo accesible desde determinada IP (las de las PC de fichaje)
?>
<style>
#divFichaje {
	margin: 24%;
}
#divSuccess, #divError {
	display: none;
	margin-top: 5px;
	font: normal 14px Arial;
}
#divSuccess {
	color: green;
}
#divError {
	color: red;
}
input.textbox.inputFichaje {
	font: normal 36px Calibri, sans-serif;
	height: 50px;
}
</style>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Fichaje';
		$(window).keypress(function(){
			$('#inputEmpleado').focus();
		});
		$('#inputEmpleado').keyup(function(event){
			if ($('#inputEmpleado').val().length == 8 && event.keyCode == 13)
				guardar();
		});
	});

	function guardar(){
		var url = '/content/fichaje/agregar.php?';
		$.showLoading();
		$.postJSON(url, armoObjetoGuardar(), function(json){
			$('#inputEmpleado').val('').focus();
			$.hideLoading();
			switch (funciones.getJSONType(json)){
				case funciones.jsonNull:
				case funciones.jsonEmpty:
					error('Ocurrió un error al intentar guardar');
					break;
				case funciones.jsonError:
					error(funciones.getJSONMsg(json));
					break;
				case funciones.jsonSuccess:
					success(funciones.getJSONMsg(json));
					break;
			}
		});
	}

	function error(msg) {
		$('#divError').text(msg).slideFadeToggle('fast').delay(2000).slideFadeToggle('slow');
	}

	function success(msg) {
		$('#divSuccess').text(msg).slideFadeToggle('fast').delay(2000).slideFadeToggle('slow');
	}

	function armoObjetoGuardar(){
		return {
			id: $('#inputEmpleado').val()
		};
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divFichaje' class='aCenter'>
		<input id='inputEmpleado' class='inputFichaje textbox obligatorio w400 aCenter' />
		<div id='divSuccess'>asd</div>
		<div id='divError'>asd</div>
	</div>
</div>
<div id='programaPie'></div>
