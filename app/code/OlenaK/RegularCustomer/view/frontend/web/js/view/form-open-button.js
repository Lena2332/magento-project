define([
    'jquery',
    'ko',
    'uiComponent',
    'OlenaK_RegularCustomer_formSubmitRestriction',
    'OlenaK_RegularCustomer_form'
], function ($, ko, Component, formRestriction) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'OlenaK_RegularCustomer/form-open-button'
        },

        /**
         * Initialize data links (listens/imports/exports/links)
         * @returns {*}
         */
        initLinks: function () {
            this._super();

            // Check whether it is possible to open the modal - either form is modal or there are any other restrictions
            this.canShowOpenModalButton = ko.computed(() => {
                return this.isModal && !formRestriction.submitDenied();
            });

            return this;
        },


        /**
         * Generate event to open the form
         */
        openRequestForm: function () {
            $(document).trigger('olenak_regular_customer_form_open');
        }
    });
});
