<?php

namespace App\Http\Middleware;

use App\Custom\Traits\JsonResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authentication
{
    use JsonResponseTrait;

    public function handle(Request $request, Closure $next, string $type): Response
    {
        if(Auth::attempt()) {
            return $next($request);
        }
        return redirect('authentication/login');
    }
}
