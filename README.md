# DLF (Kadence Child) theme — `dlf-child`

Custom WordPress child theme for the **Dalai Lama Fellows** site
(dalailamafellows.com), maintained by UVA Contemplative Sciences Center.

This is a **Kadence child theme**. Site chrome (header, footer, nav, base
colors/typography, buttons) lives in Kadence Global Styles + the Header/Footer
Builder, configured in wp-admin (Customizer → Kadence) — **not** in this
repo. The theme code here carries only the custom components Kadence/core
blocks don't provide: the fellows archive grid + modal, the mid-page
full-bleed parallax section-hero, the core-values icon row, the team grid, and
the accessible scroll-driven parallax variants.

## Runtime dependencies

A fresh clone of this theme is **not** self-sufficient. The running site also
needs:

1. **Kadence parent theme** — this is a child theme (`Template: kadence` in
   `style.css`). Install the free Kadence theme first.
2. **Kadence Blocks** plugin — used by the homepage's full-bleed Row Layout
   photos.
3. **Adobe Fonts kit** `https://use.typekit.net/dkh7eln.css` (Than Grove's
   Creative Cloud account) — enqueued in `functions.php`. Supplies the site's
   Futura PT / Proxima Nova. The kit's allowed-domains list must include the
   site's hostname or the fonts won't load.
4. **The `dlf` data model (mu-plugins)** — `dlf-model.php`, `dlf-rest.php`,
   `dlf-acf-fields.php`, `dlf-learn-more.php`, `dlf-redirects.php`. These define
   the `fellow` custom post type, the `fellowship_year` / `region` / `country`
   taxonomies, and the `dlf/v1/fellows` REST endpoint that this theme's
   templates and archive JS depend on. They live in the
   [`uva-csc/dlf-wordpress`](https://github.com/uva-csc/dlf-wordpress) monorepo
   under `web/wp-content/mu-plugins/`, and are deployed to the server via the
   UpdraftPlus backup/restore flow — **not** part of this theme repo.
5. **ACF (free)** — the two fellow text fields use ACF local field groups
   (registered by the mu-plugin above).

## Deploying to the WordPress site

Two supported paths. Pick one at deploy time.

### A. Git clone/pull over SSH (recommended)

The host (a2hosted / hosting.com) has SSH + WP-CLI. Deploy the theme as a
checkout of this repo:

```bash
cd wp-content/themes
# first time — replace the restored theme dir with a git checkout
mv dlf-child dlf-child.bak            # keep the restore as a backup
git clone git@github.com:uva-csc/dlf-child.git dlf-child
# updates thereafter
cd dlf-child && git pull
```

The server's current `dlf-child` dir came from an UpdraftPlus restore, so the
first switch to git-deploy means replacing that directory with a clone.

### B. ZIP upload via wp-admin (fallback)

GitHub → **Code → Download ZIP**, then wp-admin → **Appearance → Themes →
Add New → Upload Theme**. Note the ZIP's top folder is `dlf-child-main/`;
rename it to `dlf-child/` if WordPress complains, or upload and let it install
then rename on disk.

## Local development

Local dev happens in the `uva-csc/dlf-wordpress` monorepo (DDEV project
`dlf-wordpress`). The monorepo no longer tracks this theme's files — clone this
repo into place:

```bash
cd web/wp-content/themes
git clone git@github.com:uva-csc/dlf-child.git dlf-child
```

## Docs

Design/decision write-ups (parallax technique, the Kadence conversion, this
repo split) live in the monorepo under `docs/`.
