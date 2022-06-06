define([
    'jquery',
    'OlenaK_RegularCustomer_customAjax',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/cookies'
], function ($, asyncFormSubmit) {
    'use strict';

    $.widget('OlenaK.regularCustomer_form', {
        options: {
            action: '',
            isModal: 0,
            actionCheck: ''
        },

        _create: function () {
            //Check if it product was used in requests
            this.ajaxCheck();

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
            asyncFormSubmit(this.options.action, formData)
                .done(this.alreadyRequestedAction.bind(this))
                .always(this.ajaxComplete.bind(this));
        },

        alreadyRequestedAction: function () {
            //hide button and form, show message
            console.log('hide this func');
            $(this.element).hide();
            $(document).trigger('olenak_regular_customer_hide_button');

            let message = $.mage.__('Already requested!');

            $('.customer_request_block').after('<div class=\'message-notice notice message\'>' + message + '</div>');
        },

        ajaxComplete: function () {
            if (this.options.isModal) {
                $(this.element).modal('closeModal');
            }
        },

        ajaxCheck: function () {
            let productId = $(this.element).find('input[name = "product_id"]').val();

            $.ajax({
                url: this.options.actionCheck,
                data: {
                    product_id: productId,
                    form_key: $.mage.cookies.get('form_key'),
                    isAjax: 1
                },
                type: 'post',
                dataType: 'json',
                context: this,

                success: function (response) {
                    if (response.isUsed) {
                        console.log('hide this');
                        this.alreadyRequestedAction();
                    }
                },

                error: function () {
                    console.log($.mage.__('We can\'t check if it has beent requested.'));
                }
            });
        }

    });

    return $.OlenaK.regularCustomer_form;

});
