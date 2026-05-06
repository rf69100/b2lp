<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Secure uniquement en production (HTTPS), false en local (HTTP)
        $isSecure = app()->environment('production');

        // Forcer HttpOnly + Secure conditionnel + SameSite=Lax sur tous les cookies
        // Couvre : laravel_session (StartSession) et XSRF-TOKEN (VerifyCsrfToken)
        foreach ($response->headers->getCookies() as $cookie) {
            $response->headers->removeCookie($cookie->getName());
            $response->headers->setCookie(
                new Cookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiresTime(),
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    $isSecure,              // Secure : true en prod (HTTPS), false en local (HTTP)
                    true,                   // HttpOnly : inaccessible via document.cookie en JS
                    $cookie->isRaw(),
                    'lax'                   // SameSite=Lax : protection CSRF cross-site
                )
            );
        }

        return $response;
    }
}