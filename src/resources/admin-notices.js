jQuery(document).ready(function ($) {
    const {dispatch} = wp.data;
    const dismissButtons = $('.notice-dismiss');

    dismissButtons.on('click', function (event) {
        const $this = $(this);
        const container = $this.closest('.notice');
        const noticeId = container.data('notice-id');

        const now = Math.floor(Date.now() / 1000);
        dispatch('core/preferences').set('stellarwp/admin-notices', noticeId, now);
    });
});
