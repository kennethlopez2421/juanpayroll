$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	//start
	$(".approveCVBtn").click(function(e){
		var cvno = $("#cvno").val();
		var token = $("#token").val();


		if(cvno > 0)
		{
			$.ajax({
		  		type: 'post',
		  		url: base_url+'Main_account/update_cashvoucher_approved',
		  		data:{'cvno':cvno
		  		},
		  		beforeSend: function() {
                $.LoadingOverlay("show");
                $('.approveCVBtn').prop('disabled', true);
	            },
	            complete: function() {
	                $.LoadingOverlay("hide");
	            },
		  		success:function(data)
		  		{
		  			if(data.success == 1)
		  			{
		  				$.toast({
						    heading: 'Success',
						    text: "You have successfully approved cash voucher# "+cvno,
						    icon: 'success',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
						    bgColor: '#5cb85c',
							textColor: 'white',
							allowToastClose: false,
							//hideAfter: 2000,
						});
						window.setTimeout(function(){
							window.location.href=base_url+"Main_account/cv_approval/" + token;
						})	
		  			}
		  		}
		  	});
		}

	});
	//end


	//start
	$('#table-grid').delegate(".btnbdview", "click", function(){
	  	var cvno = $(this).data('value');
	
	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_account/display_cashvoucher_Details',
	  		data:{'cvno':cvno},
	  		success:function(data)
	  		{
	  			var res1 = data.result1;
	  			var remarks = res1[0].remarks;
	  			if(remarks == null)
	  			{
	  				remarks = "";
	  			}
	  			if (data.success == 1) 
	  			{
	  	            document.getElementById('info_fullname').innerHTML = 
	  	            res1[0].payto.toUpperCase();
	  	            document.getElementById('info_notes').innerHTML = "Notes: "+ remarks;

	  	            document.getElementById('info_cvno').innerHTML = "CV #: "+res1[0].cvno;
	  	            document.getElementById('info_trandate').innerHTML = res1[0].trandate;

	  	            document.getElementById('info_funds').innerHTML = "Funds Date: "+ res1[0].fundsdate;
	  	            document.getElementById('info_status').innerHTML = "CV Status: "+ res1[0].type;
	  	            document.getElementById('info_create').innerHTML = "Created By: "+ res1[0].username;
	  	            document.getElementById('info_app').innerHTML = "Approved By: "+ res1[0].approver;


	  	            var dataTable1 = $('#table-grid1').DataTable({
						"processing": true,
						"serverSide": true,
						"ajax":{
							url :base_url+"Main_account/view_cashvoucher_Details", // json datasource
							type: "post",  // method  , by default get
							data:{'cvno':cvno},
							error: function(){  // error handling
								$(".table-grid1-error").html("");
								$("#table-grid1").append('<tbody class="table-grid1-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
								$("#table-grid1_processing").css("display","none");
							}
						}
					});

					dataTable1.destroy();
	  	        }
	  		}
	  	});

	});
	//end  
	
});

function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}

 function blockSpecialChar(e)
 {
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
 }

function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
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


