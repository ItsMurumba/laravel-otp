<?php

if (! function_exists('otp')) {
    function otp()
    {
        return app()->make('otp');
    }
}
