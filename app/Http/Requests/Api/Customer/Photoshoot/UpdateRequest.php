<?php

namespace App\Http\Requests\Api\Customer\Photoshoot;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{

    protected $photographer_role;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->photographer_role = Role::where('name', \App\Enums\Role::PHOTOGRAPHER)->first();
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'location_id' => 'sometimes|required|exists:photoshoot_locations,id',
            'product' => 'sometimes|required|max:50',
            'description' => 'sometimes|required',
            'photographer_id' => [
                'sometimes',
                'required',
                Rule::exists('users', 'id')->where('role_id', $this->photographer_role->id)
            ],
            'number_of_shots' => 'sometimes|required'
        ];
    }
}
