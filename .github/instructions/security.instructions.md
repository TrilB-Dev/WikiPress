---
description: Security requirements for WikiPress and WordPress integrations
# applyTo: '**/*.php' # when provided, instructions are automatically added to matching files
---

## WordPress Security and Quality

- Check capabilities before privileged admin, AJAX, REST, settings, import, export, or file operations.
- Verify nonces for state-changing admin and AJAX requests, validate REST permissions, and sanitize all input with the appropriate WordPress/project helper.
- Escape output for its context and validate URLs, IDs, file paths, and uploaded file types before use.
- Never hard-code credentials, tokens, salts, or environment-specific paths. Do not log secrets or sensitive request data.
- Keep admin-only code out of frontend execution paths and avoid loading feature assets globally.
- Use WordPress APIs and project abstractions instead of direct database or filesystem access unless the owning abstraction explicitly requires it.
