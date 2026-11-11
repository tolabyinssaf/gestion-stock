<?php

namespace App\Http\Requests\IT;

use Illuminate\Foundation\Http\FormRequest;

class CorrectionStockRequest extends FormRequest
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
            'emplacement_id' => [
                'required',
                'exists:emplacements,id',
            ],
            'nouvelle_quantite' => [
                'required',
                'integer',
                'min:0',
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
            'emplacement_id.required' => 'L\'emplacement est obligatoire.',
            'nouvelle_quantite.required' => 'La nouvelle quantité constatée est obligatoire.',
            'nouvelle_quantite.min' => 'La quantité ne peut pas être négative.',
            'motif.required' => 'Le motif de régularisation d\'inventaire est strictement obligatoire.',
        ];
    }
}
