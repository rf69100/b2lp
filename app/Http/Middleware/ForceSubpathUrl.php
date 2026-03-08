<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ForceSubpathUrl
{
    public function handle(Request $request, Closure $next)
    {
        $uri = $request->server->get('REQUEST_URI', '/');
        $stripped = preg_replace('#^/b2lp#', '', $uri) ?: '/';
        $request->server->set('REQUEST_URI', $stripped);
        $request->initialize(
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all(),
            $request->getContent()
        );
        URL::forceRootUrl('https://www.ryanfonseca.fr/b2lp');
        return $next($request);
    }
}
