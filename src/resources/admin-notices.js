window.stellarwp = window.stellarwp || {};
window.stellarwp.adminNotices = {
    /**
     * Dismisses a notice with the given ID.
     *
     * @since 1.1.0
     *
     * @param {string} noticeId
     */
    dismissNotice: function (noticeId) {
        const now = Math.floor(Date.now() / 1000);
        wp.data.dispatch('core/preferences').set('stellarwp/admin-notices', noticeId, now);
    },
};

/**
 * Handles the dismissal of admin notices.
 *
 * @since 1.1.0 fixes potential event conflicts with notices not produced by this library
 * @since 1.0.0
 */
jQuery(document).ready(function ($) {
    const $notices = $('[data-stellarwp-notice-id]');

    $notices.on('click', '.notice-dismiss', function (event) {
        const noticeId = $(this).closest('[data-stellarwp-notice-id]').data('stellarwp-notice-id');

        window.stellarwp.adminNotices.dismissNotice(noticeId);
    });
});
