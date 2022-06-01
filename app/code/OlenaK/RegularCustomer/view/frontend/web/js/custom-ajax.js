define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/cookies'
], function ($, alert) {
    'use strict';

        return function (action, formData) {
                $.ajax({
                    url: this.options.action,
                    data: formData,
                    processData: false,
                    contentType: false,
                    type: 'post',
                    dataType: 'json',
                    context: this,

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
                    },

                    complete: function () {
                        if (this.options.isModal) {
                            $(this.element).modal('closeModal');
                        }
                        $('body').trigger('processStop');
                    }
                });
        };
});
