<?php
@apache_setenv('no-gzip','1');
@ini_set('zlib.output_compression', 0);
while (ob_get_level()) @ob_end_flush();
ob_implicit_flush(1);

header('Content-Type: text/plain; charset=UTF-8');
echo "TOOLS OK\n";
