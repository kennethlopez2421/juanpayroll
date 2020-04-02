$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var username = ""; // wala talagang laman to dapat for officer lang

$("#sosearchfilter").change(function() {
	var searchtype = $('#sosearchfilter').val(); // id ng dropdown
	var currentdate = new Date();
	
	   if(searchtype == "ponodiv")
       {
       	 $(".searchDateTo").val("");
		 $(".searchDateFrom").val("");
		 $('.posupplier').hide('slow');
		 $(".search_supplier").val("");
		 $('.ponodiv').show('slow');	
		 $('.podatediv').hide('slow');
		 $('.postatus').hide('slow');
		 $(".search_status").val("");
		 //$('.search_po_btn').show('slow');
	   }
	   if(searchtype == "podatediv")
       {
       	 $(".searchDateTo").val($.datepicker.formatDate('mm/dd/yy', currentdate));
		 $(".searchDateFrom").val($.datepicker.formatDate('mm/dd/yy', currentdate));
		 $('.ponodiv').hide('slow');
		 $('.posupplier').hide('slow');
		 $(".search_supplier").val("");
		 $(".search_pono").val("");
		 $('.podatediv').show('slow');
		 $('.postatus').hide('slow');
		 $(".search_status").val("");
		 //$('.search_po_btn').show('slow');
	   }
	   if(searchtype == "posupplier"){
	   	 $(".searchDateTo").val($.datepicker.formatDate('mm/dd/yy', currentdate));
		 $(".searchDateFrom").val($.datepicker.formatDate('mm/dd/yy', currentdate));
		 $('.ponodiv').hide('slow');
		 $('.podatediv').show('slow');
		 $(".search_pono").val("");
		 $('.posupplier').show('slow');
		 $('.postatus').hide('slow');
		 $(".search_status").val("");
	   }
	   if(searchtype == "postatus"){
	   	 $(".searchDateTo").val($.datepicker.formatDate('mm/dd/yy', currentdate));
		 $(".searchDateFrom").val($.datepicker.formatDate('mm/dd/yy', currentdate));
		 $('.ponodiv').hide('slow');
		 $('.podatediv').show('slow');
		 $(".search_supplier").val("");
		 $(".search_pono").val("");
		 $('.posupplier').hide('slow');
		 $('.postatus').show('slow');
	   }
});

var dataTable = $('#table-grid').DataTable({
	//"processing": true,
	"serverSide": true,
	"bDeferRender": true,
	"order": [[ 1, "desc" ]],
	"columnDefs": [{ "orderable": false, "targets": [ 5 ], "className": "dt-center" }],
	"ajax":{
		url :base_url+"Main_purchase/table_receive_po", // json datasource
		type: "post",  // method  , by default get
		data:{'username':username},
		beforeSend:function(data)
        {
            $("body").LoadingOverlay("show"); 
        },
		error: function(){  // error handling
			$(".table-grid-error").html("");
			// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
			$("#table-grid_processing").css("display","none");
		},
		complete: function()
        {
            $("body").LoadingOverlay("hide"); 
        },
	},
	"fnDrawCallback": function(){
        var api = this.api()
        var json = api.ajax.json();
        // console.log(json);
        $(".loader").hide();
        $("#table-grid").show();
    }
});

$("#search_order").click(function(){
	$(".loader").show();
	$("#table-grid").hide();

	var i1 =$('.searchDateFrom').attr('data-column');  // getting column index
	var v1 =$('.searchDateFrom').val();  // getting search input value
	var i2 =$('.searchDateTo').attr('data-column');  // getting column index
	var v2 =$('.searchDateTo').val();  // getting search input value
	var i3 =$('.search_pono').attr('data-column');  // getting column index
	var v3 =$('.search_pono').val();  // getting search input value
	var i4 =$('.search_supplier').attr('data-column');  // getting column index
	var v4 =$('.search_supplier').val();  // getting search input value
	var i5 =$('.search_status').attr('data-column');  // getting column index
	var v5 =$('.search_status').val();  // getting search input value
	
    dataTable.columns(i1).search(v1)
             .columns(i2).search(v2)
             .columns(i3).search(v3)
             .columns(i4).search(v4)
             .columns(i5).search(v5)
             .draw();
});
	
});