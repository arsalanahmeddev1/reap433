<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Response;

class NormalizeRequestPath
{
    /**
     * Collapse duplicate slashes so /api//sign-up matches /api/sign-up.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->getPathInfo();
        $normalized = preg_replace('#/+#', '/', $path) ?: '/';

        if ($normalized === $path) {
            return $next($request);
        }

        $query = $request->getQueryString();
        $request->server->set('REQUEST_URI', $normalized.($query ? '?'.$query : ''));
        $request->server->set('PATH_INFO', $normalized);

        foreach (['pathInfo', 'requestUri', 'baseUrl', 'basePath'] as $property) {
            $this->resetRequestProperty($request, $property);
        }

        return $next($request);
    }

    private function resetRequestProperty(Request $request, string $property): void
    {
        $class = new \ReflectionClass($request);

        while ($class) {
            if ($class->hasProperty($property)) {
                $prop = $class->getProperty($property);
                $prop->setAccessible(true);
                $prop->setValue($request, null);

                return;
            }

            $class = $class->getParentClass() ?: null;
        }
    }
}
