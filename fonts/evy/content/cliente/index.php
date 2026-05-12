<?php
    $files = Funciones::getFilesFromDir(Config::pathBase . 'img/fondos/catalogo_inicio');
    foreach ($files as &$file) {
        $file = '../img/fondos/catalogo_inicio/' . $file;
    }
?>
<style>
    html {
        min-height: 100%;
    }
    body {
        background: none;
    }
    #full-bg {
        background-image: url("<? echo ($files ? $files[0] : '""'); ?>");
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
        min-height: 100%;
        width: 100%;
        position: absolute;
        background-repeat: no-repeat;
        background-position: center center;
        background-attachment: fixed;
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
    <h1>SUMMER 2018</h1>
</div>
<div class="nombre-coleccion-lg hidden-xs">
    <h1>SUMMER 2018</h1>
</div>