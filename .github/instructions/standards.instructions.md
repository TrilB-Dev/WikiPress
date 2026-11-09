---
description: PHP and WordPress coding standards for WikiPress
# applyTo: '**/*.php' # when provided, instructions are automatically added to matching files
---

## PHP and WordPress Coding Standards

All code in this plugin must follow the project’s PHP coding standards, PHP compatibility requirements, and the WordPress Coding Standards suite at all times. This is a hard requirement for every changed file, not a suggestion.

- Follow PHP coding standards consistently across all new and modified PHP files.
- Follow the WordPress Coding Standards set for plugin code, including WordPress, WordPress-Core, WordPress-Docs, and WordPress-Extra where applicable.
- Keep code aligned with WordPress security, escaping, sanitization, capability checks, and API conventions.
- Ensure compatibility with the project’s supported PHP versions. Do not introduce syntax or language features that are not supported by the plugin’s declared runtime compatibility.
- Run PHP compatibility checks as part of validation and fix any issues reported by `php-compatibility` before merge.
- Do not add code that works around the standards or bypasses them by excluding vendor or third-party code from the project’s own logic.
- Ensure all plugin-owned code remains clean, readable, and standards-compliant even when third-party dependencies, bundled vendor code, or generated files are present.
- Fix PHPCS warnings, errors, and notices as they appear during development instead of leaving them for a later pass.
- Do not merge code that introduces new standards violations, even if the feature is otherwise functional.
