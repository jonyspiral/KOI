<?php
@apache_setenv('no-gzip','1');
@ini_set('zlib.output_compression', 0);
while (ob_get_level()) @ob_end_flush();
ob_implicit_flush(1);

require __DIR__ . '/premaster.php';
echo "<!-- MASTER MIN ENTER -->\n";
exit; // <-- aquí nos detenemos a propósito
