$(function(){
    var base_url = $("body").data("base_url");

        drpayno = $(".drpayno").val();

        showTableMain();
        showTableSub();

        function showTableMain(){
        var dataTable = $('#table-grid').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "order": [[ 0, "asc"]],
            "ajax":{
            url :base_url+"Main_entity/showCollectionDetails", // json datasource
            type: "post",  // method  , by default get
            data:{'drpayno':drpayno,'table':'tablemain'},
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

                    $(".lblDrpayno").text(json.drPayno);
                    $(".lblTran").text(json.tranDate);

                    $(".h1name").text(json.name);
                    $(".lblBranch").text(json.branchname);
                    $(".lblMode").text(json.creditTerm);
                    $(".lblContact").text(json.contact);
                    $(".lblOutlet").text(json.address);

                    $(".btnTotal").text('Total ' + json.total);

                }
        });//data table
        var dataTable = $('#table-grid2').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "order": [[ 0, "asc"]],
            "ajax":{
            url :base_url+"Main_entity/showCollectionDetails", // json datasource
            type: "post",  // method  , by default get
            data:{'drpayno':drpayno,'table':'tablemain'},
            beforeSend:function(data)
            {
                $("#table-grid2").LoadingOverlay("show"); 
            },
            complete: function()
            {
                $("#table-grid2").LoadingOverlay("hide"); 
            },
            error: function(){  // error handling
                $(".table-grid-error").html("");
                // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                $("#table-grid_processing").css("display","none");
            }
            }
        });  
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
            url :base_url+"Main_entity/showCollectionDetails", // json datasource
            type: "post",  // method  , by default get
            data:{'drpayno':drpayno,'table':'tablesub'},
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
        var dataTable = $('#table-ref2').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "bPaginate": false, //hide pagination
            "bInfo": false, // hide showing entries
            "columnDefs": [{ "orderable": false, "targets": [ 0, 1, 2, 3 ], "sClass":"text-center","visible": false, "targets": [ 3 ] }],
            "ajax":{
            url :base_url+"Main_entity/showCollectionDetails", // json datasource
            type: "post",  // method  , by default get
            data:{'drpayno':drpayno,'table':'tablesub'},
                beforeSend:function(data)
                {
                    $("#table-ref2").LoadingOverlay("show"); 
                },
                complete: function()
                {
                    $("#table-ref2").LoadingOverlay("hide"); 
                },
                error: function(){  // error handling
                    $(".table-grid-error").html("");
                    // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            }
        });//data table  
        };

    $('.btnPrint').on('click', function(){   // for select box

                $("body").css('visibility','hidden');
                $(".printArea").css('visibility','visible');
                window.print();
                $("body").css('visibility','visible');
                $(".printArea").css('visibility','hidden');
                $(".print-status").css('display','none');
      
    });

    $('#table-ref').delegate(".btnView", "click", function(){
        var drno = this.id;
        var currUrl = window.location.href;
        currUrl = currUrl.replace(drpayno,drno);
        currUrl = currUrl.replace("entity_customer_collection", "entity_customersoa_delivery_receipt");
        window.location = currUrl;

    });


});//main
