<?php
require __DIR__.'/../premaster.php';
header('Content-Type: text/plain; charset=utf-8');
$T=function(){static $t0; $t=microtime(true); if(!$t0)$t0=$t; return sprintf('%.3f',$t-$t0);};

echo "[0] start t=".$T()."\n";
try{ UsuarioLogin::login(); $u=Usuario::logueado(true);}catch(Exception $e){ echo "login EXC: ".$e->getMessage()."\n"; exit; }
echo "[1] login t=".$T()." user=".$u->id." esCliente=".($u->esCliente()?'SI':'NO')."\n";

$prefix=$u->esCliente()?'cliente/':'';
$aux=isset($_GET['pagename'])?trim($_GET['pagename'],'/'):'';
if($prefix==='cliente/' && strpos($aux,'cliente/')===0)$aux=substr($aux,8);
$p=($aux!==''?$prefix.$aux:$prefix.'index');
$f1=__DIR__."/../content/$p.php"; $f2=__DIR__."/../content/$p/index.php";
if(!is_file($f1) && is_file($f2))$p.='/index'; elseif(!is_file($f1) && !is_file($f2))$p=$prefix.'index';
echo "[2] resolver t=".$T()." pagename=$p\n";

$permKey=(substr($p,-6)==='/index')?substr($p,0,-6):$p;
$ok=false; $err='';
try{$ok=$u->puede($permKey);}catch(Exception $e){$err=$e->getMessage();}
echo "[3] puede() t=".$T()." permKey=$permKey puede=".($ok?'SI':'NO').($err?(" ERR=".$err):"")."\n";
if(!$ok){ $p=$prefix.'index'; }

$H=microtime(true); ob_start(); include __DIR__.'/../main.php'; $out=ob_get_clean();
echo "[4] render t=".sprintf('%.3f',microtime(true)-$H)." bytes=".strlen($out)."\n";
echo "\n--- OUT HEAD ---\n", substr($out,0,400), "\n--- /OUT HEAD ---\n";
