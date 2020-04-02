$(function(){

	var fromDate;
	var toDate;
	var buildNo;
	var itemSearch;
	var locationSearch;

	var dateNow;

    var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
    	
    dateNow = $('.search-input-select1').val();

    getValues();
    
    showTable(fromDate,toDate,buildNo,itemSearch,locationSearch);

    $('.divdate').show('slow');
    $('.divlocation').show('slow');
	$('.divbuildno').hide('slow');
	$('.divitemname').hide('slow');
	
    $("#divsearchfilter").change(function() {
        var searchtype = $('#divsearchfilter').val();

           if(searchtype == "divdate")
           {
             $('.divdate').show('slow');
             $('.divlocation').show('slow');
             $('.divbuildno').hide('slow');
             $('.divitemname').hide('slow');

             $(".buildnosearch").val("");
             $(".itemsearch").val("");
             $(".locationsearch").val("");
			 $(".search-input-select1").val(dateNow);
             $(".search-input-select2").val(dateNow);   
           }
           else if(searchtype == "divbuildno")
           {
             $('.divdate').hide('slow');
             $('.divlocation').hide('slow');
             $('.divbuildno').show('slow');
             $('.divitemname').hide('slow');

			 $(".buildnosearch").val("");
             $(".itemsearch").val("");
             $(".locationsearch").val("");
             $(".search-input-select1").val("");
             $(".search-input-select2").val("");
           }
           else if(searchtype == "divitemname")
           {
             $('.divdate').hide('slow');
             $('.divlocation').hide('slow');
             $('.divbuildno').hide('slow');
             $('.divitemname').show('slow');

			 $(".buildnosearch").val("");
             $(".itemsearch").val("");
             $(".locationsearch").val("");
             $(".search-input-select1").val("");
             $(".search-input-select2").val("");
           }
    });

    function showTable(fromDate,toDate,buildNo,itemSearch,locationSearch){
		var dataTable = $('#table-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"destroy": true,
			"order": [[ 0, "asc" ], [1, "asc"]],
			"columnDefs": [{ "orderable": false, "targets": [ 6 ], "sClass":"text-center" }],
			"ajax":{
			url :base_url+"Main_manufacturing/table_buildlist", // json datasource
			type: "post",  // method  , by default get
			data:{'fromDate':fromDate,'toDate':toDate,'buildNo':buildNo,'itemSearch':itemSearch,'locationSearch':locationSearch},
				beforeSend:function(data)
            	{
                	$("#table-grid").LoadingOverlay("show"); 
            	},
            	complete: function()
            	{
                	$("#table-grid").LoadingOverlay("hide"); 
            	},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});//data table
		
	};

	function getValues(){
		fromDate =  $(".search-input-select1").val();
		toDate = $(".search-input-select2").val();
		buildNo = $(".buildnosearch").val();
		itemSearch = $(".itemsearch").val();
		locationSearch = $(".locationsearch").val();			
	} 
        
    //for datepicker
	$('.searchBtn').on('click', function(){   // for select box
         	
		getValues();

		showTable(fromDate,toDate,buildNo,itemSearch,locationSearch);

	});

	$('#table-grid').delegate(".btnView", "click", function(){
		$('#viewBuildlistModal').modal('show');
		$('.btnPrintWin').css('display','block');
		buildNo = this.id;

		var dataTable = $('#table-build-view').DataTable({
			"processing": true,
			"destroy": true,
			"serverSide": true,
			"ajax":{
			url :base_url+"Main_manufacturing/build_view_modal", // json datasource
			type: "post",  // method  , by default get
			data:{buildNo},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			},
		        "fnDrawCallback": function() {
			        var api = this.api()
			        var json = api.ajax.json();
			        
                    $(".buildNo").text("Build Inventory" + " #" + json.lblbuildNo);
                    $(".tranDate").text(json.lbltranDate);
                    $(".lblprepDate").text(json.lblprepDate);
                    $(".lblbuildDate").text(json.lbltranDate);
                    $(".lblQty").text(json.lblqty);
                    $(".lblUnit").text(json.lblunit);
                    $(".lblItem").text(json.lblitemname);
                    $(".lbllocation").text(json.lbllocation);

			    }
		});//data table

	});

	$('.btnPrint').click(function(e){
		$('#viewBuildlistModal').modal('show');
		$('.btnPrintWin').css('display','block');
		buildNo = this.id;

		var dataTable = $('#table-build-view').DataTable({
			"processing": true,
			"destroy": true,
			"serverSide": true,
			"ajax":{
			url :base_url+"Main_manufacturing/build_view_modal", // json datasource
			type: "post",  // method  , by default get
			data:{buildNo},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			},
		        "fnDrawCallback": function() {
			        var api = this.api()
			        var json = api.ajax.json();

                    $(".buildNo").text("Build Inventory" + " #" + json.lblbuildNo);
                    $(".tranDate").text(json.lbltranDate);
                    $(".lblprepDate").text(json.lblprepDate);
                    $(".lblbuildDate").text(json.lbltranDate);
                    $(".lblQty").text(json.lblqty);
                    $(".lblUnit").text(json.lblunit);
                    $(".lblItem").text(json.lblitemname);
                    $(".lbllocation").text(json.lbllocation);

			    }
		});//data table

	});

	$(".printWin").click(function(e){

        window.location.href=''+base_url+'Main_manufacturing/printBuildlist/'+buildNo;

	});		

	$('#table-grid').delegate(".btnResched", "click", function(){
    	$('#viewReschedModal').modal('show');
        buildNo = this.id;

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_manufacturing/builditemResched/',
	  		data:{'buildNo':buildNo},
	  		success:function(data){
	  			$('#resched_build_date').css("border-color", "#eee");  //rollback when not empty
	  			if(data.success == 1){
	  				$('#resched_prep_date').val(data.prepDate);
	  				$('#resched_build_date').datepicker("setDate", new Date(data.buildDate));

	  				$('#resched_build_date').attr("placeholder", "Build date");
					$('#resched_qty').val(data.qty);
					$('#resched_unit').val(data.unit);
					$('#resched_item').val(data.itemname.toUpperCase());
					$('#resched_location').val(data.location);
					$('#resched_notes').val(data.notes);

					$('#resched_buildno').val(data.buildNo);

					$('#resched_olddate').val(data.olddate);

					$('#resched_itemid').val(data.itemid);

	  			}

	  		},
	  		error: function(error){
	  			$.toast({
				    heading: 'Note',
				    text: 'Something went wrong. Please try again.',
				    icon: 'error',
				    loader: false,  
				    stack: false,
				    position: 'top-center', 
					allowToastClose: false,
					bgColor: '#f0ad4e',
					textColor: 'white'  
				});
	  		}
	  	});


	});


	$(".resched_btn_save").click(function(e){

		var newdate = $('#resched_build_date').val();

		var buildNumber = $('#resched_buildno').val();

		var olddate = $('#resched_olddate').val();

		var itemid = $('#resched_itemid').val();

		if(newdate == "" ){
			$.toast({
			heading: 'Note',
			text: 'Please fill out this field',
			icon: 'error',
			loader: false,  
			stack: false,
			position: 'top-center', 
			allowToastClose: false,
			bgColor: '#f0ad4e',
			textColor: 'white'  
			});

			$('#resched_build_date').css("border-color", "#d9534f"); //change all empty to color red
			$('#resched_build_date').focus();

		}else{

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_manufacturing/updateBuildate/',
	  		data:{'buildNo':buildNumber,'olddate':olddate,'newdate':newdate,'itemid':itemid},
             	beforeSend:function(data){
                    $(".resched_btn_save").text("Please wait...");
                    $(".resched_btn_save").prop("disabled",true); 
					$('#ajaxbusy').modal('show');
					$('#viewReschedModal').modal('hide');               
                },	  			
	  			success:function(data){
                	if(data.success == 1){
 						$('#ajaxbusy').modal('toggle');
 						$('#viewReschedModal').modal('show');
                    	$.toast({
                        heading: 'Success',
                        text: data.message,
                        icon: 'success',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#5cb85c',
                        textColor: 'white'  
                    	});

                    	$('#viewReschedModal').modal('toggle');   
            		}else{
                    	$.toast({
                        heading: 'Note',
                        text: data.message,
                        icon: 'error',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#f0ad4e',
                        textColor: 'white'  
                    	});
	  				} 

                    	$(".resched_btn_save").text("Save");
                    	$(".resched_btn_save").prop("disabled",false)	  				
	  			}

	  			});

	  	}        
	});


	$(".resched_cancelBtn").click(function(e){

		$(".resched_btn_save").text("Save");
        $(".resched_btn_save").prop("disabled",false); 	  	

	});

	$('#table-grid').delegate(".btnDeform", "click", function(){
        $('#viewDeformModal').modal('show');
        buildNo = this.id;

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_manufacturing/builditemDeform/',
	  		data:{'buildNo':buildNo},
	  		success:function(data){

	  			if(data.success == 1){
	  				$('#deform_build_date').val(data.buildDate);
	  				$('#deform_qty').val(accounting.formatMoney(data.qty));
					$('#deform_unit').val(data.unit);
					$('#deform_item').val(data.itemname.toUpperCase());
					$('#deform_location').val(data.location);
					$('#deform_notes').val(data.notes);
					
					$('#deform_buildno').val(data.buildNo);

					$('#deform_locid').val(data.locid);

					$('#deform_itemid').val(data.itemid);
	  			}



	  		},
	  		error: function(error){
	  			$.toast({
				    heading: 'Note',
				    text: 'Something went wrong. Please try again.',
				    icon: 'error',
				    loader: false,  
				    stack: false,
				    position: 'top-center', 
					allowToastClose: false,
					bgColor: '#f0ad4e',
					textColor: 'white'  
				});
	  		}
	  	});


	});

	$(".deform_btn_save").click(function(e){

		var defQty = $('#deform_defqty').val();

		var buildNumber = $('#deform_buildno').val();

		var deftype = $("#deform_deftype>option:selected").html();

		var remarks = $('#deform_remarks').val();

		var itemid = $('#deform_itemid').val();

		var trandate = $('#deform_build_date').val();

		var locid = $('#deform_locid').val();

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_manufacturing/updateDeform/',
	  		data:{'defQty':defQty,'buildNumber':buildNumber,'deftype':deftype,'remarks':remarks,'itemid':itemid,'trandate':trandate,'locid':locid},
                beforeSend:function(data){
                    $(".deform_btn_save").text("Please wait...");
                    $(".deform_btn_save").prop("disabled",true); 
                },	  			
	  			success:function(data){

                	if(data.success == 1){
                		$('#deform_defqty').css("border-color", "#eee");  //rollback when not empty
                    	$.toast({
                        	heading: 'Success',
                        	text: data.message,
                        	icon: 'success',
                        	loader: false,  
                        	stack: false,
                        	position: 'top-center', 
                        	allowToastClose: false,
                        	bgColor: '#5cb85c',
                        	textColor: 'white'  
                    	}); 
                    	$(".deform_btn_save").text("Save");
                    	$(".deform_btn_save").prop("disabled",false);
                    	clearDeform();
                    	$('#viewDeformModal').modal('toggle');   
            			
            		}else{
                    	$.toast({
                        	heading: 'Note',
                        	text: data.message,
                        	icon: 'error',
                        	loader: false,  
                        	stack: false,
                        	position: 'top-center', 
                        	allowToastClose: false,
                        	bgColor: '#f0ad4e',
                        	textColor: 'white'  
                    	});

                    	$(".deform_btn_save").text("Save");
                    	$(".deform_btn_save").prop("disabled",false); 


                		$('#deform_defqty').css("border-color", "#d9534f"); //change all empty to color red
                		$('#deform_defqty').focus();
                	  				

	  				}
	  			}

	  		}); 
       
	});

	$(".deform_cancelBtn").click(function(e){
		clearDeform();
		$('#deform_defqty').css("border-color", "#eee");  //rollback when not empty
		$(".deform_btn_save").text("Save");
        $(".deform_btn_save").prop("disabled",false); 	  	

	});

    function isNumberKeyOnly(evt)   
    {    
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
        return true;
    }


    //allowing numeric with decimal 
    $(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
    
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
        }
    });

    function clearDeform(){

        $('#deform_defqty').val("");
        $('#deform_remarks').val("");
        var arg ='option Siomai';
 		$('#deform_deftype > option').each(function(){
 		if($(this).text()==arg) $(this).parent('select').val($(this).val())
 		})

    }    	  	
	
});//main
