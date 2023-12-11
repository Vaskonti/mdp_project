<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CarPostRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'brand' => 'string|required',
            'card' => 'string|exists:mysql.discount_cards,type',
            'category' => 'string|exists:mysql.categories,category',
            'color' => 'string|required',
            'model' => 'string|required',
            'registrationPlate' => 'string|required|max:10',
        ];
    }

}
