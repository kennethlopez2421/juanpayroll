$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();\
	$('.piconceptdiv').show('slow');
	var concept = "none";

	// get the date today
	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	dataTable = $('#table-grid').DataTable({
		destroy: true,
		"serverSide": true,
		"ajax":{
			url:base_url+"Main_cart/get_package_inclusion", // json datasource
			type: "post",  // method  , by default get
			data:{'concept': concept, 'datefrom': date, 'dateto': date},
			beforeSend:function(data){
				$("#table-grid").LoadingOverlay("show");	
			},
			complete: function()
			{
				setTimeout(function(){
					$("#table-grid").LoadingOverlay("hide");
				},500); 
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	$(".btnSearchPI").click(function(e){
		e.preventDefault();

		var datefrom;
		var dateto;
		var searchtype = $('#pisearchfilter').val();
		var checker = 0;

		if(searchtype == "none") {
			checker = 0;
		}
		else {
			if($("#concept").val() == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select a concept.",
				    icon: 'info',
				    loader: false,   
				    stack: false,
				    position: 'top-center',  
				    bgColor: '#FFA500',
					textColor: 'white',
					allowToastClose: false,
					hideAfter: 3000          
				});
			}
			else {
				checker = 1;
				concept = $("#concept").val();
			}
		}
		
		if(checker == 1) {
			dataTable = $('#table-grid').DataTable({
				destroy: true,
				"serverSide": true,
				"ajax":{
					url:base_url+"Main_cart/get_package_inclusion", // json datasource
					type: "post",  // method  , by default get
					data:{'concept': concept, 'datefrom': date, 'dateto': date},
					beforeSend:function(data) {
						$("#table-grid").LoadingOverlay("show"); 
					},
					complete: function() {
						$("#table-grid").LoadingOverlay("hide"); 
					},
					error: function(){  // error handling
						$(".table-grid-error").html("");
						$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
						$("#table-grid_processing").css("display","none");
					}
				}
			});
		}
	});

	$('#table-grid').delegate(".btnView", "click", function(){
	  	$(".m_rdno").html($(this).data('rdno'));
	  	$(".m_trandate").html($(this).data('trandate'));
	  	$(".m_franchisee").html($(this).data('name'));
	  	$(".m_location").html($(this).data('location'));
	  	$(".m_concept").html($(this).data('concept'));
	  	$(".m_type").html($(this).data('type'));
	  	$(".m_size").html($(this).data('size'));
	  	$(".m_mor").html($(this).data('mor'));
	  	$(".m_notes").html($(this).data('note'));
	});

	$('#table-grid').delegate(".btnEdit", "click", function(){
	  	$(".em_rdno").html($(this).data('rdno'));
	  	$(".em_trandate").html($(this).data('trandate'));
	  	$("#em_date").val($(this).data('trandate'));
	  	$("#em_franchisee").val($(this).data('name'));
	  	$("#em_franchiseeid").val($(this).data('franchiseeid'));
	  	$("#em_location").val($(this).data('location'));
	  	$("#em_concept").val($(this).data('concept')).change();
	  	$("#em_type").val($(this).data('type')).change();
	  	$("#em_size").val($(this).data('size')).change();
	  	$("#em_improvements").val($(this).data('improvements')).change();
	  	$("#em_mode").val($(this).data('mor')).change();
	  	$("#em_notes").val($(this).data('note'));
	});

	$('.em_saveBtn').click(function(e) {
		var rdno = $(".em_rdno").text();
		var date = $("#em_date").val();
		var concept = $("#em_concept").val();
		var type = $("#em_type").val();
		var size = $("#em_size").val();
		var improvements = $("#em_improvements").val();
		var mode = $("#em_mode").val();
		var notes = $("#em_notes").val();
		var date1 = formatDate(date);

		if(date1 == "" || concept == "none" || type == "none" || size == "none" || improvements == "none" || mode == "none" || notes == "") {
			$.toast({
			    heading: 'Note:',
			    text: "Please fill out all required fields.",
			    icon: 'error',
			    loader: false,   
			    stack: false,
			    position: 'top-center',  
			    bgColor: '#FFA500',
				textColor: 'white',
				allowToastClose: false,
				hideAfter: 3000          
			});
		}
		else {
			var data = {
				'rdno' : rdno,
				'date' : date1,
				'concept' : concept,
				'type' : type,
				'size' : size,
				'improvements' : improvements,
				'mode' : mode,
				'notes' : notes
			}
			console.log(data);

			$.ajax({
		  		url: base_url+"Main_cart/update_release_details",
	            type: "post",
				data: {'data':data},
		  		beforeSend:function(data){
					$.LoadingOverlay("show");
				},
		  		success:function(data){
		  			if (data.success == 1) {
						$.toast({
						    heading: 'Success',
						    text: 'You have successfully saved the record.',
						    icon: 'success',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: 'yellowgreen',
							textColor: 'white'  
						});
					 	setTimeout(function(){
							$.LoadingOverlay("hide");
					 		reset_em_data();
						},500);
		  			}
		  		}
	  		});
		}
	});

	$('#table-grid').delegate(".btnDelete", "click", function(){
		$(".dm_pkgname").html($(this).data('name'));
	  	$(".dm_pkgid").val($(this).data('id'));
	});

	$('.dm_deleteBtn').click(function(e) {
		var pkgid = $(".dm_pkgid").val();

		$.ajax({
	  		url: base_url+"Main_cart/delete_package",
            type: "post",
			data: {'pkgid':pkgid},
	  		beforeSend:function(data){
				$.LoadingOverlay("show");
			},
	  		success:function(data){
	  			if (data.success == 1) {
					$.toast({
					    heading: 'Success',
					    text: 'You have successfully deleted the package.',
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: 'yellowgreen',
						textColor: 'white'  
					});
				 	setTimeout(function(){
						$.LoadingOverlay("hide");
						$('#deleteModal').modal("hide");
				 		dataTable.draw();
					},500);
	  			}
	  		}
  		});
	});

});

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}

function isNumberKeyOnly(evt) {    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}
