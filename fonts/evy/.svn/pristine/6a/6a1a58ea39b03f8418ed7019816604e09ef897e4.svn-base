<?php
$files = Funciones::getFilesFromDir(Config::pathBase . 'img/fondos/login');
foreach ($files as &$file) {
    $file = './img/fondos/login/' . $file;
}
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
	<head>
		<title><?php echo Config::pageTitle; ?></title>
		<meta http-equiv='Content-Type' content='text/html; utf-8'>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
		<link rel='shortcut icon' type='image/ico' href='<?php echo Config::siteRoot; ?>img/varias/favicon.ico' />
		<link href='<?php echo Config::siteRoot; ?>css/styles.css' rel='stylesheet' type='text/css' />
		<link href='<?php echo Config::siteRoot; ?>css/login.css' rel='stylesheet' type='text/css' />
		<script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/jquery.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/backgroundRotator/backgroundRotator.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/onEnterFocusNext/onEnterFocusNext.js'></script>
        <style>
            #full-bg {
                background-image: url("<? echo ($files ? $files[0] : '""'); ?>");
            }
        </style>
		<script type='text/javascript'>
			$(document).ready(function(){
				<?php if (isset($onDocumentReady)) echo $onDocumentReady; ?>
				$('input[name="user"]').focus();
				$('input').onEnterFocusNext();
				$('.change-empresa').click(
					function(){
						if ($('#empresa').val() == '1') {
							$('.login-box-boton-acceder').addClass('black');
							$('#empresa').val('2');
						} else {
							$('.login-box-boton-acceder').removeClass('black');
							$('#empresa').val('1');
						}
					}
				);

                var bgImages = <? echo ($files ? json_encode($files) : '""'); ?>;
                $('#full-bg').backgroundRotator({
                    images: bgImages,
                    initialImage: bgImages[0]
                });
			});
			function ifEnter(e){
				if (e.keyCode == 13)
					login();
			}
			function login(){
				if (validarLogin()){
					document.forms[0].submit();
				}
			}
			function validarLogin(){
				if ($('#user').val() == ''){
					$('#login-error-message').text('Por favor ingrese un nombre de usuario');
					$('#user').focus();
					return false;
				}
				if ($('#pass').val() == ''){
					$('#login-error-message').text('Por favor ingrese una contraseña');
					$('#pass').focus();
					return false;
				}
				$('#login-error-message').text('');
				return true;
			}
			function loginFail(causa){
				$('#login-error-message').text(causa);
			}
		</script>
	</head>
	<body>
        <div id="full-bg"></div>
        <div class="change-empresa"></div>
		<div id='divBody'>
			<div class='login-box'>
				<div class='login-box-inner'>
                    <!--
                    <div class="login-box-logos">
                        <div class="login-box-logo-koi"></div>
                        <div class="login-box-logo-spiral"></div>
                    </div>
                    -->
                    <form name='login-box-form' action='' method='POST'>
                        <div>
                            <input type="text" id="user" name="user" placeholder="usuario" class="login-input-username" />
                            <input type="password" id="pass" name="pass" placeholder="password" class="login-input-password" onkeypress="ifEnter(window.event);" />
                            <input type='text' id='empresa' name='empresa' value="1" class="hidden" />
                        </div>

                        <a href='javascript:login();' class="login-box-boton-acceder">login</a>
                        <div id='login-error-message'></div>
                    </form>
				</div>
			</div>
		</div>
	</body>
</html>