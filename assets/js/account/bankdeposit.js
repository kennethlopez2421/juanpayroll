$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	//start
	$(".saveBDBtn").click(function(e){
		e.preventDefault();
		var date1 = $("#date1").val();
		var date2 = $("#date2").val();
		var account = $("#account").val();
		var bdtype = $("#bdtype").val();
		var amt = $("#amt").val();
		var notes = $("#notes").val();

		if(date1 == "" || date2 == "" || account == "none" || bdtype == "none" || amt == "" || notes == "") {
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
		else {
			var bddate = formatDate(date1);
			var salesdate = formatDate(date2);
			$.ajax({
				type: 'post',
				url: base_url + 'Main_account/save_bankdeposit_details',
				data:{'date1':bddate,
					'date2':salesdate,
					'bdtype':bdtype,
					'account':account,
					'amt':amt,
					'notes':notes
				},
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				success:function(data) {
					$.LoadingOverlay("hide"); 
					if(data.success == 1) {
						$.toast({
							heading: 'Success',
							text: "You have successfully saved bank deposit.",
							icon: 'success',
							loader: false,  
							stack: false,
							position: 'top-center', 
							bgColor: '#5cb85c',
							textColor: 'white',
							allowToastClose: false,
							hideAfter: 2000,
						});
					}
					
					$("#date1").val("");
					$("#date2").val("");
					$("#amt").val("");
					$("#notes").val("");
					$('#account').val('none').change();
					$('#bdtype').val('none').change();
					$(".saveBDBtn").prop("disabled",false);
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


