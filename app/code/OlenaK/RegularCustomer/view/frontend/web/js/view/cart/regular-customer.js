define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/totals'
], function (Component, quote, totals) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'OlenaK_RegularCustomer/cart/regular-customer',
            title: ''
        },
        totals: quote.getTotals(),

        /**
         * @return {*|Boolean}
         */
        isDisplayed: function () {
            return this.isFullMode() && this.getPureValue() !== 0.00;
        },

        /**
         * Get pure value.
         *
         * @return {*}
         */
        getPureValue: function () {
            let price = 0;

            if (quote.getTotals()()) {
                price = totals.getSegment('regular_customer').value;
            }

            return parseFloat(price);
        },

        /**
         * @return {*|String}
         */
        getValue: function () {
            return this.getFormattedPrice(this.getPureValue());
        }
    });
});
