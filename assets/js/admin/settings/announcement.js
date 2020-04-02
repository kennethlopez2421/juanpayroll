$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$('.datepicker2').datepicker({
		format: 'yyyy/mm/dd',
    	startDate: '0',
	});

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"ajax":{
			url :base_url+"Main/settings_announcement_table", // json datasource
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	
	// $('.search-input-text').on('keyup click', function(){   // for text boxes
	// 	var i =$(this).attr('data-column');  // getting column index
	// 	var v =$(this).val();  // getting search input value
	// 	dataTable.columns(i).search(v).draw();
	// });

	// $('.search-input-select').on('change', function(){   // for select box
	// 	var i =$(this).attr('data-column');  
	// 	var v =$(this).val();  
	// 	dataTable.columns(i).search(v).draw();
	// });


	// $('.searchPosition').change(function(){
	// 	if ($(this).val() == "") {
	// 		$('.searchFullname').prop("disabled", true);
	// 		$('.searchContact').prop("disabled", true);
	// 		$('.searchEmail').prop("disabled", true);
	// 	}else{
	// 		$('.searchFullname').prop("disabled", false);
	// 		$('.searchContact').prop("disabled", false);
	// 		$('.searchEmail').prop("disabled", false);
	// 	}
	// });

	$(".saveBtnAnnouncement").click(function(e){
		
		e.preventDefault();

		var thiss = $("#add_announcement-form");

		var serial = thiss.serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main/save_announcement',
			data: serial,
			success:function(data){
				if (data.success == 1) {
					$(thiss).find('input').val(""); // clean fields
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
					}),
					dataTable.draw(); //refresh table
					$('#addAnnouncementModal').modal('toggle');
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
			}
		});
	});
	
	$('#table-grid').delegate(".btnDelete","click", function(){

		var announcement_id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoAnnouncementUsingID',
	  		data:{'announcement_id': announcement_id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {

	  				if(res[0].position_id == 1) { //Admin
						$(".del_announcement_id").val(res[0].announcement_id);

	  				} else if(res[0].position_id == 3){ //Branch Admin
						$(".del_announcement_id").val(res[0].announcement_id);
	  				}
	  			}
	  		}
		});

		$('#deleteAnnouncementModal').modal('show');

	});

	$('#table-grid').delegate(".btnView", "click", function(){
		$('#viewAnnouncementModal').modal('show');
	});

	$('#table-grid').delegate(".btnView", "click", function(){

	  	var announcement_id = $(this).data('value');

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main/getInfoAnnouncementUsingID',
	  		data:{'announcement_id': announcement_id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {

	  				if(res[0].position_id == 1) { //Admin
	  					$(".a_title").html(res[0].subject);
		  				$(".a_posted_by").html("Posted by:  "+ res[0].admin_first_name + " " +res[0].admin_middle_name +" "+res[0].admin_last_name + " (Admin)");
						$(".a_posted_on").html("Posted on:  "+ res[0].posted_on);
						$(".a_posted_until").html("Posted until:  "+ res[0].display_until);
						$(".a_content").html(res[0].content);

						$(".a_etitle").val(res[0].subject);
		  				$(".a_eposted_by").val(res[0].admin_first_name + " " +res[0].admin_middle_name +" "+res[0].admin_last_name + " (Admin)");
						$(".a_eposted_on").val(res[0].posted_on);
						$(".a_eposted_until").val(res[0].display_until);
						$(".a_econtent").val(res[0].content);

						$(".announcement_id").val(res[0].announcement_id);
						$(".del_announcement_id").val(res[0].announcement_id);

	  				} else if(res[0].position_id == 3){ //Branch Admin

	  					$(".a_title").html(res[0].subject);
		  				$(".a_posted_by").html("Posted by:  "+ res[0].branch_admin_first_name + " " +res[0].branch_admin_middle_name +" "+res[0].branch_admin_last_name + " (Branch Admin)");
						$(".a_posted_on").html("Posted on:  "+ res[0].posted_on);
						$(".a_posted_until").html("Posted until:  "+ res[0].display_until);
						$(".a_content").html(res[0].content);

						$(".a_etitle").val(res[0].subject);
		  				$(".a_eposted_by").val(res[0].admin_first_name + " " +res[0].admin_middle_name +" "+res[0].admin_last_name);
						$(".a_eposted_on").val(res[0].posted_on);
						$(".a_eposted_until").val(res[0].display_until);
						$(".a_econtent").val(res[0].content);

						$(".announcement_id").val(res[0].announcement_id);
						$(".del_announcement_id").val(res[0].announcement_id);
	  				}
	  			}
	  		}
		});
	});


	$(".goToEditModalAnnouncementBtn").click(function(e){
		e.preventDefault();
			$('#viewAnnouncementModal').modal('toggle');
			$('#editAnnouncementModal').modal('show'); 
	});

	$("#edit_announcement-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main/edit_announcement',
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
					$('#editAnnouncementModal').modal('toggle'); //close modal
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

	$('.deleteAnnouncementBtn').click(function(e){
		e.preventDefault();

		var del_announcement_id = $(".del_announcement_id").val();
	
		$.ajax({
			type:'post',
			url:base_url+'Main/deleteAnnouncement',
			data:{'del_announcement_id':del_announcement_id},
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
					$('#deleteAnnouncementModal').modal('toggle'); //close modal
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

	$('.btnClickAddAnnouncement').click(function(){
		$('.info_position').val('').trigger('change');
	});
	
});