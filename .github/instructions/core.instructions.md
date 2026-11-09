---
description: Core repository layout and architecture rules for WikiPress
# applyTo: 'src/**/*.php' # when provided, instructions are automatically added to matching files
---

## Repository Layout

The repository is organized into a core application and provider-based internal plugins:

- `src/Plugin.php` is the main application entry point that wires the core services and plugin lifecycle.
- `src/Admin/` contains core admin UI and managers.
- `src/API/` contains shared API and response contracts.
- `src/Assets/` is the central asset pipeline. `src/Assets/Assets.php` owns registration and enqueueing of styles and scripts.
- `src/Includes/Core/` contains WordPress infrastructure and shared registries, including capabilities, editor, post types, taxonomies, shortcodes, activation, and deactivation.
- `src/Includes/Functions/` contains reusable functions and helpers.
- `src/Includes/Settings/` contains core settings storage and management services.
- `src/Includes/Plugins/` contains internal plugins, provider interfaces, and the plugin discovery/initialization service.
- `src/languages/` contains WikiPress core translation catalogs and compiled language files.
- `src/Public/` contains core frontend behavior.
- `Test/` contains automated tests and test fixtures.
- `Docs/` contains the public architecture, API, helper, settings, and integration documentation.

Use the repository's existing casing: `src/Includes/` is authoritative, not `src/includes/`. Do not create duplicate parallel directories for the same concern.

## Core Boundary

- Keep feature-specific behavior out of WikiPress core. Core must not contain conditions, selectors, settings keys, labels, capabilities, or business rules for a particular plugin.
- Do not add plugin-specific branches to shared classes such as admin renderers, settings managers, asset managers, API services, or shared helpers.
- If a plugin needs behavior that core cannot currently express, first implement it inside the plugin using its own `Admin/`, `Assets/`, `Includes/`, templates, helpers, services, and hooks.
- Decide whether to change core using this decision gate:
  1. Can the behavior live entirely in the plugin? If yes, do that.
  2. If no, does an identical need exist in two or more existing plugins? If no, stop — do not change core.
  3. If yes, add a generic extension point; never reference a plugin slug or class in core.
- When adapting core, expose a stable extension point and let the plugin provide the data or behavior. Never make core identify a plugin by slug, class name, or feature-specific setting.
- Prefer composition over inheritance. Core service classes are generally final and should be composed by plugins.
- Preserve existing public interfaces and hook contracts. Extend them backward-compatibly when possible.

## Internal Plugins

- Internal plugins live under `src/Includes/Plugins/<WikiPress>/` and are initialized by `Plugins.php` through the contracts in `PluginsInterface.php`.
- Separately installed plugins register through `wikipress_register_plugin` and follow the same ownership rules. They must not require core changes that reference their slug or class.
- Do not bypass the plugin loader with direct feature requires from `WikiPress.php`, `Plugin.php`, or core bootstrap code.
- Treat each internal plugin as a self-contained extension with its own bootstrap, assets, includes, settings, translations, capabilities, and frontend/admin behavior where needed. A plugin may use this structure:

```text
WikiPress/
|- Admin/                         (optional)
|- Assets/
|  |- dist/
|  |- js/
|  |- scss/
|  `- Assets.php                  (optional)
|- Includes/
|  |- Core/                       (optional)
|  |- Functions/                  (optional)
|  |- Settings/                   (optional)
|  `- Includes.php                (optional)
|- Language/ or Languages/
`- WikiPress.php
```

- A plugin owns its settings defaults, settings sanitization, business rules, templates, JavaScript, Sass, translations, REST routes, admin pages, capabilities, and feature-specific helpers.
- Implement only the provider interfaces required by the feature: settings, settings page, database, assets, admin pages, admin menus/sidebar, REST routes, frontend behavior, shortcodes, and translations.
- Keep plugin bootstrap classes thin. Delegate business logic to plugin-owned services and use the plugin lifecycle and `LoaderHelper` for hooks.
- Use unique plugin slugs, namespaces, option names, AJAX actions, REST namespaces, script handles, and CSS selectors.
- Do not place plugin settings in core settings groups unless the setting is genuinely a core concern.
- Plugin settings pages should provide declarative field metadata to the generic renderer. Put dynamic option generation, validation, conditional behavior, and persistence in the plugin.
- Plugin JavaScript must discover and control plugin-owned markup through plugin data attributes or generic metadata. Do not require core to add a marker for one plugin.
