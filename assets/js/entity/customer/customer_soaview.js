$(function(){
    var base_url = $("body").data("base_url");

    var idno;
    var filetype;
    var filtertype;
    var filename;
    var link;

        function showTable(idno,filetype,filename){
        var dataTable = $('#table-grid').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "columnDefs": [{ "orderable": false, "targets": [ 4, 5 ], "sClass":"text-center" }],
            "order": [[ 0, "asc"]],
            "ajax":{
            url :base_url+"Main_entity/entity_customerSOAFilterTable", // json datasource
            type: "post",  // method  , by default get
            data:{'idno':idno,'filetype':filetype,'filename':filename},
                 beforeSend:function(data)
                {
                    $("body").LoadingOverlay("show"); 
                },
                complete: function()
                {
                    $("body").LoadingOverlay("hide"); 
                },
                error: function(){  // error handling
                    $(".table-grid-error").html("");
                    // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            }
        });//data table
        };

        function getValues(){
            filtertype =  $(".filtertype").val();
            filetype = $(".filetype").val();
            idno = $(".idno").val();
            getLink();
        }

    function getLink(){
        var currUrl = window.location.href;

        currUrl = currUrl.replace("entity_customersoa_view", "entity_customersoa_delivery_receipt");
        link = currUrl.replace(idno, "");


    }

    $('.searchBtn').on('click', function(){   // for select box
        
        getValues();
        showTable(idno,filetype,filename);      
             
    });

    $('#table-grid').delegate(".btnView", "click", function(){

        var drno = this.id;
        var currUrl = window.location.href;
        currUrl = currUrl.replace("entity_customersoa_view", "entity_customersoa_delivery_receipt");
        link = currUrl.replace(idno, "");
        window.open(link+drno)


    });

    $('.exportBtn').on('click', function(){   // for select box
        
        getValues();

        window.location.href=''+base_url+'Main_entity/printCustomersoa/'+idno+'/'+filetype+'/'+filtertype;
    });    


});//main
