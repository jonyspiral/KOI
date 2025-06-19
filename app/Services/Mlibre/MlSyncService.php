<?php
namespace App\Services\Mlibre;

use App\Models\MlVariante;
use Illuminate\Support\Facades\Http;

class MlSyncService
{
    protected string $modo;
    protected array $ids = [];
    protected string $campo = 'stock'; // stock o precio
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
        $query = MlVariante::query()->with('skuVariante');

        if ($this->modo === 'filtrados') {
            // ya viene filtrado desde el controlador
        } elseif ($this->modo === 'seleccionados') {
            $query->whereIn('id', $this->ids);
        } elseif ($this->modo === 'pendientes') {
            $query->where('sync_status', 'U');
        }

        $variantes = $query->get();
        $ok = 0;
        $errors = 0;

        foreach ($variantes as $v) {
            $resultado = match ($this->campo) {
                'stock' => $this->syncStock($v),
                'precio' => $this->syncPrecio($v),
                default => false,
            };

            if ($resultado === true) {
                $ok++;
            } else {
                $errors++;
            }
        }

        return compact('ok', 'errors');
    }

    protected function syncStock(MlVariante $v): bool
    {
        return $v->actualizarStockML(); // ya implementado
    }

    protected function syncPrecio(MlVariante $v): bool
    {
        if (!$v->ml_id || !$v->skuVariante) {
            $v->markAsError("❌ Falta ml_id o SKU");
            return false;
        }

        $precio = match ($this->marketplace) {
            'mlibre' => $v->skuVariante->ml_price,
            'eshop'  => $v->skuVariante->eshop_price,
            'segunda' => $v->skuVariante->segunda_price,
            default => null,
        };

        if ($precio === null) {
            $v->markAsError("❌ Precio no definido para {$this->marketplace}");
            return false;
        }

        $token = app(MlibreTokenService::class)->getValidAccessToken();

        $put = Http::withToken($token)->put("https://api.mercadolibre.com/items/{$v->ml_id}", [
            'price' => $precio
        ]);

        if ($put->ok()) {
            $v->precio = $precio;
            $v->sync_status = 'S';
            $v->sync_log = "✅ Precio actualizado a $precio";
            $v->save();
            return true;
        } else {
            $v->markAsError("❌ Error al actualizar precio: " . $put->status() . " - " . $put->body());
            return false;
        }
    }
}
