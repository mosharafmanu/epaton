# Epaton — Session Handoff

**Date:** 2026-07-07
**Latest commit:** `25067a8 Add hero logo field with header-aligned responsive layout`
**Branches:** `main`, `imran`, `faisal` — all synced to `25067a8`
**Remote:** `https://github.com/mosharafmanu/epaton.git` (renamed from `epaton-wp-theme`; old URL redirects)

---

## What We Built This Session

### 1. Hero Logo Field (`acf-json/group_flexible_content.json`, `hero_section.php`)

The combined "epaton notape" logo is no longer baked into the hero background image — it is now a separate ACF field so the background can be a clean cloud image.

**ACF:** `hero_logo` — single Image field (return Array, `jpg,jpeg,png,webp,svg`), last field in the Hero Section's Media tab. Added by editing the JSON directly and bumping the `modified` timestamp (never `acf_update_field_group()`).

**Template:** collected with the other sub-fields, adds `has-logo` to `.hero-section` when set, rendered via `epaton_render_icon()` (inline SVG) as:

```html
<div class="hero-grid">
    <div class="hero-content">…</div>   <!-- first in DOM (H1 first) -->
    <div class="hero-logo-wrap">        <!-- moved visually left via order: -1 -->
        <svg class="hero-logo">…</svg>
    </div>
</div>
```

### 2. Hero Logo CSS (`style.css`, Hero Style block)

| Breakpoint | Layout |
|---|---|
| ≥1200px | Row: logo left, content right, vertically centered. Logo left edge = **header logo** (gutter + 2.8125rem bar padding) **+ 5rem (80px) nudge**. |
| 768–1199px | Stacked: `.hero-grid` becomes a centered content-width column (`max-width: 37.125rem`, `align-items: flex-start`) — logo left-aligns with the text block. |
| ≤767px | Stacked and centered (`align-items: center`) to match the centered mobile text; logo `max-width: 16rem`. |

Key mechanism (desktop): negative `margin-left` on `.hero-logo-wrap`:

```css
--container-outdent: max(0rem, (100vw - <2×gutter> - 68.625rem) / 2);
margin-left: calc(2.8125rem + 5rem - var(--container-outdent));
```

`--container-outdent` = distance from the centered `.epaton-container` edge to the `layout-padding` gutter edge, with breakpoint-matched gutter values (`1.5rem` base / `2rem` ≥1200 / `3.125rem` ≥1600). Caveat: `100vw` includes classic scrollbars (~8px drift on Windows; exact on macOS overlay scrollbars).

Logo display width is CSS-controlled (`29rem`/`20rem`/`16rem` via `.hero-logo-wrap`), independent of the uploaded file. Requirements for uploads: tightly cropped export (alignment is to the bounding box), SVGs must keep their `viewBox`, rasters ≥ ~930px wide for retina.

### 3. Hero Gutter Fix (`hero_section.php`, `style.css`)

`layout-padding` was on `.hero-grid` **inside** `.epaton-container`, insetting hero content 50px from the container edge (every other section has the gutter outside the container). Moved it to `.hero-inner`, and split the hero tablet/mobile `padding` shorthands into `padding-top`/`padding-bottom` so they no longer zero the horizontal gutter.

### 4. Production Zip Build

`~/Desktop/epaton.zip` (clean `epaton/` root, WP-Admin uploadable). Excluded: `.ai/`, all `*.md`, `inc/wp-cli/` (seeder), `deploy.sh`, `composer.json`, `package.json`, `assets/js/jquery.mb.vimeo_player.min.js` (unreferenced), `.DS_Store`/git artifacts. Kept: `LICENSE`, `screenshot.png`, `style-rtl.css`, `languages/`, full `acf-json/`.

To support shipping without `inc/wp-cli/`, the seeder `require` in `functions.php` is now wrapped in `file_exists()` — otherwise any `wp` command on the live server would fatal.

### 5. Git Reconnection & Branch Sync

The local theme folder had **lost its `.git` directory**. Re-linked by cloning the repo and moving the clone's `.git` into the theme folder. Reconciled: only 8 files differed from remote `main` (`a541387`, a doc-only commit past the last handoff). Restored the repo's `.gitignore`, kept two small local tweaks that predate this session (`inner_hero.php`: `layout-padding` removed from container; `products_listing.php`: `media` class on card image), started tracking `deploy.sh` (lftp SFTP mirror, no credentials — uses SSH host alias `epaton`).

Committed everything as `25067a8`, pushed `main`, and fast-forwarded `imran` + `faisal` to the same commit (both were ancestors of `main`, no unmerged work).

---

## Files Changed This Session

| File | Change |
|---|---|
| `acf-json/group_flexible_content.json` | `hero_logo` image field in Hero Section Media tab |
| `template-parts/sections/hero_section.php` | Logo collection + render, `has-logo` class, gutter class moved to `.hero-inner` |
| `style.css` | Hero logo layout (all breakpoints), hero gutter/padding fixes |
| `functions.php` | `file_exists()` guard on WP-CLI seeder require |
| `deploy.sh` | Now tracked in git |
| `.ai/PROJECT-MEMORY.md` | Updated to current state |
| `.ai/SESSION-HANDOFF-2026-07-07.md` | **New** — this file |

---

## Next Session

1. Upload `epaton.zip` to the live site, activate, run **Custom Fields → Sync** for all field groups/post types/options pages.
2. Upload the combined hero logo file to the Hero Logo field; swap the hero background for the clean (logo-free) version.
3. Serialized-safe URL replacement, then enable indexing and flush:
   ```bash
   wp search-replace 'http://localhost/ClientProjects/WordPress/2026/epaton' 'https://client-domain.com' --all-tables --skip-columns=guid
   wp option update blog_public 1
   wp rewrite flush --hard
   ```
4. Production verification per SESSION-HANDOFF-2026-06-15.md (SSL, forms, video, menus, Lighthouse).
