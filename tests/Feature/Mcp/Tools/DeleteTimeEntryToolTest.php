<?php

declare(strict_types=1);

use App\Mcp\Tools\DeleteTimeEntryTool;
use App\Services\RedmineService;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;

beforeEach(function (): void {
    config(['redmine.base_url' => 'https://redmine.test', 'redmine.api_key' => 'key']);
});

it('returns success message after deleting a time entry', function (): void {
    Http::fake([
        'redmine.test/time_entries/42.json' => Http::response('', 204),
    ]);

    $response = (new DeleteTimeEntryTool)->handle(
        new Request(['time_entry_id' => 42]),
        new RedmineService,
    );

    expect($response->isError())->toBeFalse()
        ->and($response->content()->toArray()['text'])->toContain('#42');
});

it('sends a DELETE request to the correct endpoint', function (): void {
    Http::fake([
        'redmine.test/time_entries/99.json' => Http::response('', 204),
    ]);

    (new DeleteTimeEntryTool)->handle(
        new Request(['time_entry_id' => 99]),
        new RedmineService,
    );

    Http::assertSent(fn ($req): bool => $req->method() === 'DELETE'
        && str_ends_with((string) $req->url(), '/time_entries/99.json'));
});

it('returns error when time entry is not found', function (): void {
    Http::fake([
        'redmine.test/time_entries/999.json' => Http::response('', 404),
    ]);

    $response = (new DeleteTimeEntryTool)->handle(
        new Request(['time_entry_id' => 999]),
        new RedmineService,
    );

    expect($response->isError())->toBeTrue()
        ->and($response->content()->toArray()['text'])->toContain('Failed to delete time entry');
});

it('returns error when deletion fails', function (): void {
    Http::fake([
        'redmine.test/time_entries/1.json' => Http::response(['errors' => ['You are not authorized']], 403),
    ]);

    $response = (new DeleteTimeEntryTool)->handle(
        new Request(['time_entry_id' => 1]),
        new RedmineService,
    );

    expect($response->isError())->toBeTrue()
        ->and($response->content()->toArray()['text'])->toContain('Failed to delete time entry');
});
