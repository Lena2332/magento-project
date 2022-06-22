define([
    'jquery',
    'ko',
    'Magento_Customer/js/customer-data',
    'uiComponent',
    'OlenaK_RegularCustomer_formSubmitRestriction',
    'Magento_Customer/js/model/authentication-popup',
    'OlenaK_RegularCustomer_form'
], function ($, ko, customerData, Component, formRestriction, authPopup) {
    'use strict';

    return Component.extend({
        defaults: {
            allowForGuests: false,
            template: 'OlenaK_RegularCustomer/login-button'
        },
        customerMustLogIn: formRestriction.customerMustLogIn,
        isLoggedIn: !!customerData.get('regular-customer')().isLoggedIn,

        /**
         * Constructor
         */
        initialize: function () {
            this._super();
            customerData.get('regular-customer').subscribe((personalInfo) => {
                this.isLoggedIn(!!personalInfo.isLoggedIn);
            });
        },

        /**
         * Initialize observables and subscribe to their change if needed
         * @returns {*}
         */
        initObservable: function () {
            this._super();

            this.observe(['isLoggedIn']);

            this.customerMustLogIn = ko.computed(() => {
                return !this.allowForGuests && !this.isLoggedIn();
            });

            formRestriction.customerMustLogIn(this.customerMustLogIn());
            this.customerMustLogIn.subscribe((newValue) => {
                formRestriction.customerMustLogIn(newValue);
            });

            this.shouldShowLoginButton = ko.computed(() => {
                return this.customerMustLogIn() && !formRestriction.requestAlreadySent();
            });

            return this;
        },

        /**
         * Show login popup on button click
         */
        showModal: function () {
            authPopup.showModal();
        }
    });
});
