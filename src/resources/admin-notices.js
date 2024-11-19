(function ($, dispatch, document) {
    // Set up the package functions using the namespace provided by the script tag.
    const currentScript = typeof document.currentScript !== 'undefined' ? document.currentScript : document.scripts[document.scripts.length - 1];
    const namespace = currentScript.getAttribute('data-stellarwp-namespace');

    if (!namespace) {
        console.error('The stellarwp/admin-notices library failed to load because the namespace attribute is missing.');
        return;
    }

    window.stellarwp = window.stellarwp || {};
    window.stellarwp.adminNotices = window.stellarwp.adminNotices || {};
    window.stellarwp.adminNotices[namespace] = {
        /**
         * Dismisses a notice with the given ID.
         *
         * @since 1.1.0
         *
         * @param {string} noticeId
         */
        dismissNotice: function (noticeId) {
            const now = Math.floor(Date.now() / 1000);
            dispatch('core/preferences').set(`stellarwp/admin-notices/${namespace}`, noticeId, now);
        },
    };

    // Begin notice dismissal code
    const noticeIdAttribute = `stellarwp-${namespace}-notice-id`;
    const dataNoticeIdAttribute = `data-${noticeIdAttribute}`;
    const $notices = $(`[${dataNoticeIdAttribute}]`);

    // Mark standard notices as closed
    $notices.on('click', '.notice-dismiss', function () {
        const $this = $(this);
        const noticeId = $this.closest(`[${dataNoticeIdAttribute}]`).data(`stellarwp-${namespace}-notice-id`);

        window.stellarwp.adminNotices[namespace].dismissNotice(noticeId);
    });

    // Mark and close custom notice closes
    const closeAttribute = `stellarwp-${namespace}-close-notice`;
    const dataCloseAttribute = `data-${closeAttribute}`;
    const closeBehaviorAttribute = `stellarwp-${namespace}-close-notice-behavior`;

    $(`[${dataCloseAttribute}]`).on('click', function () {
        const $this = $(this);
        const noticeId = $this.data(closeAttribute);
        const $notice = $(`[${dataNoticeIdAttribute}="${noticeId}"]`);

        if (!$notice.length) {
            console.log(`Unable to find and close notice with ID: ${noticeId}`);
            return;
        }

        window.stellarwp.adminNotices[namespace].dismissNotice(noticeId);

        if ($this.data(closeBehaviorAttribute) === 'hide') {
            $notice.fadeOut();
        }
    });

    // Position custom notices
    const locationAttribute = `stellarwp-${namespace}-location`;
    const dataLocationAttribute = `data-${locationAttribute}`;

    $(`[${dataLocationAttribute}]`).each(function () {
        const $notice = $(this);
        const location = $notice.data(locationAttribute);

        if (location === 'below_header') {
            $notice.insertAfter('h1');
        } else if (location === 'above_header') {
            $notice.insertBefore('h1');
        } else if (location === 'inline') {
            $notice.insertBefore('#wpdoby-content > .wrap');
        }
    });
})(window.jQuery, window.wp.data.dispatch, document);
