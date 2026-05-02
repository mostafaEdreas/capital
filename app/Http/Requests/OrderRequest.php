<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
                'items' => 'required|array|min:1',
                'items.*.bottle_id' => 'required|exists:bottles,id',
                'items.*.bottle_quantity' => 'required|integer|min:1',
                'items.*.ingredients' => 'required|array|min:1',
                'items.*.ingredients.*.ingredient_id' => 'required|exists:ingredients,id',
                'items.*.ingredients.*.ingredient_type_id' => 'required|exists:ingredient_types,id',
                'items.*.ingredients.*.sold_quantity_grams' => 'required|integer|min:1',
        ];
    }
}
