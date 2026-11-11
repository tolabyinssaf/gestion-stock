<?php

namespace App\Http\Requests\IT;

use App\Models\Produit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isResponsableIt() || $this->user()->isAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $produitId = $this->route('product') instanceof Produit
            ? $this->route('product')->id
            : $this->route('product');

        return [
            'nom' => ['required', 'string', 'max:150'],
            'categorie_id' => ['required', 'exists:categories,id'],
            'marque' => ['nullable', 'string', 'max:80'],
            'modele' => ['nullable', 'string', 'max:100'],
            'reference' => ['nullable', 'string', 'max:50', Rule::unique('produits', 'reference')->ignore($produitId)],
            'type_suivi' => ['nullable', 'string', Rule::in(Produit::TYPES_SUIVI)],
            'description' => ['nullable', 'string'],
            'caracteristiques' => ['nullable', 'string'],
            'prix_indicatif' => ['nullable', 'numeric', 'min:0'],
            'seuil_minimum' => ['nullable', 'integer', 'min:0'],
            'statut' => ['nullable', 'string', Rule::in(Produit::STATUTS)],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du produit est obligatoire.',
            'categorie_id.required' => 'Veuillez sélectionner une catégorie.',
            'categorie_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'marque.required' => 'La marque ou constructeur est obligatoire.',
            'reference.required' => 'La référence interne (SKU) est obligatoire.',
            'reference.unique' => 'Cette référence interne existe déjà pour un autre produit.',
            'type_suivi.required' => 'Le type de suivi est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.',
            'photo.image' => 'Le fichier doit être une image valide.',
            'photo.mimes' => 'Formats de photo autorisés : JPEG, PNG, JPG, WEBP.',
            'photo.max' => 'La taille de la photo ne doit pas dépasser 2 Mo.',
        ];
    }
}
