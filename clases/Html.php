<?php
// Fix de sensibilidad de mayúsculas/minúsculas en Linux.
// Si se pide HTML.php via autoloader, redirigimos a Html.php y aliasamos.
require_once __DIR__ . '/Html.php';
if (class_exists('Html', false) && !class_exists('HTML', false)) {
    class_alias('Html', 'HTML');
}
