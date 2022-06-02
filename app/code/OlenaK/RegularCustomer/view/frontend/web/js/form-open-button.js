define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('OlenaK.regularCustomer_formOpenButton', {
        /**
         * Constructor
         * @private
         */
        _create: function () {
            $(this.element).on('click.olenak_regular_customer_form_open_button',this.openRequestForm.bind(this));

            $(document).on('olenak_regular_customer_hide_button', this.hideButton());
        },

        /**
         * Generate event to open the form
         */
        openRequestForm: function () {
            $(document).trigger('olenak_regular_customer_form_open');
        },

        hideButton: function () {
            $(this.element).hide();
        }
    });

    return $.OlenaK.regularCustomer_formOpenButton;
});
