define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/cookies'
], function ($, OlenaK_RegularCustomer_customAjax) {
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
            let action = this.options.action;
            console.log('form-js');
            OlenaK_RegularCustomer_customAjax();
        }
    });

    return $.OlenaK.regularCustomer_form;
});
