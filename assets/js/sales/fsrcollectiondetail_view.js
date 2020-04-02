$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var id = $("#fsrcollection_id_sec").data("id");
	var srpayno = $("#fsrcollection_id_sec").data("id");
	var drpayno = $(".drpayno").val();


	var dataTable = $('#table-grid').DataTable({
		
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"Main_sales/tbl_fsrcollectiondetailed", // json datasource
			type: "post",  // method  , by default get
			data: {"id" : id},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable.destroy();

	var dataTable1 = $('#table-srcollection').DataTable({
		
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"Main_sales/tbl_srcollection_view", // json datasource
			type: "post",  // method  , by default get
			data: {"srpayno" : srpayno},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable1.destroy();

	
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

		currUrl = currUrl.replace("fsrcollectiondetail_view", "fsrcollection_print");
		window.open (currUrl, '_blank');

		$('.printBtn').attr("disabled","true");
		$('.printBtn').attr("title","This document has already been printed.");
	});

// $(".printCollection").click(function(e){
// 		e.preventDefault();
//     $('#printDivsRet1').attr('class','hidden'); 
//     $.ajax({
//         type: "POST",
//         url:base_url+'Main_sales/print_collection',
//         data: {"collectionid" : collectionid},
//         cache: false,
//         success:function(data){
//                     if(data.success == 1)
//                     {
                
//                 $(document).ready(function() {
//                     window.print();
//                 }); 
//             }   
//             $('#printDivsret2').removeAttr('class','hidden');   
//         }
//     });  
//   });
});


