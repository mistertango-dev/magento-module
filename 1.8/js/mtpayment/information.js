MisterTango.Information = {
    init: function () {
        setInterval(MisterTango.Information.updateOrderStatesTable, 30000);
    },
    updateOrderStatesTable: function () {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                console.log(xhttp.responseText);

                if (xhttp.responseText.success) {
                    var order = document.getElementById('mtpayment-information-order');
                    order.innerHTML(xhttp.responseText.html);

                    if (MisterTango.disallowDifferentPayment) {
                        var elements = document.getElementsByClassName('jsAllowDifferentPayment');
                        for(var index = 0; index < elements.length; index++) {
                            elements[index].parentNode.removeChild(elements[index]);
                        }
                    }
                }
            }
        };
        xhttp.open('POST', urlOrders, true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.send(
            'ajax=1' +
            '&order=' + order
        );
    }
};

document.addEventListener('DOMContentLoaded', MisterTango.Information.init, false);
