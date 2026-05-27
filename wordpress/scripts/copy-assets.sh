#!/usr/bin/env bash
# Copy original static-site images and fonts into the Divi child theme assets folder.
# Run this once locally before zipping the theme for upload to your WP hosting.
#
# Usage:
#   bash wordpress/scripts/copy-assets.sh
#
# After this completes, zip up:
#   wordpress/wp-content/themes/hotelcosmopolitan-child/
# and upload via WP-Admin > Appearance > Themes > Add New > Upload Theme.

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
SRC_IMG="$ROOT/public_html/images"
SRC_FONTS="$ROOT/public_html/fonts"
DEST="$ROOT/wordpress/wp-content/themes/hotelcosmopolitan-child/assets"

if [ ! -d "$SRC_IMG" ]; then
    echo "ERROR: $SRC_IMG not found. Run this script from a checkout that includes public_html/." >&2
    exit 1
fi

echo "Copying images from $SRC_IMG -> $DEST/images/ ..."
mkdir -p "$DEST/images"
cp -R "$SRC_IMG"/* "$DEST/images/"

if [ -d "$SRC_FONTS" ]; then
    echo "Copying fonts from $SRC_FONTS -> $DEST/fonts/ ..."
    mkdir -p "$DEST/fonts"
    cp -R "$SRC_FONTS"/* "$DEST/fonts/"
fi

IMG_COUNT=$(find "$DEST/images" -type f | wc -l | tr -d ' ')
SIZE=$(du -sh "$DEST" | awk '{print $1}')
echo ""
echo "Done. $IMG_COUNT image files copied. Total assets size: $SIZE"
echo "Next: zip the theme folder and upload it via WP-Admin."
