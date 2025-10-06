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
    mysqli_options($this->ln, MYSQLI_SET_CHARSET_NAME, 'utf8mb4');
  }

  if (!@mysqli_real_connect($this->ln, $host, $user, $pass, $db, $port)) {
    throw new Exception('MySQL connect error: '.mysqli_connect_error());
  }

  // Charset/collation: **una sola** configuración coherente
  mysqli_set_charset($this->ln, 'utf8mb4');
  mysqli_query($this->ln, "SET collation_connection = 'utf8mb4_0900_as_ci'");

  // NO usar $this->_link, NO hacer luego 'utf8'
  @mysqli_query($this->ln, "SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
  @mysqli_query($this->ln, "SET time_zone='-03:00'");
}


  function __destruct(){ if ($this->ln) { @mysqli_close($this->ln); } }

  /* Tx */
  function begin(){ if(!$this->inTx){ $this->exec("START TRANSACTION"); $this->inTx = true; } }
  function commit(){ if($this->inTx){ $this->exec("COMMIT"); $this->inTx = false; } }
  function rollback(){ if($this->inTx){ $this->exec("ROLLBACK"); $this->inTx = false; } }

  /* Lectura/Escritura */
    public function query($sql, $params = array()) {
    $t0  = microtime(true);
    $sql = $this->shim($sql);            // SQL final (sin T-SQL)
    $ok  = true; $err = ''; $rows = array();

    try {
        if (!empty($params)) {
            // Prepared
            $stmt = mysqli_prepare($this->ln, $sql);
            if ($stmt === false) { throw new Exception(mysqli_error($this->ln)); }

            // tipos + refs
            $types = ''; $bindParams = array();
            foreach ($params as $p) {
                if (is_int($p))      $types .= 'i';
                elseif (is_float($p))$types .= 'd';
                else                 $types .= 's';
                $bindParams[] = $p;
            }
            $bindNames = array($types);
            for ($i=0; $i<count($bindParams); $i++) { $bindNames[] = &$bindParams[$i]; }
            call_user_func_array(array($stmt, 'bind_param'), $bindNames);

            if (!mysqli_stmt_execute($stmt)) { throw new Exception(mysqli_stmt_error($stmt)); }

            $result = mysqli_stmt_get_result($stmt);
            if ($result instanceof mysqli_result) {
                while ($r = mysqli_fetch_assoc($result)) { $rows[] = $r; }
                mysqli_free_result($result);
            }
            mysqli_stmt_close($stmt);

        } else {
            // Directa
            $res = mysqli_query($this->ln, $sql);
            if ($res === false) { throw new Exception(mysqli_error($this->ln)); }
            if ($res instanceof mysqli_result) {
                while ($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
                mysqli_free_result($res);
            }
        }
        return $rows;

    } catch (Exception $ex) {
        $ok  = false;
        $err = $ex->getMessage();
        throw $ex;

    } finally {
        $ms = (int)((microtime(true) - $t0) * 1000);
        // ⚠️ Log **después** del shim, una sola vez
        error_log('[SQL][QUERY]['.$ms.'ms] '. $sql . (empty($params) ? '' : ' --params='.json_encode($params)) . ($ok ? '' : ' --ERROR='.$err));
    }
}

public function exec($sql, $params = array()) {
    $t0  = microtime(true);
    $sql = $this->shim($sql);
    $ok  = true; $err = ''; $aff = 0;

    try {
        if (!empty($params)) {
            $stmt = mysqli_prepare($this->ln, $sql);
            if ($stmt === false) { throw new Exception(mysqli_error($this->ln)); }

            $types = ''; $bindParams = array();
            foreach ($params as $p) {
                if (is_int($p))      $types .= 'i';
                elseif (is_float($p))$types .= 'd';
                else                 $types .= 's';
                $bindParams[] = $p;
            }
            $bindNames = array($types);
            for ($i=0; $i<count($bindParams); $i++) { $bindNames[] = &$bindParams[$i]; }
            call_user_func_array(array($stmt, 'bind_param'), $bindNames);

            if (!$stmt->execute()) { throw new Exception($stmt->error); }
            $aff = $stmt->affected_rows;
            $stmt->close();

        } else {
            $okQ = mysqli_query($this->ln, $sql);
            if ($okQ === false) { throw new Exception(mysqli_error($this->ln)); }
            $aff = mysqli_affected_rows($this->ln);
        }

        return $aff;

    } catch (Exception $ex) {
        $ok  = false;
        $err = $ex->getMessage();
        throw $ex;

    } finally {
        $ms = (int)((microtime(true) - $t0) * 1000);
        error_log('[SQL][EXEC]['.$ms.'ms]['.$aff.' rows] '. $sql . (empty($params) ? '' : ' --params='.json_encode($params)) . ($ok ? '' : ' --ERROR='.$err));
    }
}

  function queryOne($sql,$p=array()){ $r=$this->query($sql,$p); return $r? $r[0]:null; }
  function value($sql,$p=array()){ $r=$this->queryOne($sql,$p); return $r? array_shift($r):null; }

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
  private function shim($sql) {
    $r = $sql;
    // [col] → col
    $r = preg_replace('/\[([^\]]+)\]/', '$1', $r);
    // NOLOCK
    $r = preg_replace('/\s+WITH\s*\(\s*NOLOCK\s*\)/i', ' ', $r);
    // ISNULL → IFNULL
    $r = str_ireplace('ISNULL(', 'IFNULL(', $r);
    // LEN( → CHAR_LENGTH(
    $r = preg_replace('/\bLEN\s*\(/i', 'CHAR_LENGTH(', $r);
    // GETDATE() → NOW()
    $r = preg_replace('/\bGETDATE\s*\(\s*\)/i', 'NOW()', $r);
    // DATEDIFF(day, a, b) → DATEDIFF(b, a)
    $r = preg_replace('/\bDATEDIFF\s*\(\s*day\s*,\s*([^,]+)\s*,\s*([^)]+)\)/i', 'DATEDIFF($2, $1)', $r);
    // @@IDENTITY → LAST_INSERT_ID()
    $r = preg_replace('/@@IDENTITY\b/i', 'LAST_INSERT_ID()', $r);
    // TOP n → LIMIT n
    if (preg_match('/^\s*SELECT\s+TOP\s+(\d+)\s+/i', $r, $m)) {
        $n = (int)$m[1];
        $r = preg_replace('/^\s*SELECT\s+TOP\s+\d+\s+/i', 'SELECT ', $r);
        if (!preg_match('/\bLIMIT\s+\d+(\s*,\s*\d+)?\b/i', $r)) {
            $r = rtrim($r, " \t\n\r;") . " LIMIT " . $n;
        }
    }
    return $r; // 👈 shim NO loguea; logueamos en query/exec
}

      /* Helpers de seguridad/IDs */
    public function escape($value) {
        return mysqli_real_escape_string($this->ln, (string)$value);
    }

    public function lastInsertId() {
        return mysqli_insert_id($this->ln);
    }

}
