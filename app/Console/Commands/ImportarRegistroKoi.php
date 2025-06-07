<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportarRegistroKoi extends Command
{
    protected $signature = 'importar:registro {tabla} {--condiciones=*}';
    protected $description = 'Importa un registro específico desde SQL Server a MySQL, utilizando condiciones dinámicas.';

    public function handle()
    {
        $tabla = $this->argument('tabla');
        $condicionesInput = $this->option('condiciones');

        $condiciones = [];
        foreach ($condicionesInput as $cond) {
            if (strpos($cond, '=') === false) {
                $this->error("❌ Condición inválida: $cond (esperado campo=valor)");
                return;
            }
            list($campo, $valor) = explode('=', $cond, 2);
            $condiciones[$campo] = $valor;
        }

        if (empty($condiciones)) {
            $this->error('❌ Debes proporcionar al menos una condición con --condiciones=campo=valor');
            return;
        }

        try {
            $registro = DB::connection('sqlsrv_koi')
                ->table($tabla)
                ->where($condiciones)
                ->first();
        } catch (\Exception $e) {
            $this->error("❌ Error al consultar SQL Server: " . $e->getMessage());
            return;
        }

        if (!$registro) {
            $this->warn('⚠️ No se encontró ningún registro que cumpla las condiciones dadas.');
            return;
        }

        $registro = (array) $registro;
        $registro['sync_status'] = 'S';

        try {
            DB::table($tabla)->updateOrInsert($condiciones, $registro);
            $this->info('✅ Registro importado y actualizado en MySQL correctamente.');
        } catch (\Exception $e) {
            $this->error("❌ Error al insertar en MySQL: " . $e->getMessage());
        }
    }
}
