$(function(){

	var base_url = $("body").data('base_url');

	$('.dividno').show('slow');
	var searchtype ="none";
	$("#divsearchfilter").change(function() {
		var searchtype = $('#divsearchfilter').val();

		   if(searchtype == "dividno")
	       {
	         $('.dividno').show('slow');
	         $('.divname').hide('slow');
	         $('.divaccount').hide('slow');	
	         $("#nameSearch").val("");
	         $("#accountSearch").val("");
	         $("#idno").val("");	  
	       }
	       else if(searchtype == "divname")
	       {
	         $('.divname').show('slow');
	         $('.dividno').hide('slow');
	         $('.divaccount').hide('slow');	
	         $("#accountSearch").val("");
	         $("#idno").val("");
	       }
	       else if(searchtype == "divaccount")
	       {
	         $('.divname').hide('slow');
	         $('.dividno').hide('slow');
	         $('.divaccount').show('slow');	
	         $("#nameSearch").val("");
	         $("#idno").val("");
	       }
	     
	});


	//start
	// $(".searchBtn").click(function(e){
	// 	e.preventDefault();
	// 	var idno = $("#idno").val();
	// 	var nameSearch = $("#nameSearch").val();
	// 	var searchtype = $('#divsearchfilter').val();
	// 	var accSearch = $('#accountSearch').val();
	// 	var checker=0;

	// 	if(searchtype == "none")
	// 	{
	// 		checker=0;
	// 	}
	// 	else if(searchtype == "dividno")
	// 	{
	// 		if(idno == "")
	// 		{
	// 			checker=0;
	// 			$.toast({
	// 			    heading: 'Note:',
	// 			    text: "Please fill idno field.",
	// 			    icon: 'info',
	// 			    loader: false,   
	// 			    stack: false,
	// 			    position: 'top-center',  
	// 			    bgColor: '#FFA500',
	// 				textColor: 'white',
	// 				allowToastClose: false,
	// 				hideAfter: 3000          
	// 			});
	// 		}
	// 		else
	// 		{
	// 			checker=1;
	// 		}
	// 	}
	// 	else if(searchtype == "divaccount")
	// 	{
	// 		if(accountSearch == "")
	// 		{
	// 			checker=0;
	// 			$.toast({
	// 			    heading: 'Note:',
	// 			    text: "Please fill idno field.",
	// 			    icon: 'info',
	// 			    loader: false,   
	// 			    stack: false,
	// 			    position: 'top-center',  
	// 			    bgColor: '#FFA500',
	// 				textColor: 'white',
	// 				allowToastClose: false,
	// 				hideAfter: 3000          
	// 			});
	// 		}
	// 		else
	// 		{
	// 			checker=1;
	// 		}
	// 	}

	// 	else
	// 	{
	// 		if(nameSearch == "")
	// 		{
	// 			checker=0;
	// 			$.toast({
	// 			    heading: 'Note:',
	// 			    text: "Please fill name field.",
	// 			    icon: 'info',
	// 			    loader: false,   
	// 			    stack: false,
	// 			    position: 'top-center',  
	// 			    bgColor: '#FFA500',
	// 				textColor: 'white',
	// 				allowToastClose: false,
	// 				hideAfter: 3000          
	// 			});
	// 		}
	// 		else
	// 		{
	// 			checker=1;
	// 		}
	// 	}
	// 	if(checker == 1)
	// 	{
	// 		var dataTable = $('#table-grid').DataTable({
	// 			"destroy": true,
	// 			//"processing": true,
	// 			"serverSide": true,
	// 			"columnDefs": [
	// 	    		{ targets: 5, orderable: false, "sClass":"text-center" }
	// 			],
	// 			"ajax":{
	// 				url :base_url+"Main_entity/get_customer_data", // json datasource
	// 				type: "post",  // method  , by default get
	// 				data:{'searchtype': searchtype, 'idno': idno, 'name': nameSearch, 'account':accSearch },
	// 				beforeSend:function(data)
	// 				{
	// 					$("body").LoadingOverlay("show"); 
	// 				},
	// 				complete: function()
	// 				{
	// 					$("body").LoadingOverlay("hide"); 
	// 				},
	// 				error: function(){  // error handling
	// 					$(".table-grid-error").html("");
	// 					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="6" style = "text-align: center;">No data found in the server</th></tr></tbody>');
	// 					$("#table-grid_processing").css("display","none");
	// 				}
	// 			}
	// 		});
	// 	}
	// });
	//end

	$("#searchBtn").click(function(){
		$(".loader").show();
		$("#table_salesorder").hide();
		
		var i1 =$('#idno').attr('data-column');  // getting column index
		var v1 =$('#idno').val();  // getting search input value
		var i2 =$('#nameSearch').attr('data-column');  // getting column index
		var v2 =$('#nameSearch').val();  // getting search input value
		var i3 =$('#accountSearch').attr('data-column');  // getting column index
		var v3 =$('#accountSearch').val();  // getting search input value

        dataTable.columns(i1).search(v1)
                 .columns(i2).search(v2)
                 .columns(i3).search(v3)
                 .draw();
	});

	$("#save_changes").click(function(){// if save changes button is clicked
		var err = validate_required("#customer_info",".req","#cust_email"); // validate for empty required field
		if (err ==false){ // no error
			var Soc = new Date($('#cust_SoC').val());
			var Eoc = new Date($('#cust_EoC').val());
			if (Soc > Eoc){ // check if end of contract is earlier than start of contract
				$.toast({
				    heading: 'Note',
				    text: "End of contract is earlier than start of contract.",
				    icon: 'info',
				    loader: false,  
				    stack: false,
				    position: 'top-center', 
					allowToastClose: false,
					bgColor: '#FFA500',
					textColor: 'white',
					hideAfter: 10000  
				});
				
			}
			else{ // if start and end of contract is just right.

				var fname = $("#cust_fname").val();
				var mname = $("#cust_mname").val();
				var lname = $("#cust_lname").val();

				var bday1 = $("#cust_bday").val();
				var bday = formatDate(bday1);

				var gender = $("#cust_gender").val();
				var conno = $("#cust_conno").val();
				var email_add = $("#cust_email").val();
				var home_add = $("#cust_address").val();
				var branch_name = $("#cust_branchname").val();

				var SoC1 = $("#cust_SoC").val();
				var SoC = formatDate(SoC1);

				var EoC1 = $("#cust_EoC").val();
				var EoC = formatDate(EoC1);

				var outlet_add = $("#cust_outlet_address").val();
				var franchise = $("#cust_franchise").val();
				var credit = $("#cust_credit_term").val();
				var area = $("#cust_area").val();
				var price_cat = $("#cust_price").val();
				var sales_agent = $("#cust_agent").val();
				var sales_area = $("#cust_sales_area").val();
				var customer_status = $("#cust_status").val();

				var cust_email_orig = $("#cust_email_orig").val();




				// var thiss = $("#customer_info").serialize();

				// console.log(thiss);
				if ($("#button_switch").val() == "add"){// check if the process is add or just update
					$.ajax({	//if add
				  		type: 'post',
				  		url: base_url+'Main_entity/add_customer1',
				  		data:{'fname': fname,
				  			  'mname': mname, 
				  			  'lname': lname, 
				  			  'bday': bday, 
				  			  'gender': gender, 
				  			  'conno': conno, 
				  			  'email_add': email_add, 
				  			  'home_add': home_add, 
				  			  'branch_name': branch_name, 
				  			  'SoC': SoC, 
				  			  'EoC': EoC, 
				  			  'outlet_add': outlet_add, 
				  			  'franchise': franchise, 
				  			  'credit': credit, 
				  			  'area': area, 
				  			  'price_cat': price_cat, 
				  			  'sales_agent': sales_agent, 
				  			  'sales_area': sales_area, 
				  			  'customer_status': customer_status,
				  			 },
				  		success:function(data){
				  			
				  			if (data.success==1){
				  				$.toast({
								    heading: 'Success',
								    text: data.message,
								    icon: 'success',
								    loader: false,  
								    stack: false,
								    position: 'top-center', 
								    bgColor: '#5cb85c',
									textColor: 'white',
									allowToastClose: false,
									hideAfter: 5000
								});
								$('#addItemModal').modal('hide');
				  			}
				  			else{
				  				$.toast({
								    heading: 'Note',
								    text: data.message,
								    icon: 'info',
								    loader: false,  
								    stack: false,
								    position: 'top-center', 
									allowToastClose: false,
									bgColor: '#FFA500',
									textColor: 'white'  
								});
				  			}
				  		},
				  		error: function(error){
				  			$.toast({
							    heading: 'Note',
							    text: 'Something went wrong. Please try again.',
							    icon: 'info',
							    loader: false,  
							    stack: false,
							    position: 'top-center', 
								allowToastClose: false,
								bgColor: '#FFA500',
								textColor: 'white'  
							});
				  		}
				  	});
				}
				else if ($("#button_switch").val() == "update"){//if update
					// alert ("updating...");
					thiss = $("#customer_info").serialize();
					$.ajax({
				  		type: 'post',
				  		url: base_url+'Main_entity/update_customer1',
				  		data:{'fname': fname,
				  			  'mname': mname, 
				  			  'lname': lname, 
				  			  'bday': bday, 
				  			  'gender': gender, 
				  			  'conno': conno, 
				  			  'email_add': email_add, 
				  			  'home_add': home_add, 
				  			  'branch_name': branch_name, 
				  			  'SoC': SoC, 
				  			  'EoC': EoC, 
				  			  'outlet_add': outlet_add, 
				  			  'franchise': franchise, 
				  			  'credit': credit, 
				  			  'area': area, 
				  			  'price_cat': price_cat, 
				  			  'sales_agent': sales_agent, 
				  			  'sales_area': sales_area, 
				  			  'customer_status': customer_status, 
				  			  'cust_email_orig': cust_email_orig
				  			 },
				  		success:function(data){
				  			alert(data.success);
				  			if (data.success==1){
				  				$.toast({
								    heading: 'Success',
								    text: data.message,
								    icon: 'success',
								    loader: false,  
								    stack: false,
								    position: 'top-center', 
								    bgColor: '#5cb85c',
									textColor: 'white',
									allowToastClose: false,
									hideAfter: 10000
								});
								$('#addItemModal').modal('hide');
				  			}
				  			else{
				  				$.toast({
								    heading: 'Note',
								    text: data.message,
								    icon: 'info',
								    loader: false,  
								    stack: false,
								    position: 'top-center', 
									allowToastClose: false,
									bgColor: '#FFA500',
									textColor: 'white'  
								});
				  			}
				  		},
				  		error: function(error){
				  			$.toast({
							    heading: 'Note',
							    text: 'Something went wrong. Please try again.',
							    icon: 'info',
							    loader: false,  
							    stack: false,
							    position: 'top-center', 
								allowToastClose: false,
								bgColor: '#FFA500',
								textColor: 'white'  
							});
				  		}
				  	});
				}
			}
		}
	});

	var dataTable = $('#table-grid').DataTable({
		//"processing": true,
		"serverSide": true,
		// "columnDefs": [
  		// { targets: 5, orderable: false, "sClass":"text-center" }
		// ],
		"ajax":{
			url :base_url+"Main_entity/get_customer_data", // json datasource
			type: "post",  // method  , by default get
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
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="6" style = "text-align: center;">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});
	// $('.searchBtn').on('click', function(){   // for text boxes

	// 	var oTable = $('#table-grid').dataTable();

	// 	oTable.fnFilter( $(".idno").val(), '0' );
	// 	// oTable.fnFilter( $(".idSearch").val(), '0' );

	// 	oTable.fnFilter( $(".nameSearch").val(), '1' );
	// 	oTable.fnFilter( $(".conSearch").val(), '2' );
	// 	oTable.fnFilter( $(".termSearch").val(), '3' );
	// 	oTable.fnFilter( $(".areaSearch").val(), '4' );
	// 	oTable.fnFilter( $(".branchSearch").val(), '5' );

	// });

	// $('.filterBtn').click(function(){

	// 	if($('.searchAppName').val() != "" || $('.searchDate').val != "" || $('.searchDate2').val != ""){ //all

	// 		var c =$('.searchAppName').attr('data-column');  // getting column index
	// 		var d =$('.searchAppName').val();  // getting search input value

	// 		var e =$('.searchDate').attr('data-column');  
	// 		var f =$('.searchDate').val();  

	// 		var g =$('.searchDate2').attr('data-column');  
	// 		var h =$('.searchDate2').val();  

	// 		dataTable.columns(c).search(d);
	// 		dataTable.columns(e).search(f);
	// 		dataTable.columns(g).search(h).draw();
	// 	}else{
	// 		dataTable.columns(0).search("");
	// 		dataTable.columns(1).search("");
	// 		dataTable.columns(2).search("").draw();
	// 	};
	// });

	$(".saveCustomer").click(function(e){
		// e.preventDefault();

		var fname = $("#cust_fname").val();
		var mname = $("#cust_mname").val();
		var lname = $("#cust_lname").val();

		var bday1 = $("#cust_bday").val();
		var bday = formatDate(bday1);

		var accno = $("#acc_no").val();
		var gender = $("#cust_gender").val();
		var conno = $("#cust_conno").val();
		var email_add = $("#cust_email").val();
		var home_add = $("#cust_address").val();
		var branch_name = $("#cust_branchname").val();

		var SoC1 = $("#cust_SoC").val();
		var SoC = formatDate(SoC1);

		var EoC1 = $("#cust_EoC").val();
		var EoC = formatDate(EoC1);

		var outlet_add = $("#cust_outlet_address").val();
		var franchise = $("#cust_franchise").val();
		var credit = $("#cust_credit_term").val();
		var area = $("#cust_area").val();
		var price_cat = $("#cust_price").val();
		var sales_agent = $("#cust_agent").val();
		var sales_area = $("#cust_sales_area").val();
		var customer_status = $("#cust_status").val();
		var cust_email_orig = $("#cust_email_orig").val();

		var checker=1;


		// if(
		// fname == "" &&
		// mname == "" &&
		// lname == "" &&
		// bday == "" &&
		// gender == "" &&
		// conno == "" &&
		// email_add == "" &&
		// home_add == "" && 
		// branch_name == "" &&
		// SoC == "" &&
		// EoC == "" &&
		// outlet_add == "" &&  
		// franchise == "" &&
		// credit == "" &&
		// area == "" &&
		// price_cat == "" &&
		// sales_agent == "" &&
		// sales_area == "" &&
		// customer_status == "" 
		// )	
		if(checker > 0)
		{

			$.ajax({
		  		type: 'post',
		  		url: base_url+'Main_entity/add_customer1',
		  		data:{'fname': fname,
		  			  'mname': mname, 
		  			  'lname': lname, 
		  			  'accno': accno,
		  			  'bday': bday, 
		  			  'gender': gender, 
		  			  'conno': conno, 
		  			  'email_add': email_add, 
		  			  'home_add': home_add, 
		  			  'branch_name': branch_name, 
		  			  'SoC': SoC, 
		  			  'EoC': EoC, 
		  			  'outlet_add': outlet_add, 
		  			  'franchise': franchise, 
		  			  'credit': credit, 
		  			  'area': area, 
		  			  'price_cat': price_cat, 
		  			  'sales_agent': sales_agent, 
		  			  'sales_area': sales_area, 
		  			  'customer_status': customer_status
		  			 },
		  		beforeSend:function(data)
				{
					$("body").LoadingOverlay("show"); 
				},
				complete: function()
				{
					$("body").LoadingOverlay("hide"); 
				},
		  		success:function(data)
		  		{

		  			if (data.success==1){
		  				$.toast({
						    heading: 'Success',
						    text: data.message,
						    icon: 'success',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
						    bgColor: '#5cb85c',
							textColor: 'white',
							allowToastClose: false,
							hideAfter: 5000
						});
						clearFields();
						$(".saveCustomer").prop("disabled",false);
		  			}
		  			else
		  			{
		  				$.toast({
						    heading: 'Note',
						    text: data.message,
						    icon: 'info',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#FFA500',
							textColor: 'white'  
						});
		  			}

		  		}
	  		});

		}
    });	

    $(".updatesaveCustomer").click(function(e){
		e.preventDefault();
		var idno = $("#uidno").val();
		var fname = $("#ucust_fname").val();
		var mname = $("#ucust_mname").val();
		var lname = $("#ucust_lname").val();
		var accno = $("#uacc_no").val();
		var bday1 = $("#ucust_bday").val();
		var bday = formatDate(bday1);

		var gender = $("#ucust_gender").val();
		var conno = $("#ucust_conno").val();
		var email_add = $("#ucust_email").val();
		var home_add = $("#ucust_address").val();
		var branch_name = $("#ucust_branchname").val();

		var SoC1 = $("#ucust_SoC").val();
		var SoC = formatDate(SoC1);

		var EoC1 = $("#ucust_EoC").val();
		var EoC = formatDate(EoC1);

		var outlet_add = $("#ucust_outlet_address").val();
		var franchise = $("#ucust_franchise").val();
		var credit = $("#ucust_credit_term").val();
		var area = $("#ucust_area").val();
		var price_cat = $("#ucust_price").val();
		var sales_agent = $("#ucust_agent").val();
		var sales_area = $("#ucust_sales_area").val();
		var customer_status = $("#ucust_status").val();
		var cust_email_orig = $("#ucust_email_orig").val();
		var checker=1;


		if(checker > 0)
		{

			$.ajax({
		  		type: 'post',
		  		url: base_url+'Main_entity/update_customer1',
		  		data:{'fname': fname,
		  			  'mname': mname, 
		  			  'lname': lname, 
		  			  'bday': bday, 
		  			  'gender': gender, 
		  			  'conno': conno, 
		  			  'email_add': email_add, 
		  			  'home_add': home_add, 
		  			  'branch_name': branch_name, 
		  			  'SoC': SoC, 
		  			  'EoC': EoC, 
		  			  'outlet_add': outlet_add, 
		  			  'franchise': franchise, 
		  			  'credit': credit, 
		  			  'area': area, 
		  			  'price_cat': price_cat, 
		  			  'sales_agent': sales_agent, 
		  			  'sales_area': sales_area, 
		  			  'customer_status': customer_status,
		  			  'cust_email_orig': cust_email_orig,
		  			  'idno': idno,
		  			  'accno': accno
		  			 },
		  		beforeSend:function(data){
						$(".updatesaveCustomer").prop("disabled",true);
				},
		  		success:function(data)
		  		{

		  			if (data.success==1){
		  				$.toast({
						    heading: 'Success',
						    text: data.message,
						    icon: 'success',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
						    bgColor: '#5cb85c',
							textColor: 'white',
							allowToastClose: false,
							hideAfter: 5000
						});
						
						
		  			}
		  			else
		  			{
		  				$.toast({
						    heading: 'Note',
						    text: data.message,
						    icon: 'info',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#FFA500',
							textColor: 'white'  
						});

		  			}

		  			$(".updatesaveCustomer").prop("disabled",false);

		  		}
	  		});

		}
    });	

	$('#table-grid').delegate(".btnView", "click", function(){

	  	var idno = $(this).data('value');

	  	$.ajax({
        type: 'post',
        url: base_url+'Main_entity/get_customer',
        data: {'idno':idno},
        beforeSend:function(data)
		{
			$("body").LoadingOverlay("show"); 
		},
		complete: function()
		{
			$("body").LoadingOverlay("hide"); 
		},
        success:function(data){
            if(data.success == 1) {
                var bdate = new Date(data.customer_info.bday);//birthdate
                // alert(data.customer_info.contract_start_date);

                $("#uidno").val(data.customer_info.idno);
                $("#ucust_fname").val(data.customer_info.fname);
                $("#ucust_mname").val(data.customer_info.mname);
                $("#ucust_lname").val(data.customer_info.lname);
                $("#uacc_no").val(data.customer_info.acctno);
                $('#ucust_bday').datepicker({ dateFormat: 'yyyy-mm-dd'}).datepicker("setDate", new Date(data.customer_info.bday));// set Birth date

                $("#ucust_gender").val(data.customer_info.gender);
                $("#ucust_conno").val(data.customer_info.conno);
                $("#ucust_email").val(data.customer_info.email);
                $("#ucust_address").val(data.customer_info.homeaddress);
                //franchise information
                $("#ucust_branchname").val(data.customer_info.branchname);

                $("#ucust_email_orig").val(data.customer_info.email);	
                $('#ucust_SoC').datepicker({ dateFormat: 'yyyy-mm-dd'}).datepicker("setDate", new Date(data.customer_info.contractstartdate));// set EOC DATE
                $('#ucust_EoC').datepicker({ dateFormat: 'yyyy-mm-dd'}).datepicker("setDate", new Date(data.customer_info.regdate));// set EOC DATE
                $("#ucust_outlet_address").val(data.customer_info.address);
                $("#ucust_franchise").val(data.customer_info.franchiseid);
                $("#ucust_credit_term").val(data.customer_info.termcredit);
                $("#ucust_area").val(data.customer_info.areaid);
                $("#ucust_price").val(data.customer_info.pricecat);
                $("#ucust_agent").val(data.customer_info.empid);
                $("#ucust_sales_area").val(data.customer_info.salesareaid);
                $("#ucust_status").val(data.customer_info.branchstatus);
                
            }
        }
    	}); 	
	});

	$(".generateBillBtn").on("click", function(){
		idno = $("#uidno").val();
		console.log(idno);

		$.ajax({
			url: base_url+"Main_automated_bill/generate_bill",
			type: 'post',
			data: { 'idno': idno },
			beforeSend: function() {
				$.LoadingOverlay("show");
			},
			success: function(data) {
				$.LoadingOverlay("hide");
				if (data.success == 1) {

					setTimeout(function() {
					   location.reload();
				  	}, 1500);
				}
				else {
				}
			}
		});
	});
});

function display_info(idno){
	$("#customer_info").get(0).reset()
        $("#addItemModal").modal({backdrop: "false"});
        $("#button_switch").val("update");
        // $("#cust_email").prop("readOnly", true);
    $(idno).click(function(e){
        e.preventDefault();
    });
    $.ajax({
        type: 'post',
        url: base_url+'Main_entity/get_customer',
        data: {'idno':idno},
        success:function(data){
            if(data.success == 1) {
                var bdate = new Date(data.customer_info.bday);//birthdate


                $("#idno").val(data.customer_info.idno);
                $("#cust_fname").val(data.customer_info.fname);
                $("#cust_mname").val(data.customer_info.mname);
                $("#cust_lname").val(data.customer_info.lname);
                $('#cust_bday').datepicker({ dateFormat: 'yyyy-mm-dd'}).datepicker("setDate", new Date(data.customer_info.bday));// set Birth date

                $("#cust_gender").val(data.customer_info.gender);
                $("#cust_conno").val(data.customer_info.conno);
                $("#cust_email").val(data.customer_info.email);
                $("#cust_address").val(data.customer_info.homeaddress);
                //franchise information
                $("#cust_branchname").val(data.customer_info.branchname);

                $("#cust_email_orig").val(data.customer_info.email);	
                $('#cust_SoC').datepicker({ dateFormat: 'yyyy-mm-dd'}).datepicker("setDate", new Date(data.customer_info.contract_start_date));// set EOC DATE
                $('#cust_EoC').datepicker({ dateFormat: 'yyyy-mm-dd'}).datepicker("setDate", new Date(data.customer_info.regdate));// set EOC DATE
                $("#cust_outlet_address").val(data.customer_info.address);
                $("#cust_franchise").val(data.customer_info.franchiseid);
                $("#cust_credit_term").val(data.customer_info.termcredit);
                $("#cust_area").val(data.customer_info.areaid);
                $("#cust_price").val(data.customer_info.pricecat);
                $("#cust_agent").val(data.customer_info.empid);
                $("#cust_sales_area").val(data.customer_info.salesareaid);
                $("#cust_status").val(data.customer_info.status_id);
            }
        }
    });
}

function clearFields(){
    $("#cust_fname").val("");
    $("#cust_mname").val("");
	$("#cust_lname").val("");
	$("#cust_bday").val("");
	$("#cust_gender").val("");
	$("#cust_conno").val("");
	$("#cust_email").val("");
	$("#cust_address").val("");
	$("#cust_branchname").val("");
	$("#cust_SoC").val("");
	$("#cust_EoC").val("");
	$("#acc_no").val("");


	$("#cust_outlet_address").val("");
	$("#cust_franchise").val("");
	$("#cust_credit_term").val("");
	$("#cust_area").val("");
	$("#cust_price").val("");

	$("#cust_agent").val("").change();
	$("#cust_sales_area").val("").change();
	$("#cust_status").val("").change();
	$("#cust_email_orig").val("").change();
}

function isNumberKeyOnly(evt){    
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
	return false;
	return true;
}

///required fields validator
function validate_required(form_caller, required_fields,register_email){//form_caller is the class or id of the  form
		var serial = $(form_caller).serialize(); // collect all user input
		var error = false; //declare error 
		// if ($(termsCheckbox).is(':checked')){ // if checked terms and condition
			$(form_caller).find(required_fields).each(function(){ //loop all input field then validate
				if ($(this).val() == ""){
					$(this).css("border-color", "#d9534f"); //change all empty to color red
				}else{
					$(this).css("border-color", "#eee");  //rollback when not empty
				}
			});

			$(form_caller).find(required_fields).each(function(){ //loop all input field then validate
				if ($(this).val() == ""){ // if empty show error
					error = true; //update error to 1
					// $(this).css("border-color","#d9534f");
					$(this).focus();
					$.toast({
					    heading: 'Note',
					    text: 'Please fill out this field',
					    icon: 'info',
					    loader: false,   
					    stack: false,
					    position: 'top-center',     
					    bgColor: '#FFA500;',
						textColor: 'white'
					});

					//focus first empty fields
				}
			});
			if (error == false) { // if no error 
				if (!validate_email($(register_email).val())) { //validate email
					error = true; //update error to 1
					$(this).focus();
					$.toast({
					    heading: 'Note',
					    text: 'Please fill out email properly',
					    icon: 'info',
					    loader: false,   
					    stack: false,
					    position: 'top-center',     
					    bgColor: '#FFA500;',
						textColor: 'white'
					});
				}
			}
			return error;
			// 

	$(".generateBillBtn").on("click", function(){
		idno = $("#uidno").val();
		console.log(idno);

		$.ajax({
			url: base_url+"Main_automated_bill/generate_bill",
			type: 'post',
			data: { 'idno': idno },
			beforeSend: function() {
				$.LoadingOverlay("show");
			},
			success: function(data) {
				$.LoadingOverlay("hide");
				if (data.success == 1) {

					setTimeout(function() {
					   location.reload();
				  	}, 1500);
				}
				else {
				}
			}
		});
	});
}
function validate_email(email){ 
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
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



