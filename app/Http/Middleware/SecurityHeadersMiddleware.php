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

        if (app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

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

        // Content Security Policy
        $response->headers->set('Content-Security-Policy', "default-src 'none'; frame-ancestors 'none'");

        // Headers de sécurité complémentaires
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $response;
    }
}