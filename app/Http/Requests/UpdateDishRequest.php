<?php

namespace App\Http\Requests;

use App\Models\Dish;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDishRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $dish = Dish::find($this->id);
//        $dish = Dish::where('id', $this->id)->firstOrFail();
        return $dish->user_id === request()->user()->id || request()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255', 'unique:dishes,name,'. $this->id],
            "description" => "required|max:2048",
            'image' => 'required|url',
            'user_id' => 'exists:users,id',
        ];
    }
}
