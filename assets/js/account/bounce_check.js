$(document).ready(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

    $('.rcheckdate').datepicker({
		format: 'mm/dd/yyyy',
		startDate: '-2d',
		endDate: '+90d'
	});

	//allowing numeric with decimal 
    $(".allownumericwithdecimal").on("keypress keyup blur",function (event){
        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    //allowing numeric without decimal 
    $(".allownumericwithoutdecimal").on("keypress keyup blur",function (event){    
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

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

	$.ajax({
        type:'post',
        url: base_url + 'account/Bouncecheck/getBouncedChecks',
		beforeSend:function() {
			$.LoadingOverlay("show"); 
		},
		complete: function() {
			$.LoadingOverlay("hide"); 
		},
        success:function(data){
            if (data.success == 1) {
                $.each(data.result, function(k, v) {
					$("#searchBouncedCheck").append(new Option(v.chkno, v.chkno));
				});
            }
        }
	});
	
	$("#searchBouncedCheck").change(function() {
		bcheckNumber = $("#searchBouncedCheck").val();

		$.ajax({
			type:'post',
			url: base_url + 'account/Bouncecheck/getBouncedCheckDetails',
			data: { "bcheckNumber" : bcheckNumber },
			beforeSend:function() {
				$.LoadingOverlay("show"); 
			},
			complete: function() {
				$.LoadingOverlay("hide"); 
			},
			success:function(data){
				if (data.success == 1) {
					$.each(data.result, function(k, v) {
						$("#bcheckDate").val(v.chkdate);
						$("#bcheckAmount").val(accounting.formatMoney(v.amount));
						$("#hdnBcheckAmount").val(v.amount);
					});
				}
			}
		});
	});

	$('#btnSave').click(function(e){
		e.preventDefault();

		var bcheckNo = $("#searchBouncedCheck").val();
		var bcheckDate = $("#bcheckDate").val();
		var bcheckAmount = $("#hdnBcheckAmount").val();
		var reason = $("#reason").val();
		var rcheckNo = $("#rcheckNumber").val();
		var rcheckDate = formatDate($("#rcheckDate").val());
		var rcheckAmount = $("#rcheckAmount").val();
		var rcheckType = $("#rcheckType").val();
		var remarks = $("#remarks").val();

		if (bcheckNo == "" || bcheckDate == "" || bcheckAmount == "" || reason == "" || rcheckNo == "" || rcheckDate == "" || rcheckAmount == "" || rcheckType == "") {
			toastMessage('Note:', "Please fill out all required fields.", 'error', '#FFA500');
		}
		else {
			$.ajax({
				type:'post',
				url: base_url + 'account/Bouncecheck/checkNumberAvailability',
				data: { "chkno" : rcheckNo },
				beforeSend:function() {
					$.LoadingOverlay("show"); 
				},
				complete: function() {
					$.LoadingOverlay("hide"); 
				},
				success:function(data){
					if (data.success == 1) {
						if (data.result == null) {
							$.ajax({
								type: 'post',
								url: base_url + 'account/Bouncecheck/save_bounce_check',
								data:{
									'bcheckNo':bcheckNo,
									'bcheckDate':bcheckDate,
									'bcheckAmount':bcheckAmount,
									'reason':reason,
									'rcheckNo':rcheckNo,
									'rcheckDate':rcheckDate,
									'rcheckAmount':rcheckAmount,
									'rcheckType':rcheckType,
									'remarks':remarks
								},
								beforeSend:function() {
									$.LoadingOverlay("show"); 
								},
								success:function(data) {
									if (data.success == 1) {
										toastMessage('Success', 'You have successfully saved Bounce Check.', 'success', '#5cb85c');
										$.LoadingOverlay("hide");
										location.reload();
									}
								}
							});
						}
						else {
							toastMessage('Note:', "Check Number already exists.", 'error', '#FFA500');
						}
					}
				}
			});
			
		}
	});

});

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}

function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}
