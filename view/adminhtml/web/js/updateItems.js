require(['jquery', 'jquery/ui'], function ($) {
    jQuery(document).ready(function () {
        $(document).on('change', 'select#group_id', function () {
            order.itemsUpdate();
        });
    });
});