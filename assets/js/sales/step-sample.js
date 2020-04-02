$(function(){
	var base_url = $("body").data('base_url'); //url
	var datas = $("body").data('datas'); // data for query
	var search_label = $("body").data('label'); //label search

	var checkbox_ticket = []; // array of ticket_id selected in checkbox
	var checkbox_amount = []; // array of ticket_amoun selected in checkbox
	var sum_of_amount = 0;
	var sum_of_amount_viewing_step1 = 0;
	var generated_reference_no = getReferenceNo(9);

	$(".select2").select2({});
	accounting.settings = {
		currency: {
			symbol : "₱ ",   // default currency symbol is '$'
			format: "%s%v", // controls output: %s = symbol, %v = value/number (can be object: see below)
			decimal : ".",  // decimal point separator
			thousand: ",",  // thousands separator
			precision : 2   // decimal places
		},
		number: {
			precision : 0,  // default precision on numbers is 0
			thousand: ",",
			decimal : "."
		}
	}

	$(".BtnNext").click(function(e){
		e.preventDefault();
		var searchCustomer = $(".searchCustomer").val();
        var ishold = $(".ishold").val();
        var name_only = $(".name_only").val();


		if (ishold == 1 ) {

			makeProgress(33.3,66.6);
			$('.step_label').text('Sales Order Form 2'); //step 2
			$('.step1').css('overflow',"hidden");
			$('.step1').css('position',"absolute");
			$('.step1').hide('slide', {direction: 'left'}, 1000);
			$('.step2').stop().show('slide', {direction: 'right'}, 1000);

			// $(".BtnNext").prop("disabled",true);
			setTimeout(function(){
				$('.step1').css('overflow',"visible");
				$('.step1').css('position',"static");

			},2000);
		}else{
			$.toast({
			    heading: 'Ooops Sorry!',
			    text: name_only + ' account is on hold. <a href="">View details here</a>.',
                hideAfter: false,
			    icon: 'error',
			    loader: false,  
			    stack: false,
			    position: 'top-center', 
				allowToastClose: true,
				bgColor: '#d9534f',
				textColor: 'white'  
			});
		}
	});
	
	$(".BtnBack2").click(function(e){
		e.preventDefault();

		makeRollback(66.6, 33.3);

		$('.step_label').text('Sales Order Form 1'); //step 1
		$('.step2').hide('slide', {direction: 'right'}, 1000);
		$('.step1').stop().show('slide', {direction: 'left'}, 1000);

		$(".card-body").css("height","315px");
		setTimeout(function(){
			$(".card-body").css("height","auto");
		},1000);

		sum_of_amount = 0; //set to 0 the amount to prevent bubbles
		//console.log(sum_of_amount);

		$(".summary_totalamt").val(sum_of_amount);
	});

	$(".BtnNext, .BtnBack2, .BtnProceed, .BtnForm1").click(function(e){
		e.preventDefault();
		var text_label = $('.step_label').text();
		if (text_label == 'Sales Order Form 1') {

			//$(".label-top_up").text('E-Wallet Encashment');
			$(".BtnNext").prop("hidden",false);
			$(".BtnBack2").prop("hidden",true);
			$(".BtnProceed").prop("hidden",true);
            $(".BtnForm1").prop("hidden",true);

		}else if (text_label == 'Sales Order Form 2') {
			
			//p$(".label-top_up").text('Encashment Summary');
			$(".BtnNext").prop("hidden",true);
			$(".BtnBack2").prop("hidden",false);
			$(".BtnProceed").prop("hidden",false);
            $(".BtnForm1").prop("hidden",false);
			
		}else{

			$(".BtnNext").prop("hidden",true);
			$(".BtnBack2").prop("hidden",true);
			$(".BtnProceed").prop("hidden",true);
            $(".BtnForm1").prop("hidden",false);
		}
		
		$(".BtnNext").prop("disabled",true);
		$(".BtnBack2").prop("disabled",true);
		$(".BtnProceed").prop("disabled",true);
        $(".BtnForm1").prop("disabled",false);

		setTimeout(function(data){
			$(".BtnNext").prop("disabled",false);
			$(".BtnBack2").prop("disabled",false);
			$(".BtnProceed").prop("disabled",false);
            $(".BtnForm1").prop("hidden",false);
			
		},2000);
	});

	/*$(".BtnProceed").click(function(e){
		$(".refNospan").text(generated_reference_no);
	});*/
		
	function getReferenceNo(length) {
		return Math.floor(Math.pow(10, length-1) + Math.random() * 9 * Math.pow(10, length-1));
	}

	$('#table-grid').delegate('.btnViewViolations', 'click', function(data){
		var violations = $(this).data('violation_text');
		$('.violations_modal_span').html(violations);
	});


	function makeProgress(from, to){ //increase

		if(from < to){
			from = from + .20;
			$(".progress-bar").css("width", from + "%");

			
		}
		// Wait for sometime before running this script again
		setTimeout(function(){
			makeProgress(from, to);
		}, 1);
	}
	function makeRollback(from, to){ //decrease
		
		if(from > to){
			from = from - .20;
			$(".progress-bar").css("width", from + "%");

		}
		// Wait for sometime before running this script again
		setTimeout(function(){
			makeRollback(from, to);
		}, 1);
	}

  /*  $("#sales_form").submit(function(e){
        e.preventDefault();

        var serial = $(this).serialize();
        var thiss = $(this);
        $.ajax({
            type:'post',
            url: base_url+'Main_sales/save_sales_order',
            data: serial,
            beforeSend:function(data){
                $('.saveBtnEncode').prop('disabled', true);
                $('.clearBtnEncode').prop('disabled', true);
            },
            success:function(data){
                $('.saveBtnEncode').prop('disabled', false);
                $('.clearBtnEncode').prop('disabled', false);
                if (data.success == 1) {
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
                        hideAfter: 6000
                    });

                    $('.step_label').text('Step 3 to 3'); //step 5
                    makeProgress(66.6,100);

                    $('.step2').css('overflow',"hidden");
                    $('.step2').css('position',"absolute");
                    $('.step2').hide('slide', {direction: 'left'}, 1000);
                    $('.step3').stop().show('slide', {direction: 'right'}, 1000);

                    // $(".BtnNext").prop("disabled",true);
                    setTimeout(function(){
                        $('.step2').css('overflow',"visible");
                        $('.step2').css('position',"static");


                    },2000);

                    $(".BtnNext").prop("hidden",true);
                    $(".BtnBack2").prop("hidden",true);
                    $(".BtnProceed").prop("hidden",true);

                    //to avoid hidden input fields to clear
                    $(thiss).find('input#searchCustomer').val("");
                    $(thiss).find('input#address').val("");
                    $(thiss).find('input#contact_no').val("");
                    $(thiss).find('input#grand_total').val("");
                    $(thiss).find('input#c_notes').val("");
                    $(thiss).find('input#term_credit').val("");
                    //$(thiss).find('input#shipping').val("");
                    $(thiss).find('#shipping_id').val("");
                    $(thiss).find('#location_id').val("");
    
                    dataTable.draw(); //clear the violation table

                    //go to top page when save
                    var offTop = $('body').offset().top;
                    $(window).scrollTop(offTop);
                }else{
                    $.toast({
                        heading: 'Error',
                        text: data.message,
                        icon: 'error',
                        loader: false,   
                        stack: false,
                        position: 'top-center',  
                        bgColor: '#d9534f',
                        textColor: 'white'        
                    });
                }
            }
        });
    });
*/

    //if last step in encashment
    $(".BtnProceed").click(function(e){
        e.preventDefault();

        var shipping_id = $(".shipping_id").val();
        var sales_date = $(".sales_date").val();
        var idno = $(".idno").val();
        var grand_total1 = $(".grand_total1").val();
        var c_notes = $(".c_notes").val();
        var freight = $(".shipping_cost1").val();
        var location_id = $(".location_id").val();
        //dapat pati ung charge makasama sa deduction
        $.ajax({
            type:'post',
            url: base_url+'Main_sales/save_sales_order',
            data:{'shipping_id':shipping_id, 'sales_date': sales_date, 'idno': idno, 
            'grand_total1': grand_total1, 'c_notes': c_notes, 'freight': freight, 'location_id' : location_id},
            success:function(data){
                if (data.success == 1) {
                    
                    getCurrentBalance();

                    $('.step_label').text('Message'); //step 5
                    makeProgress(66.6,100);

                    $('.step2').css('overflow',"hidden");
                    $('.step2').css('position',"absolute");
                    $('.step2').hide('slide', {direction: 'left'}, 1000);
                    $('.step3').stop().show('slide', {direction: 'right'}, 1000);

                    // $(".BtnNext").prop("disabled",true);
                    setTimeout(function(){
                        $('.step2').css('overflow',"visible");
                        $('.step2').css('position',"static");


                    },2000);

                    $(".BtnNext").prop("hidden",true);
                    $(".BtnBack2").prop("hidden",true);
                    $(".BtnForm1").prop("hidden",true);
                    $(".BtnProceed").prop("hidden",true);
                }else{
                    $.toast({
                        heading: 'Error',
                        text: data.message,
                        icon: 'error',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#d9534f',
                        textColor: 'white'  
                    });
                }
            }
        });
    });


	   var shipping = $("#shipping").val();

    accounting.settings = {
        currency: {
            symbol : "",   // default currency symbol is '$'
            format: "%s%v", // controls output: %s = symbol, %v = value/number (can be object: see below)
            decimal : ".",  // decimal point separator
            thousand: ",",  // thousands separator
            precision : 2   // decimal places
        },
        number: {
            precision : 0,  // default precision on numbers is 0
            thousand: ",",
            decimal : "."
        }
    }

    var dataTable = $('#table-grid').DataTable({
        
        "serverSide": true,

        "ajax":{
            url :base_url+"Main_sales/table_sales_session", // json datasource
            type: "post",  // method  , by default get
           // data: {"shipping":shipping},
            
            error: function(){ 
             // error handling
                $(".table-grid-error").html("");
                $("#table-grid_processing").css("display","none");
            }

        },

        "fnDrawCallback": function() {
                    var api = this.api()
                    var json = api.ajax.json();
                    console.log(json);
                  //  alert(json.grand_total_price);
                    //$(".th_total_amount").text(json.totalSum);

                    // var res = json.breakDownSumArr;
                    // var reslen = res.length;
                    // var list = "";
                    // var totalSum = json.totalSum.replace(new RegExp(',', 'g'),"");
                    // var totalbreakdown = 0;
                    trigger_grandtotal(json.grand_total_price);



                  //  trigger_shipping(json.shipping_cost);

                    //total_collection = json.total_collection_amt;
                    
                }
           });

   $("#shipping").bind("change paste keyup", function() {
       var shipping = $("#shipping").val();
    });



    function trigger_grandtotal(grand_total){

        $('#grand_total').val(accounting.formatMoney(grand_total));
        $('#grand_total1').val(grand_total);
        $('#grand_total3').val(grand_total);

       // alert(grand_total);
      //  die();
    }  

    function trigger_shipping(shipping_cost){
        $('#shipping_cost1').val(shipping_cost);
       // $('#grand_total1').val(grand_total);
    }    
    
    $('.search-input-text').on('keyup click', function(){   // for text boxes
        var i =$(this).attr('data-column');  // getting column index
        var v =$(this).val();  // getting search input value
        dataTable.columns(i).search(v).draw();
    });

    $('.search-input-select').on('change', function(){   // for select box
        var i =$(this).attr('data-column');  
        var v =$(this).val();  
        dataTable.columns(i).search(v).draw();
    });

   $(document).on("change",".searchCustomer",function(e){

  
    var sales_id = "";
    var sales_name = "";
    var franchise_id = $("#franchise_id").val();
    var options = {

        url: base_url+'Main_sales/autocomplete_sales',
        getValue: "name",   
        data: {'franchise_id' : franchise_id},

        list: {
            onSelectItemEvent: function() {

                sales_id = $(".searchSalesorder").getSelectedItemData().code;
                $("#searchSalesorderCode_id").val(sales_id);
                sales_name = $(".searchSalesorder").getSelectedItemData().name;
            },
            match: {
                enabled: true
            }
        },
        theme: "square"
    };
    $(".searchSalesorder").easyAutocomplete(options);
  });

  

    $.ajax({
        type:'post',
        url:base_url+'Main_sales/UnsetSessionSalesTable',
        success:function(data){

        }
    });

    $('#table-grid').delegate(".btnDelete","click", function(){
        var sales_id = $(this).data('value');

        $.ajax({
            type:'post',
            url:base_url+'Main_sales/deleteSalesOrderSession',
            data:{'sales_id': sales_id},
            success:function(data){
                dataTable.draw();
            }
        });
    });

// get customer information

    var member_id = "";
    var member_name = "";
    var home_address = "";
    var contact_no = "";
    var mode_payment = "";
    var franchise_id = "";
    var idno = "";
    var options = {

        url: base_url+'Main_sales/autocomplete_customer',
        getValue: "name",
      //  getValue: "address",
        list: {
            onSelectItemEvent: function() {
                member_id = $(".searchCustomer").getSelectedItemData().code;
                home_address = $(".searchCustomer").getSelectedItemData().address;
                contact_no = $(".searchCustomer").getSelectedItemData().contact_no;
                mode_payment = $(".searchCustomer").getSelectedItemData().mode_payment;
                customer_name = $(".searchCustomer").getSelectedItemData().name;
                franchise_id = $(".searchCustomer").getSelectedItemData().franchise_id;
                idno = $(".searchCustomer").getSelectedItemData().idno;
                ishold = $(".searchCustomer").getSelectedItemData().ishold;
                branchname = $(".searchCustomer").getSelectedItemData().branchname;
                name_only = $(".searchCustomer").getSelectedItemData().name_only;


                console.log(franchise_id);
                $("#searchCustomer_id").val(member_id);
                $("#address").val(home_address);
                $("#contact_no").val(contact_no);
                $("#mode_payment").val(mode_payment);    
                $("#franchise_id").val(franchise_id); 
                $("#idno").val(idno); 
                $("#ishold").val(ishold);  
                $("#branchname").val(branchname);
                $("#name_only").val(name_only);
            },
            match: {
                enabled: true
            }

        },
        theme: "square"
    };
    $(".searchCustomer").easyAutocomplete(options);

    $("#step1").clone().prependTo("#receiver");

        $(document).on("change",".searchCustomer",function(e){
           
           var credit_id = $("#mode_payment").val();

            if (credit_id != "") {
                        $.ajax({
                            type:'post',
                            url: base_url+'Main_sales/show_credit_name',
                            data:{"credit_id": credit_id},
                           //  data:{"qty": qty},
                            success:function(data){
                                // alert(dataTable.draw());
                                if (data.success == 1) {
                                    var res = data.result;
                                    $("#term_credit").val(res[0].description);
                                }
                            }

                        });
                    }
        });

    $(".searchCustomer").on("focusout",function() {
        var member_id = $("#searchCustomer_id").val();

        if (member_id == "" || member_id == 0) {
            $(this).val("");
        }

        if ($(this).val() != member_name) {
            $(this).val("");
            $("#searchCustomer_id").val("");
            member_id = "";
        }
    });


  //  $(document).on("change","#searchSalesorder",function(e){
    $(".addSalesOrderEncodeBtn").click(function(e){
        e.preventDefault();
        
        var sa_id = $("#searchSalesorderCode_id").val();
        var qty = $("#qty").val();

        if (sa_id != "") {
            $.ajax({
                type:'post',
                url: base_url+'Main_sales/InsertSessionSalesOrder',
                data:{"sa_id": sa_id, "qty": qty},
               //  data:{"qty": qty},
                success:function(data){
                    // alert(dataTable.draw());
                    $("#searchSalesorderCode_id").val("");
                    $("#searchSalesorder").val("");
                    $("#qty").val("");
                    dataTable.draw();
                }
            });
        }

    });

    $(".clearBtnEncode").click(function(e){
        e.preventDefault();
        $("#encode_form").find('input').val('');
        $.ajax({
            type:'post',
            url:base_url+'Main_sales/UnsetSessionSalesTable',
            success:function(data){

                setTimeout(function(){
                    dataTable.draw();
                },4000)
                
            }
        });

    });

    //var courses = ['1','2'];

    $('#searchCustomer').one('change', function() {
         $('#addsalesorder').prop('disabled', false);
         $('#addShipping').prop('disabled', false);
         $('#shipping_cost1').prop('disabled', false);
         $('#c_notes').prop('disabled', false);
    });

    $('#searchCustomer').one('change', function() {
         $('#addsalesorder').prop('disabled', false);
         $('#addShipping').prop('disabled', false);
         $('#shipping_cost1').prop('disabled', false);
         $('#c_notes').prop('disabled', false);
    });


    
});