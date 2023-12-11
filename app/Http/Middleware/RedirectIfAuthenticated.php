<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use function redirect;

final class RedirectIfAuthenticated
{

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     */
    public function handle(Request $request, Closure $next, ?string ...$guards): Response|RedirectResponse
    {
        $guards = empty($guards)
            ? [null]
            : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return \redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }

}
