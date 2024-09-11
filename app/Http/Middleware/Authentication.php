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

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('token') ?? null;
        if ($token && Auth::attempt($token) === true) {
            return $next($request);
        }
        return $this->failed(data: 'UNAUTHORIZED', status: Response::HTTP_UNAUTHORIZED);
    }
}
