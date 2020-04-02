$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var pono = $("#pono_id_sec").data("pono");
	var suppid = $(".idno").val();
	var token = $(".token").val();

	var dataTable = $('#table-grid').DataTable({
		"serverSide": true,
		"destroy":true,
		"bDeferRender": true,
		"ajax":{
			url :base_url+"purchase/PO_historypoview/table_purchaseorder_view", // json datasource
			type: "post",  // method  , by default get
			data: {"pono" : pono},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			complete: function() {
				$.LoadingOverlay("hide"); 
			},
			error: function() {  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable.destroy();

	$('.printBtn').click(function(e){

		var currUrl = window.location.href;

		currUrl = currUrl.replace("purchaseorder_view", "purchaseorder_print");
		window.open (currUrl, '_blank');

		//$('.printBtn').attr("disabled","true");
		$('.printBtn').attr("title","This document has already been printed.");
	});

	$('.sendEmailto').click(function(e){
	//$('#encode_form').delegate(".sendEmailto","click", function(e) {
		e.preventDefault();
		var serial = $(this).serialize();
		var email = $(".email").val();
			//var currUrl = window.location.href;

			//currUrl = currUrl.replace("purchaseorder_view", "purchaseorder_
			$.ajax({
				type: 'post',
				url: base_url+'purchase/PO_historypoview/purchaseorder_print_foremail',
				data: {'email':email, 'pono':pono,'email':email,'suppid':suppid, 'token':token},
				beforeSend:function(data) {
				$.LoadingOverlay("show"); 
				},
				complete: function() {
					$.LoadingOverlay("hide"); 
				},
				success:function(data) {				
					if(data.success == 1) {
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
							hideAfter: 10000
						});
						$('#sendEmail').modal('toggle'); //close modal
					}
					else {
						$.toast({
						    heading: 'Note',
						    text: data.message,
						    icon: 'error',
						    loader: false,   
						    stack: false,
						    position: 'top-center',  
						    bgColor: '#f0ad4e',
							textColor: 'white'        
						});
						
					}
					
					$.LoadingOverlay("hide"); 
				}
			});

		
	});
});

function formatMoney(n,c, d, t) {
    c = isNaN(c = Math.abs(c)) ? 2 : c;
    d = d == undefined ? "." : d;
    t = t == undefined ? "," : t; 
    s = n < 0 ? "-" : "";
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "";
    j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}