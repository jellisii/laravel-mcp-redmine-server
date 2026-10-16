<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Description('Generate a random token to use as MCP_AUTH_TOKEN')]
#[Signature('mcp:create-token')]
final class CreateMcpToken extends Command
{
    public function handle(): int
    {
        $token = Str::random(64);

        $this->info('Generated MCP auth token.');
        $this->line('');
        $this->warn('Add this to your .env file:');
        $this->line('');
        $this->line('MCP_AUTH_TOKEN='.$token);
        $this->line('');
        $this->warn('Store this token securely — it grants full access to the MCP server.');

        return self::SUCCESS;
    }
}
