$(document).ready(function(){


	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();


	var cvno = $('.cvno').text();

	dataTable = $('#table-grid2').DataTable({
		"processing": true,
		"serverSide": true,
		"ajax":{
			url:base_url+"Main_account/cv_approval_table", // json datasource
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	

	$('.submitcheckbtn').click(function(e){
		var cvno = $("#cvno").val();
		var token = $("#token").val();

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_account/approve_cvno_details',
	  		data:{'cvno':cvno},
	  		success:function(data){
	  		

	  			if (data.success == 1)
	  			{
						$.toast({
					    heading: 'Success',
					    text: 'You have successfully approved CV No.' + cvno,
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: 'yellowgreen',
						textColor: 'white'  
					});

					window.setTimeout(function(){
						 window.location.href = base_url+'Main_account/cv_approval/'+ token;
					},2000)
	  			}


	  		}
	  
	  	});
		
	});

	$('#table-grid2').delegate(".btnLink","click", function(){
		var id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_account/apvpo_edit',
	  		data:{'id':id},
	  		beforeSend:function(data) {
				$("body").LoadingOverlay("show"); 
			},
			complete: function() {
				$("body").LoadingOverlay("hide"); 
			},
	  		success:function(data){

	  			if (data.success == 1){

	  				var res = data.result;
	  				$('.rcvno').text(res.rcvno);
	  				
	  			}

	  			/*$('#del_item_id').val(data.id);
	  			$('#info_desc').html(data.itemname);
				$('#deleteItemModal').modal();*/

				

	  		},
	  		error: function(error){
	  			$.toast({
				    heading: 'Error',
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
	  	});

	});


	// $('#table-grid').delegate(".btnLink3","click", function(e){
 //       e.preventDefault();
 //       var currUrl = window.location.href;
 //        currUrl = currUrl.replace("apv_list", "apvno_log");
 //        window.location = currUrl;
 // 	});

	$('#table-grid2').delegate(".nxtPage","click", function(e){
			e.preventDefault();
	 		var val = $(this).data('value');
	 		alert(val);
		});

	$('.search-input-text').on('keyup blur', function(){   // for text boxes
		var i =$(this).attr('data-column');  // getting column index
		var v =$(this).val();  // getting search input value
		dataTable.columns(i).search(v).draw();
	});

	$('#add_item_btn').click(function(e){
		$('#addItemModal').modal();
	});

	$('#table-grid2').delegate(".btnView", "click", function(){

	  	var id = $(this).data('value');

	  	var currUrl = window.location.href;

	  	currUrl = currUrl.replace("apv_list", "check_view_approval");
	  	window.location = currUrl+"/"+id;
	  	
	});


	

	$('#table-grid2').delegate(".view_apv","click", function(){
 		var val = $(this).data('value');
 		alert(val);
	});

	$('#table-grid2').delegate(".btnDelete","click", function(){
		var id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_inventory/get_item',
	  		data:{'id':id},
	  		success:function(data){

	  			console.log(data);
	  			
	  			data = JSON.parse(data);

	  			$('#del_item_id').val(data.id);
	  			$('#info_desc').html(data.itemname);
				$('#deleteItemModal').modal();

	  		},
	  		error: function(error){
	  			$.toast({
				    heading: 'Error',
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
	  	});

	});

	//-----------

	$('#table-grid2').delegate(".btnLink","click", function(){
		var id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_account/get_voucher_info',
	  		data:{'id':id},
	  		success:function(data){

	  			if (data.success == 1){

	  				var res = data.result;
	  				$('.apvno').text(res.apvno);
	  				$('.trandate').text(res.trandate);
	  				$('.voucher_name').text(res.supname);
					$('.terms_of_payment').text(res.terms);
					$('.apv_amount').text(res.amount);
					$('.apv_balance').text(res.bal);
					$('.apv_status').text(res.apvstatus);
	  			}

	  			/*$('#del_item_id').val(data.id);
	  			$('#info_desc').html(data.itemname);
				$('#deleteItemModal').modal();*/

				

	  		},
	  		error: function(error){
	  			$.toast({
				    heading: 'Error',
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
	  	});

	});

	
	$('#table-grid2').delegate(".btnAnchor","click", function(){
      var id = $(this).data("value");
      var supid = $('.searchSupplier').val();
      $.ajax({
          type:'post',
          url:base_url+'Main_account/get_voucher_info',
          data:{'id':id ,'supid':supid},
          success:function(data){
              if (data.success == 1) {
                  var res = data.result;
                  var reslen = res.length;
                  var list = "";
                  for(var x = 0; x < reslen; x++){
                      list += "<tr>";
                      list += "<td>"+res[x].id+"</td>";
                      list += "<td>"+res[x].name+"</td>";
                      list += "<td>"+res[x].qty+"</td>";
                      list += "<td>"+res[x].uomid+"</td>";
                      list += "<td>"+res[x].qtyrcv+"</td>";
                      list += "<td>"+res[x].price+"</td>";
                      list += "<td>"+res[x].total+"</td>";
                      list += "</tr>";
                  }
                  $(".apvno1").html(res[0].apvno);
                  $(".voucher_name").html(res[0].supname);
                  $(".contactp").html(res[0].contactperson);
                  $(".stat").html(res[0].rcvall);
                  $(".mop").html(res[0].description);
                  $(".num").html(res[0].contactno);
                  $(".add").html(res[0].address);
                  $(".pono").html("#"+res[0].pono);
                  $(".tdate").html(res[0].trandate);
                  $(".tablebody").html(list);

              }

          }
      });
    });


});
