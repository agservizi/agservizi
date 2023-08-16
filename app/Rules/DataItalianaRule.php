<?php

namespace App\Rules;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Contracts\Validation\Rule;

class DataItalianaRule implements Rule
{
    protected $errorMessage;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected $quando = null)
    {
        //
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
        $attribute = str_replace('_', ' ', $attribute);
        try {
            [$giorno, $mese, $anno] = explode('/', $value);
            $data = Carbon::createFromDate($anno, $mese, $giorno);

            if ($this->quando == 'passato' && ! $data->isPast()) {
                $this->errorMessage = "La $attribute deve essere nel passato.";

                return false;
            }
            if ($this->quando == 'futuro' && ! $data->isFuture()) {
                $this->errorMessage = "La $attribute deve essere nel futuro.";

                return false;
            }
        } catch (InvalidFormatException $e) {
            $this->errorMessage = "Il formato della $attribute non è corretto";

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }
}
