$(document).ready(function(){


	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.chkdatediv').show('slow');
	var searchtype = "chkdatediv";

	var d = new Date();
	var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

	function clearTable(){
	    table.clear();         
	    table.draw();           
	}
	// dataTable = $('#table-grid').DataTable({
	// 	"serverSide": true,
	// 	"columnDefs": [
	// 	    		{ targets: 8, orderable: false, "sClass":"text-center" }
	// 	],
	// 	"ajax":{
	// 		url:base_url+"Main_account/get_check_transactions", // json datasource
	// 		type: "post",  // method  , by default get
	// 		data:{'searchtype': searchtype, 'datefrom': date, 'dateto': date},
	// 		beforeSend:function(data) {
	// 			$("#table-grid").LoadingOverlay("show"); 
	// 		},
	// 		complete: function() {
	// 			$("#table-grid").LoadingOverlay("hide"); 
	// 		},
	// 		error: function() {  // error handling
	// 			$(".table-grid-error").html("");
	// 			$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
	// 			$("#table-grid_processing").css("display","none");
	// 		}
	// 	}
	// });

	var table = $('#table-grid').DataTable({ //declaring of table
	    columnDefs: [{ targets: [3], visible: true, orderable: false, sClass: 'text-center'}, { targets: [5], visible: false, orderable: false}],

	    columnDefs: [{ targets: [0], sClass: 'td_id'}],
	  columnDefs: [{targets: [ 5 ],visible: false,searchable: false}]
	});//data table

	loadTables();

    function loadTables(){
        $.LoadingOverlay("show");
        var checkno = $("#checkno").val();

        $.ajax({
            type: 'post',
            url: base_url+'Main_account/check_edit_table',
            data:{"checkno":checkno,"type":'edit'},
            
            success:function(data){
                $.LoadingOverlay("hide");
                if(data.success == 1){
                    var count = data.count;   
                    var chdDateData = data.chdDate;
                    var descData = data.desc;
                    var glAccountData = data.glAccount;
                    var acctidData = data.acctid
                    var totalData = data.totalData;
                    var delBtnData = data.delBtn;

                   // clearTable();

                    try{
                        for(i=0;i<count;i++){
                            editArray = [chdDateData[i],descData[i],glAccountData[i],totalData[i],delBtnData[i],acctidData[i]];
                            populateTable(editArray,1);
                        }
                    }catch(e){
                        // console.log("Error msg",e);
                    }
                
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
            }
        });       
    }

    //insert data to table
function populateTable(data,val){
    if(val == 1){
        table.row.add(editArray);         
        table.draw();           
    }else if(val == 2){
        table.row.add(selectedDataarray);         
        table.draw();
    }else{
        alert('There was an error populating table, Please check material balance and material edit table codes');
    }
}

//for delete parent row in the table
    $("#table-grid").on('click', '.btnDelete', function(){ 

        var discount = $("#discount").val();
        //var discount_gen_type_select = $("#disc_perc").val(); //discount type
      
        table.row($(this).parents('tr')).remove().draw(false); //get the selected row to delete

        var data_totalamt = table.rows().columns(3).data(); //get no.6 data which is the total
    });

    //add item inside modal
$("#save_gl_item").click(function(e){

  //  checkInputs('#addRow');
    var cdate = $("#ff_date").val();
    var cdescription = $("#ff_description").val();
    var camount = $("#ff_amount").val();
    var caccount = $("#ff_gl_account").val();
    var gl_account = $( "#ff_gl_account option:selected" ).text();

    //check if not empty fields
    if((cdate == "") || (cdescription == "") || (camount == "") || (caccount == "") ){
       // clearAddform();

        $.toast({
            heading: 'Note',
            text: "Please fill up all required fields",
            icon: 'error',
            loader: false,  
            stack: false,
            position: 'top-center', 
            allowToastClose: false,
            bgColor: '#f0ad4e',
            textColor: 'white'  
        });// if there is no seleccted item or the input item is not on the list

    }else{

            selectedDataarray = [
                formatDate(cdate),
				cdescription.toUpperCase(),	
				gl_account.toUpperCase(), 
				accounting.formatMoney(camount),
                "<center><button class='btn btn-danger btnDelete btnTable'><i class='fa fa-trash-o'></i> Delete</button></center>",
                caccount
            ];// adding selected data to array 

            populateTable(selectedDataarray,2);  

            var data_totalamt = table.rows().columns(3).data(); 

            data_totalamt.each(function(value, index){ 
	            grand_total = eval(value.join("+").replace(/,/g, ''));
	        }); 

	        $(".grand_total").text("Total : "+ accounting.formatMoney(grand_total));

	        $(".totalamt").val(grand_total);

       $('#addItemModal').modal('toggle'); //close modal

        clearAddform(); //clear all forms  

       // clearAddform(); //clear all forms                        
    }
});

	$("#chksearchfilter").change(function() {
		var searchtype = $('#chksearchfilter').val();

   		if(searchtype == "chkdatediv") {
			$('.chkdatediv').show('slow');  
			$('.chkstatdiv').hide('slow');
			$('.chkchecknodiv').hide('slow');
			$('#checkno').val("");

   		}
		else if(searchtype == "chkstatdiv") {
			$('.chkstatdiv').show('slow');
			$('.chkdatediv').hide('slow');
			$('.chkchecknodiv').hide('slow');
			$('#checkno').val("");
		}
		else
		{
			$('.chkchecknodiv').show('slow');
			$('.chkstatdiv').hide('slow');
			$('.chkdatediv').hide('slow');
		}
	});

	$(".btnSearchChk").click(function(e){
		e.preventDefault();

		var datefrom;
		var dateto;
		var searchtype = $('#chksearchfilter').val();
		var chkstatus = $("#chkstatus").val();
		var checker = 0;
		var checkno = $("#checkno").val();

		if(searchtype == "none") {
			checker = 0;
		}
		else if(searchtype == "chkdatediv") {
			if($("#datefrom").val() == "" || $("#dateto").val() == "") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please fill date range field.",
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
				checker = 1;
				datefrom = formatDate($("#datefrom").val());
				dateto = formatDate($("#dateto").val());
			}
		}
		else if(searchtype == "chkstatdiv") {
			if($("#datefrom1").val() == "" && $("#dateto1").val() == "" && chkstatus == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select date and check status.",
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
			else if($("#datefrom1").val() != "" && $("#dateto1").val() != "" && chkstatus == "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please select check status.",
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
			else if($("#datefrom1").val() == "" && $("#dateto1").val() == "" && chkstatus != "none") {
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please fill date range field.",
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
				checker = 1;
				datefrom = formatDate($("#datefrom1").val());
				dateto = formatDate($("#dateto1").val());
			}
		}
		else if(searchtype == "chkchecknodiv")
		{
			if(checkno == "")
			{
				checker = 0;
				$.toast({
				    heading: 'Note:',
				    text: "Please fill check number field.",
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
				checker = 1;
			}
		}
		
		if(checker == 1) {
			dataTable = $('#table-grid').DataTable({
				destroy: true,
				"serverSide": true,
				"columnDefs": [
		    		{ targets: 8, orderable: false, "sClass":"text-center" }
				],
				"ajax":{
					url:base_url+"Main_account/get_check_transactions", // json datasource
					type: "post",  // method  , by default get
					data:{'searchtype': searchtype, 'datefrom': datefrom, 'dateto': dateto, 'chkstatus': chkstatus, 'checkno': checkno},
					beforeSend:function(data) {
						$("#table-grid").LoadingOverlay("show"); 
					},
					complete: function() {
						$("#table-grid").LoadingOverlay("hide"); 
					},
					error: function() {  // error handling
						$(".table-grid-error").html("");
						$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
						$("#table-grid_processing").css("display","none");
					}
				}
			});
		}
	});

	$('#add_item_btn').click(function(e){
		$('#addItemModal').modal();
	});

	$('#table-grid').delegate(".btnView", "click", function(){

	  	var id = $(this).data('value');

	  	var currUrl = window.location.href;

	  	currUrl = currUrl.replace("check_transaction_history", "check_transaction_history_view");
	  	window.location = currUrl+"/"+id;
	});


	$('#table-grid').delegate(".btnTag", "click", function(){

	  	var id = $(this).data('value');

	  	var currUrl = window.location.href;

	  	currUrl = currUrl.replace("check_transaction_history", "check_transaction_history_view_tag");
	  	//window.open(currUrl+"/"+id,'_blank');

	  	window.location = currUrl+"/"+id;
	});

	$('#add_inventory_form').submit(function(event){
		event.preventDefault();

		var form = $(this);

		console.log(form.serialize());

	        $.ajax({
		            url: form.attr('action'),
		            type: form.attr('method'),
					data: form.serialize(),
		        }).done(function(response) {

		            var response = JSON.parse(response);

		            if(response.success===false)
		            {
		            	$.toast({
						    heading: 'Error',
						    text: response.message,
						    icon: 'error',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#d9534f',
							textColor: 'white'  
						});
		            }
		            else
		            {
		            	dataTable.draw();

		            	$('#addItemModal').modal('hide');
		            	$('#add_inventory_form')[0].reset();
		            	$.toast({
						    heading: 'Error',
						    text: response.message,
						    icon: 'error',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: 'yellowgreen',
							textColor: 'white'  
						});
						
		            }
		             clearAddform(); //clear all forms  

		    });
	});

	$('#delete_item_form').submit(function(event){
		event.preventDefault();

		var form = $(this);

		console.log(form.serialize());

	        $.ajax({
		            url: form.attr('action'),
		            type: form.attr('method'),
					data: form.serialize(),
		        }).done(function(response) {

		            var response = JSON.parse(response);

		            if(response.success===false)
		            {
		            	$.toast({
						    heading: 'Error',
						    text: response.message,
						    icon: 'error',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#d9534f',
							textColor: 'white'  
						});
		            }
		            else
		            {
		            	dataTable.draw();
		            	
		            	$('#deleteItemModal').modal('hide');
		            	$.toast({
						    heading: 'Error',
						    text: response.message,
						    icon: 'error',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: 'yellowgreen',
							textColor: 'white'  
						});
						
		            }

		    });
	});

	$('#table-grid').delegate(".btnPrint","click", function(){
		var id = $(this).data('value');

		var currUrl = window.location.href;

	  	currUrl = currUrl.replace("check_transaction_history", "check_transaction_history_print");
	  	window.open(currUrl+"/"+id);
	});

	accountItems = [];
	dateArray = [];
	descArray = [];
	amountArray = [];
	acctidArray = [];

$(".saveBtnEncode").click(function(e){
    e.preventDefault();

    var notes = $(".notes").val();
    var checkno = $(".checkno").val();
    var grand_total = $("#totalamt").val();

    //getting data in datatable
    var dataDate = table.rows().columns(0).data();
    dataDate.each(function (value, index) {
        
        if((value == null) || (value == 0) || (value == '')){
            tableisEmpty = true;
        }else{
            tableisEmpty = false;
        }//check if table is empty

        dateArray = dateArray.concat(value);
    });

    var dataDesc = table.rows().columns(1).data();
    dataDesc.each(function (value, index) {
        
        if((value == null) || (value == 0) || (value == '')){
            tableisEmpty = true;
        }else{
            tableisEmpty = false;
        }//check if table is empty

        descArray = descArray.concat(value);
    });

    var dataAmount = table.rows().columns(3).data();
    dataAmount.each(function (value, index) {
        
        if((value == null) || (value == 0) || (value == '')){
            tableisEmpty = true;
        }else{
            tableisEmpty = false;
        }//check if table is empty

        amountArray = amountArray.concat(value);
    });

    var dataAcctid = table.rows().columns(5).data();
    dataAcctid.each(function (value, index) {
        
        if((value == null) || (value == 0) || (value == '')){
            tableisEmpty = true;
        }else{
            tableisEmpty = false;
        }//check if table is empty

        acctidArray = acctidArray.concat(value);
    });

    var data = table.rows().data();
    data.each(function (value, index) {

        entry = {
            date: value[0],
            desciption: value[1],
            amount: value[3].replace(/,/g, ''), //remove ' , ' comma to work properly
            glid: value[5]
        }
        accountItems.push(entry);
    });

    if (grand_total == 0) {
        $.toast({
            heading: 'Note',
            text: 'Must have at least one item.',
            icon: 'error',
            loader: false,  
            stack: false,
            position: 'top-center', 
            allowToastClose: true,
            bgColor: '#f0ad4e',
            textColor: 'white' 
        });

    } else {
        $.ajax({
            url: base_url+"Main_account/updateCheck",
            type: 'post',
            data: { 
                'dateArray':dateArray,
				'descArray':descArray,
				'amountArray':amountArray,
				'acctidArray':acctidArray,
				'accountItems':accountItems,
				'checkno':checkno,
				'notes':notes,
				'grand_total':grand_total
            },

            beforeSend: function() {
                $.LoadingOverlay("show");
            },

            success:function(data){
                $.LoadingOverlay("hide");
                if(data.success == 1){      
                window.setTimeout(function(){
                         window.location.href=base_url+"Main_account/check_transaction_history/" + token;
                  },500);
                    $.toast({
                        heading: 'Success',
                        text: 'Check Items has been successfully updated.',
                        icon: 'success',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        bgColor: '#5cb85c',
                        textColor: 'white',
                        allowToastClose: false,
                     });
                }else{
                    $.toast({
                        heading: 'Note',
                        text: 'Please fill required fields.',
                        icon: 'error',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        bgColor: '#f0ad4e',
                        textColor: 'white',
                        allowToastClose: false,
                   });
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

function isNumberKeyOnly(evt){    
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;
    return true;
}

//clear all form function, please add/change other input to clear if needed
function clearAddform(){
    $("#ff_date").css("border-color", "#eee");  //rollback when not empty
    $("#ff_date").val('');
    $("#ff_description").val('');
    $("#ff_amount").val("");
    $("#ff_gl_account").val("");
}