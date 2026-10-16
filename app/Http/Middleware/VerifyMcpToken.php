<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class VerifyMcpToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $configured = config('mcp.auth_token');
        $bearer = $request->bearerToken();

        if (! is_string($configured) || $configured === '' || $bearer !== $configured) {
            return new JsonResponse(['message' => 'Unauthorized.'], 401);
        }

        /** @var Response */
        return $next($request);
    }
}
