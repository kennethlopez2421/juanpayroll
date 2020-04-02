$(document).ready(function(){
    var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
    var token = $("#hdnToken").val();
    transactions = [];

	// 100118 - nick
	// searching process that can adapt the retaining of previous search if the user returns to this page

	// get the date today
	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

    function fillDatatable(supid, datefrom, dateto) {
        dataTable = $('#table-grid').DataTable({
            destroy: true,
            "serverSide": true,
			"order": [[ 2, "desc" ]],
			"columnDefs": [{ "orderable": false, "targets": [ 0 ], "className": "dt-center" }],
            beforeSend:function(data) {
              $.LoadingOverlay("show"); 
            },
            complete: function() {
              $.LoadingOverlay("hide"); 
            },
            "ajax":{
                url:base_url+"account/APV/accounts_payable_voucher_table", // json datasource
                type: "post",  // method  , by default get
                data: { 'supid' : supid, 'datefrom' : datefrom, 'dateto' : dateto },
                beforeSend:function(data) {
                    $.LoadingOverlay("show"); 
                },
                complete: function() {
                    $.LoadingOverlay("hide"); 
                },
                error: function() {  // error handling
                    $(".table-grid-error").html("");
                    $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            }
        });
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

	function formatDate(date) {
		var d = new Date(date),
			month = '' + (d.getMonth() + 1),
			day = '' + d.getDate(),
			year = d.getFullYear();

		if (month.length < 2) month = '0' + month;
		if (day.length < 2) day = '0' + day;

		return [year, month, day].join('-');
	}

	search = $("#hdnSearch").text();

	if (search != "") {
		$("#date1").val($("#hdnDatefrom").text());
		$("#date2").val($("#hdnDateto").text());
		$("#searchSelect").val($("#hdnSupid").text()).change();

		datefrom 	= formatDate($("#hdnDatefrom").text());
		dateto		= formatDate($("#hdnDateto").text());
		supid		= $("#hdnSupid").text();
	}
	else {
		datefrom 	= formatDate($("#date1").val());
		dateto		= formatDate($("#date2").val());
		supid		= $("#searchSelect").val();
	}
    
    fillDatatable(supid, datefrom, dateto);

    $("#btnSearch").on('click', function(){
		supid 		= $("#searchSelect").val();
		datefrom 	= formatDate($("#date1").val());
		dateto		= formatDate($("#date2").val());

		$('#hdnDatefrom').text(datefrom);
		$('#hdnDateto').text(dateto);
        $('#hdnSupid').text(supid);

        if (supid != "" && datefrom != "" && dateto != "") {
            fillDatatable(supid, datefrom, dateto);
        }
        else {
            toastMessage('Note', 'Please indicate date range and supplier', 'error', '#f0ad4e');
        }
    });

	$("#chkAll").click(function(e){
        $('input:checkbox').not(this).prop('checked', this.checked);

		if ($('input[name="chkPono"]:checked').length > 0) {
			$("#btnProceed").prop("disabled", false);
        }
		else {
			$("#btnProceed").prop("disabled", true);
        }
	});

	$("#table-grid").delegate( "#chkPono", "click", function() {
		if ($('input[name="chkPono"]:checked').length > 0) {
			$("#btnProceed").prop("disabled", false);
		}
		else {
			$("#btnProceed").prop("disabled", true);
		}

		if (($('input[name="chkPono"]:checked').length) == $('input[name="chkPono"]').length) {
			$("#chkAll").prop("checked", true);
		}
		else {
			$("#chkAll").prop("checked", false);
		}
	});

	$("#btnProceed").on("click", function(){
		$( ".chkPono:checkbox:checked" ).each(function( index ) {
            entry = {
                pono: $( this ).data("pono"),
                rcvno: $( this ).data("rcvno"),
                rcvdate: $( this ).data("rcvdate"),
                total: $( this ).data("total"),
                supid: $( this ).data("supid")
            }

            transactions.push(entry);
		});

		$("#batchApproveConfirmationModal").modal("toggle");
		$("#supplier").text($("#searchSelect option:selected").text());
		$("#batchApproveConfirmationModal").modal("toggle");
	});
	
	$("#btnConfirmBatchApprove").on("click", function(){
        if (transactions == "") {
            toastMessage('Note', 'Please select atleast one transaction', 'error', '#f0ad4e');
        }
        else {
            $.ajax({
                type:'post',
                url:base_url+'account/APV/generateAPV',
                data:{ "supid" : $('#hdnSupid').text(), "transactions" : transactions },
                beforeSend:function(data) {
                    $.LoadingOverlay("show"); 
                },
                success:function(data) {
                    $.LoadingOverlay("hide");
   
                    if (data.success == 1) {
                        toastMessage('Success', data.message, 'success', '#5cb85c');

                        supid       = $('#hdnSupid').text();
                        datefrom    = $('#hdnDatefrom').text();
                        dateto      = $('#hdnDateto').text();
                        fillDatatable(supid, datefrom, dateto);
                        $("#btnProceed").attr("disabled", true);
                        $("#chkAll").prop("checked", false);
						$("#batchApproveConfirmationModal").modal("hide");
                    }
                    else {
                        toastMessage('Note', data.message, 'error', '#f0ad4e');
                   }
                }
            });
        }
	});

	// Storing session data for ease of navigation after clicking PO Number
	$("#table-grid").delegate( "#btnPono", "click", function() {
		// for url
		url_pono = $(this).data("pono");
		supid = $(this).data("supid");

		// get search variables
		supid = $('#hdnSupid').text();
		datefrom = $('#hdnDatefrom').text();
		dateto = $('#hdnDateto').text();

		$.ajax({
			type: 'post',
			url: base_url+'account/APV/storeSearchVariables',
			data:{'search': "APV|search", 'datefrom': datefrom, 'dateto': dateto, 'supid': supid},
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			success:function(data) {
				window.open(base_url+"account/APV/apv_rcvno/" + token + "/" + url_pono, '_self');
				$.LoadingOverlay("hide");
			}
		});
	});

    // end nick      
});