<?php

declare(strict_types=1);

namespace App\Mcp\Concerns;

use App\Services\RedmineService;
use Illuminate\Http\Client\ConnectionException;
use RuntimeException;

trait FetchesRedminePages
{
    /**
     * Fetches every active user across all pages (bounded by total_count).
     *
     * @return list<array<string, mixed>>
     *
     * @throws RuntimeException
     */
    private function fetchAllUsers(RedmineService $redmine): array
    {
        $all = [];
        $offset = 0;

        do {
            $result = $redmine->getUsers($offset, 100);

            if ($result['items'] === []) {
                break;
            }

            $all = array_merge($all, $result['items']);
            $offset += count($result['items']);
        } while ($offset < $result['total']);

        return $all;
    }

    /**
     * Fetches every membership of a project across all pages (bounded by total_count).
     *
     * @return list<array<string, mixed>>
     *
     * @throws ConnectionException
     * @throws RuntimeException
     */
    private function fetchAllMemberships(RedmineService $redmine, int $projectId): array
    {
        $all = [];
        $offset = 0;

        do {
            $result = $redmine->getProjectMemberships($projectId, $offset, 100);

            if ($result['items'] === []) {
                break;
            }

            $all = array_merge($all, $result['items']);
            $offset += count($result['items']);
        } while ($offset < $result['total']);

        return $all;
    }

    /**
     * Resolves user IDs to display names without the admin-only /users.json
     * endpoint: project memberships first, then /users/{id}.json for IDs not
     * found there. IDs that still can't be resolved are left out, so callers
     * show a placeholder instead of failing.
     *
     * @param  list<int>  $userIds
     * @return array<int, string>
     */
    private function resolveUserNames(RedmineService $redmine, ?int $projectId, array $userIds): array
    {
        $names = [];

        if ($userIds === []) {
            return $names;
        }

        if ($projectId !== null) {
            try {
                foreach ($this->fetchAllMemberships($redmine, $projectId) as $membership) {
                    $principal = $membership['user'] ?? $membership['group'] ?? null;

                    if (! is_array($principal)) {
                        continue;
                    }

                    $id = $this->intOf($principal['id'] ?? 0);
                    $name = $this->strOf($principal['name'] ?? '');

                    if ($id > 0 && $name !== '') {
                        $names[$id] = $name;
                    }
                }
            } catch (RuntimeException|ConnectionException) {
                // Memberships unavailable to this key; fall through to per-user lookups.
            }
        }

        foreach (array_diff($userIds, array_keys($names)) as $userId) {
            try {
                $user = $redmine->getUser($userId);
            } catch (RuntimeException|ConnectionException) {
                continue; // Not visible to this key; the caller shows a placeholder.
            }

            if ($user !== []) {
                $names += $this->buildUserNameMap([$user]);
            }
        }

        return $names;
    }

    /**
     * Fetches every time entry for the given date and returns unique user IDs.
     *
     * @return list<int>
     *
     * @throws RuntimeException
     */
    private function fetchAllLoggedUserIds(RedmineService $redmine, string $date): array
    {
        $userIds = [];
        $offset = 0;

        do {
            $result = $redmine->getTimeLogsByDate($date, $offset, 100);

            if ($result['items'] === []) {
                break;
            }

            foreach ($result['items'] as $entry) {
                $userId = $this->intOf(data_get($entry, 'user.id'));

                if ($userId > 0) {
                    $userIds[] = $userId;
                }
            }

            $offset += count($result['items']);
        } while ($offset < $result['total']);

        return array_values(array_unique($userIds));
    }

    /**
     * @param  list<array<string, mixed>>  $users
     * @return array<int, string>
     */
    private function buildUserNameMap(array $users): array
    {
        $map = [];

        foreach ($users as $user) {
            $id = $this->intOf($user['id']);

            if ($id <= 0) {
                continue;
            }

            $name = mb_trim(sprintf(
                '%s %s',
                $this->strOf($user['firstname'] ?? ''),
                $this->strOf($user['lastname'] ?? ''),
            ));

            $map[$id] = $name !== '' ? $name : $this->strOf($user['login'] ?? (string) $id);
        }

        return $map;
    }
}
