<?php

use Carbon\Carbon;

if (! function_exists('carbon')) {
    function carbon($time = null)
    {
        return Carbon::parse($time);
    }
}
