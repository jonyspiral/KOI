<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DeployKoi2 extends Command
{
    protected $signature = 'deploy:koi2';
    protected $description = 'Realiza el deploy de koi2_v1 a koi2 con limpieza de caches y restart de Apache';

    public function handle()
    {
        $this->info('🔁 Iniciando deploy a producción KOI2...');

        // Paso 1: Rsync de archivos
        $this->info('📦 Copiando archivos con rsync...');
        $rsync = new Process(['sudo', 'rsync', '-av', '--exclude=.env', '/var/www/koi2_v1/', '/var/www/koi2/']);
        $rsync->setTimeout(300);
        $rsync->run(function ($type, $buffer) {
            echo $buffer;
        });

        // Paso 2: Clear de caches
        $this->info('🧹 Limpiando caches de Laravel...');
        foreach (['config:clear', 'cache:clear', 'view:clear', 'route:clear', 'optimize'] as $cmd) {
            $this->call($cmd);
        }

        // Paso 3: Reiniciar Apache
        $this->info('🔁 Reiniciando Apache...');
        $restart = new Process(['sudo', 'systemctl', 'restart', 'apache2']);
        $restart->run();

        if (!$restart->isSuccessful()) {
            $this->error('❌ Error al reiniciar Apache');
            return 1;
        }

        $this->info('✅ Deploy completado exitosamente. KOI2 en producción.');
        return 0;
    }
}
