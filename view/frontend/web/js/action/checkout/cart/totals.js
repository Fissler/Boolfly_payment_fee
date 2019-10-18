define(
    [
        'jquery',
        'ko',
        'mage/storage',
        'mage/url',
        'Magento_Checkout/js/action/get-totals',
    ],
    function(
        $,
        ko,
        storage,
        urlBuilder,
        getTotalsAction
    ) {
        'use strict';

        // if we have a active boolfly payment fee configuration then pass to the callback below
        // else we return the getTotalsAction callback
        var paymentFeeConfig = $.extend({is_active: false}, window.checkoutConfig.boolfly_payment_fee || {}),
            getTotals = function() {
                var deferred = $.Deferred();
                if (typeof isLoading === "function") {
                    isLoading(false);
                }
                getTotalsAction([], deferred);
            };

        if (!paymentFeeConfig.is_active) {
            return getTotals;
        }

        return function (isLoading, payment) {
            var serviceUrl = urlBuilder.build('paymentfee/checkout/totals');
            return storage.post(
                serviceUrl,
                JSON.stringify({payment: payment})
            ).done(
                function(response) {
                    if (response) {
                        getTotals();
                    }
                }
            ).fail(
                function (response) {
                    isLoading(false);
                    //var error = JSON.parse(response.responseText);
                }
            );
        }
    }
);
