$(function(){

    //Variables Declaration//////////////////////////////////////////

    var base_url = $("body").data("base_url");

    var idno;
    var name;
    var paymenttype;
    var paymentfor;
    var paymentdate;
    var refno;
    var amount; 
    var searchtype = "none";  

    var dataTable = $('#table-grid').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "order": [[ 0, "asc"]],
            "ajax":{
            url :base_url+"Main_entity/franchise_list", // json datasource
            type: "post",  // method  , by default get
            data:{'idno':idno,'name':name,'searchtype':searchtype},
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
            }
    });//data table


    $(".printArea").css('visibility','hidden');
    $('.dividno').show('slow');
    $("#divsearchfilter").change(function() {
        var searchtype = $('#divsearchfilter').val();

           if(searchtype == "dividno")
           {
             $('.dividno').show('slow');
             $('.divname').hide('slow');
             $("#nameSearch").val("");
             $("#idnosearch").val("");      
           }
           else if(searchtype == "divname")
           {
             $('.divname').show('slow');
             $('.dividno').hide('slow');    
             $("#nameSearch").val("");
             $("#idnosearch").val("");
           }
         
    });
  
    //LISTENERS///////////////////////////////////////////////////

    $('.searchBtn').on('click', function(){   // for select box     
        getValues();
        showTable(idno,name,searchtype);
    });

    $('#table-grid').delegate(".btnView", "click", function(){
        var viewID  = this.id;
        var viewTrandate = $(this).data('trandate');
        var viewName = $(this).data('name');
        var viewRefno = $(this).data('refno');
        var viewMOP = $(this).data('paymenttype');
        var viewPF = $(this).data('paymentfor');
        var viewNotes = $(this).data('notes');

        $('#viewFranchise').modal('show');
        updateFranchiseinfo(viewID,viewTrandate,viewName.toUpperCase(),viewRefno,viewMOP,viewPF,viewNotes);
        
        showTable2(viewID);

    });

    $('.printBtn').on('click', function(){   // for select box     
        $("body").css('visibility','hidden');
        $(".printArea").css('visibility','visible');
        window.print();
        $("body").css('visibility','visible');
        $(".printArea").css('visibility','hidden');
    });

    //FUNCTIONS///////////////////////////////////////////////////

    function showTable(idno,name,searchtype){
        var dataTable = $('#table-grid').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "order": [[ 0, "asc"]],
            "ajax":{
            url :base_url+"Main_entity/franchise_list", // json datasource
            type: "post",  // method  , by default get
            data:{'idno':idno,'name':name,'searchtype':searchtype},
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
            }
        });//data table
    };

    function showTable2(viewID){
        var dataTable = $('#table-grid2').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "bPaginate": false, //hide pagination
            "bInfo": false, // hide showing entries
            "columnDefs": [{ "orderable": false, "targets": [ 0, 1, 2 ], "sClass":"text-center" }],
            "order": [[ 0, "asc"]],
            "ajax":{
            url :base_url+"Main_entity/franchise_list_details", // json datasource
            type: "post",  // method  , by default get
            data:{'viewID':viewID},
                error: function(){  // error handling
                    $(".table-grid-error").html("");
                    // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            }
        });//data table

        var dataTableprint = $('#table-grid3').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "bPaginate": false, //hide pagination
            "bInfo": false, // hide showing entries
            "columnDefs": [{ "orderable": false, "targets": [ 0, 1, 2 ], "sClass":"text-center" }],
            "order": [[ 0, "asc"]],
            "ajax":{
            url :base_url+"Main_entity/franchise_list_details", // json datasource
            type: "post",  // method  , by default get
            data:{'viewID':viewID},
                error: function(){  // error handling
                    $(".table-grid-error").html("");
                    // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            }
        });//data table

    };    

    function getValues(){
        paymentdate =  $("#paymentdatesearch").val();
        idno = $("#idnosearch").val();
        paymenttype =  $("#paymenttypesearch").val();
        paymentfor = $("#paymentforsearch").val();
        refno =  $("#refnosearch").val();
        name = $("#nameSearch").val();
        searchtype = $('#divsearchfilter').val();
    }

    function updateFranchiseinfo(viewID,viewTrandate,viewName,viewRefno,viewMOP,viewPF,viewNotes){
      $(".lblid").text('Franchise Payment Record View');
      $(".lbltrandate").text(viewTrandate);
      $(".h3customerName").text(viewName);
      $(".lblReference").text(viewRefno);
      $(".lblMode").text(viewMOP);
      $(".lblFor").text(viewPF);
      $(".parDetails").text(viewNotes);
    }  

});//main


function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}
