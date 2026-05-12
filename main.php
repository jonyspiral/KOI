<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html ng-app='Koi'>
    <head>
        <title><?php echo Config::pageTitle . ' :: [' . Funciones::session('empresa') . '] ' . $tit; ?></title>
        <!--<meta http-equiv='Content-Type' content='text/html; utf-8' />-->
        <link rel='shortcut icon' type='image/ico' href='<?php echo Config::siteRoot; ?>img/varias/favicon.ico' />
        <link href='<?php echo Config::siteRoot; ?>css/styles.css' rel='stylesheet' type='text/css' />
        <link href='<?php echo Config::siteRoot; ?>css/jquery-ui/jquery-ui.css' rel='stylesheet' type='text/css' media='screen' charset='utf-8'/>
        <link href='<?php echo Config::siteRoot; ?>css/menu/menu.css' rel='stylesheet' type='text/css' media='screen' charset='utf-8'/>
        <link href='<?php echo Config::siteRoot; ?>js/acordeon/acordeon.css' rel='stylesheet' type='text/css' media='screen' charset='utf-8'/>
        <link href='<?php echo Config::siteRoot; ?>js/autoSuggestBox/autoSuggestBox.css' rel='stylesheet' type='text/css' media='screen' charset='utf-8'/>
        <link href='<?php echo Config::siteRoot; ?>js/checktree/checktree.css' rel='stylesheet' type='text/css' media='screen' charset='utf-8' />
        <link href='<?php echo Config::siteRoot; ?>js/draggableDialog/draggableDialog.css' rel='stylesheet' type='text/css' media='screen' charset='utf-8'/>
        <link href='<?php echo Config::siteRoot; ?>js/importes/importes.css' rel='stylesheet' type='text/css' media='screen' charset='utf-8'/>
        <link href='<?php echo Config::siteRoot; ?>js/jMsgBox/jMsgBox.css' rel='stylesheet' type='text/css' media='screen' charset='utf-8'/>
        <link href='<?php echo Config::siteRoot; ?>js/jPopUp/jPopUp.css' rel='stylesheet' type='text/css' media='screen' charset='utf-8'/>
        <link href='<?php echo Config::siteRoot; ?>js/loading/loading.css' rel='stylesheet' type='text/css' media='screen' charset='utf-8'/>
        <link href='<?php echo Config::siteRoot; ?>js/solapas/solapas.css' rel='stylesheet' type='text/css' media='screen' charset='utf-8'/>
        <link href='<?php echo Config::siteRoot; ?>js/tablaDinamica/tablaDinamica.css' rel='stylesheet' type='text/css' media='screen' charset='utf-8'/>
        <link href='<?php echo Config::siteRoot; ?>js/tabs/tabs.css' rel='stylesheet' type='text/css' media='screen' charset='utf-8'/>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/jquery.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/jquery-ui.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/jquery-easing.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/jquery-mousewheel.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/menu/menu.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/funciones.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/acordeon/acordeon.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/autoSuggestBox/autoSuggestBox.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/checktree/checktree.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/draggableDialog/draggableDialog.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/fixedHeader/fixedHeader.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/importes/importes.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/jMsgBox/jMsgBox.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/jPopUp/jPopUp.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/livequery/livequery.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/loading/loading.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/loadJSON/loadJSON.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/onEnterFocusNext/onEnterFocusNext.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/solapas/solapas.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/tablaDinamica/tablaDinamica.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/tabs/tabs.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/textSize/textSize.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/validate/validate.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/angular.min.js'></script>
        <!--suppress CheckValidXmlInScriptTagBody -->
        <script type='text/javascript'>
          //Variables Globales JS
          var Koi = angular.module('Koi', []);
          var funciones;
          var tituloPrograma;
          var nombreUsuarioLogueado;

          Koi.config(function($httpProvider) {
            $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
            $httpProvider.defaults.transformRequest = function(data){
              return data ? $.param(data) : data;
            };
          }).filter('fixed', function () {
            return function (n, len) {
              var num = parseInt(n, 10);
              len = parseInt(len, 10);
              if (isNaN(num) || isNaN(len)) {
                return n;
              }
              num = '' + num;
              while (num.length < len) {
                num = '0' + num;
              }
              return num;
            };
          });;

          //On Ready
          $(document).ready(function(){
            funciones = new Funciones();
            nombreUsuarioLogueado = '<?php echo Usuario::logueado(true)->nombreApellido; ?>';
            funciones.inicializarJQuery();
            menuKoi.init({mainmenuid: 'divMenu', classname: 'classMenu'});
            //var heartbeats = setInterval('heartbeat();', 5000);
            $('#inputBuscar').blur(function(){funciones.delay('buscar();');});
              <?php if (!empty($onDocumentReady)) echo $onDocumentReady; ?>
          });
          //Prevengo que apretar BACKSPACE vuelva a la página anterior
          $(document).unbind('keydown').bind('keydown', function (event) {
            var doPrevent = false;
            if (event.keyCode === 8) {
              var d = event.srcElement || event.target;
              if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD'))
                || d.tagName.toUpperCase() === 'TEXTAREA') {
                doPrevent = d.readOnly || d.disabled;
              } else {
                doPrevent = true;
              }
            }
            if (doPrevent) {
              event.preventDefault();
            }
          });
        </script>
        <style type="text/css">
            <?php if (Usuario::logueado(true)->esCliente()) { ?>
            /*noinspection CssUnusedSymbol*/
            #divMenu {
                display: none;
            }
            /*noinspection CssUnusedSymbol*/
            #divPanelBotones {
                padding-top: 13%;
                float: right;
                width: 480px;
            }
            #divPanelBotones div {
                float: left;
            }
            #divPanelBotones img {
                padding: 15px;
            }
            <?php } ?>
        </style>
        <?php
        /* CON ESTO, PUEDO HACER UNA CARPETA HEAD Y METER ALGO EN EL HEADER DE CADA PROGRAMA. EJ: head/abm/clientes/index.php
        if (!findRealPath('head/' . $pagename . '.php')) {
            require_once('head/index.php');
        } else {
            require_once('head/' . $pagename . '.php');
        }
        */
        ?>
    </head>
    <body  <? echo (Funciones::session('empresa') == '2' ? ' style="background-color: black;"' : 'style="background-color: #38d5cb29;"');?>>
    <div id='divBody'>
        <?php include('header.php'); ?>
        <div id='divContent'>
            <?php require('content/' . $pagename . '.php'); ?>
        </div>
        <?php include('footer.php'); ?>
    </div>
    </body>
</html>