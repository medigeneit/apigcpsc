<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }


    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        $first_guard = $guards[0] ?? '';

        if ($ignore_unauthenticated = ($first_guard == 'sanctum.ignore-unauthenticated')) {
            $guards[0] = 'sanctum';
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        if (!$ignore_unauthenticated) {
            $this->unauthenticated($request, $guards);
        }
    }

}
