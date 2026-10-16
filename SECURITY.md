# Security Policy

## Supported versions

Only the latest commit on `main` is supported. Fixes land there first; tagged releases are cut from `main`.

## Reporting a vulnerability

Please **do not** open a public issue for security problems, including auth bypass, credential leakage, or prompt-injection issues that make tools act outside the caller's permissions.

Use GitHub private vulnerability reporting instead:

1. Open <https://github.com/xodert/laravel-mcp-redmine-server/security/advisories/new>
2. Describe the issue, the steps to reproduce, and the impact you see.

You can expect an acknowledgement within a few days. Once a fix is available, a GitHub Security Advisory is published and reporters are credited unless they prefer to stay anonymous.

## Scope

In scope:

- The MCP server in this repository (`app/`, `routes/`, `config/`, `docker/`)
- Authentication and credential handling: `MCP_AUTH_TOKEN`, and the per-request `X-Redmine-API-Key` / `X-Redmine-Username` / `X-Redmine-Password` / `X-Redmine-Base-URL` headers
- Container images built from this repository

Out of scope:

- The Redmine instance the server connects to, and its own configuration
- Third-party MCP clients and harnesses
- Findings that require an already-compromised host or physical access
