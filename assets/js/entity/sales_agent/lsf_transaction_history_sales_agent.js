$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$('.datepicker2').datepicker({
		format: 'yyyy/mm/dd',
    	startDate: '0',
	});

	var fis_app_id =  $(".fis_app_id").val();
	console.log(fis_app_id);

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
        "columnDefs": [
            { targets: 6, orderable: false, "sClass":"text-center"}
        ],
		"ajax":{
			url :base_url+"Main_entity/lsf_transaction_history_sales_agent", // json datasource
			data: {'fis_app_id' :fis_app_id},
			type: "post",  // method  , by default get
            beforeSend:function(data)
                {
                    $.LoadingOverlay("show"); 
                },
                complete: function()
                {
                    $.LoadingOverlay("hide"); 
                },
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	$('.convertToCustomerBtn').click(function(e){
        e.preventDefault();
        var fis_app_id = $(this).data('value');

        $.ajax({ 
            type: 'post',
            url: base_url+'Main_entity/convert_to_customer',
            data: {'fis_app_id':fis_app_id},
            beforeSend:function(data){
            	$('.convertToCustomerBtn').prop('disabled',true);
                $(this).html('Please wait...');
            },
            success:function(data){
               $('.convertToCustomerBtn').html('Converted to Customer');
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
                    hideAfter: 3000
                });
                
            }
        });
    });

    //Scanning QRCODE
    var scanning = false;

    $('.btnScanQrCode').click(function(e){
        scanning = true;
        $(".backdrop").fadeTo(200, 1);
        var qrcode = "";
        var token = $(".page-header").data('token');
         
        $(document).keypress(function (e) { 
            var code = (e.keyCode ? e.keyCode : e.which);
            qrcode += String.fromCharCode(code);

            if(code == 13){
                if(qrcode.length == 11){
                    if(scanning){
                        $.ajax({
                            type: 'post',
                            url: base_url+'Main_entity/getProposedLocationInfoUsingRefNo',
                            data:{'app_ref_no':qrcode},
                            success:function(data){
                                qrcode = "";
                                var success = data.success;
                                if(success == 1){
                                    var pl_id = data.pl_id;
                                    var status = data.status;
                                    if (status !=0) { 
                                        window.open(base_url+"Main_entity/endorsement_for_ocular_view2/"+pl_id +"/"+token,'_blank');
                                    }else{
                                        window.open(base_url+"Main_entity/endorsement_for_ocular_sa/"+pl_id+"/"+token,'_blank');
                                    }
                                }else{
                                    $.toast({
                                        heading: 'Warning',
                                        text: data.message,
                                        icon: 'warning',
                                        loader: false,   
                                        stack: false,
                                        position: 'top-center',  
                                        bgColor: '#f0ad4e;',
                                        textColor: 'white'        
                                    });
                                }
                              
                            }
                        }); 
                    }else{
                        $.toast({
                            heading: 'Warning',
                            text: "Please click on 'SCAN QR CODE' button before scanning.",
                            icon: 'warning',
                            loader: false,   
                            stack: false,
                            position: 'top-center',  
                            bgColor: '#f0ad4e;',
                            textColor: 'white'        
                        });
                        qrcode = "";
                    }
                }else if(qrcode.length == 10){
                    if(scanning){
                        $.ajax({
                            type: 'post',
                            url: base_url+'Main_entity/getLSFinfoUsingRefNo',
                            data:{'app_ref_no':qrcode},
                            success:function(data){
                                qrcode = "";
                                var success = data.success;
                                if(success == 1){
                                    var lsf_id = data.lsf_id;
                                    window.open(base_url+"Main_entity/view_pdf_lsf_form/"+lsf_id +"/"+token,'_blank');
                                }else{
                                      $.toast({
                                        heading: 'Warning',
                                        text: data.message,
                                        icon: 'warning',
                                        loader: false,   
                                        stack: false,
                                        position: 'top-center',  
                                        bgColor: '#f0ad4e;',
                                        textColor: 'white'        
                                    });
                                }
                            }
                        }); 
                    }else{
                        $.toast({
                            heading: 'Warning',
                            text: "Please click on 'SCAN QR CODE' button before scanning.",
                            icon: 'warning',
                            loader: false,   
                            stack: false,
                            position: 'top-center',  
                            bgColor: '#f0ad4e;',
                            textColor: 'white'        
                        });
                        qrcode = "";
                    }
                }else{
                    $.toast({
                        heading: 'Warning',
                        text: "Invalid QR Code",
                        icon: 'warning',
                        loader: false,   
                        stack: false,
                        position: 'top-center',  
                        bgColor: '#f0ad4e;',
                        textColor: 'white'        
                    });
                    qrcode = "";
                }
            }   
        });
        
    });

    $(".btnStopScan").click(function(e){
        e.preventDefault();
        $(".backdrop").fadeOut(200);
        scanning = false;
    });

    $(".btnSendOTL").click (function(e){
        e.preventDefault();
        var fis_app_id = $(".fis_app_id").val();
        
        $.ajax({
            type: 'post',
            url: base_url+'Main_entity/send_onetimelink_proposed_location_sales_agent',
            data:{'fis_app_id':fis_app_id},
            beforeSend:function(){
                $(".btnSendOTL").prop('disabled',true);
                $(".btnSendOTL").text("Please wait ...");
            },
            success:function(data){
                var success = data.success;
                var res = data.result;
                if(success == 1){
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
                        hideAfter: 3000
                    });
                    $(".btnSendOTL").prop('disabled',false);
                    $(".btnSendOTL").text('Send OTL Proposed Location');
                }else{
                      $.toast({
                        heading: 'Warning',
                        text: data.message,
                        icon: 'warning',
                        loader: false,   
                        stack: false,
                        position: 'top-center',  
                        bgColor: '#f0ad4e;',
                        textColor: 'white'        
                    });
                    $(".btnSendOTL").prop('disabled',false);
                    $(".btnSendOTL").text('Send OTL Proposed Location');
                }
            }
        }); 

    });

	
});