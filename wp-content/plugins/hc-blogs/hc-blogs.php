<?php
/**
 * Plugin Name: HC Blogs
 * Description: Custom post type "Blog" for Hotel Cosmopolitan, mirroring the news-blogs section of the original site.
 * Version: 1.0.0
 * Author: Wappnet Systems
 * Text Domain: hc-blogs
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function () {

    register_post_type( 'hc_blog', array(
        'labels' => array(
            'name'          => __( 'Blogs', 'hc-blogs' ),
            'singular_name' => __( 'Blog', 'hc-blogs' ),
            'menu_name'     => __( 'Blogs', 'hc-blogs' ),
            'add_new_item'  => __( 'Add New Blog', 'hc-blogs' ),
            'edit_item'     => __( 'Edit Blog', 'hc-blogs' ),
            'view_item'     => __( 'View Blog', 'hc-blogs' ),
            'search_items'  => __( 'Search Blogs', 'hc-blogs' ),
        ),
        'public'        => true,
        'show_in_rest'  => true,
        'has_archive'   => 'news-blogs',
        'rewrite'       => array( 'slug' => 'blog', 'with_front' => false ),
        'menu_icon'     => 'dashicons-welcome-write-blog',
        'menu_position' => 21,
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'comments' ),
    ) );

    register_taxonomy( 'hc_blog_category', 'hc_blog', array(
        'labels' => array(
            'name'          => __( 'Blog Categories', 'hc-blogs' ),
            'singular_name' => __( 'Category', 'hc-blogs' ),
        ),
        'public'       => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'blog-category' ),
    ) );
} );

register_activation_hook( __FILE__, 'flush_rewrite_rules' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/**
 * Expose to Divi builder.
 */
add_filter( 'et_builder_post_types', function ( $types ) {
    $types[] = 'hc_blog';
    return $types;
} );
add_filter( 'et_fb_post_types', function ( $types ) {
    $types[] = 'hc_blog';
    return $types;
} );

/**
 * [hc_blogs_grid limit="6"] — used on the news-blogs landing page.
 */
add_shortcode( 'hc_blogs_grid', function ( $atts ) {
    $atts = shortcode_atts( array( 'limit' => 6, 'columns' => 3 ), $atts );

    $q = new WP_Query( array(
        'post_type'      => 'hc_blog',
        'posts_per_page' => intval( $atts['limit'] ),
    ) );
    if ( ! $q->have_posts() ) return '<p>No blogs yet.</p>';

    ob_start(); ?>
    <div class="hc-blogs-grid" style="display:grid;grid-template-columns:repeat(<?php echo esc_attr( $atts['columns'] ); ?>,1fr);gap:30px;">
        <?php while ( $q->have_posts() ) : $q->the_post(); ?>
            <article class="hc-blog-card" style="background:#fff;border:1px solid #eee;">
                <?php if ( has_post_thumbnail() ) : ?>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'hc-blog-card' ); ?></a>
                <?php endif; ?>
                <div style="padding:18px 20px;">
                    <h3 style="font-size:18px;margin:0 0 10px;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p style="font-size:14px;color:#999;margin:0 0 10px;"><?php echo esc_html( get_the_date() ); ?></p>
                    <p style="font-size:14px;"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
                    <p style="margin-top:12px;"><a href="<?php the_permalink(); ?>">Read more &rarr;</a></p>
                </div>
            </article>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
} );
