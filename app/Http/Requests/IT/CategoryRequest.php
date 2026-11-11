<?php

namespace App\Http\Requests\IT;

use App\Models\Categorie;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
        $categoryId = $this->route('category') instanceof Categorie
            ? $this->route('category')->id
            : $this->route('category');

        return [
            'nom' => ['required', 'string', 'max:100', Rule::unique('categories', 'nom')->ignore($categoryId)],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon_name' => ['nullable', 'string', 'max:50'],
            'statut' => ['required', 'string', Rule::in(Categorie::STATUTS)],
        ];
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de la catégorie est obligatoire.',
            'nom.unique' => 'Cette catégorie existe déjà dans le catalogue.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut sélectionné est invalide.',
        ];
    }
}
