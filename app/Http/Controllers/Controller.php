<?php

namespace App\Http\Controllers;

use App\Custom\Traits\JsonResponseTrait;

abstract class Controller
{
    use JsonResponseTrait;
}
