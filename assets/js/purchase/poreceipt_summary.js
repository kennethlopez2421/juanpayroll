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
       $(".search_status").val("");
       $('.ponodiv').show('slow');	
       $('.podatediv').hide('slow');
       $('.ponostatus').hide('slow');
       $('.search_po_btn').show('slow');
    }
    if(searchtype == "podatediv")
    {
        $('.ponodiv').hide('slow');
        $(".search_status").val("");	
        $(".searchDateTo").val($.datepicker.formatDate('mm/dd/yy', currentdate));
        $(".searchDateFrom").val($.datepicker.formatDate('mm/dd/yy', currentdate));
        $('.podatediv').show('slow');
        $(".search_pono").val("");
        $('.ponostatus').hide('slow');
        $('.search_po_btn').show('slow');
    }
    if(searchtype == "ponostatus")
    {
      $(".searchDateTo").val($.datepicker.formatDate('mm/dd/yy', currentdate));
      $(".searchDateFrom").val($.datepicker.formatDate('mm/dd/yy', currentdate));
      $('.ponodiv').hide('slow');	
      $(".search_pono").val("");
      $('.podatediv').show('slow');
      $('.ponostatus').show('slow');
      $('.search_po_btn').show('slow');
    }
});

  var dataTable = $('#table-grid').DataTable({
			//"processing": true,
			"serverSide": true,
			"bDeferRender": true,
			"ajax":{
				url :base_url+"Main_purchase/table_poreceipt_summary", // json datasource
				type: "post",  // method  , by default get
				data:{'username':username},
				beforeSend:function(data)
               {
                   $("body").LoadingOverlay("show"); 
               },
               complete: function()
               {
                   $("body").LoadingOverlay("hide"); 
               },
				error: function(){  // error handling
					$(".table-grid-error").html("");
					// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});

  $("#search_order").click(function(){
	// $(".loader").show();
	// $("#table-grid").hide();

	var i1 =$('.searchDateFrom').attr('data-column');  // getting column index
	var v1 =$('.searchDateFrom').val();  // getting search input value
	var i2 =$('.searchDateTo').attr('data-column');  // getting column index
	var v2 =$('.searchDateTo').val();  // getting search input value
	var i3 =$('.search_pono').attr('data-column');  // getting column index
	var v3 =$('.search_pono').val();  // getting search input value
	var i4 =$('.search_status').attr('data-column');  // getting column index
	var v4 =$('.search_status').val();  // getting search input value
	
    dataTable.columns(i1).search(v1)
    .columns(i2).search(v2)
    .columns(i3).search(v3)
    .columns(i4).search(v4)
    .draw();
});
});

function isNumberKeyOnly(evt)   
{    
  var charCode = (evt.which) ? evt.which : evt.keyCode;
  if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
   return false;
return true;
}