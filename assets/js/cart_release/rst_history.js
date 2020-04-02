$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();\
	$('.rddatediv').show('slow');
	var searchtype = "rddatediv";

	// get the date today
	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	$('#calendar').hide();

	var toast = $('#toast').text();

	if (toast == "toast") {
		dataTable = $('#table-grid').DataTable({
			destroy: true,
			"serverSide": true,
			"ajax":{
				url:base_url+"Main_cart/get_rst_history", // json datasource
				type: "post",  // method  , by default get
				data:{'searchtype': 'rdnodiv', 'rdno': $('#rdnoflash').text()},
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
	}
	else {
		dataTable = $('#table-grid').DataTable({
			destroy: true,
			"serverSide": true,
			"ajax":{
				url:base_url+"Main_cart/get_rst_history", // json datasource
				type: "post",  // method  , by default get
				data:{'searchtype': searchtype, 'datefrom': date, 'dateto': date},
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
	}

	function reset_em_data() {
		data = [];	
		$("#r_date").val("");
		$("#em_franchisee").val("");
		$("#em_location").val("");
		$('#em_concept').val('none').change();
		$('#em_type').val('none').change();
		$('#em_size').val('none').change();
		$('#em_improvements').val('none').change();
		$('#em_mode').val('none').change();
		$("#em_notes").val("");
		$('#calendar').hide();
	}

	$("#rdsearchfilter").change(function() {
		var searchtype = $('#rdsearchfilter').val();

   		if(searchtype == "rddatediv") {
			$('.rddatediv').show('slow');  
			$('.rdnodiv').hide('slow');
			$('.rdfranchiseediv').hide('slow');
   		}
		else if(searchtype == "rdnodiv") {
			$('.rdnodiv').show('slow');
			$('.rddatediv').hide('slow');
			$('.rdfranchiseediv').hide('slow');
		}
		else if(searchtype == "rdfranchiseediv") {
			$('.rdfranchiseediv').show('slow');
			$('.rddatediv').hide('slow');
			$('.rdnodiv').hide('slow');
		}
	});

	$(".btnSearchRD").click(function(e){
		e.preventDefault();

		var datefrom;
		var dateto;
		var rdno;
		var franchisee;
		var searchtype = $('#rdsearchfilter').val();
		var checker = 0;

		if(searchtype == "none") {
			checker = 0;
		}
		else if(searchtype == "rddatediv") {
			if($("#datefrom").val() == "" || $("#dateto").val() == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please fill date range field.",
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
				datefrom = formatDate($("#datefrom").val());
				dateto = formatDate($("#dateto").val());
			}
		}
		else if(searchtype == "rdnodiv") {
			if($("#rdno").val() == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please input a RD Number.",
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
				rdno = $("#rdno").val();
			}
		}
		else if(searchtype == "rdfranchiseediv") {
			if($("#franchisee").val() == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select a franchisee.",
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
				franchisee = $("#franchisee").val();
			}
		}
		
		if(checker == 1) {
			dataTable = $('#table-grid').DataTable({
				destroy: true,
				"serverSide": true,
				"ajax":{
					url:base_url+"Main_cart/get_rst_history", // json datasource
					type: "post",  // method  , by default get
					data:{'searchtype': searchtype, 'datefrom': datefrom, 'dateto': dateto, 'rdno': rdno, 'franchisee': franchisee},
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
		var date1 = formatDate(date);

		if(date1 == "" || concept == "none" || type == "none" || size == "none" || improvements == "none" || mode == "none") {
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
							bgColor: '#5cb85c',
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

    $('#btnCalendar').click(function(e) {
    	if ($('#btnCalendar').text() == "View As Calendar") {
			$('#calendar').fullCalendar({
	            loading: function (isLoading) {
			        if (isLoading) {
			            $('#calendar').LoadingOverlay("show");
			        }
			        else {
			            $('#calendar').LoadingOverlay("hide");
			        }
			    },
				eventClick: function(event, jsEvent, view) {
					$('.m_franchisee').html(event.name);
					$('.m_location').html(event.location);
					$('.m_concept').html(event.concept);
					$('.m_type').html(event.type);
					$('.m_size').html(event.size);
					$('.m_notes').val(event.note);
					$('.m_mor').html(event.mor);
					$('.m_trandate').html(moment(event.start).format('YYYY/MM/DD'));
					$('.m_rdno').html(event.id);
					$('#viewModal').modal();
				},
				eventSources: [{
	            	color: '#54C96F',   
	        		textColor: '#000000',
	                events: function(start, end, timezone, callback) {
						$.ajax({
							url: base_url+'Main_cart/get_schedules',
							dataType: 'json',
							data: {
							// our hypothetical feed requires UNIX timestamps
								start: start.unix(),
								end: end.unix()
							},
							success: function(msg) {
								var events = msg.events;
								callback(events);
							}
						});
	         		}
	            }],
				eventLimit: true, // limits the number of schedule to show in a day
					views: {
						agenda: {
							eventLimit: 4 // only 4 shedule per day will be shown, the rest will be inside a popover
					}
				}
	    	});

		    $('#calendar').show();
			$('.table-responsive').hide('slow');
			$('.searchfilter').hide('slow');
			$('.rddatediv').hide('slow');
			$('.rdnodiv').hide('slow');
			$('.rdfranchiseediv').hide('slow');
			$('.btnSearchRD').hide();
			$('#btnCalendar').html('Exit Calendar View');
			$('#btnCalendar').removeClass('btn-primary');
			$('#btnCalendar').addClass('btn-danger');
		}
		else {
			$('.table-responsive').show('slow');
			$('.searchfilter').show('slow');
			$('.date-label').show('slow');
			$('.rddatediv').show('slow');
			$('.btnSearchRD').show();
			$('#btnCalendar').html('View As Calendar');
			$('#btnCalendar').removeClass('btn-danger');
			$('#btnCalendar').addClass('btn-primary');
			$('#calendar').hide();
		}
	});

	$('#table-grid').delegate(".btnReschedule", "click", function(){
		$(".rm_franchisee").html($(this).data('name') + " (" + $(this).data('branchname') + ")");
	  	$(".rm_rdno").val($(this).data('rdno'));
	  	$(".rm_date").val($(this).data('trandate'));
	});

	$('.rm_reschedBtn').click(function(e) {
		var rdno = $(".rm_rdno").val();
		var date = formatDate($("#rm_date").val());

		if(date == "") {
			$.toast({
			    heading: 'Note:',
			    text: "Please select a date.",
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
				'date' : date
			}

			$.ajax({
		  		url: base_url+"Main_cart/resched_release",
	            type: "post",
				data: {'data':data},
		  		beforeSend:function(data){
					$.LoadingOverlay("show");
				},
		  		success:function(data){
		  			if (data.success == 1) {
						$.toast({
						    heading: 'Success',
						    text: 'Release has been successfully saved.',
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
					 		dataTable.draw();
						},500);
		  			}
		  		}
	  		});
		}
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
