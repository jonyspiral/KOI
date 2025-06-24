<?php

namespace App\Services\Mlibre;

use App\Models\MlVariante;
use Illuminate\Support\Facades\Http;

class MlSyncService
{
    protected string $modo = 'seleccionados';
    protected array $ids = [];
    protected string $campo = 'stock'; // 'stock' o 'precio'
    protected string $marketplace = 'mlibre';

    public function setModo(string $modo): static
    {
        $this->modo = $modo;
        return $this;
    }

    public function setCampo(string $campo): static
    {
        $this->campo = $campo;
        return $this;
    }

    public function setMarketplace(string $marketplace): static
    {
        $this->marketplace = $marketplace;
        return $this;
    }

    public function setIds(array $ids): static
    {
        $this->ids = $ids;
        return $this;
    }

   public function sync(): array
{
    \Log::info("[SYNC] Iniciando sincronización modo={$this->modo}, campo={$this->campo}");

    $query = MlVariante::query()->with('skuVariante');

    if ($this->modo === 'filtrados') {
        \Log::info("[SYNC] Modo: filtrados");
        // viene prefiltrado desde el controlador
    } elseif ($this->modo === 'seleccionados') {
        \Log::info("[SYNC] Modo: seleccionados - IDS: ", $this->ids);
        $query->whereIn('id', $this->ids);
    } elseif ($this->modo === 'pendientes') {
        \Log::info("[SYNC] Modo: pendientes");
        $query->where('sync_status', 'U');
    }

    $variantes = $query->get();
    \Log::info("[SYNC] Cantidad de variantes a sincronizar: " . $variantes->count());

    $ok = 0;
    $errors = 0;

    foreach ($variantes as $v) {
        \Log::info("[SYNC] Procesando variante ID={$v->id}, ml_id={$v->ml_id}, SCF={$v->seller_custom_field}");

        $resultado = match ($this->campo) {
            'stock'  => $this->syncStock($v),
            'precio' => $this->syncPrecio($v),
            'global' => $this->syncStock($v) & $this->syncPrecio($v),
            default  => false,
        };

        if ($resultado === true) {
            \Log::info("[SYNC] ✅ Variante ID={$v->id} sincronizada OK");
            $ok++;
        } else {
            \Log::warning("[SYNC] ❌ Variante ID={$v->id} con error");
            $errors++;
        }
    }

    \Log::info("[SYNC] Finalizado. OK={$ok}, Errors={$errors}");

    return compact('ok', 'errors');
}


 protected function syncStock(MlVariante $v): bool
{
    \Log::info("[syncStock] Iniciando variante ID={$v->id} | SCF={$v->seller_custom_field}");

    if (!$v->skuVariante) {
        $v->sync_status_stock = 'E';
        $v->sync_log_stock = '❌ No tiene SKU asociado';
        $v->save();
        return false;
    }

    $resultado = $v->actualizarStockML(); // Este método ya guarda sync_status_stock y sync_log_stock

    \Log::info("[syncStock] Resultado variante ID={$v->id} | Status={$v->sync_status_stock} | Log={$v->sync_log_stock}");

    return $resultado;
}

    protected function syncPrecio(MlVariante $v): bool
{
    if (!$v->ml_id || !$v->skuVariante) {
        $v->markAsError('❌ Sin ml_id o SKU', 'precio');
        return false;
    }

    $precio = match ($this->marketplace) {
        'mlibre'  => $v->skuVariante->ml_price,
        'eshop'   => $v->skuVariante->eshop_price,
        'segunda' => $v->skuVariante->segunda_price,
        default   => null,
    };

    if ($precio === null) {
        $v->markAsError("❌ Precio no definido para {$this->marketplace}", 'precio');
        return false;
    }

    $token = app(MlibreTokenService::class)->getValidAccessToken();
    $response = Http::withToken($token)->put("https://api.mercadolibre.com/items/{$v->ml_id}", [
        'price' => $precio,
    ]);

    if ($response->ok()) {
        $v->precio = $precio;
        $v->markAsSynced('precio', "Precio actualizado a $precio");
        return true;
    }

    $v->markAsError("❌ Error al actualizar precio: {$response->status()} - {$response->body()}", 'precio');
    return false;
}

}
