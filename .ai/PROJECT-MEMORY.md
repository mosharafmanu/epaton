# Epaton Project Memory

Last updated: 2026-06-05 18:48 +06

## Project

- Theme path: `/Applications/AMPPS/www/ClientProjects/WordPress/2026/epaton/wp-content/themes/epaton`
- GitHub remote: `https://github.com/mosharafmanu/epaton-wp-theme.git`
- Branch: `main`
- Initial pushed commit: `984e285 Initial Epaton theme commit`
- Theme function prefix: `epaton_`
- ACF Flexible Content field: `cms`
- Active Home page ID observed via WP-CLI: `7`

## Header and Footer

Header/footer rebuilt from supplied screenshots.

Files:

- `header.php`
- `footer.php`
- `inc/helper-functions/site-settings.php`
- `acf-json/group_site_settings.json`
- Header/footer markup and settings are built. Detailed/pixel CSS has been intentionally removed from root `style.css` for the Figma styling pass.

Current Site Settings fields:

- `site_logo`
- `header_button`
- `footer_company_text`
- `footer_email`
- `footer_phone`
- `footer_offices` repeater (`city`, `address`)
- `footer_copyright`
- `footer_credit_text`

Footer legal links come from WordPress menu location `footerLegalMenu` (not ACF).
Header uses `mainMenu` with `Epaton_Primary_Menu_Walker` (`inc/components/header/class-epaton-primary-menu-walker.php`) — injects `.submenu-indicator` SVG arrow on parent items.

Seeder:

```bash
wp epaton seed-site-settings --path="/Applications/AMPPS/www/ClientProjects/WordPress/2026/epaton"
```

## ACF JSON — Critical Rules

**NEVER call `acf_update_field_group()` from WP-CLI or code** — it inserts a new DB row each time (because the JSON-loaded group has no DB post ID), creating duplicate field groups in the admin.

The correct workflow for JSON edits:
1. Edit the JSON file
2. Bump the `"modified"` Unix timestamp to the current time (`date +%s`)
3. Done — ACF reads from JSON automatically when timestamp is newer than DB

If duplicates appear in ACF admin, find them with:
```bash
wp post list --post_type=acf-field-group --post_status=any --fields=ID,post_name,post_date --path="..."
```
Delete all but the newest with `wp post delete <ID> --force --path="..."`.

Current DB entry for Page Builder: **ID 256** (2026-06-04 18:30:40).

## Post Types

- `service` — ACF CPT JSON `acf-json/post_type_service.json`, slug `/services/`, **archive disabled**
- `product` — ACF CPT JSON `acf-json/post_type_product.json`, slug `/products/`, **archive disabled**

Both CPT archives were disabled (`has_archive: false`) so that static pages with matching slugs are served by `page.php` with flexible content. Both post types synced to DB.

## Pages

| ID | Title | Slug | Notes |
|---|---|---|---|
| 7 | Home | `/` | Home page |
| 186 | Products | `/products/` | Uses page.php + flexible content |
| 192 | Partners | `/partners/` | Uses page.php + flexible content |
| 536 | Contact | `/contact/` | Uses page.php + flexible content |

Services page was planned to use page.php + flexible content with `inner_hero` + `services_listing`.

## Flexible Content Sections Built

All flexible content section templates and ACF layouts are built. Section styling was intentionally removed from root `style.css`; the user will do pixel-perfect styling from Figma.
Global container: `.epaton-container` (max-width `68.625rem` / `1098px`) in `assets/css/epaton-theme-style.css`.

| Layout name | Template | Notes |
|---|---|---|
| `hero_section` | `template-parts/sections/hero_section.php` | Home hero, image or video bg |
| `inner_hero` | `template-parts/sections/inner_hero.php` | Inner page hero, image or video bg, left-heavy overlay |
| `featured_services_intro` | `template-parts/sections/featured_services_intro.php` | Two-col intro + service cards |
| `core_areas` | `template-parts/sections/core_areas.php` | Three card grid |
| `approach_panels` | `template-parts/sections/approach_panels.php` | Bullets + two panels |
| `commitment_panel` | `template-parts/sections/commitment_panel.php` | Bullet list + statement |
| `looking_forward` | `template-parts/sections/looking_forward.php` | Centred bold statements |
| `contact_cta` | `template-parts/sections/contact_cta.php` | CTA card |
| `clients_logos` | `template-parts/sections/clients_logos.php` | Logo grid + copy |
| `products_listing` | `template-parts/sections/products_listing.php` | Auto-queries `product` CPT, horizontal cards |
| `services_listing` | `template-parts/sections/services_listing.php` | Auto-queries `service` CPT, alternating light/blue cards |
| `media_content_5050` | `template-parts/sections/media_content_5050.php` | Single 50/50 row, add multiple for stacked rows |
| `partners_listing` | `template-parts/sections/partners_listing.php` | Partner logo cards; theme choices are `blue` and `cyan` |
| `contact_panel` | `template-parts/sections/contact_panel.php` | Contact form + email/phone/social links |

## Inner Hero Section

Template: `template-parts/sections/inner_hero.php`

ACF fields (direct sub-fields of layout):
- `inner_hero_title` (textarea)
- `inner_hero_description` (textarea)
- `inner_hero_buttons` repeater (`button_link`, `button_style`)
- `inner_hero_media_type` (image / video toggle)
- `inner_hero_image` (image, conditional)
- `inner_hero_video` (group with full video source/behavior fields, conditional)

Dual-mode: works as flexible content section AND as direct `get_template_part()` call with `$args` if needed. Blog index no longer uses an inner hero.

Seeder for Products page (ID 186):

```bash
wp epaton seed-inner-hero-products --page_id=186 --path="/Applications/AMPPS/www/ClientProjects/WordPress/2026/epaton"
```

Optional: `--image_path=<path>` (default: `/Users/mosharafmanu/Desktop/epaton-assets/inner-hero.jpg`)

Seeded content:
- Title: `STORAGE, BACKUP, RECOVERY AND SOFTWARE DEFINED SOLUTIONS`
- Description: `A vendor independent specialist in Next Generation Storage, Backup, Recovery and Software Defined Solutions`
- Button: `Contact Epaton` → `/contact/`
- Background: `inner-hero.jpg`
- Asset key: `inner_hero_products_bg` (re-runs skip re-import)

## Products Listing Section

Template: `template-parts/sections/products_listing.php`

- Auto-queries all published `product` posts ordered by `menu_order`
- Uses `wp_get_attachment_image_url()` to build the array `['ID' => ..., 'url' => ...]` before passing to `epaton_render_responsive_picture()` — passing a bare int does NOT work (function requires array with `ID` and `url` keys)
- ACF fields: `products_listing_heading` (optional), `products_listing_button_text` (default: Find Out More)

Products page (ID 186) can be seeded with `inner_hero` + `products_listing`.

## Services Listing Section

Template: `template-parts/sections/services_listing.php`

- Auto-queries all published `service` posts ordered by `menu_order`
- ACF fields: `services_listing_heading` (optional), `services_listing_button_text` (default: Find Out More)

Services page should use `inner_hero` + `services_listing` sections.

## Media Content 50/50 Section

Template: `template-parts/sections/media_content_5050.php`

**One section instance = one row.** Add multiple "Media Content 50/50" sections to the page builder for stacked rows.

ACF fields (direct sub-fields, no repeater):
- **Content tab:** `mc5050_eyebrow`, `mc5050_title`, `mc5050_body` (wysiwyg), `mc5050_button` (link)
- **Media tab:** `mc5050_media_position` (right/left), `mc5050_media_type` (image/video), `mc5050_image`, `mc5050_video` (full video group)

Layout intent:
- `.media-right` (default): content left, media right
- `.media-left`: media left, content right
- Mobile styling will be handled in the Figma CSS pass

## Hero Section State

File: `template-parts/sections/hero_section.php`

ACF fields:
- `hero_title`, `hero_description`, `hero_buttons` repeater
- `hero_media_type`, `hero_image`, `hero_video` (full video group)

## Styling State

Root `style.css` currently only contains the WordPress theme header comment. This is intentional: all previous section CSS was removed so the user can build pixel-perfect styles from Figma. Do not re-add section CSS unless explicitly asked.

Global/base utilities remain in `assets/css/epaton-theme-style.css`.

## Assets

Source asset folder: `/Users/mosharafmanu/Desktop/epaton-assets`

- `hero.jpg` → `assets/images/hero.jpg`
- `Epaton Logo.svg` — imported to Media Library by site settings seeder
- `inner-hero.jpg` — imported to Media Library by inner hero seeder
- `client-logos/` — individual SVG client logo files
- `partners/` — individual partner logo files
- `socials/` — social media SVG icons for Site Settings
- `services/01.jpg` – `06.jpg` — service featured images
- `products/01.jpg` – `03.jpg` — product featured images

## Services Post Type

- ACF JSON: `acf-json/post_type_service.json`
- Post type key: `service`, slug: `/services/`, archive: **disabled**
- No Service Details ACF field group needed — use core WordPress excerpt for card descriptions
- Service media field group: `acf-json/group_service_media.json`
  - `service_secondary_thumbnail` — optional alternate image for featured service cards (falls back to featured image)

## Products Post Type

- ACF JSON: `acf-json/post_type_product.json`
- Post type key: `product`, slug: `/products/`, archive: **disabled**
- Products use featured image only — no separate media ACF group

## WP-CLI ACF Content Seeding

Seeder file: `inc/wp-cli/acf-content-seeder.php`
Loaded from `functions.php` only when `WP_CLI` is defined.

All commands:

```bash
wp epaton seed-site-settings --path="..."
wp epaton seed-hero --page_id=7 --path="..."
wp epaton seed-core-areas --page_id=7 --path="..."
wp epaton seed-approach-panels --page_id=7 --path="..."
wp epaton seed-commitment-panel --page_id=7 --path="..."
wp epaton seed-looking-forward --page_id=7 --path="..."
wp epaton seed-contact-cta --page_id=7 --path="..."
wp epaton seed-clients-section --page_id=7 --logos_dir="/Users/mosharafmanu/Desktop/epaton-assets/client-logos" --path="..."
wp epaton seed-services --assets_dir="/Users/mosharafmanu/Desktop/epaton-assets/services" --path="..."
wp epaton seed-products --assets_dir="/Users/mosharafmanu/Desktop/epaton-assets/products" --path="..."
wp epaton seed-inner-hero-products --page_id=186 --path="..."
wp epaton seed-inner-hero-partners --page_id=192 --path="..."
wp epaton seed-partners-listing --page_id=192 --logos_dir="/Users/mosharafmanu/Desktop/epaton-assets/partners" --path="..."
wp epaton seed-blog-posts --image_path="/Users/mosharafmanu/Desktop/epaton-assets/news.jpg" --path="..."
wp epaton seed-contact-form --path="..."
wp epaton seed-contact-page --page_id=536 --socials_dir="/Users/mosharafmanu/Desktop/epaton-assets/socials" --path="..."
```

If WP-CLI says `Error establishing a database connection`, start AMPPS MySQL first.

## Environment Notes

- WP-CLI: `/usr/local/bin/wp` v2.12.0
- ACF Pro: v6.3.8, active
- Active plugins: Classic Editor, SVG Support
- Gutenberg: disabled globally by the theme
- DB: `epaton` / `root` / `root` / `localhost`

SVG sideload note: SVG Support's WP-CLI sanitizer can fatal with a null service. Trusted local SVGs are copied directly into uploads and registered as attachments to bypass this.

## 2026-06-04 Container Width Update

- Added global `.epaton-container` in `assets/css/epaton-theme-style.css` (max-width `68.625rem` / `1098px`)
- All built sections use `.layout-padding` as outer gutter + `.epaton-container` for content

## Blog State

- Blog index uses `index.php`, not a flexible inner hero.
- Blog title/description are controlled from the Blog Options ACF options page under Posts.
- Blog card component lives in `inc/components/cards/blog-card.php`.
- Blog options JSON: `acf-json/group_blog_options.json` and `acf-json/ui_options_page_blog_options.json`.

## Partners State

- Partner page ID: `192`.
- Flexible layout: `partners_listing`.
- Logo theme choices are only `blue` and `cyan`.
- SVG color intent:
  - `blue`: white SVG fill/stroke
  - `cyan`: black SVG fill/stroke
- If the client uploads separate white/black image assets, that is also fine.

## Contact State

- Contact page ID: `536`.
- Flexible layout: `contact_panel`.
- Contact Form 7 form ID used by seeder: `535`.
- Site Settings includes social links repeater, read by `epaton_get_social_links()`.

## 2026-06-05 Current Handoff Checklist

1. Flexible content sections are built.
2. Section CSS was intentionally removed from `style.css`.
3. Next work is the user's Figma pixel-perfect CSS pass.
4. Check for stale/duplicate ACF field group entries if anything looks off in admin.
5. Commit all accepted work when ready.

## Uncommitted Files (as of 2026-06-05)

All changes from this session are uncommitted. Key files:

- `acf-json/group_flexible_content.json` — all new layouts
- `acf-json/group_site_settings.json`
- `acf-json/group_service_media.json`
- `acf-json/post_type_product.json` — archive disabled
- `acf-json/post_type_service.json` — archive disabled
- `assets/css/epaton-theme-style.css`
- `footer.php`
- `functions.php`
- `header.php`
- `inc/helper-functions/site-settings.php`
- `inc/wp-cli/acf-content-seeder.php`
- `inc/components/header/class-epaton-primary-menu-walker.php`
- `assets/svgs/epaton-angle-down.php`
- `style.css` — intentionally stripped back to theme header comment only
- `template-parts/sections/hero_section.php`
- `template-parts/sections/inner_hero.php`
- `template-parts/sections/featured_services_intro.php`
- `template-parts/sections/core_areas.php`
- `template-parts/sections/approach_panels.php`
- `template-parts/sections/commitment_panel.php`
- `template-parts/sections/looking_forward.php`
- `template-parts/sections/contact_cta.php`
- `template-parts/sections/clients_logos.php`
- `template-parts/sections/products_listing.php`
- `template-parts/sections/services_listing.php`
- `template-parts/sections/media_content_5050.php`
- `template-parts/sections/partners_listing.php`
- `template-parts/sections/contact_panel.php`
- `inc/wp-cli/acf-content-seeder.php`
