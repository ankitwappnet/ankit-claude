<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ACF Pro field group registered in PHP (no JSON sync needed).
 * Requires ACF Pro for Repeater + Gallery field types.
 */
add_action( 'acf/init', function () {

    if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

    acf_add_local_field_group( array(
        'key'    => 'group_hc_room_details',
        'title'  => 'Room Details',
        'fields' => array(

            array(
                'key'   => 'field_hc_room_short_desc',
                'label' => 'Short Description',
                'name'  => 'short_description',
                'type'  => 'textarea',
                'rows'  => 3,
                'instructions' => 'Shown on the rooms archive card and meta-description fallback.',
            ),

            array(
                'key'     => 'field_hc_room_bed',
                'label'   => 'Bed Type',
                'name'    => 'bed_type',
                'type'    => 'text',
                'placeholder' => 'King bed / Twin beds',
            ),
            array(
                'key'     => 'field_hc_room_size',
                'label'   => 'Room Size',
                'name'    => 'room_size',
                'type'    => 'text',
                'placeholder' => '285 sq ft',
            ),
            array(
                'key'     => 'field_hc_room_price_from',
                'label'   => 'Price From (INR)',
                'name'    => 'price_from',
                'type'    => 'number',
                'min'     => 0,
            ),

            array(
                'key'        => 'field_hc_room_amenities',
                'label'      => 'Amenities',
                'name'       => 'amenities',
                'type'       => 'repeater',
                'layout'     => 'table',
                'button_label' => 'Add Amenity',
                'sub_fields' => array(
                    array(
                        'key'   => 'field_hc_room_amenity_icon',
                        'label' => 'Icon Class',
                        'name'  => 'icon',
                        'type'  => 'text',
                        'placeholder' => 'fa-light fa-bed-front',
                        'instructions' => 'FontAwesome class, e.g. "fa-light fa-wifi". Optional.',
                    ),
                    array(
                        'key'   => 'field_hc_room_amenity_label',
                        'label' => 'Label',
                        'name'  => 'label',
                        'type'  => 'text',
                        'required' => 1,
                    ),
                ),
            ),

            array(
                'key'     => 'field_hc_room_gallery',
                'label'   => 'Gallery',
                'name'    => 'gallery',
                'type'    => 'gallery',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'instructions'  => 'Images shown in the room carousel on single-room pages.',
            ),

            array(
                'key'   => 'field_hc_room_booking_options',
                'label' => 'Booking Options',
                'name'  => 'booking_options',
                'type'  => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Option',
                'sub_fields' => array(
                    array(
                        'key'   => 'field_hc_room_booking_label',
                        'label' => 'Option Label',
                        'name'  => 'label',
                        'type'  => 'text',
                        'placeholder' => 'Room Only / Room + Breakfast',
                        'required' => 1,
                    ),
                    array(
                        'key'   => 'field_hc_room_booking_url',
                        'label' => 'Booking URL',
                        'name'  => 'url',
                        'type'  => 'url',
                        'default_value' => 'https://www.swiftbook.io/inst/#home?propertyId=2166',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'room',
                ),
            ),
        ),
        'menu_order' => 0,
        'position'   => 'normal',
        'style'      => 'default',
    ) );
} );
