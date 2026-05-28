(function ($) {
    'use strict';

    /**
     * Inquiry / room booking form — submits via admin-ajax.
     * Markup is rendered by the hc-inquiries plugin via the [hc_inquiry_form] shortcode.
     */
    $(document).on('submit', '.hc-form', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $status = $form.find('.hc-form-status');
        var $submit = $form.find('button[type="submit"], input[type="submit"]');

        $status.removeClass('is-success is-error').text('').hide();
        $submit.prop('disabled', true).data('original', $submit.text()).text('Sending...');

        var payload = $form.serializeArray();
        payload.push({ name: 'action',  value: 'hc_submit_inquiry' });
        payload.push({ name: '_nonce',  value: hcSettings.nonce });

        $.post(hcSettings.ajaxUrl, payload)
            .done(function (res) {
                if (res && res.success) {
                    $status.addClass('is-success').text(res.data.message || 'Thank you — we will be in touch shortly.').show();
                    $form[0].reset();
                } else {
                    $status.addClass('is-error').text((res && res.data && res.data.message) || 'Submission failed. Please try again.').show();
                }
            })
            .fail(function () {
                $status.addClass('is-error').text('Network error. Please try again.').show();
            })
            .always(function () {
                $submit.prop('disabled', false).text($submit.data('original') || 'Submit');
            });
    });

    /**
     * Room-type quick-jump dropdown (rooms archive page) — mirrors original rooms.php behaviour.
     */
    $(document).on('change', '#hc-room-type-select', function () {
        var slug = this.value;
        if (!slug) return;
        var $target = $('#room-' + slug);
        if ($target.length) {
            $('html, body').animate({ scrollTop: $target.offset().top - 100 }, 500);
        }
    });

})(jQuery);
