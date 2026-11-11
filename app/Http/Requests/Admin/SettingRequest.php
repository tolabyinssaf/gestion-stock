<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Informations générales
            'nom_entreprise' => ['nullable', 'string', 'max:150'],
            'nom_application' => ['required', 'string', 'max:100'],
            'adresse_entreprise' => ['nullable', 'string', 'max:255'],
            'telephone_entreprise' => ['nullable', 'string', 'max:50'],
            'email_contact' => ['nullable', 'email', 'max:255'],

            // Configuration Gmail
            'directeur_nom' => ['required', 'string', 'max:150'],
            'directeur_email' => ['required', 'email', 'max:255'],
            'signature_service_it' => ['nullable', 'string', 'max:150'],

            // Préférences d'affichage
            'lignes_par_page' => ['required', 'integer', 'in:10,15,20,50,100'],
            'format_date' => ['required', 'string', 'in:d/m/Y,Y-m-d,d-m-Y'],
            'fuseau_horaire' => ['required', 'string', 'in:Europe/Paris,UTC,Africa/Casablanca,America/New_York'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nom_entreprise' => 'nom de l\'entreprise',
            'nom_application' => 'nom de l\'application',
            'email_contact' => 'e-mail de contact',
            'directeur_nom' => 'nom du directeur / destinataire',
            'directeur_email' => 'adresse e-mail du directeur',
            'signature_service_it' => 'signature du service informatique',
            'lignes_par_page' => 'nombre de lignes par page',
            'format_date' => 'format d\'affichage des dates',
            'fuseau_horaire' => 'fuseau horaire',
        ];
    }
}
