$(function(){
	var base_url = $("body").data('base_url'); //url
	var datas = $("body").data('datas'); // data for query
	var search_label = $("body").data('label'); //label search
	var token = $("#hdnToken").val();

    var currentSelectedItemId = ""; //declare data for fetch selection
    var currentSelectedItemName = ""; //declare data for fetch selection
    var currentSelectedUnit = ""; //declare data for fetch selection
    var shipping = 0;
    var itemtotalamtArr = [];
    var errorFound = false;
    var matchFound = false;
    var itemtotalamt_val = 0;
    var newgrandtotal = 0;
    var grand_total = 0;
    var ship_val = 0;
    returnItems = [];

    var table = $('#table-grid').DataTable({ //declaring of table
        columnDefs: [{ targets: [4], visible: true, orderable: false, sClass: 'text-center'}],
        columnDefs: [{ targets: [0], sClass: 'td_id'}],
        columnDefs: [{ "targets": [ 0 ], "visible": false, "searchable": false }, { "targets": [ 3 ], "visible": false }, { "targets": [ 7 ], "visible": false }]
    });//data table

    $( "#shipping" ).val(0);

    function tofixed(x){
        return numberWithCommas(parseFloat(x).toFixed(2));
    }
    function numberWithCommas(x){
      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function populateTable(data){
        table.row.add(selectedDataarray);         
        table.draw();
    }

    //for delete parent row in the table
    $( "#table-grid" ).on('click', '.btnDelete', function(){ 
        table.row($(this).parents('tr')).remove().draw(false); //get the selected row to delete

        var data_totalamt = table.rows().columns(8).data(); // get no.8 data which is the total

        data_totalamt.each(function(value, index){ //fetch array to string sum 
            grand_total = eval(value.join("+").replace(/,/g, '')); //convert array to summation of string without comma
        });

        ship = $("#ship_hide").val();
        genDiscount = $("#hdnGenDiscount").val();
        genDiscountType = $("#hdnGenDiscountType").val();

        $("#grandtotal_hide").val(grand_total);

        if (genDiscountType == 2) {
            discount = parseFloat(grand_total) * (parseFloat(genDiscount) / 100);
        }
        else {
            discount = parseFloat(genDiscount);
        }
        
        var newgrandtotal = (parseFloat(grand_total) - parseFloat(discount)) + parseFloat(ship);

        $(".grand_total").text("Total : "+ accounting.formatMoney(newgrandtotal)); 
        $(".btnShipping").val(ship);
    });

	$( ".select2" ).select2({});

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

    $(document).on( "change", ".searchCustomer", function(e){
        var sino = $( "#searchCustomer" ).val();

        if (sino != "") {
            $.ajax({
                type:'post',
                url: base_url + 'sales/Sales_salesreturn/getSalesInvoice',
                data:{"sino": sino},
                beforeSend:function(data) {
                    $.LoadingOverlay("show"); 
                },
                complete: function() {
                    $.LoadingOverlay("hide"); 
                },
                success:function(data) {
                    var obj = JSON.parse(data);
                    if (obj.discount_type == 2) {
                        $(".btnGenDiscount").text("DISCOUNT: " + obj.gen_discount + "%");
                        discount = parseFloat(obj.totalamt) * (parseFloat(obj.gen_discount) / 100);
                    }
                    else {
                        $(".btnGenDiscount").text("DISCOUNT: " + accounting.formatMoney(obj.gen_discount));
                        discount = parseFloat(obj.gen_discount);
                    }

                    amount = (parseFloat(obj.totalamt) - parseFloat(discount)) + parseFloat(obj.freight);
                    $(".btnShipping").text("SHIPPING: " + accounting.formatMoney(obj.freight));
                    $(".ship_hide").val(obj.freight);

                    $("#address").val(obj.address);
                    $("#contact_no").val(obj.conno);
                    $("#sino").val(sino);
                    $("#idno").val(obj.idno);
                    $("#membername").val(obj.membername);
                    $("#branchname").val(obj.branchname);
                    $("#term_credit").val(obj.description);
                    $("#mode_payment").val(obj.termcredit);
                    $("#amount").val(amount);
                    $("#ispaid").val(obj.ispaid);
                    $("#hdnGenDiscount").val(obj.gen_discount);
                    $("#hdnGenDiscountType").val(obj.discount_type);
                }
            });
        }
    });
        
	$( ".BtnNext" ).click(function(e){
		e.preventDefault();
        var sino = $( "#searchCustomer" ).val();
        var shipping_id = $(".shipping_id").val();
        var sales_date = $(".sales_date").val();
        var location_id = $(".location_id").val();
        var name_only = $("#membername").val();

        if (searchCustomer == "" || shipping_id == "" || location_id == "" || sales_date == "") {
            $.toast({
                heading: 'Warning!',
                text: 'Please fill out required fields',
                icon: 'error',
                loader: false,  
                stack: false,
                position: 'top-center', 
                allowToastClose: true,
                bgColor: '#f0ad4e',
                textColor: 'white' 
            });
        }
        else {
            $('.step_label').text('Step 2'); //step 2
            makeProgress(33.3,66.6);
            $('.step1').css('overflow',"hidden");
            $('.step1').css('position',"absolute");
            $('.step1').hide('slide', {direction: 'left'}, 1000);
            $('.step2').stop().show('slide', {direction: 'right'}, 1000);
            
            $.ajax({
                type:'post',
                url: base_url+'sales/Sales_salesreturn/getSalesInvoiceItems',
                data:{"sino": sino},
                success:function(data) {
                    var obj = JSON.parse(data);

                    var dropdown = $("#selectItem");
                    $.each(obj, function() {
                        dropdown.append($("<option />").val(this.itemid).text(this.itemid + " - " + this.itemname));
                    });

                    setTimeout(function(){
                        $('.step1').css('overflow',"visible");
                        $('.step1').css('position',"static");
                    },2000);
                }
            });
		}
	});

    $( "#selectItem" ).change(function(){
        itemid = $("#selectItem").val();
        sino = $( "#searchCustomer" ).val();

        if (itemid != "") {
            $.ajax({
                type:'post',
                url: base_url+'sales/Sales_salesreturn/getSalesInvoiceItemDetail',
                data:{"sino": sino, "itemid": itemid},
                success:function(data) {
                    var obj = JSON.parse(data);
                    $( "#price" ).val(obj.price);
                    $( "#uomid" ).val(obj.uomid);
                    $( "#uom" ).val(obj.description);
                    $( "#discamt" ).val(obj.discamt);
                    $( "#disctype" ).val(obj.discount_type);
                    $( "#qty" ).val(obj.qty);
                    $( "#qty" ).attr({"max" : obj.qty});
                }
            });
        }
        else {
            $( "#qty" ).val("0");
        }
    });

    $('.sales_date').datepicker({
        format: 'mm/dd/yyyy',
        startDate: '-2d',
        endDate: '+60d'
    });
	
	$(".BtnBack2").click(function(e){
		e.preventDefault();

		makeRollback(66.6, 33.3);

		$('.step_label').text('Step 1'); //step 1
        $('.required_fields').text('Required fields'); //step 1
		$('.step2').hide('slide', {direction: 'right'}, 1000);
		$('.step1').stop().show('slide', {direction: 'left'}, 1000);

		$(".card-body").css("height","315px");
		setTimeout(function(){
			$(".card-body").css("height","auto");
		},1000);

		sum_of_amount = 0; //set to 0 the amount to prevent bubbles

		$(".summary_totalamt").val(sum_of_amount);
	});

	$(".BtnNext, .BtnBack2, .BtnSaveProceed, .BtnForm1, .BtnForm2").click(function(e){
		e.preventDefault();
		var text_label = $('.step_label').text();
		if (text_label == 'Step 1') {
			$(".BtnNext").prop("hidden",false);
			$(".BtnBack2").prop("hidden",true);
			$(".BtnSaveProceed").prop("hidden",true);
            $(".BtnForm1").prop("hidden",true);
            $(".BtnForm2").prop("hidden",true);

		}
        else if (text_label == 'Step 2') {	
			$(".BtnNext").prop("hidden",true);
			$(".BtnBack2").prop("hidden",false);
			$(".BtnSaveProceed").prop("hidden",false);
            $(".BtnForm1").prop("hidden",false);
            $(".BtnForm2").prop("hidden",false);
            $(".required_fields").prop("hidden",true);	
		}
        else {
			$(".BtnNext").prop("hidden",true);
			$(".BtnBack2").prop("hidden",true);
			$(".BtnSaveProceed").prop("hidden",true);
            $(".BtnForm1").prop("hidden",false);
            $(".BtnForm2").prop("hidden",false);
		}
		
		$(".BtnNext").prop("disabled",true);
		$(".BtnBack2").prop("disabled",true);
		$(".BtnSaveProceed").prop("disabled",true);
        $(".BtnForm1").prop("disabled",false);
        $(".BtnForm2").prop("disabled",false);

		setTimeout(function(data) {
			$(".BtnNext").prop("disabled",false);
			$(".BtnBack2").prop("disabled",false);
			$(".BtnSaveProceed").prop("disabled",false);
            $(".BtnForm1").prop("hidden",false);
            $(".BtnForm2").prop("hidden",false);
			
		},2000);
	});

	function makeProgress(from, to) { //increase
		if(from < to){
			from = from + .20;
			$(".progress-bar").css("width", from + "%");
		}
		// Wait for sometime before running this script again
		setTimeout(function(){
			makeProgress(from, to);
		}, 1);
	}

	function makeRollback(from, to) { //decrease
		if(from > to){
			from = from - .20;
			$(".progress-bar").css("width", from + "%");

		}
		// Wait for sometime before running this script again
		setTimeout(function(){
			makeRollback(from, to);
		}, 1);
	}

    //add item inside modal
    $(".addSalesOrderEncodeBtn").click(function(e){
        checkInputs('#addRow');
        var quantity = $("#qty").val();
        var item = $("#selectItem").val();
        var str = $( "#selectItem option:selected" ).text();
        var res = str.split( " - " );
        var itemname = res[1];
        var price = $("#price").val();
        var uomid = $("#uomid").val();
        var uom = $("#uom").val();
        var discamt = $("#discamt").val();
        var disctype = $("#disctype").val();
        var returnType = $("#returnType").val();
        currentSelectedItemId = item;
        currentSelectedItemName = itemname;
        currentSelectedUnit = uom;

        //check if not empty fields
        if (currentSelectedItemId == "" || currentSelectedItemName == "" || currentSelectedUnit == null || quantity == "" || quantity == 0 || returnType == "") {
            clearAddform();

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
        }
        else {
            matchFound = false;

            var dataIDstackup = table.rows().columns(0).data();
            tableID = [];

            dataIDstackup.each(function (value, index){    
                tableID = tableID.concat(value);
            });//columns COLUMN TO CHANGE/GET VALUE data VALUE OF CELL row CURRENT ROW IN THE LOOP 

            for(i = 0; i < tableID.length; i++){
                if(tableID[i] == currentSelectedItemId) {
                    deleteMatchRow(tableID[i]);
                }
                else {
                    matchFound = false;
                }

            }//columns COLUMN TO CHANGE/GET VALUE data VALUE OF CELL row CURRENT ROW IN THE LOOP     

            if(matchFound == false) {
                // Adding Discount in Order Form (percent/whole numbers) 

                var total =  parseFloat(quantity) * parseFloat(price);
                var totalarray = [];

                if (disctype == 2) {
                    discount = parseFloat(total) * (parseFloat(discamt) / 100);
                    discountText = discamt + "%";
                }
                else {
                    discount = parseFloat(discamt);
                    discountText = formatMoney(discamt);
                }

                itemTotal = parseFloat(total) - parseFloat(discount);

                //for fetching of rows in the table, prepare your data.
                selectedDataarray = [
                    currentSelectedItemId,
                    currentSelectedItemName.toUpperCase(),
                    accounting.formatMoney(quantity),
                    uomid,
                    currentSelectedUnit,
                    accounting.formatMoney(price),
                    discountText,
                    disctype,
                    accounting.formatMoney(itemTotal),
                    returnType,
                    "<center><button class='btn btn-danger btnDelete btnTable'><i class='fa fa-trash-o'></i> Delete</button></center>"
                ];// adding selected data to array 

                populateTable(selectedDataarray);    
            }

            //start - to get total amount in the specific column
            var data_totalamt = table.rows().columns(8).data();

            data_totalamt.each(function(value, index){ 
                grand_total = eval(value.join("+").replace(/,/g, ''));
            });

            //add shipping
            var ship_val = $(".ship_hide").val();
            genDiscount = $("#hdnGenDiscount").val();
            genDiscountType = $("#hdnGenDiscountType").val();

            $("#grandtotal_hide").val(grand_total);

            if (genDiscountType == 2) {
                discount = parseFloat(grand_total) * (parseFloat(genDiscount) / 100);
            }
            else {
                discount = parseFloat(genDiscount);
            }
            
            var newgrandtotal = (parseFloat(grand_total) - parseFloat(discount)) + parseFloat(ship_val);

            $(".grand_total").text("Total : "+ accounting.formatMoney(newgrandtotal));
            $(".grandtotal_hide").val(newgrandtotal);
            //end - to get total amount in the specific column

            $('#viewAddrowModal').modal('toggle'); //close modal

            clearAddform(); //clear all forms                        
        }
    });

    //adding shipping amount
    $(".btnAddShipping").click(function(e){
        var shipping = $("#shipping").val();
        var grandtotal = $("#grandtotal_hide").val();
        var ship_hide = $("#ship_hide").val();

        genDiscount = $("#hdnGenDiscount").val();
        genDiscountType = $("#hdnGenDiscountType").val();

        $("#grandtotal_hide").val(grand_total);

        if (genDiscountType == 2) {
            discount = parseFloat(grand_total) * (parseFloat(genDiscount) / 100);
        }
        else {
            discount = parseFloat(genDiscount);
        }
        
        var newgrandtotal = (parseFloat(grand_total) - parseFloat(discount)) + parseFloat(shipping);

        $(".grand_total").text("Total : "+ accounting.formatMoney(newgrandtotal));
        
        var ship_amt = $(".btnShipping").text("Shipping: " + accounting.formatMoney(shipping));
        
        $(".ship_hide").val(shipping);
    });

    //merge if match row inserted then add the quantity of item, overwrite the discount
    function deleteMatchRow(matchValue){
        var filteredData = table.rows().indexes().filter(function(value, index) {
            return table.row(value).data()[0] == matchValue; 
        });

        table.rows(filteredData).remove().draw();
    }

    $(".BtnSaveProceed").click(function(e){
        e.preventDefault();

        var shipping = $(".ship_hide").val();
        var sino = $(".sino").val();

        var data = table.rows().data();
        data.each(function (value, index) {
            entry = {
                itemid: value[0],
                price: value[5],
                qty: value[2],
                itemname: value[1],
                uomid: value[3],
                uom: value[4],
                subtotal: value[8],
                returnType: value[9]
            }

            returnItems.push(entry);
        });

        if (grand_total == 0) {
            $.toast({
                heading: 'Note',
                text: 'Must have at least one sales return.',
                icon: 'error',
                loader: false,  
                stack: false,
                position: 'top-center', 
                allowToastClose: true,
                bgColor: '#f0ad4e',
                textColor: 'white' 
            });
        }
        else {
            $.ajax({
                url: base_url+"sales/Sales_salesreturn/saveSalesReturn",
                type: 'post',
                data: { 
                    'returnItems': returnItems, 
                    'returnTotal': grand_total, 
                    'freight': shipping,
                    'sino': sino,
                    'idno': $("#idno").val(),
                    'itemlocid': $("#location_id").val(),
                    'shipping_id': $("#shipping_id").val(),
                    'sales_date': $("#sales_date").val(),
                    'notes': $("#notes").val()
                },
                beforeSend: function() {
                    $.LoadingOverlay("show");
                },
                success: function(data) {
                    $.LoadingOverlay("hide");
                    if (data.success == 1) {
                        $('.step_label').text(''); //step 5
                        makeProgress(66.6,100);

                        $('.step2').css('overflow',"hidden");
                        $('.step2').css('position',"absolute");
                        $('.step2').hide('slide', {direction: 'left'}, 1000);
                        $('.step3').stop().show('slide', {direction: 'right'}, 1000);

                        // $(".BtnNext").prop("disabled",true);
                        setTimeout(function(){
                            $('.step2').css('overflow',"visible");
                            $('.step2').css('position',"static");
                        }, 2000);
                        $(".required_fields").prop("hidden",true);
                        $(".BtnNext").prop("hidden",true);
                        $(".BtnBack2").prop("hidden",true);
                        $(".BtnForm1").prop("hidden",true);
                        $(".BtnForm2").prop("hidden",true);
                        $(".BtnSaveProceed").prop("hidden",true);

                        $.toast({
                            heading: 'Success',
                            text: 'Sales Return has been successfully saved.',
                            icon: 'success',
                            loader: false,  
                            stack: false,
                            position: 'top-center', 
                            bgColor: '#5cb85c',
                            textColor: 'white',
                            allowToastClose: false,
                            hideAfter: 3000
                        });
                    }
                    else {
                        $.toast({
                            heading: 'Note',
                            text: 'Sales Return has not been saved.',
                            icon: 'error',
                            loader: false,  
                            stack: false,
                            position: 'top-center', 
                            allowToastClose: true,
                            bgColor: '#f0ad4e',
                            textColor: 'white' 
                        });
                    }
                }
            });
        }   
    });

    $('#qty').on('keydown keyup', function(e){
        if ($(this).val() > parseInt($(this).attr('max')) 
            && e.keyCode !== 46 // keycode for delete
            && e.keyCode !== 8 // keycode for backspace
           ) {
           e.preventDefault();
           $(this).val($(this).attr('max'));
        }
    });

    function checkInputs(formname){
        $(formname).find('.required_fields').each(function(){ //loop all input field then validate
            if ($(this).val() == ""){
                $(this).css("border-color", "#d9534f"); //change all empty to color red
            }else{
                $(this).css("border-color", "#eee");  //rollback when not empty
                errorFound = false;
            }
        });

        $(formname).find('.required_fields').each(function(){ //loop all input field then validate
            if ($(this).val() == ""){ // if empty show error
                flag = false; //update error to 1
                // $(this).css("border-color","#d9534f");
                $(this).css("border-color", "#d9534f"); //change all empty to color red
                $(this).focus();

                $.toast({
                heading: 'Note',
                text: 'Please fill out this field',
                icon: 'error',
                loader: false,   
                stack: false,
                position: 'top-center',     
                bgColor: '#f0ad4e;',
                textColor: 'white'
                });
                errorFound = true;
                return false; //focus first empty fields
            }else{
                errorFound = false;
                flag = true;
            }
        });

        if(errorFound == false){
            $(formname).find('.qty').each(function(){ //loop all input field then validate
                if (($(this).val() <= 0) || ($(this).val() == '.')){ // if empty show error
                    flag = false; //update error to 1
                    
                    $(this).css("border-color", "#d9534f"); //change all empty to color red
                    $(this).focus();

                    $.toast({
                    heading: 'Note',
                    text: 'Quantity must not be less than zero',
                    icon: 'error',
                    loader: false,   
                    stack: false,
                    position: 'top-center',     
                    bgColor: '#f0ad4e;',
                    textColor: 'white'
                    });

                    return false; //focus first empty fields

                }else{
                    flag = true;
                    $(this).css("border-color", "#eee");  //rollback when not empty
                }
            }); 
        }
    }

    //clear all form function, please add/change other input to clear if needed
    function clearAddform(){
        $("#qty").css("border-color", "#eee");  //rollback when not empty
        $("#addAOrderItemModal").modal('hide');
        $('#selectItem').val("").change();
        $('#returnType').val("").change();
    }

    //allowing numeric with decimal 
    $(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    //allowing numeric without decimal 
    $(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    // redirect to credit memo
    $("#btnCreditMemo").on("click", function (e) {
        e.preventDefault();
        window.open(base_url+"Main_sales/credit_memo/" + token + "/" + $("#idno").val(), '_self');
    });
    //<?=base_url('Main_sales/credit_memo/'.$token);?>

});