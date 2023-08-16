<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CapRule implements Rule
{
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
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
        if (! is_numeric($value)) {
            $this->message = 'Il cap deve contenere solo numeri';

            return false;
        }

        if (\Str::length($value) !== 5) {
            $this->message = 'Il cap deve essere lungo 5 caratteri numerici';

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
        return $this->message;
    }
}
