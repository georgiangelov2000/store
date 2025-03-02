$(function () {
    let table = $(".table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/api/v1/products",
            data: function (d) {
                const orderColumnIndex = d.order[0].column;
                const orderColumnName = d.columns[orderColumnIndex].name;
                console.log(orderColumnName);
                return $.extend({}, d, {
                    "search": d.search.value,
                    'order_column': orderColumnName,
                    'order_dir': d.order[0].dir,
                    'limit': d.custom_length = d.length,
                });
            }
        },
        columns: [
            {
                "data": "id",
                "name" : "id",
                "orderable": true,
                "render": function (data) {
                    return `<strong>#${data}</strong>`;
                }
            },
            {
                "data": "name",
                "name": "name",
                "orderable": false
            },
            {
                "data": "sku",
                "name": "sku",
                "orderable": false
            },
            {
                "data": "unitPrice",
                "name": "unitPrice",
                "orderable": true,
                "render": function (data,type,row) {
                    console.log(row.unitPrice);
                    return `$${parseFloat(row.unitPrice).toFixed(2)}`;
                }
            }
        ],
        pageLength: 10,
        order: [[0, "asc"]],
    });
});
