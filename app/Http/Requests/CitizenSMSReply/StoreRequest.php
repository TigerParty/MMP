<?php

namespace App\Http\Requests\CitizenSMSReply;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'citizen_sms_id' => 'required|exists:citizen_sms,id',
            'message' => 'required|string|max:160'
        ];
    }
}
