---
description: Validation and WordPress test environment workflow for WikiPress
# applyTo: '**/*.{php,js,ts,scss}' # when provided, instructions are automatically added to matching files
---

## Required Validation and Test Workflow

After creating or modifying code, validate it immediately and fix issues before continuing.

- Run focused validation after each significant code change.
- Check PHP syntax and linting for changed files before broader validation.
- Run the relevant PHPCS checks for plugin source files after adding or editing PHP.
- Run PHP compatibility validation against the supported PHP versions for the project.
- Fix all warnings and errors surfaced by the standards and compatibility tools before considering the work complete.
- Re-run validation after each fix to confirm the issue is resolved and no new issue was introduced.
- Treat code quality checks as part of implementation, not as a final cleanup step.

## WordPress Test Environment with .wp-env

This project includes `.wp-env.json` and it must be used for local WordPress testing when validating plugin behavior and standards in a real WordPress environment.

- Use `.wp-env.json` as the canonical local WordPress test environment for this plugin.
- Run the WordPress environment before broader validation or suite-based checks.
- Use the WordPress environment to exercise the plugin under actual WordPress runtime conditions rather than relying only on static analysis.
- When running the coding standards suite, target the plugin source and exclude third-party or vendor code from the checks so the project is evaluated fairly and without unrelated noise.
- Keep `.wp-env.json` aligned with the plugin’s expected local development/test setup.

Typical workflow:

```bash
npm install
npx wp-env start
```

Then run the standards suite against the plugin source, for example:

```bash
vendor/bin/phpcs \
  --standard=WordPress,WordPress-Core,WordPress-Docs,WordPress-Extra \
  --extensions=php \
  --ignore=vendor,dist,node_modules \
  src
```

For a full WordPress-oriented validation flow via the local environment, run the plugin under `.wp-env` and execute the WordPress test tooling from there as needed.

The standard is simple: if a change introduces a PHP warning, PHPCS violation, or WordPress coding issue, it must be corrected before the task is considered finished.
