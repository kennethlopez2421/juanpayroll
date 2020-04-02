$(document).ready(function(){


	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.cvdatediv').show('slow');

	// 101018 - nick
	// searching process that can adapt the retaining of previous search if the user returns to this page

	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

    function fillDatatable(searchtype, datefrom, dateto, bcheckno) {
        dataTable = $('#table-grid').DataTable({
			destroy: true,
			"serverSide": true,
			"ajax":{
				url:base_url + "account/BCT/bct_history_table", // json datasource
				type: "post",  // method  , by default get
				data:{ "searchtype" : searchtype, "datefrom" : datefrom, "dateto" : dateto, "bcheckno" : bcheckno },
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function() {
					$.LoadingOverlay("hide"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}
	
	// reuseable toast call function for easeness and shorter code
	function toastMessage(heading, text, icon, bgcolor) {
		// #5cb85c success
		// #f0ad4e error
		$.toast({
			heading: heading,
			text: text,
			icon: icon,
			loader: false,  
			stack: false,
			position: 'top-center', 
			allowToastClose: false,
			bgColor: bgcolor,
			textColor: 'white'  
		});
    }

	function formatDate(date) {
		var d = new Date(date),
			month = '' + (d.getMonth() + 1),
			day = '' + d.getDate(),
			year = d.getFullYear();
	
		if (month.length < 2) month = '0' + month;
		if (day.length < 2) day = '0' + day;
	
		return [year, month, day].join('-');
	}

	$("#cvsearchfilter").change(function() {
		var searchtype = $('#cvsearchfilter').val();

	   	if(searchtype == "cvdatediv") {
			$('.cvdatediv').show('slow');
			$('.drno').hide('slow');
       	}
		else if(searchtype == "bchecknodiv") {
			$('.bchecknodiv').show('slow');
			$('.cvdatediv').hide('slow');	
			$("#bcheckno").val("");
		}
	});

	fillDatatable($('#cvsearchfilter').val(), date, date, "");

	$('#searchBtn').click(function(e) {
		var searchtype = $('#cvsearchfilter').val();
		var datefrom = formatDate($("#date1").val());
		var dateto = formatDate($("#date2").val());
		var bcheckno = $("#bcheckno").val();
		var checker = 0;

		if(searchtype == "cvdatediv") {
			if (datefrom == "" && dateto == "") {
				checker = 0;
				toastMessage('Note:', "No date selected.", 'error', '#FFA500');
			}
			else {
				checker = 1;
			}
		}
		else if(searchtype == "bchecknodiv") {
	    	if (bcheckno == "") {
				checker = 0;
				toastMessage('Note:', "Please fill up Bounced Check No.", 'error', '#FFA500');
			}
			else {
				checker = 1;
			}
		}
			
		if(checker == 1){		
			fillDatatable(searchtype, datefrom, dateto, bcheckno);
		}
	});

	// $('#table-grid').delegate(".nxtPage","click", function(e){
	// 		e.preventDefault();
	//  		var val = $(this).data('value');
	//  		alert(val);
	// 	});

	// $('.search-input-text').on('keyup blur', function(){   // for text boxes
	// 	var i =$(this).attr('data-column');  // getting column index
	// 	var v =$(this).val();  // getting search input value
	// 	dataTable.columns(i).search(v).draw();
	// });

	// $('#add_item_btn').click(function(e){
	// 	$('#addItemModal').modal();
	// });

	// $('#table-grid').delegate(".btnView", "click", function(){

	//   	var id = $(this).data('value');

	//   	var currUrl = window.location.href;

	//   	currUrl = currUrl.replace("apv_list", "check_view_approval");
	//   	window.location = currUrl+"/"+id;
	  	
	// });


	// $('#add_inventory_form').submit(function(event){
	// 	event.preventDefault();

	// 	var form = $(this);

	// 	console.log(form.serialize());

	//         $.ajax({
	// 	            url: form.attr('action'),
	// 	            type: form.attr('method'),
	// 				data: form.serialize(),
	// 	        }).done(function(response) {

	// 	            var response = JSON.parse(response);

	// 	            if(response.success===false)
	// 	            {
	// 	            	$.toast({
	// 					    heading: 'Error',
	// 					    text: response.message,
	// 					    icon: 'error',
	// 					    loader: false,  
	// 					    stack: false,
	// 					    position: 'top-center', 
	// 						allowToastClose: false,
	// 						bgColor: '#d9534f',
	// 						textColor: 'white'  
	// 					});
	// 	            }
	// 	            else
	// 	            {
	// 	            	dataTable.draw();

	// 	            	$('#addItemModal').modal('hide');
	// 	            	$('#add_inventory_form')[0].reset();
	// 	            	$.toast({
	// 					    heading: 'Success',
	// 					    text: response.message,
	// 					    icon: 'success',
	// 					    loader: false,  
	// 					    stack: false,
	// 					    position: 'top-center', 
	// 						allowToastClose: false,
	// 						bgColor: 'yellowgreen',
	// 						textColor: 'white'  
	// 					});
						
	// 	            }

	// 	    });
	// });

	// $('#delete_item_form').submit(function(event){
	// 	event.preventDefault();

	// 	var form = $(this);

	// 	console.log(form.serialize());

	//         $.ajax({
	// 	            url: form.attr('action'),
	// 	            type: form.attr('method'),
	// 				data: form.serialize(),
	// 	        }).done(function(response) {

	// 	            var response = JSON.parse(response);

	// 	            if(response.success===false)
	// 	            {
	// 	            	$.toast({
	// 					    heading: 'Error',
	// 					    text: response.message,
	// 					    icon: 'error',
	// 					    loader: false,  
	// 					    stack: false,
	// 					    position: 'top-center', 
	// 						allowToastClose: false,
	// 						bgColor: '#d9534f',
	// 						textColor: 'white'  
	// 					});
	// 	            }
	// 	            else
	// 	            {
	// 	            	dataTable.draw();
		            	
	// 	            	$('#deleteItemModal').modal('hide');
	// 	            	$.toast({
	// 					    heading: 'Success',
	// 					    text: response.message,
	// 					    icon: 'success',
	// 					    loader: false,  
	// 					    stack: false,
	// 					    position: 'top-center', 
	// 						allowToastClose: false,
	// 						bgColor: 'yellowgreen',
	// 						textColor: 'white'  
	// 					});
						
	// 	            }

	// 	    });
	// });

	// $('#table-grid').delegate(".view_apv","click", function(){
 	// 	var val = $(this).data('value');
 	// 	alert(val);
	// });

	// $('#table-grid').delegate(".btnDelete","click", function(){
	// 	var id = $(this).data('value');

	// 	$.ajax({
	//   		type: 'post',
	//   		url: base_url+'Main_inventory/get_item',
	//   		data:{'id':id},
	//   		success:function(data){

	//   			console.log(data);
	  			
	//   			data = JSON.parse(data);

	//   			$('#del_item_id').val(data.id);
	//   			$('#info_desc').html(data.itemname);
	// 			$('#deleteItemModal').modal();

	//   		},
	//   		error: function(error){
	//   			$.toast({
	// 			    heading: 'Error',
	// 			    text: 'Something went wrong. Please try again.',
	// 			    icon: 'error',
	// 			    loader: false,  
	// 			    stack: false,
	// 			    position: 'top-center', 
	// 				allowToastClose: false,
	// 				bgColor: '#d9534f',
	// 				textColor: 'white'  
	// 			});
	//   		}
	//   	});

	// });

	// //-----------

	// $('#table-grid').delegate(".btnLink","click", function(){
	// 	var id = $(this).data('value');

	// 	$.ajax({
	//   		type: 'post',
	//   		url: base_url+'Main_account/get_voucher_info',
	//   		data:{'id':id},
	//   		success:function(data){

	//   			if (data.success == 1){

	//   				var res = data.result;
	//   				$('.apvno').text(res.apvno);
	//   				$('.trandate').text(res.trandate);
	//   				$('.voucher_name').text(res.supname);
	// 				$('.terms_of_payment').text(res.terms);
	// 				$('.apv_amount').text(res.amount);
	// 				$('.apv_balance').text(res.bal);
	// 				$('.apv_status').text(res.apvstatus);
	//   			}

	//   			/*$('#del_item_id').val(data.id);
	//   			$('#info_desc').html(data.itemname);
	// 			$('#deleteItemModal').modal();*/

				

	//   		},
	//   		error: function(error){
	//   			$.toast({
	// 			    heading: 'Error',
	// 			    text: 'Something went wrong. Please try again.',
	// 			    icon: 'error',
	// 			    loader: false,  
	// 			    stack: false,
	// 			    position: 'top-center', 
	// 				allowToastClose: false,
	// 				bgColor: '#d9534f',
	// 				textColor: 'white'  
	// 			});
	//   		}
	//   	});

	// });

	
	// $('#table-grid').delegate(".btnAnchor","click", function(){
    //   var id = $(this).data("value");
    //   var supid = $('.searchSupplier').val();
    //   $.ajax({
    //       type:'post',
    //       url:base_url+'Main_account/get_voucher_info',
    //       data:{'id':id ,'supid':supid},
    //       success:function(data){
    //           if (data.success == 1) {
    //               var res = data.result;
    //               var reslen = res.length;
    //               var list = "";
    //               for(var x = 0; x < reslen; x++){
    //                   list += "<tr>";
    //                   list += "<td>"+res[x].id+"</td>";
    //                   list += "<td>"+res[x].name+"</td>";
    //                   list += "<td>"+res[x].qty+"</td>";
    //                   list += "<td>"+res[x].uomid+"</td>";
    //                   list += "<td>"+res[x].qtyrcv+"</td>";
    //                   list += "<td>"+res[x].price+"</td>";
    //                   list += "<td>"+res[x].total+"</td>";
    //                   list += "</tr>";
    //               }
    //               $(".apvno1").html(res[0].apvno);
    //               $(".voucher_name").html(res[0].supname);
    //               $(".contactp").html(res[0].contactperson);
    //               $(".stat").html(res[0].rcvall);
    //               $(".mop").html(res[0].description);
    //               $(".num").html(res[0].contactno);
    //               $(".add").html(res[0].address);
    //               $(".pono").html("#"+res[0].pono);
    //               $(".tdate").html(res[0].trandate);
    //               $(".tablebody").html(list);

    //           }

    //       }
    //   });
    // });


});
