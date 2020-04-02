$(function(){
  var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

  var itno = $("#itno_id_sec").data("itno");
  var token = $("#token").val();


  var dataTable = $('#table-grid').DataTable({
    
    "serverSide": true,
    "destroy":true,
    "ajax":{
      url :base_url+"Main_sales/table_itinerary_report", // json datasource
      type: "post",  // method  , by default get
      data: {"itno" : itno},
      error: function(){  // error handling
        $(".table-grid-error").html("");
        // $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
        $("#table-grid_processing").css("display","none");
      }
    },
  });

  dataTable.destroy();

  
  $('.search-input-text').on('keyup click', function(){   // for text boxes
    var i =$(this).attr('data-column');  // getting column index
    var v =$(this).val();  // getting search input value
    dataTable.columns(i).search(v).draw();
  });

  $('.search-input-select').on('change', function(){   // for select box
    var i =$(this).attr('data-column');  
    var v =$(this).val();  
    dataTable.columns(i).search(v).draw();
  });

  $('.printBtn').click(function(e){

    var currUrl = window.location.href;

    currUrl = currUrl.replace("itnerary_report", "itinerary_print");
    window.open (currUrl, '_blank');


    $('.printBtn').attr("disabled","true");
    $('.printBtn').attr("title","This document has already been printed.");
  });

    // var qrcode_text = itno;

    //       $('#qrcode').qrcode({
    //       width: 128,
    //       height: 128,
    //       text: qrcode_text
    //     });

    // var canvas = $('#qrcode canvas');
    // var blob = canvas.get(0).toDataURL("image/png");
    // $("#qrcimg").prop('src',blob);

    $(".addNotes").click(function(e){
        
        e.preventDefault();

        var notes = $("#notes").val();

        $.ajax({
            type:'post',
            url: base_url+'Main_sales/save_itinerary_notes',
            data: {"itno" : itno, "notes": notes},
            beforeSend:function(data){
                $(".cancelBtn, .updateNotes").prop('disabled', true); 
                $(".updateNotes").text("Please wait...");
                
            },
            success:function(data){
                // $(".cancelBtn, .updateNotes").prop('disabled', false);
                // $(".updateNotes").text("Update");
                if (data.success == 1) {
                    dataTable.draw(); //refresh datatable
                    //$(thiss).find('input').val(""); // clean fields
                    
                    $.toast({
                        heading: 'Success',
                        text: data.message,
                        icon: 'success',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        bgColor: '#5cb85c',
                        textColor: 'white',
                        allowToastClose: false,
                        hideAfter: 10000
                    });

                    window.setTimeout(function(){
                            window.location.href=base_url+"Main_sales/itnerary_report/" + token + '/' + itno;
                        },1000)

                }else{
                    $.toast({
                        heading: 'Note',
                        text: data.message,
                        icon: 'error',
                        loader: false,   
                        stack: false,
                        position: 'top-center',  
                        bgColor: '#f0ad4e',
                        textColor: 'white'        
                    });
                }
            }
        });
    });

});