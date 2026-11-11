<?php

namespace App\Http\Requests\IT;

use App\Models\Equipement;
use App\Models\Produit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEquipementRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'produit_id' => [
                'required',
                'exists:produits,id',
                function ($attribute, $value, $fail) {
                    $produit = Produit::find($value);
                    if ($produit && !$produit->isIndividuel()) {
                        $fail("Le produit sélectionné « {$produit->nom} » n'est pas configuré pour un suivi individuel (suivi par lot/quantité). Seuls les modèles à suivi sérialisé peuvent faire l'objet d'un équipement unitaire.");
                    }
                },
            ],
            'code_inventaire' => [
                'required',
                'string',
                'max:50',
                Rule::unique('equipements', 'code_inventaire'),
            ],
            'numero_serie' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('equipements', 'numero_serie'),
            ],
            'etat_physique' => [
                'required',
                'string',
                Rule::in(array_keys(Equipement::ETATS_PHYSIQUES)),
            ],
            'statut_utilisation' => [
                'required',
                'string',
                Rule::in(array_keys(Equipement::STATUTS_UTILISATION)),
            ],
            'emplacement_id' => [
                'nullable',
                'exists:emplacements,id',
            ],
            'date_entree' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            'observations' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'statut' => [
                'nullable',
                'string',
                Rule::in([Equipement::STATUT_ACTIF, Equipement::STATUT_ARCHIVE]),
            ],
        ];
    }

    /**
     * Configure the validator instance with business coherence rules.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $etat = $this->input('etat_physique');
            $statutUtil = $this->input('statut_utilisation');

            if ($etat === Equipement::ETAT_HORS_SERVICE && $statutUtil === Equipement::STATUT_DISPONIBLE) {
                $validator->errors()->add('statut_utilisation', 'Incohérence métier : un équipement physiquement hors service ne peut pas être marqué comme disponible.');
            }

            if ($etat === Equipement::ETAT_EN_PANNE && $statutUtil === Equipement::STATUT_DISPONIBLE) {
                $validator->errors()->add('statut_utilisation', 'Incohérence métier : un équipement en panne ne peut pas être mis à disposition immédiate.');
            }
        });
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'produit_id.required' => 'Le choix d\'un produit du catalogue est obligatoire.',
            'produit_id.exists' => 'Le produit sélectionné est introuvable.',
            'code_inventaire.required' => 'Le code d\'inventaire est obligatoire.',
            'code_inventaire.unique' => 'Ce code d\'inventaire est déjà utilisé par un autre équipement.',
            'numero_serie.unique' => 'Ce numéro de série est déjà enregistré dans le parc informatique.',
            'etat_physique.required' => 'L\'état physique du matériel doit être spécifié.',
            'statut_utilisation.required' => 'Le statut d\'utilisation doit être spécifié.',
            'emplacement_id.exists' => 'L\'emplacement sélectionné est introuvable.',
            'date_entree.before_or_equal' => 'La date d\'entrée ne peut pas être postérieure à aujourd\'hui.',
        ];
    }
}
