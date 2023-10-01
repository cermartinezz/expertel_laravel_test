<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MeetingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'users'         => ['required', 'array', Rule::exists('users', 'id')],
            'start_time'    => ['required','date', 'before:end_time', 'after_or_equal:now'],
            'end_time'      => ['required','date', 'after:start_time'],
            'meeting_name'  => ['required', 'string', 'max:255'],
        ];
    }
}
