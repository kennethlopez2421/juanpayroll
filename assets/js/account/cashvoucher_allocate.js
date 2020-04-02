$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	//start
	$(".allocateCVBtn").click(function(e){
		var cvno = $("#cvno").val();
		var token = $("#token").val();
		var atype = $("#atype").val();
		var supp = $("#supp").val();
		var checker = 0;

		
		if(atype == "none")
		{
			checker=0;
			$.toast({
				    heading: 'Note:',
				    text: "Please select allocation type.",
				    icon: 'info',
				    loader: false,   
				    stack: false,
				    position: 'top-center',  
				    bgColor: '#FFA500',
					textColor: 'white',
					allowToastClose: false,
					hideAfter: 3000          
			});
		}
		else
		{
			if(atype == "Expenses")
			{
				checker=1;
			}
			else
			{
				if(supp == "none")
				{
					checker=0;
					$.toast({
					    heading: 'Note:',
					    text: "Please select supplier.",
					    icon: 'info',
					    loader: false,   
					    stack: false,
					    position: 'top-center',  
					    bgColor: '#FFA500',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 3000          
					});
				}
				else
				{
					checker=1;
				}
			}
		}
	



		if(checker == 1)
		{
			
			if(atype == "Expenses")
			{
		 		window.location.href=base_url+"Main_account/cashvoucher_allocatedetails/" + cvno + "/" + token;
		 	}
		 	else
		 	{
		 		window.location.href=base_url+"Main_account/cashvoucher_allocatedetailspo/" + cvno + "/" + supp + "/" + token;
		 	}
		}

	});
	//end

	//start
  	$("#atype").change(function () {
  		var atype = $("#atype").val();
  		if(atype == "Expenses")
  		{
  			$("#supp").prop('disabled',true);
  			$('#supp').val('none').change();
  			$('.suppDiv').hide('slow');
  		}
  		else if(atype == "Purchases")
  		{
  			$("#supp").prop('disabled',false);
  			$('.suppDiv').show('slow');
  		}
  		else
  		{
  			$("#supp").prop('disabled',false);
  			$('#supp').val('none').change();
  			$('.suppDiv').hide('slow');
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


