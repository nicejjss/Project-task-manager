<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Custom\Traits\JsonResponseTrait;

class BaseController extends Controller
{
    use JsonResponseTrait;
}
