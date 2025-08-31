<?php
/**
 * DbMysql — driver MySQL para KOI1 (PHP 5.6)
 * - Handshake charset: utf8 (utf8mb3)
 * - Sesión: sql_mode y time_zone
 * - query/exec/value/queryOne, transacciones y CALL con múltiples resultsets
 * - Shim T-SQL → MySQL (mínimo)
 */
class DbMysql {
  private $ln;
  private $inTx = false;

  function __construct($cfg){
    $host = isset($cfg['host']) ? $cfg['host'] : '127.0.0.1';
    $user = isset($cfg['user']) ? $cfg['user'] : '';
    $pass = isset($cfg['pass']) ? $cfg['pass'] : '';
    $db   = isset($cfg['name']) ? $cfg['name'] : '';
    $port = isset($cfg['port']) ? (int)$cfg['port'] : 3306;
    $timeout = isset($cfg['timeout']) ? (int)$cfg['timeout'] : 5;

    $this->ln = mysqli_init();
    mysqli_options($this->ln, MYSQLI_OPT_CONNECT_TIMEOUT, $timeout);
    if (defined('MYSQLI_SET_CHARSET_NAME')) {
      mysqli_options($this->ln, MYSQLI_SET_CHARSET_NAME, 'utf8'); // clave para 5.x
    }
    if (!@mysqli_real_connect($this->ln, $host, $user, $pass, $db, $port)) {
      throw new Exception('MySQL connect error: '.mysqli_connect_error());
    }
    mysqli_set_charset($this->ln, 'utf8');
    @mysqli_query($this->ln, "SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    @mysqli_query($this->ln, "SET time_zone='-03:00'");
  }

  function __destruct(){ if ($this->ln) { @mysqli_close($this->ln); } }

  /* Tx */
  function begin(){ if(!$this->inTx){ $this->exec("START TRANSACTION"); $this->inTx = true; } }
  function commit(){ if($this->inTx){ $this->exec("COMMIT"); $this->inTx = false; } }
  function rollback(){ if($this->inTx){ $this->exec("ROLLBACK"); $this->inTx = false; } }

  /* Lectura/Escritura */
  function query($sql, $params=array()){
    $sql = $this->shim($sql);
    if ($params) {
      $stmt = $this->prep($sql,$params); if(!$stmt->execute()) throw new Exception($stmt->error);
      $res = $stmt->get_result(); $out=array(); if($res){ while($r=$res->fetch_assoc()) $out[]=$r; $res->free(); }
      $stmt->close(); return $out;
    } else {
      $res = mysqli_query($this->ln,$sql); if($res===false) throw new Exception(mysqli_error($this->ln));
      $out=array(); if($res instanceof mysqli_result){ while($r=mysqli_fetch_assoc($res)) $out[]=$r; mysqli_free_result($res); }
      return $out;
    }
  }
  function queryOne($sql,$p=array()){ $r=$this->query($sql,$p); return $r? $r[0]:null; }
  function value($sql,$p=array()){ $r=$this->queryOne($sql,$p); return $r? array_shift($r):null; }
  function exec($sql,$p=array()){
    $sql = $this->shim($sql);
    if ($p){ $s=$this->prep($sql,$p); if(!$s->execute()) throw new Exception($s->error); $aff=$s->affected_rows; $s->close(); return $aff; }
    $ok = mysqli_query($this->ln,$sql); if($ok===false) throw new Exception(mysqli_error($this->ln)); return mysqli_affected_rows($this->ln);
  }

  /* CALL con múltiples resultsets */
  function call($callSql){
    $callSql = $this->shim($callSql);
    $sets = array();
    if (!mysqli_multi_query($this->ln,$callSql)) throw new Exception(mysqli_error($this->ln));
    do {
      if ($res = mysqli_store_result($this->ln)) {
        $rows=array(); while($row=mysqli_fetch_assoc($res)) $rows[]=$row; mysqli_free_result($res);
        $sets[] = $rows;
      }
    } while (mysqli_more_results($this->ln) && mysqli_next_result($this->ln));
    return $sets;
  }

  /* Internos */
  private function prep($sql,$p){
    $s = mysqli_prepare($this->ln,$sql); if(!$s) throw new Exception(mysqli_error($this->ln));
    $types=''; $vals=array();
    foreach($p as $v){ $types.= is_int($v)?'i':(is_float($v)?'d':'s'); $vals[]=$v; }
    $refs=array(); $refs[]=&$types; for($i=0;$i<count($vals);$i++){ $refs[$i+1]=&$vals[$i]; }
    call_user_func_array(array($s,'bind_param'),$refs);
    return $s;
  }
  private function shim($sql){
    $r=$sql;
    $r=preg_replace('/\[([^\]]+)\]/','$1',$r);                 // [col] → col
    $r=preg_replace('/\s+WITH\s*\(\s*NOLOCK\s*\)/i',' ',$r);   // NOLOCK
    $r=str_ireplace('ISNULL(','IFNULL(',$r);
    $r=preg_replace('/\bLEN\s*\(/i','CHAR_LENGTH(',$r);
    $r=preg_replace('/\bGETDATE\s*\(\s*\)/i','NOW()',$r);
    if (preg_match('/^\s*SELECT\s+TOP\s+(\d+)\s+/i',$r,$m)){   // TOP n → LIMIT n
      $n=(int)$m[1]; $r=preg_replace('/^\s*SELECT\s+TOP\s+\d+\s+/i','SELECT ',$r);
      if(!preg_match('/\bLIMIT\s+\d+/i',$r)) $r=rtrim($r," \t\n\r;")." LIMIT ".$n;
    }
    return $r;
  }
}
