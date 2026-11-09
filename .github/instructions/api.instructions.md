---
description: Reusable API and integration contracts for WikiPress
# applyTo: 'src/API/**/*.php' # when provided, instructions are automatically added to matching files
---

## Reusable Core APIs

- Before adding local logic, check the existing APIs and helpers in `src/API`, `src/Includes/Functions`, `src/Includes/Settings`, `src/Includes/Core`, and the provider interfaces in `src/Includes/Plugins/PluginsInterface.php`.
- Prefer `Settings` and `SettingsManager` for settings access, project sanitization/request helpers for input normalization, permission helpers for authorization, form-field helpers for controls, AJAX helpers for AJAX responses, alert helpers for admin notices, and `Response`/`Validators` for REST responses and validation.
- Do not query the settings database directly, duplicate shared sanitization or permission logic, or hand-build shared form/alert markup when a project helper exists.
- Use hooks, filters, provider interfaces, and generic metadata to deepen integration without coupling extensions to core internals.
