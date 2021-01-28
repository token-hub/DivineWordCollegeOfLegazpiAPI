<?php

namespace App\Helpers;

function current_user()
{
    return auth()->user();
}
