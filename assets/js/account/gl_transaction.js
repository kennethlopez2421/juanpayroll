$(document).ready(function(){

	base_url = $("body").data('base_url');

	function tofixed(x){
		return numberWithCommas(parseFloat(x).toFixed(2));
	}

	function numberWithCommas(x){
	  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	GLTransEntries = [];
	TransTotal = 0;

	$('#total_label').html(tofixed(TransTotal));

	resetData = function(){
		GLTransEntries = [];
		TransTotal = 0;

		$('#total_label').html(tofixed(TransTotal));

		$('#t_body').html('');

		$('#f_date').val('');
		$('#f_type').val('');
		$('#f_supplier').val('');
		$('#f_reference').val('');
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

	var table = $('#table-grid').DataTable({ //declaring of table
        columnDefs: [{ targets: [5], visible: true, orderable: false, sClass: 'text-center'}]
	});//data table
	
	// function for binding and refreshing datatable data
    function refreshTable(){
    	table.clear();
    	for(var a = 0; a < GLTransEntries.length; a++){
			selectedDataarray = [
                GLTransEntries[a].date,
                GLTransEntries[a].description,
                accounting.formatMoney(GLTransEntries[a].amount),
                GLTransEntries[a].deb_account,
                GLTransEntries[a].cre_account,
				//"<center><button class='btn btn-danger btnDelete btnTable' data-value='" + a + "'><i class='fa fa-trash-o'></i> Delete</button></center>",
				"<button class='btn btn-sm btn-danger deletebtn' id='"+a+"'><i class='fa fa-trash-o'></i> Delete</button>"
            ];// adding selected data to array 

        	table.row.add(selectedDataarray);   
		}        
		table.draw();
		set_handler();
	}

	$( "#t_debit_account" ).change(function() {
		$("option", $( "#t_credit_account" )).attr("disabled",false);
		$('#t_credit_account option').filter(function () { return $(this).html() == $("#t_debit_account option:selected").text(); }).attr("disabled",true);
	});

	$( "#t_credit_account" ).change(function() {
		$("option", $( "#t_debit_account" )).attr("disabled",false);
		$('#t_debit_account option').filter(function () { return $(this).html() ==$("#t_credit_account option:selected").text(); }).attr("disabled",true);
	});

	$('#add_entry_form').submit(function(event){
		event.preventDefault();

		var valid = true;

		if($('#t_date').val()==""){
			valid = false;
		}
		if($('#t_debit_account').val()==""){
			valid = false;
		}
		if($('#t_credit_account').val()==""){
			valid = false;
		}
		if($('#t_description').val()==""){
			valid = false;
		}
		if($('#t_amount').val()==""){
			valid = false;
		}

		if(valid==true){
			var entry = {
				date: $('#t_date').val() ,
				description: $('#t_description').val() ,
				amount: $('#t_amount').val() ,
				deb_val: $('#t_debit_account').val().toString() ,
				cre_val: $('#t_credit_account').val().toString() ,
				deb_account: $("#t_debit_account option:selected").text() ,
				cre_account: $("#t_credit_account option:selected").text()
			}

			GLTransEntries.push(entry);


			refreshTable();

			TransTotal = TransTotal+parseFloat($('#t_amount').val());

			$('#total_label').html(tofixed(TransTotal));

			$('#add_entry_form')[0].reset();
			$('#addItemModal').modal('hide');
		}
		else {
			$.toast({
			    heading: 'Note',
			    text: 'Please fill out all required fields',
			    icon: 'info',
			    loader: false,  
			    stack: false,
			    position: 'top-center', 
				allowToastClose: false,
				bgColor: '#FFA500',
				textColor: 'white'  
			});
		}
	});

	set_handler = function(){
		$('.deletebtn').click(function(e){

			TransTotal = TransTotal-parseFloat(GLTransEntries[e.currentTarget.id].amount);
			$('#total_label').html(tofixed(TransTotal));

			GLTransEntries.splice(e.currentTarget.id, 1);
			refreshTable();
		});
	}

	$('#submitgltransbtn').click(function(event){
		if (GLTransEntries != "") {
			$("#confirmationModal").modal("toggle");
		}
		else {
			toastMessage('Note', 'No GL Transaction/s to save.', 'error', '#f0ad4e');
		}
	});

	$('#btnConfirm').click(function(){
		var data = {
			'GLTransEntries': GLTransEntries,
			'TransTotal': TransTotal
		}

		$.ajax({
		  	type: 'post',
		  	url: base_url+'account/GL/save_gl_trans',
		  	data:{'data':data},
		  	beforeSend:function(data){
				$.LoadingOverlay("show"); 
			},
			complete: function(data)
			{
				$.LoadingOverlay("hide"); 
			},
		  	success:function(data){
		  		data = JSON.parse(data);

		  		if(data.valid == false) {
		  			$.toast({
					    heading: 'Note',
					    text: data.message,
					    icon: 'info',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: '#FFA500',
						textColor: 'white'  
					});
		  		}
		  		else{
		  			$.toast({
					    heading: 'Success',
					    text: data.message,
					    icon: 'success',
					    loader: false,  
					    stack: false,
					    position: 'top-center', 
						allowToastClose: false,
						bgColor: '#5cb85c',
						textColor: 'white'  
					});

					resetData();
					refreshTable();
					$("#confirmationModal").modal("hide");
		  		}
		  	},
		  	error: function(error){
		  		data = JSON.parse(error);
		  	}
		});
	});
});

function isNumberKeyOnly(evt) {
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
	return true;
}