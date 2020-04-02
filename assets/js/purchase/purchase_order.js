// ARWIN CALINGASAN 2018
//NOTE: Affected functions for changing/updating add order/table/total/shipping/discounts
// 1. add_inventory_modal - for add inventory modal
//     *new item
//     *add qty
// 2. btnassignShip - for shipping button
// 3. btnGeneralDiscount - for general discount button
//DONT FORGET TO EDIT salesorder edit - salesorder_edit.js


$(function(){
	var base_url = $("body").data('base_url'); //url
	var datas = $("body").data('datas'); // data for query
	var search_label = $("body").data('label'); //label search

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
    $("#searchSalesorder").keyup(function(){
        $("#searchInventory_id").val("");
        $("#price").val("");

        if ($("#searchInventory_id").val() == "" && $("#price").val() == "") { //for remove loading
            $(this).css("cssText", "background-image: url('');");
        }
    });
    // to remove when the id is empty
    $("#searchSalesorder").focusout(function(){

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
    $("#searchSalesorder").autocomplete({
        source: function(request, response){
            var typing = $("#searchSalesorder").val();
            var supplierid = $(".searchSupplier").val();

            //var pricecat = $("#pricecat").val();
            // var francid = $("#searchSupplier").val();
            // var parts = francid.split('|', 2);
            // var franchise_id = parts[1]; 

            //ajax to fetch data in autocomplete

            // Check if we already searched and map the existing results
            // into the proper autocomplete format
            if (request.term in vendorCache) {
                $("#searchSalesorder").css("cssText", "background-image: url('"+base_url+"assets/img/autocomplete_loading.gif');");
                response($.map(vendorCache[request.term], function (m) {
                    return { 
                        label: m.name, //name = key of array for label
                        id: m.code, // for id
                        price: m.price, // for price
                        supplierid: m.supplierid, // for supplierid
                        unit: m.unit, // for unit
                        unitid: m.unitid, // for unit
                        catid: m.catid // for inv category
                    };
                }));
                setTimeout(function(){
                    $("#searchSalesorder").css("cssText", "background-image: url('');");
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
                    $("#searchSalesorder").css("cssText", "background-image: url('"+base_url+"assets/img/autocomplete_loading.gif');");
                },
                success: function (data, status, xhr){    
                    $("#searchSalesorder").css("cssText", "background-image: url('');");
                    // cache the results
                    vendorCache[request.term] = data.result;

               //  purchase_id = $(".searchInventory").getSelectedItemData().code;
               //  $("#searchInventory_id").val(purchase_id);
               //  $("#supplierid").val(supplierid);
               //  purchase_name = $(".searchInventory").getSelectedItemData().name;
               // supplierid = $(".searchInventory").getSelectedItemData().supplierid;

                    // if this is the same request, return the results
                    if (xhr === vendorXhr) {
                        // data is an array of objects and must be transformed for autocomplete to use
                        var array = $.map(data.result, function(m) {
                            return {
                                label: m.name, //name = key of array for label
                                id: m.code, // for name
                                supplierid: m.supplierid, // for supplierid
                                price: m.price, // for price
                                unit: m.unit, // for unit
                                unitid: m.unitid, // for unit
                                catid: m.catid // for inv category
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
            $("#unitid").val(ui.item.unitid);
            $("#unit").val(ui.item.unit);
            //$("#price").val(ui.item.price);
            get_discount(ui.item.id);
            get_price(ui.item.price, ui.item.id, ui.item.catid);

        }
    }).data("ui-autocomplete")._renderItem = function(ul, item){ // create highlighted 
    var $div = $("<div></div>").text(item.label);
    highlightText(this.term, $div);
    return $("<li></li>").append($div).appendTo(ul);
};  

var table = $('#table-grid').DataTable({ //declaring of table
    columnDefs: [{ targets: [4], visible: true, orderable: false, sClass: 'text-center'}],
    columnDefs: [{ targets: [0], sClass: 'td_id'}],
    columnDefs: [{targets: [ 0 ],visible: false,searchable: false},{targets: [ 1 ],visible: false,searchable: false},{targets: [ 8 ],visible: false,searchable: false}]
});//data table

//add item inside modal
$(".add_inventory_modal").click(function(e){

    checkInputs('#addRow');
    var quantity = $("#qty").val();
    var item = $("#searchInventory_id").val();
    var itemname = $("#searchSalesorder").val();
    var price = $("#price").val();
    var unit = $("#unit").val();
    var unitid = $("#unitid").val();
    var disc_percent = $("#disc_percent").val();
    var disc_amt = $("#disc_amt").val();
    var disctype = $("#discount_type_select").val();
    //$(".discount_type_select").text()
    currentSelectedItemId = item;
    currentSelectedItemName = itemname;
    currentSelectedUnit = unit;

    //check if not empty fields
    if((currentSelectedItemId == "") || (currentSelectedItemName == "") || (currentSelectedUnit == null) || (quantity == "") || (quantity == 0)){
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

    }else if(price == 0){
     $.toast({
        heading: 'Note',
        text: "No Price. Please proceed to Inventory->Inventory Price List",
        icon: 'error',
        loader: false,  
        stack: false,
        position: 'top-center', 
        allowToastClose: false,
        bgColor: '#f0ad4e',
        textColor: 'white'  
        });// if there is no seleccted item or the input item is not on the list   
 }else{

    matchFound = false;

    var dataIDstackup = table.rows().columns(0).data();
    tableID = [];

    dataIDstackup.each(function (value, index){    
        tableID = tableID.concat(value);
        });//columns COLUMN TO CHANGE/GET VALUE data VALUE OF CELL row CURRENT ROW IN THE LOOP 

    for(i=0;i<tableID.length;i++){

        if(tableID[i] == currentSelectedItemId){

                var oldValraw = table.row(i).column(3).data();//getting all the value qty from the table record
                var oldVal = oldValraw[i].split(',').join('');    
                var quantity = $("#qty").val();//getting the input values in quantity input box
                var price = $("#price").val();
                var disc_percent = $("#disc_percent").val();
                var disc_amt = $("#disc_amt").val();
                var inputVal = parseFloat(quantity);//inserting the value of input box in inputVal variable and converting the value to float/double
                var newVal =  parseFloat(oldVal) + parseFloat(inputVal);//Sum the two values above the oldVal and inputVal
                //oldVal[i] is the index of the match record
                var total1 =  parseFloat(newVal) * parseFloat(price);

                // Adding Discount in Order Form (percent/whole numbers) 
                if(disc_percent > 0){ //if percentage
                    var percentage = (disc_percent/100).toFixed(2);
                    var disc = $("#disc_percent").val() + '%';
                    var total_discount =   parseFloat(percentage) * parseFloat(total1); 
                    var total = parseFloat(total1) - parseFloat(total_discount);

                }else if(disc_amt > 0){ //if whole number
                    var disc = accounting.formatMoney($("#disc_amt").val());
                    var total =  parseFloat(total1) - parseFloat(disc);

                }else{ //if no discount
                    var disc = accounting.formatMoney(0);
                    var total =  total1;
                }

                var totalarray = [];

                //delete function delete the old record that match the new input
                //dinedelete niya yung luma instead na palitan ng value yung nagmatch na record  

                deleteMatchRow(currentSelectedItemId);

                // var parts = currentSelectedItemName.split('-', 2);
                // var itemname = parts[1];  

                selectedDataarray = [
                currentSelectedItemId,
                unitid,  
                itemname.toUpperCase(),
                accounting.formatMoney(newVal),
                currentSelectedUnit,
                accounting.formatMoney(price),
                disc,
                accounting.formatMoney(total),
                disctype,
                "<center><button class='btn btn-danger btnDelete btnTable'><i class='fa fa-trash-o'></i> Delete</button></center>"
                ];// adding selected data to array 
                populateTable(selectedDataarray);//adding of new record that have the updated qty value that we sum up above

                matchFound =  true;//setting the matchFound boolean to true to avoid adding new record, kasi pag walang ganito magadd lng siya ng new record hindi siya magdedelete pag may magkaparehas

                break;

            }else{
                matchFound = false;
            }

        }//columns COLUMN TO CHANGE/GET VALUE data VALUE OF CELL row CURRENT ROW IN THE LOOP     

        if(matchFound == false){
            // Adding Discount in Order Form (percent/whole numbers) 

            var total1 =  parseFloat(quantity) * parseFloat(price);

            if(disc_percent > 0 && disc_percent != ""){ //if percentage
                var percentage = (disc_percent/100).toFixed(2);
                var disc = $("#disc_percent").val() + '%';
                var total_discount =   parseFloat(percentage) * parseFloat(total1); 
                var total = parseFloat(total1) - parseFloat(total_discount);

            }else if(disc_amt > 0 && disc_amt != ""){ //if whole number 
                var disc = accounting.formatMoney($("#disc_amt").val());
                var disc_calc = $("#disc_amt").val();
                var total_calc =  parseFloat(total1) - parseFloat(disc_calc);

                if(total_calc < 0){
                    $.toast({
                        heading: 'Note',
                        text: 'Total value is less than zero. please enter valid discount',
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
                        var total =  parseFloat(total1) - parseFloat(disc_calc);
                    }   


            }else{ //if no discount
                var disc = accounting.formatMoney(0);
                var total =  total1;
            }

            var totalarray = [];

            var parts = currentSelectedItemName.split('-', 2);
            var itemname_concut = parts[1];  

            //for fetching of rows in the table, prepare your data.
            selectedDataarray = [
            currentSelectedItemId,
            unitid,       
            itemname.toUpperCase(),
            accounting.formatMoney(quantity),
            currentSelectedUnit,
            accounting.formatMoney(price),
            disc,
            accounting.formatMoney(total),
            disctype,
            "<center><button class='btn btn-danger btnDelete btnTable'><i class='fa fa-trash-o'></i> Delete</button></center>"
            ];// adding selected data to array 

            populateTable(selectedDataarray);    
        }

        //start - to get total amount in the specific column
        var data_totalamt = table.rows().columns(7).data();

        data_totalamt.each(function(value, index){ 
            grand_total = eval(value.join("+").replace(/,/g, ''));
        });

        //add shipping
        var ship_val = $(".ship_hide").val();

        //for overall discount
        //var gen_disc = $("#discount_whole_no").val();
        var total_hide = $(".grandtotal_hide").val();
        var discount = $(".discount").val();
        var discount_gen_type_select = $(".disc_perc").val();

        if(discount_gen_type_select == 2){ //if percentage
            var percentage = (discount/100).toFixed(2);
            var gen_disc =   parseFloat(percentage) * parseFloat(grand_total);
        }else if(discount_gen_type_select == 1){ //if whole number 
            var gen_disc =  $(".discount").val();
        }else{ //if no discount
            var gen_disc = 0;
        }

        //add shipping and item total
        var gs_total = parseFloat(grand_total) + parseFloat(ship_val);
        var newgrandtotal = parseFloat(gs_total) - parseFloat(gen_disc);

        $(".grand_total").text("Total : "+ accounting.formatMoney(newgrandtotal));
        $(".grandtotal_hide").val(grand_total);
        //end - to get total amount in the specific column

        $('#addAOrderItemModal').modal('toggle'); //close modal

        $(".btnGenDiscount").val(newgrandtotal);

        //enabling general discount
        $(".btnGenDisc").prop("disabled",false);

        //enabling shipping amount
        $(".btnShip").prop("disabled",false);

        clearAddform(); //clear all forms                        
    }
});

function populateTable(data){
    table.row.add(selectedDataarray);         
    table.draw();
}
$(".BtnNext").click(function(e){
  e.preventDefault();
  var searchSupplier = $(".searchSupplier").val();
  var searchAddress = $(".searchAddress").val();

  if (searchSupplier == "" || searchAddress == "") {

    $.toast({
        heading: 'Note!',
        text: 'Please fill out required fields',
        icon: 'error',
        loader: false,  
        stack: false,
        position: 'top-center', 
        allowToastClose: true,
        bgColor: '#f0ad4e',
        textColor: 'white' 
    });

}else{
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

    }else if (text_label == 'Step 2') {

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

//for format Money
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
    }else{
        grand_total_wship = parseFloat(grand_total) + parseFloat(shipping_cost);
    }
    $('#grand_total').val(accounting.formatMoney(grand_total_wship));
    $('#grand_total1').val(parseFloat(grand_total_wship) || 0);
    $('#grand_total3').val(parseFloat(grand_total));
}  

function trigger_shipping(shipping_cost){
    $('#shipping_cost1').val(shipping_cost);
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

$('#disc_percent').keyup(function(){
    if ($(this).val() > 100){
        $.toast({
            heading: 'Note',
            text: 'Discount must not above 100.',
            icon: 'error',
            loader: false,  
            stack: false,
            position: 'top-center', 
            bgColor: '#f0ad4e',
            textColor: 'white',
            allowToastClose: false,
            hideAfter: 3000
        });
        $(this).val('');
    }
});

$('#disc_gen_percent').keyup(function(){
  if ($(this).val() > 100){
   $.toast({
       heading: 'Note',
       text: 'Discount must not above 100.',
       icon: 'error',
       loader: false,  
       stack: false,
       position: 'top-center', 
       bgColor: '#f0ad4e',
       textColor: 'white',
       allowToastClose: false,
       hideAfter: 3000
   });
   $(this).val('');
}
});

//merge if match row inserted then add the quantity of item, overwrite the discount
function deleteMatchRow(matchValue){
    var filteredData = table.rows().indexes().filter(function(value, index) {
        return table.row(value).data()[0] == matchValue; 
    });

    table.rows(filteredData).remove().draw();
}

//for delete parent row in the table
$("#table-grid").on('click', '.btnDelete', function(){ 

    var discount = $("#discount").val();
    var discount_gen_type_select = $("#disc_perc").val(); //discount type

    table.row($(this).parents('tr')).remove().draw(false); //get the selected row to delete

    var data_totalamt = table.rows().columns(7).data(); //get no.6 data which is the total

    data_totalamt.each(function(value, index){ //fetch array to string sum 
        grand_total = eval(value.join("+").replace(/,/g, '')); //convert array to summation of string without comma
    });

    ship = $("#ship_hide").val();

    $("#grandtotal_hide").val(grand_total);

    //select discount
    if(discount_gen_type_select == 2){ //if percentage
        var percentage = (discount/100).toFixed(2);      
        var gen_disc =   parseFloat(percentage) * parseFloat(grand_total);   

    }else if(discount_gen_type_select == 1){ //if whole number 
        var gen_disc = discount;
    }else{ //if no discount
        var gen_disc =  0;
    }
    //add shipping and item total
    var newgrandtotal = (parseFloat(grand_total) - parseFloat(gen_disc))+ parseFloat(ship);

    //var newgrandtotal = parseFloat(ship) + parseFloat(grand_total) - parseFloat(gen_disc);
    $(".grand_total").text("Total : "+ accounting.formatMoney(newgrandtotal)); 
    $(".btnShipping").val(ship);

    if ( table.data().count() == 0 ) {
     $('.btnGenDisc').prop('disabled', true);
     $('.btnShip').prop('disabled', true);
 }else{    
    $('.btnGenDisc').prop('disabled', false);
    $('.btnShip').prop('disabled', false);
}
});

//clear forms 
$(".cancelBtn").click(function(e){ 
    clearAddform(); 
});

// get customer information
var address_id = "";
var member_name = "";
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
            beforeSend:function(data)
            {
                $("body").LoadingOverlay("show"); 
            },
            complete: function()
            {
                $("body").LoadingOverlay("hide"); 
            },
            success:function(data){
                    // alert(dataTable.draw());
                    if (data.success == 1) {
                        var res = data.result;
                        $("#txtResult").html(res.data[1]);
                    }
                }

            });
    }
});


    $('#searchCustomer').one('change', function() {
     $('#addpurchaseorder').prop('disabled', false);
     $('#addShipping').prop('disabled', false);
     $('#shipping_cost1').prop('disabled', false);
     $('#c_notes').prop('disabled', false);
 });

    $('#table-grid').delegate(".btnDelete","click", function(){
        var purchase_id = $(this).data('value');
        $.ajax({
            type:'post',
            url:base_url+'Main_purchase/deletePurchaseOrderSession',
            data:{'purchase_id': purchase_id},
            success:function(data){
                dataTable.draw();
            }
        });
    });

//adding shipping amount
$(".btnassignShip").click(function(e){
	var shipping = $("#shipping").val();
	var grandtotal = $("#grandtotal_hide").val();
	var discount_gen_type_select = $("#disc_perc").val();  
	var discount = $("#discount").val();

	//select discount
	if(discount_gen_type_select == 2){ //if percentage
		var percentage = (discount/100).toFixed(2);      
		var gen_disc =   parseFloat(percentage) * parseFloat(grandtotal);   
		var total = (parseFloat(grandtotal) - parseFloat(gen_disc));

	}else if(discount_gen_type_select == 1){ //if whole number 
		var total =  (parseFloat(grandtotal) - parseFloat(discount));
	}else{ //if no discount
		var total =  grandtotal;
	}

	var newgrandtotal = parseFloat(shipping) + parseFloat(total);
	$(".grand_total").text("Total : "+ accounting.formatMoney(newgrandtotal));
	var ship_amt = $(".btnShipping").text("Shipping: " + accounting.formatMoney(shipping));
	$(".ship_hide").val(shipping);
});

//adding general discount
$(".btnGeneralDiscount").click(function(e){

    var disc_gen_percent = $("#disc_gen_percent").val();
    var disc_gen_amt = $("#disc_gen_amt").val();
    var grandtotal = $("#grandtotal_hide").val();
    var shipping = $("#ship_hide").val();
    var discount_gen_type_select = $("#discount_gen_type_select").val();

    if(discount_gen_type_select == 2){ //if percentage
        var percentage = (disc_gen_percent/100).toFixed(2);      
        var gen_disc =   parseFloat(percentage) * parseFloat(grand_total);   
        var total = (parseFloat(grandtotal) - parseFloat(gen_disc)) + parseFloat(shipping);

        var disc1 = discount_gen_type_select; //discount type
        var disc = $("#disc_gen_percent").val() + '%'; //for view
        var total_discount = disc_gen_percent; //hidden discount

    }else if(discount_gen_type_select == 1){ //if whole number 
        var disc1 = discount_gen_type_select;
        var disc = accounting.formatMoney($("#disc_gen_amt").val());
        var total_calc =  parseFloat(grandtotal)  - parseFloat(disc_gen_amt);

        if(total_calc < 0){
            $.toast({
                heading: 'Note',
                text: 'Total value is less than zero. please enter valid discount',
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
                var total =  (parseFloat(grandtotal) - parseFloat(disc_gen_amt))  + parseFloat(shipping);
            }   

            var total_discount = disc_gen_amt;
    }else{ //if no discount
        var disc = accounting.formatMoney(0);
        var total =  grandtotal;
        var total_discount = 0;
    }

    //show amount
    $(".grand_total").text("Total : "+ accounting.formatMoney(total));   
    $(".btnGenDiscount").text("Discount: " + disc);

    //return fields to empty
    clearAddform()

    $("#disc_perc").val(disc1); //hidden discount type
    $(".discount").val(total_discount); //hidden total discount
});

$('.purchase_date').datepicker({
	format: 'mm/dd/yyyy',
	startDate: '-1d',
	endDate: '+90d'
});

///////////saving


orderItems = [];

$(".BtnProceed").click(function(e){
    e.preventDefault();

    var supplierid = $(".searchSupplier_id").val();
    var itemlocid = $(".searchAddress").val();
    var shipping = $("#shipping_id").val();
    var sales_date = $("#purchase_date").val();
    var idno = $("#idno").val();
    var totalamt = $("#grandtotal_hide").val();
    var freight = $("#ship_hide").val();
    var sono = $("#sono").val();
    var username = $("#username").val();
    var notes = $("#notes").val();
    var gen_disc = $("#discount").val();
    var gen_distype = $("#disc_perc").val();

    var data = table.rows().data();
    data.each(function (value, index) {

        entry = {
            itemid: value[0],
            uomid: value[1],
            qty: value[3].replace(/,/g, ''), //remove ' , ' comma to work properly
            price: value[5].replace(/,/g, ''), //remove ' , ' comma to work properly
            discamt: value[6].replace(/,/g, ''), //remove ' , ' comma to work properly
            subtotal: value[7].replace(/,/g, ''), //remove ' , ' comma to work properly
            disctype: value[8],
        }
        orderItems.push(entry);
    });

    if (totalamt == 0) {
        $.toast({
            heading: 'Note',
            text: 'Must have at least one purchase order.',
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
            url: base_url+"Main_purchase/table_save_addpurchaseorder",
            type: 'post',
            data: { 
                'orderItems': orderItems, 
                'orderTotal': totalamt, 
                'freight': freight,
                'gen_disc':gen_disc,
                'notes': $("#notes").val(),
                'idno': $("#idno").val(),
                'itemlocid': $("#searchAddress").val(),
                'shipping_id': $("#shipping_id").val(),
                'sales_date': $("#purchase_date").val(),
                'discamt': $(".discount_type_select").text(),
                'gendisctype': gen_distype,
                'supplierid':supplierid
            },

            beforeSend: function() {
                $.LoadingOverlay("show");
            },

            success:function(data){
                $.LoadingOverlay("hide");
                if(data.success == 1){
                            $('.step_label').text(''); //step 5
                            makeProgress(66.6,100);

                            $('.step2').css('overflow',"hidden");
                            $('.step2').css('position',"absolute");
                            $('.step2').hide('slide', {direction: 'left'}, 1000);
                            $('.step3').stop().show('slide', {direction: 'right'}, 1000);

                            setTimeout(function(){
                                $('.step2').css('overflow',"visible");
                                $('.step2').css('position',"static");

                            },2000);
                            $(".required_fields").prop("hidden",true);
                            $(".BtnNext").prop("hidden",true);
                            $(".BtnBack2").prop("hidden",true);
                            $(".BtnForm1").prop("hidden",true);
                            $(".BtnForm2").prop("hidden",true);
                            $(".BtnProceed").prop("hidden",true);

                            $.toast({
                                heading: 'Success',
                                text: 'Sales Order has been successfully saved.',
                                icon: 'success',
                                loader: false,  
                                stack: false,
                                position: 'top-center', 
                                bgColor: '#5cb85c',
                                textColor: 'white',
                                allowToastClose: false,
                                hideAfter: 3000
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
                                hideAfter: 3000
                            });
                        }    
                    }
                });
    }   
});       

//validation of required fields, add another code for validation if necessary
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

function get_price(price, itemid, catid){
    var gprice = price;
    var gitemid = itemid;
    var gcatid = catid;

    if(gcatid == 45){

        $("#price").val(gprice);
        $("#price").prop("hidden", false);
        $("#price_div").prop("hidden", false)
    }else{
        $("#price").val(gprice);
        $("#price").prop("hidden", true);
        $("#price_div").prop("hidden", true)
    }
}

});

    //show hide descount input
    $(function (){
        // choose discount type select hide show the field
        $(".discount_type_select").change(function(){
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
    });

    $(function (){
        // choose discount type select hide show the field
        $(".discount_gen_type_select").change(function(){
            var val = $(this).val();

            if (val == 1) {
                $(".amount_div").show();
                $("#disc_gen_amt").val("");
                $(".percentage_div").hide();

            }else if (val == 2) {
                $(".percentage_div").show();
                $("#disc_gen_percent").val("");
                $(".amount_div").hide();

            }else{
                $(".amount_div").hide();
                $("#disc_gen_amt").val("");
                $("#disc_gen_percent").val("");
                $(".percentage_div").hide();
            }
        });
    });

    function formatMoney(n,c, d, t){
        c = isNaN(c = Math.abs(c)) ? 2 : c;
        d = d == undefined ? "." : d;
        t = t == undefined ? "," : t; 
        s = n < 0 ? "-" : "";
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "";
        j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    }

    $('#addAPurchaseItemModal').on('hidden.bs.modal', function(){
        $(this).find('form').trigger('reset');
    });

    function isNumberKeyOnly(evt){
       var charCode = (evt.which) ? evt.which : evt.keyCode;
       if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

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
});

var bse_url = $("body").data('base_url'); //url
function get_discount(val){
    var itemid = val;  
    var idno = $("#supplierid").val();
    if (itemid != "") 
    {
        $.ajax({
            type:'post',
            url: bse_url+'Main_purchase/get_supplier_discount',
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



//clear all form function, please add/change other input to clear if needed
function clearAddform(){
    $("#qty").css("border-color", "#eee");  //rollback when not empty
    $("#qty").val('');
    $("#searchInventory_id").val('');
    $("#disc_amt").val("");
    $("#disc_percent").val("");
    $("#discount_type_select").val("");
    $(".percentage_div").hide();
    $(".amount_div").hide();
}