<?php
// Obtener archivos del directorio
$files = Funciones::getFilesFromDir(Config::pathBase . 'img/fondos/login');
// print_r($files);
// Filtrar solo archivos de imagen válidos (compatible con PHP 5.2.9)
$image_files = array();
$valid_extensions = array('jpg', 'jpeg', 'png', 'gif'); // Extensiones permitidas
foreach ($files as $file) {
    $path_parts = pathinfo($file);
    $extension = strtolower(isset($path_parts['extension']) ? $path_parts['extension'] : '');
    if (in_array($extension, $valid_extensions)) {
        $image_files[] = $file;
    }
}

// Asignar imágenes con valores por defecto si no hay suficientes
$file_desktop = './img/fondos/login/' . (isset($image_files[0]) ? $image_files[0] : 'default-desktop.jpg');
$file_mobile = './img/fondos/login/' . (isset($image_files[1]) ? $image_files[1] : 'default-mobile.jpg');

// Depuración (compatible con PHP 5.2.9)
echo "<!-- Debug: Desktop file = $file_desktop, Mobile file = $file_mobile -->";
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
	<head>
		<title><?php echo Config::pageTitle; ?></title>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0, minimum-scale=1.0'>
		<link rel='shortcut icon' type='image/ico' href='<?php echo Config::siteRoot; ?>img/varias/favicon.ico' />
		<link href='<?php echo Config::siteRoot; ?>css/styles.css' rel='stylesheet' type='text/css' />
		<link href='<?php echo Config::siteRoot; ?>css/login.css' rel='stylesheet' type='text/css' />
		<link href='https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap' rel='stylesheet'> <!-- Fuente moderna para navegadores actuales -->
		<script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/jquery.js'></script>
        <script type='text/javascript' src='<?php echo Config::siteRoot; ?>js/onEnterFocusNext/onEnterFocusNext.js'></script>
        <style type='text/css'>
        /* Reset básico */
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    font-family: 'Roboto', Arial, sans-serif;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  /* Fondo con imagen dinámica */
  #full-bg {
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: -1;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3)),
                url('<?php echo $file_desktop; ?>') no-repeat center center;
    background-size: cover;
  }
  /* Botón de cambio de empresa (ahora circular) */
  .change-empresa {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 50px; /* Tamaño del círculo */
    height: 50px;
    background: transparent;
    border: 2px solid #000;
    border-radius: 50%;
    cursor: pointer;
    transition: 0.3s;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .change-empresa:hover {
    background: #000;
    color: white;
  }

  @media only screen and (max-width: 768px) {
    #full-bg {
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.5)),
                    url('<?php echo $file_mobile; ?>') no-repeat center center;
        background-size: cover;
    }
  }

  /* Contenedor principal */
  #divBody {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    padding: 10px;
  }

  /* Caja de login */
  .login-box-inner {
    width: 70%;
    max-width: 300px;
    padding: 0; /* Eliminado el padding extra */
    text-align: center;
    position: relative; /* No más efecto vidriado */
  }

  /* Inputs alineados */
  .login-input-username,
  .login-input-password {
    width: 70%;
    padding: 12px;
    margin: 8px 0; /* Espaciado uniforme */
    border: 2px solid #000;
    border-radius: 20px;
    font-size: 16px;
    background: transparent;
    color: #000;
    text-align: center;
    outline: none;
    transition: 0.3s;
  }

  /* Placeholder en color gris */
  .login-input-username::placeholder,
  .login-input-password::placeholder {
    color: rgba(0, 0, 0, 0.5);
  }

  /* Efecto en focus */
  .login-input-username:focus,
  .login-input-password:focus {
    border-color: #000;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
  }

  /* Botón de login alineado */
  .login-box-boton-acceder {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 50px;
    height: 50px;
    margin: 15px auto 0; /* Alineado con los inputs */
    background: #333;
    color: white;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
    border-radius: 50%;
    border: none;
        transition: 0.3s;
    padding: 0px 0;
  }

  .login-box-boton-acceder:hover {
    background: #222;
    transform: scale(1.05);
  }

  /* Estado alternativo en negro */
  .login-box-boton-acceder.black {
    background: #000;
  }

  .login-box-boton-acceder.black:hover {
    background: #222;
  }

  /* Mensaje de error */
  #login-error-message {
    color: #dc3545;
    font-size: 14px;
    margin-top: 5px
}
        </style>
		<script type='text/javascript'>
			// Asegurar compatibilidad con jQuery 1.7
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
        <div id='full-bg'></div>
        <div class='change-empresa'></div> <!-- Círculo vacío negro con borde blanco -->
		<div id='divBody'>
			<div class='login-box-inner'>
                <form name='login-box-form' action='' method='POST'>
                    <div>
                        <input type='text' id='user' name='user' placeholder='user' class='login-input-username' />
                        <input type='password' id='pass' name='pass' placeholder='password' class='login-input-password' onkeypress='ifEnter(window.event);' />
                        <a href='javascript:login();' class='login-box-boton-acceder'>Login</a>
                        <div id='login-error-message'></div>
                        <input type='text' id='empresa' name='empresa' value='1' class='hidden' />
                    </div>
                </form>
			</div>
		</div>
	</body>
</html>
