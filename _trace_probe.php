<?php
function mark($m){ file_put_contents("/tmp/k1.trace", date("H:i:s ").$m."\n", FILE_APPEND); }
mark("ENTER trace_probe");
require __DIR__."/includes.php"; mark("AFTER includes.php");
require __DIR__."/premaster.php"; mark("AFTER premaster.php");
require __DIR__."/master.php"; mark("AFTER master.php");
echo "TRACE_DONE\n";
