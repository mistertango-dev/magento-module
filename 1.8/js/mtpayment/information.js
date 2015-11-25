MisterTango.Information = {
    init: function () {
        setInterval(MisterTango.Information.updateOrderStatesTable, 30000);
    },
    updateOrderStatesTable: function () {
        $.ajax({
            type: 'GET',
            async: true,
            dataType: "json",
            url: urlOrders,
            headers: { "cache-control": "no-cache" },
            cache: false,
            data: {
                action: 'get_html_table_order_states',
                id_order: idOrder
            },
            success: function(data)
            {
                $('#mistertango-information-order-states').replaceWith(data.html_table_order_states);
                if (MisterTango.disallow_different_payment) {
                    $('.jsAllowDifferentPayment').remove();
                }
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', MisterTango.Information.init, false);
