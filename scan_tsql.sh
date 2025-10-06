# crea reporte_incompatibilidades.txt
{
  echo "== SELECT TOP =="
  grep -nEi -C2 'SELECT\s+TOP\s+\d+' Mapper.php || echo "(sin coincidencias)"

  echo -e "\n== WITH (NOLOCK) =="
  grep -nEi -C2 'WITH\s*\(\s*NOLOCK\s*\)' Mapper.php || echo "(sin coincidencias)"

  echo -e "\n== Funciones T-SQL =="
  grep -nEi -C2 '\b(ISNULL|GETDATE|DATEDIFF|DATEADD|DATENAME|CONVERT|IIF|LEN)\s*\(' Mapper.php || echo "(sin coincidencias)"

  echo -e "\n== ROW_NUMBER OVER =="
  grep -nEi -C2 '\bROW_NUMBER\s*\(\)\s*OVER' Mapper.php || echo "(sin coincidencias)"

  echo -e "\n== IDENTITY / INSERTED =="
  grep -nEi -C2 '\bIDENTITY\s*\(|\bSCOPE_IDENTITY\s*\(|@@IDENTITY\b|OUTPUT\s+INSERTED\.' Mapper.php || echo "(sin coincidencias)"

  echo -e "\n== MERGE =="
  grep -nEi -C2 '(^|[;[:space:]])MERGE\b' Mapper.php || echo "(sin coincidencias)"

  echo -e "\n== OFFSET n ROWS / FETCH NEXT =="
  grep -nEi -C2 '\bOFFSET\s+\d+\s+ROWS|\bFETCH\s+NEXT\b' Mapper.php || echo "(sin coincidencias)"

  echo -e "\n== Tipos T-SQL =="
  grep -nEi -C2 '\b(NVARCHAR|NCHAR|NTEXT|UNIQUEIDENTIFIER|BIT|MONEY|SMALLMONEY)\b' Mapper.php || echo "(sin coincidencias)"

  echo -e "\n== Unicode N'cad' =="
  grep -nE  -C2 "N'" Mapper.php || echo "(sin coincidencias)"

  echo -e "\n== [identificadores] =="
  grep -nE  -C2 '\[[A-Za-z0-9_]+\]' Mapper.php || echo "(sin coincidencias)"

  echo -e "\n== Concatenación con + (strings) =="
  grep -nE  -C2 "'[^']*'\s*\+\s*'[^']*'" Mapper.php || echo "(sin coincidencias)"

  echo -e "\n== Tablas temporales #tmp =="
  grep -nEi -C2 '(^|[^\w])#{1,2}[A-Za-z0-9_]+' Mapper.php || echo "(sin coincidencias)"

  echo -e "\n== GO (batch) =="
  grep -nEi -C2 '(^|[;[:space:]])GO([;[:space:]]|$)' Mapper.php || echo "(sin coincidencias)"

  echo -e "\n== FOR XML / sp_executesql =="
  grep -nEi -C2 '\bFOR\s+XML\b|\bsp_executesql\b' Mapper.php || echo "(sin coincidencias)"
} > reporte_incompatibilidades.txt

echo "Reporte generado: reporte_incompatibilidades.txt"
