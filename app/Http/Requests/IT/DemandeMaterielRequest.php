<?php

namespace App\Http\Requests\IT;

use App\Models\DemandeMateriel;
use App\Models\Produit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DemandeMaterielRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isResponsableIt();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'produit_id' => [
                'required',
                Rule::exists('produits', 'id')->where(function ($query) {
                    $query->where('statut', Produit::STATUT_ACTIF);
                }),
            ],
            'quantite' => ['required', 'integer', 'min:1'],
            'priorite' => ['required', Rule::in(DemandeMateriel::PRIORITES)],
            'prix_estime' => ['nullable', 'numeric', 'min:0'],
            'motif' => ['nullable', 'string', 'max:255'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
            'destinataire_nom' => ['nullable', 'string', 'max:150'],
            'email_destinataire' => ['nullable', 'email', 'max:150'],
            'email_expediteur' => ['nullable', 'email', 'max:150'],
            'action_type' => ['nullable', Rule::in(['save_draft', 'prepare_gmail', 'update'])],
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'produit_id' => 'produit informatique',
            'quantite' => 'quantité demandée',
            'priorite' => 'priorité',
            'motif' => 'motif de la demande',
            'commentaire' => 'commentaire complémentaire',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'produit_id.exists' => 'Le produit sélectionné doit être un article actif du catalogue.',
            'quantite.min' => 'La quantité demandée doit être d\'au moins 1 unité.',
        ];
    }
}
