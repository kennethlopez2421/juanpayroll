$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var pono = $("#poretno_id").data("poretno");

    var dataTable = $('#table-grid').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        "ajax":{
            url :base_url+"Main_purchase/table_purchasereturn_display", // json datasource
            type: "post",  // method  , by default get
            data: {"pono":pono},
            
            error: function(){ 
             // error handling
                $(".table-grid-error").html("");
                $("#table-grid_processing").css("display","none");
            }
        }

    });  

    dataTable.destroy();
	
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

  //$(document).on("change",".searchSupplier",function(e){

    var purchase_id = "";
    var purchase_name = "";
    var supplierid = "";
    var supplierid = $("#searchSupplier_id").val();
    var options = {

        url: base_url+'Main_purchase/autocomplete_inventory',
        getValue: "name",   
        ajaxSettings: {
            dataType: "json",
            method: "POST",
            data: {'supplierid': supplierid},
        },

        list: {

            onSelectItemEvent: function() {

                purchase_id = $(".searchInventory").getSelectedItemData().code;
                $("#searchInventory_id").val(purchase_id);
                $("#supplierid").val(supplierid);
                purchase_name = $(".searchInventory").getSelectedItemData().name;
               // supplierid = $(".searchInventory").getSelectedItemData().supplierid;
            },
            match: {
                enabled: true
            }
        },
        theme: "square"
    };
    $(".searchInventory").easyAutocomplete(options);

    $(".addPurchaseOrderEncodeBtn").click(function(e){
        e.preventDefault();

        var itemid = $("#searchInventory_id").val();
        var supplierid = $("#supplierid").val();
        var qty = $("#qty").val();  

        if((itemid > 0) && (qty > 0)) 
        {
            var rowrec = $("#rowrec").val(); // validation
            var totaldata = $("#tdata").val(); // validation

            var itemarray = [];
            var qtyarray = [];
            var ponoarray = [];
            var pricearray = [];
            var uomidarray = [];


            itemarray = [];
            qtyarray = [];
            ponoarray = [];
            pricearray = [];
            uomidarray = [];

            if(rowrec == 0)
            {
                if (itemid > 0){
                    itemarray.push(itemid);
                    qtyarray.push(qty);
                    ponoarray.push(pono);
                    pricearray.push(0);
                    uomidarray.push(0);
                }
                $("#rowrec").val(1);
            }
            else
            {
                if(rowrec == 1)
                {

                    for(i = 0; i < totaldata ; i++)
                    {
                    var citemid = $("#citemid"+i).val();
                    var cqty = $("#cqty"+i).val();
                    var pono_count = $("#pono"+i).val();
                    var price_count = $("#cprice"+i).val();
                    var uomid_count = $("#cuomid"+i).val();

                         if((citemid > 0) && (pono == pono_count))
                         {
                            itemarray.push(citemid);
                            qtyarray.push(cqty);
                            ponoarray.push(pono);
                            pricearray.push(price_count);
                            uomidarray.push(uomid_count);
                          }
                    }
                
                }
            }
      
            if ((itemid > 0) && (qty > 0)) {
                $.ajax({
                    type:'post',
                    url: base_url+'Main_purchase/InsertTemporaryPurchaseOrder',
                    data:{"itemid": itemid,
                     "qty": qty,
                      "ponoarray": ponoarray,
                       "pono": pono, 
                         'pricearray': pricearray,
                          "qtyarray":qtyarray,
                           "rowrec": rowrec, 
                           "itemarray":itemarray,
                            "uomidarray":uomidarray,
                            "supplierid":supplierid},
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
                            "processing": true,
                            "serverSide": true,
                            "ajax":{
                                url :base_url+"Main_purchase/table_purchase_maindisplay", // json datasource
                                type: "post",  // method  , by default get
                               data: {"itemid": itemid,
                                     "qty": qty,
                                      "ponoarray": ponoarray,
                                       "pono": pono, 
                                         'pricearray': pricearray,
                                          "qtyarray":qtyarray,
                                           "rowrec": rowrec, 
                                           "itemarray":itemarray,
                                            "uomidarray":uomidarray,
                                            "supplierid":supplierid},
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

  $('#table-grid').delegate(".btnDeletePurchase","click", function(){
            var idcount = $(this).data('value'); // this
         
            var totaldata = $("#tdata").val(); // this
            //var shippingamt = $("#shippingamt").val();
            var itemarray = [];
            var qtyarray = [];
            var ponoarray = [];
            var pricearray = [];
            var uomidarray = [];
            var checker = 0;

            itemarray = [];
            qtyarray = [];
            ponoarray = [];
            pricearray = [];
            uomidarray = [];

            for (i=0; i < totaldata; i++)
            {
                var pono_count = $("#pono"+i).val(); // this
                var itemid_count = $("#citemid"+i).val(); // this
                var qty_count = $("#cqty"+i).val(); // this
                var price_count = $("#cprice"+i).val(); // this
                var uomid_count = $("#cuomid"+i).val(); // this
            

                if((idcount != i) && (pono == pono_count))
                {
                    
                      itemarray.push(itemid_count);
                      qtyarray.push(qty_count);
                      ponoarray.push(pono_count);
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
                          ponoarray.push(pono_count);
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
                    url:base_url+'Main_purchase/validate_purchasedetails',
                    data:{'ponoarray': ponoarray, 
                    'pono': pono, 
                    'pricearray': pricearray,
                     "qtyarray":qtyarray, 
                     "rowrec": rowrec,
                     "uomidarray":uomidarray
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
                            "processing": true,
                            "serverSide": true,
                            "ajax":{
                                url :base_url+"Main_purchase/table_purchase_maindisplay", // json datasource
                                type: "post",  // method  , by default get
                               data: {"itemarray":itemarray,
                                "qtyarray":qtyarray, 
                                "ponoarray":ponoarray,
                                  "pono": pono, "pricearray": pricearray, "rowrec": rowrec, "uomidarray":uomidarray},
                                
                                error: function(){ 
                                 // error handling
                                    $(".table-grid-error").html("");
                                    $("#table-grid_processing").css("display","none");
                                }
                            }
                            
                        }); 
                            
                        //  $.toast({
                        //     heading: 'Success',
                        //     text: 'Item has been successfully deleted.',
                        //     icon: 'success',
                        //     loader: false,  
                        //     stack: false,
                        //     position: 'top-center', 
                        //     bgColor: '#5cb85c',
                        //     textColor: 'white',
                        //     allowToastClose: false,
                        //     hideAfter: 3000
                        // });

                            itemarray = [];
                            qtyarray = [];
                            sonoarray = [];
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
        var supplierid = $("#searchSupplier_id").val();
        var itemlocid = $("#itemlocid").val();
        var totalamt = $(".item_total").val();
        var notes = $(".notes").val();
        var freight = $(".shippingamt").val();
        var edate = $("#edate").val();
        var checker =0;
        if(rowrec == 1) 
        {
            var rowrec = $("#rowrec").val(); // validation
            var totaldata = $("#tdata").val(); // validation

            var shipping = $("#shipping_cost1").val();
            var notes = $("#notes").val();

            var itemarray = [];
            var qtyarray = [];
            var ponoarray = [];
            var pricearray = [];
            var uomidarray = [];

            itemarray = [];
            qtyarray = [];
            ponoarray = [];
            pricearray = [];
            uomidarray = [];

         
                if(rowrec == 1)
                {

                    for(i = 0; i < totaldata ; i++)
                    {
                        var citemid = $("#citemid"+i).val();
                        var cqty = $("#cqty"+i).val();
                        var pono_count = $("#pono"+i).val();
                        var price_count = $("#cprice"+i).val();
                        var uomid_count = $("#cuomid"+i).val();

                         if((citemid > 0) && (pono == pono_count))
                         {
                            itemarray.push(citemid);
                            qtyarray.push(cqty);
                            ponoarray.push(pono);
                            pricearray.push(price_count);
                            uomidarray.push(uomid_count);
                            checker=1;
                          }
                    }
                
                }
            
            if (checker == 1) {
                $.ajax({
                    type:'post',
                    url: base_url+'Main_purchase/InsertPurchaseOrdertoDB',
                    data:{
                      "ponoarray": ponoarray,
                       "pono": pono,
                       "shipping": shipping,
                       "notes": notes,
                       "supplierid":supplierid,
                        "itemlocid":itemlocid
                   },
                   beforeSend:function(data){
                        $(".saveBtnEncode").text("Please wait...");
                        $('.saveBtnEncode ').prop('disabled', true);
                    },
                    success:function(data){
                        if(data.success == 1)
                        {
                           
                        $.ajax({
                            type:'post',
                            url:base_url+'Main_purchase/table_save_purchasereturn',
                            data:{"itemarray":itemarray,
                            "qtyarray":qtyarray, 
                            "ponoarray":ponoarray, 
                            "pricearray":pricearray, 
                            'pono': pono,
                            'uomidarray': uomidarray,
                            "shipping": shipping,
                            "freight":freight,
                            "edate": edate,
                            "notes": notes,
                            "supplierid":supplierid,
                            "itemlocid":itemlocid,
                            "totalamt":totalamt
                            },
                            success:function(data){
                            if(data.success == 1)
                                {
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
                                     window.setTimeout(function(){
                                             window.location.href=base_url+"Main_purchase/return_summary/" + token;
                                      });
                                        
                                }
                                
                                
                            }
                        });
                    }
               
            }
        });
     }
    }        
 });
});

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