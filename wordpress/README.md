# Hotel Cosmopolitan — WordPress conversion

Conversion of the static PHP site under `public_html/` to a dynamic WordPress site running on Divi.

**👉 Start here: [SETUP.md](./SETUP.md)** — step-by-step install instructions for your hosting.

## At a glance

- **Parent theme:** Divi (you supply the zip from your Elegant Themes account)
- **Child theme:** `wp-content/themes/hotelcosmopolitan-child/` — preserves the original red `#D81418` + Poppins look
- **Plugins:** 3 custom plugins (`hc-rooms`, `hc-blogs`, `hc-inquiries`)
- **Required dependency:** ACF Pro
- **Booking integration:** SwiftBook (preserved — same URL as the original)

## Repo layout

```
public_html/                              ← original static site (unchanged, for reference)
wordpress/                                ← THIS deliverable
├── SETUP.md                              ← install guide
├── README.md                             ← this file
├── scripts/copy-assets.sh                ← run once before zipping the theme
├── wp-content/
│   ├── themes/hotelcosmopolitan-child/   ← Divi child theme
│   └── plugins/
│       ├── hc-rooms/                     ← Room CPT + ACF fields + shortcodes + seed data
│       ├── hc-blogs/                     ← Blog CPT + grid shortcode
│       └── hc-inquiries/                 ← Forms, DB table, admin UI, email
└── divi-layouts/                         ← Divi layout JSONs (import via Divi Library)
```

## What's Phase 1?

This PR delivers the foundation: child theme + 3 plugins + Home page + Rooms archive + Single Room template. See [SETUP.md → Phase 2 / 3 backlog](./SETUP.md#phase-2--3-backlog) for what's next.
