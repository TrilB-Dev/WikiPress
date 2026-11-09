---
description: Translation and localization requirements for WikiPress
# applyTo: '**/*.{php,js,ts,scss}' # when provided, instructions are automatically added to matching files
---

## Localization

- WikiPress and its plugins use the repository language scripts to manage translation catalogs and compiled language files.
- Run `npm run i18n:pot` after adding or changing translatable strings. The script scans core and plugin source files and writes the appropriate POT catalog under `src/languages` or the plugin's `Language/` directory.
- Run `npm run i18n:mo` after updating a POT or PO catalog to compile the corresponding MO files used by WordPress.
- Keep plugin translation catalogs and language files inside the owning plugin's `Language/` or `Languages/` directory. Do not move plugin strings into the core catalog or hard-code translated text in shared core code.
- Use the correct text domain for the owning component and preserve existing translation file naming conventions.
