$(function(){
	var base_url = $("body").data("base_url");

    var addno;
    var fromDate;
    var toDate;
    var refNo;
    var ingLocation;
    var buildLocation;
    var dateNow;
    var locationSearch;
    dateNow = $('.search-input-select1').val();

        getValues();
        showTable(fromDate,toDate,refNo,ingLocation,buildLocation);

        $('.divdate').show('slow');
        $('.divlocation').show('slow');
        
        $("#divsearchfilter").change(function() {
        var searchtype = $('#divsearchfilter').val();

           if(searchtype == "1")
           {
             $('.divdate').show('slow');
             $('.divlocation').show('slow');
             $('.divrefno').hide('slow');

             $(".refnosearch").val("");
             $(".ingLocation").val("");
             $(".buildLocation").val("");
             $(".search-input-select1").val(dateNow);
             $(".search-input-select2").val(dateNow);   
           }
           else if(searchtype == "2")
           {
             $('.divdate').hide('slow');
             $('.divlocation').hide('slow');
             $('.divrefno').show('slow');

             $(".refnosearch").val("");
             $(".ingLocation").val("");
             $(".buildLocation").val("");
             $(".search-input-select1").val("");
             $(".search-input-select2").val("");
           }
    });        

        function showTable(fromDate,toDate,refNo,ingLocation,buildLocation){
        var dataTable = $('#table-grid').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "order": [[ 2, "asc" ]],
            "ajax":{
            url :base_url+"Main_manufacturing/table_ingredientslist", // json datasource
            type: "post",  // method  , by default get
            data:{'fromDate':fromDate,'toDate':toDate,'refNo':refNo,'ingLocation':ingLocation,'buildLocation':buildLocation},
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

        function getValues(){
            fromDate =  $(".search-input-select1").val();
            toDate = $(".search-input-select2").val();
            refNo = $(".refnosearch").val();
            ingLocation = $(".ingLocation").val();
            buildLocation = $(".buildLocation").val();            
        } 

    $('.searchBtn').on('click', function(){   // for select box
        
        getValues();
        showTable(fromDate,toDate,refNo,ingLocation,buildLocation);       
             
    });

    $('#table-grid').delegate(".btnView", "click", function(){
    
        $('#viewIngredientlistModal').modal('show');
        
        addno = this.id;
        var dataTable = $('#table-ingredients-view').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "order": [[ 0, "asc" ]],
            "ajax":{
                url :base_url+"Main_manufacturing/ingredientslist_view_modal", // json datasource
                type: "post",  // method  , by default get
                data:{"addno":addno},
                error: function(){  // error handling
                    $(".table-grid-error").html("");
                    // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            },
            "fnDrawCallback": function() {
                var api = this.api()
                var json = api.ajax.json();

                $(".lblAddno").text("Ingredients Addition" + " #" + json.lblAddno);
                $(".lblPrep").text(json.lblPrepdate);
                $(".lblprepDate").text(json.lblPrepdate);
                $(".lblbuildDate").text(json.lblBuilddate);
                $(".lblingLocation").text(json.lblInglocation);
                $(".lblbuildLocation").text(json.lblBuildlocation);
                $(".lblencoder").text(json.lblEncoder);
                $("#notes").text(json.lblNotes);

            }
        });//data table

    });

    $(".printWin").click(function(e){

        window.location.href=''+base_url+'Main_manufacturing/printIngredientslist/'+addno;

    });                 

});//main
