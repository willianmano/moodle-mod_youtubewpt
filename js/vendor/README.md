# Vendored frontend rendering bundle

`bft-vendor-10.29.2-3.1.1.min.js` is a single concatenation of three upstream
UMD distributions, wrapped in an outer IIFE that shadows RequireJS's `define`
so the UMDs take their global-script branch instead of registering as
anonymous AMD modules.

The bundle is loaded from
[`pages/spike_react.php`](../../pages/spike_react.php) (and any future plugin
page that mounts a Preact root) via
`$PAGE->requires->js(..., $inhead = true)`. The shim
[`amd/src/lib/preact.js`](../../amd/src/lib/preact.js) is the only AMD module
that reads the bundle's globals; every component imports from that shim.

The bundle aliases the upstream globals to `window.bftPreact`,
`window.bftPreactHooks`, and `window.bftHtm`, then deletes the original
`window.preact` / `window.preactHooks` / `window.htm` so other plugins
vendoring different versions don't collide.

## Components

| Library | Version | License | Upstream |
|---|---|---|---|
| Preact | 10.29.2 | MIT | https://github.com/preactjs/preact |
| Preact hooks | 10.29.2 | MIT | https://github.com/preactjs/preact (same release) |
| htm | 3.1.1 | Apache-2.0 | https://github.com/developit/htm |

## Provenance — how the bundle was assembled

The bundle is the concatenation of three CDN downloads, with a fixed
prologue/epilogue (license header, `var define;` IIFE, and bft-prefix
aliasing). Re-assemble with:

```sh
cd blocks/feedback_tracker/js/vendor
curl -sSLf -o /tmp/preact.min.js \
  "https://cdn.jsdelivr.net/npm/preact@10.29.2/dist/preact.min.js"
curl -sSLf -o /tmp/preact-hooks.umd.js \
  "https://cdn.jsdelivr.net/npm/preact@10.29.2/hooks/dist/hooks.umd.js"
curl -sSLf -o /tmp/htm.js \
  "https://cdn.jsdelivr.net/npm/htm@3.1.1/dist/htm.js"
# Verify the bundle's component SHA-384s before re-bundling:
#   preact-10.29.2.min.js       : sha384-z8kMTPU6osFLGiuH4yXDVra0WDmQlsppa9pjgFRqfZLIXTLLrwVLA2ASRpj8mNxW
#   preact-hooks-10.29.2.umd.js : sha384-SmlLD1N5/8XzQOVGl+3vXFWFItiof7iyUGdpNWr2QsVpmels7h9++PAQjNWhpQuA
#   htm-3.1.1.js                : sha384-iPOMe3E8jVgp/PepuDy7lJvw7L/QP97X5POfi+X1EVyDVQi8Kuv/MooNVB5OFXVN
```

The bundle's own SHA-384 (`sha384-DNfuOwdEaNecAaVGhsXTOaEieXpf2+YnxWL0Og0JV/oFFrD74WwK+O/2Sy9YPq7R` at
the time of writing) is recorded in [`thirdpartylibs.xml`](../../thirdpartylibs.xml).

## Why concatenated, not three separate scripts

The Preact hooks UMD checks `typeof define === 'function' && define.amd` and,
if true, registers as an **anonymous** AMD module — which trips a "Mismatched
anonymous define()" error the next time Moodle's RequireJS resolves any other
module. Loading three separate `<script>` tags would expose each UMD to a
live `define` global. Wrapping all three inside one IIFE with a local
`var define;` is the smallest, least invasive fix; trying to do the same
across three separate scripts would require either patching each upstream
file (more divergence from upstream) or non-standard inline script tricks.

## Forward migration

When the plugin's required Moodle version bumps to 5.2+ (which ships React
natively via the `react`/`react-dom` import-map specifiers), the bundle is
deleted entirely and [`amd/src/lib/preact.js`](../../amd/src/lib/preact.js)
re-exports from `react` instead of `window.bftPreact`. See the migration
table in the project's MVP-2 plan.
