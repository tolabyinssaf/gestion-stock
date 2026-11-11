<?php

namespace App\Http\Requests\IT;

use Illuminate\Foundation\Http\FormRequest;

class SortieStockRequest extends FormRequest
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
                'nullable',
                'exists:emplacements,id',
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
            'beneficiaire' => [
                'nullable',
                'string',
                'max:150',
            ],
            'nouveau_statut' => [
                'nullable',
                'string',
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
            'quantite.min' => 'La quantité à sortir doit être supérieure à zéro.',
            'motif.required' => 'Le motif de la sortie est obligatoire.',
            'date_mouvement.before_or_equal' => 'La date ne peut pas être future.',
        ];
    }
}
