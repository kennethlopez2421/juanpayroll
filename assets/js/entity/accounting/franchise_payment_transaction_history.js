$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$('.datepicker2').datepicker({
		format: 'yyyy/mm/dd',
    	startDate: '0',
	});

	var dataTable = $('#table-grid').DataTable({
		//"processing": true,
		"serverSide": true,
		"columnDefs": [
    		{ targets: 6, orderable: false, "sClass":"text-center"}
		],
		"ajax":{
			url :base_url+"Main_franchise_accounting/table_franchise_payment_transaction_history", // json datasource
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

	$('.filterBtn').click(function(){
		//|| $('.searchAppName').val != "" 
		if($('.searchDate2').val() != "" && $('.searchDate').val != ""){ //all

			$(".table_fis").show();
			var c =$('.searchAppName').attr('data-column');  // getting column index
			var d =$('.searchAppName').val();  // getting search input value

			var e =$('.searchDate').attr('data-column');  
			var f =$('.searchDate').val();  

			var g =$('.searchDate2').attr('data-column');  
			var h =$('.searchDate2').val();  

			dataTable.columns(c).search(d);
			dataTable.columns(e).search(f);
			dataTable.columns(g).search(h).draw();

			$("#btn_export_excel").prop('hidden',false);
		}else{
			$.toast({
			    heading: 'Warning',
			    text: "Please Fill up `Date from` and `Date to`",
			    icon: 'warning',
			    loader: false,   
			    stack: false,
			    position: 'top-center',  
			    bgColor: '#f0ad4e;',
				textColor: 'white'        
			});

			// dataTable.columns(0).search("");
			// dataTable.columns(1).search("");
			dataTable.columns(2).search("").draw();
		};
	});

	//Scanning QRCODE
	var scanning = false;
	var token = $(".page-header").data('token');

	$('.btnScanQrCode').click(function(e){
		scanning = true;
		$(".backdrop").fadeTo(200, 1);
		var qrcode = "";
		 
	    $(document).keypress(function (e) { 
	        var code = (e.keyCode ? e.keyCode : e.which);
	        qrcode += String.fromCharCode(code);
	        if(code == 13){
	        	if(qrcode.length == 11){
	        		if(scanning){
			        	 window.open(base_url+"Main_entity/view_fis_transaction_history/"+qrcode+"/"+token,'_blank');
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

});