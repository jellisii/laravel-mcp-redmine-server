<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class InjectRedmineApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-Redmine-API-Key');

        if ($apiKey !== null && $apiKey !== '') {
            config(['redmine.api_key' => $apiKey]);
        }

        $baseUrl = $request->header('X-Redmine-Base-URL');

        if ($baseUrl !== null && $baseUrl !== '') {
            config(['redmine.base_url' => $baseUrl]);
        }

        $username = $request->header('X-Redmine-Username');

        if ($username !== null && $username !== '') {
            config(['redmine.username' => $username]);
        }

        $password = $request->header('X-Redmine-Password');

        if ($password !== null && $password !== '') {
            config(['redmine.password' => $password]);
        }

        /** @var Response */
        return $next($request);
    }
}
