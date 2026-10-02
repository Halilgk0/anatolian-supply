<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Opens the product admin only through the secret link. Any other key looks like a missing page.
 */
class EnsureAdminKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = (string) config('store.admin_key');
        $givenKey = (string) $request->route('adminKey');

        if ($expectedKey === '' || ! hash_equals($expectedKey, $givenKey)) {
            abort(404);
        }

        URL::defaults(['adminKey' => $givenKey]);
        $request->route()->forgetParameter('adminKey');

        $response = $next($request);

        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        $response->headers->set('Referrer-Policy', 'no-referrer');

        return $response;
    }
}
