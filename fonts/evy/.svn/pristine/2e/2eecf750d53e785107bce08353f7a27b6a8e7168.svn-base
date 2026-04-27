<?php
?>

<script type='text/javascript'>
	$(document).ready(function() {
		if (!$('#indicadores li').length) {
			$('#indicadores').invisible();
		}
	});

	Koi.directive('onLoad', function () {
		return function($scope) {
			$scope.heartbeat();

			setInterval(function(){
				$scope.$apply(function() {
					$scope.heartbeat();
				});
			}, 30000);
		};
	});

	Koi.controller('AppCtrl', function ($scope, $http) {
		$scope.titulo = document.title;
		$scope.ordenadoPor = function(item) {
			return funciones.toInt(item.id);
		};
		$scope.notificaciones = [];
		$scope.sendObject = {
			anuladas: [],
			vistas: [],
			ultimaFechaHora : ''
		};
	
		$scope.setTitulo = function() {
			var count = $scope.notificaciones.filter(function(item) {
				return item.vista == 'N';
			}).length;
			document.title = (count > 0 ? '(' + count + ')' : '') + $scope.titulo;
		};
	
		$scope.getFecha = function(notificaciones) {
			if (typeof notificaciones[0] == 'undefined')
				return $scope.sendObject.ultimaFechaHora;
			return notificaciones[0].fechaUltimaMod;
		};

		$scope.getPosition = function(id) {
			var i = $.map($scope.notificaciones, function(o, i) {
				return (o['id'] == id ? i : false);
			}).join('').replace(/false/g, '');
			return (i != '' ? funciones.toInt(i) : $scope.notificaciones.length);
		};

		$scope.heartbeat = function() {
			$http.post('/heartbeat.php', {heartbeat: $scope.sendObject}).success(function(json) {
				switch (funciones.getJSONType(json)){
					case funciones.jsonNull:
					case funciones.jsonEmpty:
						$.error('Ocurrió un error');
						break;
					case funciones.jsonAlert:
						$.alert(funciones.getJSONMsg(json));
						break;
					case funciones.jsonError:
						$.error(funciones.getJSONMsg(json));
						break;
					case funciones.jsonObject:
						for (var i in json.data) {
							var not = json.data[i];
							var pos = $scope.getPosition(not.id);

							if (not.anulado == 'S') {
								if (pos != $scope.notificaciones.length)
									$scope.notificaciones.splice(pos, 1);
							} else {
								$scope.notificaciones[pos] = json.data[i];

								//Asigno propiedades a la notificación
								$scope.notificaciones[pos].clase = function() {
									return (this.vista == 'S' ? 'vista' : '');
								};
								$scope.notificaciones[pos].visar = function() {
									if (this.anulado == 'S') {
										//$.error('La notificación ya fue anulada');
									} else if (this.vista == 'S') {
										//$.error('La notificación ya fue marcada como vista');
									} else {
										this.vista = 'S';
										$scope.sendObject.vistas.push(this.id);
										$scope.heartbeat();
										return true;
									}
									return false;
								};
								$scope.notificaciones[pos].eliminar = function() {
									if (this.anulado == 'S') {
										//$.error('La notificación ya fue anulada');
									} else if (this.eliminable == 'N') {
										//$.error('No se puede eliminar la notificación ya que aún no se completó la acción');
									} else {
										this.anulado = 'S';
										$scope.sendObject.anuladas.push(this.id);
										$scope.notificaciones.splice($scope.getPosition(this.id), 1);
										$scope.heartbeat();
										return true;
									}
									return false;
								};
								$scope.notificaciones[pos].goLink= function() {
									//Pongo visado y ABRO!
									this.visar();
									window.open(this.link, '_blank');
								};
							}
						}
						$scope.sendObject.anuladas = [];
						$scope.sendObject.vistas = [];
						$scope.sendObject.ultimaFechaHora = $scope.getFecha(json.data);
						$scope.setTitulo();
					break;
				}
			});
		};
	});



	function snow() {
		//Configure below to change URL path to the snow image
		var snowsrc = [
			"snow.gif",
			"snow.gif",
			"snow.gif",
			"snow.gif",
			"snow.gif",
			"snow.gif",
			"snow.gif",
			"snow.gif",
			"snow.gif",
			"snow.gif",
			"snow.gif",
			"snow.gif",
			"snow.gif",
			"snow.gif",
			"snow.gif",
			"snow.gif",
			"mureri1.gif",
			"mureri2.gif",
			"snow.gif",
			"snow.gif"
		];
		// Configure below to change number of snow to render
		var no = 20;
		// Configure whether snow should disappear after x seconds (0=never):
		var hidesnowtime = 0;
		// Configure how much snow should drop down before fading ("windowheight" or "pageheight")
		var snowdistance = "windowheight";
		///////////Stop Config//////////////////////////////////
		var ns6up = (document.getElementById && !document.all) ? 1 : 0;
		var dx, xp, yp;    // coordinate and position variables
		var am, stx, sty;  // amplitude and step variables
		var i, doc_width = 800, doc_height = 600;
		if (ns6up) {
			doc_width = self.innerWidth;
			doc_height = self.innerHeight;
		}
		dx = [];
		xp = [];
		yp = [];
		am = [];
		stx = [];
		sty = [];
		for (i = 0; i < no; ++ i) {
			dx[i] = 0;                        // set coordinate variables
			xp[i] = Math.random()*(doc_width-50);  // set position variables
			yp[i] = Math.random()*doc_height;
			am[i] = Math.random()*20;         // set amplitude variables
			stx[i] = 0.02 + Math.random()/10; // set step variables
			sty[i] = 0.7 + Math.random();     // set step variables
			document.write('<div id="dot' + i + '" style="position: absolute; z-index: ' + (9999 + i) +
						   '; visibility: visible; top: 15px; left: 15px;"><img src="/img/varias/' + snowsrc[i] + '" border="0"><\/div>');
		}
		function snowIE_NS6() {  // IE and NS6 main animation function
			doc_width = window.innerWidth - 10;
			doc_height = (window.innerHeight && snowdistance == "windowheight") ? window.innerHeight : document.documentElement.offsetHeight;
			for (i = 0; i < no; ++ i) {  // iterate for every dot
				yp[i] += sty[i];
				if (yp[i] > doc_height-50) {
					xp[i] = Math.random()*(doc_width-am[i]-30);
					yp[i] = 0;
					stx[i] = 0.02 + Math.random()/10;
					sty[i] = 0.7 + Math.random();
				}
				dx[i] += stx[i];
				document.getElementById("dot"+i).style.top=yp[i]+"px";
				document.getElementById("dot"+i).style.left=xp[i] + am[i]*Math.sin(dx[i])+"px";
			}
			snowtimer = setTimeout(snowIE_NS6, 10);
		}
		snowIE_NS6();
	}

	//snow();
</script>

<style>
#divNotificaciones {
	width: 600px;
	height: 530px;
	padding-top: 25px;
}
.notificacion {
	width: 260px;
	height: 50px;
	padding: 10px;
	margin: 0 15px 10px 0;
	background-color: #E67B19;
}
.notificacion:hover {
	background-color: #C55A29;
}
.notificacion.vista {
	opacity: 0.5;
	background-color: #E67B19;
}
.notificacion.vista:hover {
	background-color: #C55A29;
}
.notificacion>div {
	height: 50px;
	line-height: 1;
	overflow: hidden;
}
.div1 {
	width: 50px;
}
.div2 {
	width: 175px;
	padding: 0 10px;
}
.div3 {
	width: 15px;
}
.nombre {
	height: 15px;
}
.detalle {
	height: 35px;
}
.imagen {
	width: 50px;
	height: 50px;
}

</style>

<?php if (Usuario::logueado()->esCliente()){ ?>
<div id='divPanelBotones'>
	<div id='divCarro'><a class='boton' href='/comercial/pedidos/nota_de_pedido/' title='Nota de Pedido'><img src='/img/botones/personales/carro.jpg' /></a></div>
	<div id='divPerfil'><a class='boton' href='#' title='Perfil' ><img src='/img/botones/personales/perfil.jpg' /></a></div>
	<div id='divCtaCte'><a class='boton' href='/comercial/cuenta_corriente/'  title='Cta. Corriente'><img src='/img/botones/personales/ctacte.jpg' /></a></div>
	<div id='divInventario'><a class='boton' href='#'  title='Inventario'><img src='/img/botones/personales/inventario.jpg' /></a></div>
	<div id='divPendientes'><a class='boton' href='/comercial/pedidos/pendientes/' title='Pendientes' ><img src='/img/botones/personales/pendientes.jpg' /></a></div>
	<div id='divLogout'><a class='boton' href='/logout/' title='Logout' ><img src='/img/botones/personales/logout.jpg' /></a></div>
</div>
<?php } else { ?>
<div id='indicadores' class='fLeft customScroll'>
	<?php echo Usuario::logueado()->getHtmlIndicadores(); ?>
</div>
<div id='divNotificaciones' class='customScroll fRight' ng-controller='AppCtrl' on-load>
	<div class='notificacion fLeft cPointer corner10 {{not.clase()}}' ng-repeat='not in notificaciones | filter:{anulado: "N"} | orderBy:ordenadoPor:true'>
		<div class='div1 fLeft imagen corner5 bWhite' ng-click='not.goLink();'>
			<img ng-src='/img/notificaciones/{{not.imagen}}' />
		</div>
		<div class='div2 fLeft calibri white s15 bold' ng-click='not.goLink();'>
			<div class='nombre underline pBottom5'>{{not.nombre}}</div>
			<div class='detalle'>{{not.detalle}}</div>
		</div>
		<div class='div3 fLeft'>
			<div class='cPointer pBottom5' ng-show='not.vista == "N"' ng-click='not.visar();'><img src='/img/varias/tilde_notif.gif' /></div>
			<div class='cPointer pBottom5' ng-show='not.eliminable == "S"' ng-click='not.eliminar();'><img src='/img/varias/cruz_notif.gif' /></div>
		</div>
	</div>
</div>
<? if (Usuario::logueado()->mensajeHome) { ?>
<div style="
    background-color: darkred;
    color: white;
    font-weight: bold;
    width: 100%;
    text-align: center;
    height: 20px;
"><? echo Usuario::logueado()->mensajeHome; ?></div>
<? } ?>
<?php } ?>