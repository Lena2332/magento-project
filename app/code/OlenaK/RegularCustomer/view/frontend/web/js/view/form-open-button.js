define([
    'jquery',
    'uiComponent',
    'OlenaK_RegularCustomer_formSubmitRestriction'
], function ($, Component, formRestriction) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'OlenaK_RegularCustomer/form-open-button'
        },

        formSubmitIsRestricted: formRestriction.formSubmitDeniedMessage,

        /**
         * Generate event to open the form
         */
        openRequestForm: function () {
            $(document).trigger('olenak_regular_customer_form_open');
        }
    });
});
