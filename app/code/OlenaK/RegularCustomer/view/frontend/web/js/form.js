define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/cookies'
], function ($, alert) {
    'use strict';

    $.widget('OlenaK.regularCustomer_form', {
        options: {
            action: '',
            isModal: 0
        },

        _create: function () {
            $(this.element).on('submit.olenak_personal_disc_form', this.sendRequest.bind(this));
            if (this.options.isModal) {
                $(this.element).modal({
                    buttons: []
                });

                $(document).on('olenak_regular_customer_form_open', this.openModal.bind(this));
            }
        },

        openModal: function () {
            $(this.element).modal('openModal');
        },

        sendRequest: function () {
            if (!this.validate()) {
                return;
            }

            this.ajaxSubmit();
        },

        validate: function () {
            return $(this.element).validation().valid();
        },

        ajaxSubmit: function () {
            let formData = new FormData($(this.element).get(0));
            formData.append('form_key', $.mage.cookies.get('form_key'));
            formData.append('isAjax', 1);

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
            console.log('submitted');
        }

    });

    return $.OlenaK.regularCustomer_form;

});
