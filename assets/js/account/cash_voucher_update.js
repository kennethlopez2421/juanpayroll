$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	//start
	$(".saveCVBtn").click(function(e){
		e.preventDefault();
		var date1 = $("#date1").val();
		var date2 = $("#date2").val();
		var cvtype = $("#cvtype").val();
		var payto = $("#payto").val();
		var amt = $("#amt").val();
		var notes = $("#notes").val();
		var cvno = $("#cvno").val();
		var token = $("#token").val();
		var checker = 0;


		if(date1 == "" || date2 == "" || cvtype == "none" || payto == "" || amt == "" || notes == "")
		{
			checker=0;
			$.toast({
				    heading: 'Note:',
				    text: "Please fill all required fields.",
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
		

		if(checker == 1)
		{
			var cvdate = formatDate(date1);
			var funddate = formatDate(date2);

			$.ajax({
		  		type: 'post',
		  		url: base_url+'Main_account/cash_voucher_update_save',
		  		data:{'date1':cvdate,
		  			  'date2':funddate,
		  			  'cvtype':cvtype,
		  			  'payto':payto,
		  			  'amt':amt,
		  			  'notes':notes,
		  			  'cvno':cvno
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
						    text: "You have successfully update cash voucher #" + cvno,
						    icon: 'success',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
						    bgColor: '#5cb85c',
							textColor: 'white',
							allowToastClose: false,
							//hideAfter: 4000,
						});
					// 	setTimeout(function(){
					// 	$('#Modalloadingbar').modal('hide');
					// },500);
						window.location.href = base_url +'Main_account/cashvoucher_transaction/' +token;
						
		  			}
		  			// window.setTimeout(function(){location.reload()},2000)
	  			  	
					$("#date1").val("");
					$("#date2").val("");
					$("#cvtype").val("");
					$("#payto").val("");
					$("#amt").val("");
					$("#notes").val("");
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


