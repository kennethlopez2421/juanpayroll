$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang
	var searchtype = $('#sosearchfilter').val(); // id ng dropdown

	// 	$("#sosearchfilter").change(function() {
		
	// 	var currentdate = new Date();
	// 	var searchtype = $('#sosearchfilter').val(); // id ng dropdown
	// 	   if(searchtype == "ponodiv")
	//        {
	//        	 $(".searchDateTo").val("");
	// 		 $(".searchDateFrom").val("");
	// 		 $(".search_status").val("");
	// 		 $('.ponodiv').show('slow');	
	// 		 $('.podatediv').hide('slow');
	// 		 $(".search_customer").val("");	
	// 		 $('.searchbyName').hide('slow');
	// 		 $('.poshipping').hide('slow');
	// 		 $('.ponostatus').hide('slow');
	// 		 $('.search_po_btn').show('slow');
	// 		 $("#search_shipping").val("");	
	// 	   }
	// });

	var dataTable = $('#table-grid').DataTable({

		"serverSide": true,
		"order": [ 1, "desc" ],
        "columnDefs": [{ "orderable": false, "targets": [ 0, 4 ], "className": "dt-center" }],
		"ajax":{
				url :base_url+"Main_sales/salesorder_prep_table", // json datasource
				type: "post",  // method  , by default get
				data:{'searchtype':searchtype},
				beforeSend:function(data){
					$.LoadingOverlay("show"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
					$("#btn_export_excel").prop('hidden',true);
				},
				complete: function(data)
				{
					$.LoadingOverlay("hide"); 
				}

			}
		}); 



	$(".BtnSaveSOPrep").click(function(e){
		e.preventDefault();

		var token = $("#token").val();    
        var count = $("#tdata").val(); // validation    

        var checkbox_value = "";
        $("input.prep_check[type=checkbox]").each(function () {
        	var ischecked = $(this).is(":checked");
        	if (ischecked) {
        		checkbox_value +=  $(this).val() + ["|"];
        	}
        });

        $.ajax({
        	type:'post',
        	url:base_url+'Main_sales/table_save_soprep',
        	data:{
        		"checkbox_value":checkbox_value,
        	},
        	beforeSend:function(data){
        		$.LoadingOverlay("show"); 
        	},
        	complete: function(data)
        	{
        		$.LoadingOverlay("hide"); 
        	},
        	success:function(data){
        		if(data.success == 1)
        		{     
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

        		} else{
        			$.toast({
        				heading: 'Note',
        				text: data.message,
        				icon: 'error',
        				loader: false,  
        				stack: false,
        				position: 'top-center', 
        				bgColor: '#f0ad4e',
        				textColor: 'white',
        				allowToastClose: false,
        				hideAfter: 3000
        			});

        		}
        		window.setTimeout(function(){
        			window.location.href=base_url+"Main_sales/sales_order_preparation/" + token;
        		},1000)
        	}   
        });    
    });


	// 	$("#search_order").click(function() {
	// 		oTable.fnFilter( $(".search-input-select1").val(), '0' );
	// 		oTable.fnFilter( $(".search-input-select2").val(), '1' );
	// 		//oTable.fnFilter( $(".search_pono").val(), '2' );
	// 	});

	$("#search_order").click(function(){
		$(".loader").show();
		$("#table_salesorder").hide();

		var i1 =$('.searchDateFrom').attr('data-column');  // getting column index
		var v1 =$('.searchDateFrom').val();  // getting search input value
		var i2 =$('.searchDateTo').attr('data-column');  // getting column index
		var v2 =$('.searchDateTo').val();  // getting search input value
		

		dataTable.columns(i1).search(v1)
		.columns(i2).search(v2)

		.draw();
	});

});