$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang

		$("#sosearchfilter").change(function() {
		var searchtype = $('#sosearchfilter').val(); // id ng dropdown
		var currentdate = new Date();

		   if(searchtype == "ponodiv")
	       {
			 $(".search_status").val("");
			 $('.ponodiv').show('slow');	
			 $('.podatediv').hide('slow');
			 $(".search_customer").val("");	
			 $('.searchbyName').hide('slow');
			 $('.ponostatus').hide('slow');
			 $('.search_po_btn').show('slow');

		   }
		   if(searchtype == "podatediv")
	       {
			 $('.ponodiv').hide('slow');
			 $(".search_customer").val("");	
			 $(".search_status").val("");	
			 $('.podatediv').show('slow');
			 $('.searchbyName').hide('slow');
			 $('.ponostatus').hide('slow');
			 $('.search_po_btn').show('slow');
		   }
		   if(searchtype == "ponostatus")
	       {
			 $('.ponodiv').hide('slow');
			 $(".search_pono").val("");	
			 $(".search_customer").val("");	
			 $('.podatediv').show('slow');
			 $('.searchbyName').hide('slow');
			 $('.ponostatus').show('slow');
			 $('.search_po_btn').show('slow');
		   }
		   if(searchtype == "searchbyName")
	       {
			 $('.ponodiv').hide('slow');
			 $(".search_pono").val("");	
			 $(".search_customer").val("");	
			 $('.podatediv').show('slow');
			 $('.ponostatus').hide('slow');
			 $('.search_po_btn').show('slow');
			 $('.searchbyName').show('slow');
		   }
	});

	
	searchToday();
	function searchToday(){ //for date today search first
		var dataTable = $('#table-grid').DataTable({
			
			"serverSide": true,
			"order": [[ 1, "desc" ]],
			"columnDefs": [{ "orderable": false, "targets": [ 5 ], "className": "dt-center" }],
			"ajax":{
				url :base_url+"Main_sales/tble_franchise_summary", // json datasource
				type: "post",  // method  , by default get
				data:{'username':username},
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
		}); 

		//var i = $(".searchDateTo").attr('data-column');  // getting column index
		//var v = $(".searchDateTo").val();  // getting search input value
		//alert(i+' '+v);
		//dataTable.columns(i).search(v).draw();

		// var i = $(".searchDateFrom").attr('data-column');  // getting column index
		// var v = $(".searchDateFrom").val();  // getting search input value
		// dataTable.columns(i).search(v).draw();
	}

		var oTable = $('#table-grid').dataTable();

		$("#search_order").click(function() {
			var checker=0;
		var search_pono =  $('.search_pono').val();
		var dateto =  $('.search-input-select1').val();
		var datefrom =  $('.search-input-select2').val();
		var status =  $('#search_status').val();
		var customer =  $('#search_customer').val();
		var shipping =  $('#search_shipping').val();
		var searchtype = $('#sosearchfilter').val();

		if(searchtype == "podatediv")
		{
			if(dateto == "" || datefrom == "")
			{
				$.toast({
					heading: 'Note:',
					text: "No date found. Please choose a date.",
					icon: 'info',
					loader: false,  
					stack: false,
					position: 'top-center', 
					allowToastClose: false,
					bgColor: '#FFA500',
					textColor: 'white'  
				});
				checker=0;
			}
			else
			{
				checker=1;
			}

		}

		else if(searchtype == "ponodiv")
		{
			if(search_pono == "")
			{
				$.toast({
					heading: 'Note:',
					text: "No franchise service number found. Please fill in data.",
					icon: 'info',
					loader: false,  
					stack: false,
					position: 'top-center', 
					allowToastClose: false,
					bgColor: '#FFA500',
					textColor: 'white'  
				});
				checker=0;
			}
			else
			{
				checker=1;
			}

		}

		else if(searchtype == "ponostatus")
		{
			if(status == "")
			{
				$.toast({
					heading: 'Note:',
					text: "No status selected. Please select a status.",
					icon: 'info',
					loader: false,  
					stack: false,
					position: 'top-center', 
					allowToastClose: false,
					bgColor: '#FFA500',
					textColor: 'white'  
				});
				checker=0;
			}
			else
			{
				checker=1;
			}

		}

		else if(searchtype == "poshipping")
		{
			if(shipping == "")
			{
				$.toast({
					heading: 'Note:',
					text: 'Please fill in shipping field.',
					icon: 'info',
					loader: false,  
					stack: false,
					position: 'top-center', 
					allowToastClose: false,
					bgColor: '#FFA500',
					textColor: 'white'  
				});
				checker=0;
			}
			else
			{
				checker=1;
			}

		}

		else if(searchtype == "searchbyName")
		{
			if(customer == "")
			{
				$.toast({
					heading: 'Note:',
					text: 'Please fill in customer name field.',
					icon: 'info',
					loader: false,  
					stack: false,
					position: 'top-center', 
					allowToastClose: false,
					bgColor: '#FFA500',
					textColor: 'white'  
				});
				checker=0;
			}
			else
			{
				checker=1;
			} 
		}


		if(checker == 1){
			oTable.fnFilter( $(".search-input-select1").val(), '0' );
			oTable.fnFilter( $(".search-input-select2").val(), '1' );
			oTable.fnFilter( $(".search_pono").val(), '2' );
			oTable.fnFilter( $("#search_status").val(), '3' );
			oTable.fnFilter( $("#search_customer").val(), '4' );

		}

		});
	
});

function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}