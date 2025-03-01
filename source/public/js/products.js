$(function () {
    let table = $(".table").DataTable({
        serverSide: true,
        ajax: {
            url: "/api/v1/products",
            data: function (d) {
                return $.extend({}, d, {
                    "search": d.search.value,
                    "limit": d.length
                });
            }
        },
        columns: [
            {
                "data": "id",
                "orderable": false,
                "render": function (data) {
                    return `<strong>#${data}</strong>`;
                }
            },
            {
                "data": "name",
                "orderable": false
            },
            {
                "data": "sku",
                "orderable": false
            },
            {
                "data": "price",
                "orderable": true,
                "render": function (data) {
                    return `$${parseFloat(data).toFixed(2)}`;
                }
            }
        ]
    });
});
