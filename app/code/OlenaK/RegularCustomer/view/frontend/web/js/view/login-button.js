define([
    'jquery',
    'ko',
    'uiComponent',
    'OlenaK_RegularCustomer_formSubmitRestriction',
    'Magento_Customer/js/model/authentication-popup',
    'OlenaK_RegularCustomer_form'
], function ($, ko, Component, formRestriction, authPopup) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'OlenaK_RegularCustomer/login-button'
        },

        customerMustLogIn: formRestriction.customerMustLogIn,

        showModal: function () {
            authPopup.showModal();
        }
    });
});
