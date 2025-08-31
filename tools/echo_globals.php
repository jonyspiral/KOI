<?php
header('Content-Type: text/plain; charset=utf-8');
echo "REQUEST_URI: ", isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "(n/a)", "\n";
echo "QUERY_STRING: ", isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : "(n/a)", "\n";
echo "_GET=\n"; var_dump($_GET);
