$(function(){
    var base_url = $("body").data("base_url");

    var drno;
        drno = $(".drno").val();

        showTableMain();
        showTableSub();

        function showTableMain(){
        var dataTable = $('#table-grid').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "order": [[ 0, "asc"]],
            "ajax":{
            url :base_url+"Main_entity/showDirectSalesDetailsReference", // json datasource
            type: "post",  // method  , by default get
            data:{'drno':drno,'table':'tablemain'},
            beforeSend:function(data)
            {
                $("#table-grid").LoadingOverlay("show"); 
            },
            complete: function()
            {
                $("#table-grid").LoadingOverlay("hide"); 
            },
            error: function(){  // error handling
                $(".table-grid-error").html("");
                // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                $("#table-grid_processing").css("display","none");
            }
            },"fnDrawCallback": function() {
                    var api = this.api()
                    var json = api.ajax.json();
                    // console.log(json);

                    $(".lblDrno").text(json.drNo);
                    $(".lblTran").text(json.tranDate);

                    $(".h1name").text(json.name);
                    $(".lblBranch").text(json.branchname);
                    $(".lblMode").text(json.creditTerm);
                    $(".lblContact").text(json.contact);
                    $(".lblOutlet").text(json.address);
                    $(".lblShip").text(json.shippingVia);

                    $(".btnSubtotal").text('Subtotal: ' + json.subtotal);
                    $(".btnShipping").text('Shipping: ' + json.shipping);
                    $(".btnTotal").text('Total ' + json.total);
                    $(".txtarea").text(json.notes);

                }
        });//data table
        };

        function showTableSub(){
        var dataTable = $('#table-ref').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "bPaginate": false, //hide pagination
            "bInfo": false, // hide showing entries
            "columnDefs": [{ "orderable": false, "targets": [ 0, 1, 2, 3 ], "sClass":"text-center" }],
            "ajax":{
            url :base_url+"Main_entity/showDirectSalesDetailsReference", // json datasource
            type: "post",  // method  , by default get
            data:{'drno':drno,'table':'tablesub'},
            beforeSend:function(data)
            {
                $("#table-ref").LoadingOverlay("show"); 
            },
            complete: function()
            {
                $("#table-ref").LoadingOverlay("hide"); 
            },
                error: function(){  // error handling
                    $(".table-grid-error").html("");
                    // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            }
        });//data table
        };

    $('#table-ref').delegate(".btnViewSales", "click", function(){
        var drretno = this.id;
        var currUrl = window.location.href;
        currUrl = currUrl.replace(drno,drretno);
        currUrl = currUrl.replace("entity_customersoa_delivery_receipt", "entity_customer_salesreturn");
        window.location = currUrl;

    });

    $('#table-ref').delegate(".btnViewCollection", "click", function(){
        var drpayno = this.id;
        var currUrl = window.location.href;
        currUrl = currUrl.replace(drno,drpayno);
        currUrl = currUrl.replace("entity_customersoa_delivery_receipt", "entity_customer_collection");
        window.location = currUrl;

    });

});//main
