<?php

if (! function_exists('validate_email')) {
    /**
     * 校验邮箱是否合法
     *
     * @param  string  $email
     * @return bool
     */
    function validate_email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (! function_exists('validate_username')) {
    /**
     * 校验用户名是否合法
     *
     * @param  string  $username
     * @return bool
     */
    function validate_username(string $username): bool
    {
        return (bool) preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $username);
    }
}
