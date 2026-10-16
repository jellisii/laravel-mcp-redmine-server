# Contributing

Thanks for your interest in improving the Redmine MCP server. This guide covers local setup, the checks CI runs, and how to get a change merged.

## Requirements

- PHP **8.4+** with the standard Laravel extensions — run `composer check-platform-reqs` to verify
- Composer **2**
- Docker — optional, only for the full local Redmine stack and for building the container image

No database is required: the MCP server authenticates callers with a bearer token (`MCP_AUTH_TOKEN`) and talks to Redmine over HTTP.

## Local setup

```bash
git clone https://github.com/xodert/laravel-mcp-redmine-server.git
cd laravel-mcp-redmine-server

composer setup   # installs dependencies, creates .env, generates APP_KEY, installs git hooks
```

`composer setup` leaves `REDMINE_*` empty. Tests mock Redmine with `Http::fake()`, so credentials are only needed for live integration checks.

### Optional: a real Redmine to play against

```bash
docker compose -f docker-compose.local.yml up -d
bash docker/scripts/redmine-init.sh   # seeds users, projects, issues, time entries
```

The stack serves Redmine on `http://localhost:3000` and the MCP HTTP endpoint on `http://localhost:8080/mcp/redmine`.

## Running checks

`composer test` is exactly what CI enforces:

| Command | What it does |
|---|---|
| `composer test` | type coverage (100%), Pest with **100% line coverage** (xdebug), Pint, Rector, PHPStan at `level: max` |
| `composer test:quick` | Pest only (`--compact --parallel`) — use while iterating |
| `composer lint` | applies Pint and Rector fixes |
| `vendor/bin/pest --filter=testName` | runs a single test |
| `vendor/bin/phpstan analyse` | static analysis on its own |

Coverage is enforced with `--exactly=100.0`, so new and changed code — including error paths — needs tests.

## Making a change

1. Fork the repository and create a topic branch (`fix/journal-user-names`, `feature/delete-time-entry`, …).
2. Write the change and its tests. Keep the code fully typed: `declare(strict_types=1)`, parameter and return types, PHPDoc array shapes — PHPStan runs at maximum level.
3. Run `composer test` locally.
4. Commit using [Conventional Commits](https://www.conventionalcommits.org/): `feat:`, `fix:`, `docs:`, `chore:`, `refactor:`, `test:`.
5. Open a pull request and fill in the template. CI (`.github/workflows/tests.yml`) must pass before a merge.

Useful context for common changes:

- **New or changed MCP tool** — a class in `app/Mcp/Tools/`, a method on `RedmineService`, registration in `app/Mcp/Servers/RedmineServer.php`, tests in `tests/Feature/Mcp/Tools/`, and entries in the tool tables of `README.md` and `.agents/skills/redmine-mcp/SKILL.md`.
- **Config or auth headers** — `config/redmine.php`, `app/Http/Middleware/InjectRedmineApiKey.php`, `app/Http/Middleware/VerifyMcpToken.php`.
- **Docker/bootstrapping** — `docker/mcp/Dockerfile`, `docker/mcp/entrypoint.sh`, `docker-compose.yml`.

Git hooks are installed by `composer setup`; the pre-commit hook runs lint, static analysis, and tests. Use `git commit --no-verify` to skip it for a one-off commit.

## Reporting bugs and requesting features

Use the [issue templates](https://github.com/xodert/laravel-mcp-redmine-server/issues/new/choose) — they ask for what we need (transport, client, Redmine version, logs). **Never paste real `MCP_AUTH_TOKEN` values or Redmine API keys into an issue.**

## Security

Report vulnerabilities privately, as described in [SECURITY.md](SECURITY.md).

## License

By contributing, you agree that your contributions are licensed under the [MIT License](LICENSE).
