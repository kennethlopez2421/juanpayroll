$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var checkno = $("#checkno_id").data("checkno_id");
	var idno = $(".idno").val();

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"destroy":true,
		"bDeferRender": true,
		"ajax":{
			url :base_url+"Main_purchase/table_checkreference_view", // json datasource
			type: "post",  // method  , by default get
			data: {"checkno" : checkno},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable.destroy();


	var dataTable1 = $('#table-checkno').DataTable({
		"processing": true,
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"Main_purchase/tbl_check_view", // json datasource
			type: "post",  // method  , by default get
			data: {"checkno" : checkno},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		},
	});

	dataTable1.destroy();
	
	$('.search-input-text').on('keyup click', function(){   // for text boxes
		var i =$(this).attr('data-column');  // getting column index
		var v =$(this).val();  // getting search input value
		dataTable.columns(i).search(v).draw();
	});

	$('.search-input-select').on('change', function(){   // for select box
		var i =$(this).attr('data-column');  
		var v =$(this).val();  
		dataTable.columns(i).search(v).draw();
	});

$(".printPurchaseReturn1").click(function(e){
		e.preventDefault();
    $('#printDivsRet1').attr('class','hidden'); 
    $.ajax({
        type: "POST",
        url:base_url+'Main_purchase/print_purchasereturn',
        data: {"pono" : pono},
        cache: false,
        success:function(data){
                    if(data.success == 1)
                    {
                
                $(document).ready(function() {
                    window.print();
                }); 
                $(document).ready(function() {
                    location.reload();
                }); 
            }   
            $('#printDivsret2').removeAttr('class','hidden');   
        }
    });  
  });
});

// function formatMoney(n,c, d, t)
// {
   
//     c = isNaN(c = Math.abs(c)) ? 2 : c;
//     d = d == undefined ? "." : d;
//     t = t == undefined ? "," : t; 
//     s = n < 0 ? "-" : "";
//     i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "";
//     j = (j = i.length) > 3 ? j % 3 : 0;
//     return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
// }