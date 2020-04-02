$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang


	searchToday();
	function searchToday(){ //for date today search first
		var dataTable = $('#table-grid').DataTable({
			
			"serverSide": true,
			"ajax":{
				url :base_url+"Main_sales/itinerary_table_summary", // json datasource
				type: "post",  // method  , by default get
				data:{'username':username},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		}); 

		// var i = $(".searchDateTo").attr('data-column');  // getting column index
		// var v = $(".searchDateTo").val();  // getting search input value
		// dataTable.columns(i).search(v).draw();

		var i = $(".searchDateFrom").attr('data-column');  // getting column index
		var v = $(".searchDateFrom").val();  // getting search input value
		dataTable.columns(i).search(v).draw();
	}


	// var dataTable = $('#table-grid').DataTable({
	// 	
	// 	"serverSide": true,
	// 	//"destroy":true,
	// 	"ajax":{
	// 		url :base_url+"Main_sales/itinerary_table_summary", // json datasource
	// 		type: "post",  // method  , by default get
	// 		data:{'username':username},
	// 		error: function(){  // error handling
	// 			$(".table-grid-error").html("");
	// 			// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
	// 			$("#table-grid_processing").css("display","none");
	// 		}
	// 	}

	// });
	
		$("#sosearchfilter").change(function() {
		var searchtype = $('#sosearchfilter').val(); // id ng dropdown
		var currentdate = new Date();

		   if(searchtype == "ponodiv")
	       {
	       	 $(".searchDateTo").val("");
			 $(".searchDateFrom").val("");
			 $(".search_status").val("");
			 

			 $('.ponodiv').show('slow');	
			 $('.podatediv').hide('slow');
			 $('.ponostatus').hide('slow');
			 $('.search_po_btn').show('slow');

		   }
		   if(searchtype == "podatediv")
	       {
	       	$(".searchDateTo").val($.datepicker.formatDate('mm/dd/yy', currentdate));
			 $(".searchDateFrom").val($.datepicker.formatDate('mm/dd/yy', currentdate));
			 $('.ponodiv').hide('slow');
			 $(".search_status").val("");	
			 $('.podatediv').show('slow');
			 $('.ponostatus').hide('slow');
			 $('.search_po_btn').show('slow');
		   }
		   if(searchtype == "ponostatus")
	       {
	       	$(".searchDateTo").val("");
			 $(".searchDateFrom").val("");
			 $('.ponodiv').hide('slow');
			 $(".search_pono").val("");	
			 $('.podatediv').hide('slow');
			 $('.ponostatus').show('slow');
			 $('.search_po_btn').show('slow');
		   }
	});
	
	//searchToday();
	// function searchToday(){ //for date today search first

	// 	var i = $(".searchDateTo").attr('data-column');  // getting column index
	// 	var v = $(".searchDateTo").val();  // getting search input value
	// 	//alert(i+' '+v);
	// 	dataTable.columns(i).search(v).draw();

	// 	var i = $(".searchDateFrom").attr('data-column');  // getting column index
	// 	var v = $(".searchDateFrom").val();  // getting search input value
	// 	dataTable.columns(i).search(v).draw();
	// }

	var oTable = $('#table-grid').dataTable();

		$("#search_order").click(function() {
			oTable.fnFilter( $(".search-input-select1").val(), '0' );
			oTable.fnFilter( $(".search-input-select2").val(), '1' );
			oTable.fnFilter( $(".search_pono").val(), '2' );
			oTable.fnFilter( $("#search_status").val(), '3' );
			//oTable.fnFilter( $("#input3").val(), '4' );
		});


   $(".BtnSaveItinerary").click(function(e){
         e.preventDefault();

        var token = $("#token").val();    
        var count = $("#tdata").val(); // validation    

            var sonoarray=[];
			var truckarray=[];
			var datearray=[];
			var userarray=[];
			var idnoarray=[];
			var areaarray=[];

            sonoarray = [];
            truckarray = [];
            datearray = [];
            userarray = [];
            idnoarray = [];
            areaarray = [];
            
           	for(i=0; i < count; i++ )
			{
				var sono = $('#sono'+i).val(); 
				var truck = $('#valplateno'+i).val(); 
				var trandate = $('#trandate'+i).val(); 
				var username = $('#uname'+i).val(); 
				var idno = $('#idno'+i).val(); 
				var area = $('#area'+i).val(); 

				if (truck != "" || truck != 0)
                {  
                      sonoarray.push(sono);
					  truckarray.push(truck);
					  datearray.push(trandate);
					  userarray.push(username);
					  idnoarray.push(idno);
					  areaarray.push(area);
	                 }		
				}
                        $.ajax({
                            type:'post',
                            url:base_url+'Main_sales/r_salesorderitineraryadd',
                            data:{
                            "sonoarray": sonoarray,
                            "truckarray": truckarray,
                            "datearray": datearray,
                            "userarray": userarray,
                            "idnoarray": idnoarray,
                            "areaarray": areaarray,
                            },
                            success:function(data){
                            if(data.success == 1)
                                {     
								$.toast({
		                            heading: 'Success',
		                            text: 'You have successfully save Sales Order Itinerary.',
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
			                            text: 'No record found.',
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
				                     window.location.href=base_url+"Main_sales/salesorder_itinerary/" + token;
				             	 },500)   
                            }   
                        });    
					});

	
});

function SetTruckvalue(count)
{
	var plateno = $('#plateno'+count).val();
	$('#valplateno'+count).val(plateno);
}