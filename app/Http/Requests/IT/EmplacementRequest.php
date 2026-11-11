<?php

namespace App\Http\Requests\IT;

use App\Models\Emplacement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmplacementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isResponsableIt();
    }

    public function rules(): array
    {
        $id = $this->route('emplacement')?->id ?? $this->route('emplacement');

        return [
            'nom' => [
                'required',
                'string',
                'max:100',
                Rule::unique('emplacements', 'nom')->ignore($id),
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('emplacements', 'code')->ignore($id),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'statut' => [
                'required',
                'string',
                Rule::in([Emplacement::STATUT_ACTIF, Emplacement::STATUT_ARCHIVE]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de l\'emplacement est obligatoire.',
            'nom.unique' => 'Cet emplacement existe déjà.',
            'code.required' => 'Le code de l\'emplacement est obligatoire.',
            'code.unique' => 'Ce code d\'emplacement est déjà utilisé.',
        ];
    }
}
