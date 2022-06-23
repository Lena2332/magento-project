define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function ($, alert) {
    'use strict';

    return function (action, formData) {
        return $.ajax({
            url: action,
            data: formData,
            type: 'post',
            dataType: 'json',

            beforeSend: function () {
                $('body').trigger('processStart');
            },

            success: function (response) {
                let title = $.mage.__('Your request posted');

                alert({
                    title: title,
                    content: response.message
                });
            },

            error: function () {
                alert({
                    title: $.mage.__('Error'),
                    content: $.mage.__('Your request can\'t be sent. Please, contact us if you see this message.')
                });
            },

            complete: function () {
                $('body').trigger('processStop');
            }
        });
    };
});
