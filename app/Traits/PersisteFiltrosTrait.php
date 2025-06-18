<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;


trait PersisteFiltrosTrait
{
    /**
     * Guarda, recupera o resetea filtros automáticamente en sesión.
     *
     * @param Request $request
     * @param string $claveSesion
     * @param array $camposPermitidos
     * @return Request
     */
public function manejarFiltros(Request $request, string $claveSesion, array $camposPermitidos): Request|RedirectResponse
    {
        // Resetear filtros si se indica
        if ($request->get('reset') === '1') {
            session()->forget($claveSesion);
            return redirect()->route($request->route()->getName()); // redirige a la misma ruta sin filtros
        }

        // Guardar en sesión solo los campos permitidos
        if ($request->isMethod('get') && $request->query()) {
            $filtros = collect($request->query())->only($camposPermitidos)->toArray();
            session([$claveSesion => $filtros]);
        }

        // Aplicar filtros guardados
        $filtrosGuardados = session($claveSesion, []);
        $request->merge($filtrosGuardados);

        return $request;
    }
}
