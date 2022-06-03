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
        },

        /**
         * Generate event to open the form
         */
        openRequestForm: function () {
            $(document).trigger('olenak_regular_customer_form_open');
        }
    });

    return $.OlenaK.regularCustomer_formOpenButton;
});
