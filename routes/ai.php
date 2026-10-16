<?php

declare(strict_types=1);

use App\Http\Middleware\InjectRedmineApiKey;
use App\Http\Middleware\VerifyMcpToken;
use App\Mcp\Servers\RedmineServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/redmine', RedmineServer::class)
    ->middleware([VerifyMcpToken::class, 'throttle:120,1', InjectRedmineApiKey::class]);

Mcp::local('redmine', RedmineServer::class);
