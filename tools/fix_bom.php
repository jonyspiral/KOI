<?php
ini_set('display_errors',1); error_reporting(E_ALL);

$targets = [
  '/var/www/encinitas/factory/Factory.php',
  '/var/www/encinitas/includes.php',
  '/var/www/encinitas/premaster.php',
  '/var/www/encinitas/master.php',
  '/var/www/encinitas/login.php',
];

function strip_bom($f){
  if (!file_exists($f)) return "SKIP (no existe)  $f\n";
  $data = file_get_contents($f);
  $changed = false;

  // BOM UTF-8
  if (substr($data,0,3) === "\xEF\xBB\xBF") {
    $data = substr($data,3);
    $changed = true;
  }
  // Espacios/lineas antes de <?php (a veces queda un \n o espacios)
  $data2 = ltrim($data, "\x00\x09\x0B\x0C\x0D\x20"); // no quitamos \n principal si <?php no aparece
  if ($data2 !== $data) {
    // aseguramos que arranque con '<?php'
    if (strpos($data2, '<?php') === 0) { $data = $data2; $changed = true; }
  }

  if ($changed) {
    file_put_contents($f, $data);
    return "FIXED: $f\n";
  }
  return "OK   : $f (sin BOM/espacios antes de <?php)\n";
}

foreach ($targets as $f) echo strip_bom($f);
echo "DONE\n";
