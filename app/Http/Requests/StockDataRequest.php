<?php

namespace App\Http\Requests;

use App\Rules\ValidSymbolRule;
use Illuminate\Foundation\Http\FormRequest;

class StockDataRequest extends FormRequest
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
            'symbol' => ['required', new ValidSymbolRule()],
            'startdate' => ['required', 'date_format:Y-m-d', 'before_or_equal:now', 'before_or_equal:enddate'],
            'enddate' => ['required', 'date_format:Y-m-d', 'after_or_equal:startdate', 'before_or_equal:now'],
            'email' => ['required', 'email:rfc,dns'],
        ];
    }
}
