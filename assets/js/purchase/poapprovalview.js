$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang
    var token = $("#hdnToken").val();
    var pono = $("#pono").data("pono");
    var nextsupid = $("#pono").data("nextsupid");
    var nextpono = $("#pono").data("nextpono");
    
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
    
    $(".approvePurchaseOrder").click(function(){
        if (pono != "") {
            $("#approvePurchaseModal").modal("toggle");
        }
    });

    $(".approvePurchaseOrder").click(function(){
        $.ajax({
            type:'post',
            url: base_url+'purchase/PO_approvalview/approvePurchaseOrder',
            data:{"pono": pono},
            beforeSend: function() {
                $.LoadingOverlay("show");
            },
            complete: function(data) {
                $.LoadingOverlay("hide"); 
            },
            success:function(data) {
                if (data.success == 1) {
                    toastMessage('Success', data.message, 'success', '#5cb85c');
                    if (nextpono != ""){
                        window.open(base_url + "purchase/PO_approvalview/poapprovalview/" + token + "/" + nextpono + "/" + nextsupid, '_self');
                    }
                    else {
                        window.open(base_url + "Main_purchase/purchase_summary/" + token, '_self');
                    }
                }
                else if (data.success == 0) {
                    toastMessage('Note', data.message, 'error', '#f0ad4e');
                }
            }
       });
    });

    dataTable = $('#table-grid').DataTable({
        "serverSide": true,
        "order": [[ 1, "desc" ]],
        "destroy": true,
        "ajax":{
            url :base_url+"purchase/PO_approvalview/table_poapprovalview", // json datasource
            type: "post",
            data:{'pono': pono},
            beforeSend:function(data) {
                $.LoadingOverlay("show"); 
            },
            error: function() {  // error handling
                $(".table-grid-error").html("");
                $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                $("#table-grid_processing").css("display","none");
            },
            complete: function(data) {
                $.LoadingOverlay("hide"); 
            }
        }
    });

});