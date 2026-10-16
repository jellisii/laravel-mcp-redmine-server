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

#[Description('Permanently delete an issue from Redmine. This action is irreversible — always confirm with the user before calling.')]
final class DeleteIssueTool extends Tool
{
    use CastsApiData;

    public function handle(Request $request, RedmineService $redmine): Response
    {
        try {
            $request->validate([
                'issue_id' => ['required', 'integer', 'min:1'],
            ]);

            $issueId = $request->integer('issue_id');
            $redmine->deleteIssue($issueId);

            return Response::text(sprintf('Issue #%d has been permanently deleted.', $issueId));
        } catch (RuntimeException $runtimeException) {
            return Response::error('Failed to delete issue: '.$runtimeException->getMessage());
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'issue_id' => $schema->integer()
                ->description('ID of the Redmine issue to permanently delete')
                ->required(),
        ];
    }
}
