<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Mcp\Concerns\CastsApiData;
use App\Services\RedmineService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use RuntimeException;

#[Description('Permanently delete a time entry from Redmine. This action is irreversible — always confirm with the user before calling.')]
final class DeleteTimeEntryTool extends Tool
{
    use CastsApiData;

    public function handle(Request $request, RedmineService $redmine): Response
    {
        try {
            $request->validate([
                'time_entry_id' => ['required', 'integer', 'min:1'],
            ]);

            $timeEntryId = $request->integer('time_entry_id');
            $redmine->deleteTimeEntry($timeEntryId);

            return Response::text(sprintf('Time entry #%d has been permanently deleted.', $timeEntryId));
        } catch (RuntimeException $runtimeException) {
            return Response::error('Failed to delete time entry: '.$runtimeException->getMessage());
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'time_entry_id' => $schema->integer()
                ->description('ID of the Redmine time entry to permanently delete')
                ->required(),
        ];
    }
}
