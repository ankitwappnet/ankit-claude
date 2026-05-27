# Hotel Cosmopolitan — WordPress / Divi Setup

This package converts the existing static site at `public_html/` into a dynamic WordPress site using **Divi** as the parent theme.

## What's in this package

```
wordpress/
├── SETUP.md                                            ← this file
├── scripts/copy-assets.sh                              ← copies images/ + fonts/ into the child theme
├── wp-content/themes/hotelcosmopolitan-child/          ← Divi child theme (red #D81418 + Poppins)
├── wp-content/plugins/hc-rooms/                        ← "Room" CPT + ACF fields + shortcodes
├── wp-content/plugins/hc-blogs/                        ← "Blog" CPT for news/blogs
├── wp-content/plugins/hc-inquiries/                    ← inquiry/booking form, DB storage, email
└── divi-layouts/                                       ← Divi layout JSONs (Home, Rooms archive, Single Room template)
```

## What's dynamic vs. still to do

| Area | Status |
|---|---|
| Rooms (Executive / Premium / Presidential / Luxury / Deluxe) | ✅ CPT + ACF fields + **auto-seed of 5 rooms with images imported into Media Library** |
| Blogs / news-blogs | ✅ CPT + grid shortcode |
| Inquiry & booking forms | ✅ Custom plugin, admin UI, email + DB |
| Site Content (Hero slides, Facility icons, Gallery, Testimonials, Awards, Contact, Restaurant hours, Banquet/Conference/Cuisine categories) | ✅ **ACF Options page + full seed of original content** |
| Header / footer / global styling | ✅ Child theme CSS + custom footer widget area |
| Home page layout | ✅ Importable Divi JSON (5 sections matching original) |
| About Us layout | ✅ Importable Divi JSON |
| Contact Us layout | ✅ Importable Divi JSON (form + contact info + map) |
| Gallery layout | ✅ Importable Divi JSON (filterable grid) |
| Restaurant layout | ✅ Importable Divi JSON (gallery + cuisines + hours sidebar) |
| Banquet Hall layout | ✅ Importable Divi JSON (with event categories) |
| Conference Room layout | ✅ Importable Divi JSON (with use-case categories) |
| Facilities index | ✅ Importable Divi JSON |
| Rooms archive page | ✅ Importable Divi JSON |
| Single Room template | ✅ Theme Builder template JSON |
| **Phase 3 backlog** — FAQ, policies, single Blog template, 30+ SEO landing pages | ⬜ Future PR |

## Prerequisites on hosting

1. PHP 7.4+ (PHP 8.1+ preferred)
2. MySQL 5.7+ / MariaDB 10.3+
3. WordPress 6.0+ — install via cPanel / Softaculous / manually
4. Your Elegant Themes **Divi** zip (you have the license)
5. **ACF Pro** zip from advancedcustomfields.com (required for Repeater + Gallery fields)

## Install steps (production)

### 1. Install WordPress
Install WP at `hotelcosmopolitan.in` via your hosting panel. Note the WP admin URL.

### 2. Install Divi (parent theme)
*WP-Admin → Appearance → Themes → Add New → Upload Theme* → upload `Divi.zip` → **Activate**.

Then enter your Elegant Themes API key at *Divi → Theme Options → Updates*.

### 3. Build the child theme zip
On your machine (where this repo is checked out):

```bash
bash wordpress/scripts/copy-assets.sh
cd wordpress/wp-content/themes
zip -r hotelcosmopolitan-child.zip hotelcosmopolitan-child
```

Upload `hotelcosmopolitan-child.zip` via *WP-Admin → Appearance → Themes → Add New → Upload Theme* → **Activate**.

### 4. Install ACF Pro
Upload `advanced-custom-fields-pro.zip` via *WP-Admin → Plugins → Add New → Upload Plugin* → **Activate** → enter your ACF license key.

### 5. Install the three custom plugins
Zip each plugin folder individually:

```bash
cd wordpress/wp-content/plugins
zip -r hc-rooms.zip      hc-rooms
zip -r hc-blogs.zip      hc-blogs
zip -r hc-inquiries.zip  hc-inquiries
```

Upload + activate each via *WP-Admin → Plugins → Add New → Upload Plugin*.

On activation:
- `hc-rooms` registers the **Rooms** menu and auto-seeds 5 rooms (Executive, Premium, Presidential, Luxury, Deluxe). The 5 rooms come with the bed type, room size, amenities and booking options from the original site.
- `hc-blogs` registers the **Blogs** menu.
- `hc-inquiries` creates the `wp_hc_inquiries` table and adds the **Inquiries** menu.

### 5b. Verify auto-seed completed

On the next admin page-load after activating the plugins, the seeders run automatically:

1. *Rooms → All Rooms* — should show **5 rooms** (Executive, Premium, Presidential, Luxury, Deluxe) with featured images and gallery images populated.
2. *Site Content* (left-side menu) — should show **Gallery** (~22 images), **Testimonials** (3), **Awards** (10), **Facility Icons** (6), **Hero Slides** (5), **Banquet/Conference/Cuisine categories**, **Contact info** all pre-populated from the original site.
3. *Media* — should show all the imported assets.

If something is missing, check that `wordpress/wp-content/themes/hotelcosmopolitan-child/assets/images/` exists on the server (this is where the seeder reads from — created by `copy-assets.sh`).

To re-seed (if needed): delete the `hc_rooms_seeded` and `hc_site_content_seeded` rows from `wp_options` and reload WP-Admin.

### 6. Import Divi layouts

Create the pages first (*Pages → Add New*), then import each layout. For every page below:
1. Create the page with the specified slug.
2. Click "Use Divi Builder" → "Build From Scratch".
3. In Divi Builder, click the gear/portability icon (top-right) → **Import** → upload the JSON file.
4. Save.

| Page Title | Slug | Import file |
|---|---|---|
| Home | (set as front page) | `home-layout.json` |
| About Us | `about-us` | `about-us-layout.json` |
| Contact Us | `contact-us` | `contact-us-layout.json` |
| Gallery | `gallery` | `gallery-layout.json` |
| Coriander Restaurant | `restaurant` | `restaurant-layout.json` |
| Banquet Hall | `banquet-hall` | `banquet-hall-layout.json` |
| Board Room | `conference-room` | `conference-room-layout.json` |
| Facilities | `facilities` | `facilities-layout.json` |
| Rooms | `rooms` | `rooms-archive-layout.json` |

After importing **Home**: *Settings → Reading* → set **Your homepage displays** to "A static page" → Homepage = Home.

**Token replacements:** A few layouts contain placeholders like `@@HC_THEME_URI@@` (theme URL) and `@@HC_RESTAURANT_GALLERY@@` (gallery IDs). Open each layout in Divi Builder and:
- Replace `@@HC_THEME_URI@@` in image-src fields with your actual site URL prefix (e.g. `https://hotelcosmopolitan.in/wp-content/themes/hotelcosmopolitan-child`)
- For `@@HC_RESTAURANT_GALLERY@@` (in `restaurant-layout.json`), the Divi gallery module — click the gallery icon, remove the placeholder, and pick the restaurant images from Media Library (they're already imported there).

**Rooms archive:**
1. Create a Page titled **Rooms** (slug `rooms`).
2. Use Divi Builder → Import → `rooms-archive-layout.json` → Save.
3. (Optional) Replace the CPT archive: this page now serves the rooms listing. The CPT's own archive at `/rooms/` will conflict — go to *WP-Admin → Settings → Permalinks*, save once, then the page will take precedence over the CPT archive (because pages outrank CPT archives in the rewrite hierarchy).

**Single Room template (Theme Builder):**
1. *WP-Admin → Divi → Theme Builder* → **Add New Template** → assign to "All Rooms" (`room` post type) → save the template.
2. Click "Add Custom Body" → "Build Custom Body" → Import → `single-room-template.json` → Save.
3. Visit `/room/executive-room/` to verify the template renders.

### 7. Set up menus

*WP-Admin → Appearance → Menus*:

**Primary Menu** (assign to location "Primary Menu"):
- Home → `/`
- Rooms → `/rooms/` *(with submenu of all 5 rooms)*
- Restaurant → `/restaurant/` *(create in Phase 2)*
- Banquet Hall → `/banquet-hall/`
- Board Room → `/conference-room/`
- Gallery → `/gallery/`
- Contact Us → `/contact-us/`

The child theme automatically appends a "Book Now" button to the primary menu (filterable via the `hc_book_now_url` filter).

### 8. Set inquiry recipient email

*WP-Admin → Inquiries → Settings* — confirm the recipient is `reserve@hotelcosmopolitan.in` (or change it).

If outbound email is unreliable, install an SMTP plugin (e.g., FluentSMTP) and route through your transactional provider.

## Customizing rooms

*WP-Admin → Rooms → choose a room*:
- **Title** — room name
- **Featured image** — used on the rooms grid card
- **Editor** — long description shown on single room page
- **Short Description** — used on the card
- **Bed Type / Room Size / Price From**
- **Amenities** — repeater (icon + label)
- **Gallery** — multi-image gallery shown in the single-room carousel
- **Booking Options** — repeater of "label + URL" rows (e.g. *Room Only*, *Room + Breakfast*)

## Shortcodes reference

### Rooms
| Shortcode | Where to use |
|---|---|
| `[hc_rooms_grid columns="2"]` | Rooms archive, home page |
| `[hc_room_type_select]` | Rooms archive (quick-jump dropdown) |
| `[hc_room_gallery]` | Single room template |
| `[hc_room_amenities]` | Single room template |
| `[hc_room_booking]` | Single room template / sidebar |

### Home page sections
| Shortcode | Reads from |
|---|---|
| `[hc_hero_carousel]` | Site Content → Hero Slides |
| `[hc_facility_icons]` | Site Content → Facility Icons |
| `[hc_category_cards]` | Hard-coded 4-card grid (Rooms/Restaurant/Banquet/Board Room) |
| `[hc_gallery_slider]` | Site Content → Gallery |
| `[hc_testimonials]` | Site Content → Testimonials |
| `[hc_awards]` | Site Content → Awards |
| `[hc_counters]` | Hard-coded (50+ Rooms, 70+ Staff, 100+ Dishes) |

### Page sections
| Shortcode | Reads from |
|---|---|
| `[hc_gallery_grid filters="yes" columns="3"]` | Site Content → Gallery (filterable) |
| `[hc_contact_info style="light"]` | Site Content → Contact tab |
| `[hc_map]` | Site Content → Map URL |
| `[hc_restaurant_hours]` | Site Content → Restaurant tab |
| `[hc_category_grid type="banquet"]` | Site Content → Banquet categories |
| `[hc_category_grid type="conference"]` | Site Content → Conference categories |
| `[hc_category_grid type="cuisine"]` | Site Content → Restaurant cuisines |
| `[hc_footer_widgets]` | Auto-injected above Divi footer |

### Blogs / forms
| Shortcode | Where to use |
|---|---|
| `[hc_blogs_grid limit="6" columns="3"]` | Home page, blogs archive |
| `[hc_inquiry_form]` | Contact page |
| `[hc_inquiry_form variant="booking"]` | Home, single room, booking page |
| `[hc_page_title title="…" background="https://…"]` | Any page that needs the dark page-title header |

## SEO migration

The original site uses paths like `/about-us`, `/rooms`, `/executive-room`, `/banquet-hall`, etc. **These slugs are preserved** by the WP setup above:
- `about-us`, `contact-us`, etc. → standard WP pages with matching slugs
- `rooms` → archive page (handled in step 6.2)
- `executive-room`, `premium-room`, etc. → CPT singles at `/room/{slug}/`

**⚠️ Watch out:** original room URLs are `/executive-room` (no `/room/` prefix). For 1:1 URL parity, either:
- (a) Add 301 redirects in `.htaccess` mapping `/executive-room → /room/executive-room/`, or
- (b) In `wp-content/plugins/hc-rooms/includes/cpt.php`, change the `rewrite` to `array( 'slug' => '', 'with_front' => false )` — but this can clash with pages, so test on staging first.

For the 30+ SEO landing pages (`3-star-hotel-near-railway-station`, etc.), create them as regular WP pages with matching slugs and Divi-built content. Phase 3 deliverable will template these.

## Phase 2 / 3 backlog

| Page | Source file | Approach |
|---|---|---|
| About Us | `public_html/about-us.php` | Divi layout JSON |
| Contact Us | `public_html/contact-us.php` | Divi layout + `[hc_inquiry_form]` |
| Gallery | `public_html/gallery.php` | Divi Gallery module |
| Restaurant | `public_html/restaurent.php` | Divi layout JSON |
| Banquet Hall | `public_html/banquet-hall.php` | Divi layout JSON |
| Conference Room / Board Room | `public_html/conference-room.php` | Divi layout JSON |
| Facilities index | `public_html/facilities.php` | Divi layout JSON |
| FAQ / Policies | `public_html/faq.php`, `terms-condition.php`, etc. | Plain pages |
| News & Blogs index | `public_html/news-blogs.php` | `[hc_blogs_grid]` on a page |
| Single Blog template | n/a | Theme Builder template |
| 30+ SEO landing pages | `public_html/3-star-*.php`, `4-star-*.php`, etc. | One "Landing" template + CSV import via WP All Import |

Once Phase 1 is verified in staging, we'll do these in Phase 2.
