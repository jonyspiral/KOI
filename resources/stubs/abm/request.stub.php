<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\File;

class __MODELO__Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $campos = [];

        $path = resource_path("meta_abms/config_form___MODELO__.json");
        if (File::exists($path)) {
            $config = json_decode(File::get($path), true);
            $campos = $config['campos'] ?? [];
        }

        $rules = [];

        foreach ($campos as $campo => $conf) {
            if (!empty($conf['incluir'])) {
                $tipo = $conf['input_type'] ?? 'text';

                switch ($tipo) {
                    case 'number':
                        $rules[$campo] = 'nullable|numeric';
                        break;
                    case 'date':
                        $rules[$campo] = 'nullable|date';
                        break;
                    case 'checkbox':
                        $rules[$campo] = 'boolean';
                        break;
                    default:
                        $rules[$campo] = 'nullable|string';
                        break;
                }
            }
        }

        return $rules;
    }
}
