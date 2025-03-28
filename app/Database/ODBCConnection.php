<?php

namespace App\Database;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Grammars\SqlServerGrammar as QuerySqlServerGrammar;
use Illuminate\Database\Schema\Grammars\SqlServerGrammar as SchemaSqlServerGrammar;
use Illuminate\Database\Query\Processors\Processor;
use PDO;

class ODBCConnection extends Connection
{
    protected $connection;

    public function __construct(PDO $connection, $database, $tablePrefix = '', array $config = [])
    {
        $this->connection = $connection;
        $this->database = $database;
        $this->tablePrefix = $tablePrefix;
        $this->config = $config;

        // Solo establecer el modo de error, dejar otras configuraciones predeterminadas
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        parent::__construct($connection, $database, $tablePrefix, $config);
    }

    public function getPdo()
    {
        if (!$this->connection instanceof PDO) {
            throw new \RuntimeException('PDO connection is not initialized.');
        }
        return $this->connection;
    }

    protected function getDefaultQueryGrammar()
    {
        return new QuerySqlServerGrammar($this);
    }
    
    protected function getDefaultSchemaGrammar()
    {
        return new SchemaSqlServerGrammar($this);
    }
    
    protected function getDefaultPostProcessor()
    {
        return new Processor();
    }
    
    public function getDatabaseName()
    {
        return $this->database;
    }

    public function getDriverName()
    {
        return 'odbc';
    }
}