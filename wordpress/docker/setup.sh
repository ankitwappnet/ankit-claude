#!/bin/sh
# Hotel Cosmopolitan — one-shot WordPress installer.
# Runs inside the `installer` service of docker-compose.yml.
# Idempotent: safe to re-run.

set -e

cd /var/www/html

echo "==> Waiting for WordPress files to be in place..."
for i in $(seq 1 30); do
    [ -f wp-config.php ] && break
    sleep 2
done

# ----- 1. Install WordPress core if not installed -----
if ! wp core is-installed 2>/dev/null; then
    echo "==> Installing WordPress core..."
    wp core install \
        --url="http://localhost:8080" \
        --title="Hotel Cosmopolitan" \
        --admin_user="admin" \
        --admin_password="admin" \
        --admin_email="admin@hotelcosmopolitan.test" \
        --skip-email
else
    echo "==> WordPress core already installed — skipping."
fi

# ----- 2. Install Divi parent theme from /uploads/Divi.zip -----
if [ -f /uploads/Divi.zip ]; then
    if ! wp theme is-installed Divi 2>/dev/null; then
        echo "==> Installing Divi theme from /uploads/Divi.zip ..."
        wp theme install /uploads/Divi.zip
    else
        echo "==> Divi already installed."
    fi
else
    echo "!!  /uploads/Divi.zip not found — child theme will be inert until Divi is installed."
fi

# ----- 3. Activate the child theme (Divi must be present as parent) -----
if wp theme is-installed hotelcosmopolitan-child 2>/dev/null; then
    echo "==> Activating Hotel Cosmopolitan child theme..."
    wp theme activate hotelcosmopolitan-child
fi

# ----- 4. Install ACF Pro from /uploads/acf-pro.zip (or fall back to free ACF) -----
if [ -f /uploads/acf-pro.zip ]; then
    if ! wp plugin is-installed advanced-custom-fields-pro 2>/dev/null; then
        echo "==> Installing ACF Pro from /uploads/acf-pro.zip ..."
        wp plugin install /uploads/acf-pro.zip
    fi
    wp plugin activate advanced-custom-fields-pro || true
else
    echo "==> No ACF Pro zip found — installing free ACF from the repository."
    wp plugin install advanced-custom-fields --activate
    echo "!!  WARNING: free ACF lacks Repeater + Gallery fields — Site Content seeding will be limited."
fi

# ----- 5. Activate the 3 custom plugins -----
echo "==> Activating custom plugins..."
wp plugin activate hc-rooms hc-blogs hc-inquiries

# ----- 6. Trigger the seeders (they run on admin_init; we'll poke admin once) -----
# WP-CLI doesn't fire admin_init by default, so we use eval-file to do it explicitly.
echo "==> Running seeders (rooms / site content / pages / menu)..."
wp eval '
    do_action( "admin_init" );
    flush_rewrite_rules();
'

# ----- 7. Permalinks (should already be set by page seeder, but double-check) -----
wp rewrite structure "/%postname%/" --hard

echo ""
echo "============================================================"
echo "  Hotel Cosmopolitan WordPress install complete."
echo ""
echo "  Front-end:  http://localhost:8080"
echo "  WP-Admin:   http://localhost:8080/wp-admin"
echo "      user:   admin"
echo "      pass:   admin"
echo "  phpMyAdmin: http://localhost:8081 (hc_wp / hc_wp)"
echo "============================================================"
