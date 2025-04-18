<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VerificarExtensiones extends Command
{
    protected $signature = 'sistema:verificar-extensiones';
    protected $description = 'Verifica que las extensiones necesarias para KOI estén cargadas en PHP';

    public function handle()
    {
        $extensiones = [
            'pdo',
            'pdo_mysql',
            'pdo_odbc',
            'mbstring',
            'curl',
            'openssl',
            'json',
            'xml',
            'fileinfo',
            'tokenizer',
            'bcmath',
        ];

        $this->info("🔍 Verificando extensiones de PHP necesarias para KOI...");

        $faltantes = [];

        foreach ($extensiones as $ext) {
            if (!extension_loaded($ext)) {
                $this->error("❌ Falta la extensión: $ext");
                $faltantes[] = $ext;
            } else {
                $this->line("✅ $ext");
            }
        }

        if (count($faltantes) > 0) {
            $this->warn("\n⚠️ Algunas extensiones faltan. Por favor instalalas antes de continuar.");
        } else {
            $this->info("\n🎉 Todo OK. Todas las extensiones necesarias están cargadas.");
        }
    }
}

