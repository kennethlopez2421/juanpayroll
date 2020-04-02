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
    		{ targets: 3, orderable: false, "sClass":"text-center"}
		],
		"ajax":{
			url :base_url+"Main_entity/fis_transaction_history_moni_officer", // json datasource
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

});