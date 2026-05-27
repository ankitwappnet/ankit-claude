<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcodes that render sections from the ACF Options page (site-options.php).
 * All consumed by the Divi layout JSONs in divi-layouts/.
 */

/**
 * [hc_hero_carousel] — 5-slide Bootstrap carousel matching index.php.
 */
add_shortcode( 'hc_hero_carousel', function () {
    $slides = get_field( 'hero_slides', 'option' );
    if ( ! $slides ) return '';

    ob_start(); ?>
    <div id="hcHero" class="carousel slide carousel-fade hc-hero-carousel" data-bs-ride="carousel" data-ride="carousel">
        <ol class="carousel-indicators">
            <?php foreach ( $slides as $i => $s ) : ?>
                <li data-bs-target="#hcHero" data-target="#hcHero" data-bs-slide-to="<?php echo $i; ?>" data-slide-to="<?php echo $i; ?>" class="<?php echo 0 === $i ? 'active' : ''; ?>"></li>
            <?php endforeach; ?>
        </ol>
        <div class="carousel-inner">
            <?php foreach ( $slides as $i => $s ) :
                $bg = ! empty( $s['image']['url'] ) ? $s['image']['url'] : ''; ?>
                <div class="carousel-item hc-hero-item<?php echo 0 === $i ? ' active' : ''; ?>" style="<?php echo $bg ? 'background-image:url(' . esc_url( $bg ) . ');' : ''; ?>">
                    <div class="carousel-caption">
                        <h<?php echo 0 === $i ? '1' : '2'; ?>><?php echo wp_kses_post( nl2br( $s['title'] ) ); ?></h<?php echo 0 === $i ? '1' : '2'; ?>>
                        <?php if ( ! empty( $s['rating_source'] ) ) : ?>
                            <div class="rating">
                                <span>
                                    <?php if ( ! empty( $s['rating_icon']['url'] ) ) : ?>
                                        <img src="<?php echo esc_url( $s['rating_icon']['url'] ); ?>" alt="<?php echo esc_attr( $s['rating_source'] ); ?>" class="img-fluid">
                                    <?php endif; ?>
                                    <strong><?php echo esc_html( $s['rating_value'] ); ?></strong> ★ |
                                    <?php echo esc_html( $s['rating_count'] ); ?> Reviews on <?php echo esc_html( $s['rating_source'] ); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#hcHero" data-target="#hcHero" data-bs-slide="prev" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden sr-only">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#hcHero" data-target="#hcHero" data-bs-slide="next" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden sr-only">Next</span>
        </button>
    </div>
    <style>
        .hc-hero-carousel { position:relative; }
        .hc-hero-carousel .hc-hero-item { min-height: 560px; background-size:cover; background-position:center; position:relative; }
        .hc-hero-carousel .hc-hero-item:before { content:""; position:absolute; inset:0; background:rgba(20,20,30,0.55); }
        .hc-hero-carousel .carousel-caption { position:absolute; left:0; right:0; bottom:auto; top:50%; transform:translateY(-50%); color:#fff; padding:0 20px; }
        .hc-hero-carousel .carousel-caption h1, .hc-hero-carousel .carousel-caption h2 { color:#fff; font-size:48px; font-weight:700; line-height:1.2; max-width:900px; margin:0 auto 20px; text-transform:uppercase; letter-spacing:1.5px; }
        .hc-hero-carousel .rating { display:inline-block; background:rgba(255,255,255,.12); padding:10px 20px; border-radius:2px; backdrop-filter:blur(6px); }
        .hc-hero-carousel .rating img { width:24px; height:24px; vertical-align:middle; margin-right:8px; }
        @media (max-width:768px){.hc-hero-carousel .carousel-caption h1, .hc-hero-carousel .carousel-caption h2{font-size:28px;}}
    </style>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_facility_icons] — 6-icon "Why Choose Us" strip.
 */
add_shortcode( 'hc_facility_icons', function () {
    $rows = get_field( 'facility_icons', 'option' );
    if ( ! $rows ) return '';

    ob_start(); ?>
    <div class="hc-facility-icons" style="display:grid;grid-template-columns:repeat(6,1fr);gap:24px;text-align:center;">
        <?php foreach ( $rows as $r ) : ?>
            <div class="hc-fac">
                <?php if ( ! empty( $r['icon']['url'] ) ) : ?>
                    <img src="<?php echo esc_url( $r['icon']['url'] ); ?>" alt="<?php echo esc_attr( $r['label'] ); ?>" style="height:60px;width:auto;margin:0 auto 10px;">
                <?php endif; ?>
                <h6 style="font-size:14px;font-weight:600;color:#14141e;text-transform:uppercase;letter-spacing:.5px;"><?php echo esc_html( $r['label'] ); ?></h6>
            </div>
        <?php endforeach; ?>
    </div>
    <style>@media(max-width:768px){.hc-facility-icons{grid-template-columns:repeat(3,1fr) !important;}}</style>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_category_cards] — 4 image-cards for Rooms/Restaurant/Banquet Hall/Board Room.
 */
add_shortcode( 'hc_category_cards', function () {
    // Try to import the home category images at render-time as a fallback
    $cards = array(
        array( 'label' => 'Rooms',        'url' => '/rooms/',            'src' => 'images/home/room.webp' ),
        array( 'label' => 'Restaurant',   'url' => '/restaurant/',       'src' => 'images/home/restaurent.webp' ),
        array( 'label' => 'Banquet Hall', 'url' => '/banquet-hall/',     'src' => 'images/home/banquet-hall.webp' ),
        array( 'label' => 'Board Room',   'url' => '/conference-room/',  'src' => 'images/home/conference-room.webp' ),
    );

    ob_start(); ?>
    <div class="hc-category-cards" style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
        <?php foreach ( $cards as $c ) :
            $img = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/' . $c['src']; ?>
            <a href="<?php echo esc_url( $c['url'] ); ?>" class="hc-cat-card" style="position:relative;display:block;overflow:hidden;">
                <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $c['label'] ); ?>" style="width:100%;height:280px;object-fit:cover;display:block;transition:transform .5s ease;">
                <div class="overlay" style="position:absolute;inset:0;background:linear-gradient(transparent 50%, rgba(0,0,0,.7));display:flex;align-items:flex-end;padding:20px;">
                    <h4 style="color:#fff;margin:0;font-size:22px;font-weight:600;text-transform:uppercase;"><?php echo esc_html( $c['label'] ); ?></h4>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <style>
        .hc-cat-card:hover img{transform:scale(1.05);}
        @media(max-width:768px){.hc-category-cards{grid-template-columns:repeat(2,1fr) !important;}}
    </style>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_gallery_grid] — filterable gallery grid (categories: room/reception/restaurent/hall/corridor).
 */
add_shortcode( 'hc_gallery_grid', function ( $atts ) {
    $atts = shortcode_atts( array( 'filters' => 'yes', 'columns' => 3 ), $atts );
    $items = get_field( 'gallery', 'option' );
    if ( ! $items ) return '';

    $cats = array( 'room' => 'Room', 'reception' => 'Reception', 'restaurent' => 'Restaurant', 'hall' => 'Banquet Hall', 'corridor' => 'Corridor' );

    ob_start(); ?>
    <div class="hc-gallery">
        <?php if ( 'yes' === $atts['filters'] ) : ?>
            <ul class="hc-gallery-filters" style="list-style:none;padding:0;margin:0 0 30px;text-align:center;display:flex;justify-content:center;gap:20px;flex-wrap:wrap;">
                <li><a href="#" class="hc-filter is-active" data-filter="all">All</a></li>
                <?php foreach ( $cats as $slug => $label ) : ?>
                    <li><a href="#" class="hc-filter" data-filter="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="hc-gallery-grid" style="display:grid;grid-template-columns:repeat(<?php echo intval( $atts['columns'] ); ?>,1fr);gap:14px;">
            <?php foreach ( $items as $g ) :
                if ( empty( $g['image']['url'] ) ) continue; ?>
                <a class="hc-gallery-item" data-cat="<?php echo esc_attr( $g['category'] ); ?>" href="<?php echo esc_url( $g['image']['url'] ); ?>" data-lightbox="hc-gallery">
                    <img src="<?php echo esc_url( $g['image']['url'] ); ?>" alt="<?php echo esc_attr( $g['image']['alt'] ?: 'Gallery image' ); ?>" style="width:100%;height:260px;object-fit:cover;display:block;">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <style>
        .hc-gallery-filters .hc-filter { color:#777; text-transform:uppercase; font-size:13px; letter-spacing:1px; padding-bottom:4px; border-bottom:2px solid transparent; }
        .hc-gallery-filters .hc-filter.is-active { color:#D81418; border-bottom-color:#D81418; }
        .hc-gallery-item { display:block; overflow:hidden; }
        .hc-gallery-item img { transition:transform .5s ease; }
        .hc-gallery-item:hover img { transform:scale(1.08); }
        .hc-gallery-item.is-hidden { display:none; }
        @media(max-width:768px){ .hc-gallery-grid{grid-template-columns:repeat(2,1fr) !important;} }
    </style>
    <script>
    (function(){
        var filters = document.querySelectorAll('.hc-gallery-filters .hc-filter');
        var items   = document.querySelectorAll('.hc-gallery-item');
        filters.forEach(function(btn){
            btn.addEventListener('click', function(e){
                e.preventDefault();
                filters.forEach(function(b){ b.classList.remove('is-active'); });
                btn.classList.add('is-active');
                var f = btn.getAttribute('data-filter');
                items.forEach(function(i){
                    i.classList.toggle('is-hidden', f !== 'all' && i.getAttribute('data-cat') !== f);
                });
            });
        });
    })();
    </script>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_gallery_slider] — owl-style horizontal slider for the home gallery section.
 */
add_shortcode( 'hc_gallery_slider', function () {
    $items = get_field( 'gallery', 'option' );
    if ( ! $items ) return '';

    ob_start(); ?>
    <div class="hc-gallery-slider" style="display:flex;overflow-x:auto;gap:14px;scroll-snap-type:x mandatory;padding-bottom:10px;">
        <?php foreach ( $items as $g ) :
            if ( empty( $g['image']['url'] ) ) continue; ?>
            <a href="<?php echo esc_url( $g['image']['url'] ); ?>" data-lightbox="hc-home-gallery" style="flex:0 0 300px;scroll-snap-align:start;">
                <img src="<?php echo esc_url( $g['image']['url'] ); ?>" alt="" style="width:300px;height:220px;object-fit:cover;display:block;">
            </a>
        <?php endforeach; ?>
    </div>
    <style>.hc-gallery-slider::-webkit-scrollbar{height:6px;}.hc-gallery-slider::-webkit-scrollbar-thumb{background:#D81418;}</style>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_testimonials] — 3-column reviews block.
 */
add_shortcode( 'hc_testimonials', function () {
    $rows = get_field( 'testimonials', 'option' );
    if ( ! $rows ) return '';

    ob_start(); ?>
    <div class="hc-testimonials" style="display:grid;grid-template-columns:repeat(<?php echo min( 3, count( $rows ) ); ?>,1fr);gap:30px;">
        <?php foreach ( $rows as $t ) : ?>
            <div class="hc-testimonial" style="background:rgba(255,255,255,.06);padding:30px;border-left:3px solid #D81418;">
                <p style="color:rgba(255,255,255,.9);font-style:italic;line-height:1.7;margin:0 0 20px;">&ldquo;<?php echo esc_html( $t['review'] ); ?>&rdquo;</p>
                <p style="margin:0;color:#fff;font-weight:600;letter-spacing:1px;">— <?php echo esc_html( $t['name'] ); ?>
                    <span style="color:#E88800;margin-left:8px;"><?php echo str_repeat( '★', intval( $t['stars'] ) ); ?></span>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
    <style>@media(max-width:768px){.hc-testimonials{grid-template-columns:1fr !important;}}</style>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_awards] — slider/grid of certificates.
 */
add_shortcode( 'hc_awards', function () {
    $awards = get_field( 'awards', 'option' );
    if ( ! $awards ) return '';

    ob_start(); ?>
    <div class="hc-awards-slider" style="display:flex;overflow-x:auto;gap:14px;scroll-snap-type:x mandatory;padding-bottom:10px;">
        <?php foreach ( $awards as $a ) : ?>
            <a href="<?php echo esc_url( $a['url'] ); ?>" data-lightbox="hc-awards" style="flex:0 0 220px;scroll-snap-align:start;">
                <img src="<?php echo esc_url( $a['url'] ); ?>" alt="<?php echo esc_attr( $a['alt'] ?: 'Award' ); ?>" style="width:220px;height:300px;object-fit:contain;background:#fff;padding:12px;border:1px solid #eee;display:block;">
            </a>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_contact_info] — phone/email/address block (used in contact page + footer).
 */
add_shortcode( 'hc_contact_info', function ( $atts ) {
    $atts = shortcode_atts( array( 'style' => 'light' ), $atts );
    $phones  = get_field( 'phones', 'option' );
    $emails  = get_field( 'emails', 'option' );
    $address = get_field( 'address', 'option' );

    ob_start(); ?>
    <div class="hc-contact-info hc-contact-<?php echo esc_attr( $atts['style'] ); ?>">
        <?php if ( $phones ) : ?>
            <div class="hc-ci-block"><h6>Phone</h6>
                <?php foreach ( $phones as $p ) : ?>
                    <p><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $p['value'] ) ); ?>"><?php echo esc_html( $p['value'] ); ?></a>
                        <?php if ( $p['label'] ) : ?><small style="opacity:.6;"> — <?php echo esc_html( $p['label'] ); ?></small><?php endif; ?>
                    </p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ( $emails ) : ?>
            <div class="hc-ci-block"><h6>Email</h6>
                <?php foreach ( $emails as $e ) : ?>
                    <p><a href="mailto:<?php echo esc_attr( $e['value'] ); ?>"><?php echo esc_html( $e['value'] ); ?></a></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ( $address ) : ?>
            <div class="hc-ci-block"><h6>Address</h6><p><?php echo esc_html( $address ); ?></p></div>
        <?php endif; ?>
    </div>
    <style>
        .hc-contact-light .hc-ci-block h6 { color:#14141e; font-size:13px; letter-spacing:1px; margin:18px 0 8px; }
        .hc-contact-dark  .hc-ci-block h6 { color:#fff;    font-size:13px; letter-spacing:1px; margin:18px 0 8px; }
        .hc-contact-light p, .hc-contact-light a { color:#666; }
        .hc-contact-dark  p, .hc-contact-dark  a { color:#ccc; }
        .hc-contact-info a:hover { color:#D81418; }
    </style>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_map] — Google Maps iframe.
 */
add_shortcode( 'hc_map', function () {
    $url = get_field( 'map_url', 'option' );
    if ( ! $url ) return '';
    return '<div class="hc-map"><iframe src="' . esc_url( $url ) . '" width="100%" height="450" style="border:0;display:block;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>';
} );

/**
 * [hc_restaurant_hours]
 */
add_shortcode( 'hc_restaurant_hours', function () {
    $rows    = get_field( 'restaurant_hours', 'option' );
    $qr      = get_field( 'restaurant_qr', 'option' );
    $reserve = get_field( 'restaurant_reserve_url', 'option' );
    if ( ! $rows ) return '';

    ob_start(); ?>
    <div class="hc-rest-hours" style="background:#f8f8f8;padding:30px;">
        <h3 style="margin-top:0;">Restaurant Hours</h3>
        <ul style="list-style:none;padding:0;margin:0;">
            <?php foreach ( $rows as $r ) : ?>
                <li style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #eee;">
                    <strong><?php echo esc_html( $r['meal'] ); ?></strong>
                    <span><?php echo esc_html( $r['time'] ); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php if ( $reserve ) : ?>
            <a href="<?php echo esc_url( $reserve ); ?>" target="_blank" rel="noopener noreferrer" class="hc-btn-primary" style="display:inline-block;margin-top:20px;">Reserve a Table</a>
        <?php endif; ?>
        <?php if ( ! empty( $qr['url'] ) ) : ?>
            <h5 style="margin-top:30px;">Scan QR to View Menu</h5>
            <img src="<?php echo esc_url( $qr['url'] ); ?>" alt="Menu QR" style="max-width:180px;display:block;margin-top:10px;">
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_category_grid type="banquet|conference|cuisine"] — generic image-tile grid.
 */
add_shortcode( 'hc_category_grid', function ( $atts ) {
    $atts = shortcode_atts( array( 'type' => 'banquet', 'columns' => 3 ), $atts );
    $field = $atts['type'] === 'conference' ? 'conference_categories'
           : ( $atts['type'] === 'cuisine'  ? 'restaurant_cuisines'  : 'banquet_categories' );
    $rows = get_field( $field, 'option' );
    if ( ! $rows ) return '';

    ob_start(); ?>
    <div class="hc-cat-grid" style="display:grid;grid-template-columns:repeat(<?php echo intval( $atts['columns'] ); ?>,1fr);gap:20px;">
        <?php foreach ( $rows as $r ) : ?>
            <div class="hc-cat-tile" style="position:relative;overflow:hidden;">
                <?php if ( ! empty( $r['image']['url'] ) ) : ?>
                    <img src="<?php echo esc_url( $r['image']['url'] ); ?>" alt="<?php echo esc_attr( $r['label'] ); ?>" style="width:100%;height:240px;object-fit:cover;display:block;">
                <?php endif; ?>
                <div style="position:absolute;inset:0;background:linear-gradient(transparent 50%, rgba(0,0,0,.7));display:flex;align-items:flex-end;padding:18px;">
                    <h5 style="color:#fff;margin:0;font-size:18px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;"><?php echo esc_html( $r['label'] ); ?></h5>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <style>@media(max-width:768px){.hc-cat-grid{grid-template-columns:repeat(2,1fr) !important;}}</style>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_counters] — about-us counters block (50+ Rooms, 70+ Staffs, 100+ Dishes).
 */
add_shortcode( 'hc_counters', function () {
    $items = array(
        array( 'number' => 50,  'label' => 'Rooms' ),
        array( 'number' => 70,  'label' => 'Staffs' ),
        array( 'number' => 100, 'label' => 'Dishes' ),
    );
    ob_start(); ?>
    <div class="hc-counters" style="display:grid;grid-template-columns:repeat(3,1fr);gap:30px;text-align:center;background:#14141e;color:#fff;padding:60px 30px;">
        <?php foreach ( $items as $c ) : ?>
            <div>
                <div style="font-size:56px;font-weight:700;color:#D81418;line-height:1;"><?php echo intval( $c['number'] ); ?>+</div>
                <div style="margin-top:10px;text-transform:uppercase;letter-spacing:2px;font-size:14px;"><?php echo esc_html( $c['label'] ); ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <style>@media(max-width:768px){.hc-counters{grid-template-columns:1fr !important;}}</style>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_footer_widgets] — full 4-column footer block matching component/footer.php.
 */
add_shortcode( 'hc_footer_widgets', function () {
    $phones  = get_field( 'phones', 'option' );
    $emails  = get_field( 'emails', 'option' );
    $address = get_field( 'address', 'option' );
    $fb      = get_field( 'facebook_url', 'option' );
    $ig      = get_field( 'instagram_url', 'option' );

    ob_start(); ?>
    <div class="hc-footer-widgets" style="display:grid;grid-template-columns:1.3fr 1fr 1fr 1.3fr;gap:40px;padding:60px 0;">
        <div>
            <p style="font-style:italic;color:#ccc;font-size:18px;">A symphony of luxury and comfort</p>
            <div style="margin-top:18px;">
                <?php if ( $fb ) : ?>
                    <a href="<?php echo esc_url( $fb ); ?>" target="_blank" rel="noopener" style="display:inline-block;width:36px;height:36px;line-height:36px;text-align:center;background:#222;color:#fff;margin-right:8px;">f</a>
                <?php endif; ?>
                <?php if ( $ig ) : ?>
                    <a href="<?php echo esc_url( $ig ); ?>" target="_blank" rel="noopener" style="display:inline-block;width:36px;height:36px;line-height:36px;text-align:center;background:#222;color:#fff;">i</a>
                <?php endif; ?>
            </div>
        </div>
        <div>
            <h3 style="color:#fff;font-size:14px;letter-spacing:2px;text-transform:uppercase;margin:0 0 16px;">Rooms</h3>
            <ul style="list-style:none;padding:0;margin:0;">
                <li><a href="/room/executive-room/">Executive</a></li>
                <li><a href="/room/premium-room/">Premium</a></li>
                <li><a href="/room/presidential-room/">Presidential</a></li>
                <li><a href="/room/luxury-room/">Luxury</a></li>
                <li><a href="/room/deluxe-room/">Deluxe</a></li>
            </ul>
        </div>
        <div>
            <h3 style="color:#fff;font-size:14px;letter-spacing:2px;text-transform:uppercase;margin:0 0 16px;">Quick Links</h3>
            <ul style="list-style:none;padding:0;margin:0;">
                <li><a href="/about-us/">About Us</a></li>
                <li><a href="/news-blogs/">News &amp; Blogs</a></li>
                <li><a href="/faq/">FAQ's</a></li>
                <li><a href="/gallery/">Gallery</a></li>
                <li><a href="/contact-us/">Contact Us</a></li>
            </ul>
        </div>
        <div>
            <h3 style="color:#fff;font-size:14px;letter-spacing:2px;text-transform:uppercase;margin:0 0 16px;">Contact Us</h3>
            <?php if ( $phones ) : ?>
                <?php foreach ( $phones as $p ) : ?>
                    <p style="margin:4px 0;"><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $p['value'] ) ); ?>"><?php echo esc_html( $p['value'] ); ?></a></p>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if ( $emails ) : foreach ( $emails as $e ) : ?>
                <p style="margin:4px 0;"><a href="mailto:<?php echo esc_attr( $e['value'] ); ?>"><?php echo esc_html( $e['value'] ); ?></a></p>
            <?php endforeach; endif; ?>
            <?php if ( $address ) : ?><p style="margin-top:10px;color:#ccc;"><?php echo esc_html( $address ); ?></p><?php endif; ?>
        </div>
    </div>
    <style>
        .hc-footer-widgets ul li { padding:4px 0; }
        .hc-footer-widgets ul li a, .hc-footer-widgets p a { color:#ccc; }
        .hc-footer-widgets ul li a:hover, .hc-footer-widgets p a:hover { color:#D81418; }
        @media(max-width:768px){.hc-footer-widgets{grid-template-columns:1fr 1fr !important;}}
    </style>
    <?php
    return ob_get_clean();
} );
