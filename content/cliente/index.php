<?php   
    /**
     * Función simple para detectar si es un dispositivo móvil.
     * Retorna true si encuentra alguna palabra clave propia de un user-agent móvil.
     */
    var_dump("entro a cliente index");exit;
    function esDispositivoMovil() {
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $dispositivosMoviles = array('mobile', 'android', 'iphone', 'ipad', 'ipod', 'blackberry', 'windows phone');
        foreach ($dispositivosMoviles as $dispositivo) {
            if (strpos($userAgent, $dispositivo) !== false) {
                return true;
            }
        }
        return false;
    }


    /**
     * Determinar la carpeta según si el usuario navega en móvil o no.
     */
    if (esDispositivoMovil()) {
        $carpeta = Config::pathBase . 'img/fondos/catalogo_inicio_mobile';
        $rutaRelativa = '../img/fondos/catalogo_inicio_mobile/';


    } else {   var_dump("entro a no es movil");
                
                
                
        $carpeta = Config::pathBase . 'img/fondos/catalogo_inicio';
var_dump("carpeta: ".$carpeta);

        $rutaRelativa = '../img/fondos/catalogo_inicio/';
        var_dump("ruta relativa: ".$rutaRelativa);
    }

    /**
     * Obtener los archivos del directorio y filtrar solo JPG / PNG
     */
    $filesInDir = Funciones::getFilesFromDir($carpeta);
    $files = array();
    foreach ($filesInDir as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if ($ext == 'jpg' || $ext == 'png') {
            $files[] = $file;
        }
    }

    /**
     * Ajustar cada archivo para usar la ruta relativa apropiada.
     */
    foreach ($files as $key => $file) {
        $files[$key] = $rutaRelativa . $file;
    }
?>
<style>
    html {
        min-height: 100%;
    }
    body {
        background: none;
        margin: 0;
        padding: 0;
    }
    #full-bg {
      background-color: rgb(76 76 76);
        background-image: url("<?php echo ($files ? $files[0] : ''); ?>");
        background-size: cover; /* Mantener cover como base, ajustar con media queries */
        background-position: center top;
        background-repeat: no-repeat;
        background-attachment: fixed; /* Opcional, puedes quitarlo si causa problemas en móviles */
        min-height: 100vh; /* Usa viewport height para mejor adaptabilidad */
        width: 100%;
        position: relative; /* Corregido de position-area */
    }

    /* Media queries para móviles */
    @media (max-width: 768px) {
        #full-bg {
            background-size: contain; /* Evita recortes, muestra toda la imagen */
            /* background-position: center top; /* Ajusta la posición para móviles */ */
            min-height: 100vh; /* Asegura que cubra toda la pantalla */
            background-attachment: scroll; /* Quitar fixed en móviles para mejor scroll */
                  width: 105%;
        }
        .nombre-coleccion-lg {
            position: absolute;
            bottom: 10px; /* Reducido para móviles */
            right: 10px; /* Reducido para móviles */
            font-size: 24px; /* Tamaño de fuente más pequeño */
        }
        h1 {
            font-size: 24px; /* Ajuste para móviles */
        }
    }

    /* Estilos para escritorio */
    @media (min-width: 769px) {
        #full-bg {
            background-size: cover; /* Mantener cover en pantallas grandes */
        }
        .nombre-coleccion-lg {
            position: absolute;
            bottom: 20px;
            right: 40px;
        }
        h1 {
            font-size: 44px;
        }
    }

    #divContentCliente {
        padding: 0;
    }
    #page-container {
        padding: 0;
    }
    .nombre-coleccion-lg {
        position: absolute;
        bottom: 20px;
        right: 40px;
    }
    h1 {
        color: white;
        font-family: Arial, serif;
        margin-top: 10px;
        font-size: 44px;
    }
</style>
<script>
    var bgImages = <? echo ($files ? json_encode($files) : '""'); ?>;

    $('#full-bg').backgroundRotator({
      images: bgImages,
      initialImage: bgImages[0]
    });
</script>

<div class="visible-xs">
    <h1></h1>
</div>
<div class="nombre-coleccion-lg hidden-xs">
    <h1></h1>
</div>
