<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Site-content shortcodes (no ACF dependency).
 * Read from wp_options via hc_get().
 */

/**
 * [hc_hero_carousel] — 5-slide carousel matching index.php.
 */
add_shortcode( 'hc_hero_carousel', function () {
    $slides = hc_get( 'hero_slides' );
    if ( ! is_array( $slides ) || ! $slides ) return '';

    ob_start(); ?>
    <div id="hcHero" class="carousel slide hc-hero-carousel">
        <ol class="carousel-indicators">
            <?php foreach ( $slides as $i => $s ) : ?>
                <li data-target="#hcHero" data-slide-to="<?php echo $i; ?>" class="<?php echo 0 === $i ? 'active' : ''; ?>"></li>
            <?php endforeach; ?>
        </ol>
        <div class="carousel-inner">
            <?php foreach ( $slides as $i => $s ) :
                $bg = ! empty( $s['image'] ) ? hc_image_url( $s['image'], 'full' ) : ''; ?>
                <div class="carousel-item hc-hero-item<?php echo 0 === $i ? ' active' : ''; ?>" style="<?php echo $bg ? 'background-image:url(' . esc_url( $bg ) . ');' : ''; ?>">
                    <div class="carousel-caption">
                        <h<?php echo 0 === $i ? '1' : '2'; ?>><?php echo wp_kses_post( nl2br( $s['title'] ) ); ?></h<?php echo 0 === $i ? '1' : '2'; ?>>
                        <?php if ( ! empty( $s['rating_source'] ) ) :
                            $icon_url = ! empty( $s['rating_icon'] ) ? hc_image_url( $s['rating_icon'] ) : ''; ?>
                            <div class="rating">
                                <span>
                                    <?php if ( $icon_url ) : ?>
                                        <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $s['rating_source'] ); ?>" class="img-fluid">
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
        <button class="carousel-control-prev" type="button">‹</button>
        <button class="carousel-control-next" type="button">›</button>
    </div>
    <style>
        .hc-hero-carousel { position:relative; overflow:hidden; }
        .hc-hero-carousel .carousel-inner { position:relative; }
        .hc-hero-carousel .hc-hero-item { min-height: 560px; background-size:cover; background-position:center; position:relative; display:none; }
        .hc-hero-carousel .hc-hero-item.active { display:block; }
        .hc-hero-carousel .hc-hero-item:before { content:""; position:absolute; inset:0; background:rgba(20,20,30,0.55); }
        .hc-hero-carousel .carousel-caption { position:absolute; left:0; right:0; bottom:auto; top:50%; transform:translateY(-50%); color:#fff; padding:0 20px; text-align:center; z-index:2; }
        .hc-hero-carousel .carousel-caption h1, .hc-hero-carousel .carousel-caption h2 { color:#fff; font-size:48px; font-weight:700; line-height:1.2; max-width:900px; margin:0 auto 20px; text-transform:uppercase; letter-spacing:1.5px; }
        .hc-hero-carousel .rating { display:inline-block; background:rgba(255,255,255,.12); padding:10px 20px; border-radius:2px; }
        .hc-hero-carousel .rating img { width:24px; height:24px; vertical-align:middle; margin-right:8px; }
        .hc-hero-carousel .carousel-control-prev,
        .hc-hero-carousel .carousel-control-next {
            position:absolute; top:50%; transform:translateY(-50%);
            width:50px; height:50px; background:rgba(0,0,0,.4);
            border:none; color:#fff; font-size:30px; cursor:pointer; z-index:3;
        }
        .hc-hero-carousel .carousel-control-prev { left:20px; }
        .hc-hero-carousel .carousel-control-next { right:20px; }
        .hc-hero-carousel .carousel-indicators {
            position:absolute; bottom:30px; left:0; right:0; z-index:3;
            display:flex; justify-content:center; gap:8px; padding:0; margin:0; list-style:none;
        }
        .hc-hero-carousel .carousel-indicators li {
            width:30px; height:3px; background:rgba(255,255,255,.4); cursor:pointer; transition:background .3s;
        }
        .hc-hero-carousel .carousel-indicators li.active { background:#fff; }
        @media (max-width:768px){.hc-hero-carousel .carousel-caption h1, .hc-hero-carousel .carousel-caption h2{font-size:28px;}}
    </style>
    <script>
    (function(){
        var root = document.getElementById('hcHero'); if (!root) return;
        var items = root.querySelectorAll('.hc-hero-item');
        var dots  = root.querySelectorAll('.carousel-indicators li');
        var idx = 0, timer;
        function show(i){
            items.forEach(function(el){ el.classList.remove('active'); });
            dots.forEach(function(el){ el.classList.remove('active'); });
            items[i].classList.add('active');
            if (dots[i]) dots[i].classList.add('active');
            idx = i;
        }
        function next(){ show((idx + 1) % items.length); }
        function prev(){ show((idx - 1 + items.length) % items.length); }
        root.querySelector('.carousel-control-next').addEventListener('click', next);
        root.querySelector('.carousel-control-prev').addEventListener('click', prev);
        dots.forEach(function(d, i){ d.addEventListener('click', function(){ show(i); resetTimer(); }); });
        function resetTimer(){ clearInterval(timer); timer = setInterval(next, 6000); }
        resetTimer();
    })();
    </script>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_facility_icons] — 6-icon "Why Choose Us" strip.
 */
add_shortcode( 'hc_facility_icons', function () {
    $rows = hc_get( 'facility_icons' );
    if ( ! is_array( $rows ) || ! $rows ) return '';

    ob_start(); ?>
    <div class="hc-facility-icons" style="display:grid;grid-template-columns:repeat(6,1fr);gap:24px;text-align:center;">
        <?php foreach ( $rows as $r ) :
            $icon_url = ! empty( $r['icon'] ) ? hc_image_url( $r['icon'] ) : ''; ?>
            <div class="hc-fac">
                <?php if ( $icon_url ) : ?>
                    <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $r['label'] ); ?>" style="height:60px;width:auto;margin:0 auto 10px;">
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
 * Uses static theme assets.
 */
add_shortcode( 'hc_category_cards', function () {
    $cards = array(
        array( 'label' => 'Rooms',        'url' => home_url( '/rooms/' ),            'src' => 'images/home/room.webp' ),
        array( 'label' => 'Restaurant',   'url' => home_url( '/restaurant/' ),       'src' => 'images/home/restaurent.webp' ),
        array( 'label' => 'Banquet Hall', 'url' => home_url( '/banquet-hall/' ),     'src' => 'images/home/banquet-hall.webp' ),
        array( 'label' => 'Board Room',   'url' => home_url( '/conference-room/' ),  'src' => 'images/home/conference-room.webp' ),
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
 * [hc_gallery_grid] — filterable gallery grid.
 */
add_shortcode( 'hc_gallery_grid', function ( $atts ) {
    $atts = shortcode_atts( array( 'filters' => 'yes', 'columns' => 3 ), $atts );
    $items = hc_get( 'gallery' );
    if ( ! is_array( $items ) || ! $items ) return '';

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
                $url = hc_image_url( $g['image'] ?? 0, 'large' );
                if ( ! $url ) continue; ?>
                <a class="hc-gallery-item" data-cat="<?php echo esc_attr( $g['category'] ?? '' ); ?>" href="<?php echo esc_url( $url ); ?>" target="_blank">
                    <img src="<?php echo esc_url( $url ); ?>" alt="" style="width:100%;height:260px;object-fit:cover;display:block;">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <style>
        .hc-gallery-filters { list-style:none; }
        .hc-gallery-filters .hc-filter { color:#777; text-transform:uppercase; font-size:13px; letter-spacing:1px; padding-bottom:4px; border-bottom:2px solid transparent; text-decoration:none; }
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
 * [hc_gallery_slider] — horizontal slider for the home gallery section.
 */
add_shortcode( 'hc_gallery_slider', function () {
    $items = hc_get( 'gallery' );
    if ( ! is_array( $items ) || ! $items ) return '';

    ob_start(); ?>
    <div class="hc-gallery-slider" style="display:flex;overflow-x:auto;gap:14px;scroll-snap-type:x mandatory;padding-bottom:10px;">
        <?php foreach ( $items as $g ) :
            $url = hc_image_url( $g['image'] ?? 0, 'large' );
            if ( ! $url ) continue; ?>
            <a href="<?php echo esc_url( $url ); ?>" target="_blank" style="flex:0 0 300px;scroll-snap-align:start;">
                <img src="<?php echo esc_url( $url ); ?>" alt="" style="width:300px;height:220px;object-fit:cover;display:block;">
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
    $rows = hc_get( 'testimonials' );
    if ( ! is_array( $rows ) || ! $rows ) return '';

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
 * [hc_awards] — slider/grid of certificates (stored as attachment IDs).
 */
add_shortcode( 'hc_awards', function () {
    $awards = hc_get( 'awards' );
    if ( ! is_array( $awards ) || ! $awards ) return '';

    ob_start(); ?>
    <div class="hc-awards-slider" style="display:flex;overflow-x:auto;gap:14px;scroll-snap-type:x mandatory;padding-bottom:10px;">
        <?php foreach ( $awards as $att_id ) :
            $url = hc_image_url( $att_id, 'large' );
            if ( ! $url ) continue; ?>
            <a href="<?php echo esc_url( $url ); ?>" target="_blank" style="flex:0 0 220px;scroll-snap-align:start;">
                <img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( hc_image_alt( $att_id, 'Award' ) ); ?>" style="width:220px;height:300px;object-fit:contain;background:#fff;padding:12px;border:1px solid #eee;display:block;">
            </a>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_contact_info] — phone/email/address block.
 */
add_shortcode( 'hc_contact_info', function ( $atts ) {
    $atts = shortcode_atts( array( 'style' => 'light' ), $atts );
    $phones  = hc_get( 'phones' );
    $emails  = hc_get( 'emails' );
    $address = hc_get( 'address' );

    ob_start(); ?>
    <div class="hc-contact-info hc-contact-<?php echo esc_attr( $atts['style'] ); ?>">
        <?php if ( is_array( $phones ) && $phones ) : ?>
            <div class="hc-ci-block"><h6>Phone</h6>
                <?php foreach ( $phones as $p ) : ?>
                    <p><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $p['value'] ) ); ?>"><?php echo esc_html( $p['value'] ); ?></a>
                        <?php if ( ! empty( $p['label'] ) ) : ?><small style="opacity:.6;"> — <?php echo esc_html( $p['label'] ); ?></small><?php endif; ?>
                    </p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ( is_array( $emails ) && $emails ) : ?>
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
    $url = hc_get( 'map_url' );
    if ( ! $url ) return '';
    return '<div class="hc-map"><iframe src="' . esc_url( $url ) . '" width="100%" height="450" style="border:0;display:block;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>';
} );

/**
 * [hc_restaurant_hours]
 */
add_shortcode( 'hc_restaurant_hours', function () {
    $rows    = hc_get( 'restaurant_hours' );
    $qr_id   = hc_get( 'restaurant_qr' );
    $reserve = hc_get( 'restaurant_reserve_url' );
    if ( ! is_array( $rows ) || ! $rows ) return '';

    $qr_url = $qr_id ? hc_image_url( $qr_id ) : '';

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
        <?php if ( $qr_url ) : ?>
            <h5 style="margin-top:30px;">Scan QR to View Menu</h5>
            <img src="<?php echo esc_url( $qr_url ); ?>" alt="Menu QR" style="max-width:180px;display:block;margin-top:10px;">
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
    $key = $atts['type'] === 'conference' ? 'conference_categories'
         : ( $atts['type'] === 'cuisine'  ? 'restaurant_cuisines'  : 'banquet_categories' );
    $rows = hc_get( $key );
    if ( ! is_array( $rows ) || ! $rows ) return '';

    ob_start(); ?>
    <div class="hc-cat-grid" style="display:grid;grid-template-columns:repeat(<?php echo intval( $atts['columns'] ); ?>,1fr);gap:20px;">
        <?php foreach ( $rows as $r ) :
            $url = ! empty( $r['image'] ) ? hc_image_url( $r['image'], 'large' ) : ''; ?>
            <div class="hc-cat-tile" style="position:relative;overflow:hidden;">
                <?php if ( $url ) : ?>
                    <img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $r['label'] ); ?>" style="width:100%;height:240px;object-fit:cover;display:block;">
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
 * [hc_counters] — about-us counters block.
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
 * [hc_footer_widgets] — full 4-column footer block with logo + icon-prefixed contact rows.
 * Mirrors component/footer.php from the original site.
 */
add_shortcode( 'hc_footer_widgets', function () {
    $phones  = hc_get( 'phones' );
    $emails  = hc_get( 'emails' );
    $address = hc_get( 'address' );
    $fb      = hc_get( 'facebook_url' );
    $ig      = hc_get( 'instagram_url' );
    $logo    = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/images/logo.png';

    ob_start(); ?>
    <div class="hc-footer-widgets">

        <div class="hc-fw-brand">
            <img src="<?php echo esc_url( $logo ); ?>" alt="Hotel Cosmopolitan" class="hc-fw-logo">
            <p class="hc-fw-tagline">A symphony of<br>luxury and comfort</p>
            <div class="hc-fw-social">
                <?php if ( $fb ) : ?>
                    <a href="<?php echo esc_url( $fb ); ?>" target="_blank" rel="noopener" aria-label="Facebook">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.04c-5.5 0-9.96 4.46-9.96 9.96 0 4.97 3.64 9.09 8.4 9.84v-6.96H7.9V12h2.54V9.85c0-2.51 1.49-3.89 3.78-3.89 1.09 0 2.24.19 2.24.19v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.45 2.88h-2.34v6.96A9.96 9.96 0 0 0 21.96 12c0-5.5-4.46-9.96-9.96-9.96z"/></svg>
                    </a>
                <?php endif; ?>
                <?php if ( $ig ) : ?>
                    <a href="<?php echo esc_url( $ig ); ?>" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c2.72 0 3.06.01 4.12.06 1.06.05 1.79.22 2.43.47.65.25 1.21.59 1.77 1.15.51.5.9 1.1 1.15 1.77.25.64.42 1.37.47 2.43.05 1.06.06 1.4.06 4.12s-.01 3.06-.06 4.12c-.05 1.06-.22 1.79-.47 2.43-.25.65-.59 1.21-1.15 1.77-.5.51-1.1.9-1.77 1.15-.64.25-1.37.42-2.43.47-1.06.05-1.4.06-4.12.06s-3.06-.01-4.12-.06c-1.06-.05-1.79-.22-2.43-.47a4.9 4.9 0 0 1-1.77-1.15 4.9 4.9 0 0 1-1.15-1.77c-.25-.64-.42-1.37-.47-2.43C2.01 15.06 2 14.72 2 12s.01-3.06.06-4.12c.05-1.06.22-1.79.47-2.43.25-.65.59-1.21 1.15-1.77.5-.51 1.1-.9 1.77-1.15.64-.25 1.37-.42 2.43-.47C8.94 2.01 9.28 2 12 2zm0 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm6.5-.25a1.25 1.25 0 1 0-2.5 0 1.25 1.25 0 0 0 2.5 0zM12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6z"/></svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="hc-fw-col">
            <h3>Rooms</h3>
            <ul>
                <li><a href="<?php echo esc_url( home_url( '/room/executive-room/' ) ); ?>">Executive</a></li>
                <li><a href="<?php echo esc_url( home_url( '/room/premium-room/' ) ); ?>">Premium</a></li>
                <li><a href="<?php echo esc_url( home_url( '/room/presidential-room/' ) ); ?>">Presidential</a></li>
                <li><a href="<?php echo esc_url( home_url( '/room/luxury-room/' ) ); ?>">Luxury</a></li>
                <li><a href="<?php echo esc_url( home_url( '/room/deluxe-room/' ) ); ?>">Deluxe</a></li>
            </ul>
        </div>

        <div class="hc-fw-col">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>">About Us</a></li>
                <li><a href="<?php echo esc_url( home_url( '/news-blogs/' ) ); ?>">News &amp; Blogs</a></li>
                <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">FAQ's</a></li>
                <li><a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>">Gallery</a></li>
                <li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Contact Us</a></li>
            </ul>
        </div>

        <div class="hc-fw-col hc-fw-contact">
            <h3>Contact Us</h3>
            <?php if ( is_array( $phones ) && $phones ) :
                $phone_lines = array();
                foreach ( $phones as $p ) {
                    $tel = preg_replace( '/[^0-9+]/', '', $p['value'] );
                    $phone_lines[] = '<a href="tel:' . esc_attr( $tel ) . '">' . esc_html( $p['value'] ) . '</a>';
                } ?>
                <div class="hc-fw-row">
                    <svg class="hc-fw-icon" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20 15.5c-1.25 0-2.45-.2-3.57-.57a1 1 0 0 0-1.02.24l-2.2 2.2a15.07 15.07 0 0 1-6.58-6.58l2.2-2.21a1 1 0 0 0 .25-1.02A11.36 11.36 0 0 1 8.5 4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1c0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.5a1 1 0 0 0-1-1z"/></svg>
                    <div><?php echo implode( ' | ', $phone_lines ); ?></div>
                </div>
            <?php endif; ?>

            <?php if ( is_array( $emails ) && $emails ) : foreach ( $emails as $e ) : ?>
                <div class="hc-fw-row">
                    <svg class="hc-fw-icon" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg>
                    <a href="mailto:<?php echo esc_attr( $e['value'] ); ?>"><?php echo esc_html( $e['value'] ); ?></a>
                </div>
            <?php endforeach; endif; ?>

            <?php if ( $address ) : ?>
                <div class="hc-fw-row">
                    <svg class="hc-fw-icon" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 0 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                    <span><?php echo esc_html( $address ); ?></span>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <div class="hc-footer-copyright">
        <div class="hc-copy-inner">
            <p>&copy; <?php echo date( 'Y' ); ?> All Rights Reserved to Hotel Cosmopolitan</p>
            <p>
                <a href="<?php echo esc_url( home_url( '/terms-condition/' ) ); ?>">Terms &amp; Conditions</a> |
                <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy Policy</a> |
                <a href="<?php echo esc_url( home_url( '/reservation-policy/' ) ); ?>">Booking Policy</a> |
                <a href="<?php echo esc_url( home_url( '/cancellation-policy/' ) ); ?>">Cancellation Policy</a>
            </p>
        </div>
    </div>

    <style>
        .hc-footer-widgets {
            display:grid; grid-template-columns:1.3fr 1fr 1fr 1.5fr; gap:50px;
            padding:70px 30px 50px; max-width:1300px; margin:0 auto;
            font-family: 'Poppins', sans-serif;
        }
        .hc-fw-brand .hc-fw-logo { max-width:180px; height:auto; display:block; margin-bottom:18px; filter: brightness(0) invert(1); }
        .hc-fw-tagline { font-style:italic; color:rgba(255,255,255,.75); font-size:16px; line-height:1.5; margin:0 0 18px; }
        .hc-fw-social { display:flex; gap:10px; }
        .hc-fw-social a {
            display:inline-flex; align-items:center; justify-content:center;
            width:36px; height:36px; background:rgba(255,255,255,.08); color:#fff;
            border:1px solid rgba(255,255,255,.15); transition:all .3s;
        }
        .hc-fw-social a:hover { background:#D81418; border-color:#D81418; }

        .hc-fw-col h3, .hc-fw-contact h3 {
            color:#fff; font-size:13px; letter-spacing:2px; text-transform:uppercase;
            margin:0 0 22px; padding-bottom:10px; border-bottom:2px solid #D81418; display:inline-block;
        }
        .hc-fw-col ul { list-style:none; padding:0; margin:0; }
        .hc-fw-col ul li { padding:5px 0; position:relative; padding-left:16px; }
        .hc-fw-col ul li:before { content:"›"; color:#D81418; position:absolute; left:0; font-weight:bold; }
        .hc-fw-col ul li a, .hc-fw-contact a, .hc-fw-contact span {
            color:rgba(255,255,255,.75); text-decoration:none; font-size:14px; line-height:1.6;
        }
        .hc-fw-col ul li a:hover, .hc-fw-contact a:hover { color:#D81418; }

        .hc-fw-row { display:flex; gap:10px; align-items:flex-start; margin:10px 0; }
        .hc-fw-icon { flex:0 0 18px; color:#D81418; margin-top:3px; }
        .hc-fw-row > div, .hc-fw-row > a, .hc-fw-row > span { font-size:14px; line-height:1.6; }

        .hc-footer-copyright { background:#0a0a12; padding:20px 30px; color:rgba(255,255,255,.6); }
        .hc-copy-inner {
            max-width:1300px; margin:0 auto; display:flex; justify-content:space-between;
            align-items:center; gap:20px; flex-wrap:wrap; font-size:13px;
        }
        .hc-copy-inner p { margin:0; }
        .hc-copy-inner a { color:rgba(255,255,255,.7); text-decoration:none; }
        .hc-copy-inner a:hover { color:#D81418; }

        @media (max-width:900px){
            .hc-footer-widgets { grid-template-columns:1fr 1fr; gap:30px; padding:50px 20px 30px; }
            .hc-copy-inner { flex-direction:column; text-align:center; }
        }
        @media (max-width:480px){
            .hc-footer-widgets { grid-template-columns:1fr; }
        }
    </style>
    <?php
    return ob_get_clean();
} );
