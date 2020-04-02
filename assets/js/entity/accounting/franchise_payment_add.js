
$(function(){

    var base_url = $("body").data('base_url'); //url
    var datas = $("body").data('datas'); // data for query
    var search_label = $("body").data('label'); //label search


    // var shipping = 0;
    // var itemtotalamtArr = [];
    var errorFound = false;
    var matchFound = false;
    //var itemtotalamt_val = 0;
    var newgrandtotal = 0;
    var grand_total = 0;
    //var ship_val = 0;


//add item inside modal
$(".add_inventory_modal").click(function(e){

    //checkInputs('#addRow');
    var item = $("#searchPayment_id").val();
    var itemname = $("#searchPayment").val();
    var price = $("#price").val();



    //check if not empty fields
    if(item == "" || itemname == "" || price == ""){
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

        deleteMatchRow(item);

        //matchFound = false;

        // var dataIDstackup = table.rows().columns(0).data();
        // tableID = [];

        // dataIDstackup.each(function (value, index){    
        //     tableID = tableID.concat(value);
        // });//columns COLUMN TO CHANGE/GET VALUE data VALUE OF CELL row CURRENT ROW IN THE LOOP 

        // for(i=0;i<tableID.length;i++){

            //for fetching of rows in the table, prepare your data.
            selectedDataarray = [
            item,             
            itemname.toUpperCase(),
            accounting.formatMoney(price),
            "<center><button class='btn btn-danger btnDelete btnTable'><i class='fa fa-trash-o'></i> Delete</button></center>"
            ];// adding selected data to array 

            populateTable(selectedDataarray);    
       // }

        //start - to get total amount in the specific column
        var data_totalamt = table.rows().columns(2).data();

        data_totalamt.each(function(value, index){ 
            grand_total = eval(value.join("+").replace(/,/g, ''));
        });

        $(".grand_total").text("Total : "+ accounting.formatMoney(grand_total));
        $(".grandtotal_hide").val(grand_total);
        //end - to get total amount in the specific column

        $('#addPayment').modal('toggle'); //close modal

        clearAddform(); //clear all forms                        
    }
});

//SAVING
paymentItems = [];

$(".BtnSaveProceed").click(function(e){
    e.preventDefault();

    var franchisee = $("#franchise_id").val();
    var today = $("#todaydate").val();
    var totalamt = $("#grandtotal_hide").val();
   

    var data = table.rows().data();
    data.each(function (value, index) {

        entry = {
            itemid: value[0],
            price: value[2].replace(/,/g, ''), //remove ' , ' comma to work properly
        }
        paymentItems.push(entry);
    });

    if (totalamt == 0) {
        $.toast({
            heading: 'Note',
            text: 'Must have at least one sales order.',
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
            url: base_url+"Main_franchise_accounting/savePayment",
            type: 'post',
            data: { 
                'paymentItems': paymentItems, 
                'totalamt': totalamt, 
                'today': today,
                'franchisee':franchisee
               
            },

            beforeSend: function() {
                $.LoadingOverlay("show");
            },

            success:function(data){
                $.LoadingOverlay("hide");
                if(data.success == 1){
                    
                    $.toast({
                        heading: 'Success',
                        text: 'Payment  has been successfully saved.',
                        icon: 'success',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        bgColor: '#5cb85c',
                        textColor: 'white',
                        allowToastClose: false,
                        hideAfter: 3000
                     });
                    window.setTimeout(function(){
                         window.location.href=base_url+"Main_franchise_accounting/franchise_payment_details/" + $("#token").val();
                  },1000);

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

//for delete parent row in the table
$("#table-grid").on('click', '.btnDelete', function(){ 

    table.row($(this).parents('tr')).remove().draw(false); //get the selected row to delete
    var data_totalamt = table.rows().columns(2).data(); //get no.6 data which is the total
    data_totalamt.each(function(value, index){ //fetch array to string sum 
        grand_total = eval(value.join("+").replace(/,/g, '')); //convert array to summation of string without comma
    });
    $("#grandtotal_hide").val(grand_total);
    $(".grand_total").text("Total : "+ accounting.formatMoney(grand_total)); 
});

$(".cancelBtn").click(function(e){ //clear forms 
    clearAddform(); 
});

//merge if match row inserted then add the quantity of item, overwrite the discount
function deleteMatchRow(matchValue){
    var filteredData = table.rows().indexes().filter(function(value, index) {
        return table.row(value).data()[0] == matchValue; 
    });

    table.rows(filteredData).remove().draw();
}


var table = $('#table-grid').DataTable({ //declaring of table
    columnDefs: [{ targets: [4], visible: true, orderable: false, sClass: 'text-center'}],
    columnDefs: [{ targets: [0], sClass: 'td_id'}],
    //columnDefs: [{targets: [ 0 ],visible: false,searchable: false},{targets: [ 1 ],visible: false,searchable: false},{targets: [ 8 ],visible: false,searchable: false}]
});//data table

//insert data to table
function populateTable(data){
    table.row.add(selectedDataarray);         
    table.draw();
}

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


 // An obbject/map for search term/results tracking
 var vendorCache = {};

    // Keep track of the current AJAX request
    var vendorXhr;

    //autocomplete plugin with ajax 
    $("#searchPayment").autocomplete({
        source: function(request, response){
            var typing = $("#searchPayment").val();
            // Check if we already searched and map the existing results
            // into the proper autocomplete format
            if (request.term in vendorCache) {
                $("#searchPayment").css("cssText", "background-image: url('"+base_url+"assets/img/autocomplete_loading.gif');");
                response($.map(vendorCache[request.term], function (m) {
                    return { 
                        label: m.name, //name = key of array for label
                        id: m.code, // for id
                    };
                }));
                setTimeout(function(){
                    $("#searchPayment").css("cssText", "background-image: url('');");
                },500);
                
                return;
            }

            // search term wasn't cached, let's get new results
            vendorXhr = $.ajax({
                type: "POST",
                url: base_url+'Main_franchise_accounting/autocomplete_payment',
                dataType: "json",
                data:{'texttyped':typing},
                beforeSend:function(data){
                    $("#searchPayment").css("cssText", "background-image: url('"+base_url+"assets/img/autocomplete_loading.gif');");
                },
                success: function (data, status, xhr){    
                    $("#searchPayment").css("cssText", "background-image: url('');");
                    // cache the results
                    vendorCache[request.term] = data.result;

                    // if this is the same request, return the results
                    if (xhr === vendorXhr) {

                        // data is an array of objects and must be transformed for autocomplete to use
                        var array = $.map(data.result, function(m) {
                            return {
                                label: m.name, //name = key of array for label
                                id: m.code, // for id
                            };
                        });

                        response(array);
                    }                                             
                }
            });
        },

        select: function (event, ui) { //to get id of an item
            $("#searchPayment_id").val(ui.item.id);
        }
    }).data("ui-autocomplete")._renderItem = function(ul, item){ // create highlighted 
        var $div = $("<div></div>").text(item.label);
        highlightText(this.term, $div);
        return $("<li></li>").append($div).appendTo(ul);
    };  

    //to remove when the input doesn't select in the autocomplete
    $("#searchPayment").keyup(function(){
        $("#searchPayment_id").val("");
        $("#price").val("");

        if ($("#searchPayment_id").val() == "" && $("#price").val() == "") { //for remove loading
            $(this).css("cssText", "background-image: url('');");
        }
    });
    // to remove when the id is empty
    $("#searchPayment").focusout(function(){

        if ($("#searchPayment_id").val() == "" && $("#price").val() == "") { 
            $(this).val("");
            $(this).css("cssText", "background-image: url('');"); //for remove loading
        }
    });

});

//clear all form function, please add/change other input to clear if needed
function clearAddform(){
    $("#price").css("border-color", "#eee");  //rollback when not empty
    $("#price").val('');
    $("#searchPayment").css("border-color", "#eee");  //rollback when not empty
    $("#searchPayment").val('');
    $("#searchPayment_id").val('');
}