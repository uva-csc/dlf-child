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
4. **The DLF Core plugin** —
   [`uva-csc/dlf-plugin`](https://github.com/uva-csc/dlf-plugin). It defines the
   `fellow` custom post type, the `fellowship_year` / `region` / `country`
   taxonomies, and the `dlf/v1/fellows` REST endpoint that this theme's templates
   and archive JS depend on (plus the editing UI and region auto-derivation).
   Install it as a normal plugin — deployed separately, **not** part of this
   theme repo. (It was previously a set of mu-plugins in the `dlf-wordpress`
   monorepo; consolidated into its own plugin repo 2026-07-29.)
5. **ACF (free)** — the two fellow text fields use ACF local field groups
   (registered by the DLF Core plugin above).

## Deploying to the WordPress site

This repo is **public**, so it can be cloned over HTTPS with no credentials,
deploy keys, or tokens. Two supported paths — pick one at deploy time.

### A. Git clone/pull over HTTPS (recommended)

If the host has shell access, deploy the theme as a checkout of this repo in
`wp-content/themes`:

```bash
cd wp-content/themes
git clone https://github.com/uva-csc/dlf-child.git dlf-child
# updates thereafter
cd dlf-child && git pull
```

If a `dlf-child` directory already exists there, move it aside first (e.g.
`mv dlf-child dlf-child.bak`) so the clone lands cleanly. Once switched to a
git checkout, updating the theme is just `git pull` — no re-upload, no re-zip.

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
git clone https://github.com/uva-csc/dlf-child.git dlf-child
```

## Building pages — how to recreate the theme's signature features

Most of the distinctive look is **CSS keyed to class names / anchors**, not
template code — so you reproduce a feature by pasting the right markup into a
**Custom HTML block** (or setting a Heading block's HTML anchor) in wp-admin,
and the stylesheet (`assets/css/site.css`) styles it. This section is the field
guide. Every class below is defined in `site.css`; search it for the
`/* ---- Name ---- */` banners to see the exact rules and inline notes.

### First, two things that govern everything

- **Content width.** Static pages (`page.php`) render post_content inside
  `.dlf-plain-page__inner`, a **960px centered column**. The homepage
  (`front-page.php`) renders its content *without* that wrapper, so homepage
  blocks can go edge-to-edge on their own. On a **static page**, anything that
  must be full-viewport-width has to "break out" of the 960px column (next
  point).
- **The full-bleed breakout trick.** To make a block span the whole viewport
  from inside a centered column, give it:

  ```css
  position: relative;
  left: 50%;
  width: 100vw;
  margin-left: -50vw;   /* must come after any `margin` shorthand */
  ```

  This is what the navy band and the mid-page section hero use. It only stays
  scrollbar-free because `body` (and ancestors) are set to **`overflow-x: clip`**
  — *not* `hidden`. `hidden` silently turns `<body>` into a scroll container and
  **freezes every scroll-driven parallax on the site**. If drifts ever stop
  animating, check for a stray `overflow-x: hidden` first.

### The "blue stripe" — full-bleed navy title band

The thick navy band with a centered heading (first used for the About page's
"Meet the Team"). It's an **opt-in CSS class**, so it works on any page — the
heading text is arbitrary:

- In the editor: add a **Heading (H2)** block, type the title, then under
  **Advanced → Additional CSS class(es)** add **`dlf-title-band`**. It becomes a
  full-bleed `rgba(8,28,99,0.9)` navy band — white, centered, uppercase, ~80px
  vertical padding.
- For the centered uppercase **subheads** under a band (e.g. "Leadership Team" /
  "Community Fellows"), add the class **`dlf-section-subhead`** to those Heading
  blocks the same way. It's opt-in per heading, so ordinary H3s elsewhere on the
  page are unaffected.
- Equivalent Custom HTML: `<h2 class="dlf-title-band">Your Title</h2>` and
  `<h3 class="dlf-section-subhead">Your Subhead</h3>`.
- To restyle either, edit `.dlf-plain-page__inner .dlf-title-band` /
  `.dlf-plain-page__inner .dlf-section-subhead` in `site.css`.

### Photo heroes at the top of a page

These are **template-driven** (`page.php` / `front-page.php`), not authored in
content — they come from the page's **Featured Image**:

- **Full-height sticky + parallax-drift hero** (`.dlf-hero--home`): the homepage
  and the About / Apply / The-Fellowship pages. The photo pins full-viewport and
  the content panel rides up over it as you scroll, with a subtle image drift.
  A page gets this by default (the "Page Hero" setting is "Tall parallax hero").
  Set the page's Featured Image and enable Kadence's Transparent Header (so the
  white logo/nav sit over the photo).
- **Short static banner** (`.dlf-hero--banner`): any page whose **"Page Hero"**
  setting is "Short banner (like Donate)" — Donate + Contact ship this way. A
  shorter, non-parallax banner matching the live site's SquareSpace page banner:
  height `clamp(320px, 34vw, 460px)`, image top-anchored (`object-position: top`)
  so subjects framed toward the top stay visible. Per-page crop tuning via a slug
  class, e.g. `.dlf-hero--banner-contact-us { object-position: 50% 15% }`.
  Switching a page to this style is a no-code editor setting — see **Creating a
  new page with the short (Donate-style) hero** below.

### Creating a new page with the short (Donate-style) hero

New pages default to the **tall parallax hero**. Switching a page to the shorter,
static Donate/Contact-style banner is a **per-page editor setting — no code**:

1. **Create the page** in wp-admin (Pages → Add New) and write its content.
2. Set the page's **Featured Image** — that image becomes the banner photo.
3. In the **"Page Hero"** box in the editor sidebar, choose **"Short banner
   (like Donate)"** (the default is "Tall parallax hero"). Update/Publish.

That's it — the page renders the short banner and gets the white nav-over-photo
transparent header automatically. The control is a metabox wired up in
`functions/page-hero.php`; the choice is stored in the page's `_dlf_hero_style`
post meta, which `page.php` and the transparent-header filter in `functions.php`
both read.

*Optional per-image crop tuning (code):* the banner emits a slug class, so you
can fine-tune a specific photo's framing in `site.css`, e.g.
`.dlf-hero--banner-get-involved .dlf-hero__img { object-position: 50% 20%; }`.
Purely cosmetic; the hero itself needs no code.

> **Deploy note:** the setting lives in post meta (the database), so like all
> content it travels with the site's database, not the theme repo. A page set to
> "Short banner" on ddev must have the same setting on the server — re-select it
> there, or let the database copy carry it.

### Mid-page full-bleed parallax section (`.dlf-section-hero`)

A 100vh full-bleed image band with an overlaid title, dropped **into page
content** (the About page's "Our Origin"). Paste into a **Custom HTML block**:

```html
<section class="dlf-section-hero">
  <img src="https://your-site/wp-content/uploads/your-photo.jpg" alt="">
  <h2 class="dlf-section-hero__title">Our Origin</h2>
</section>
```

- It uses the full-bleed breakout automatically. By default the image does a
  scroll-linked `view()` **drift**; if JS runs, `homepage-parallax.js` upgrades
  it to a **viewport-fixed background reveal** (the section scrolls over a
  pinned photo like a window). No URL config needed — the script copies the
  `<img>`'s own `src` into a CSS variable, so swapping the photo in the editor
  Just Works.
- Respects `prefers-reduced-motion` (falls back to a normal scrolling image).

### Full-bleed photo bands in content (`.dlf-content-photo`)

A plain full-width photo band (no title). Three flavors, all via a wrapper class
on the block:

- `dlf-content-photo` — static full-width photo (max-height 70vh).
- `dlf-content-photo dlf-content-photo--parallax` — adds the scroll drift.
- `dlf-content-photo dlf-content-photo--silhouette` — the viewport-fixed
  background reveal (the homepage silhouette), same JS mechanism as the section
  hero.

⚠️ **Full-bleed caveat on static pages:** `.dlf-content-photo` only spans edge-
to-edge on the **homepage**, because that template doesn't wrap content in the
960px column. On a `page.php` static page it would be capped at 960px — for a
full-viewport band there, use `.dlf-section-hero` (which includes the breakout)
or add the breakout rules yourself. These bands were authored as **Kadence Row
Layout + Image blocks**; the `--silhouette` fixed-bg targets Kadence's
`.kt-inside-inner-col` wrapper, so keep that structure if you rebuild them.

### Reusable content components (Custom HTML blocks)

Each of these is styled by a class in `site.css` — paste the markup into a
**Custom HTML block** and it picks up the styling. The snippets below are the
minimum structure; repeat the inner items as needed.

**Core-values circle row** — 5-up on desktop, 3-up on mobile; images are
auto-circled.

```html
<div class="dlf-values-grid">
  <img src="…" alt="Integrity">
  <img src="…" alt="Interdependence">
  <!-- …five total… -->
</div>
```

**Team / fellow headshot grid** — 4 per row, square crops, name under each.

```html
<div class="dlf-team-grid">
  <div class="dlf-team-member">
    <img src="…" alt="Jane Doe">
    <p>Jane Doe</p>
  </div>
  <!-- …repeat per person… -->
</div>
```

**FAQ accordion** — native `details`/`summary`, no JS. Group items under a
category heading.

```html
<h3 class="dlf-faq-category">Eligibility</h3>

<details class="dlf-faq-item">
  <summary>Who can apply?</summary>
  <p>Answer text here.</p>
</details>

<details class="dlf-faq-item">
  <summary>Is there an age limit?</summary>
  <p>Answer text here.</p>
</details>
```

**External-links row** — a horizontal row of plain links.

```html
<div class="dlf-external-links">
  <a href="…">Application portal</a>
  <a href="…">Guidelines (PDF)</a>
</div>
```

**Captioned photo** — image with optional small italic caption.

```html
<figure class="dlf-page-photo">
  <img src="…" alt="…">
  <figcaption>Countries our Fellows come from.</figcaption>
</figure>
```

**Pull-quote** and **outlined pill button** are homepage patterns — see the
`/* ---- Pull-quote ---- */` and `/* ---- Outlined pill button ---- */` sections
in `site.css` for their markup.

### Global type & motion conventions (gotchas)

- **Fonts** come from Than's Adobe Fonts kit (enqueued in `functions.php`);
  Kadence's Customizer can't reference it, so base `font-family` lives in
  `site.css`. Body copy is **weight 300** on purpose.
- **`strong`/`b` are pinned to 700** — the UA default `bolder` maps 300→400
  (not 700), so bold text looked non-bold until this was set explicitly.
- **Body `letter-spacing: .015em`** (≈0.27px at the 18px body) matches the live
  site's tracking, cascading to all paragraphs/list items.
- **Titles are uppercase** and use the heading font; hero/section titles cap
  around **49px** to match the live site.
- Every parallax/fixed-bg effect is **gated by `prefers-reduced-motion`** and
  degrades to a static image — preserve those fallbacks when editing.

### Adding a fellow

Fellows are a custom post type (**Fellows** in the wp-admin menu, groups icon),
registered by the **DLF Core plugin**
([`uva-csc/dlf-plugin`](https://github.com/uva-csc/dlf-plugin)) — not by this
theme. This theme only provides the templates that display them
(`archive-fellow.php`, `single-fellow.php`, `taxonomy.php`) and the archive facet
JS. So the plugin must be present on the site for any of this to work; adding a
fellow itself is pure wp-admin:

1. **Fellows → Add New.**
2. **Title** = the fellow's full name.
3. **Featured Image** = the headshot (this is what the cards, modal, and profile
   page all use — there is no separate photo field). Roughly square crops look
   best in the grid.
4. **Taxonomies** (boxes in the sidebar):
   - **Fellowship Years** — the cohort year (e.g. `2026`). One per fellow.
   - **Countries** — one or more countries. Can be multi-valued.
   - **Regions are set automatically** — a fellow's region(s) are derived from
     its countries on save, so there is *no* Regions box on the fellow screen.
     Each country's region is configured once at **Fellows → Countries** (a
     Region dropdown on the country's edit screen). See the DLF Core plugin.
   - Add a new country/year term by typing it if it doesn't exist yet; the
     archive filters pick up new terms automatically. (A brand-new country added
     inline has no region until one is assigned on the Countries screen — the
     plugin shows a reminder.)
5. **Fellow fields** (under the title, where the body editor used to be):
   - **Leadership Vision** (ACF) — optional; leave blank to omit that section on
     the profile.
   - **Project Description** (ACF) — the main project write-up.
   - **Learn More Links** ("Learn More Links" metabox) — a mini-repeater: click
     **+ Add Link**, fill **Link Text** + **URL** for each external link.
6. There is **no body editor** on the fellow screen (removed by the DLF Core
   plugin) — the structured fields above are the entire editing surface, and the
   profile page is built from them.
7. **Publish.** The fellow appears on `/fellows/` (archive grid + filters +
   modal) and at its own `/fellow/<slug>/` profile page immediately.

Bulk imports (the original 263-fellow load) are done differently — via a WXR
generated by the monorepo's `scripts/` pipeline — but for adding fellows
one at a time, the wp-admin flow above is all you need.

### Where the moving parts live

- `assets/css/site.css` — all component styles, with a searchable quick-index at
  the top and a `/* ---- ---- */` banner per section.
- `assets/js/homepage-parallax.js` — the fixed-background reveal enhancer
  (`.dlf-section-hero` and `.dlf-content-photo--silhouette`).
- `assets/js/fellows-archive.js` — the fellows archive facet filtering + modal.
- `page.php` / `front-page.php` — the hero chrome and content wrappers described
  above; `functions.php` — asset enqueues, image sizes, and the transparent-
  header filter.
