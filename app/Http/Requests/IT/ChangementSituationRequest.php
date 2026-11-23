<?php

namespace App\Http\Requests\IT;

use App\Models\Stock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangementSituationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isResponsableIt() || $this->user()->isAdmin());
    }

    public function rules(): array
    {
        $situationsValides = [
            Stock::SITUATION_EN_STOCK,
            Stock::SITUATION_EN_UTILISATION,
            Stock::SITUATION_EN_PANNE,
        ];

        return [
            'produit_id' => [
                'required',
                'integer',
                'exists:produits,id',
            ],
            'situation_depart' => [
                'required',
                'string',
                Rule::in($situationsValides),
            ],
            'situation_arrivee' => [
                'required',
                'string',
                Rule::in($situationsValides),
                'different:situation_depart',
            ],
            'quantite' => [
                'required',
                'integer',
                'min:1',
            ],
            'emplacement_source_id' => [
                'nullable',
                'integer',
                'exists:emplacements,id',
            ],
            'emplacement_destination_id' => [
                'nullable',
                'integer',
                'exists:emplacements,id',
            ],
            'motif' => [
                'nullable',
                'string',
                'max:255',
            ],
            'confirme_fonctionnel' => [
                'nullable',
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
            'produit_id.required'         => 'Veuillez sélectionner un matériel.',
            'produit_id.exists'           => 'Le matériel sélectionné n\'existe pas.',
            'situation_depart.required'   => 'Veuillez indiquer la situation actuelle.',
            'situation_depart.in'         => 'La situation actuelle est invalide.',
            'situation_arrivee.required'  => 'Veuillez indiquer la nouvelle situation.',
            'situation_arrivee.in'        => 'La nouvelle situation est invalide.',
            'situation_arrivee.different' => 'La nouvelle situation doit être différente de la situation actuelle.',
            'quantite.required'           => 'Veuillez préciser la quantité à déplacer.',
            'quantite.min'                => 'La quantité doit être au minimum de 1.',
            'date_mouvement.date'         => "La date n'est pas valide.",
        ];
    }

    /**
     * Normalisation AVANT validation :
     * - Force confirme_fonctionnel à un booléen strict (la checkbox HTML n'envoie rien si décochée)
     * - Injecte la date du jour si non fournie
     */
    protected function prepareForValidation(): void
    {
        $confirme = $this->input('confirme_fonctionnel');

        if (is_null($confirme) || $confirme === '' || $confirme === '0' || $confirme === 0 || $confirme === false) {
            $confirmeBool = false;
        } else {
            $confirmeBool = filter_var($confirme, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
        }

        $this->merge(['confirme_fonctionnel' => $confirmeBool]);

        if (!$this->filled('date_mouvement')) {
            $this->merge(['date_mouvement' => now()->toDateTimeString()]);
        }
    }

    /**
     * Validation métier après les règles principales.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $depart   = $this->input('situation_depart');
            $arrivee  = $this->input('situation_arrivee');
            $confirme = (bool) $this->input('confirme_fonctionnel', false);

            // Confirmation obligatoire pour remise en service depuis "en_panne"
            if ($depart === Stock::SITUATION_EN_PANNE && !$confirme) {
                $validator->errors()->add(
                    'confirme_fonctionnel',
                    'Veuillez confirmer que le matériel est de nouveau fonctionnel avant de le remettre en service.'
                );
            }

            // Interdire les transitions absurdes
            if ($depart === $arrivee) {
                $validator->errors()->add(
                    'situation_arrivee',
                    'La nouvelle situation doit être différente de la situation actuelle.'
                );
            }
        });
    }
}