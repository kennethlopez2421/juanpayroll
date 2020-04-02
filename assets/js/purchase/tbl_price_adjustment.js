$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	var pono = $("#pono_id_sec").data("pono");
    var token = $("#token").val();
    var supid = $("#supid").val();


    var dataTable = $('#table-grid').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        "ajax":{
            url :base_url+"Main_purchase/table_priceadjust_display", // json datasource
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

    $(".savePriceAdjust").click(function(e){
        
        e.preventDefault();

        var thiss = $("#updatepriceadjust-form");

        var serial = thiss.serialize();

        $.ajax({
            type:'post',
            url: base_url+'Main_purchase/save_price_adjustment',
            data: serial,
            beforeSend:function(data){
                $(".cancelBtn, .savePriceAdjust").prop('disabled', true); 
                $(".savePriceAdjust").text("Please wait...");
                
            },
            success:function(data){
                $(".cancelBtn, .saveBtnAccounts").prop('disabled', false);
                $(".savePriceAdjust").text("Update");
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
                        hideAfter: 10000
                    });

                    window.setTimeout(function(){
                            window.location.href=base_url+"Main_purchase/priceadjust_edit/" + token + '/' + pono + '/' + supid;
                        },500)

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
});  


function editItemPODetails(itemid,itemname,uomid,qty,price)
{
    $('#itemid').val(itemid);
    $('#itemdesc').val(itemname);
    $('#unit').val(uomid);
    $('#qty').val(qty);
    $('#price').val(price);
}