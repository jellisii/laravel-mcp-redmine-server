<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    config([
        'mcp.auth_token' => 'test-secret-token',
        'redmine.base_url' => 'https://redmine.test',
        'redmine.api_key' => 'test-api-key',
    ]);

    Http::fake([
        'redmine.test/users/current.json*' => Http::response(['user' => ['id' => 1, 'admin' => true]], 200),
    ]);
});

it('returns 401 when no bearer token is provided', function (): void {
    $this->postJson('/mcp/redmine')->assertUnauthorized();
});

it('returns 401 for an invalid bearer token', function (): void {
    $response = $this->post('/mcp/redmine', [
        'jsonrpc' => '2.0',
        'method' => 'tools/list',
        'id' => 1,
    ], [
        'Authorization' => 'Bearer wrong-token',
        'Content-Type' => 'application/json',
    ]);

    $response->assertUnauthorized()
        ->assertJson(['message' => 'Unauthorized.']);
});

it('returns 401 when no bearer token is provided without accept json header', function (): void {
    $response = $this->post('/mcp/redmine', [
        'jsonrpc' => '2.0',
        'method' => 'tools/list',
        'id' => 1,
    ], [
        'Content-Type' => 'application/json',
    ]);

    $response->assertUnauthorized()
        ->assertJson(['message' => 'Unauthorized.']);
});

it('passes auth when correct token is provided', function (): void {
    $response = $this->postJson('/mcp/redmine', [
        'jsonrpc' => '2.0',
        'method' => 'tools/list',
        'id' => 1,
    ], ['Authorization' => 'Bearer test-secret-token']);

    $response->assertOk();
});
