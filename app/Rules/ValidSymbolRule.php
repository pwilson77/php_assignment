<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ValidSymbolRule implements Rule
{
    public $symbolData = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Cache::has('company-data')) {
            $json = Cache::get('company-data');
        } else {
            $response = Http::get('https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json');
            $json = $response->json();
            Cache::put('company-data', $json, now()->addMinutes(10));
        }
        foreach ($json as $js) {
            array_push($this->symbolData, $js["Symbol"]);
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($value, $this->symbolData);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The company symbol sent was invalid';
    }
}
