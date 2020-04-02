$(function(){
    var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
    var fsrno = $("#fsrno_id_sec").data("fsrno");
    var sa_id = $("#searchSalesorderCode_id").val();
    var qty = $("#qty").val();

  //  $('.btnassignShip').attr('disabled', true);

    $('#shipping').on('keydown',function() {
        if($(this).val() != '') {
            $('.btnassignShip').attr('disabled' , false);
        }else{
            $('.btnassignShip').attr('disabled' , true);
        }
    });


     var dataTable = $('#table-grid').DataTable({
        "destroy": true,
        
        "serverSide": true,
        "ajax":{
            url :base_url+"Main_sales/table_fsr_display", // json datasource
            type: "post",  // method  , by default get
            data: {"fsrno":fsrno},
            beforeSend:function(data)
            {
                $("#table-grid").LoadingOverlay("show"); 
            },
            complete: function()
            {
                $("#table-grid").LoadingOverlay("hide"); 
            },
            error: function(){ 
             // error handling
                $(".table-grid-error").html("");
                $("#table-grid_processing").css("display","none");
            }
        }

    });  

    dataTable.destroy();

    $('#table-grid').delegate(".btnDeleteSales","click", function(){
            var idcount = $(this).data('value'); // this
         
            var pricecat = $("#pricecat").val();
            var totaldata = $("#tdata").val(); // this
            var itemarray = [];
            var qtyarray = [];
            var fsrnoarray = [];
            var pricearray = [];
            var uomidarray = [];

            var checker = 0;

            itemarray = [];
            qtyarray = [];
            fsrnoarray = [];
            pricearray = [];
            uomidarray = [];

            for (i=0; i < totaldata; i++)
            {
                var fsrno_count = $("#fsrno"+i).val(); // this
                var itemid_count = $("#citemid"+i).val(); // this
                var qty_count = $("#cqty"+i).val(); // this
                var price_count = $("#cprice"+i).val(); // this  
                var uomid_count = $("#cuomid"+i).val(); // this     
               
                if((idcount != i) && (fsrno == fsrno_count))
                {
                    
                      itemarray.push(itemid_count);
                      qtyarray.push(qty_count);
                      fsrnoarray.push(fsrno_count);
                      pricearray.push(price_count);
                      uomidarray.push(uomid_count);


                      checker = 1;
                  
                }
                else
               {
                    if(itemid_count > 0) // eto part na toh pag empty ung table zero data kaya need mag lagay ng empty array.
                    {
                          itemarray.push();
                          qtyarray.push();
                          pricearray.push();
                          uomidarray.push();
                          fsrnoarray.push(fsrno_count);
                          checker = 1;
                    }   
               }
           
            }

            var itemvalidate = itemarray.length
            if(itemvalidate == 0)
            {
             $("#rowrec").val(0);
            }
            if(checker == 1)
            {
                    var rowrec = $("#rowrec").val();
                    $.ajax({
                    type:'post',
                    url:base_url+'Main_sales/validate_fsrdetails',
                    data:{'fsrnoarray': fsrnoarray, 
                    'fsrno': fsrno, 
                    'pricearray': pricearray,
                    "uomidarray":uomidarray,
                     "qtyarray":qtyarray, 
                     "rowrec": rowrec,
                     "pricecat": pricecat},
                    beforeSend:function(data)
                    {
                        $("#table-grid").LoadingOverlay("show"); 
                    },
                    complete: function()
                    {
                        $("#table-grid").LoadingOverlay("hide"); 
                    },
                    success:function(data){
                    if(data.success == 1)
                    {
                       
                            var shippingamt = $("#shippingamt").val();
                            var grand_total1 = $("#grand_total1").val();
                            supergrandtotal=0;

                                if((shippingamt > 0) && (shippingamt != 0))
                                {
                                    var newgrandtotal = parseFloat(data.grandtotal) + parseFloat(shippingamt);
                                    $(".grand_total").text("Total : "+formatMoney(newgrandtotal));
                                    $(".grand_total2").val(newgrandtotal);
                                    $(".item_total").val(data.grandtotal);
                                }   
                                else
                                {
                                    $(".grand_total").text("Total : "+formatMoney(data.grandtotal));
                                    $(".grand_total2").val(data.grandtotal);
                                    $(".item_total").val(data.grandtotal);
                                }
                            var dataTable = $('#table-grid').DataTable({
                            "destroy": true,
                            
                            "serverSide": true,
                            "ajax":{
                                url :base_url+"Main_sales/table_fsr_maindisplay", // json datasource
                                type: "post",  // method  , by default get
                               data: {"itemarray":itemarray,
                                "qtyarray":qtyarray, 
                                "fsrnoarray":fsrnoarray,
                                "uomidarray":uomidarray,
                                  "fsrno": fsrno, "pricearray": pricearray, "rowrec": rowrec, "pricecat": pricecat},
                                beforeSend:function(data)
                                {
                                    $("#table-grid").LoadingOverlay("show"); 
                                },
                                complete: function()
                                {
                                    $("#table-grid").LoadingOverlay("hide"); 
                                },
                                error: function(){ 
                                 // error handling
                                    $(".table-grid-error").html("");
                                    $("#table-grid_processing").css("display","none");
                                }
                            }
                            
                        }); 
                         $.toast({
                            heading: 'Success',
                            text: 'Item has been successfully deleted.',
                            icon: 'success',
                            loader: false,  
                            stack: false,
                            position: 'top-center', 
                            bgColor: '#5cb85c',
                            textColor: 'white',
                            allowToastClose: false,
                            hideAfter: 3000
                        });

                            itemarray = [];
                            qtyarray = [];
                            fsrnoarray = [];
                            pricearray = [];
                            uomidarray = [];

                    } 
                    else
                    {
                           $.toast({
                            heading: 'Note',
                            text: 'No record found.',
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
            
                dataTable.destroy();

                    }
                });
            }
    });

    $(".saveBtnEncode").click(function(e){
         e.preventDefault();
        
        var rowrec = $("#rowrec").val();
        var token = $("#token").val();
        var checker =0;
        if(rowrec == 1) 
        {
            var rowrec = $("#rowrec").val(); // validation
            var totaldata = $("#tdata").val(); // validation
            var shipping_id = $("#shipping_id").val();
            var location_id = $("#location_id").val();
            var edate = $("#edate").val();
            var totalamt = $("#item_total").val();
            var idno = $("#idno").val();
            var shipping = $("#shippingamt").val();
            var notes = $("#notes").val();
            var agentid = $("#agentid").val();

            var itemarray = [];
            var qtyarray = [];
            var fsrnoarray = [];
            var pricearray = [];

            itemarray = [];
            qtyarray = [];
            fsrnoarray = [];
            pricearray = [];

                if(rowrec == 1)
                {
                    for(i = 0; i < totaldata ; i++)
                    {
                        var citemid = $("#citemid"+i).val();
                        var cqty = $("#cqty"+i).val();
                        var fsrno_count = $("#fsrno"+i).val();
                        var price_count = $("#cprice"+i).val();

                         if((citemid > 0) && (fsrno == fsrno_count))
                         {
                            itemarray.push(citemid);
                            qtyarray.push(cqty);
                            fsrnoarray.push(fsrno);
                            pricearray.push(price_count);
                            checker=1;
                          }
                    }
                
                }
            
            if (checker == 1) {
                $.ajax({
                    type:'post',
                    url: base_url+'Main_sales/InsertFSRtoDB',
                    data:{
                      "fsrnoarray": fsrnoarray,
                       "fsrno": fsrno,
                       "location_id": location_id,
                       "shipping_id": shipping_id,
                       "shipping": shipping,
                       "notes": notes,
                       "agentid":agentid
                   },
                    success:function(data){
                        if(data.success == 1)
                        {
                           
                        $.ajax({
                            type:'post',
                            url:base_url+'Main_sales/table_save_fsr',
                            data:{"itemarray":itemarray,
                            "qtyarray":qtyarray, 
                            "fsrnoarray":fsrnoarray, 
                            "pricearray":pricearray, 
                            'fsrno': fsrno,
                             "location_id": location_id,
                            "shipping_id": shipping_id,
                            "shipping": shipping,
                            "notes": notes,
                            "idno": idno,
                            "edate": edate,
                            "agentid":agentid,
                            "totalamt": totalamt,
                            "freight": edate
                            },
                            beforeSend:function(data){
                               // $(".cancelBtn, .saveBtnViolation").prop('disabled', true); 
                                $(".saveBtnEncode").text("Please wait...");
                                $('.saveBtnEncode').prop('disabled', true);
                            },
                                    success:function(data){
                            if(data.success == 1)
                                {
                                    $.toast({
                                        heading: 'Success',
                                        text: 'Franchise Service has been successfully saved.',
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
                }

                window.setTimeout(function(){
                     window.location.href=base_url+"Main_sales/fran_service_history/" + token;
              }, 500);
            }
        });
     }
    }        
});


     $(".addSalesOrderEncodeBtn").click(function(e){
        e.preventDefault();
        var origitemcode = $("#searchSalesorder").val();
        var parts = origitemcode.split('-', 2);
        var sa_id = parts[0];
        var qty = $("#qty").val();
        var franchiseid = $("#franchise_id").val();
        var pricecat = $("#pricecat").val();
        var fsr_price = $("#fsr_price").val();

        if((sa_id > 0) && (qty > 0)) 
        {
            var rowrec = $("#rowrec").val(); // validation
            var totaldata = $("#tdata").val(); // validation

            var itemarray = [];
            var qtyarray = [];
            var fsrnoarray = [];
            var pricearray = [];
            var uomidarray = [];

            itemarray = [];
            qtyarray = [];
            fsrnoarray = [];
            pricearray = [];
            uomidarray = [];

            if(rowrec == 0)
            {
                if (sa_id > 0){
                    itemarray.push(sa_id);
                    qtyarray.push(qty);
                    fsrnoarray.push(fsrno);
                    pricearray.push(0);
                    uomidarray.push(0);
                    $("#rowrec").val(1);
                }
                
            }
            else
            {
                if(rowrec == 1)
                {

                    for(i = 0; i < totaldata ; i++)
                    {
                    var citemid = $("#citemid"+i).val();
                    var cqty = $("#cqty"+i).val();
                    var fsrno_count = $("#fsrno"+i).val();
                    var price_count = $("#cprice"+i).val();
                    var uomid_count = $("#cuomid"+i).val();

                         if((citemid > 0) && (fsrno == fsrno_count))
                         {          
                            itemarray.push(citemid);
                            qtyarray.push(cqty);
                            fsrnoarray.push(fsrno);
                            pricearray.push(price_count);
                            uomidarray.push(uomid_count);
                          }
                    }
                
                }
            }
     
            if ((sa_id > 0) && (qty > 0)) {
                $.ajax({
                    type:'post',
                    url: base_url+'Main_sales/InsertTemporaryFSR',
                    data:{"sa_id": sa_id,
                     "qty": qty,
                      "fsrnoarray": fsrnoarray,
                       "fsrno": fsrno, 
                       "franchiseid": franchiseid,
                        "pricecat": pricecat,
                         'pricearray': pricearray,
                         'uomidarray':uomidarray,
                          "qtyarray":qtyarray,
                          "fsr_price":fsr_price,
                           "rowrec": rowrec, 
                           "itemarray":itemarray, 'rowrec': rowrec },
                    beforeSend:function(data)
                    {
                        $("#table-grid").LoadingOverlay("show"); 
                    },
                    complete: function()
                    {
                        $("#table-grid").LoadingOverlay("hide"); 
                    },
                    success:function(data){
                        if(data.success == 1)
                        {
                            var shippingamt = $("#shippingamt").val();
                            var grand_total1 = $("#grand_total1").val();

                                if((shippingamt > 0) && (shippingamt != 0))
                                {
                                    var newgrandtotal = parseFloat(data.grandtotal) + parseFloat(shippingamt);
                                    $(".grand_total").text("Total : "+formatMoney(newgrandtotal));
                                    $(".grand_total2").val(newgrandtotal);
                                    $(".item_total").val(data.grandtotal);
                                }   
                                else
                                {
                                    $(".grand_total").text("Total : "+formatMoney(data.grandtotal));
                                    $(".grand_total2").val(data.grandtotal);
                                    $(".item_total").val(data.grandtotal);
                                }
                            var dataTable = $('#table-grid').DataTable({
                            "destroy": true,
                            
                            "serverSide": true,
                            "ajax":{
                                url :base_url+"Main_sales/table_fsr_maindisplay", // json datasource
                                type: "post",  // method  , by default get
                               data: {"itemarray":itemarray,
                                "qtyarray":qtyarray,
                                 "fsrnoarray":fsrnoarray, 
                                 "fsrno": fsrno,
                                 "fsr_price":fsr_price,
                                  "sa_id": sa_id, 
                                  "qty": qty, 
                                  "uomidarray":uomidarray,
                                  "franchiseid": franchiseid,
                                   "pricecat": pricecat,  "pricearray": pricearray, "rowrec": rowrec},
                                
                                error: function(){ 
                                 // error handling
                                    $(".table-grid-error").html("");
                                    $("#table-grid_processing").css("display","none");
                                }
                            }
                            
                            }); 

                              $.toast({
                                heading: 'Success',
                                text: 'Item has been successfully added.',
                                icon: 'success',
                                loader: false,  
                                stack: false,
                                position: 'top-center', 
                                bgColor: '#5cb85c',
                                textColor: 'white',
                                allowToastClose: false,
                                hideAfter: 3000
                            });
                        dataTable.destroy();
                        }
                        else
                        {
                                $.toast({
                                heading: 'Note',
                                text: 'No record found.',
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
                        document.getElementById("fsr_price").disabled = true;
                        $('#table-grid').DataTable().destroy();
                         
                    }
                });
            }
        }
        else
        {
              $.toast({
                    heading: 'Note',
                    text: 'Please fill all required fields.',
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

    });


    // $('#table-grid').delegate(".btnDelete","click", function(){

    //     if ($('.saveBtnEncode').length) {
    //         var itemid = $(this).data('value');

    //         $.ajax({
    //             type:'post',
    //             url:base_url+'Main_sales/deleteSalesOrderEditSession',
    //             data:{'itemid': itemid},
    //             success:function(data){
    //                 dataTable.draw();
    //             }
    //         });
    //     }else{

    //         $(this).prop('disabled',true);
    //     }
        
    // });

   $("#shipping").bind("change paste keyup", function() {
       var shipping = $("#shipping").val();
    });

    function trigger_grandtotal(grand_total){

        var shipping_cost1 = $('#shipping_cost1').val();
        var shipping_cost = $('#shipping_cost1').val();

        if (shipping_cost == ""){
            grand_total_wship = parseFloat(grand_total) + shipping_cost1;
            
        }else{
            grand_total_wship = parseFloat(grand_total) + parseFloat(shipping_cost1);
  
        }

            $('#grand_total').val(accounting.formatMoney(grand_total_wship));
            $('#grand_total1').val(parseFloat(grand_total_wship) || 0);
            $('#grand_total3').val(parseFloat(grand_total));
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

    // for delete modal
    $('#table-grid').delegate(".btnSalesItemDelete","click", function(){

        var sales_id = $(this).data('value');

        $.ajax({
            type: 'post',
            url: base_url+'Main_sales/getSalesOrderInfoUsingID',
            data:{'sales_id':sales_id},
            success:function(data){
                var res = data.result;
                if (data.success == 1) {

                    $(".del_sales_id").val(res[0].sales_id); //user_id pk
                    $(".code_del").text(res[0].code);
                }
            }
        });
    });

    $('.deleteSalesOrderBtn').click(function(e){
        e.preventDefault();

        var del_sales_id = $(".del_sales_id").val();

        $.ajax({
            type:'post',
            url:base_url+'Main_sales/deleteSalesOrder',
            data:{'del_sales_id':del_sales_id},
            success:function(data){
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
                        hideAfter: 4000
                    });
                    dataTable.draw(); //refresh table
                    $('#deleteSalesOrderModal').modal('toggle'); //close modal
                }else{
                    $.toast({
                        heading: 'Note',
                        text: data.message,
                        icon: 'error',
                        loader: false,   
                        stack: false,
                        position: 'top-center',  
                        bgColor: '#f0ad4e',
                        textColor: 'white'        
                    });
                }
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
                console.log(franchise_id);
                $("#searchCustomer_id").val(member_id);
                $("#address").val(home_address);
                $("#contact_no").val(contact_no);
                $("#mode_payment").val(mode_payment);    
                $("#franchise_id").val(franchise_id); 
                $("#idno").val(idno);  
            },
            match: {
                enabled: true
            }
        },
        theme: "square"
    };
    $(".searchCustomer").easyAutocomplete(options);

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

    $(".saveBtnSalesItem").click(function(e){
        
        e.preventDefault();

        var thiss = $("#add_salesorder-form");

        var serial = thiss.serialize();

        $.ajax({
            type:'post',
            url: base_url+'Main_sales/save_salesorder_edit',
            data: serial,
            beforeSend:function(data){
               // $(".cancelBtn, .saveBtnViolation").prop('disabled', true); 
                $(".saveBtnViolation").text("Please wait...");
                
            },
            success:function(data){
               // $(".cancelBtn, .saveBtnSalesItem").prop('disabled', false);
                $(".saveBtnSalesItem").text("Save");
                if (data.success == 1) {
                    dataTable.draw(); //refresh datatable
                    $(thiss).find('input').val(""); // clean fields
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

                    $("#addSalesOrderModal").modal('toggle'); //hide modal
                    $(thiss).find('input').val(""); //clear fileds in modal
                }else{
                    $.toast({
                        heading: 'Note',
                        text: data.message,
                        icon: 'error',
                        loader: false,   
                        stack: false,
                        position: 'top-center',  
                        bgColor: '#f0ad4e',
                        textColor: 'white'        
                    });
                }
            }
        });
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

    $(".searchSalesorder").on("focusout",function() {
        var sales_id = $("#searchSalesorderCode_id").val();
        var price = $("#fsr_price").val();

        if (sales_id == "" || sales_id == 0 || price == "" ) {
            $(this).val("");
        }

        if ($(this).val() != sales_name) {
            $(this).val("");
            $("#searchSalesorderCode_id").val("");
            sales_id = "";
            $("#fsr_price").val("");
            price = "";
        }
    });
  
    var francid = $("#searchCustomer").val();
    var parts = francid.split('|', 2);
    var franchise_id = parts[1]; 

    var options = {
         url: base_url+'Main_sales/autocomplete_service',
         getValue: "name",
         
        ajaxSettings: {
            dataType: "json",
            method: "POST",
            data: {},
        },
        list: {
            onSelectItemEvent: function() {
                sales_id = $(".searchSalesorder").getSelectedItemData().code;
                $("#searchSalesorderCode_id").val(sales_id);
                sales_name = $(".searchSalesorder").getSelectedItemData().name;
                price = $(".searchSalesorder").getSelectedItemData().price;

                if(price == null){
                        $("#fsr_price").val(0);
                }else{
                    $("#fsr_price").val(price); 
                }
                
                $("#orig_price").val(price);
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
    }

    $(".searchSalesorder").easyAutocomplete(options);

});

function clearFields()
{
    $("#searchSalesorderCode_id").val("");
    $("#searchSalesorder").val("");
    $("#qty").val("");
}

$(function(){
    // Run this on document ready to allow only numbers between
    // max and min to be entered into textboxes with class="maxmin".
    // Attributes max, min, and intOnly="true/false" are set in the tag.
    // Min should probably be "0" or "1", or max and min could be single digits.
    // Otherwise for example, if min=5, you couldn't enter 23 because 2 < 5.
    $("#qty, #valbox").each(function () {

        var thisJ = $(this);
       // var max = thisJ.attr("max") * 1;
        var min = thisJ.attr("min") * 1;
        var intOnly = String(thisJ.attr("intOnly")).toLowerCase() == "true";

        var test = function (str) {
            return str == "" || /* (!intOnly && str == ".") || */
                ($.isNumeric(str)  && str * 1 >= min &&
                (!intOnly || str.indexOf(".") == -1) && str.match(/^0\d/) == null);
                // commented out code would allow entries like ".7"
        };

        thisJ.keydown(function () {
            var str = thisJ.val();
            if (test(str)) thisJ.data("dwnval", str);
        });

        thisJ.keyup(function () {
            var str = thisJ.val();
            if (!test(str)) thisJ.val(thisJ.data("dwnval"));
        })
    });

});

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

$(".btnassignShip").click(function(e){
e.preventDefault();


    var ship_grandtotal=0;
    var shipamt = $("#shipping").val();
    var grand_total1 = $("#grand_total2").val();
    var shippinginput =  $("#shippingamt").val();

    if((shippinginput > 0) && (shippinginput != 0)) 
    {
         var ship_grandtotal1  =  parseFloat(shipamt) + parseFloat(grand_total1);
         ship_grandtotal = parseFloat(ship_grandtotal1) - parseFloat(shippinginput) ;
    }
    else
    {
        ship_grandtotal  =  parseFloat(shipamt) + parseFloat(grand_total1);
    }

    $("#shippingamt").val(shipamt);

    
    document.getElementById('btnShipping').innerText = "Shipping: " + formatMoney(shipamt,2, ".", ",");

   

     $("#grand_total2").val(ship_grandtotal);
     $("#grand_total").text("Total : "+formatMoney(ship_grandtotal,2, ".", ","));

     $("#shippingamt").val(shipamt);
     
});

$(".btn.disabled").toggleClass('disable');
