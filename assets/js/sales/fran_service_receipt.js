$(function(){
    var base_url = $("body").data('base_url'); //url
    var datas = $("body").data('datas'); // data for query
    var search_label = $("body").data('label'); //label search

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
        var shipping_id = $(".shipping_id").val();
        var location_id = $(".location_id").val();
        var ishold = $(".ishold").val();
        var agentid = $("#agentid").val();
        var name_only = $(".name_only").val();
        var passCustomerLink = $(".passCustomerLink").val();
        var trandate = $(".sales_date").val();

        if (searchCustomer == "" || trandate == "" || agentid == "") {

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
        else if (ishold == 3 ) {

            $.toast({
                heading: 'Warning!',
                text: name_only + ' account is on hold. <a href=" ' + passCustomerLink + '" target="_blank"> View details here</a>.',
                //hideAfter: false,
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

    $('.sales_date').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '-2d',
            endDate: '+90d'
       // dataTable.columns(i).search(v).draw();
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

            //$(".label-top_up").text('E-Wallet Encashment');
            $(".BtnNext").prop("hidden",false);
            $(".BtnBack2").prop("hidden",true);
            $(".BtnSaveProceed").prop("hidden",true);
            $(".BtnForm1").prop("hidden",true);
            $(".BtnForm2").prop("hidden",true);

        }else if (text_label == 'Step 2') {
            
            //p$(".label-top_up").text('Encashment Summary');
            $(".BtnNext").prop("hidden",true);
            $(".BtnBack2").prop("hidden",false);
            $(".BtnSaveProceed").prop("hidden",false);
            $(".BtnForm1").prop("hidden",false);
            $(".BtnForm2").prop("hidden",false);
            
        }else{

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

        setTimeout(function(data){
            $(".BtnNext").prop("disabled",false);
            $(".BtnBack2").prop("disabled",false);
            $(".BtnSaveProceed").prop("disabled",false);
            $(".BtnForm1").prop("hidden",false);
            $(".BtnForm2").prop("hidden",false);
            
        },2000);
    });

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


    var shipping = $("#shipping").val();
    
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



       $(".searchSalesorder").on("focusout",function() {
        var search_salesitem = $("#searchSalesorderCode_id").val();
        var fsr_price = $("#fsr_price").val();

        if (search_salesitem == "" || search_salesitem == 0 || fsr_price == "") {
            $(this).val("");
        }

        if ($(this).val() != member_name) {
            $(this).val("");
            $("#searchSalesorderCode_id").val("");
            $("#fsr_price").val("");
            customer_id = "";
        }
    });

    // $.ajax({
    //     type:'post',
    //     url:base_url+'Main_sales/UnsetSessionSalesTable',
    //     success:function(data){

    //     }
    // });

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

    var member_name = "";
    customer_id = ""


    $(".searchCustomer").on("focusout",function() {
        var search_customer = $("#searchCustomer_id").val();

        if (search_customer == "" || search_customer == 0) {
            $(this).val("");
        }

        if ($(this).val() != member_name) {
            $(this).val("");
            $("#searchCustomer_id").val("");
            customer_id = "";
        }
    });

    $("#step1").clone().prependTo("#receiver");


   //$(document).on("change",".searchCustomer",function(e){
   // e.preventDefault();
    
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
    };

    $(".searchSalesorder").easyAutocomplete(options);
 // });


   $(document).on("change",".searchCustomer",function(e){
            
        var custid = $("#searchCustomer").val();
        var parts = custid.split('|', 2);
        var idno = parts[0];
        
            if (idno != "") {
                        $.ajax({
                            type:'post',
                            url: base_url+'Main_sales/show_supplier_info',
                            data:{"idno": idno},
                           //  data:{"qty": qty},
                            beforeSend:function(data)
                            {
                                $("#step1").LoadingOverlay("show"); 
                            },
                            complete: function()
                            {
                                $("#step1").LoadingOverlay("hide"); 
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

  //  $(document).on("change","#searchSalesorder",function(e){
    $(".addSalesOrderEncodeBtn").click(function(e){
        e.preventDefault();
        
        var idcount = $(this).data('value'); // this
        var origitemcode = $("#searchSalesorder").val();
        var parts = origitemcode.split('-', 2);
        var itemid = parts[0];   
        var qty = $("#qty").val();  
        var pricecat = $("#pricecat").val();   
        var shippingamt = $("#shippingamt").val();
        var orig_price = $("#orig_price").val();
        var fsr_price = $("#fsr_price").val();

        if((itemid > 0) && (qty > 0)){ 

            var itemarray = [];
            var qtyarray = [];
            var pricearray = [];
            //var sonoarray = [];

            itemarray = [];
            qtyarray = [];
            pricearray = [];
            //sonoarray = [];

            var rowrec = $("#rowrec").val(); // validation
            var totaldata = $("#tdata").val(); // validation
        
            if(rowrec == 0){
                //itemarray.push();
                  if (itemid > 0){
                     itemarray.push(itemid);
                     qtyarray.push(qty);
                     pricearray.push(0);
                     //sonoarray.push(sono);
                  }
               $("#rowrec").val(1);
               document.getElementById("searchCustomer").disabled = true;
               document.getElementById("notif_changecustomer").hidden = false;
            }
            
            else{
                if(rowrec == 1)
                {
                    for(i = 0; i < totaldata ; i++)
                    {
                        var citemid = $("#citemid"+i).val(); 
                        var price_count = $("#cprice"+i).val();
                        var cqty = $("#cqty"+i).val();
                        //var sono_count = $("#sono"+i).val();

                        if (citemid > 0){

                            itemarray.push(citemid);
                            qtyarray.push(cqty);
                            pricearray.push(price_count);
                            
                        }
                    }

                }
            }
        }

        if ((itemid > 0) && (qty > 0)) {
                $.ajax({
                    type:'post',
                    url: base_url+'Main_sales/Insertfranservice_add',
                    data:{"itemid": itemid,
                            "itemarray":itemarray,
                            "qty":qty, 
                            "orig_price":orig_price,
                            "fsr_price":fsr_price,
                            "qtyarray":qtyarray,
                            "pricecat": pricecat,
                            "pricearray":pricearray,
                            "rowrec":rowrec},
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
                            "columnDefs": [
                                {"className": "dt-center", "targets": "_all"}
                              ],
                            "ajax":{
                                url :base_url+"Main_sales/table_addfranservice_display", // json datasource
                                type: "post",  // method  , by default get
                               data: {"itemid":itemid,"itemarray":itemarray,"rowrec":rowrec,
                                        "qty":qty, "qtyarray":qtyarray,"pricecat": pricecat,
                                         "pricearray":pricearray, "orig_price":orig_price,"fsr_price":fsr_price,},
                                
                                error: function(){ 
                                 // error handling
                                    $(".table-grid-error").html("");f
                                    $("#table-grid_processing").css("display","none");
                                }
                            }
                            
                            }); 

                              $.toast({
                                heading: 'Success',
                                text: 'Service has been successfully added.',
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

    // $(".clearBtnEncode").click(function(e){
    //     e.preventDefault();
    //     $("#encode_form").find('input').val('');
    //     $.ajax({
    //         type:'post',
    //         url:base_url+'Main_sales/UnsetSessionSalesTable',
    //         success:function(data){

    //             setTimeout(function(){
    //                 dataTable.draw();
    //             },4000)
                
    //         }
    //     });

    // });

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

    //delete
   $('#table-grid').DataTable().destroy();

        $('#table-grid').delegate(".btnDeleteSalesOrder","click", function(){
            var idcount = $(this).data('value'); // this
            var shippingamt = $("#shippingamt").val();
            var pricecat = $("#pricecat").val();
            var totaldata = $("#tdata").val(); // this
            var itemarray = [];
            var qtyarray = [];
            var pricearray = [];
            var checker = 0;

            itemarray = [];
            qtyarray = [];
            pricearray = [];

            for (i=0; i < totaldata; i++)
            {
                var itemid_count = $("#citemid"+i).val(); // this
                var qty_count = $("#cqty"+i).val(); // this
                var price_count = $("#cprice"+i).val(); // this

                if(idcount != i)
                {
                    
                  itemarray.push(itemid_count);
                  qtyarray.push(qty_count);
                  pricearray.push(price_count);
                  checker = 1;
                  
                }
                else
               {
                    if(itemid_count > 0) // eto part na toh pag empty ung table zero data kaya need mag lagay ng empty array.
                    {
                          itemarray.push();
                          qtyarray.push();
                          pricearray.push();
                          checker = 1;
                    }   
               }
           
            }

            var itemvalidate = itemarray.length
            if(itemvalidate == 0)
            {
             $("#rowrec").val(0);
             document.getElementById("searchCustomer").disabled = false;
             document.getElementById("notif_changecustomer").hidden = true;
            }
                var rowrec = $("#rowrec").val();
                    $.ajax({
                    type:'post',
                    url:base_url+'Main_sales/validate_addfranservicedetails',
                    data:{
                    'pricearray': pricearray,
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
                                url :base_url+"Main_sales/table_addfranservice_display", // json datasource
                                type: "post",  // method  , by default get
                                   data: {"itemarray":itemarray,
                                    "qtyarray":qtyarray, 
                                    "pricearray": pricearray, 
                                    "rowrec": rowrec, 
                                    "pricecat": pricecat},
                                
                                error: function(){ 
                                 // error handling
                                    $(".table-grid-error").html("");
                                    $("#table-grid_processing").css("display","none");
                                }
                            }
                            
                        }); 
                         $.toast({
                            heading: 'Success',
                            text: 'Service has been successfully deleted.',
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
                            pricearray = [];
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
            
                 $('#table-grid').DataTable().destroy();

                    }
                });
           // }
    });

   $(".BtnSaveProceed").click(function(e){
         e.preventDefault();
        
        var rowrec = $("#rowrec").val();
        var checker =0;
        if(rowrec == 1) 
        {
            var rowrec = $("#rowrec").val(); // validation
            var totaldata = $("#tdata").val(); // validation
         
            var sales_date = $("#sales_date").val();
            var idno = $("#idno").val();
            var agentid = $("#agentid").val();
            var totalamt = $("#item_total").val();
            var freight = $("#shippingamt").val();
            var sono = $("#sono").val();
            var username = $("#username").val();
            var notes = $("#notes").val();
            var payid = $("#payterm").val();

            var itemarray = [];
            var qtyarray = [];
            var pricearray = [];

            itemarray = [];
            qtyarray = [];
            pricearray = [];
            
           if(rowrec == 1)
                {
                    for(i = 0; i < totaldata ; i++)
                    {
                        var citemid = $("#citemid"+i).val();
                        var cqty = $("#cqty"+i).val();
                        var sono_count = $("#sono"+i).val();
                        var price_count = $("#cprice"+i).val();

                         if(citemid > 0)
                         {
                            itemarray.push(citemid);
                            qtyarray.push(cqty);
                            //sonoarray.push(sono);
                            pricearray.push(price_count);
                            //checker=1;
                          }
                      }
                
                 }        
                        $.ajax({
                            type:'post',
                            url:base_url+'Main_sales/table_save_addfsr',
                            data:{
                            "sales_date": sales_date,
                            "idno": idno,
                            "totalamt": totalamt,
                            "sono_count": sono_count,
                            "username": username,
                            "itemarray":itemarray,
                            "qtyarray":qtyarray,
                            "pricearray":pricearray,
                            "notes":notes,
                            "agentid":agentid,
                            "payid":payid
                            },
                            beforeSend:function(data){
                               // $(".cancelBtn, .saveBtnViolation").prop('disabled', true); 
                                $(".BtnSaveProceed").text("Please wait...");
                                $('.BtnSaveProceed').prop('disabled', true);
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
                                            $(".BtnForm2").prop("hidden",true);
                                            $(".BtnSaveProceed").prop("hidden",true);

                                    $.toast({
                                        heading: 'Success',
                                        text: 'Service has been successfully saved.',
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
              //  }
        }        
});

});

function test()
{
    $("#manual_total").val(1);
}

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


    $(document).on("change",".supplier_id",function(e){
           
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