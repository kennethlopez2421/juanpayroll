$(function(){
    var base_url = $("body").data("base_url");

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

    var table = $('#table-grid').DataTable({ //declaring of table
        columnDefs: [{ targets: [4], visible: true, orderable: false, sClass: 'text-center'}],
        columnDefs: [{ targets: [0], sClass: 'td_id'}]
    });//data table

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

    function populateTable(data){
        table.row.add(selectedDataarray);         
        table.draw();
    }

    //for delete parent row in the table
    $("#table-grid").on('click', '.btnDelete', function(){ 

        table.row($(this).parents('tr')).remove().draw(false); //get the selected row to delete

        var data_totalamt = table.rows().columns(6).data(); //get no.6 data which is the total

        data_totalamt.each(function(value, index){ //fetch array to string sum 
            grand_total = eval(value.join("+").replace(/,/g, '')); //convert array to summation of string without comma
        });

        ship = $("#ship_hide").val();

        $("#grandtotal_hide").val(grand_total);
        
        var newgrandtotal = parseFloat(ship) + parseFloat(grand_total);

        $(".grand_total").text("Total : "+ accounting.formatMoney(newgrandtotal)); 
        $(".btnShipping").val(ship);
    });

    $(".cancelBtn").click(function(e){ //clear forms 
        clearAddform(); 
    });

    
    //add item inside modal
    $(".add_inventory_modal").click(function(e){

        checkInputs('#addRow');
        var quantity = $("#qty").val();
        var item = $("#searchSalesorderCode_id").val();
        var itemname = $("#searchSalesorder").val();
        var price = $("#price").val();
        var disc_percent = $("#disc_percent").val();
        var disc_amt = $("#disc_amt").val();
        currentSelectedItemId = item;
        currentSelectedItemName = itemname;

        //check if not empty fields
        if((currentSelectedItemId == "") || (currentSelectedItemName == "") || (currentSelectedUnit == null) || (quantity == "")){
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

        }else{
            
            matchFound = false;

            var dataIDstackup = table.rows().columns(0).data();
            tableID = [];

            dataIDstackup.each(function (value, index){    
                tableID = tableID.concat(value);
            });//columns COLUMN TO CHANGE/GET VALUE data VALUE OF CELL row CURRENT ROW IN THE LOOP 

            for(i=0;i<tableID.length;i++){

                if(tableID[i] == currentSelectedItemId){

                    var oldValraw = table.row(i).column(2).data();//getting all the value qty from the table record
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
                        var disc = $("#disc_amt").val();
                        var total =  parseFloat(total1) - parseFloat(disc);

                    }else{ //if no discount
                        var disc = 0;
                        var total =  total1;
                    }

                    var totalarray = [];

                    //delete function delete the old record that match the new input
                    //dinedelete niya yung luma instead na palitan ng value yung nagmatch na record  

                    deleteMatchRow(currentSelectedItemId);

                    selectedDataarray = [
                        currentSelectedItemId,
                        currentSelectedItemName.toUpperCase(),
                        accounting.formatMoney(newVal),
                        currentSelectedUnit,
                        accounting.formatMoney(price),
                        disc,
                        accounting.formatMoney(total),
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

                if(disc_percent > 0){ //if percentage
                    var percentage = (disc_percent/100).toFixed(2);
                    var disc = $("#disc_percent").val() + '%';
                    var total_discount =   parseFloat(percentage) * parseFloat(total1); 
                    var total = parseFloat(total1) - parseFloat(total_discount);

                }else if(disc_amt > 0){ //if whole number 
                    var disc = $("#disc_amt").val();
                    var total =  parseFloat(total1) - parseFloat(disc);

                }else{ //if no discount
                    var disc = 0;
                    var total =  total1;
                }

                var totalarray = [];

                //for fetching of rows in the table, prepare your data.
                selectedDataarray = [
                    currentSelectedItemId,
                    currentSelectedItemName.toUpperCase(),
                    accounting.formatMoney(quantity),
                    currentSelectedUnit,
                    accounting.formatMoney(price),
                    disc,
                    accounting.formatMoney(total),
                    "<center><button class='btn btn-danger btnDelete btnTable'><i class='fa fa-trash-o'></i> Delete</button></center>"
                ];// adding selected data to array 

                populateTable(selectedDataarray);    
            }

            //start - to get total amount in the specific column
            var data_totalamt = table.rows().columns(6).data();

            data_totalamt.each(function(value, index){ 
                grand_total = eval(value.join("+").replace(/,/g, ''));
            });

            //add shipping
            var ship_val = $(".ship_hide").val();

            //add shipping and item total
            var newgrandtotal = parseFloat(grand_total) + parseFloat(ship_val);

            $(".grand_total").text("Total : "+ accounting.formatMoney(newgrandtotal));
            $(".grandtotal_hide").val(newgrandtotal);
            //end - to get total amount in the specific column

            $('#viewAddrowModal').modal('toggle'); //close modal

            clearAddform(); //clear all forms                        
        }
    });

    //adding shipping amount
    $(".btnassignShip").click(function(e){
        var shipping = $("#shipping").val();
        var grandtotal = $("#grandtotal_hide").val();
        var ship_hide = $("#ship_hide").val();

        var newgrandtotal = parseFloat(shipping) + parseFloat(grandtotal); 

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
});

//clear all form function, please add/change other input to clear if needed
function clearAddform(){
    $("#qty").css("border-color", "#eee");  //rollback when not empty
    $("#qty").val('');
    $("#searchSalesorder").val('');
    $("#disc_amt").val(0);
    $("#disc_percent").val(0);
    $("#discount_type_select").val("");
    $(".percentage_div").hide();
    $(".amount_div").hide();
}

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

//check if number
function isNumberKeyOnly(evt){    
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;
    return true;
}

// 08/14/18 for autocomplete -josh
$(function(){
    var base_url = $("body").data('base_url'); //url

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
        $("#searchSalesorderCode_id").val("");

        if ($("#searchSalesorderCode_id").val() == "") { //for remove loading
            $(this).css("cssText", "background-image: url('');");
        }
    });
    // to remove when the id is empty
    $("#searchSalesorder").focusout(function(){
        if ($("#searchSalesorderCode_id").val() == "") { 
            $(this).val("");
            $(this).css("cssText", "background-image: url('');"); //for remove loading
        }
    });

    //autocomplete plugin with ajax 
    $("#searchSalesorder").autocomplete({
        source: function(request, response){
            var typing = $("#searchSalesorder").val();
            $.ajax({
                type: "POST",
                url: base_url+'Main_dynamic_table/autocomplete_sales',
                dataType: "json",
                data:{'texttyped':typing},
                beforeSend:function(data){
                $("#searchSalesorder").css("cssText", "background-image: url('"+base_url+"assets/img/autocomplete_loading.gif');");
                },
                success: function (data){    
                $("#searchSalesorder").css("cssText", "background-image: url('');");
                // data is an array of objects and must be transformed for autocomplete to use
                var array = data.Response === "False" ? [] : $.map(data.result, function(m) {
                return {
                label: m.name, //name = key of array for label
                id: m.code // for id
                };
                });
                response(array);
                }                                             
            });
        },

        select: function (event, ui) { //to get id of an item
            $("#searchSalesorderCode_id").val(ui.item.id);
            // currentSelectedItemId = ui.item.id
            // currentSelectedItemName = ui.item.id
            // currentSelectedUnit = ui.item.id
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
});
// 08/14/18 for autocomplete -josh