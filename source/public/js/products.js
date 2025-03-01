$(function () {
    let table = $(".table").DataTable({
        serverSide: true,
        ajax: {
            url: "/api/v1/products",
            data: function (d) {
                return $.extend({}, d, {
                    "search": d.search.value,
                    "limit": d.length,
                    "order_column": d.order[0].column,
                    "order_dir": d.order[0].dir
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
        order: [[0, "asc"]],
    });
});
