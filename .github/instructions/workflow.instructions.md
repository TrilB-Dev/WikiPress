---
description: Change workflow and implementation boundaries for WikiPress
# applyTo: '**/*.{php,js,ts,scss,md}' # when provided, instructions are automatically added to matching files
---

## Change Workflow

1. Read the relevant README and documentation before changing architecture. Start with [README.md](../README.md), then consult [API.md](../Docs/API.md), [HELPERS.md](../Docs/HELPERS.md), [SETTINGS.md](../Docs/SETTINGS.md), [INTERNAL_PLUGINS.md](../Docs/INTERNAL_PLUGINS.md), and [WORDPRESS_PLUGINS.md](../Docs/WORDPRESS_PLUGINS.md) as applicable.
2. Identify the owning plugin or extension boundary before editing. State whether the requested behavior belongs in a plugin or requires a generic core capability.
3. Inspect the nearest implementation, provider interface, and test or call site before changing behavior. Prefer the smallest plugin-owned implementation.
4. If core must change, document the generic contract and keep the implementation independent of any plugin identity. Update every affected provider implementation when an interface changes.
5. Preserve existing public APIs, hooks, settings names, and data formats unless a backward-compatible migration is included.
6. Validate changed PHP with `php -l`, changed JavaScript with `node --check`, and run focused tests before broader checks.
7. Rebuild frontend assets with `npm run build` when source assets change.
8. Regenerate language catalogs with `npm run i18n:pot` and `npm run i18n:mo` when translatable strings change.
9. Run `git diff --check` and review generated files, deleted files, and unrelated worktree changes before finishing.
