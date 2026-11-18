<?php

namespace App\Http\Requests\IT;

use App\Models\Stock;
use Illuminate\Foundation\Http\FormRequest;

class ChangementSituationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isResponsableIt() || $this->user()->isAdmin());
    }

    public function rules(): array
    {
        return [
            'produit_id' => [
                'required',
                'exists:produits,id',
            ],
            'situation_depart' => [
                'required',
                'string',
                'in:' . implode(',', array_keys(Stock::SITUATIONS)),
            ],
            'situation_arrivee' => [
                'required',
                'string',
                'different:situation_depart',
                'in:' . implode(',', array_keys(Stock::SITUATIONS)),
            ],
            'quantite' => [
                'required',
                'integer',
                'min:1',
            ],
            'emplacement_source_id' => [
                'nullable',
                'exists:emplacements,id',
            ],
            'emplacement_destination_id' => [
                'nullable',
                'exists:emplacements,id',
            ],
            'motif' => [
                'nullable',
                'string',
                'max:255',
            ],
            'confirme_fonctionnel' => [
                'nullable',
                'boolean',
            ],
            'commentaire' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'date_mouvement' => [
                'nullable',
                'date',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'produit_id.required' => 'Veuillez sélectionner un matériel.',
            'produit_id.exists' => 'Le matériel sélectionné n\'existe pas.',
            'situation_depart.required' => 'Veuillez indiquer la situation actuelle.',
            'situation_arrivee.required' => 'Veuillez indiquer la nouvelle situation.',
            'situation_arrivee.different' => 'La nouvelle situation doit être différente de la situation actuelle.',
            'quantite.required' => 'Veuillez préciser la quantité à déplacer.',
            'quantite.min' => 'La quantité doit être au minimum de 1.',
            'motif.required' => 'Le motif du changement de situation est obligatoire.',
        ];
    }
}
