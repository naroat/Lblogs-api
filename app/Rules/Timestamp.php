<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Timestamp implements Rule
{
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
     * 验证时间戳
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!(preg_match('/^\d{10}$/', $value) || preg_match('/^\d{13}$/', $value))) {
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
        return '时间戳格式错误!';
    }
}
