$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var pono = $('.pono').text();
	var apvno = $('.apvno').text();

	remove = [];
	totalamt = 0;

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

	resetData = function(){
		remove = [];
	}

	dataTable = $('#table-grid2').DataTable({
		"serverSide": true,
		"ajax":{
			url:base_url+"account/APV_list/table_apvpo_edit", // json datasource
			type: "post",  // method  , by default get
			data:{"apvno": apvno},
			beforeSend:function() {
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

	$('#table-grid2').delegate(".btnViewRcv", "click", function(){
 		var pono = $(this).data('pono');
 		var rcvno = $(this).data('value');
 		var printed = $(this).data('printed');
 		var suprefno = $(this).data('suprefno');
 		var potrandate = $(this).data('potrandate');
 		var itemtrandate = $(this).data('itemtrandate');
 		$('.m_pono').text(pono);
 		$('.m_rcvno').text(rcvno);
 		$('.m_suprefno').text(suprefno);
 		$('.m_potrandate').text(potrandate);
 		$('.m_itemtrandate').text(itemtrandate);

 		if (printed > 0){
 			$("#printedText").show();
 			$(".printBtn").hide();
 		}
 		else {
 			$("#printedText").hide();
 			$(".printBtn").show();
 		}

		dataTable2 = $('#table-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax":{
				url:base_url+"Main_account/get_rcvno_edit_breakdown", // json datasource
				type: "post",  // method  , by default get
				data:{"apvno": apvno, "rcvno": rcvno},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});

		$('#poReceiveModal').modal('show');
		dataTable2.destroy();
	});

	$("#table-grid2").delegate( "#chkRcvno", "click", function() {
		if ($('input[name="chkRcvno"]:checked').length > 0) {
			$(".btnSave").prop("disabled", false);
		}
		else {
			$(".btnSave").prop("disabled", true);
		}
	});

	$('#apvpo_edit_form').delegate(".btnSave", "click", function(){
		remove = [];
		totalamt = 0;
		$("input.chkRcvno:not(:checked)").each (function (index) {
            entry = {
                rcvno: $( this ).data("rcvno")
            }

			remove.push(entry);
		});

		$( ".chkRcvno:checkbox:checked" ).each(function( index ) {
			totalamt += $( this ).data("amount");
		});
		
		if ($('.chkRcvno:checked').size() <= 0) {
			toastMessage('Note', 'Please select atleast one PO Receive record', 'error', '#f0ad4e');
		}
		else {
			if (remove == "") {
				toastMessage('Note', 'No changes to be saved', 'error', '#f0ad4e');
			}
			else {
				$('#confirmSaveModal').modal();
			}
		}
	});

	$('.approveApvBtn').click(function(e){
		var data = {
			'remove': remove,
			'apvno': apvno,
			'totalamt': totalamt
		}

		$.ajax({
		  	type: 'post',
		  	url: base_url+'account/APV_list/save_apvpo_edit',
		  	data:{'data':data},
		  	success:function(data){
		  		data = JSON.parse(data);

		  		if (data.valid==false) {
					toastMessage('Note', data.message, 'error', '#f0ad4e');
		  		}
		  		else {
					toastMessage('Success', data.message, 'success', '#5cb85c');

		  			dataTable.draw();
					resetData();
		  		}
		  	},
		  	error: function(error){
		  		data = JSON.parse(error);
		  	}
		});

		$('#confirmSaveModal').modal('hide');
	});
});