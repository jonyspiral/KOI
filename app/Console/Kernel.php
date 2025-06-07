<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Definir comandos programados si es necesario
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }

    protected $commands = [
        \App\Console\Commands\ImportarTablaKoi::class,
       \App\Console\Commands\SyncTablaCommand::class,
    ];
    
    protected $commands = [
        \App\Console\Commands\SetupAbmStubs::class,
    ];
    

}

