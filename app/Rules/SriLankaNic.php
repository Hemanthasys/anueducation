<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SriLankaNic implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Old format: 9 digits + V or X (case insensitive)  e.g. 990123456V
        // New format: 12 digits                              e.g. 199012345678
        if (! preg_match('/^(\d{9}[VvXx]|\d{12})$/', $value)) {
            $fail(__('validation_nic_invalid'));
        }
    }
}
