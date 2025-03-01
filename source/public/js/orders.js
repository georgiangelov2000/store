$(function(){
    let table = $(".table").DataTable({
        serverSide: true,
        ajax: {
            url: "/api/v1/orders",
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
                "data": "status",
                "orderable": false,
                "render": function (data) {
                    let statusClass = (data === "completed") ? "success" : (data === "canceled") ? "danger" : "warning";
                    return `<span class="badge badge-${statusClass}">${data}</span>`;
                }
            },
            {
                "data": "total_price",
                "orderable": true,
                "render": function (data) {
                    return `$${parseFloat(data).toFixed(2)}`;
                }
            },
            {
                "orderable": false,
                "render": function(data,type,row) {
                    return `
                        <button class="btn btn-sm shadow-sm btn-primary" onclick="confirmUpdateStatus(${row.id}, 2)"><i class="fa-solid fa-wrench"></i> Update Status</button>
                        <button class="btn btn-sm shadow-sm btn-info" onclick="viewOrderItems(${row.id})"><i class="fa-solid fa-magnifying-glass"></i> Order Items</button>
                        <button class="btn btn-sm shadow-sm btn-danger" onclick="confirmDeleteOrder(${row.id})"><i class="fa-solid fa-trash"></i> Delete</button>
                    `;
                }
            }
        ],
        order: [[0, "asc"]],
    });
    
    /**
     * Confirm status update with SweetAlert
     */
    window.confirmUpdateStatus = function (orderId, status) {
        Swal.fire({
            title: "Are you sure?",
            text: "You are about to change the order status.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, update it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                updateStatus(orderId, status);
            }
        });
    }

    /**
     *  Update Order Status
     */
    function updateStatus(orderId, status) {
        fetch(`/api/v1/orders/${orderId}/update-status`, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ "status": status })
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire("Updated!", data.message || "Order status updated.", "success");
            $('.table').DataTable().ajax.reload();
        })
        .catch(error => {
            Swal.fire("Error!", "Something went wrong.", "error");
            console.error("Error:", error);
        });
    }

    /**
     * View Order Items
     */
    window.viewOrderItems = function(orderId) {
        fetch(`/api/v1/orders/${orderId}/items`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                Swal.fire("Error!", data.error, "error");
                return;
            }

            // 🔹 Build Table for Order Items
            let itemsTable = `
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Discount</th>
                        </tr>
                    </thead>
                    <tbody>`;

            data.items.forEach(item => {
                itemsTable += `
                    <tr>
                        <td>${item.id}</td>
                        <td>${item.product}</td>
                        <td>${item.sku}</td>
                        <td>${item.quantity}</td>
                        <td>$${parseFloat(item.price).toFixed(2)}</td>
                        <td>$${parseFloat(item.discount).toFixed(2)}</td>
                    </tr>`;
            });

            itemsTable += `</tbody></table>`;

            Swal.fire({
                title: `Order #${orderId} Items`,
                html: itemsTable,
                icon: "info",
                width: "600px"
            });
        })
        .catch(error => {
            Swal.fire("Error!", "Could not fetch order items.", "error");
            console.error("Error:", error);
        });
    }

    /**
     *  Confirm Order Deletion
     */
    window.confirmDeleteOrder = function(orderId){
        Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                deleteOrder(orderId);
            }
        });
    }

    /**
     * Delete Order
     */
    function deleteOrder(orderId) {
        fetch(`/api/v1/orders/${orderId}/delete`, {
            method: "DELETE",
            headers: { "Content-Type": "application/json" }
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire("Deleted!", data.message || "Order has been deleted.", "success");
            $('.table').DataTable().ajax.reload();
        })
        .catch(error => {
            Swal.fire("Error!", "Could not delete order.", "error");
            console.error("Error:", error);
        });
    }

    
    $("#createOrderForm").submit(function (e) {
        e.preventDefault();
        
        let itemsInput = $("#orderItems").val().trim();
        if (!itemsInput) {
            Swal.fire("Error!", "Please enter order items.", "error");
            return;
        }


        console.log(itemsInput);
        fetch("/api/v1/orders", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ items: itemsInput })
        })
        .then(response => response.json())
        .then(data => {
            if (data.order_id) {
                Swal.fire("Success!", "Order created successfully!", "success");
                $('#createOrderModal').modal('hide');
                $('.table').DataTable().ajax.reload(null,false);
            } else {
                Swal.fire("Error!", data.error || "Failed to create order.", "error");
            }
        })
        .catch(error => {
            Swal.fire("Error!", "Something went wrong.", "error");
            console.error("Error:", error);
        });
    });


})