$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$('#datepicker').datepicker({
		format: 'yyyy/mm/dd'
	});


	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"columnDefs": [
			{ targets: [5], orderable: false}
		],
		"ajax":{
			url :base_url+"Main/transactions_applications_table", // json datasource
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	$('.filterBtn').click(function(){

		if($('.searchAppStatus').val() != "" || $('.searchRefNum').val() != "" || $('.searchDate').val != "" || $('.searchDate2').val != ""){ //all

			var a =$('.searchAppStatus').attr('data-column');  // getting column index
			var b =$('.searchAppStatus').val();  // getting search input value
			console.log(a);
			console.log(b);
			var c =$('.searchRefNum').attr('data-column');  // getting column index
			var d =$('.searchRefNum').val();  // getting search input value

			var e =$('.searchDate').attr('data-column');  
			var f =$('.searchDate').val();  

			var g =$('.searchDate2').attr('data-column');  
			var h =$('.searchDate2').val();  
			
			dataTable.columns(a).search(b);
			dataTable.columns(c).search(d);
			dataTable.columns(e).search(f);
			dataTable.columns(g).search(h).draw();
		}else{
			dataTable.columns(0).search("");
			dataTable.columns(1).search("");
			dataTable.columns(2).search("");
			dataTable.columns(3).search("").draw();
		};

		// //app status only
		// if($('.searchAppStatus').val() != "" || $('.searchRefNum').val() == "" || $('.searchDate').val == "" || $('.searchDate2').val == ""){
	
		// 	var a =$('.searchAppStatus').attr('data-column');  // getting column index
		// 	var b =$('.searchAppStatus').val();  // getting search input value
		// 	dataTable.columns(0).search("").draw();
		// 	dataTable.columns(1).search("").draw();
		// 	dataTable.columns(2).search("").draw();
		// 	dataTable.columns(a).search(b).draw();
		
		// };
		// // ref num only
		// if($('.searchAppStatus').val() == "" || $('.searchRefNum').val() != "" || $('.searchDate').val == "" || $('.searchDate2').val == ""){
	
		// 	var c =$('.searchRefNum').attr('data-column');  // getting column index
		// 	var d =$('.searchRefNum').val();  // getting search input value
		// 	dataTable.columns(0).search("").draw();
		// 	dataTable.columns(1).search("").draw();
		// 	dataTable.columns(c).search(d).draw();
		// 	dataTable.columns(3).search("").draw();
		
		// };


			
		// if($('.searchRefNum').val() != "" && $('.searchAppStatus').val() == "" ){ //ref num only
		// 	var c =$('.searchRefNum').attr('data-column');  // getting column index
		// 	var d =$('.searchRefNum').val();  // getting search input value
		// 	dataTable.columns(c).search(d).draw();
		// 	dataTable.columns(3).search("").draw();
		// };

		// if($('.searchDate').val != ""){
		// 	var e =$('.searchDate').attr('data-column');  
		// 	var f =$('.searchDate').val();  
		// 	dataTable.columns(e).search(f).draw();
		// }

		// if($('.searchDate2').val != ""){
		// 	var g =$('.searchDate2').attr('data-column');  
		// 	var h =$('.searchDate2').val();  
		// 	dataTable.columns(g).search(h).draw();
		// }
		
		// if($('.searchDate2').val == "" && $('.searchDate').val == "" && $('.searchRefNum').val() == "" && $('.searchAppStatus').val() == ""){
		// 	dataTable.columns(0).search("").draw();
		// 	dataTable.columns(1).search("").draw();
		// 	dataTable.columns(2).search("").draw();
		// 	dataTable.columns(3).search("").draw();
		// }
	 
	});
	
	// $('.search-input-text').on('keyup click', function(){   // for text boxes
	// 	var i =$(this).attr('data-column');  // getting column index
	// 	var v =$(this).val();  // getting search input value
	// 	dataTable.columns(i).search(v).draw();
	// });

	// $('.searchDate').on('change', function(){   // for select box
	// 	var i =$(this).attr('data-column');  
	// 	var v =$(this).val();  
	// 	dataTable.columns(i).search(v).draw();
	// });


	$('#table-grid').delegate(".btnView", "click", function(){
		$('#viewAccountsModal').modal('show');
	});

	$('#table-grid').delegate(".btnView", "click", function(){

	  	var app_id = $(this).data('value');
	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoTransactionApplicationUsingID',
	  		data:{'app_id':app_id},
	  		success:function(data){
	  			var res = data.result;
	  			// console.log(res);
	  			if (data.success == 1) {
					
	  					// $(".info_app_id").val(data.email);
	  					$(".info_app_id").val(res[0].email);


		  				$(".info_app_status").val(res[0].status_description);
						$(".info_app_ref_number").val(res[0].reference_no);
						$(".info_app_date").val(moment(res[0].application_date).format('L'));
						$(".info_app_type").val(res[0].description);

						var a = accounting.formatMoney(res[0].application_fee,"₱",2);
						var name = res[0].first_name + " " +  res[0].middle_name + " " + res[0].last_name;
	  					
						$(".info_app_fee").val(a);
						$(".info_app_name").val(name);
						$(".info_app_req").val(res[0].requirement_description);
						$(".appr_application_id").val(res[0].id);
						$(".rej_application_id").val(res[0].id);


						var html = ''
						var uploaded_doc = data.uploaded_docs;
						// console.log(data.uploaded_docs);
						// console.log(uploaded_doc.length);

						if(uploaded_doc.length == 0){
							html += "<p> No uploaded document </p>"
						}

						if (uploaded_doc.length != 0){
							for(var i = 0; i < uploaded_doc.length ; i++){

								if( uploaded_doc[i].uploaded_doc_ext != "pdf"){
									html += "<div class='col-md-2'>"
									html += "<p>" + uploaded_doc[i].requirement_description + "</p>";
									html += "<a target='_blank' href='";
									html += base_url+ "assets/img/applicant_documents/" + uploaded_doc[i].uploaded_doc + "'>";
									html += "<img class='img-fluid' width ='100' height ='90' src='";
									html += base_url+ "assets/img/applicant_documents/" + uploaded_doc[i].uploaded_doc;
									html += "'></a>";
									html += "</div>"
								}else{
									html += "<div class='col-md-2'>"
									html += "<p>" + uploaded_doc[i].requirement_description + "</p>";
									html += "<a target='_blank' href='";
									html += base_url+ "assets/img/applicant_documents/" + uploaded_doc[i].uploaded_doc + "'>";
									html += "<img class='img-fluid' width ='100' height ='90' src='";
									html += base_url+ "assets/images/pdf_icon.png";
									html += "'></a>";
									html += "</div>"
								}
							}
						}

						$('.uploaded_docs').html(html);

						if(res[0].rejection_reason == null){
							$('.rejection_reason_div').css('display', 'none')
	  					} else(
							$(".info_app_rej_reason").val(res[0].rejection_reason)
	  					)

						if(res[0].application_status == 1){
							$(".goToEditModalAccountsBtn").prop('disabled', 'disabled');
							$(".goToRejectModalApplicationBtn").prop('disabled', false);
							$('.rejection_reason_div').css('display', 'none');
						} else if(res[0].application_status == 2){
							$(".goToRejectModalApplicationBtn").prop('disabled', 'disabled');
							$(".goToEditModalAccountsBtn").prop('disabled', false);
							$(".info_app_rej_reason").val(res[0].rejection_reason);
							$('.rejection_reason_div').css('display', 'block');
						}
	  				}

	  			}
	  	});
	});

	$('#table-grid').delegate(".btnDelete","click", function(){
		var app_id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoTransactionApplicationUsingID',
	  		data:{'app_id':app_id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
	  					// $(".del_app_id").val(res[0].applicant_id);
	  					$(".reference_del").html('Reference No. ' + res[0].reference_no);
	  					$(".del_application_id").val(app_id);
	  					
	  				}
	  			}
	  		});
		$('#deleteAccountsModal').modal('show');

	});


	$('.deleteAccountBtn').click(function(e){
		e.preventDefault();

		var del_app_id = $(".del_application_id").val();
		console.log(del_app_id);

		$.ajax({
			type:'post',
			url:base_url+'Main/deleteAdminTransactionsApplications',
			data: {'del_app_id':del_app_id},
			success:function(data){
				if (data.success == 1) {
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
						hideAfter: 4000
					});
					dataTable.draw(); //refresh table
					$('#deleteAccountsModal').modal('toggle'); //close modal
				}else{
					$.toast({
					    heading: 'Error',
					    text: data.message,
					    icon: 'error',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#d9534f',
						textColor: 'white'        
					});
				}
			}
		});
	});



	$(".goToEditModalAccountsBtn").click(function(e){
		e.preventDefault();
			$('#viewAccountsModal').modal('toggle');
			$('#approveApplicationModal').modal('show'); 
	});

	$(".goToRejectModalApplicationBtn").click(function(e){
		e.preventDefault();
			$('#viewAccountsModal').modal('toggle');
			$('#rejectApplicationModal').modal('show'); 
		
	});

	$('.uploaded_images').click(function(e){
		e.preventDefault();
		$('#uploadedImagesModal').modal('show');
	})


	$("#approve_applicationinfo-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main/approveAdminTransactionsApplications',
			data:serial,
			success:function(data){
				if (data.success == 1) {
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
						hideAfter: 4000
					});
					dataTable.draw(); //refresh table
					$('#approveApplicationModal').modal('toggle'); //close modal
				}else{
					$.toast({
					    heading: 'Error',
					    text: data.message,
					    icon: 'error',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#d9534f',
						textColor: 'white'        
					});
				}
			}
		});
	});

	$("#reject_applicationinfo-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main/rejectAdminTransactionsApplications',
			data:serial,
			success:function(data){
				if (data.success == 1) {
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
						hideAfter: 4000
					});
					dataTable.draw(); //refresh table
					$('#rejectApplicationModal').modal('toggle'); //close modal
				}else{
					$.toast({
					    heading: 'Error',
					    text: data.message,
					    icon: 'error',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#d9534f',
						textColor: 'white'        
					});
				}
			}
		});
	});

});