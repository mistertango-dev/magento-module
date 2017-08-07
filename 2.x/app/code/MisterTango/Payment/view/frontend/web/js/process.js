define(
    'MisterTango_Payment/js/process',
    [
        'jquery',
        'mage/url',
        'https://payment.mistertango.com/resources/scripts/third/socket/socket.io-1.2.1.js',
        'https://payment.mistertango.com/resources/scripts/mt.collect.js'
    ],
    function ($, url, io) {
        'use strict';

        var MisterTangoPaymentProcess = {
            config: {
                autoOpen: null,
                quoteId: null,
                username: null,
                callbackUrl: null,
                locale: null,
                websocket: null
            },
            state: {
                open: false,
                offlinePayment: false,
                success: false
            },
            init: function (config) {
                window.io = io;

                MisterTangoPaymentProcess.config.autoOpen = config.autoOpen;
                MisterTangoPaymentProcess.config.quoteId = config.quoteId;
                MisterTangoPaymentProcess.config.username = config.username;
                MisterTangoPaymentProcess.config.callbackUrl = config.callbackUrl;
                MisterTangoPaymentProcess.config.locale = config.locale;

                MisterTangoPaymentProcess.load(function () {
                    $(document).on('click', '[data-mtpayment-trigger]', function () {
                        MisterTangoPaymentProcess.open($(this));
                    });

                    if (MisterTangoPaymentProcess.config.autoOpen === 1) {
                        MisterTangoPaymentProcess.open($('[data-mtpayment-trigger]').eq(0));
                    }

                    setInterval(
                        function () {
                            if (MisterTangoPaymentProcess.state.open === false) {
                                window.location.href = url.build(
                                    '/mistertango_payment/order/review/quote_id/'
                                    + MisterTangoPaymentProcess.config.quoteId
                                );
                            }
                        },
                        30000
                    );
                });
            },
            load: function (afterLoad) {
                mrTangoCollect.load();

                mrTangoCollect.set.recipient(MisterTangoPaymentProcess.config.username);

                mrTangoCollect.onOpened = MisterTangoPaymentProcess.onOpen;
                mrTangoCollect.onClosed = MisterTangoPaymentProcess.onClose;

                mrTangoCollect.onSuccess = MisterTangoPaymentProcess.onSuccess;
                mrTangoCollect.onOffLinePayment = MisterTangoPaymentProcess.onOfflinePayment;

                afterLoad();
            },
            open: function ($target) {
                MisterTangoPaymentProcess.state.offlinePayment = false;
                MisterTangoPaymentProcess.state.open = true;

                mrTangoCollect.set.description($target.attr('data-transaction-id'));
                mrTangoCollect.set.payer($target.attr('data-transaction-email'));
                mrTangoCollect.set.amount($target.attr('data-transaction-amount'));
                mrTangoCollect.set.currency($target.attr('data-transaction-currency'));
                mrTangoCollect.set.lang(MisterTangoPaymentProcess.config.locale);
                if (MisterTangoPaymentProcess.config.callbackUrl) {
                    mrTangoCollect.custom = {'callback': MisterTangoPaymentProcess.config.callbackUrl};
                }

                mrTangoCollect.submit();
            },
            onOpen: function () {
                MisterTangoPaymentProcess.state.open = true;
            },
            onOfflinePayment: function (response) {
                mrTangoCollect.onSuccess = function () {
                };
                MisterTangoPaymentProcess.state.offlinePayment = true;
            },
            onSuccess: function (response) {
                MisterTangoPaymentProcess.state.success = true;
            },
            onClose: function () {
                MisterTangoPaymentProcess.state.open = false;

                if (MisterTangoPaymentProcess.state.success) {
                    window.location.href = url.build('checkout/onepage/success');
                }
            }
        };

        return MisterTangoPaymentProcess.init;
    }
);
