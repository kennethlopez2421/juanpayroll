$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var poretno = $("#poretno_id").data("poretno");
	var idno = $(".idno").val();

	var dataTable = $('#table-grid').DataTable({
		"serverSide": true,
		"destroy":true,
		"bDeferRender": true,
		"ajax":{
			url :base_url+"purchase/PR_historyview/table_poreturn_view", // json datasource
			type: "post",  // method  , by default get
			data: {"poretno" : poretno},
			beforeSend: function() {
				$.LoadingOverlay("show");
			},
			complete: function(data) {
				$.LoadingOverlay("hide"); 
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable.destroy();

	var dataTable1 = $('#table-poretno').DataTable({
		"serverSide": true,
		"destroy":true,
		"ajax": {
			url :base_url+"purchase/PR_historyview/tbl_poreturn_view", // json datasource
			type: "post",  // method  , by default get
			data: {"poretno" : poretno},
			beforeSend: function() {
				$.LoadingOverlay("show");
			},
			complete: function(data) {
				$.LoadingOverlay("hide"); 
			},
			error: function() {  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	dataTable1.destroy();

	$('.printBtn').click(function(e){
		e.preventDefault();
		var currUrl = window.location.href;

		currUrl = currUrl.replace("poreturn_view", "poreturn_print");
		window.open (currUrl, '_blank');
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