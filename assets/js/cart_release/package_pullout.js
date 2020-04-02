$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var token = $('.token').text();

	// get the date today
	var d = new Date();
	var date_today = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	var data = [];
	entries = [];

	$('.interface2').hide();

	function reset() {
		data = [];
		entries = [];
		$("#pp_date").val("");
		$("#pp_franchisee").val('none').change();
		$("#pp_location").val("");
		$('#pp_concept').val("");;
		$('#pp_type').val('none').change();
		$('#pp_size').val('none').change();
		$('#pp_rdhistory').val("");
		$("#pp_notes").val("");
		$(".save").prop("disabled",false);
		$("pp_location").prop('disabled', true);
		$('.interface1').show();
		$('.interface2').hide();
	}

	function tofixed(x){
		return numberWithCommas(parseFloat(x).toFixed(2));
	}

	function numberWithCommas(x){
	  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	refreshTable = function(){
		var tableBody = "";

		for(var a = 0; a<entries.length; a++){
			var tableRow = 
				"<tr>"+
                    "<td>"+entries[a].itemid+"</td>"+
                    "<td>"+entries[a].itemname+"</td>"+
                    "<td>"+tofixed(entries[a].qty)+"</td>"+
                    "<td>"+entries[a].unit+"</td>"+
                    "<td></td>"+
                    "<td>"+
                    	"<button class='btn btn-sm btn-danger deletebtn' id='"+a+"'>Delete</button>"+
                    "</td>"+
                "</tr>";
		    tableBody+= tableRow;
		}
		
		$('#t_body').html(tableBody);
		set_handler();
	}

	set_handler = function(){
		$('.deletebtn').click(function(e){
			entries.splice(e.currentTarget.id, 1);
			refreshTable();
		});
	}

	$('.proceed').click(function(e) {
		var date = $("#pp_date").val();
		var franchisee = $("#pp_franchisee").val();
		var location = $("#pp_location").val();
		var concept = $("#pp_concept").val();
		var type = $("#pp_type").val();
		var size = $("#pp_size").val();
		var rdhistory = $("#pp_rdhistory").val();
		var date1 = formatDate(date);

		if(date1 == "" || franchisee == "none" || location == "" || concept == "" || type == "none" || size == "none") {
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
			if (date1 >= formatDate(date_today)) {
				var franchisee = $('#pp_franchisee').val();

			    $.ajax({
			  		url: base_url+"Main_cart/get_pullout_details",
		            type: 'post',
					data: {'franchisee':franchisee},
			  		success:function(data){
			  			$('#franchisee').text(data.lname + ", " + data.fname);
			  			$('#address').text($("#pp_location").val());
			  			$('#concept').text($('#pp_concept').val());
			  			$('#contact').text(data.conno);
			  			$('#type').text($('#pp_type').val());
			  			$('#size').text($('#pp_size').val());
			  			$('#mode').text(data.mor);
			  			$('#date').text($('#pp_date').val());
			  		}
		  		});

				$('.interface1').hide();
				$('.interface2').show();
			}
			else {
				$.toast({
				    heading: 'Note:',
				    text: "Date cannot be earlier than today.",
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
		}
	});

	$('#confirmForm').submit(function(event) {
		event.preventDefault();

		var form = $(this);

		var data = {
			'date' : formatDate($("#pp_date").val()),
			'franchisee' : $("#pp_franchisee").val(),
			'location' : $("#pp_location").val(),
			'concept' : $("#pp_concept").val(),
			'type' : $("#pp_type").val(),
			'size' : $("#pp_size").val(),
			'rdhistory' : $("#pp_rdhistory").val(),
			'notes' : $("#pp_notes").val()
		}

		$.ajax({
	  		url: form.attr('action'),
            type: form.attr('method'),
			data: {'data':data},
	  		beforeSend:function(data){
				$.LoadingOverlay("show");
				$(".save").prop("disabled",true);
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
						$('#confirmModal').modal('hide');
					},500);

				 	reset();
	  			}
	  		}
  		});
	});

	$("#pp_franchisee").change(function () {
	    var franchisee = $('#pp_franchisee').val();

	    $.ajax({
	  		url: base_url+"Main_cart/get_branch_location2",
            type: 'post',
			data: {'franchisee':franchisee},
	  		success:function(data){
	  			if (data.address == "") {
	  				$.toast({
					    heading: 'Note',
					    text: 'Franchisee have no address. You may manually enter an address.',
					    icon: 'error',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: 'orange',
						textColor: 'white'  
					});
					$("#pp_location").prop('disabled', false);
					$("#pp_location").val("");
	  			}
	  			else {
	  				$('#pp_location').val(data.address);
	  				$('#pp_concept').val(data.concept);
	  				$("#pp_location").prop('disabled', true);
	  			}
	  		}
  		});
	});

	$("#am_item").change(function () {
	    var itemid = $('#am_item').val();

	    $.ajax({
	  		url: base_url+"Main_cart/get_item_details",
            type: 'post',
			data: {'itemid':itemid},
	  		success:function(data){
  				$('#am_unit').val(data.unit);
	  		}
  		});
	});

	$('#addRowForm').submit(function(event){
		event.preventDefault();

		var form = $(this);

		itemid = $('#am_item').val();
		unit = $('#am_unit').val();
		qty = $('#am_qty').val();

		if(itemid != "" || qty != "" || unit != "") {
			data = {
				itemid: itemid,
				itemname: $("#am_item option:selected").text(),
				qty: qty,
				unit: unit
			}

			entries.push(data);

			refreshTable();

			$('#addRowForm')[0].reset();
			$('#addRowModal').modal('hide');
		}
		else {
			$.toast({
			    heading: 'Warning',
			    text: 'Please fill out all required fields',
			    icon: 'error',
			    loader: false,  
			    stack: false,
			    position: 'top-center', 
				allowToastClose: false,
				bgColor: '#ffc107',
				textColor: 'white'  
			});
		}
	});

	$('.savePulloutBtn').click(function(event){
		if (entries == "") {
			$.toast({
			    heading: 'Warning',
			    text: 'Atleast one item is needed to save package pullout.',
			    icon: 'error',
			    loader: false,  
			    stack: false,
			    position: 'top-center', 
				allowToastClose: false,
				bgColor: '#ffc107',
				textColor: 'white'  
			});
		}
		else {
			var data = {
				'entries': entries,
				'ppfno': $('#ppfno').text(),
				'date': formatDate($("#pp_date").val()),
				'franchisee': $("#pp_franchisee").val(),
				'location': $("#pp_location").val(),
				'concept': $("#pp_concept").val(),
				'type': $("#pp_type").val(),
				'size': $("#pp_size").val(),
				'rdhistory': $("#pp_rdhistory").val(),
				'notes': $("#pp_notes").val()
			}

			$.ajax({
			  	type: 'post',
			  	url: base_url+'Main_cart/save_package_pullout',
			  	data:{'data':data},
			  	success:function(data){
			  			
			  		data = JSON.parse(data);

			  		if(data.valid==false){
			  			$.toast({
						    heading: 'Warning',
						    text: data.message,
						    icon: 'error',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#ffc107',
							textColor: 'white'  
						});
			  		}
			  		else{
						window.location.replace(base_url+'Main_cart/ppt_history/'+token);
			  		}
			  	},
			  	error: function(error){
			  		data = JSON.parse(error);
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
