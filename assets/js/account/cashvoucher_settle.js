$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	//start
	$(".settleCVBtn").click(function(e){
		var cvno = $("#cvno").val();
		var token = $("#token").val();
		var sdate = $("#sdate").val();
		var actualamt = $("#actualamt").val();
		var remarks = $("#remarks").val();
		var amt = $("#amt").val();
		var checker = 0;

		if(sdate != "" && actualamt != "" && remarks != "")
		{
			if(actualamt > amt)
			{
				checker=0;
				$.toast({
						    heading: 'Note:',
						    text: "You have exceeded amount for settle. Please check the amount.",
						    icon: 'info',
						    loader: false,   
						    stack: false,
						    position: 'top-center',  
						    bgColor: '#FFA500',
							textColor: 'white',
							allowToastClose: false,
							hideAfter: 4000          
					}); 
			}
			else
			{
				checker=1;
			}
		}
		else
		{
			checker=0;
			$.toast({
					    heading: 'Note:',
					    text: "Please fill in all required fields.",
					    icon: 'info',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#FFA500',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 4000          
				}); 
		}

		if(checker == 1)
		{
			$.ajax({
		  		type: 'post',
		  		url: base_url+'Main_account/update_cashvoucher_settle',
		  		data:{'cvno':cvno,
		  			  'sdate':sdate,
		  			  'actualamt':actualamt,
		  			  'remarks':remarks,	
		  		},
		  		beforeSend:function(data)
				{
					$.LoadingOverlay("show"); 
				},
				complete: function()
				{
					$.LoadingOverlay("hide"); 
				},
		  		success:function(data)
		  		{
		  			if(data.success == 1)
		  			{
		  				$.toast({
						    heading: 'Success',
						    text: "You have successfully settled cash voucher# "+cvno,
						    icon: 'success',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
						    bgColor: '#5cb85c',
							textColor: 'white',
							allowToastClose: false,
							hideAfter: 2000,
						});

						window.setTimeout(function(){
							window.location.href=base_url+"Main_account/cashvoucher_transaction/" + token;
						},2000)	
		  			}
		  		}
		  	});
		}

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


