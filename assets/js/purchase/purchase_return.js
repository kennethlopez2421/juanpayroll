$(function(){
	var base_url = $("body").data('base_url'); //url
	var datas = $("body").data('datas'); // data for query
    var search_label = $("body").data('label'); //label search
    prItems = [];
	grandtotal = 0;

	$(".BtnNext").click(function(e){
		e.preventDefault();
		var searchSupplier = $(".searchSupplier").val();
        var searchAddress = $(".searchAddress").val();

        if (searchSupplier == "" || searchAddress == "") {

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
            makeProgress(33.3,66.6);
            $('.step_label').text('Step 2'); //step 2
            $('.step1').css('overflow',"hidden");
            $('.step1').css('position',"absolute");
            $('.step1').hide('slide', {direction: 'left'}, 1000);
            $('.step2').stop().show('slide', {direction: 'right'}, 1000);

            // $(".BtnNext").prop("disabled",true);
            setTimeout(function(){
                $('.step1').css('overflow',"visible");
                $('.step1').css('position',"static");

            },2000);
        }
    });
    
    // initialize datatable
  	var table = $('#table-grid').DataTable({ //declaring of table
        destroy: true,
        columnDefs: [{ targets: [10], visible: true, orderable: false, sClass: 'text-center'}],
        columnDefs: [{ targets: [0], sClass: 'td_id'}],
        columnDefs: [{ "targets": [ 0 ], "visible": false, "searchable": false }, { "targets": [ 3 ], "visible": false }, { "targets": [ 7 ], "visible": false }, { "targets": [ 8 ], "visible": false }]
    });//data table

    ////////////////////////
    //   Auto complete    //
    ////////////////////////

    //function to hightlight the text matched in autocomplete
    function highlightText(text, $node){ 
        var searchText = $.trim(text).toLowerCase(), currentNode = $node.get(0).firstChild, matchIndex, newTextNode, newSpanNode;
        while ((matchIndex = currentNode.data.toLowerCase().indexOf(searchText)) >= 0) {
            newTextNode = currentNode.splitText(matchIndex);
            currentNode = newTextNode.splitText(searchText.length);
            newSpanNode = document.createElement("span");
            newSpanNode.className = "highlight";
            currentNode.parentNode.insertBefore(newSpanNode, currentNode);
            newSpanNode.appendChild(newTextNode);
        }
    }

    //to remove when the input doesn't select in the autocomplete
    $("#searchInventory").keyup(function(){
        $("#searchInventory_id").val("");
        $("#price").val("");

        if ($("#searchInventory_id").val() == "" && $("#price").val() == "") { //for remove loading
            $(this).css("cssText", "background-image: url('');");
        }
    });
    // to remove when the id is empty
    $("#searchInventory").focusout(function(){
        if ($("#searchInventory_id").val() == "" && $("#price").val() == "") { 
            $(this).val("");
            $(this).css("cssText", "background-image: url('');"); //for remove loading
        }
    });

    // An obbject/map for search term/results tracking
    var vendorCache = {};

    // Keep track of the current AJAX request
    var vendorXhr;

    //autocomplete plugin with ajax 
    $("#searchInventory").autocomplete({
        source: function(request, response){
            var typing = $("#searchInventory").val();
            var supplierid = $(".searchSupplier").val();

            //ajax to fetch data in autocomplete

            // Check if we already searched and map the existing results
            // into the proper autocomplete format
            if (request.term in vendorCache) {
                $("#searchInventory").css("cssText", "background-image: url('"+base_url+"assets/img/autocomplete_loading.gif');");
                response($.map(vendorCache[request.term], function (m) {
                    return { 
                        label: m.name, //name = key of array for label
                        id: m.code, // for id
                        price: m.price, // for price
                        unit: m.unit, // for unit
                        unitid: m.unitid // for unit
                    };
                }));
                setTimeout(function(){
                    $("#searchInventory").css("cssText", "background-image: url('');");
                },500);
                
                return;
            }

            // search term wasn't cached, let's get new results
            vendorXhr = $.ajax({
                type: "POST",
                url: base_url+'Main_purchase/get_inventory_item',
                dataType: "json",
                data:{'texttyped':typing, 'supplierid':supplierid},
                beforeSend:function(data){
                    $("#searchInventory").css("cssText", "background-image: url('"+base_url+"assets/img/autocomplete_loading.gif');");
                },
                success: function (data, status, xhr){    
                    $("#searchInventory").css("cssText", "background-image: url('');");
                    // cache the results
                    vendorCache[request.term] = data.result;

                    // if this is the same request, return the results
                    if (xhr === vendorXhr) {
                        // data is an array of objects and must be transformed for autocomplete to use
                        var array = $.map(data.result, function(m) {
                            return {
                                label: m.name, //name = key of array for label
                                id: m.code, // for id
                                price: m.price, // for price
                                unit: m.unit, // for unit
                                unitid: m.unitid // for unit
                            };
                        });

                        response(array);
                    }                                             
                }
            });
        },

        select: function (event, ui) { //to get id of an item
            $("#searchInventory_id").val(ui.item.id);
            $("#supplierid").val(ui.item.supplierid);
            $("#price").val(ui.item.price);
            $("#unit").val(ui.item.unit);
            $("#unitid").val(ui.item.unitid);
            get_discount(ui.item.id);
        }

    }).data("ui-autocomplete")._renderItem = function(ul, item){ // create highlighted 
        var $div = $("<div></div>").text(item.label);
        highlightText(this.term, $div);
        return $("<li></li>").append($div).appendTo(ul);
    };  

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

    function get_discount(val){
        var itemid = val;  
        var idno = $("#supplierid").val();
        if (itemid != "") 
        {
            $.ajax({
                type:'post',
                url: base_url+'Main_purchase/get_supplier_discount',
                data:{"itemid": itemid, "idno": idno},
                beforeSend:function(data){
                    $(".select_disc").LoadingOverlay("show"); 
                },
                complete: function(){
                    $(".select_disc").LoadingOverlay("hide"); 
                },
                success:function(data){
                    if (data.success == 1){
                        var res = data.result;
                        var disctype = data.disctype;
    
                        //show discount
                        if (disctype == 1) {
                            $(".amount_div").show();
                            $("#disc_amt").val(res);
                            $("#discount_type_select").val(1);
                            $(".percentage_div").hide();
    
                        }else if (disctype == 2) {
                            $(".percentage_div").show();
                            $("#disc_percent").val();
                            $("#disc_percent").val(res);
                            $("#discount_type_select").val(2);
                            $(".amount_div").hide();
    
                        }else{
                            $(".amount_div").hide();
                            $("#disc_amt").val();
                            $("#disc_percent").val();
                            $(".percentage_div").hide();
                        }    
                    }
                }
            });
        }
    }
    
    ////////////////////////////
    //   Auto complete end    //
    ////////////////////////////

    function clearAddform(){
        $("#qty").css("border-color", "#eee");  //rollback when not empty
        $("#qty").val('');
        $("#searchInventory_id").val('');
        $("#disc_amt").val("");
        $("#disc_percent").val("");
        $("#discount_type_select").val("").change();
        $(".percentage_div").hide();
        $(".amount_div").hide();
    }

    // choose discount type select hide show the field
    $(".discount_type_select").change(function() {
        var val = $(this).val();

        if (val == 1) {
            $(".amount_div").show();
            $("#disc_amt").val(0);
            $(".percentage_div").hide();

        }else if (val == 2) {
            $(".percentage_div").show();
            $("#disc_percent").val(0);
            $(".amount_div").hide();

        }else{
            $(".amount_div").hide();
            $("#disc_amt").val(0);
            $("#disc_percent").val(0);
            $(".percentage_div").hide();
        }
    });

    // choose general discount type select hide show the field
    $(".discount_gen_type_select").change(function() {
        var val = $(this).val();

        if (val == 1) {
            $(".amount_div").show();
            $("#disc_gen_amt").val("");
            $(".percentage_div").hide();

        }
        else if (val == 2) {
            $(".percentage_div").show();
            $("#disc_gen_percent").val("");
            $(".amount_div").hide();

        }
        else {
            $(".amount_div").hide();
            $("#disc_gen_amt").val("");
            $("#disc_gen_percent").val("");
            $(".percentage_div").hide();
        }
    });

    //for delete parent row in the table
    $( "#table-grid" ).on('click', '.btnDelete', function() { 
        table.row($(this).parents('tr')).remove().draw(false); //get the selected row to delete
    });

	$( '#table-grid' ).delegate(".btnDelete", "click", function() {
		id = $(this).data('id');
        prItems.splice(id, 1);
        updateTotal();
        populateTable();
    });

    // Add item to returns
    $(".addPurchaseOrderEncodeBtn").on("click", function(){
        item = $("#searchInventory").val();
        itemid = $("#searchInventory_id").val();
        supplierid = $("#supplierid").val();
        price = $("#price").val();
        unit = $("#unit").val();
        uomid = $("#unitid").val();
        qty = $("#qty").val();
        disctype = $("#discount_type_select").val();

        if (item != "" && qty != "") {
            if (prItems.length >= 1) {
                for(var a = 0; a < prItems.length; a++){
                    if (prItems[a].itemid == itemid) {
					    prItems.splice(a, 1);
                    }
                }
            }
            
            subtotal = parseFloat(price) * parseFloat(qty);

            if (disctype == "") {
                discount = 0;
            }
            else if (disctype == "1") {
                discamt = $("#disc_amt").val();
                discount = parseFloat(discamt);
            }
            else if (disctype == "2") {
                discamt = $("#disc_percent").val();
                discount = parseFloat(subtotal) * (parseFloat(discamt) / 100);
            }

            discountedTotal = parseFloat(subtotal) - parseFloat(discount);

            if (discountedTotal > 0) {
                data = {
                    itemid: itemid,
                    itemname: item,
                    qty: qty,
                    uomid: uomid,
                    unit: unit,
                    price: price,
                    discamt: discount,
                    disctype: disctype,
                    subtotal: subtotal,
                    total: discountedTotal
                }

                prItems.push(data);
                console.log(prItems);
                clearAddform();
                updateTotal();
                populateTable();
            }
            else {
                $.toast({
                    heading: 'Warning!',
                    text: 'Invalid total amount.',
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

  	// function for binding and refreshing datatable data
    function populateTable(){
    	table.clear();
    	for(var a = 0; a < prItems.length; a++){
    		if (prItems[a].disctype == 2) {
    			discount = prItems[a].discamt + "%";
    		}
    		else {
    			discount = accounting.formatMoney(prItems[a].discamt)
    		}
			selectedDataarray = [
                prItems[a].itemid,
                prItems[a].itemname.toUpperCase(),
                accounting.formatMoney(prItems[a].qty),
                prItems[a].uomid,
                prItems[a].unit,
                accounting.formatMoney(prItems[a].price),
                discount,
                prItems[a].disctype,
                accounting.formatMoney(prItems[a].subtotal),
                accounting.formatMoney(prItems[a].total),
                "<center><button class='btn btn-danger btnDelete btnTable' data-value='" + a + "'><i class='fa fa-trash-o'></i> Delete</button></center>"
            ];// adding selected data to array 

        	table.row.add(selectedDataarray);   
		}        
        table.draw();
    }

    // function for updating total
    // mostly called when changing the release quantity and shipping amount
    function updateTotal() {
		grandTotal = 0;
		gendiscount = $("#gendiscount").val();
		gendisctype = $("#gendiscounttype").val();
		shippingamt = $("#shippingamt").val();

		$.each(prItems, function(index, value) { 
		    var total = parseFloat(value.total);
	        grandTotal += total;
		});
		$("#totalamt").val(grandTotal);

		if (gendisctype == 2) {
			grandDiscount = parseFloat(grandTotal) * (parseFloat(gendiscount) / 100);
		}
		else {
			grandDiscount = gendiscount;
		}

		discountedGrandTotal = (parseFloat(grandTotal) - parseFloat(grandDiscount)) + parseFloat(shippingamt);

		if (discountedGrandTotal <= 0) {
			discountedGrandTotal = 0;
		}

		if (grandTotal > 0) {
			$(".btnProceed").prop('disabled',false);
		}
		else {
			$(".btnProceed").prop('disabled',true);
		}

		$(".btnGrandtotal").html("TOTAL: " + formatMoney(discountedGrandTotal));
    }
    
    $(".btnShip").click(function(e){
		$("#shipping").val($("#shippingamt").val());
	});
	
	// update the shipping amount and grand total
	$(".btnassignShip").click(function(e){
        e.preventDefault();
        
		var shipamt = $("#shipping").val();

		if(shipamt == "") {
			shipamt = 0;
		}

		$("#shippingamt").val(shipamt);
		$(".btnShipping").text("Shipping : " + formatMoney(shipamt,2, ".", ","));
		updateTotal();
	});
	
	// update the general discount amount and grand total
	$(".btnGeneralDiscount").click(function(e){
        e.preventDefault();
        
		var gendisctype = $("#discount_gen_type_select").val();

		if(gendisctype == "") {
			gendiscamt = 0;
        }
        else if (gendisctype == "1") {
            gendiscamt = $("#disc_gen_amt").val();
            $(".btnGenDiscount").text("Discount : " + formatMoney(gendiscamt,2, ".", ","));
        }
        else if (gendisctype == "2") {
            gendiscamt = $("#disc_gen_percent").val();
            $(".btnGenDiscount").text("Discount : " + formatMoney(gendiscamt,2, ".", ",") + "%");
        }

		$("#gendiscount").val(gendiscamt);
		$("#gendiscounttype").val(gendisctype);
		updateTotal();
    });

    $(" .BtnProceed ").on("click", function(){
        if (grandTotal == 0) {
            $.toast({
			    heading: 'Note:',
			    text: "No item to return.",
			    icon: 'info',
			    loader: false,   
			    stack: false,
			    position: 'top-center',  
			    bgColor: '#FFA500',
				textColor: 'white',
				allowToastClose: false,
				hideAfter: 5000          
			});
        }
        else {
            $.ajax({
                type : 'post',
                url : base_url+'purchase/PR/table_save_addpurchasereturn',
                data:{
                    'supplierid'        : $(".searchSupplier_id").val(), 
                    'prItems'           : prItems,
                    'idno'              : $(".idno").val(),
                    'suppAddress'       : $("#supp_address").val(),
                    'locid'             : $("#searchAddress").val(),
                    'shippingid'        : $("#shipping_id").val(),
                    'address'           : $(".searchAddress").val(),
                    'purchase_date'     : formatDate($(".purchase_date").val()),
                    'totalamt'          : grandTotal,
                    'notes'             : $("#notes").val(),
                    'gendiscount'       : $("#gendiscount").val(),
                    'gendisctype'       : $("#gendiscounttype").val(),
                    'shippingamt'       : $("#shippingamt").val()
                },
                beforeSend:function(data) {
                    $.LoadingOverlay("show");
                },
                success:function(data) {
                    $.LoadingOverlay("hide");
                    if (data.success == 1) {
                        getCurrentBalance();

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
                        },2000);
                        
                        $(".required_fields").prop("hidden",true);
                        $(".BtnNext").prop("hidden",true);
                        $(".BtnBack2").prop("hidden",true);
                        $(".BtnForm1").prop("hidden",true);
                        $(".BtnProceed").prop("hidden",true);

                        $.toast({
                            heading: 'Success',
                            text: 'Purchase Return has been successfully saved.',
                            icon: 'success',
                            loader: false,  
                            stack: false,
                            position: 'top-center', 
                            bgColor: '#5cb85c',
                            textColor: 'white',
                            allowToastClose: false,
                            hideAfter: 3000
                        });
                        
                        $.LoadingOverlay("hide"); 
                    }
                }
            });
        }
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
    
    /////
	
	$(".BtnBack2").click(function(e){
		e.preventDefault();

		makeRollback(66.6, 33.3);

		$('.step_label').text('Step 1'); //step 1
		$('.step2').hide('slide', {direction: 'right'}, 1000);
		$('.step1').stop().show('slide', {direction: 'left'}, 1000);

		$(".card-body").css("height","315px");
		setTimeout(function(){
			$(".card-body").css("height","auto");
		},1000);

		sum_of_amount = 0; //set to 0 the amount to prevent bubbles

		$(".summary_totalamt").val(sum_of_amount);
	});

    $(".BtnNext, .BtnBack2, .BtnProceed, .BtnForm1").click(function(e){
        e.preventDefault();
        var text_label = $('.step_label').text();

        if (text_label == 'Step 1') {
            //$(".label-top_up").text('E-Wallet Encashment');
            $(".BtnNext").prop("hidden",false);
            $(".BtnBack2").prop("hidden",true);
            $(".BtnProceed").prop("hidden",true);
            $(".BtnForm1").prop("hidden",true);
        }
        else if (text_label == 'Step 2') {
            //p$(".label-top_up").text('Encashment Summary');
            $(".BtnNext").prop("hidden",true);
            $(".BtnBack2").prop("hidden",false);
            $(".BtnProceed").prop("hidden",false);
            $(".BtnForm1").prop("hidden",false);
            
        }
        else {
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

    $("#shipping").bind("change paste keyup", function() {
       var shipping = $("#shipping").val();
    });

    function trigger_grandtotal(grand_total){
        var shipping_cost = $('#shipping_cost').val();

        if (shipping_cost == ""){
            grand_total_wship = parseFloat(grand_total) + shipping_cost;
            
        }
        else {
            grand_total_wship = parseFloat(grand_total) + parseFloat(shipping_cost);
        }

        $('#grand_total').val(accounting.formatMoney(grand_total_wship));
        $('#grand_total1').val(parseFloat(grand_total_wship) || 0);
        $('#grand_total3').val(parseFloat(grand_total));
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

    // get customer information

    var address_id = "";
    var address_name = "";
    var options = {

        url: base_url+'Main_purchase/autocomplete_address',
        getValue: "name",
        list: {

            onSelectItemEvent: function() {
                address_id = $(".searchAddress").getSelectedItemData().code;
                address_name = $(".searchAddress").getSelectedItemData().name;

                //console.log(franchise_id);
                $("#searchAddress_id").val(address_id);
            },
            hideAnimation: {
                type: "slide", //normal|slide|fade
                time: 300,
                callback: function() {}
            },
            match: {
                enabled: true
            }

        },
        theme: "square"
    };

    $(".searchAddress").easyAutocomplete(options);

    $(".searchAddress").on("focusout",function() {
        var address_id = $("#searchAddress_id").val();

        if (address_id == "" || address_id == 0) {
            $(this).val("");
        }

        if ($(this).val() != address_name) {
            $(this).val("");
            $("#searchAddress_id").val("");
            address_id = "";
        }
    });

    $(document).on("change",".searchSupplier",function(e){
        var supplierid = $("#searchSupplier").val();

        if (supplierid != "") {
            $.ajax({
                type:'post',
                url: base_url+'Main_purchase/show_supplier_info',
                data:{"supplierid": supplierid},
                beforeSend:function(data) {
                    $.LoadingOverlay("show"); 
                },
                complete: function() {
                    $.LoadingOverlay("hide"); 
                },
                success:function(data){
                    if (data.success == 1) {
                        var res = data.result;
                        $("#txtResult").html(res.data[1]);
                    }
                }

            });
        }
    });

    $('#searchCustomer').on('change', function() {
        $('#addpurchaseorder').prop('disabled', false);
        $('#addShipping').prop('disabled', false);
        $('#shipping_cost1').prop('disabled', false);
        $('#c_notes').prop('disabled', false);
    });

    $('.purchase_date').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '-2d',
            endDate: '+60d'
       // dataTable.columns(i).search(v).draw();
    });

    ///////////saving

    $(".BtnProceeds").click(function(e){
        e.preventDefault();
        
        var rowrec = $("#rowrec").val();
        var checker =0;
        if(rowrec == 1) {
            var supplierid = $(".searchSupplier_id").val();
            var shipping_id = $(".shipping_id").val();
            var purchase_date = $(".purchase_date").val();
            var idno = $(".idno").val();
            var totalamt = $(".item_total").val();
            var notes = $(".notes").val();
            var freight = $(".shippingamt").val();
            var locationid1 = $(".searchAddress").val();
            //var locationid1 = $(".searchAddress").val();


            var rowrec = $("#rowrec").val(); // validation
            var totaldata = $("#tdata").val(); // validation

            var itemarray = [];
            var qtyarray = [];
            var pricearray = [];
            var uomidarray = [];

            itemarray = [];
            qtyarray = [];
            pricearray = [];
            uomidarray = [];
            
            if(rowrec == 1)
                {
                    for(i = 0; i < totaldata ; i++)
                    {
                        var citemid = $("#citemid"+i).val();
                        var cqty = $("#cqty"+i).val();
                        var price_count = $("#cprice"+i).val();
                        var uomid_count = $("#cuomid"+i).val();

                         if(citemid > 0)
                         {
                            itemarray.push(citemid);
                            qtyarray.push(cqty);
                            pricearray.push(price_count);
                            uomidarray.push(uomid_count);
                            //checker=1;
                          }
                    }
                
                }
                           
                        $.ajax({
                            type:'post',
                            url:base_url+'Main_purchase/table_save_addpurchasereturn',
                            data:{       
                            "shipping_id": shipping_id,
                            "purchase_date": purchase_date,
                            "idno": idno,
                            "totalamt": totalamt,
                            "freight": freight,    
                            "itemarray":itemarray,
                            "qtyarray":qtyarray,
                            "pricearray":pricearray,
                            "notes":notes,
                            "uomidarray":uomidarray,
                            "supplierid":supplierid,
                            "locationid1":locationid1,
                            //"locationid1":locationid1
                            },
                            success:function(data){
                            if(data.success == 1)
                                {
                                            getCurrentBalance();

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


                                            },2000);
                                            $(".required_fields").prop("hidden",true);
                                            $(".BtnNext").prop("hidden",true);
                                            $(".BtnBack2").prop("hidden",true);
                                            $(".BtnForm1").prop("hidden",true);
                                            $(".BtnProceed").prop("hidden",true);

                                    $.toast({
                                        heading: 'Success',
                                        text: 'Purchase Return has been successfully saved.',
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
                                
                            }
                        });
              //  }
            }        
    });

});

function formatMoney(n,c, d, t) {
    c = isNaN(c = Math.abs(c)) ? 2 : c;
    d = d == undefined ? "." : d;
    t = t == undefined ? "," : t; 
    s = n < 0 ? "-" : "";
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "";
    j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

$('#addAPurchaseItemModal').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
});

function isNumberKeyOnly(evt) {    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}

// $(".btnassignShip").click(function(e){
// e.preventDefault();
//     var ship_grandtotal=0;
//     var shipamt = $("#shipping").val();
//     var grand_total1 = $("#grand_total2").val();
//     var shippinginput =  $("#shippingamt").val();

//     if((shippinginput > 0) && (shippinginput != 0)) 
//     {
//          var ship_grandtotal1  =  parseFloat(shipamt) + parseFloat(grand_total1);
//          ship_grandtotal = parseFloat(ship_grandtotal1) - parseFloat(shippinginput) ;
//     }
//     else
//     {
//         ship_grandtotal  =  parseFloat(shipamt) + parseFloat(grand_total1);
//     }

//     $("#shippingamt").val(shipamt);

    
//     document.getElementById('btnShipping').innerText = "Shipping: " + formatMoney(shipamt,2, ".", ",");
//      $("#grand_total2").val(ship_grandtotal);
//      $("#grand_total").text("Total : "+formatMoney(ship_grandtotal,2, ".", ","));

//      $("#shippingamt").val(shipamt);
     
//     });

$(".btn.disabled").toggleClass('disable');

$(window).load(function(){
    $('.btnassignShip').attr('disabled', true);
    $('#shipping').on('keyup',function() {
        if($(this).val() != '') {
            $('.btnassignShip').attr('disabled' , false);
        }else{
            $('.btnassignShip').attr('disabled' , true);
        }
    });
});//]]> 