define([
    'jquery',
    'OlenaK_RegularCustomer_ajaxFormSubmit',
    'Magento_Customer/js/customer-data',
    'uiComponent',
    'ko',
    'OlenaK_RegularCustomer_formSubmitRestriction',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/cookies'
], function ($, asyncFormSubmit, customerData, Component, ko, formRestriction) {
    'use strict';

    return Component.extend({
        defaults: {
            action: '',
            allowForGuests: false,
            isModal: 0,
            productId: 0,
            template: 'OlenaK_RegularCustomer/form'
        },

        customerName: '',
        customerEmail: '',
        isLoggedIn: !!customerData.get('regular-customer')().isLoggedIn,
        productIds: [],

        initialize: function () {
            this._super();

            this.updateFormState(customerData.get('regular-customer')());
            customerData.get('regular-customer').subscribe(this.updateFormState.bind(this));
        },

        initObservable: function () {
            this._super();

            this.observe(['customerName', 'customerEmail', 'isLoggedIn', 'productIds']);

            this.formSubmitDeniedMessage = ko.computed(
                function () {
                    if (this.productIds().indexOf(this.productId) !== -1) {
                        return $.mage.__('Already requested!');
                    }

                    if (!this.allowForGuests && !this.isLoggedIn()) {
                        return $.mage.__('Please, log in to send a request!');
                    }

                    return '';
                }.bind(this)
            );

            formRestriction.formSubmitDeniedMessage(this.formSubmitDeniedMessage());
            this.formSubmitDeniedMessage.subscribe((newValue) => {
                formRestriction.formSubmitDeniedMessage(newValue);
            });

            return this;
        },

        /**
         * Update storage to indicate that new restrictions are in action
         */
        updateFormSubmitRestrictions: function () {
            formRestriction.formSubmitDeniedMessage(this.formSubmitDeniedMessage());
        },


        /**
         * Pre-fill form fields with data, hide fields if needed.
         */
        updateFormState: function (personalInfo) {
            //Set name and email if customer send request or logged in
            if (personalInfo.hasOwnProperty('email')) {
                this.customerEmail(personalInfo.email);
            }

            if (personalInfo.hasOwnProperty('name')) {
                this.customerName(personalInfo.name);
            }

            if (personalInfo.hasOwnProperty('productIds')) {
                this.productIds(personalInfo.productIds);
            }

            this.isLoggedIn(!!personalInfo.isLoggedIn);
        },

        initModal: function (element) {
            this.$form = $(element);
            if (this.isModal) {
                this.$modal = this.$form.modal({
                    buttons: []
                });
                $(document).on('olenak_regular_customer_form_open', this.openModal.bind(this));
            }
        },

        openModal: function () {
            this.$modal.modal('openModal');
        },

        sendRequest: function () {
            if (!this.validate()) {
                return;
            }
            this.ajaxSubmit();
        },

        validate: function () {
            return this.$form.validation().valid();
        },

        ajaxSubmit: function () {
            let payload = {
                name: this.customerName(),
                email: this.customerEmail(),
                'product_id': this.productId,
                'form_key': $.mage.cookies.get('form_key'),
                isAjax: 1
            };

            asyncFormSubmit(this.action, payload)
                .always(this.ajaxComplete.bind(this));
        },

        ajaxComplete: function () {
            if (this.isModal) {
                this.$modal.modal('closeModal');
            }
        }
    });
});
