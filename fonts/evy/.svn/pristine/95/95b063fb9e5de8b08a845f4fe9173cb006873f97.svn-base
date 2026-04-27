<?php
$css = array(
    'css/bootstrap.min.css',
    'css/jquery-ui/jquery-ui.css',
    'css/font-awesome/css/font-awesome.min.css',
    'css/animate.min.css',
    'css/menu/menu.css',
    'js/autoSuggestBox/autoSuggestBox.css',
    'js/growl/jquery.growl.css',
    'js/jMsgBox/jMsgBox.css',
    'js/jPopUp/jPopUp.css',
    'js/loading/loading.css',
    'js/autoSuggestBox/autoSuggestBox.css',
    'css/sidebar.css',
    'css/page-modal.css',
    'css/styles-responsive.css',
    'css/styles.css',
    'css/styles-cliente.css'
);
$js = array(
    'pre' => array(
        'js/jquery-1.9.js',
        'js/jquery-ui.js',
        'js/jquery-easing.js',
        'js/jquery-mousewheel.js',
        'js/angular.min.js',
        'js/angular-animate.js',
        'js/bootstrap.min.js',
        'js/ui-bootstrap.js',
        'js/funciones.js',
        'js/autoSuggestBox/autoSuggestBox.js',
        'js/backgroundRotator/backgroundRotator.js',
        'js/growl/jquery.growl.js',
        'js/jMsgBox/jMsgBox.js',
        'js/jPopUp/jPopUp.js',
        'js/livequery/livequery.js',
        'js/loading/loading.js',
        'js/loadJSON/loadJSON.js',
        'js/onEnterFocusNext/onEnterFocusNext.js',
        'js/slimScroll/jquery.slimscroll.js',
        'js/textSize/textSize.js',
        'js/validate/validate.js'
    ),
    'post' => array(
        'js/directives/sidebar.js',
        'js/directives/default-src.js',
        'js/directives/picture-modal.js',
        'js/services/cliente.js',
        'js/services/catalogo.js'
    )
);
?>

<!DOCTYPE html>
<html ng-app='Koi'>
    <head>
        <title><?php echo Config::pageTitle . ' :: Clientes :: ' . ($tit ? $tit : 'Inicio'); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
        <link rel='shortcut icon' type='image/ico' href='<?php echo Config::siteRoot; ?>img/varias/favicon.ico' />
        <?php
            foreach ($css as $cssPath) {
                echo '<link href="' . Config::siteRoot . $cssPath . '" rel="stylesheet" type="text/css" />';
            }
            foreach ($js['pre'] as $jsPath) {
                echo '<script type="text/javascript" src="' . Config::siteRoot . $jsPath . '"></script>';
            }
        ?>
        <script type='text/javascript'>
          var Koi = angular.module('Koi', []);
        </script>
        <?php
        foreach ($js['post'] as $jsPath) {
            echo '<script type="text/javascript" src="' . Config::siteRoot . $jsPath . '"></script>';
        }
        ?>
        <script type='text/javascript'>
          //Variables Globales JS
          var funciones = new Funciones();

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
          });

          function hideModal(event) {
            if (event.target.nodeName === 'IMG') {
              event.preventDefault();
            } else {
              $('#page-modal').hide();
            }
          }

          //On Ready
          $(document).ready(function(){
            funciones.inicializarJQuery();

            $('#page-modal').unbind('click').bind('click', hideModal);
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
    </head>

    <body>
        <div id="full-bg"></div>
        <div id="page-container" class="page-header-fixed">
            <a href="#" id="dummy-link"> </a>
            <?php include('content/cliente/menu.php'); ?>
            <?php include('content/cliente/usermenu.php'); ?>
            <div id='divContentCliente' class="content">
                <?php require('content/' . $pagename . '.php'); ?>
            </div>
            <?php include('content/cliente/mobilemenu.php'); ?>
        </div>

        <div id="page-modal" class="modal">
            <span class="modal-close">&times;</span>
            <img id="modal-image" class="modal-content" />
            <div id="modal-caption"></div>
        </div>
    </body>
</html>