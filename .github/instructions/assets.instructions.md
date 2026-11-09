---
description: Asset pipeline and registration rules for WikiPress
# applyTo: 'src/Assets/**/*.{js,ts,scss,php}' # when provided, instructions are automatically added to matching files
---

## Assets

All WikiPress and internal-plugin assets must flow through `src/Assets/Assets.php`.

- Plugin asset classes may define asset lists and page conditions, but must register them through the central `WikiPress\Assets\Assets` service.
- Use the central page registry and enqueue pipeline; do not call `wp_enqueue_style()` or `wp_enqueue_script()` directly from plugin code when the central service can express the requirement.
- Keep source JavaScript and SCSS in the owning plugin's `Assets/js` and `Assets/scss` directories, and generated files in its `Assets/dist` directory. Core source assets remain in `src/Assets/js` and `src/Assets/scss`.
- Preserve page-specific loading conditions so plugin assets do not load on unrelated admin or frontend pages.
- Update webpack configuration only for the owning asset pipeline. Do not commit ad hoc generated bundles in a source directory.
- Run the asset build after changing JavaScript, SCSS, webpack entries, or asset registration, and inspect generated changes for stale bundles.
