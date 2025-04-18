<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestMysqlConnection extends Command
{
    protected $signature = 'debug:test-mysql';
    protected $description = 'Prueba directa de conexión a MySQL usando Laravel';

    public function handle()
    {
        try {
            $this->info("⏳ Probando conexión...");
            $result = DB::connection('mysql')->select('SELECT VERSION() as version');
            $this->info('✅ Conectado: ' . json_encode($result));
        } catch (\Throwable $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}
