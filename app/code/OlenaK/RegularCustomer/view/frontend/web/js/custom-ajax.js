define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/cookies'
], function ($, alert) {
    'use strict';

        return function (action, formData) {
            let myXHR = $.ajax({
                url: action,
                data: formData,
                processData: false,
                contentType: false,
                type: 'post',
                dataType: 'json',

                beforeSend: function () {
                    $('body').trigger('processStart');
                },

                success: function (response) {
                    let title = response.added ? $.mage.__('Your request posted') : $.mage.__('Your request not posted');

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
                }
            });
            return myXHR;
        };
});
