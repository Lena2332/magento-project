define([
    'jquery',
    'OlenaK_RegularCustomer_customAjax',
    'Magento_Customer/js/customer-data',
    'uiComponent',
    'ko',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/cookies'
], function ($, asyncFormSubmit, customerData, Component, ko) {
    'use strict';

    return Component.extend({
        defaults: {
            action: '',
            customerName: '',
            customerEmail: '',
            isLoggedIn: !!customerData.get('regular-customer')().isLoggedIn,
            isModal:0,
            productId:0,
            productIds:[],
            template: 'OlenaK_RegularCustomer/form'
        },

        initialize: function () {
            this._super();

            console.log(this);
        },

        sendRequest: function () {
            console.log('form works');
        }
    });

    //------
    $.widget('OlenaK.regularCustomer_form', {
        options: {
            action: '',
            isModal: 0,
            actionCheck: ''
        },

        _create: function () {
            $(this.element).on('submit.olenak_personal_disc_form', this.sendRequest.bind(this));

            if (this.options.isModal) {
                $(this.element).modal({
                    buttons: []
                });

                $(document).on('olenak_regular_customer_form_open', this.openModal.bind(this));
            }

            this.updateFormState(customerData.get('regular-customer')());
            customerData.get('regular-customer').subscribe(this.updateFormState.bind(this));
        },

        /**
         * Pre-fill form fields with data, hide fields if needed.
         */
        updateFormState: function (personalInfo) {
            //Set name and email if customer send request or logged in
            let emailField = $(this.element).find('input[name = "email"]');

            if (personalInfo.email !== undefined) {
                emailField.val(personalInfo.email);
            }

            let nameField =  $(this.element).find('input[name = "name"]');

            if (personalInfo.name !== undefined) {
                nameField.val(personalInfo.name);
            }

            //Hide fields if customer is logged
            if (personalInfo.isLoggedIn) {
                emailField.attr('type', 'hidden');
                nameField.attr('type', 'hidden').parents('fieldset').hide();
            }

            //Hide button if product has been requested
            let productId =  Number($(this.element).find('input[name = "product_id"]').val());

            if ($.inArray(productId, personalInfo.productIds) !== -1) {
                this.alreadyRequestedAction();
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
                .always(this.ajaxComplete.bind(this));
        },

        alreadyRequestedAction: function () {
            //hide button and form, show message
            $(this.element).hide();
            $(document).trigger('olenak_regular_customer_hide_button');

            let message = $.mage.__('Already requested!');

            $('.customer_request_block').after('<div class=\'message-notice notice message\'>' + message + '</div>');
        },

        ajaxComplete: function () {
            if (this.options.isModal) {
                $(this.element).modal('closeModal');
            }
        }
    });

    //return $.OlenaK.regularCustomer_form;
});
