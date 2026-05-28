<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Room shortcodes — read from native post meta (no ACF).
 */

/**
 * [hc_rooms_grid columns="2"] — grid of all rooms, used on the /rooms archive and home.
 */
add_shortcode( 'hc_rooms_grid', function ( $atts ) {
    $atts = shortcode_atts( array(
        'columns' => '2',
        'limit'   => -1,
    ), $atts );

    $q = new WP_Query( array(
        'post_type'      => 'room',
        'posts_per_page' => intval( $atts['limit'] ),
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ) );

    if ( ! $q->have_posts() ) return '<p>No rooms yet.</p>';

    ob_start(); ?>
    <div class="hc-rooms-grid" style="display:grid;grid-template-columns:repeat(<?php echo esc_attr( $atts['columns'] ); ?>,1fr);gap:30px;">
        <?php while ( $q->have_posts() ) : $q->the_post();
            $id        = get_the_ID();
            $amenities = hc_get( 'amenities',       $id );
            $bed       = hc_get( 'bed_type',        $id );
            $size      = hc_get( 'room_size',       $id );
            $options   = hc_get( 'booking_options', $id );
            ?>
            <article class="hc-room-card" id="room-<?php echo esc_attr( get_post_field( 'post_name' ) ); ?>">
                <div class="hc-room-card__image">
                    <a href="<?php the_permalink(); ?>">
                        <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'hc-room-card' ); ?>
                    </a>
                </div>
                <div class="hc-room-card__body">
                    <h3 class="hc-room-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <?php if ( $bed || $size ) : ?>
                        <p style="font-size:14px;margin:0 0 10px;color:#999;">
                            <?php echo esc_html( trim( $bed . ( $bed && $size ? ' · ' : '' ) . $size ) ); ?>
                        </p>
                    <?php endif; ?>
                    <?php if ( is_array( $amenities ) && $amenities ) : ?>
                        <ul class="hc-room-amenities">
                            <?php foreach ( array_slice( $amenities, 0, 8 ) as $a ) : ?>
                                <li><?php echo esc_html( $a['label'] ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <?php if ( is_array( $options ) && $options ) : ?>
                        <div class="hc-room-options">
                            <?php foreach ( $options as $opt ) : ?>
                                <p>
                                    <span><?php echo esc_html( $opt['label'] ); ?></span>
                                    <a class="hc-btn-primary" href="<?php echo esc_url( $opt['url'] ); ?>" target="_blank" rel="noopener noreferrer">Book Now</a>
                                </p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <p style="margin-top:14px;"><a href="<?php the_permalink(); ?>">Know More &rarr;</a></p>
                </div>
            </article>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_room_type_select] — quick-jump dropdown used on rooms archive.
 */
add_shortcode( 'hc_room_type_select', function () {
    $q = new WP_Query( array(
        'post_type'      => 'room',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'fields'         => 'ids',
    ) );
    if ( ! $q->have_posts() ) return '';

    ob_start(); ?>
    <select id="hc-room-type-select" class="hc-room-type-select" style="padding:10px 14px;border:1px solid #ddd;">
        <option value="" disabled selected><?php echo esc_html( $q->found_posts ); ?> Room Types</option>
        <?php foreach ( $q->posts as $id ) : ?>
            <option value="<?php echo esc_attr( get_post_field( 'post_name', $id ) ); ?>"><?php echo esc_html( get_the_title( $id ) ); ?></option>
        <?php endforeach; ?>
    </select>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_room_gallery] — single-room gallery carousel.
 */
add_shortcode( 'hc_room_gallery', function () {
    if ( ! is_singular( 'room' ) ) return '';
    $gallery_ids = hc_get( 'gallery', get_the_ID() );
    if ( ! is_array( $gallery_ids ) || ! $gallery_ids ) {
        if ( has_post_thumbnail() ) return get_the_post_thumbnail( null, 'hc-room-banner' );
        return '';
    }

    $id = 'hcRoomCarousel-' . get_the_ID();
    ob_start(); ?>
    <div id="<?php echo esc_attr( $id ); ?>" class="carousel slide hc-room-gallery" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php foreach ( $gallery_ids as $i => $att_id ) :
                $url = hc_image_url( $att_id, 'large' );
                if ( ! $url ) continue; ?>
                <div class="carousel-item<?php echo 0 === $i ? ' active' : ''; ?>">
                    <img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( hc_image_alt( $att_id, get_the_title() ) ); ?>" class="d-block w-100">
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo esc_attr( $id ); ?>" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#<?php echo esc_attr( $id ); ?>" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span>
        </button>
    </div>
    <style>
        .hc-room-gallery { position:relative; }
        .hc-room-gallery .carousel-item img { width:100%; height:auto; display:block; }
        .hc-room-gallery .carousel-control-prev,
        .hc-room-gallery .carousel-control-next { width:50px; background:rgba(0,0,0,.4); top:50%; bottom:auto; transform:translateY(-50%); height:50px; }
        .hc-room-gallery .carousel-control-prev-icon,
        .hc-room-gallery .carousel-control-next-icon {
            width:20px;height:20px;background-size:contain;background-repeat:no-repeat;background-position:center;display:inline-block;filter:invert(1);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath d='M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z'/%3e%3c/svg%3e");
        }
        .hc-room-gallery .carousel-control-next-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath d='M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }
    </style>
    <script>
    (function(){
        var el = document.getElementById('<?php echo esc_js( $id ); ?>');
        if (!el) return;
        var items = el.querySelectorAll('.carousel-item');
        var idx = 0;
        function show(i){
            items.forEach(function(it){ it.classList.remove('active'); });
            items[i].classList.add('active');
        }
        el.querySelector('.carousel-control-prev').addEventListener('click', function(){
            idx = (idx - 1 + items.length) % items.length; show(idx);
        });
        el.querySelector('.carousel-control-next').addEventListener('click', function(){
            idx = (idx + 1) % items.length; show(idx);
        });
        setInterval(function(){ idx = (idx + 1) % items.length; show(idx); }, 5000);
    })();
    </script>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_room_amenities] — two-column amenities list, used inside single-room layout.
 */
add_shortcode( 'hc_room_amenities', function () {
    if ( ! is_singular( 'room' ) ) return '';
    $amenities = hc_get( 'amenities', get_the_ID() );
    if ( ! is_array( $amenities ) || ! $amenities ) return '';

    ob_start(); ?>
    <div class="hc-room-additional">
        <h3>AMENITIES AND SERVICES</h3>
        <ul class="hc-room-amenities">
            <?php foreach ( $amenities as $a ) : ?>
                <li>
                    <?php if ( ! empty( $a['icon'] ) ) : ?>
                        <i class="<?php echo esc_attr( $a['icon'] ); ?>" aria-hidden="true"></i>
                    <?php endif; ?>
                    <?php echo esc_html( $a['label'] ); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
} );

/**
 * [hc_room_booking] — booking-options block for single-room page.
 */
add_shortcode( 'hc_room_booking', function () {
    if ( ! is_singular( 'room' ) ) return '';
    $options = hc_get( 'booking_options', get_the_ID() );
    if ( ! is_array( $options ) || ! $options ) return '';

    ob_start(); ?>
    <div class="hc-room-options" style="background:#f8f8f8;padding:24px;">
        <h4 style="margin-top:0;">Book this room</h4>
        <?php foreach ( $options as $opt ) : ?>
            <p>
                <span><?php echo esc_html( $opt['label'] ); ?></span>
                <a class="hc-btn-primary" href="<?php echo esc_url( $opt['url'] ); ?>" target="_blank" rel="noopener noreferrer">Book Now</a>
            </p>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
} );
