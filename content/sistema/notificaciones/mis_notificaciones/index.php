<?php
$tipoPorDefecto = Funciones::toString(Funciones::get('tipoPorDefecto'));
?>

<style>
#divTipos {
	height: 414px;
	width: 195px;
}

#divTipos ul li {
	padding-top: 4px;
}
</style>

<script type='text/javascript'>
	Koi.directive('onLoad', function () {
		return function($scope) {
			$scope.getTipos();
		};
	}).filter('filtrar', function() {
		return function(lista, checks) {
			return lista.filter(function(item) {
				if (item.anulado == 'N') {
					for (tipo in checks) {
						if (checks[tipo].tildado && item.idTipoNotificacion == tipo)
							return true;
					}
				}
				return false;
			});
		};
	});

	Koi.controller('AppCtrl', function ($scope, $http) {
		$scope.ordenadoPor = 'fecha';
		$scope.tipos = [];
		$scope.notificaciones = [];

		$scope.getTipos = function() {
			var tipoPorDefecto = '<?php echo $tipoPorDefecto; ?>';
			$.showLoading();
			$http.post('/content/sistema/notificaciones/mis_notificaciones/buscar.php', {busqueda: 1}).success(function(json) {
				$scope.tipos = json.data;
				for (i in $scope.tipos) {
					$scope.tipos[i].tildado = (tipoPorDefecto == '' || i == tipoPorDefecto);
				}
				$scope.getNotificaciones();
			});
		};

		$scope.getNotificaciones = function() {
			//funciones.limpiarScreen();
			$http.post('/content/sistema/notificaciones/mis_notificaciones/buscar.php', {}).success(function(json) {
				$scope.notificaciones = json.data;
				for (i in $scope.notificaciones) {
					$scope.notificaciones[i].getSufijoOffVista = function() {
						return (this.vista == 'S' ? '_off' : '');
					};
					$scope.notificaciones[i].clase = '';
					$scope.notificaciones[i].getSufijoOffEliminar = function() {
						return (this.eliminable != 'S' ? '_off' : '');
					};
					$scope.notificaciones[i].tipoNotificacion = $scope.tipos[$scope.notificaciones[i].idTipoNotificacion];
					$scope.notificaciones[i].visar = function() {
						this.vista = 'S';
						this.ajax('visar');
					};
					$scope.notificaciones[i].eliminar = function() {
						this.anulado = 'S';
						this.ajax('eliminar');
					};
					$scope.notificaciones[i].ajax = function(modo) {
						$.showLoading();
						$http.post('/content/sistema/notificaciones/mis_notificaciones/editar.php',
									{modo: modo, idNotificacion: this.idNotificacion}).success(function(json) {
							$.hideLoading();
							switch (funciones.getJSONType(json)){
								case funciones.jsonNull:
								case funciones.jsonEmpty:
									$.error('Ocurrió un error');
									break;
								case funciones.jsonError:
									$.error(funciones.getJSONMsg(json));
									break;
								case funciones.jsonSuccess:
								break;
							}
						});
					};
				}
				$.hideLoading();
			});
		};
	});

	$(document).ready(function(){
		//AppCtrl = angular.element($('#App')[0]).scope();
		tituloPrograma = 'Mis notificaciones';
		cambiarModo('inicio');
	});

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
	}
</script>
<div id='programaTitulo'></div>
<div id='programaContenido' ng-controller='AppCtrl' on-load>
	<div id='divTiposNotificaciones' class='fLeft'>
		<div class='mTop10'>
			<label for='slctOrdenadoPor'>Orden:</label>
			<br />
			<select id='slctOrdenadoPor' class='textbox w180' ng-model='ordenadoPor'>
				<option value='fecha'>Por fecha</option>
				<option value='idTipoNotificacion'>Por tipo</option>
				<option value='vista'>Por vista</option>
			</select>
		</div>
		<div id='divTipos' class='mTop10'>
			<label>Tipos de notificaciones:</label>
			<ul class='customScroll mTop5'>
				<li ng-repeat='tipo in tipos'>
					<input id='chk_{{tipo.id}}' type='checkbox' ng-model='tipo.tildado' />
					<label for='chk_{{tipo.id}}'>{{tipo.nombre}}</label>
				</li>
			</ul>
		</div>
	</div>
	<div id='divNotificaciones' class='fRight w80p customScroll'>
		<table id='tablaNotificaciones' class='registrosAlternados w100p'>
			<tbody>
				<tr id='tr_{{notif.idNotificacion}}' ng-repeat='notif in notificaciones | orderBy: ordenadoPor | filtrar: tipos'>
					<td class='w75p'>
						<table class='w100p'>
							<tbody>
								<tr class='tableRow'>
									<td class='bold aLeft'><label><a href='{{notif.link}}' target='_blank' ng-mouseover='notif.clase = "underline"' ng-mouseleave='notif.clase = ""' class='{{notif.clase}}'>{{notif.tipoNotificacion.nombre + ': ' + notif.detalle}}</a></label></td>
								</tr>
								<tr class='tableRow'>
									<td class='aLeft'>
										<label>Fecha: {{notif.fecha}}</label>
										<label class='fRight'><a href='{{notif.link}}' target='_blank' ng-mouseover='notif.clase = "underline"' ng-mouseleave='notif.clase = ""' class='{{notif.clase}}'>Ver más</a></label>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
					<td class='w10p'>
						<div class='aCenter'>
							<img src='{{notif.tipoNotificacion.imagen}}' style='width: 45px; height: 45px;' />
						</div>
					</td>
					<td class='w15p'>
						<div class='botonera aCenter'>
							<a href='#' class='boton' title='Visto' ng-click='notif.visar();'><img src='/img/botones/40/aceptar{{notif.getSufijoOffVista()}}.gif' /></a>
							<a href='#' class='boton' title='Eliminar' ng-click='notif.eliminar()'><img src='/img/botones/40/cancelar{{notif.getSufijoOffEliminar()}}.gif' /></a>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>