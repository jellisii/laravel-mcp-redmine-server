<?php

declare(strict_types=1);

use App\Http\Middleware\InjectRedmineApiKey;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

it('sets redmine.api_key config from X-Redmine-API-Key header', function (): void {
    config(['redmine.api_key' => 'default-token']);

    $request = Request::create('/mcp/redmine', 'POST');
    $request->headers->set('X-Redmine-API-Key', 'user-personal-token');

    (new InjectRedmineApiKey)->handle($request, fn (): Response => new Response);

    expect(config('redmine.api_key'))->toBe('user-personal-token');
});

it('keeps existing config when header is absent', function (): void {
    config(['redmine.api_key' => 'default-token']);

    $request = Request::create('/mcp/redmine', 'POST');

    (new InjectRedmineApiKey)->handle($request, fn (): Response => new Response);

    expect(config('redmine.api_key'))->toBe('default-token');
});

it('keeps existing config when header is empty string', function (): void {
    config(['redmine.api_key' => 'default-token']);

    $request = Request::create('/mcp/redmine', 'POST');
    $request->headers->set('X-Redmine-API-Key', '');

    (new InjectRedmineApiKey)->handle($request, fn (): Response => new Response);

    expect(config('redmine.api_key'))->toBe('default-token');
});

it('sets redmine.base_url config from X-Redmine-Base-URL header', function (): void {
    config(['redmine.base_url' => 'https://default.redmine.test']);

    $request = Request::create('/mcp/redmine', 'POST');
    $request->headers->set('X-Redmine-Base-URL', 'https://other.redmine.test');

    (new InjectRedmineApiKey)->handle($request, fn (): Response => new Response);

    expect(config('redmine.base_url'))->toBe('https://other.redmine.test');
});

it('keeps existing base_url when X-Redmine-Base-URL header is absent', function (): void {
    config(['redmine.base_url' => 'https://default.redmine.test']);

    $request = Request::create('/mcp/redmine', 'POST');

    (new InjectRedmineApiKey)->handle($request, fn (): Response => new Response);

    expect(config('redmine.base_url'))->toBe('https://default.redmine.test');
});

it('keeps existing base_url when X-Redmine-Base-URL header is empty string', function (): void {
    config(['redmine.base_url' => 'https://default.redmine.test']);

    $request = Request::create('/mcp/redmine', 'POST');
    $request->headers->set('X-Redmine-Base-URL', '');

    (new InjectRedmineApiKey)->handle($request, fn (): Response => new Response);

    expect(config('redmine.base_url'))->toBe('https://default.redmine.test');
});

it('sets redmine.username config from X-Redmine-Username header', function (): void {
    config(['redmine.username' => '']);

    $request = Request::create('/mcp/redmine', 'POST');
    $request->headers->set('X-Redmine-Username', 'john');

    (new InjectRedmineApiKey)->handle($request, fn (): Response => new Response);

    expect(config('redmine.username'))->toBe('john');
});

it('keeps existing username when X-Redmine-Username header is absent', function (): void {
    config(['redmine.username' => 'existing-user']);

    $request = Request::create('/mcp/redmine', 'POST');

    (new InjectRedmineApiKey)->handle($request, fn (): Response => new Response);

    expect(config('redmine.username'))->toBe('existing-user');
});

it('keeps existing username when X-Redmine-Username header is empty string', function (): void {
    config(['redmine.username' => 'existing-user']);

    $request = Request::create('/mcp/redmine', 'POST');
    $request->headers->set('X-Redmine-Username', '');

    (new InjectRedmineApiKey)->handle($request, fn (): Response => new Response);

    expect(config('redmine.username'))->toBe('existing-user');
});

it('sets redmine.password config from X-Redmine-Password header', function (): void {
    config(['redmine.password' => '']);

    $request = Request::create('/mcp/redmine', 'POST');
    $request->headers->set('X-Redmine-Password', 'secret');

    (new InjectRedmineApiKey)->handle($request, fn (): Response => new Response);

    expect(config('redmine.password'))->toBe('secret');
});

it('keeps existing password when X-Redmine-Password header is absent', function (): void {
    config(['redmine.password' => 'existing-secret']);

    $request = Request::create('/mcp/redmine', 'POST');

    (new InjectRedmineApiKey)->handle($request, fn (): Response => new Response);

    expect(config('redmine.password'))->toBe('existing-secret');
});

it('keeps existing password when X-Redmine-Password header is empty string', function (): void {
    config(['redmine.password' => 'existing-secret']);

    $request = Request::create('/mcp/redmine', 'POST');
    $request->headers->set('X-Redmine-Password', '');

    (new InjectRedmineApiKey)->handle($request, fn (): Response => new Response);

    expect(config('redmine.password'))->toBe('existing-secret');
});
