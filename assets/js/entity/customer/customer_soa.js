$(function(){
    var base_url = $("body").data("base_url");

    $('.dividno').show('slow');
    $("#divsearchfilter").change(function() {
        var searchtype = $('#divsearchfilter').val();

           if(searchtype == "dividno")
           {
             $('.dividno').show('slow');
             $('.divname').hide('slow');
             $('.divaccount').hide('slow');
             $("#accountSearch").val("");
             $("#nameSearch").val("");
             $("#idno").val("");      
           }
           else if(searchtype == "divaccount")
           {
             $('.divname').hide('slow');
             $('.dividno').hide('slow');  
             $('.divaccount').show('slow');
             $("#accountSearch").val("");
             $("#nameSearch").val("");
             $("#idno").val("");
           }
           else if(searchtype == "divname")
           {
             $('.divname').show('slow');
             $('.dividno').hide('slow');
             $('.divaccount').hide('slow');
             $("#accountSearch").val("");    
             $("#nameSearch").val("");
             $("#idno").val("");
           }
         
    });
    getValues();
    var dataTable = $('#table-grid').DataTable({
            //"processing": true,
            "serverSide": true,
            "destroy": true,
            "order": [[ 1, "asc", 2, "asc"]],
            "ajax":{
            url :base_url+"Main_entity/entity_customersoalist", // json datasource
            type: "post",  // method  , by default get
            data:{'idno':idno,'name':name,'searchtype':searchtype, 'link': link, 'account':account},
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
                $("#table-grid_processing").css("display","none");
            }
            }
    });//data table

    var idno;
    var name;
    var branch;
    var creditterm;
    var link;
    var account;
    var searchtype="dividno";

        function showTable(idno,name,searchtype){
        var dataTable = $('#table-grid').DataTable({
            "destroy": true,
            //"processing": true,
            "serverSide": true,
            "destroy": true,
            "order": [[ 1, "asc", 2, "asc"]],
            "ajax":{
            url :base_url+"Main_entity/entity_customersoalist", // json datasource
            type: "post",  // method  , by default get
            data:{'idno':idno,'name':name,'searchtype':searchtype, 'link': link, 'account':account},
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
                $("#table-grid_processing").css("display","none");
            }
            }
        });//data table
        };

        function getValues(){
            idno =  $(".idnosearch").val();
            name = $(".nameSearch").val();
            searchtype = $("#divsearchfilter").val();
            account = $("#accountSearch").val();
            getLink();
        } 

    $('.searchBtn').on('click', function(){   // for select box
        
        getValues();
        showTable(idno,name,searchtype,link,account);      
             
    });

    function getLink(){
        var currUrl = window.location.href;

        link = currUrl.replace("entity_customersoa", "entity_customersoa_view");

    }

    $('#table-grid').delegate(".btnEdit", "click", function(){
        var currUrl = window.location.href;

        var ticketid = this.id;

        currUrl = currUrl.replace("entity_ticketlist", "entity_ticketedit");
        window.location = currUrl+"/"+ticketid;

    });    


});//main


function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}