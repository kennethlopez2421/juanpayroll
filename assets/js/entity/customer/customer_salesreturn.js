$(function(){
    var base_url = $("body").data("base_url");

    var drretno;

        drretno = $(".drretno").val();

        showTableMain();
        showTableSub();

        function showTableMain(){
        var dataTable = $('#table-grid').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "order": [[ 0, "asc"]],
            "ajax":{
            url :base_url+"Main_entity_jv/showSalesReturnDetails", // json datasource
            type: "post",  // method  , by default get
            data:{'drretno':drretno,'table':'tablemain'},
                error: function(){  // error handling
                    $(".table-grid-error").html("");
                    // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            },"fnDrawCallback": function() {
                    var api = this.api()
                    var json = api.ajax.json();
                    console.log(json);

                    $(".lblDrretno").text(json.drretno);
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
                    var rowCount = json.rowCount;
                    if(rowCount > 0){
                    $(".print-status").css('display','none');
                    }else{
                    $(".print-status").css('display','block');
                    }                    

                }
        });//data table
        var dataTable = $('#table-grid2').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "order": [[ 0, "asc"]],
            "ajax":{
            url :base_url+"Main_entity_jv/showSalesReturnDetails", // json datasource
            type: "post",  // method  , by default get
            data:{'drretno':drretno,'table':'tablemain'},
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
            url :base_url+"Main_entity_jv/showSalesReturnDetails", // json datasource
            type: "post",  // method  , by default get
            data:{'drretno':drretno,'table':'tablesub'},
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
            url :base_url+"Main_entity_jv/showSalesReturnDetails", // json datasource
            type: "post",  // method  , by default get
            data:{'drretno':drretno,'table':'tablesub'},
                error: function(){  // error handling
                    $(".table-grid-error").html("");
                    // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            }
        });//data table        
        };

    $('#table-ref').delegate(".btnView", "click", function(){
        var drno = this.id;
        var currUrl = window.location.href;
        currUrl = currUrl.replace(drretno,drno);
        currUrl = currUrl.replace("entity_customer_salesreturn", "entity_customersoa_delivery_receipt");
        window.location = currUrl;

    });

    $('.btnPrint').on('click', function(){   // for select box

        $.ajax({
            type:'post',
            url: base_url+'Main_entity_jv/salesreturnprintedlog',
            data: {'drretno':drretno},
                    
            beforeSend:function(data){
                $(".printBtn").text("Please wait...");
                $(".printBtn").prop("disabled",true); 
            },
            success:function(data){
                $(".printBtn").text("Print Sales Return Form");
                    
                if(data.success == 1){

                $("body").css('visibility','hidden');
                $(".printArea").css('visibility','visible');
                window.print();
                $("body").css('visibility','visible');
                $(".printArea").css('visibility','hidden');
                $(".print-status").css('display','none');


                }else{
                    $.toast({
                        heading: 'Warning',
                        text: 'This document has already been printed.',
                        icon: 'warning',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#f0ad4e',
                        textColor: 'white'  
                    });              
                }
            }

        });//ajax
      
    });    


});//main
