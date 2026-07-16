# Epaton — Session Handoff

**Date:** 2026-06-15
**Latest implementation commit:** `e43ff64`
**Branch:** `main` synced with `origin/main`
**Remote:** `https://github.com/mosharafmanu/epaton.git`

### Branch Merge State

- Remote `main` and `imran` were merged at `61b6f53`.
- That merge combines optimization commit `9162033` with Imran's `ab123e9` CSS/ACF update.
- Local `imran` was fast-forwarded to `61b6f53` and tracks `origin/imran`.
- `faisal`/`origin/faisal` remain at `9582484`; their work is already an ancestor of `main`.
- Local ACF features removed by the remote merge were restored in `c909842` without removing remote additions.
- Final ACF flexible-content locations include `page`, `post`, `service`, and `product`.
- Preserved local fields:
  - `inner_hero_eyebrow`
  - `core_areas_eyebrow`
  - `contact_cta_use_global` and its conditional override fields
- Flexible Content ACF JSON timestamp was bumped in `e43ff64` to `1781529243` (`2026-06-15 13:14:03 UTC`).
- This timestamp update is intended to make **ACF > Field Groups > Sync available** appear for `group_flexible_content`.
- If the sync option is still not visible, reload the ACF Field Groups screen and confirm the server has commit `e43ff64` or later.

---

## What We Built This Session

### 1. Dynamic Header System (`header.php`, `style.css`, `scripts.js`, `functions.php`, `flexible-content.php`)

Header now automatically switches between two modes:

| Mode | When | Behavior |
|---|---|---|
| **Absolute** | First flexible content layout is `hero_section` or `inner_hero` | `position: absolute; top: 3.75rem` — overlays hero |
| **Static (sticky)** | No hero, no CMS, blog, archive, 404, single post | `position: sticky; top: 0; margin-top: 3.75rem` — in normal flow |

- Detection uses `epaton_has_hero_first_section()` in `flexible-content.php` (fixed: removed broken `is_home()` check)
- Body classes `has-hero-section` / `has-static-header` added via filter in `functions.php`
- `--header-offset` CSS variable updated by JS — `headerHeight + 60` (desktop) or `+ 30` (mobile ≤767px)

### 2. Admin Bar Support (`style.css`)

- `.admin-bar` class compensates: header `top`, sticky `top`, and scrolled `position: fixed` offsets
- Desktop: `+32px`, Mobile ≤782px: `+46px`
- Hero/inner-hero padding-top stays correct with admin bar visible

### 3. Hero & Inner Hero — Dynamic Gaps (`style.css`)

Replaced hardcoded `padding-top` / `margin-top` with dynamic `calc(var(--header-offset) + var(--hero-gap))`:

| | Hero gap | Inner hero gap |
|---|---|---|
| Desktop | `7.5rem` (120px) | `4.063rem` (65px) |
| Tablet ≤991px | `5rem` | `3.125rem` |
| Mobile ≤767px | `3.75rem` | `2.5rem` |

- Bottom padding preserved: hero `8rem/6rem/4rem`, inner-hero `6rem/5rem/4rem`
- Mobile: text centered, buttons column

### 4. Eyebrow Fields (`acf-json`, templates, CSS)

- **Core Areas**: `core_areas_eyebrow` — default "What We Do"
- **Inner Hero**: `inner_hero_eyebrow` — seeded "WHO WE ARE", "OUR PRODUCTS", "OUR PARTNERS"
- Both: cyan, `0.875rem`, uppercase, 600 weight

### 5. Global Contact CTA (`acf-json`, `site-settings.php`, `contact_cta.php`)

- **Site Settings > Contact CTA** tab: global title, body, button style, button link
- **Flexible content toggle**: `contact_cta_use_global` (true_false, default ON)
  - ON → pulls from Site Settings
  - OFF → shows per-page custom fields
- `epaton_render_global_contact_cta()` renders it anywhere (used on blog, archive, index)
- Contact CTA footer spacing removed

### 6. Unified Non-Hero Page Spacing

All non-hero first-section spacings standardized to `5rem` desktop / `3rem` tablet / `2rem` mobile:

| Template | Changed from |
|---|---|
| `content.php` (single post) | `mt-lg-75` → `mt-lg-80` |
| `content-page.php` | `pt-lg-100` → `pt-lg-80` |
| `search.php` | `pt-lg-100`/`pt-lg-70` → `pt-lg-80` |
| `404.php` | Zero → `pt-lg-80` |
| `.epaton-breadcrumb` | `1.563rem` → `5rem` responsive |
| `.blog-grid-section` | Already correct |
| `.contact-panel-section` | Already correct |

### 7. Single Post / Entry Content Typography (`content.php`, `epaton-theme-style.css`)

- Post title (h1), author with avatar, date, category pills, featured image
- Full `.entry-content` typography: paragraphs, headings, lists, blockquotes, code, tables, figures, etc.
- Category pills: blue bg/white text, cyan hover
- Dot separators between meta items
- Border-bottom under meta row
- Wrapped in `.epaton-container`

### 8. Hamburger Menu — (+) Toggle Button (`hamburger-menu.js`, `style.css`, `hamburger-menu.php`)

- Circular `+` button injected via JS next to parent links
- Pure CSS `+` (horizontal + vertical bar) → `−` (horizontal bar only)
- Parent menu links remain clickable for navigation
- Toggle button: 36px circle, semi-transparent white, hover brightens
- Cleaned up old `::after` CSS arrow, removed walker dependency for mobile

### 9. Archive Page Matching Blog Layout (`archive.php`)

- Removed breadcrumb — now matches `index.php` layout
- `blog-index-header` with `the_archive_title()` + description
- Wrapped in `.epaton-container`, grid classes match

### 10. 404 Page Redesign (`404.php`, `epaton-theme-style.css`)

- Large blue "404" digits (`10rem` → `6rem` mobile)
- Clean title, description, centered button
- `min-height: 60vh` vertical centering
- Removed old CAA character animation classes

### 11. Footer Spacing (`style.css`)

- `.site-footer { margin-top: 240px }` — responsive: `120px` tablet, `80px` mobile

### 12. Header Box Shadow (`style.css`)

- `.site-header-bar` has `box-shadow: 0 4px 23.1px 0 rgba(0, 0, 0, 0.10)` on all header types

### 13. Select Dropdown Arrow

- Created `assets/svgs/select-arrow.svg` (was missing, causing no arrow on select fields)
- Referenced in `epaton-form.css` as `background-image`

### 14. Cleanup

- Deleted `faisal.css`, `imran.css` — all styles consolidated into `style.css`
- Removed enqueues from `functions.php`
- Removed padding-bottom from contact CTA and margin-bottom from contact panel
- Bumped `_S_VERSION` to `1.0.1`

---

## Files Changed (24 files)

| File | Change |
|---|---|
| `404.php` | Redesigned |
| `acf-json/group_flexible_content.json` | Eyebrow fields, contact CTA toggle |
| `acf-json/group_site_settings.json` | Global contact CTA tab |
| `archive.php` | Matched blog layout |
| `assets/css/epaton-theme-style.css` | Entry content, 404, blog, entry meta |
| `assets/js/hamburger-menu.js` | + toggle button, clickable parent links |
| `assets/js/scripts.js` | Dynamic topGap (60/30) |
| `assets/svgs/select-arrow.svg` | **New** — select dropdown arrow |
| `faisal.css` | **Deleted** |
| `functions.php` | Body class filter, version bump, removed imran/faisal enqueues |
| `header.php` | Dynamic header class |
| `imran.css` | **Deleted** |
| `inc/helper-functions/flexible-content.php` | Fixed `epaton_has_hero_first_section()` |
| `inc/helper-functions/site-settings.php` | `epaton_get_global_contact_cta()`, `epaton_render_global_contact_cta()` |
| `inc/wp-cli/acf-content-seeder.php` | Eyebrow + contact CTA toggle data |
| `index.php` | Global contact CTA |
| `search.php` | Unified spacing |
| `style.css` | Dynamic header, admin bar, hero/inner-hero gaps, box-shadow, footer margin, hamburger toggle, blog/contact spacing |
| `template-parts/content-page.php` | Unified spacing |
| `template-parts/content.php` | Post header, meta, featured image, container |
| `template-parts/sections/contact_cta.php` | Global/local toggle, removed pb |
| `template-parts/sections/contact_panel.php` | Removed mb |
| `template-parts/sections/core_areas.php` | Eyebrow |
| `template-parts/sections/inner_hero.php` | Eyebrow, dynamic padding |

---

## Key CSS Custom Properties

| Variable | Set by | Purpose |
|---|---|---|
| `--header-offset` | JS | Header bar height + top gap (60px/30px) |
| `--hero-gap` | CSS `:root` | Gap between header and hero content |
| `--inner-hero-gap` | CSS `:root` | Gap between header and inner hero content |

---

## Performance & SEO Optimisation — Completed

Committed and pushed in `9162033` (`Optimize theme performance and technical SEO`).

### Asset Loading

- Removed Slick completely:
  - `assets/css/slick.css`
  - `assets/css/epaton-slick-custom.css`
  - `assets/js/slick.js`
  - `assets/js/epaton-carousels.js`
- Vimeo support remains. The current ACF fields expect a **direct Vimeo MP4 URL**, rendered through the native HTML5 video helper.
- The unused `jquery.mb.vimeo_player.min.js` file remains available but is not enqueued.
- Removed the theme's frontend jQuery dependency.
- Rewrote core header and hamburger interactions in vanilla JavaScript.
- Theme scripts use the WordPress `defer` strategy.
- Video CSS/JS loads only when flexible content contains video media.
- Form CSS and Contact Form 7 assets load only on pages that contain a contact form.
- Removed unused frontend block global styles and emoji scripts.

### Font Loading

- Instrument Sans is now self-hosted as two Latin variable WOFF2 files:
  - Normal `400–700`
  - Italic `400–700`
- Font files live under `assets/fonts/instrument-sans/`.
- `assets/css/fonts.css` contains the local `@font-face` declarations.
- Normal font is preloaded; italic loads on demand.
- Google Fonts stylesheet, DNS lookup, and preconnect were removed.
- SIL Open Font License is included as `assets/fonts/instrument-sans/OFL.txt`.
- Existing body font size, weight, and line-height were intentionally preserved after review.

### Images and Video Embeds

- First hero image is preloaded with responsive `imagesrcset`/`imagesizes`.
- Responsive images include width, height, and `decoding="async"` to reduce CLS.
- Below-fold images remain lazy loaded.
- YouTube thumbnails and embeds now include dimensions and lazy loading.

### Header and Mobile Menu

- Fixed hero-header jump when switching from absolute to fixed:
  - `60px` threshold desktop
  - `30px` threshold mobile
- Scroll work is scheduled with `requestAnimationFrame`.
- Static sticky headers skip unnecessary scroll-class updates.
- Mobile submenu slide animation restored without jQuery.
- Added `aria-expanded` and `aria-controls` handling.

### Contact CTA and Heading Fixes

- Global and per-page Contact CTA button style selections now visually apply:
  - `cyan` → `btn-accent`
  - `blue` → `btn-primary`
- CSS no longer forces all CTA buttons to cyan.
- Contact page now receives an `h1` when `contact_panel` is its first layout.

### Technical SEO

- Added automatic meta descriptions with safe fallbacks.
- Added canonical URLs for singular, archive, and search routes.
- Added Open Graph and Twitter card metadata.
- Added JSON-LD graph data for:
  - Organization
  - WebSite/SearchAction
  - Article
  - BreadcrumbList
- Placeholder social links such as `#` are excluded from schema.
- Search and 404 pages receive `noindex` directives.
- Metadata/schema output yields to Yoast, Rank Math, AIOSEO, and SEOPress to avoid duplicates.

### Validation Completed

- All theme PHP files pass `php -l`.
- ACF JSON files parse successfully.
- Modified JavaScript files pass `node --check`.
- `git diff --check` passes.
- Local homepage returned HTTP 200 with:
  - Local fonts
  - Hero image preload
  - Canonical and social metadata
  - JSON-LD schema
  - Deferred theme scripts
  - No Slick or Google Fonts requests
- Contact page confirmed conditional CF7/form assets and one `h1`.

## Important Production Setting

The local WordPress site currently has **Settings > Reading > Discourage search engines** enabled (`blog_public=0`). This causes:

- Global `noindex, nofollow`
- Core XML sitemap disabled (`/wp-sitemap.xml` returns 404)
- Virtual `robots.txt` unavailable in the local subdirectory setup

Before production launch, disable **Discourage search engines**, then verify:

```bash
wp option update blog_public 1
wp rewrite flush --hard
wp cache flush
```

## Deployment Plan

The site can be deployed without WordPress Admin when SSH access is available.

### Theme-only deployment

Use `rsync` to upload the theme. Review the remote path before using `--delete`:

```bash
rsync -avz --delete \
  wp-content/themes/epaton/ \
  user@server:/path/to/wordpress/wp-content/themes/epaton/
```

### Full-site deployment

Required steps:

1. Upload WordPress files and `wp-content/uploads`.
2. Export/import the database.
3. Run serialized-safe URL replacement with WP-CLI:

```bash
wp search-replace \
  'http://localhost/ClientProjects/WordPress/2026/epaton' \
  'https://client-domain.com' \
  --all-tables --skip-columns=guid
```

4. Enable indexing, flush rewrites, and clear caches.

## Next Session

1. Obtain client SSH credentials, WordPress document root, production domain, and database access.
2. Back up the production files and database before deployment.
3. Deploy through SSH/WP-CLI.
4. Verify production SSL, redirects, permalinks, forms, video playback, menu behavior, and image loading.
5. Disable “Discourage search engines” and verify `/wp-sitemap.xml` and `robots.txt`.
6. Run production Lighthouse/PageSpeed tests for mobile and desktop.
7. Configure server/CDN caching, Brotli or Gzip, and long-lived cache headers for versioned assets.
8. Minify and audit remaining CSS, especially `assets/css/spacer.css` and legacy selectors.
9. Compress/convert large uploaded images to WebP or AVIF and review Vimeo MP4/poster sizes.
