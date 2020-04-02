$(function(){
    var base_url = $("body").data("base_url");

    $('.divdate').show('slow');
    $("#divsearchfilter").change(function() {
        var searchtype = $('#divsearchfilter').val();

           if(searchtype == "dividno")
           {
             $('.dividno').show('slow');
             $('.divname').hide('slow');
             $('.divdate').hide('slow');
             $('.divaccount').hide('slow'); 

             $("#accountsearch").val("");
             $("#namesearch").val("");
             $("#idno").val("");      
           }
           else if(searchtype == "divname")
           {
             $('.divname').show('slow');
             $('.dividno').hide('slow'); 
             $('.divdate').hide('slow');
             $('.divaccount').hide('slow'); 

             $("#accountsearch").val("");
             $("#namesearch").val("");
             $("#idno").val("");
           }
           else if(searchtype == "divaccount")
           {
             $('.divname').hide('slow');
             $('.dividno').hide('slow'); 
             $('.divdate').show('slow');   
             $('.divaccount').show('slow'); 

             $("#namesearch").val("");
             $("#idno").val("");
           }
           else
           {
             $('.divdate').show('slow');
             $('.divname').hide('slow');
             $('.dividno').hide('slow'); 
             $('.divaccount').hide('slow'); 

             $("#accountsearch").val("");
             $("#namesearch").val("");
             $("#idno").val("");    
           }
         
    });

    var type;
    var fromDate;
    var toDate;
    var ticketno;
    var name;
    var status;
    var account;
    
        getValues();
        showTable(fromDate,toDate,ticketno,name,searchtype,account);

        function showTable(fromDate,toDate,ticketno,name,searchtype,$account){
        var dataTable = $('#table-grid').DataTable({
            "destroy": true,
            //"processing": true,
            "serverSide": true,
            "destroy": true,
            "columnDefs": [{ "orderable": false, "targets": [ 6 ], "sClass":"text-center" }],
            "order": [[ 1, "asc"]],
            "ajax":{
            url :base_url+"Main_entity/entity_ticketlistview", // json datasource
            type: "post",  // method  , by default get
            data:{'fromDate':fromDate,'toDate':toDate,'ticketno':ticketno,'name':name,'searchtype':searchtype,'account':account},
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
            fromDate =  $(".search-input-select1").val();
            toDate = $(".search-input-select2").val();
            ticketno = $(".idnosearch").val();
            name = $(".namesearch").val();
            account = $(".accountsearch").val();
            // status = $(".status").val();
            // type = $(".type").val();  
            searchtype = $("#divsearchfilter").val();     
       
        } 

    $('.searchBtn').on('click', function(){   // for select box
        
        getValues();
        showTable(fromDate,toDate,ticketno,name,searchtype);       
             
    });

    $('#table-grid').delegate(".btnView", "click", function(){
        $('#viewTicket').modal('show');
           var ticketid  = this.id;
            $.ajax({
                type: 'post',
                url: base_url+'Main_entity/ticketlist_View',
                data:{'ticketid':ticketid},
            
                beforeSend:function(data)
                {
                    $("body").LoadingOverlay("show"); 
                },
                complete: function()
                {
                    $("body").LoadingOverlay("hide"); 
                },
                success:function(data){
                    if(data.success == 1){

                        $(".lblticket").text("Customer Ticket #" + ticketid);
                        $(".lbltrandate").text(data.trandate);

                        $(".h3customerName").text(data.name);

                        $(".lblBranch").text(data.branch);
                        $(".lblType").text(data.type);
                        $(".lblStatus").text(data.status);
                        $(".lblCreatedby").text(data.createdby);

                        $(".parDetails").text(data.details);

                    }else{
                        $.toast({
                        heading: 'Warning',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#d9534f',
                        textColor: 'white'  
                        });
                    }

                },
                error:function(data){

                        $.toast({
                        heading: 'Warning',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#d9534f',
                        textColor: 'white'  
                        });

                    // setTimeout(function(){
                    // location.reload(); 
                    // },3000);

                }

            });

    });

    $('#table-grid').delegate(".btnViewRes", "click", function(){
        $('#viewTicketResolved').modal('show');
           var ticketid  = this.id;
            $.ajax({
                type: 'post',
                url: base_url+'Main_entity/ticketlist_View_Resolved',
                data:{'ticketid':ticketid},
            
                beforeSend:function(data)
                {
                    $("body").LoadingOverlay("show"); 
                },
                complete: function()
                {
                    $("body").LoadingOverlay("hide"); 
                },
                success:function(data){
                    if(data.success == 1){

                        $(".lblticketres").text("Customer Ticket #" + ticketid);
                        $(".lbltrandateres").text(data.trandate);

                        $(".h3customerNameres").text(data.name);

                        $(".lblBranchres").text(data.branch);
                        $(".lblTyperes").text(data.type);
                        $(".lblStatusres").text(data.status);
                        $(".lblCreatedbyres").text(data.createdby);

                        $(".parDetailsres").text(data.details);

                        $(".lblResolveddate").text(data.soldate);
                        $(".lblResolvedby").text(data.lastuserupdate);

                        $(".parResDetails").text(data.ticketresolution);

                    }else{
                        $.toast({
                        heading: 'Note',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#FFA500',
                        textColor: 'white'  
                        });
                    }

                },
                error:function(data){

                        $.toast({
                        heading: 'Note',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#FFA500',
                        textColor: 'white'  
                        });

                    setTimeout(function(){
                    location.reload(); 
                    },2000);

                }

            });

    });

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
