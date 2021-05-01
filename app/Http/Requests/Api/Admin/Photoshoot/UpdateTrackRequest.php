<?php

namespace App\Http\Requests\Api\Admin\Photoshoot;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTrackRequest extends FormRequest
{
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
            'photoshoot_tracker_id' => [
                'required',
                Rule::exists('photoshoot_trackers', 'id'),
                Rule::unique('photoshoot_photoshoot_tracker', 'photoshoot_tracker_id')
                    ->where('photoshoot_id', $this->photoshoot->id)
            ]
        ];
    }
}
