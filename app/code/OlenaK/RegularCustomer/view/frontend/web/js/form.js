define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'OlenaK_RegularCustomer_customAjax',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/cookies'
], function ($, alert, asyncFormSubmit) {
    'use strict';

    $.widget('OlenaK.regularCustomer_form', {
        options: {
            action: '',
            isModal: 0,
            actionCheck: ''
        },

        _create: function () {
            $(this.element).on('submit.olenak_personal_disc_form', this.sendRequest.bind(this));

            this.ajaxCheck();

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
            console.log('start');
            this.ajaxSubmit();
        },

        validate: function () {
            return $(this.element).validation().valid();
        },

        ajaxSubmit: function () {
            let formData = new FormData($(this.element).get(0));

            formData.append('form_key', $.mage.cookies.get('form_key'));
            formData.append('isAjax', 1);
            let action = this.options.action;

            asyncFormSubmit(action, formData);
            $(document).on('ajaxComplete', this.ajaxComplete().bind(this));
        },

        ajaxComplete: function () {
            if (this.options.isModal) {
                $(this.element).modal('closeModal');
            }
            $('body').trigger('processStop');
            this.alreadyRequestedAction();
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
                        this.alreadyRequestedAction();
                    }
                },

                error: function () {
                    alert({
                        title: $.mage.__('Error'),
                        content: $.mage.__('We can\'t check if it has beent requested.')
                    });
                }
            });
        },

        alreadyRequestedAction: function () {
            //hide button and form, show message
            $(this.element).hide();
            $(document).trigger('olenak_regular_customer_hide_button');
            let message = $.mage.__('Already requested!');

            $('.customer_request_block').after('<div class=\'message-notice notice message\'>' + message + '</div>');
        }
    });

    return $.OlenaK.regularCustomer_form;
});
