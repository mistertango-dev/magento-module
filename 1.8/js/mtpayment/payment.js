MisterTango = {
    isOpened: false,
    success: false,
    order: null,
    disallowDifferentPayment: false,
    transaction: null,
    customerEmail: null,
    amount: null,
    currency: null,
    lang: null,
    init: function () {
        mrTangoCollect.load();

        mrTangoCollect.set.recipient(mrTangoUsername);

        mrTangoCollect.onOpened = MisterTango.onOpen;
        mrTangoCollect.onClosed = MisterTango.onClose;

        mrTangoCollect.onSuccess = MisterTango.onSuccess;
        mrTangoCollect.onOffLinePayment = MisterTango.onOfflinePayment;

        MisterTango.initButtonPay();
    },
    initButtonPay: function () {
        document.getElementsByTagName('body')[0].onclick = function (e) {
            var target = e.target || e.srcElement;

            if (target.classList.contains('mistertango-button-pay')) {
                e.preventDefault();

                var websocket = this.getAttribute('data-websocket');
                if (websocket != null) {
                    mrTangoCollect.ws_id = websocket;
                }

                MisterTango.order = null;
                var order = this.getAttribute('data-order');
                if (order != null) {
                    MisterTango.order = order;
                }

                MisterTango.transaction = target.getAttribute('data-transaction');
                MisterTango.customerEmail = target.getAttribute('data-customer-email');
                MisterTango.amount = target.getAttribute('data-amount');
                MisterTango.currency = target.getAttribute('data-currency');
                MisterTango.lang = target.getAttribute('data-language');

                mrTangoCollect.set.payer(MisterTango.customerEmail);
                mrTangoCollect.set.amount(MisterTango.amount);
                mrTangoCollect.set.currency(MisterTango.currency);
                mrTangoCollect.set.description(MisterTango.transaction);
                mrTangoCollect.set.lang(MisterTango.lang);

                mrTangoCollect.submit();
            }
        };
    },
    onOpen: function () {
        MisterTango.is_opened = true;
    },
    onOfflinePayment: function (response) {
        mrTangoCollect.onSuccess = function () {};
        MisterTango.onSuccess(response);
    },
    onSuccess: function (response) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                console.log(xhttp.responseText);

                /*if (data.success) {
                    $('.jsAllowDifferentPayment').remove();
                    MisterTango.disallow_different_payment = true;
                    MisterTango.id_order = data.order;
                    MisterTango.success = true;

                    if (MisterTango.is_opened === false) {
                        MisterTango.afterSuccess();
                    }
                }*/
            }
        };
        xhttp.open('POST', mrTangoUrlOrders + (MisterTango.order?'validatetransaction':'validateorder'), true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.send(
            'order=' + (MisterTango.order?MisterTango.order:'') +
            '&transaction=' + MisterTango.transaction +
            '&websocket=' + mrTangoCollect.ws_id +
            '&amount=' + MisterTango.amount
        );
    },
    onClose: function () {
        MisterTango.isOpened = false;

        if (MisterTango.success) {
            MisterTango.afterSuccess();
        }
    },
    afterSuccess: function () {
        var operator = mrTangoUrlInformation.indexOf('?') === -1?'?':'&';
        window.location.href = mrTangoUrlInformation + operator + 'id_order=' + MisterTango.id_order;
    }
};

document.addEventListener('DOMContentLoaded', function () { MisterTango.init() }, false);
