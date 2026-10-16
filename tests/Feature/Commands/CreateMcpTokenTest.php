<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

it('outputs a token to set as MCP_AUTH_TOKEN', function (): void {
    $this->artisan('mcp:create-token')
        ->expectsOutputToContain('MCP_AUTH_TOKEN=')
        ->assertSuccessful();
});

it('outputs a 64-character random token', function (): void {
    Artisan::call('mcp:create-token');
    $output = Artisan::output();

    preg_match('/MCP_AUTH_TOKEN=(\S+)/', $output, $matches);

    expect($matches)->toHaveKey(1)
        ->and(mb_strlen($matches[1]))->toBe(64);
});
