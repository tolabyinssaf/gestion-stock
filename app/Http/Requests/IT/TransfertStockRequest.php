<?php

namespace App\Http\Requests\IT;

use Illuminate\Foundation\Http\FormRequest;

class TransfertStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isResponsableIt();
    }

    public function rules(): array
    {
        return [
            'produit_id' => [
                'required',
                'exists:produits,id',
            ],
            'emplacement_source_id' => [
                'required',
                'exists:emplacements,id',
            ],
            'emplacement_destination_id' => [
                'required',
                'exists:emplacements,id',
                'different:emplacement_source_id',
            ],
            'quantite' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'equipement_id' => [
                'nullable',
                'exists:equipements,id',
            ],
            'motif' => [
                'required',
                'string',
                'max:255',
            ],
            'commentaire' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'date_mouvement' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'produit_id.required' => 'Le produit est obligatoire.',
            'emplacement_source_id.required' => 'L\'emplacement d\'origine est obligatoire.',
            'emplacement_destination_id.required' => 'L\'emplacement de destination est obligatoire.',
            'emplacement_destination_id.different' => 'La destination doit être différente de l\'emplacement d\'origine.',
            'motif.required' => 'Le motif du transfert est obligatoire.',
        ];
    }
}
