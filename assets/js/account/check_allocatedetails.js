$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	//start
	$(".btnAddGlaccount").click(function(e){
		var cvno = $("#cvno").val();
		var token = $("#token").val();
		var t_date = $("#t_date").val();
		var t_description = $("#t_description").val();
		var t_amount = $("#t_amount").val();
		var t_glacc = $("#t_glacc").val();
		var totalcount = $("#totaldata").val();
		var origamt = $("#origamt").val();
		var diffamt = $("#diffamt").val();
		var grandtotalamt=0;
		var gtotal=0;

		var checker = 0;

		if(t_date == "" && t_description == "" && t_amount == "" && t_glacc == "none")
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
					hideAfter: 3000          
			});
		}
		else
		{
			if(parseFloat(t_amount) <= parseFloat(diffamt))
			{
				checker=1;
			}
			else
			{
				checker=0;
				$.toast({
					    heading: 'Note:',
					    text: "You have insufficient balance for allocation. Please check the amount.",
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
			
		}
	
		if(checker == 1)
		{
		 	var datearray=[];
		 	var descarray=[];
		 	var amtarray=[];
		 	var glaccarray=[];
		 	

		 	datearray=[];
		 	descarray=[];
		 	amtarray=[];
		 	glaccarray=[];


		 	if(totalcount > 0)
		 	{
		 		for(i=0; i < totalcount; i++ )
				{
			 		var cv_date = $("#date"+i).val();
					var cv_description = $("#desc"+i).val();
					var cv_amount = $("#amt"+i).val();
					var cv_glacc = $("#gl"+i).val();

					datearray.push(cv_date);
					descarray.push(cv_description);
					amtarray.push(cv_amount);
					glaccarray.push(cv_glacc);
					grandtotalamt += parseFloat(cv_amount);
				}
				
		 	}
		 	datearray.push(t_date);
			descarray.push(t_description);
			amtarray.push(t_amount);
			glaccarray.push(t_glacc);
			gtotal = parseFloat(grandtotalamt) + parseFloat(t_amount);

			var dataTable = $('#table-grid').DataTable({
		  					"destroy": true,
							"processing": true,
							"serverSide": true,
							"ajax":{
								url :base_url+"Main_account/display_check_TableDetails", // json datasource
								type: "post",  // method  , by default get
								data:{'cvno':cvno,
						  			  'datearray': datearray,
						  			  'descarray': descarray,
						  			  'amtarray': amtarray,
						  			  'glaccarray': glaccarray,
						  			  'totalcount': totalcount
						  			 },
								error: function(){  // error handling
									$(".table-grid-error").html("");
									$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
									$("#table-grid_processing").css("display","none");
								}
							}
						});	
			$(".grand_total").text("Total:  " + formatMoney(gtotal,2, ".", ","));
			var totalamtonly = 0;
			totalamtonly = parseFloat(origamt) - parseFloat(gtotal);
			$("#diffamt").val(totalamtonly);					
			dataTable.destroy();			  			

		}




	});
	//end

	//start
	$('#table-grid').delegate(".btnDeleteCV", "click", function(){
		var a = $(this).data('value');
		var totalcount = $("#totaldata").val();
		var origamt = $("#origamt").val();
		var diffamt = $("#diffamt").val();
		var cvno = $("#cvno").val();
		var datearray=[];
	 	var descarray=[];
	 	var amtarray=[];
	 	var glaccarray=[];
	 	var grandtotalamt=0;
	 	var checker=0;

	 	datearray=[];
	 	descarray=[];
	 	amtarray=[];
	 	glaccarray=[];

	 	if(totalcount > 0)
	 	{
	 		for(i=0; i < totalcount; i++ )
			{
		 		var cv_date = $("#date"+i).val();
				var cv_description = $("#desc"+i).val();
				var cv_amount = $("#amt"+i).val();
				var cv_glacc = $("#gl"+i).val();

				if(a != i)
				{
					datearray.push(cv_date);
					descarray.push(cv_description);
					amtarray.push(cv_amount);
					glaccarray.push(cv_glacc);
					grandtotalamt += parseFloat(cv_amount);
				}	
			}
			checker=1;
	 	}
	 	if(checker == 1)
	 	{
		 	var dataTable = $('#table-grid').DataTable({
			  					"destroy": true,
								"processing": true,
								"serverSide": true,
								"ajax":{
									url :base_url+"Main_account/display_check_TableDetails", // json datasource
									type: "post",  // method  , by default get
									data:{'cvno':cvno,
							  			  'datearray': datearray,
							  			  'descarray': descarray,
							  			  'amtarray': amtarray,
							  			  'glaccarray': glaccarray,
							  			  'totalcount': totalcount
							  			 },
									error: function(){  // error handling
										$(".table-grid-error").html("");
										$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
										$("#table-grid_processing").css("display","none");
									}
								}
			});	

			$(".grand_total").text("Total:  " + formatMoney(grandtotalamt,2, ".", ","));
			var totalamtonly = 0;
			totalamtonly = parseFloat(origamt) - parseFloat(grandtotalamt);
			$("#diffamt").val(totalamtonly);					
			dataTable.destroy();	
		}	

	});
	//end	
	

	//start
	$(".adddetails").click(function(e){

		$("#t_date").val("");
		$("#t_description").val("");
		//$("#t_amount").val("");
		$('#t_glacc').val('none').change();

	});
	//end	
	

	//start
	$(".btnSaveExpenses").click(function(e){
		var cvno = $("#cvno").val();
		var token = $("#token").val();
		var totalcount = $("#totaldata").val();
		var tranamt = $("#tranamt").val();
		var payto = $("#payto").val();
		var checker=0;
		var grandtotalamt=0;
		var datearray=[];
	 	var descarray=[];
	 	var amtarray=[];
	 	var glaccarray=[];
	 
	 	datearray=[];
	 	descarray=[];
	 	amtarray=[];
	 	glaccarray=[];

		if(totalcount > 0)
		{
			for(i=0; i < totalcount; i++ )
			{
		 		var cv_date = $("#date"+i).val();
				var cv_description = $("#desc"+i).val();
				var cv_amount = $("#amt"+i).val();
				var cv_glacc = $("#gl"+i).val();

				datearray.push(cv_date);
				descarray.push(cv_description);
				amtarray.push(cv_amount);
				glaccarray.push(cv_glacc);
				grandtotalamt += parseFloat(cv_amount);
			}
		}
		else
		{
			$.toast({
                heading: 'Warning',
                text: 'No record found.',
                icon: 'error',
                loader: false,  
                stack: false,
                position: 'top-center', 
                bgColor: '#ffc107',
                textColor: 'white',
                allowToastClose: false,
                hideAfter: 3000
             });
		}

		if(tranamt == grandtotalamt)
		{
			checker=1;
		}
		else
		{
			checker=0;
			$.toast({
                heading: 'Warning',
                text: 'Total amount has not been fully allocated. Please allocate the total amount of Check.',
                icon: 'error',
                loader: false,  
                stack: false,
                position: 'top-center', 
                bgColor: '#ffc107',
                textColor: 'white',
                allowToastClose: false,
                hideAfter: 5000
             });
		}

		if(checker > 0)
		{
			$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_account/save_allocate_check',
	  		data:{'cvno':cvno, 
	  		'datearray': datearray,
	  		'descarray': descarray,
	  		'amtarray': amtarray,
	  		'glaccarray': glaccarray,
	  		'grandtotalamt': grandtotalamt,
	  		'payto': payto,
			},
	  		beforeSend:function(data){
				$(".btnCVComfirm").text("Please wait...");
				$('.btnCVComfirm').prop('disabled', true);
				$('#Modalloadingbar').modal('show');
			},
		  		success:function(data)
		  		{
		  			if (data.success == 1) 
		  			{
		  				$(".btnCVComfirm").text("Allocated"); 
						$.toast({
						    heading: 'Success',
						    text: "CV #"+ cvno +" has been successfully allocated to expenses.",
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
							window.location.href=base_url+"Main_account/check_transaction_history/" + token;
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

function formatMoney(n,c, d, t)
{
    c = isNaN(c = Math.abs(c)) ? 2 : c;
    d = d == undefined ? "." : d;
    t = t == undefined ? "," : t; 
    s = n < 0 ? "-" : "";
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "";
    j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}



