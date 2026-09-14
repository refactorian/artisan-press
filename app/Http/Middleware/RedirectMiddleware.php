<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectMiddleware
{
    /**
     * Handle an incoming request and evaluate redirects.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Don't intercept API, asset, or Filament admin requests
        if ($request->is('api/*', 'admin/*', 'livewire/*', 'up', '_debugbar/*')) {
            return $next($request);
        }

        $path = '/'.ltrim($request->path(), '/');

        try {
            $redirect = Redirect::active()
                ->where('source_path', $path)
                ->first();

            if ($redirect) {
                $redirect->recordHit();

                return redirect(
                    $redirect->target_path,
                    $redirect->status_code->value
                );
            }
        } catch (QueryException) {
            // Database not ready yet (e.g. before migrations in test environments)
        }

        return $next($request);
    }
}
